<?php

	class import_component_files
	{

		private $receipt = array();
		protected
			$alc,
			$db,
			$sogeneric_document,
			$fakebase,
			$path_upload_dir,
			$location_code,
			$location_item_id,
			$attrib_name_componentID,
			$doc_cat_id,
			$last_files_added,
			$list_component_id,
			$paths_from_file,
			$paths_empty;

		public function __construct()
		{
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->db = & $GLOBALS['phpgw']->db;
			$this->sogeneric_document = CreateObject('property.sogeneric_document');


			$this->fakebase = '/temp_files_components';
			$this->path_upload_dir = $GLOBALS['phpgw_info']['server']['files_dir'] . $this->fakebase . '/';
//			$this->path_upload_dir = '/data/portico/temp_files_components/';

			$this->location_code = phpgw::get_var('location_code');
			$this->location_item_id = phpgw::get_var('location_item_id');
			$this->attrib_name_componentID = phpgw::get_var('attribute_name_component_id');
			$this->doc_cat_id =  phpgw::get_var('doc_cat_id');

			$this->last_files_added = array();
			$this->list_component_id = array();
			$this->paths_from_file = array();
			$this->paths_empty = array();
		}

		public function get_path_upload_dir()
		{
			return $this->path_upload_dir;
		}

		public function check_upload_dir()
		{
			$rs = $this->create_document_dir();
			if (!$rs)
			{
				$receipt['error'] = lang('failed to create directory') . ': ' . $this->fakebase;
			}

			if (!is_writable($this->path_upload_dir))
			{
				$receipt['error'] = lang('Not have permission to access the directory') . ': ' . $this->fakebase;
			}

			return $receipt;
		}

		private function create_document_dir()
		{
			if (is_dir($this->path_upload_dir))
			{
				return true;
			}

			$old = umask(0);
			$rs = mkdir($this->path_upload_dir, 0755);
			umask($old);

			return $rs;
		}

		private function _valid_row( $row )
		{
			if (empty($row[(count($row) - 1)]))
			{
				return false;
			}

			if ($row[0] == 'Nummer3' && $row[(count($row) - 1)] == 'Filsti')
			{
				return false;
			}

			return true;
		}

		private function _get_files_by_component( $id, $location_id )
		{
			$sql = "SELECT a.location_id, a.location_item_id, b.file_id, b.name, b.md5_sum FROM phpgw_vfs_file_relation a INNER JOIN phpgw_vfs b "
				. " ON a.file_id = b.file_id WHERE a.location_item_id = '{$id}' AND a.location_id = '{$location_id}'"
				. " AND b.mime_type != 'Directory' AND b.mime_type != 'journal' AND b.mime_type != 'journal-deleted'";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			while ($this->db->next_record())
			{
				$values[] = $this->db->f('md5_sum');
			}

			return $values;
		}

		private function _search_file_in_db( $md5_sum )
		{
			$sql = "SELECT file_id, md5_sum FROM phpgw_vfs "
				. " WHERE md5_sum = '{$md5_sum}'"
				. " AND mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";

			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$id = $this->db->f('file_id');
			}

			return $id;
		}

		private function _search_in_last_files_added( $file_data )
		{
			$md5sum = $file_data['md5sum'];
			$file_id = array_search($md5sum, $this->last_files_added);

			return $file_id;
		}

		public function add_files_location()
		{
			@set_time_limit(5 * 60);

			$message = array();

			$uploaded_files = phpgwapi_cache::session_get('property', 'import_data');
			$this->paths_from_file = phpgwapi_cache::session_get('property', 'paths_from_file');

			$count_new_relations = 0;
			$count_relations_existing = 0;
			$files_existing = array();
			$count_new_files = 0;

			$component = array('id' => $this->location_item_id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $this->location_code))));

			$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

			foreach ($uploaded_files as $file_data)
			{
				if (in_array($file_data['md5sum'], $files_in_component))
				{
					$count_relations_existing++;
					$files_existing[$file_data['md5sum']] = $file_data['md5sum'];
					continue;
				}

				$this->db->transaction_begin();
				try
				{
					$this->db->Exception_On_Error = true;

					$file = $file_data['file'];

					$file_id = $this->_search_in_last_files_added($file_data);
					if (!$file_id)
					{
						$file_id = $this->_search_file_in_db($file_data['md5sum']);
						if (!$file_id)
						{
							$file_id = $this->_save_file($file_data);
							if (!$file_id)
							{
								throw new Exception("failed to copy file '{$file_data['path_absolute']}'");
							}
							unlink($file_data['path_absolute']);
							$count_new_files++;
						}
						else
						{
							$files_existing[$file_data['md5sum']] = $file_data['md5sum'];
						}

						$result = $this->_save_file_relation($component['id'], $component['location_id'], $file_id);
						if (!$result)
						{
							$message['error'][] = array('msg' => "failed to save relation. File: '{$file}'");
						}
						else
						{
							$this->last_files_added[$file_id] = $file_data['md5sum'];
							$count_new_relations++;
						}
					}

					$this->db->Exception_On_Error = false;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						$this->db->transaction_abort();
						if ($e->getMessage())
						{
							$message['error'][] = array('msg' => $e->getMessage());
						}
						continue;
					}
				}
				$this->db->transaction_commit();
			}

			if ($count_new_files)
			{
				$message['message'][] = array('msg' => lang('%1 files copy successfully', $count_new_files));
			}
			else
			{
				$message['message'][] = array('msg' => lang('%1 files copy', $count_new_files));
			}
			if (count($files_existing))
			{
				$message['message'][] = array('msg' => lang('%1 files existing in db', count($files_existing)));
			}
			if ($count_new_relations)
			{
				$message['message'][] = array('msg' => lang('%1 relations saved successfully', $count_new_relations));
			}
			else
			{
				$message['message'][] = array('msg' => lang('no relation has been saved'));
			}
			if ($count_relations_existing)
			{
				$message['message'][] = array('msg' => lang('%1 relations existing', $count_relations_existing));
			}

			$this->_delete_all_dir_temp();

			$this->_delete_all_dir_temp();
			
			return $message;
		}

		private function _generate_md5sum( $path )
		{
			$output = array();
			exec('md5sum "' . $path . '" 2>&1', $output, $ret);
			if ($ret)
			{
				$md5sum = '';
			}
			else
			{
				$md5sum = trim(strstr($output[0], ' ', true));
			}

			return $md5sum;
		}

		private function _compare_names( &$component_files, $uploaded_files )
		{
			if (count($component_files))
			{
				foreach ($component_files as &$files)
				{
					foreach ($files as &$file_data)
					{
						foreach ($uploaded_files as $file)
						{
							if (strtolower($file['file']) == strtolower($file_data['file']))
							{
								if ($file['path_string'])
								{
									$pos = stripos($file['path_string'], $file_data['path_string']);
									if ($pos !== false)
									{
										$file_data['path_absolute'] = $file['path_absolute'];
										$file_data['path_relative'] = $file['path_relative'];
										$file_data['md5sum'] = $this->_generate_md5sum($file['path_absolute']);
									}
								}
								else
								{
									$file_data['path_absolute'] = $file['path_absolute'];
									$file_data['path_relative'] = $file_data['path'];
									$file_data['md5sum'] = $this->_generate_md5sum($file['path_absolute']);
								}
							}
						}
						if (!empty($file_data['md5sum']))
						{
							$this->paths_from_file[$file_data['md5sum']][] = $file_data['path_relative'];
						}
						else
						{
							$this->paths_empty[strtolower($file_data['file'])] = $file_data['path'] . '/' . $file_data['file'];
						}
					}
				}
			}
			else
			{
				foreach ($uploaded_files as &$file)
				{
					$md5sum = $this->_generate_md5sum($file['path_absolute']);
					if (!empty($md5sum))
					{
						$file['md5sum'] = $md5sum;
						$component_files[$md5sum] = $file;
						$this->paths_from_file[$md5sum][] = $file['path_relative'];
					}
					else
					{
						$this->paths_empty[] = $file['path_absolute'];
					}
				}
				//$component_files = $uploaded_files;
			}
		}

		private function _un_zip( $file, $dir )
		{
			@set_time_limit(5 * 60);
/*
			$zip = new ZipArchive;
			if ($zip->open($file) === TRUE)
			{
				$zip->extractTo($dir);
				$zip->close();
				return true;
			}
			else
			{
				$this->receipt['error'][] = array('msg' => lang('Failed opening file %1', $file));
				return false;
			}
 */

			$zip = new ZipArchive;
			if ($zip->open($file) === TRUE)
			{
				for ($i = 0; $i < $zip->numFiles; $i++)
				{
					//					$file_name = str_replace('..', '.', iconv("CP850", "UTF-8", $zip->getNameIndex($i)));
					$file_name = str_replace('..', '.', $zip->getNameIndex($i));
					$copy_to = $dir . '/' . $file_name;
					if (!is_dir(dirname($copy_to)))
					{
						mkdir(dirname($copy_to), 0777, true);
					}
					copy("zip://" . $file . "#" . $zip->getNameIndex($i), "{$copy_to}");
				}
				$zip->close();

				return true;
			}
			else
			{
				$this->receipt['error'][] = array('msg' => lang('Failed opening file %1', $file));
				return false;
			}
		}

		private function _un_rar( $file, $dir )
		{
			@set_time_limit(5 * 60);

			$archive = RarArchive::open($file);
			if ($archive === FALSE)
			{
				$this->receipt['error'][] = array('msg' => lang('Failed opening file %1', $file));
				return false;
			}

			$entries = $archive->getEntries();
			foreach ($entries as $entry)
			{
				$file_name = str_replace('..', '.', $entry->getName());
				$copy_to = $dir . '/' . $file_name;
				if (!is_dir(dirname($copy_to)))
				{
					mkdir(dirname($copy_to), 0777, true);
				}
				copy("rar://" . $file . "#" . $entry->getName(), "{$copy_to}");
			}
			$archive->close();

			return true;
		}

		private function _uncompresed_file( $path_file )
		{
			$info = pathinfo($path_file);
			$path_dir = $this->path_upload_dir . $info['filename'];
			$result = true;

			if (!in_array($info['extension'], array('zip', 'rar')))
			{
				$this->receipt['error'][] = array('msg' => lang('The file extension should be zip or rar'));
				return false;
			}

			if (is_dir($path_dir))
			{
				exec("rm -Rf '{$path_dir}'", $ret);
			}
			mkdir($path_dir, 0777, true);

			if ($info['extension'] == 'zip')
			{
				$result = $this->_un_zip($path_file, $path_dir);
			}
			else if ($info['extension'] == 'rar')
			{
				$result = $this->_un_rar($path_file, $path_dir);
			}

			return $result;
		}

		private function _get_uploaded_files()
		{
			$compressed_file = phpgw::get_var('compressed_file_check');
			$compressed_file_name = phpgw::get_var('compressed_file_name');

			$list_files = array();

			if ($compressed_file)
			{
				$path_file = $this->path_upload_dir . $compressed_file_name;

				if (!is_file($path_file))
				{
					$this->receipt['error'][] = array('msg' => lang('File %1 not exist', $path_file));
					return;
				}

				if (!$this->_uncompresed_file($path_file))
				{
					return false;
				}

				$info = pathinfo($path_file);
				$path_dir = $this->path_upload_dir . $info['filename'];

				if (!is_dir($path_dir))
				{
					$this->receipt['error'][] = array('msg' => lang('Directory %1 not exist', $path_dir));
					return;
				}

				$list_files = $this->_get_dir_contents($path_dir);
			}
			else
			{
				$list_files = $this->_get_files($this->path_upload_dir);
			}

			if (!count($list_files))
			{
				$this->receipt['error'][] = array('msg' => lang("no exist files to import"));
			}

			return $list_files;
		}

		private function _get_files( $dir, $results = array() )
		{
			$content = scandir($dir);

			foreach ($content as $key => $value)
			{
				$path = realpath($dir . '/' . $value);
				if (is_file($path))
				{
					$pos = strpos($value, '..');
					if (!$pos === false)
					{
						$new_path = str_replace('..', '.', $path);
						if (rename($path, $new_path))
						{
							$value = str_replace('..', '.', $value);
							$path = $new_path;
						}
					}

					$results[] = array('file' => $value,
						'path_absolute' => $path,
						'path_relative' => '/');
				}
			}

			return $results;
		}

		private function _get_dir_contents( $dir, &$results = array() )
		{
			$content = scandir($dir);
			$patrones = array('(\\/)', '(\\\\)', '(")');
			$sustituciones = array('_', '_', '_');

			foreach ($content as $key => $value)
			{
				$path = realpath($dir . '/' . $value);
				if (is_file($path))
				{
					$results[] = array('file' => $value,
						'path_string' => preg_replace($patrones, $sustituciones, $path),
						'path_absolute' => $path,
						'path_relative' => substr($dir, strlen($this->path_upload_dir)));
				}
				else if ($value != "." && $value != "..")
				{
					$this->_get_dir_contents($path, $results);
				}
			}

			return $results;
		}

		public function get_relations()
		{
			$exceldata = $this->_getexceldata($_FILES['file']['tmp_name'], false);
			$component_files = array();

			$patrones = array('(\\/)', '(")');
			$sustituciones = array('_', '_');
			foreach ($exceldata as $k => $row)
			{
				if (!$this->_valid_row($row))
				{
					continue;
				}

				$path_file = str_replace('..', '.', $row[(count($row) - 1)]);
				$path_file = preg_replace($patrones, $sustituciones, $path_file);
				$array_path = explode("\\", $path_file);

				$file_name = $array_path[count($array_path) - 1];
				$path = implode("/", array_slice($array_path, 0, (count($array_path) - 1)));
				$path_string = implode("_", $array_path);

				$component_files[$row[0]][] = array(
					'name' => $row[1],
					'desription' => $row[2],
					'file' => $file_name,
					'path' => $path,
					'path_string' => $path_string,
					'row' => ($k + 1)
				);
			}

			return $component_files;
		}

		private function _delete_all_dir_temp()
		{
			$files = glob($this->path_upload_dir . '*', GLOB_MARK);

			foreach ($files as $file)
			{
				$path = realpath($file);
				if (is_dir($path))
				{
					exec("rm -Rf '{$path}'", $ret);
				}
			}
		}

		private function _search_relations_with_components_location( $relations )
		{
			$count_new_relations = 0;
			$count_relations_existing = 0;
			foreach ($relations as $k => $files)
			{
				if (empty($k))
				{
					$component = array('id' => $this->location_item_id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $this->location_code))));
				}
				else
				{
					$component = $this->_get_component($k, $this->attrib_name_componentID, $this->location_code);
					if (empty($component['id']) || empty($component['location_id']))
					{
						$this->receipt['message'][] = array('msg' => lang("Component '%1' with location code '%2' does not exist", $k, $this->location_code));
						continue;
					}
				}

				$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

				foreach ($files as $file_data)
				{
					if (empty($file_data['md5sum']))
					{
						continue;
					}

					if (in_array($file_data['md5sum'], $files_in_component))
					{
						$count_relations_existing++;
					}
					else
					{
						$count_new_relations++;
					}
				}
			}

			if ($count_relations_existing)
			{
				$this->receipt['message'][] = array('msg' => lang('%1 relations existing', $count_relations_existing));
			}

			if ($count_new_relations)
			{
				$this->receipt['message'][] = array('msg' => lang('%1 new relations to add', $count_new_relations));
			}
			else
			{
				$this->receipt['message'][] = array('msg' => lang('any relation to add'));
			}
		}

		private function _search_relations_with_location( $relations )
		{
			$count_new_relations = 0;
			$count_relations_existing = 0;

			$component = array('id' => $this->location_item_id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $this->location_code))));
			$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

			foreach ($relations as $file_data)
			{
				if (empty($file_data['md5sum']))
				{
					continue;
				}

				if (in_array($file_data['md5sum'], $files_in_component))
				{
					$count_relations_existing++;
				}
				else
				{
					$count_new_relations++;
				}
			}

			if ($count_relations_existing)
			{
				$this->receipt['message'][] = array('msg' => lang('%1 relations existing', $count_relations_existing));
			}

			if ($count_new_relations)
			{
				$this->receipt['message'][] = array('msg' => lang('%1 new relations to add', $count_new_relations));
			}
			else
			{
				$this->receipt['message'][] = array('msg' => lang('any relation to add'));
			}
		}

		public function preview()
		{
			$with_components = phpgw::get_var('with_components_check');

			$uploaded_files = $this->_get_uploaded_files();

			if ($this->receipt['error'])
			{
				return $this->receipt;
			}

			if ($with_components)
			{
				$relations = $this->get_relations();
				$this->_compare_names($relations, $uploaded_files);
				$this->_search_relations_with_components_location($relations);
			}
			else
			{
				$relations = array();
				$this->_compare_names($relations, $uploaded_files);
				$this->_search_relations_with_location($relations);
			}

			phpgwapi_cache::session_set('property', 'paths_from_file', $this->paths_from_file);
			phpgwapi_cache::session_set('property', 'import_data', $relations);

			$files_in_db = 0;
			foreach ($this->paths_from_file as $k => $v)
			{
				if ($this->_search_file_in_db($k))
				{
					$files_in_db++;
				}
			}

			if ($files_in_db)
			{
				$this->receipt['message'][] = array('msg' => lang('%1 files exist in db', $files_in_db));
			}
			$this->receipt['message'][] = array('msg' => lang('%1 files prepare to copy', (count($this->paths_from_file) - $files_in_db)));

			if (count($this->paths_empty))
			{
				$this->receipt['error'][] = array('msg' => lang('%1 files not exist in the temporary folder', count($this->paths_empty)));

				foreach ($this->paths_empty as $c => $v)
				{
					$this->receipt['error'][] = array('msg' => lang("file not exist: %1", $v));
				}
			}

			return $this->receipt;
		}

		public function add_files_components_location()
		{
			@set_time_limit(5 * 60);

			$message = array();

			$component_files = phpgwapi_cache::session_get('property', 'import_data');
			$this->paths_from_file = phpgwapi_cache::session_get('property', 'paths_from_file');

			$count_new_relations = 0;
			$count_relations_existing = 0;
			$count_new_files = 0;
			$files_existing = array();
			$files_not_existing = array();

			foreach ($component_files as $k => $files)
			{
				if (empty($k))
				{
					$component = array('id' => $this->location_item_id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $this->location_code))));
				}
				else
				{
					$component = $this->_get_component($k, $this->attrib_name_componentID, $this->location_code);
					if (empty($component['id']) || empty($component['location_id']))
					{
						$message['message'][] = array('msg' => lang("Component '%1' with location code '%2' does not exist", $k, $this->location_code));
						continue;
					}
				}

				$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

				foreach ($files as $file_data)
				{
					if (in_array($file_data['md5sum'], $files_in_component))
					{
						$count_relations_existing++;
						$files_existing[$file_data['md5sum']] = $file_data['md5sum'];
						continue;
					}

					$this->db->transaction_begin();
					try
					{
						$this->db->Exception_On_Error = true;

						$file = $file_data['file'];

						$file_id = $this->_search_in_last_files_added($file_data);
						if (!$file_id)
						{
							$file_id = $this->_search_file_in_db($file_data['md5sum']);
							if (!$file_id)
							{
								if (!is_file($file_data['path_absolute']))
								{
									$_file = ($file_data['path_absolute']) ? $file_data['path_absolute'] : $file_data['path'] . '/' . $file_data['file'];
									$files_not_existing[strtolower($file_data['file'])] = $_file;
									throw new Exception();
								}

								$file_id = $this->_save_file($file_data);
								if (!$file_id)
								{
									throw new Exception("failed to copy file: '{$file_data['path_absolute']}'. Component: '{$k}'");
								}
								unlink($file_data['path_absolute']);
								$count_new_files++;
							}
							else
							{
								$files_existing[$file_data['md5sum']] = $file_data['md5sum'];
							}
						}

						$result = $this->_save_file_relation($component['id'], $component['location_id'], $file_id);
						if (!$result)
						{
							$message['error'][] = array('msg' => "failed to save relation. File: '{$file}'. Component: '{$k}'");
						}
						else
						{
							$this->last_files_added[$file_id] = $file_data['md5sum'];
							$count_new_relations++;
						}

						$this->db->Exception_On_Error = false;
					}
					catch (Exception $e)
					{
						if ($e)
						{
							$this->db->transaction_abort();
							if ($e->getMessage())
							{
								$message['error'][] = array('msg' => $e->getMessage());
							}
							continue;
						}
					}
					$this->db->transaction_commit();
				}
			}

			if ($count_new_files)
			{
				$message['message'][] = array('msg' => lang('%1 files copy successfully', $count_new_files));
			}
			else
			{
				$message['message'][] = array('msg' => lang('%1 files copy', $count_new_files));
			}
			if (count($files_existing))
			{
				$message['message'][] = array('msg' => lang('%1 files existing in db', count($files_existing)));
			}
			if ($count_new_relations)
			{
				$message['message'][] = array('msg' => lang('%1 relations saved successfully', $count_new_relations));
			}
			else
			{
				$message['message'][] = array('msg' => lang('any relation has been saved'));
			}
			if ($count_relations_existing)
			{
				$message['message'][] = array('msg' => lang('%1 relations existing', $count_relations_existing));
			}

			if (count($files_not_existing))
			{
				$message['error'][] = array('msg' => lang('%1 files not exist in the temporary folder', count($files_not_existing)));
			}

			if (count($files_not_existing))
			{
				foreach ($files_not_existing as $c => $v)
				{
					$message['error'][] = array('msg' => lang("file not exist: %1", $v));
				}
			}

			$this->_delete_all_dir_temp();

			$this->_delete_all_dir_temp();
			
			return $message;
		}

		private function _get_component( $query, $attrib_name_componentID, $location_code )
		{
			if (array_key_exists($query, $this->list_component_id))
			{
				return $this->list_component_id[$query];
			}

			$location_code_values = explode('-', $location_code);
			$loc1 = $location_code_values[0];

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
			}

			$sql = "SELECT * FROM fm_bim_item WHERE loc1 = '{$loc1}' AND json_representation->>'{$attrib_name_componentID}' = '{$query}'";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();

			if ($this->db->next_record())
			{
				$values['id'] = $this->db->f('id');
				$values['location_id'] = $this->db->f('location_id');
			}

			if ($values['id'])
			{
				$this->list_component_id[$query] = $values;
			}

			return $values;
		}

		private function _save_file( $file_data )
		{
			$metadata = array();

			$path_file = $file_data['path_absolute'];
			$md5sum = $file_data['md5sum'];

			$bofiles = CreateObject('property.bofiles');

			$file_name = str_replace(' ', '_', trim($file_data['file']));

			$to_file = $bofiles->fakebase . '/generic_document/' . $file_name;

			$receipt = $bofiles->create_document_dir("generic_document");
			if (count($receipt['error']))
			{
				throw new Exception('failed to create directory');
			}
			$bofiles->vfs->override_acl = 1;

			$file_id = $bofiles->vfs->cp3(array(
				'from' => $path_file,
				'to' => $to_file,
				'id' => '',
				'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL)));
			$bofiles->vfs->override_acl = 0;

			if (empty($file_id))
			{
				return false;
			}

			$this->db->query("UPDATE phpgw_vfs SET md5_sum='{$md5sum}'"
				. " WHERE file_id='{$file_id}'", __LINE__, __FILE__);

			if (count($this->paths_from_file[$md5sum]))
			{
				$paths = array_values(array_unique($this->paths_from_file[$md5sum]));
			}
			else
			{
				$paths = array();
			}

			$metadata['report_date'] = phpgwapi_datetime::date_to_timestamp(date('Y-m-d'));
			$metadata['title'] = $file_data['name'];
			$metadata['descr'] = $file_data['desription'];
			$metadata['cat_id'] = $this->doc_cat_id;
			$metadata['path'] = $paths;

			$values_insert = array
				(
				'file_id' => $file_id,
				'metadata' => "'" . json_encode($metadata) . "'"
			);

			$this->db->query("INSERT INTO phpgw_vfs_filedata (" . implode(',', array_keys($values_insert)) . ') VALUES ('
				. implode(",", array_values($values_insert)) . ')', __LINE__, __FILE__);

			return $file_id;
		}

		private function _save_file_relation( $id, $location_id, $file_id )
		{
			$date = phpgwapi_datetime::date_to_timestamp(date('Y-m-d'));

			$values_insert = array
				(
				'file_id' => (int)$file_id,
				'location_id' => (int)$location_id,
				'location_item_id' => (int)$id,
				'is_private' => 0,
				'account_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'entry_date' => $date,
				'start_date' => $date,
				'end_date' => $date
			);

			$ok = $this->db->query("INSERT INTO phpgw_vfs_file_relation (" . implode(',', array_keys($values_insert)) . ') VALUES ('
					. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);

			if($ok)
			{
				return $this->sogeneric_document->update_relation_path($file_id);
			}
		}

		protected function _getexceldata( $path, $get_identificator = false )
		{
			phpgw::import_class('phpgwapi.phpexcel');

			$objPHPExcel = PHPExcel_IOFactory::load($path);
			$objPHPExcel->setActiveSheetIndex(0);

			$result = array();

			$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);

			$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();

			$start = $get_identificator ? 3 : 1; // Read the first line to get the headers out of the way

			if ($get_identificator)
			{
				$this->identificator = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(0, 1)->getCalculatedValue();
				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$this->fields[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, 2)->getCalculatedValue();
				}
			}
			else
			{
				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$this->fields[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, 1)->getCalculatedValue();
				}
			}

			$rows = $rows ? $rows + 1 : 0;
			for ($row = $start; $row < $rows; $row++)
			{
				$_result = array();

				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$_result[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $row)->getCalculatedValue();
				}

				$result[] = $_result;
			}

			return $result;
		}
	}
