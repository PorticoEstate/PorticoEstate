<?php

	class import_component_files
	{	
		public function __construct()
		{
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->db = & $GLOBALS['phpgw']->db;
			$this->tmp_upload_dir = '/var/lib/phpgw/syncromind/test/';
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
		
		public function add_files($id, $location_code)
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
						$component = $this->_get_component($k);
						if( empty($component['id']) || empty($component['location_id']))
						{
							throw new Exception("component {$k} does not exist");
						}
					}
					
					foreach($files as $file_data)
					{
						$file = $file_data['file'];
						
						if (!is_file($this->tmp_upload_dir.$file))
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
			$message['message'][] = array('msg' => '%1 files saved successfully', $count);		
			
			return $message;
		}
		
		
		private function _get_component( $query )
		{
			if ($query)
			{
				$query = $this->db->db_addslashes($query);
			}

			$sql = "SELECT * FROM fm_bim_item WHERE json_representation->>'benevnelse' = '{$query}'";

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
					'from' => $this->tmp_upload_dir.$tmp_file,
					'to' => $to_file,
		 			'id' => '',
					'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL)));
			$bofiles->vfs->override_acl = 0;

			if ($file_id) 
			{
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