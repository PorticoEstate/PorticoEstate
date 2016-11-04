<?php

	class import_component_files
	{	
		public function __construct()
		{
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->db = & $GLOBALS['phpgw']->db;
			
			$this->fakebase = '/temp_files_components';
			$this->path_upload_dir = $GLOBALS['phpgw_info']['server']['files_dir'].$this->fakebase.'/';
			
			$this->latest_uploads = array();
			//$this->latest_uploads_path = array();
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
		
		private function _valid_row($row)
		{
			if (empty($row[(count($row)-1)]))
			{
				return false;
			}
			
			if ($row[0] == 'Nummer3' && $row[(count($row)-1)] == 'Filsti')
			{
				return false;
			}
			
			return true;
		}
		
		private function _get_files_by_component($id, $location_id)
		{
			$sql = "SELECT a.location_id, a.location_item_id, b.file_id, b.name FROM phpgw_vfs_file_relation a INNER JOIN phpgw_vfs b "
					. " ON a.file_id = b.file_id WHERE a.location_item_id = '{$id}' AND a.location_id = '{$location_id}'"
					. " AND b.mime_type != 'Directory' AND b.mime_type != 'journal' AND b.mime_type != 'journal-deleted'";

			$this->db->query($sql, __LINE__, __FILE__);

			$values = array();
			
			while ($this->db->next_record())
			{
				$healthy = $this->db->f('file_id').'_#';
				$values[] = trim(str_replace($healthy, '', $this->db->f('name')));
			}

			return $values;			
		}
		
		private function _search_in_latest_uploads($file_data)
		{
			$file = $file_data['file'];
			$file_name = str_replace(' ', '_', $file);
			$file_id = array_search($file_name, $this->latest_uploads);
			if ($file_id)
			{
				return $file_id;
			}
			
			return false;
		}
		
		private function _search_file_in_db($file)
		{
			$file_name = str_replace(' ', '_', $file);
			
			$sql = "SELECT file_id, name FROM phpgw_vfs "
					. " WHERE name LIKE '%{$file_name}'"
					. " AND mime_type != 'Directory' AND mime_type != 'journal' AND mime_type != 'journal-deleted'";

			$this->db->query($sql, __LINE__, __FILE__);

			$value = array();

			if ($this->db->next_record())
			{
				$value['file_id'] = $this->db->f('file_id');
				$value['name'] = $this->db->f('name');
			}

			return $value['file_id'];			
		}
		
		public function add_files_location($id, $location_code)
		{		
			$message = array();
			
			$files = array();
			$dh  = opendir($this->path_upload_dir);
			if ($dh) 
			{
				while (false !== ($filename = readdir($dh))) 
				{
					if ($filename != '.' && $filename != '..') {
						$files[] = $filename;
					}
				}
				closedir($dh);
			}

			if (!count($files))
			{
				$message['error'][] = array('msg' => lang("no exist files to import"));
				return $message;
			}
			
			$count_new_relations = 0;
			$count_relations_existing = 0;
			$count_new_files = 0;
			$files_existing = array();
			$files_not_existing = array();
			
			$component = array('id' => $id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.'.count(explode('-', $location_code))));

			$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

			foreach ($files as $file_name)
			{
				if (in_array(str_replace(' ', '_', $file_name), $files_in_component))
				{
					$count_relations_existing++;
					continue;
				}

				$this->db->transaction_begin();
				try
				{
					$this->db->Exception_On_Error = true;						

					$file_data['file'] = $file_name;

					/*$file_id = $this->_search_file_in_db($file_name);
					if ($file_id)
					{
						$files_existing[$file_name] = $file_name;
						throw new Exception();
					}*/

					$file_id = $this->_save_file($file_data);
					if (!$file_id)
					{						
						throw new Exception("failed to copy file '{$file_name}'");
					} 
					unlink($this->path_upload_dir.$file_name);
					$count_new_files++;
					
					$result = $this->_save_file_relation($component['id'], $component['location_id'], $file_id);
					if (!$result)
					{						
						$message['error'][] = array('msg' => "failed to save relation. File: '{$file_name}'");
					} else {
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

			if ($count_new_files)
			{
				$message['message'][] = array('msg' => lang('%1 files copy successfully', $count_new_files));
			} else {
				$message['message'][] = array('msg' => lang('%1 files copy', $count_new_files));
			}
			if ($count_new_relations)
			{
				$message['message'][] = array('msg' => lang('%1 relations saved successfully', $count_new_relations));
			} else {
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
			
			if (count($files_existing))
			{
				foreach($files_existing as $file)
				{
					$message['error'][] = array('msg' => lang("file %1 exist in DB", $file));
				}
			}
			
			return $message;
		}
		
		private function search_repeated_names($component_files)
		{
			$message = array();
			$names = array();
			$paths = array();
			$rows = array();
			$files_repeated = array();
			/*$patrones = array('(\\/)', '(\\\\)', '( )');
			$sustituciones = array('_', '_', '_');*/
			
			foreach ($component_files as $k => $files) 
			{
				foreach ($files as $file_data)
				{
					//$file_data['file-path'] = preg_replace($patrones, $sustituciones, $file_data['path']);
					if (in_array($file_data['file'], $names)) 
					{
						if (!in_array($file_data['path'], $paths))
						{
							//$message['error'][] = array('msg' => "file '{$file_data['path']}' already exists on another path");
							$files_repeated[$file_data['file']][] = $file_data['path'].' ==> row: '.$file_data['row'];
							
						}
						continue;
					}
					$names[] = $file_data['file'];
					$rows[$file_data['file']] = $file_data['row'];
					$paths[$file_data['file']] = $file_data['path'];
				}
			}
			
			if (count($files_repeated))
			{
				foreach($files_repeated as $k => $v)
				{
					//$files_repeated[$k]['exist'] = $paths[$k].' ==> row: '.$rows[$k];
					$cad = "<ul><b>File path: {$paths[$k]}</b> ==> row: {$rows[$k]} <br>Files repeated in another path (".count($v)."):";
					foreach ($v as $file) {
						$cad .= "<li>{$file}</li>";
					}
					$cad .= "</ul>";
					$message['error'][] = array('msg' => $cad);
				}
			}
			
			return $message;
		}
		
		private function get_files()
		{

			$file = 'Dokumentasjon.zip';
			$dir = 'Dokumentasjon';
			
			if (is_dir($this->path_upload_dir.$dir))
			{
				//$ficheros  = scandir($this->path_upload_dir.$dir, 1);
				$ficheros  = $this->getDirContents($this->path_upload_dir.$dir);
			}
			else if (is_file($this->path_upload_dir.$file))
			{
				$zip = new ZipArchive;
				if ($zip->open($this->path_upload_dir.$file) === TRUE) 
				{
					$zip->extractTo($this->path_upload_dir.$dir);
					$zip->close();
					//$ficheros  = scandir($this->path_upload_dir.$dir, 1);
					$ficheros  = $this->getDirContents($this->path_upload_dir.$dir);
				} else {
					$ficheros  = array();
				}
			}	

			return $ficheros;
		}
		
		private function getDirContents($dir, &$results = array())
		{
			$files = scandir($dir);

			
			foreach($files as $key => $value)
			{
				$path = realpath($dir.'/'.$value);
				if(is_file($path)) 
				{				
					exec("md5sum {$path} 2>&1", $output, $ret);
					$results[] = array('name'=>$value, 'md5sum'=>$output, 'path'=>$path);
				} else if($value != "." && $value != "..") {
					$this->getDirContents($path, $results);
				}
			}
		

			return $results;
		}

		public function add_files_components($id, $location_code, $attrib_name_componentID)
		{		
			//$exceldata = $this->_getexceldata($_FILES['file']['tmp_name'], false);
			$component_files = array();
			$message = array();
	
			$ficheros = $this->get_files();
			print_r($ficheros); die;
			foreach ($exceldata as $k => $row) 
			{
				if (!$this->_valid_row($row))
				{
					continue;
				}
				
				$array_path = explode("\\", $row[(count($row)-1)]);
						
				$component_files[$row[0]][] = array(
					'name' => $row[1],
					'desription' => $row[2],
					'file' => $array_path[count($array_path)-1],
					'path' => $array_path,
					'row' => ($k + 1)
				);
			}
			print_r($component_files); die;
			$message = $this->search_repeated_names($component_files);
			if ($message['error'])
			{
				return $message;
			}

			$count_new_relations = 0;
			$count_relations_existing = 0;
			$count_new_files = 0;
			$files_existing = array();
			$files_not_existing = array();
	
			foreach ($component_files as $k => $files) 
			{
				if (empty($k))
				{
					$component = array('id' => $id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.'.count(explode('-', $location_code))));
				}
				else {
					$component = $this->_get_component($k, $attrib_name_componentID, $location_code);
					if( empty($component['id']) || empty($component['location_id']))
					{
						$message['message'][] = array('msg' => lang("Component '%1' with location code '%2' does not exist", $k, $location_code));
						continue;
					}
				}
				
				$files_in_component = $this->_get_files_by_component($component['id'], $component['location_id']);

				foreach ($files as $file_data)
				{
					if (in_array(str_replace(' ', '_', $file_data['file']), $files_in_component))
					{
						$count_relations_existing++;
						continue;
					}
					
					$this->db->transaction_begin();
					try
					{
						$this->db->Exception_On_Error = true;						

						$file = $file_data['file'];
						
						$file_id = $this->_search_in_latest_uploads($file_data);
						if (!$file_id) 
						{
							if (!is_file($this->path_upload_dir.$file))
							{
								$files_not_existing[$file] = $file;
								throw new Exception();
							}	

							$file_id = $this->_save_file($file_data);
							if (!$file_id)
							{						
								throw new Exception("failed to copy file '{$file}'. Component: '{$k}'");
							} 
							unlink($this->path_upload_dir.$file);
							$count_new_files++;
						}
						
						$result = $this->_save_file_relation($component['id'], $component['location_id'], $file_id);
						if (!$result)
						{						
							$message['error'][] = array('msg' => "failed to save relation. File: '{$file}'. Component: '{$k}'");
						} else {
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
			} else {
				$message['message'][] = array('msg' => lang('%1 files copy', $count_new_files));
			}
			if ($count_new_relations)
			{
				$message['message'][] = array('msg' => lang('%1 relations saved successfully', $count_new_relations));
			} else {
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
			
			if (count($files_existing))
			{
				foreach($files_existing as $file)
				{
					$message['error'][] = array('msg' => lang("file %1 exist in DB", $file));
				}
			}
			
			return $message;
		}
		
		
		private function _get_component( $query, $attrib_name_componentID, $location_code)
		{
			$location_code_values = explode('-', $location_code);
			$loc1 =  $location_code_values[0];
			 
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

			return $values;
		}
		
		
		private function _save_file( $file_data )
		{
			$metadata = array();
			
			$tmp_file = $file_data['file'];
			
			$bofiles = CreateObject('property.bofiles');
			
			$file_name = str_replace(' ', '_', $tmp_file);

			$to_file = $bofiles->fakebase . '/generic_document/' .$file_name;

			$receipt = $bofiles->create_document_dir("generic_document");
			if (count($receipt['error']))
			{
				throw new Exception('failed to create directory');
			}
			$bofiles->vfs->override_acl = 1;

			$file_id = $bofiles->vfs->cp3(array(
					'from' => $this->path_upload_dir.$tmp_file,
					'to' => $to_file,
		 			'id' => '',
					'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL)));
			$bofiles->vfs->override_acl = 0;

			if ($file_id) 
			{
				$this->latest_uploads[$file_id] = $file_name;
				//$this->latest_uploads_path[$file_id] = $file_data['file-path'];
				
				$metadata['report_date'] = phpgwapi_datetime::date_to_timestamp(date('Y-m-d'));
				$metadata['title'] = $file_data['name']; 
				$metadata['descr'] = $file_data['desription'];
				
				$values_insert = array
					(
					'file_id' => $file_id,
					'metadata' => json_encode($metadata)
				);

				$this->db->query("INSERT INTO phpgw_vfs_filedata (" . implode(',', array_keys($values_insert)) . ') VALUES ('
					. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
			}
			
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

			return $this->db->query("INSERT INTO phpgw_vfs_file_relation (" . implode(',', array_keys($values_insert)) . ') VALUES ('
				. $this->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
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