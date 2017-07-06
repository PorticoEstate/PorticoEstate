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
	include_class('property', 'import_component_files', 'inc/import/');

	class property_uiimport_components extends phpgwapi_uicommon_jquery
	{

		var $type = 'entity';
		private $receipt = array();
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
			'get_attributes_from_template' => true,
			'get_profile' => true,
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
			$this->config = createObject('phpgwapi.config', 'component_import');
			$this->config_repository = $this->config->read_repository();
		}

		public function download()
		{
			$components = phpgwapi_cache::session_get('property', 'preview_components');
			$components = ($components) ? unserialize($components) : array();

			$fields = array_keys($components[0]);

			$this->bocommon->download($components, $fields, $fields);
		}

		private function _msg_data( $receipt )
		{
			if (isset($receipt['error']) && is_array($receipt['error']))
			{
				foreach ($receipt['error'] as $dummy => $error)
				{
					$this->receipt['error'][] = $error;
				}
			}

			if (isset($receipt['message']) && is_array($receipt['message']))
			{
				foreach ($receipt['message'] as $dummy => $message)
				{
					$this->receipt['message'][] = $message;
				}
			}

			return $this->receipt;
		}

		private function _getexcelcolumnname( $index )
		{
			//Get the quotient : if the index superior to base 26 max ?
			$quotient = $index / 26;
			if ($quotient >= 1)
			{
				//If yes, get top level column + the current column code
				return $this->_getexcelcolumnname($quotient - 1) . chr(($index % 26) + 65);
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
			//$id = phpgw::get_var('location_item_id');
			//$attrib_name_componentID = phpgw::get_var('attribute_name_component_id');
			$preview = phpgw::get_var('preview');
			$with_components = phpgw::get_var('with_components_check');
			$doc_cat_id =  phpgw::get_var('doc_cat_id');

			/* if ($_FILES['file']['tmp_name'])
			  {
			  if (!$attrib_name_componentID)
			  {
			  $receipt['error'][] = array('msg' => lang('Choose attribute name for Component ID'));
			  return $receipt;
			  }
			  } */

			if (!$location_code)
			{
				$receipt['error'][] = array('msg' => lang('Choose Location'));
				return $receipt;
			}
			if (!$doc_cat_id)
			{
				$receipt['error'][] = array('msg' => lang('category'));
				return $receipt;
			}

			$import_component_files = new import_component_files();

			if ($preview)
			{
				$receipt = $import_component_files->preview();
				return $receipt;
			}

			if ($with_components)
			{
				$receipt = $import_component_files->add_files_components_location();
			}
			else
			{
				$receipt = $import_component_files->add_files_location();
			}

			return $receipt;
		}

		private function _getArrayItem( $id, $name, $selected, $options = array(), $no_lang = false, $attribs = '' )
		{
			// should be in class common.sbox
			if (!is_array($options) || !count($options))
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

			foreach ($options as $value => $option)
			{
				$check2 = isset($check[$value]) ? ' selected' : '';
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
			$path_upload_dir = phpgwapi_cache::session_get('property', 'path_upload_dir');
			if (empty($path_upload_dir))
			{
				return false;
			}
			phpgw::import_class('property.multiuploader');

			$options['upload_dir'] = $path_upload_dir;
			$options['script_url'] = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_components.handle_import_files'));
			$upload_handler = new property_multiuploader($options);
		}

		private function _get_components_cached_file()
		{
			$cached_file = phpgwapi_cache::session_get('property', 'components_import_file');

			if ($_FILES['file']['tmp_name'])
			{
				if ($cached_file)
				{
					phpgwapi_cache::session_clear('property', 'components_import_file');
					unlink($cached_file);
					unset($cached_file);
				}

				$file = $_FILES['file']['tmp_name'];
				$cached_file = "{$file}_temporary_import_file";

				file_put_contents($cached_file, file_get_contents($file));
				phpgwapi_cache::session_set('property', 'components_import_file', $cached_file);
			}

			return $cached_file;
		}

		private function _build_sheets()
		{
			$cached_file = $this->_get_components_cached_file();
			if (!$cached_file)
			{
				$this->receipt['error'][] = array('msg' => lang('Cached file not exists'));
				return;
			}

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

		private function _build_start_line()
		{
			$sheet_id = phpgwapi_cache::session_get('property', 'sheet_id');
			$cached_file = $this->_get_components_cached_file();
			if (!$cached_file)
			{
				$this->receipt['error'][] = array('msg' => lang('Cached file not exists'));
				return;
			}

			$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
			$objPHPExcel->setActiveSheetIndex((int)($sheet_id - 1));
			$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);

			$html_table = '<table class="pure-table pure-table-bordered">';
			$i = 0;
			$cols = array();
			for ($j = 0; $j < $highestColumnIndex; $j++)
			{
				$cols[] = $this->_getexcelcolumnname($j);
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

		private function _get_default_options()
		{
			return array(
				'' => ' ... ',
				'new_column' => lang('New attribute'),
				'building_part' => lang('Building Part'),
				'name_building_part' => lang('Name of the Building Part'),
				'component_id' => lang('Component ID')
			);
		}

		private function _build_columns()
		{
			$cod_profile = phpgw::get_var('cod_profile');

			$cached_file = $this->_get_components_cached_file();
			if (!$cached_file)
			{
				$this->receipt['error'][] = array('msg' => lang('Cached file not exists'));
				return;
			}
			$sheet_id = phpgwapi_cache::session_get('property', 'sheet_id');
			$start_line = phpgwapi_cache::session_get('property', 'start_line');
			$template_id = phpgwapi_cache::session_get('property', 'template_id');

			$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
			$objPHPExcel->setActiveSheetIndex((int)($sheet_id - 1));
			$highestColumm = $objPHPExcel->getActiveSheet()->getHighestDataColumn();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumm);

			$profile = array();

			if ($cod_profile)
			{
				$profiles = $this->config_repository['profiles'];
				$profile = $profiles[$cod_profile]['content'];
			}

			$html_table = '<table class="pure-table pure-table-bordered">';

			$_options = $this->_get_default_options();

			$template = explode("_", $template_id);

			$attributes = $this->custom->find($this->type_app[$this->type], ".{$this->type}.{$template[0]}.{$template[1]}", 0, '', 'ASC', 'attrib_sort', true, true);

			foreach ($attributes as $attribute)
			{
				$_options[$attribute['name']] = $attribute['input_text'];
			}

			$data_types = $this->bocommon->select_datatype();
			$_options_data_type[''] = 'data type';
			foreach ($data_types as $row)
			{
				$_options_data_type[$row['id']] = $row['name'];
			}

			for ($j = 0; $j < $highestColumnIndex; $j++)
			{
				$_column = $this->_getexcelcolumnname($j);
				$_value = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($j, $start_line)->getCalculatedValue();
				$selected = isset($profile['columns']['columns'][$_column]) && $profile['columns']['columns'][$_column] ? $profile['columns']['columns'][$_column] : '';

				$_listbox = $this->_getArrayItem("column_{$_column}", "columns[{$_column}]", $selected, $_options, true, "onchange=\"enabledAtributes('{$_column}')\" class='columns'");
				$_listTypes = $this->_getArrayItem("data_type_{$_column}", "data_types[{$_column}]", $selected, $_options_data_type, true, "disabled class='data_types'");
				$html_table .= "<tr>";
				$html_table .= "<td>[{$_column}] {$_value}</td>";
				$html_table .= "<td>{$_listbox}</td>";
				$html_table .= "<td><input type='text' id='name_{$_column}' name='names[{$_column}]' disabled class='names' placeholder='attribute name'></input></td>";
				$html_table .= "<td>{$_listTypes}</td>";
				$html_table .= "<td><input type='text' id='precision_{$_column}' name='precision[{$_column}]' disabled class='precision' placeholder='length'></input></td>";
				$html_table .= "</tr>";
			}

			$html_table .= '</table>';

			return $html_table;
		}

		private function _prepare_profile()
		{
			$columns = (array)phpgw::get_var('columns');
			$attrib_names = (array)phpgw::get_var('attrib_names');

			$template_id = phpgwapi_cache::session_get('property', 'template_id');
			$attrib_name_componentID = phpgwapi_cache::session_get('property', 'attrib_name_componentID');

			$template = explode("_", $template_id);
			$entity_id = $template[0];
			$cat_id = $template[1];
			$attributes = $this->custom->find($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", 0, '', 'ASC', 'attrib_sort', true, true);

			$_options = $this->_get_default_options();
			foreach ($attributes as $attribute)
			{
				if ($attrib_name_componentID == $attribute['column_name'])
				{
					$attrib_name_componentID_Text = $attribute['input_text'];
				}
				$_options[$attribute['column_name']] = $attribute['input_text'];
			}

			$columns_name = array();
			foreach ($columns as $k => $v)
			{
				if ($v == 'new_column')
				{
					$columns_name[] = $k . ' => ' . $attrib_names[$k];
					$columns[$k] = strtolower($attrib_names[$k]);
				}
				else
				{
					$columns_name[] = $k . ' => ' . $_options[$v];
				}
			}

			$entity_info = $this->bo->read_single($entity_id);
			$category_info = $this->bo->read_single_category($entity_id, $cat_id);
			$template_name = "{$entity_info['name']}::{$category_info['name']}";

			$profile['columns'] = array('columns' => $columns, 'columns_name' => $columns_name);
			$profile['template'] = array('template_id' => $template_id, 'template_name' => $template_name);
			$profile['attrib_name_componentID'] = array('id' => $attrib_name_componentID,
				'text' => $attrib_name_componentID_Text);
			phpgwapi_cache::session_set('property', 'profile', serialize($profile));

			return $profile;
		}

		private function _prepare_values_to_preview()
		{
			$columns = (array)phpgw::get_var('columns');
			$attrib_data_types = phpgw::get_var('attrib_data_types');
			$attrib_names = phpgw::get_var('attrib_names');
			$attrib_precision = phpgw::get_var('attrib_precision');

			$cached_file = $this->_get_components_cached_file();
			if (!$cached_file)
			{
				$this->receipt['error'][] = array('msg' => lang('Cached file not exists'));
				return;
			}

			$sheet_id = phpgwapi_cache::session_get('property', 'sheet_id');
			$start_line = phpgwapi_cache::session_get('property', 'start_line');
			$template_id = phpgwapi_cache::session_get('property', 'template_id');
			$attrib_name_componentID = phpgwapi_cache::session_get('property', 'attrib_name_componentID');

			$objPHPExcel = PHPExcel_IOFactory::load($cached_file);
			$objPHPExcel->setActiveSheetIndex((int)($sheet_id - 1));
			$rows = $objPHPExcel->getActiveSheet()->getHighestDataRow();

			$import_entity_categories = new import_entity_categories($template_id);
			$import_components = new import_components();

			if (count($attrib_names))
			{
				$receipt = $import_entity_categories->prepare_attributes_for_template($columns, $attrib_names, $attrib_data_types, $attrib_precision);
				$this->receipt = $this->_msg_data($receipt);
				if ($this->receipt['error'])
				{
					return;
				}
				else
				{
					$new_attribs_for_template = $receipt['new_attribs_for_template'];
				}
			}

			$rows = $rows ? $rows + 1 : 0;

			$building_part_out_table = array();
			$building_part_in_table = array();
			$import_data = array();

			$list_entity_categories = $import_entity_categories->list_entity_categories();

			for ($i = $start_line; $i < $rows; $i++)
			{
				$_result = array();

				foreach ($columns as $_row_key => $_value_key)
				{
					$_result[$_value_key] = htmlspecialchars($objPHPExcel->getActiveSheet()->getCell("{$_row_key}{$i}")->getCalculatedValue(), ENT_QUOTES, 'UTF-8');
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

							$building_part_in_table[$_result['building_part']] = array('entity_id' => $entity_id,
								'cat_id' => $cat_id);
						}
					}
					else
					{
						if (empty($_result['component_id']))
						{
							$building_part_out_table[$_result['building_part']] = $_result['building_part'] . ' ' . $_result['name_building_part'];
						}
					}

					if (!empty($_result['component_id']))
					{
						$import_data[$_result['building_part']]['cat_id'] = $cat_id;
						$import_data[$_result['building_part']]['entity_id'] = $entity_id;

						$_result = array($attrib_name_componentID => $_result['component_id']) + $_result;
						$_result = array('building part' => $_result['building_part']) + $_result;

						$import_data[$_result['building_part']]['components'][] = $_result;
					}
				}
			}

			if (count($building_part_out_table))
			{
				asort($building_part_out_table);
				$receipt = $import_entity_categories->prepare_entity_categories($building_part_out_table);
				$this->receipt = $this->_msg_data($receipt);
				if ($this->receipt['error'])
				{
					return;
				}
				else
				{
					$new_entity_categories = $receipt['new_entity_categories'];
				}
			}

			$result = array();

			if (!count($import_data))
			{
				$result['error'][] = array('msg' => lang('not exist components to import'));
				return $result;
			}

			$preview_components = $import_components->prepare_preview_components($import_data);

			//$config = createObject('phpgwapi.config', 'component_import');

			if (count($new_attribs_for_template))
			{
				//$this->config->value('new_attribs_for_template', serialize($new_attribs_for_template));
				phpgwapi_cache::session_set('property', 'new_attribs_for_template', serialize($new_attribs_for_template));
				foreach ($new_attribs_for_template as $attrib)
				{
					$values[] = $attrib['column_name'];
				}
				$result['new_attribs_for_template'] = $values;
			}
			else
			{
				phpgwapi_cache::session_set('property', 'new_attribs_for_template', '');
				$result['new_attribs_for_template'][] = lang('Not exist attributes to insert the template');
			}

			if (count($new_entity_categories))
			{
				//$this->config->value('new_entity_categories', serialize($new_entity_categories));
				phpgwapi_cache::session_set('property', 'new_entity_categories', serialize($new_entity_categories));
				$result['new_entity_categories'] = array_values($new_entity_categories);
			}
			else
			{
				phpgwapi_cache::session_set('property', 'new_entity_categories', '');
				$result['new_entity_categories'][] = lang('Not exist new entity categories');
			}

			$profile = $this->_prepare_profile();
			$result['profile'] = $profile;

			phpgwapi_cache::session_set('property', 'building_part_in_table', serialize($building_part_in_table));
			phpgwapi_cache::session_set('property', 'preview_components', serialize($preview_components));
			phpgwapi_cache::session_set('property', 'new_components', serialize($import_data));

			return $result;
		}

		private function _save_values_import()
		{
			$name_profile = phpgw::get_var('name_profile', 'REQUEST');
			$cod_profile = phpgw::get_var('cod_profile', 'REQUEST');
			$profile_option_save = phpgw::get_var('profile_option_save', 'int', 'REQUEST');
			$save_profile = phpgw::get_var('save_profile', 'int', 'REQUEST');

			$template_id = phpgwapi_cache::session_get('property', 'template_id');
			$attrib_name_componentID = phpgwapi_cache::session_get('property', 'attrib_name_componentID');
			$location_code = phpgwapi_cache::session_get('property', 'location_code');

			$import_entity_categories = new import_entity_categories($template_id);
			$import_components = new import_components();

			$receipt = $import_entity_categories->add_attributes_to_template();
			$this->receipt = $this->_msg_data($receipt);
			if ($this->receipt['error'])
			{
				return;
			}

			$receipt = $import_entity_categories->add_attributes_to_categories();
			$this->receipt = $this->_msg_data($receipt);
			if ($this->receipt['error'])
			{
				return;
			}

			$import_data = phpgwapi_cache::session_get('property', 'new_components');
			$import_data = ($import_data) ? unserialize($import_data) : array();

			if (!count($import_data))
			{
				$this->receipt['error'][] = array('msg' => lang("not exist components to import"));
				return;
			}

			$building_part_processed = $import_entity_categories->add_entity_categories();
			if (count($building_part_processed['not_added']))
			{
				foreach ($building_part_processed['not_added'] as $k => $v)
				{
					$this->receipt['message'][] = array('msg' => lang("entity category {$v} not added"));
				}
			}

			if (count($building_part_processed['added']))
			{
				foreach ($building_part_processed['added'] as $k => $v)
				{
					$import_data[$k]['cat_id'] = $v['id'];
					$import_data[$k]['entity_id'] = $v['entity_id'];
				}
				$this->receipt['message'][] = array('msg' => lang("%1 entity category has been added", count($building_part_processed['added'])));
			}

			$receipt = $import_components->add_components($import_data, $location_code, $attrib_name_componentID);
			$this->receipt = $this->_msg_data($receipt);

			if ($save_profile)
			{
				$profiles = $this->config_repository['profiles'];

				if ($profile_option_save == 1)
				{
					$cod_profile = str_replace(' ', '_', mb_strtolower($name_profile, 'UTF-8'));
					$profiles[$cod_profile]['name'] = $name_profile;
				}

				if ($cod_profile)
				{
					$content = phpgwapi_cache::session_get('property', 'profile');
					$content = ($content) ? unserialize($content) : array();
					$profiles[$cod_profile]['content'] = $content;

					$this->config->value('profiles', serialize($profiles));
					$this->config->save_repository();
				}
			}

			return $this->receipt;
		}

		public function import_components()
		{
			$step = phpgw::get_var('step', 'int', 'REQUEST');
			$save = phpgw::get_var('save', 'int', 'REQUEST');

			phpgw::import_class('phpgwapi.phpexcel');

			if ($step == 1)
			{
				$result = $this->_build_sheets();
			}

			if ($step == 2)
			{
				$sheet_id = phpgw::get_var('sheet_id', 'int', 'REQUEST');
				if (!$sheet_id)
				{
					$this->receipt['error'][] = array('msg' => lang('Select Sheet'));
					return $this->receipt;
				}
				phpgwapi_cache::session_set('property', 'sheet_id', $sheet_id);

				$result = $this->_build_start_line();
			}

			if ($step == 3)
			{
				$start_line = phpgw::get_var('start_line', 'int', 'REQUEST');
				$template_id = phpgw::get_var('template_id');
				if (!$start_line)
				{
					$this->receipt['error'][] = array('msg' => lang('Select start line'));
					return $this->receipt;
				}
				if (!$template_id)
				{
					$this->receipt['error'][] = array('msg' => lang('Select template'));
					return $this->receipt;
				}
				phpgwapi_cache::session_set('property', 'start_line', $start_line);
				phpgwapi_cache::session_set('property', 'template_id', $template_id);

				$result = $this->_build_columns();
			}

			if ($step == 4)
			{
				$attrib_name_componentID = phpgw::get_var('attribute_name_component_id');
				if (!$attrib_name_componentID)
				{
					$this->receipt['error'][] = array('msg' => lang('Choose attribute name for Component ID'));
					return $this->receipt;
				}
				phpgwapi_cache::session_set('property', 'attrib_name_componentID', $attrib_name_componentID);

				$result = $this->_prepare_values_to_preview();
			}

			if ($step == 5 && $save)
			{
				$location_code = phpgw::get_var('location_code');
				$location_item_id = phpgw::get_var('location_item_id');
				if (!$location_code)
				{
					$this->receipt['error'][] = array('msg' => lang('Choose Location'));
					return $this->receipt;
				}
				phpgwapi_cache::session_set('property', 'location_code', $location_code);
				phpgwapi_cache::session_set('property', 'location_item_id', $location_item_id);

				$result = $this->_save_values_import();
			}

			if ($this->receipt['error'])
			{
				return $this->receipt;
			}

			return $result;
		}

		/**
		 * Prepare UI
		 * @return void
		 */
		public function index()
		{
			$tabs = array();
			$tabs['locations'] = array('label' => lang('Locations'), 'link' => '#locations');
			$tabs['components'] = array('label' => lang('Components'), 'link' => '#components',
				'disable' => 1);
			$tabs['files'] = array('label' => lang('Files'), 'link' => '#files', 'disable' => 0);
			$tabs['relations'] = array('label' => lang('Relations'), 'link' => '#relations',
				'disable' => 1);

			$active_tab = 'locations';

			$type_filter = execMethod('property.soadmin_location.read', array());
			$category_filter = $this->get_categories_for_type();

			$district_filter = $this->bocommon->select_district_list('filter');
			array_unshift($district_filter, array('id' => '', 'name' => lang('no district')));

			$part_of_town_filter = $this->get_part_of_town();

			$related_def = array
				(
				array('key' => 'location_code', 'label' => lang('location'), 'sortable' => true,
					'resizeable' => true),
				array('key' => 'loc1_name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true)
			);


			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uiimport_components.get_locations_for_type',
						'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $related_def,
				'tabletools' => array(),
				'config' => array(
					array('singleSelect' => true)
				)
			);

			$profile_list = array();
			$profiles = $this->config_repository['profiles'];
			foreach ($profiles as $k => $v)
			{
				$profile_list[] = array('id' => $k, 'name' => $v['name']);
			}
			array_unshift($profile_list, array('id' => '', 'name' => lang('choose profile')));

			//$profile = $this->config_repository['profile'];
			$entity_list = $this->bo->read(array('allrows' => true));
			$category_list = array();
			foreach ($entity_list as $entry)
			{
				$cat_list = $this->bo->read_category(array('entity_id' => $entry['id'], 'allrows' => true));

				foreach ($cat_list as $category)
				{
					//$selected = ($profile['template']['template_id'] == "{$entry['id']}_{$category['id']}") ? 1 :0;
					$category_list[] = array
						(
						'id' => "{$entry['id']}_{$category['id']}",
						'name' => "{$entry['name']}::{$category['name']}"
					);
				}
			}

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uiimport_components.handle_import_files'));

			$access_error_upload_dir = '';
			$import_component_files = new import_component_files();
			$receipt = $import_component_files->check_upload_dir();
			if (($receipt['error']))
			{
				$access_error_upload_dir = $receipt['error'];
			}
			else
			{
				phpgwapi_cache::session_set('property', 'path_upload_dir', $import_component_files->get_path_upload_dir());
			}
			phpgwapi_jquery::init_multi_upload_file();

			$data = array
				(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'type_filter' => array('options' => $type_filter),
				'category_filter' => array('options' => $category_filter),
				'district_filter' => array('options' => $district_filter),
				'part_of_town_filter' => array('options' => $part_of_town_filter),
				'document_category'	=> array('options' => $this->_get_document_categories() ),
				'template_list' => array('options' => $category_list),
				'profile_list' => array('options' => $profile_list),
				'multi_upload_action' => $multi_upload_action,
				'access_error_upload_dir' => $access_error_upload_dir,
				'image_loader' => $GLOBALS['phpgw']->common->image('property', 'ajax-loader', '.gif', false)
			);

			self::add_javascript('property', 'portico', 'import_components.js');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('property') . '::' . lang('import components');

			self::render_template_xsl(array('import_components', 'multi_upload_file', 'datatable_inline'), $data);
		}

		public function get_attributes_from_template()
		{
			$selected_attribute = phpgw::get_var('selected_attribute');
			$category_template = phpgw::get_var('category_template');

			$template_info = explode('_', $category_template);
			$template_entity_id = $template_info[0];
			$template_cat_id = $template_info[1];

			$attrib_list = $this->bo->read_attrib(array('entity_id' => $template_entity_id,
				'cat_id' => $template_cat_id, 'allrows' => true));
			$list = array();
			foreach ($attrib_list as $attrib)
			{
				$selected = ($selected_attribute == $attrib['column_name']) ? 1 : 0;
				$list[] = array('id' => $attrib['column_name'], 'name' => $attrib['input_text'],
					'selected' => $selected);
			}

			array_unshift($list, array('id' => '', 'name' => lang('choose attribute')));

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
			foreach ($locations as $item)
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

		public function get_profile()
		{
			$cod_profile = phpgw::get_var('cod_profile', 'REQUEST');

			$profiles = $this->config_repository['profiles'];
			$content = $profiles[$cod_profile]['content'];

			$template_id = ($content['template']['template_id']);
			$attrib_name_componentID = $content['attrib_name_componentID']['id'];

			return array('template_id' => $template_id, 'attrib_name_componentID' => $attrib_name_componentID);
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

		private function _get_document_categories( $selected = 0 )
		{
			$cats = CreateObject('phpgwapi.categories', -1, 'property', '.document');
			$cats->supress_info = true;
			$categories = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => $selected,
				'globals' => true, 'use_acl' => $this->_category_acl));
			$default_value = array('cat_id' => '', 'name' => lang('no category'));
			array_unshift($categories['cat_list'], $default_value);

			foreach ($categories['cat_list'] as & $_category)
			{
				$_category['id'] = $_category['cat_id'];
			}

			return $categories['cat_list'];
		}

	}