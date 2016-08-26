<?php
	/**
	 * phpGroupWare - property: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2012 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage logistic
	 * @version $Id: class.uigeneric_document.inc.php 14913 2016-04-11 12:27:37Z sigurdne $
	 */

	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');
	
	include_class('property', 'import_update_components', 'inc/import/');

	class property_uiimport_components extends phpgwapi_uicommon_jquery
	{
		public $public_functions = array(
			'query' => true,
			'index' => true,
			'get_locations_for_type' => true,
			'import_component_files' => true,
			'handle_import_files' => true,
			'import_components' => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->bocommon = CreateObject('property.bocommon');
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->db = & $GLOBALS['phpgw']->db;
			$this->tmp_upload_dir = '/var/lib/phpgw/syncromind/test/';

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "property::documentation::generic";
		}

		public function download()
		{
			return;
		}

		private function valid_row($row)
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
		
		private function getexcelcolumnname( $index )
		{
			//Get the quotient : if the index superior to base 26 max ?
			$quotient = $index / 26;
			if ($quotient >= 1)
			{
				//If yes, get top level column + the current column code
				return $this->getexcelcolumnname($quotient - 1) . chr(($index % 26) + 65);
			}
			else
			{
				//If no just return the current column code
				return chr(65 + $index);
			}
		}
		
		public function import_component_files()
		{
			$location_code = phpgw::get_var('location_code');
			$id = phpgw::get_var('location_item_id');
			$message = array();
			
			phpgw::import_class('phpgwapi.phpexcel');
			
			if (empty($id))
			{
				return $message['error'][] = array('msg' => 'location code is empty');
			}
				
			$sheet_id = phpgw::get_var('sheet_id', 'int', 'REQUEST');

		
			if (isset($_FILES['file']['tmp_name']))
			{
					
				$file = $_FILES['file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";
				// save a copy to survive multiple steps
				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'components_import_file', $cached_file);
			}
			
			$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
			$AllSheets = $objPHPExcel->getSheetNames();

			$sheets = array();
			if ($AllSheets)
			{
				foreach ($AllSheets as $key => $sheet)
					$sheets[] = array
						(
						'id' => ($key + 1),
						'name' => $sheet,
						'selected' => $sheet_id == ($key + 1)
					);
			}					
			
			if ($sheet_id) 
			{
				$cached_file = phpgwapi_cache::session_get('property', 'components_import_file');
				
				$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
				$objPHPExcel->setActiveSheetIndex((int)($sheet_id - 1));
				$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();
				$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);		
	
				$html_table = '<table class="pure-table pure-table-bordered">';
				
				$cols = array();
				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$cols[] = $this->getexcelcolumnname($j);
				}

				$html_table .= "<thead><tr><th align = 'center'>" . lang('select') . "</th><th align = 'center'>" . lang('row') . "</th><th align='center'>" . implode("</th><th align='center'>", $cols) . '</th></tr></thead>';
				foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row)
				{
					if ($i > 20)
					{
						break;
					}
					$i++;

					$row_key = $i;
					$_checked = '';
					if ($start_line == $row_key)
					{
						$_checked = 'checked="checked"';
					}

					$_radio = "<input id=\"start_line\" type =\"radio\" {$_checked} name=\"start_line\" value=\"{$row_key}\">";

					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);

					$row_values = array();
					foreach ($cellIterator as $cell)
					{
						if (!is_null($cell))
						{
							$row_values[] = $cell->getCalculatedValue();
						}
					}
					$html_table .= "<tr><td>{$_radio}</td><td>{$row_key}</td><td>" . implode('</td><td>', $row_values) . '</td></tr>';
				}
				$html_table .= '</table>';
				
				return $html_table;
			}
			
			$result_data = array('results' => $sheets);
			return $this->jquery_results($result_data);
		}
		
		
		public function handle_import_files()
		{
			require_once PHPGW_SERVER_ROOT . "/property/inc/import/UploadHandler.php";
			$options['upload_dir'] = $this->tmp_upload_dir;
			$options['script_url'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_components.handle_import_files'));
			$upload_handler = new UploadHandler($options);
		}

		protected function get_component( $query )
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
		
		
		private function save_file( $tmp_file )
		{
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

			return $file_id;
		}
		
		
		private function save_file_relation( $id, $location_id, $file_id )
		{
			$date_format = phpgwapi_datetime::date_array(date('Y-m-d'));
			$date = mktime(2, 0, 0, $date_format['month'], $date_format['day'], $date_format['year']);
				
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
		
		private function valid_row_component($row)
		{
			if ($row[0] == '' || $row[2] == '')
			{
				return false;
			}
			
			if ($row[0] == 'Systemgruppe' && $row[1] == 'TFM nr' && $row[2] == 'Navn')
			{
				return false;
			}
			
			return true;
		}
		
		public function import_components()
		{
			$get_identificator = false;

			$location_code = phpgw::get_var('location_code');
			
			$entity_categories_in_xml = array();

			$import_components = new import_components();
			$entity_categories  = $import_components->get_entity_categories();

			$exceldata = $this->getexceldata($_FILES['file']['tmp_name'], true);

			foreach ($exceldata as $row) 
			{
				if (!$this->valid_row_component($row))
				{
					continue;
				}

				if (array_key_exists((string)$row[0], $entity_categories))
				{						
					$cat_id = $entity_categories[$row[0]]['id'];
					$entity_id = $entity_categories[$row[0]]['entity_id'];						
				} 
				else {
					$buildingpart_out_table[$row[0]] = $row[0].' - '.$row[2];
					$cat_id = '';
					$entity_id = '';
				}

				if (!empty($row[1]))
				{
					$entity_categories_in_xml[$row[0]]['cat_id'] = $cat_id;
					$entity_categories_in_xml[$row[0]]['entity_id'] = $entity_id;
					$entity_categories_in_xml[$row[0]]['components'][] = array(
						array('name' => 'benevnelse', 'value' => trim($row[1])),
						array('name' => 'beskrivelse', 'value' => trim($row[3]))
					);							
				}				
			}
	//print_r($buildingpart_out_table); die;
			if (count($buildingpart_out_table))
			{
				ksort($buildingpart_out_table);
				$buildingpart_processed = $import_components->add_entity_categories($buildingpart_out_table);
				
				if (count($buildingpart_processed['not_added']))
				{
					foreach($buildingpart_processed['not_added'] as $k => $v)
					{
						$message['error'][] = array('msg' => "parent {$k} not added");	
					}
					return $this->jquery_results($message);
				}
				
				if (count($buildingpart_processed['added']))
				{
					foreach($buildingpart_processed['added'] as $k => $v)
					{
						$entity_categories_in_xml[$k]['cat_id'] = $v['id'];
						$entity_categories_in_xml[$k]['entity_id'] = $v['entity_id'];			
					}
				} 
			}
			


			$message = $import_components->add_bim_item($entity_categories_in_xml, $location_code);
			
			return $this->jquery_results($message);
		}
		
/*
		public function import_components()
		{
			$get_identificator = false;

			$location_code = phpgw::get_var('location_code');
			
			$entity_categories_in_xml = array();

			$result = $this->getxmldata($_FILES['file']['tmp_name'], $get_identificator);

			$postnrdelkode = $result['Prosjekter']['ProsjektNS']['Postnrplan']['PostnrdelKoder']['PostnrdelKode'];
			$entities_name = array();
			foreach ($postnrdelkode as $items) 
			{
				if ($items['PostnrdelKoder']['PostnrdelKode']['Kode'])
				{
						$entities_name[$items['PostnrdelKoder']['PostnrdelKode']['Kode']] = array(
							'name' => $items['PostnrdelKoder']['PostnrdelKode']['Kode'].' - '.$items['PostnrdelKoder']['PostnrdelKode']['Navn']
						);							
				}
				else {
					foreach ($items['PostnrdelKoder']['PostnrdelKode'] as $item) 
					{
						$entities_name[$item['Kode']] = array('name' => $item['Kode'].' - '.$item['Navn']);
					}
				}
			}

			$posts = $result['Prosjekter']['ProsjektNS']['Prosjektdata']['Post'];
			foreach ($posts as $post) 
			{
				$buildingpart = $post['Postnrdeler']['Postnrdel'][1]['Kode'];
				$entity_categories_in_xml[$buildingpart]['name'] = $entities_name[$buildingpart]['name'];
				$entity_categories_in_xml[$buildingpart]['components'][] = array(
					array('name' => 'benevnelse', 'value' => trim($post['Egenskaper']['Egenskap']['Verdi'])),
					array('name' => 'beskrivelse', 'value' => trim($post['Tekst']['Uformatert']))
				);
			}

			$import_components = new import_components();
			$entity_categories  = $import_components->get_entity_categories();

			$buildingpart_out_table = array();
			foreach ($entity_categories_in_xml as $k => $v) 
			{
				if (!array_key_exists((string)$k, $entity_categories))
				{
					$buildingpart_parent = substr($k, 0, strlen($k) -1);
					$buildingpart_out_table[$k] = array('parent' => $entity_categories[$buildingpart_parent], 'name' => $v['name']);
				} else {
					$entity_categories_in_xml[$k]['cat_id'] = $entity_categories[$k]['id'];
					$entity_categories_in_xml[$k]['entity_id'] = $entity_categories[$k]['entity_id'];
				}
			}

			if (count($buildingpart_out_table))
			{
				$buildingpart_processed = $import_components->add_entity_categories($buildingpart_out_table);
				
				if (count($buildingpart_processed['not_added']))
				{
					foreach($buildingpart_processed['not_added'] as $k => $v)
					{
						$message['error'][] = array('msg' => "parent {$k} not added");	
					}
					return $this->jquery_results($message);
				}
				
				if (count($buildingpart_processed['added']))
				{
					foreach($buildingpart_processed['added'] as $k => $v)
					{
						$entity_categories_in_xml[$k]['cat_id'] = $v['id'];
						$entity_categories_in_xml[$k]['entity_id'] = $v['entity_id'];			
					}
				} 
			}

			$message = $import_components->add_bim_item($entity_categories_in_xml, $location_code);
			
			return $this->jquery_results($message);
		}
*/

		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			$tabs = array();
			$tabs['locations'] = array('label' => lang('Locations'), 'link' => '#locations');
			$tabs['files'] = array('label' => lang('Files'), 'link' => '#files', 'disable' => 0);
			$tabs['components'] = array('label' => lang('Components'), 'link' => '#components', 'disable' => 1);
			$tabs['relations'] = array('label' => lang('Relations'), 'link' => '#relations', 'disable' => 1);
			
			$active_tab = 'locations';

			$type_filter = 	execMethod('property.soadmin_location.read', array());			
			$category_filter = $this->get_categories_for_type();

			$district_filter = $this->bocommon->select_district_list('filter');
			array_unshift($district_filter, array('id' => '', 'name' => lang('no district')));

			$part_of_town_filter = $this->get_part_of_town();

			$related_def = array
				(
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => false, 'resizeable' => true),
				array('key' => 'loc1_name', 'label' => lang('name'), 'sortable' => false, 'resizeable' => true)
			);


			$datatable_def[] = array
			(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiimport_components.get_locations_for_type', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'tabletools' => array(),
				'config' => array(
					array('singleSelect' => true)
				)				
			);	
				
			$form_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_components.handle_import_files'));
			
			$data = array
			(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				
				'type_filter' => array('options' => $type_filter),
				'category_filter' => array('options' => $category_filter),
				'district_filter' => array('options' => $district_filter),
				'part_of_town_filter' => array('options' => $part_of_town_filter),
				'form_file_upload' => phpgwapi_jquery::form_file_upload_generate($form_upload_action)
			);

			self::add_javascript('property', 'portico', 'import_components.js');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('Importer ');

			self::render_template_xsl(array('import_components', 'datatable_inline'), $data);
		}

		public function get_locations_for_type()
		{
			$type_id = phpgw::get_var('type_id', 'int');

			if (!$type_id)
			{
				$type_id = 1;
			}
			
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'cat_id' => phpgw::get_var('cat_id', 'int', 'REQUEST', 0),
				'type_id' => $type_id,
				'district_id' => phpgw::get_var('district_id', 'int', 'REQUEST', 0),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int', 'REQUEST', 0),
				'allrows' => phpgw::get_var('length', 'int') == -1
			);
			
            $solocation = CreateObject('property.solocation');
            $locations = $solocation->read($params);

			$values = array();
			foreach($locations as $item)
			{
				$values[] = array(
					'id' => $item['id'],
					'location_code' => $item['location_code'],
					'loc1_name' => $item['loc1_name']
				);				
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $solocation->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}
		
		/**
		 * Fetch data from $this->bo based on parametres
		 * @return array
		 */
		public function query()
		{
			return;
		}
		
		public function get_categories_for_type()
		{
			$type_id = phpgw::get_var('type_id', 'int');

			if (!$type_id)
			{
				$type_id = 1;
			}
			
			$categories = $this->bocommon->select_category_list(array
				('format' => 'filter',
				'selected' => '',
				'type' => 'location',
				'type_id' => $type_id,
				'order' => 'descr')
			);
			array_unshift($categories, array('id' => '', 'name' => lang('no category')));

			return $categories;
		}
		
		public function get_part_of_town()
		{
			$district_id = phpgw::get_var('district_id', 'int');
			$values = $this->bocommon->select_part_of_town('filter', '', $district_id);
			array_unshift($values, array('id' => '', 'name' => lang('no part of town')));

			return $values;
		}
		
		private function _xml2array ( $xmlObject, $out = array () )
		{
			foreach ( (array) $xmlObject as $index => $node )
			{
				$out[$index] = ( is_object($node) || is_array($node) ) ? $this->_xml2array ( $node ) : $node;
			}
			
			return $out;
		}

		protected function getxmldata( $path, $get_identificator = true )
		{
			$xml = simplexml_load_file($path);
			$out = $this->_xml2array($xml);

			return $out;
		}
		
		protected function getexceldata( $path, $get_identificator = false )
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

			$this->messages[] = "Read '{$path}' file in " . (time() - $start_time) . " seconds";
			$this->messages[] = "'{$path}' contained " . count($result) . " lines";

			return $result;
		}
	}