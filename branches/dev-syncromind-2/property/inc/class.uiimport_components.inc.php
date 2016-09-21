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
	
	include_class('property', 'import_entity_categories', 'inc/import/');
	include_class('property', 'import_components', 'inc/import/');

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
			'import_components' => true,
			'get_attributes_for_template' => true,
			'download' => true
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
			$config = createObject('phpgwapi.config', 'component_import');
			$values = $config->read_repository();
			$components = $values['preview_components'];
			
			$fields = array_keys($components[0]);

			$bocommon = CreateObject('property.bocommon');
			$bocommon->download($components, $fields, $fields);
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
				$message['error'][] = array('msg' => 'location code is empty');
				return $message;
			}
				
			$exceldata = $this->getexceldata($_FILES['file']['tmp_name'], true);
			$component_files = array();
			
			foreach ($exceldata as $row) 
			{
				if (!$this->valid_row($row))
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
				
				foreach ($component_files as $k => $files) 
				{
					if (empty($k))
					{
						$component = array('id' => $id, 'location_id' => $GLOBALS['phpgw']->locations->get_id('property', '.location.'.count(explode('-', $location_code))));
					}
					else {
						$component = $this->get_component($k);
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
						
						$file_id = $this->save_file($file_data);
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
					return $message;
				}
			}

			$this->db->transaction_commit();
			$message['message'][] = array('msg' => 'all files saved successfully');
			
			return $message;
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
		
		
		private function save_file( $file_data )
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
		
		
		private function save_file_relation( $id, $location_id, $file_id )
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
		
		
		public function import_components()
		{
			$location_code = phpgw::get_var('location_code');
			$id = phpgw::get_var('location_item_id');
			$template_id = phpgw::get_var('template_id');
			$component_id = phpgw::get_var('component_id');
			
			$step = phpgw::get_var('step', 'int', 'REQUEST');
			$sheet_id = phpgw::get_var('sheet_id', 'int', 'REQUEST');
			$start_line = phpgw::get_var('start_line', 'int', 'REQUEST');
			
			$columns = phpgw::get_var('columns');
			$columns = $columns && is_array($columns) ? $columns : array();
			$attrib_data_types = phpgw::get_var('attrib_data_types');
			$attrib_names = phpgw::get_var('attrib_names');
			$attrib_precision = phpgw::get_var('attrib_precision');
			
			$save = phpgw::get_var('save', 'int', 'REQUEST');
					
			$receipt = array();
			
			phpgw::import_class('phpgwapi.phpexcel');
			
			if (empty($id))
			{
				return $receipt['error'][] = array('msg' => 'location code is empty');
			}
				
			if (empty($template_id))
			{
				return $receipt['error'][] = array('msg' => 'template id is empty');
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
						
			if ($step > 1 && $step < 5) 
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
					'building_part' => '-- Building Part',
					'name_building_part' => '-- Name of the Building Part',
					'component_id'    => '-- Component ID'
				);
				
				$template = explode("_", $template_id);
				
				$attributes = $this->custom->find($this->type_app[$this->type], ".{$this->type}.{$template[0]}.{$template[1]}", 0, '', 'ASC', 'attrib_sort', true, true);

				foreach ($attributes as $attribute)
				{
					$_options[$attribute['name']] = $attribute['input_text'];
				}
				
				$data_types = $this->bocommon->select_datatype();
				$_options_data_type[''] = 'data type';
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
					$html_table .= "<td><input type='text' id='name_{$_column}' name='names[{$_column}]' disabled class='names' placeholder='column name'></input></td>";
					$html_table .= "<td>{$_listTypes}</td>";
					$html_table .= "<td><input type='text' id='precision_{$_column}' name='precision[{$_column}]' disabled class='precision' placeholder='length'></input></td>";
					$html_table .= "</tr>";
				}
				
				$html_table .= '</table>';
				
				return $html_table;
			}
			
			if ($step == 4 && $start_line) 
			{
				$import_entity_categories = new import_entity_categories($template_id);
				$import_components = new import_components();
				
				if (count($attrib_names))
				{
					$receipt = $import_entity_categories->prepare_attributes_for_template($columns, $attrib_names, $attrib_data_types, $attrib_precision);
					if ($receipt['error'])
					{
						return $receipt;
					} else {
						$new_attribs_for_template = $receipt['new_attribs_for_template'];
					}
				}

				$rows = $rows ? $rows + 1 : 0;

				$building_part_out_table = array();
				$building_part_in_table = array();
				$import_data = array();
					
				$list_entity_categories  = $import_entity_categories->list_entity_categories();
			
				for ($i = $start_line; $i < $rows; $i++)
				{
					$_result = array();

					foreach ($columns as $_row_key => $_value_key)
					{
						$_result[$_value_key] = $objPHPExcel->getActiveSheet()->getCell("{$_row_key}{$i}")->getCalculatedValue();
					}
					
					if ((int)$_result['building_part'] || $_result['building_part'] === '0')
					{
						$cat_id = '';
						$entity_id = '';
						
						if (array_key_exists((string)$_result['building_part'], $list_entity_categories))
						{
							if (!empty($_result['component_id']))
							{
								$cat_id = $list_entity_categories[$_result['building_part']]['id'];
								$entity_id = $list_entity_categories[$_result['building_part']]['entity_id'];
								
								$building_part_in_table[$_result['building_part']] = array('entity_id' => $entity_id, 'cat_id' => $cat_id);
							}
						}
						else {
							if (empty($_result['component_id']))
							{
								$building_part_out_table[$_result['building_part']] = $_result['building_part'].' '.$_result['name_building_part'];
							}
						}

						if (!empty($_result['component_id']))
						{
							$import_data[$_result['building_part']]['cat_id'] = $cat_id;
							$import_data[$_result['building_part']]['entity_id'] = $entity_id;
							
							$_result = array($component_id => $_result['component_id']) + $_result;
							$_result = array('building part' => $_result['building_part']) + $_result;
	
							$import_data[$_result['building_part']]['components'][] = $_result;						
						}
					}
				}
			
				if (count($building_part_out_table))
				{
					asort($building_part_out_table);
					$receipt = $import_entity_categories->prepare_entity_categories($building_part_out_table);
					if ($receipt['error'])
					{
						return $receipt;
					} else {
						$new_entity_categories = $receipt['new_entity_categories'];
					}
				}
				
				$receipt = array();
				$preview_components = $import_components->prepare_preview_components($import_data);
				
				$config = createObject('phpgwapi.config', 'component_import');
				
				if (count($new_attribs_for_template))
				{
					$config->value('new_attribs_for_template', serialize($new_attribs_for_template));
					foreach($new_attribs_for_template as $attrib)
					{
						$values[] = $attrib['column_name'];
					}
					$receipt['new_attribs_for_template'] = $values;
				} else {
					$receipt['message'][] = array('msg' => lang('Not exist attributes to insert the template'));
				}
						
				if (count($new_entity_categories))
				{
					$config->value('new_entity_categories', serialize($new_entity_categories));
					$receipt['new_entity_categories'] = array_values($new_entity_categories);
				} else {
					$receipt['message'][] = array('msg' => lang('Not exist new entity categories'));
				}
				
				$config->value('building_part_in_table', serialize($building_part_in_table));
				$config->value('preview_components', serialize($preview_components));
				$config->value('new_components', serialize($import_data));
				$config->save_repository();
			
				return $receipt;
			}
			
			if ($step == 5 && $save) 
			{
				$message = array();
				
				$import_entity_categories = new import_entity_categories($template_id);
				$import_components = new import_components();
				
				$receipt = $import_entity_categories->add_attributes_to_template();
				if ($receipt['error'])
				{
					return $receipt;
				}
				array_push($message['message'], array_values($receipt['message']));
			
				$receipt = $import_entity_categories->add_attributes_to_categories();
				if ($receipt['error'])
				{
					return $receipt;
				}
				array_push($message['message'], array_values($receipt['message']));
				
				$building_part_processed = $import_entity_categories->add_entity_categories();

				if (count($building_part_processed['not_added']))
				{
					foreach($building_part_processed['not_added'] as $k => $v)
					{
						$message['message'][] = array('msg' => "Entity category {$v} not added");	
					}
				}

				$config = createObject('phpgwapi.config', 'component_import');
				$config_repository = $config->read_repository();
				$import_data = $config_repository['new_components'];

				if (count($building_part_processed['added']))
				{
					foreach($building_part_processed['added'] as $k => $v)
					{
						$import_data[$k]['cat_id'] = $v['id'];
						$import_data[$k]['entity_id'] = $v['entity_id'];			
					}
				} 

				$receipt = $import_components->add_components($import_data, $location_code, $component_id);
				if ($receipt['error'])
				{
					return $receipt;
				}
				//array_push($message['message'], array_values($receipt['message']));
			
				return $receipt;
			}
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
				'form_file_upload' => phpgwapi_jquery::form_file_upload_generate($form_upload_action),
				'image_loader' => $GLOBALS['phpgw']->common->image('property', 'ajax-loader', '.gif', false)
			);

			self::add_javascript('property', 'portico', 'import_components.js');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('Importer ');

			self::render_template_xsl(array('import_components', 'datatable_inline'), $data);
		}

		public function get_attributes_for_template()
		{
			$category_template = phpgw::get_var('category_template');

			$template_info = explode('_', $category_template);
			$template_entity_id = $template_info[0];
			$template_cat_id = $template_info[1];

			$attrib_list = $this->bo->read_attrib(array('entity_id' => $template_entity_id, 'cat_id' => $template_cat_id, 'allrows' => true));
			$list = array();
			foreach ($attrib_list as $attrib)
			{
				$list[] = array('id' => $attrib['column_name'], 'name' => $attrib['input_text']); 
			}
			
			array_unshift($list, array('id' => '', 'name' => lang('Select Component ID')));

			return $list;
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