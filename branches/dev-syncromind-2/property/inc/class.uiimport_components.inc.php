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
		var $type = 'entity';
		protected $type_app = array
			(
			'entity' => 'property',
			'catch' => 'catch'
		);
		
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
			$this->custom = CreateObject('property.custom_fields');
			$this->bo = CreateObject('property.boadmin_entity', true);
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
			
			if (empty($id))
			{
				return $message['error'][] = array('msg' => 'location code is empty');
			}
				
			$exceldata = $this->getexceldata($_FILES['file']['tmp_name'], true);
			$relations = array();
			
			foreach ($exceldata as $row) 
			{
				if (!$this->valid_row($row))
				{
					continue;
				}
				
				$relations[$row[0]][] = $row[(count($row)-1)];
			}
			
			$this->db->transaction_begin();
			
			try
			{
				$this->db->Exception_On_Error = true;
				
				foreach ($relations as $k => $files) 
				{
					if (empty($k))
					{
						$component = array('id' => $id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.'.count(explode('-', $location_code))));
					}
					else {
						$component = $this->get_component[$k];
						if( empty($component['id']) || empty($component['location_id']))
						{
							throw new Exception("component {$k} does not exist");
						}
					}
					
					foreach($files as $path_file)
					{
						$parts = explode("\\", $path_file);
						$file = $parts[count($parts)-1];
						if (!is_file($this->tmp_upload_dir.$file))
						{
							throw new Exception("the file {$file} does not exist, component: {$k}");
						}	
						
						$file_id = $this->save_file($file);
						if (!$file_id)
						{						
							throw new Exception("failed to save file {$file}, component: {$k}");
						} 
			
						$result = $this->save_file_relation($component['id'], $component['location_id'], $file_id);
						if (!$result)
						{						
							throw new Exception("failed to save relation, file: {$file}, component: {$k}");
						}
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
					return $this->jquery_results($message);
				}
			}

			$this->db->transaction_commit();
			$message['message'][] = array('msg' => 'all files saved successfully');
			
			return $this->jquery_results($message);
		}
		
		private function getArrayItem($id, $name, $selected, $options = array(), $no_lang = false, $attribs = '' )
		{
			// should be in class common.sbox
			if ( !is_array($options) || !count($options) )
			{
				$options = array('no', 'yes');
			}

			$html = <<<HTML
			<select name="$name" id="$id" $attribs>

HTML;

			$check = array();

			if (!is_array($selected))
			{
				$check[$selected] = true;	
			}
			else
			{
				foreach ($selected as $sel)
				{
					$check[$sel] = true;
				}
			}

			foreach ( $options as $value => $option )
			{
				$check2 = isset( $check[$value] ) ? ' selected' : '';
				$option = $no_lang ? $option : lang($option);

				$html .= <<<HTML
					<option value="{$value}"{$check2}>{$option}</option>

HTML;
			}
			$html .= <<<HTML
			</select>

HTML;
			return $html;
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
		
		
		public function import_components()
		{
			$location_code = phpgw::get_var('location_code');
			$id = phpgw::get_var('location_item_id');
			$template_id = phpgw::get_var('template_id');
			
			$step = phpgw::get_var('step', 'int', 'REQUEST');
			$sheet_id = phpgw::get_var('sheet_id', 'int', 'REQUEST');
			$start_line = phpgw::get_var('start_line', 'int', 'REQUEST');
			
			$columns = phpgw::get_var('columns');
			$columns = $columns && is_array($columns) ? $columns : array();
			$attrib_data_types = phpgw::get_var('attrib_data_types');
			$attrib_names = phpgw::get_var('attrib_names');
			$attrib_precision = phpgw::get_var('attrib_precision');
					
			$receipt = array();
			
			phpgw::import_class('phpgwapi.phpexcel');
			
			if (empty($id))
			{
				return $receipt['error'][] = array('msg' => 'location code is empty');
			}
				
			if ($step == 1 && isset($_FILES['file']['tmp_name']))
			{
				$file = $_FILES['file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";

				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'components_import_file', $cached_file);
				
				$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
				$AllSheets = $objPHPExcel->getSheetNames();

				$sheets = array();
				if ($AllSheets)
				{
					foreach ($AllSheets as $key => $sheet)
					{
						$sheets[] = array
							(
							'id' => ($key + 1),
							'name' => $sheet
						);
					}
				}	
				
				return $sheets;
			}
						
			if ($step > 1) 
			{
				$cached_file = phpgwapi_cache::session_get('property', 'components_import_file');
				
				$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
				$objPHPExcel->setActiveSheetIndex((int)($sheet_id - 1));
				$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();
				$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);					
			}	
			
			if ($step == 2 && $sheet_id) 
			{
				$html_table = '<table class="pure-table pure-table-bordered">';
				$i = 0;
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

					$_radio = "<input type =\"radio\" name=\"start_line\" value=\"{$row_key}\">";

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
			
			if ($step == 3 && $start_line) 
			{
				$html_table = '<table class="pure-table pure-table-bordered">';
				
				$_options = array
				(
					'' => ' ... ',
					'new_column' => 'New column',
					'building_part' => 'Building part',
					'category_name' => 'Categry name'
				);
				
				$template = explode("_", $template_id);
				
				$attributes = $this->custom->find($this->type_app[$this->type], ".{$this->type}.{$template[0]}.{$template[1]}", 0, '', 'ASC', 'attrib_sort', true, true);

				foreach ($attributes as $attribute)
				{
					$_options[$attribute['name']] = $attribute['input_text'];
				}
				
				$data_types = $this->bocommon->select_datatype();
				$_options_data_type[''] = 'select data type';
				foreach($data_types as $row) 
				{
					$_options_data_type[$row['id']] = $row['name'];
				}

				for ($j = 0; $j < $highestColumnIndex; $j++)
				{
					$_column = $this->getexcelcolumnname($j);
					$_value = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $start_line)->getCalculatedValue();
					$selected = isset($columns[$_column]) && $columns[$_column] ? $columns[$_column] : '';

					$_listbox = $this->getArrayItem("column_{$_column}", "columns[{$_column}]", $selected, $_options, true, "onchange=\"enabledAtributes('{$_column}')\" class='columns'");
					$_listTypes = $this->getArrayItem("data_type_{$_column}", "data_types[{$_column}]", $selected, $_options_data_type, true, "disabled class='data_types'");
					$html_table .= "<tr>";
					$html_table .= "<td>[{$_column}] {$_value}</td>";
					$html_table .= "<td>{$_listbox}</td>";
					$html_table .= "<td><input type='text' id='name_{$_column}' name='names[{$_column}]' disabled class='names'></input></td>";
					$html_table .= "<td>{$_listTypes}</td>";
					$html_table .= "<td><input type='text' id='precision_{$_column}' name='precision[{$_column}]' disabled class='precision'></input></td>";
					$html_table .= "</tr>";
				}
				
				$html_table .= '</table>';
				
				return $html_table;
			}
			
			if ($step == 4 && $start_line) 
			{
				if (count($attrib_names))
				{
					$receipt = $this->add_attribute_to_template($columns, $attrib_names, $attrib_data_types, $attrib_precision, $template_id);
					if ($receipt['error'])
					{
						print_r($receipt); die;
					}
				}
					
				//$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();
				$rows = $rows ? $rows + 1 : 0;

				$buildingpart_out_table = array();
				$import_data = array();

				$import_components = new import_components();
				$entity_categories  = $import_components->get_entity_categories();
			
				for ($i = $start_line; $i < $rows; $i++)
				{
					$_result = array();

					foreach ($columns as $_row_key => $_value_key)
					{
						$_result[$_value_key] = $objPHPExcel->getActiveSheet()->getCell("{$_row_key}{$i}")->getCalculatedValue();
					}
					
					if ((int)$_result['building_part'] || $_result['building_part'] === '0')
					{
						if (array_key_exists((string)$_result['building_part'], $entity_categories))
						{						
							$cat_id = $entity_categories[$_result['building_part']]['id'];
							$entity_id = $entity_categories[$_result['building_part']]['entity_id'];						
						} 
						else {
							$buildingpart_out_table[$_result['building_part']] = $_result['building_part'].' - '.$_result['category_name'];
							$cat_id = '';
							$entity_id = '';
						}

						if (!empty($_result['benevnelse']))
						{
							$import_data[$_result['building_part']]['cat_id'] = $cat_id;
							$import_data[$_result['building_part']]['entity_id'] = $entity_id;
							$import_data[$_result['building_part']]['components'] = $_result;						
						}
					}
				}
				
				if (count($buildingpart_out_table))
				{
					ksort($buildingpart_out_table);
					$buildingpart_processed = $import_components->add_entity_categories($buildingpart_out_table);

					if (count($buildingpart_processed['not_added']))
					{
						foreach($buildingpart_processed['not_added'] as $k => $v)
						{
							$receipt['error'][] = array('msg' => "parent {$k} not added");	
						}
						return $this->jquery_results($receipt);
					}

					if (count($buildingpart_processed['added']))
					{
						foreach($buildingpart_processed['added'] as $k => $v)
						{
							$import_data[$k]['cat_id'] = $v['id'];
							$import_data[$k]['entity_id'] = $v['entity_id'];			
						}
					} 
				}

				$receipt = $import_components->add_bim_item($import_data, $location_code);
			
				return $this->jquery_results($receipt);
			}
		}
		
		private function add_attribute_to_template(&$columns, $attrib_names, $attrib_data_types, $attrib_precision, $template_id)
		{
			$receipt = array();
			
			$template = explode('_', $template_id);
			$entity_id = $template[0];
			$cat_id = $template[1];

			$appname = $this->type_app[$this->type];
			$location = ".{$this->type}.{$entity_id}.{$cat_id}";
			$attrib_table = $GLOBALS['phpgw']->locations->get_attrib_table($appname, $location);
			
			$attributes = array();
			
			foreach ($columns as $_row_key => $_value_key)
			{
				$attrib = array();
				if ($_value_key == 'new_column')
				{
					$attrib['entity_id'] = $entity_id;
					$attrib['cat_id'] = $cat_id;
					$attrib['appname'] = $appname;
					$attrib['location'] = $location;
			
					$attrib['column_name'] = $attrib_names[$_row_key];
					$attrib['input_text'] = ucfirst($attrib_names[$_row_key]);
					$attrib['statustext'] = ucfirst($attrib_names[$_row_key]);
					$attrib['column_info']['type'] = $attrib_data_types[$_row_key];
					$attrib['column_info']['precision'] = $attrib_precision[$_row_key];
					$attrib['column_info']['nullable'] = 'True';
					$attrib['search'] = 1;
					
					$receipt = $this->valid_attributes($attrib);
					if ($receipt['error'])
					{
						break;
					}
					$attrib['_row_key'] = $_row_key;
					$attributes[] = $attrib;
				}
			}
			
			foreach($attributes as $attrib)
			{
				$id = $this->custom->add($attrib, $attrib_table);	
				if ($id <= 0)
				{
					$receipt['error'][] = array('msg' => lang('Unable to add field'));
					break;
				}
				else if ($id == -1)
				{
					$receipt['error'][] = array('msg' => lang('field already exists, please choose another name'));
					$receipt['error'][] = array('msg' => lang('Attribute has NOT been saved'));
					break;
				}
				$columns[$attrib['_row_key']] = $attrib['column_name'];
			}
			
			return $receipt;
		}
		
		private function valid_attributes($values)
		{
			$receipt = array();
			
			if (!$values['column_name'])
			{
				$receipt['error'][] = array('msg' => lang('Column name not entered!'));
			}

			if (!preg_match('/^[a-z0-9_]+$/i', $values['column_name']))
			{
				$receipt['error'][] = array('msg' => lang('Column name %1 contains illegal character', $values['column_name']));
			}

			if (!$values['input_text'])
			{
				$receipt['error'][] = array('msg' => lang('Input text not entered!'));
			}
			
			if (!$values['statustext'])
			{
				$receipt['error'][] = array('msg' => lang('Statustext not entered!'));
			}

			if (!$values['entity_id'])
			{
				$receipt['error'][] = array('msg' => lang('entity type not chosen!'));
			}

			if (!$values['column_info']['type'])
			{
				$receipt['error'][] = array('msg' => lang('Datatype type not chosen!'));
			}

			if (!ctype_digit($values['column_info']['precision']) && $values['column_info']['precision'])
			{
				$receipt['error'][] = array('msg' => lang('Please enter precision as integer !'));
			}

			if (!$values['column_info']['nullable'])
			{
				$receipt['error'][] = array('msg' => lang('Nullable not chosen!'));
			}			
			
			return $receipt;
		}
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
				
			$entity_list = $this->bo->read(array('allrows' => true));
			$category_list = array();
			foreach ($entity_list as $entry)
			{
				$cat_list = $this->bo->read_category(array('entity_id' => $entry['id'], 'allrows' => true));

				foreach ($cat_list as $category)
				{
					$category_list[] = array
						(
						'id' => "{$entry['id']}_{$category['id']}",
						'name' => "{$entry['name']}::{$category['name']}"
					);
				}
			}
		
			$form_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_components.handle_import_files'));

			$data = array
			(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				
				'type_filter' => array('options' => $type_filter),
				'category_filter' => array('options' => $category_filter),
				'district_filter' => array('options' => $district_filter),
				'part_of_town_filter' => array('options' => $part_of_town_filter),
				'template_list' => array('options' => $category_list),
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
		
		public function get_data_type()
		{
			$values = $this->bocommon->select_datatype();
			return $values;
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