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
			if (empty($row[0]) && empty($row[(count($row)-1)]))
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
		
		private function _search_in_latest_uploads($file)
		{
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
		
		public function add_files($id, $location_code, $attrib_name_componentID)
		{		
			$exceldata = $this->_getexceldata($_FILES['file']['tmp_name'], true);
			$component_files = array();
			$message = array();
			
			foreach ($exceldata as $row) 
			{
				if (!$this->_valid_row($row))
				{
					continue;
				}
				
				$array_path = explode("\\", $row[(count($row)-1)]);
						
				$component_files[$row[0]][] = array(
					'name' => $row[1],
					'desription' => $row[2],
					'file' => $array_path[count($array_path)-1]
				);
			}

			$count_new_relations = 0;
			$count_relations_existing = 0;
			$count_new_files = 0;
			$count_files_existing = 0;
			
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
						
						$file_id = $this->_search_in_latest_uploads($file);
						if (!$file_id)
						{
							$file_id = $this->_search_file_in_db($file);
							if ($file_id)
							{
								throw new Exception("file '{$file}' exist in DB. Component: '{$k}'");
								$count_files_existing++;
							}

							if (!is_file($this->path_upload_dir.$file))
							{
								throw new Exception("file '{$file}' does not exist in folder temporary. Component: '{$k}'");
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
							$message['error'][] = array('msg' => $e->getMessage());
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
			if ($count_relations_existing)
			{
				$message['message'][] = array('msg' => lang('%1 relations existing', $count_relations_existing));
			}
			if ($count_new_relations)
			{
				$message['message'][] = array('msg' => lang('%1 relations saved successfully', $count_new_relations));
			}
			if ($count_files_existing)
			{
				$message['message'][] = array('msg' => lang('%1 files already exist and were rejected', $count_files_existing));
			}
			
			return $message;
		}
		
		
		/*public function add_files($id, $location_code, $attrib_name_componentID)
		{		
			$exceldata = $this->_getexceldata($_FILES['file']['tmp_name'], true);
			$component_files = array();
			$message = array();
			
			foreach ($exceldata as $row) 
			{
				if (!$this->_valid_row($row))
				{
					continue;
				}
				
				$array_path = explode("\\", $row[(count($row)-1)]);
						
				$component_files[$row[0]][] = array(
					'name' => $row[1],
					'desription' => $row[2],
					'file' => $array_path[count($array_path)-1]
				);
			}

			$this->db->transaction_begin();
			
			try
			{
				$this->db->Exception_On_Error = true;
				
				$count = 0;
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
							throw new Exception("component {$k} does not exist");
						}
					}
					
					foreach($files as $file_data)
					{
						$file = $file_data['file'];
						
						if (!is_file($this->path_upload_dir.$file))
						{
							throw new Exception("the file {$file} does not exist, component: {$k}");
						}	
						
						$file_id = $this->_save_file($file_data);
						if (!$file_id)
						{						
							throw new Exception("failed to save file {$file}, component: {$k}");
						} 
			
						$result = $this->_save_file_relation($component['id'], $component['location_id'], $file_id);
						if (!$result)
						{						
							throw new Exception("failed to save relation, file: {$file}, component: {$k}");
						}
						$count++;
					}
				}
				$this->db->Exception_On_Error = false;
			}
			catch (Exception $e)
			{
				if ($e)
				{
					$this->db->transaction_abort();				
					$message['error'][] = array('msg' => $e->getMessage());
					return $message;
				}
			}

			$this->db->transaction_commit();
			$message['message'][] = array('msg' => lang('%1 files saved successfully', $count));		
			
			return $message;
		}*/
		
		
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