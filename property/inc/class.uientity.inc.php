<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA	02110-1301	USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @subpackage entity
	 * @version $Id$
	 */
	/**
	 * Description
	 * @package property
	 */
	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('phpgwapi.jquery');

	class property_uientity extends phpgwapi_uicommon_jquery
	{

		private $receipt = array();
		var $grants;
		var $cat_id;
		var $start;
		var $query;
		var $sort;
		var $order;
		var $filter;
		var $part_of_town_id;
		var $sub;
		var $currentapp;
		var $check_lst_time_span = array();
		var $controller_helper;
		var $public_functions = array
			(
			'columns' => true,
			'query' => true,
			'download' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'add' => true,
			'delete' => true,
			'view_file' => true,
			'attrib_history' => true,
			'attrib_help' => true,
			'print_pdf' => true,
			'index' => true,
			//'addfiles' => true,
			'get_documents' => true,
			'get_files' => true,
			'get_target' => true,
			'get_related' => true,
			'get_inventory' => true,
			'add_inventory' => true,
			'edit_inventory' => true,
			'inventory_calendar' => true,
			'get_controls_at_component' => true,
			'get_assigned_history' => true,
			'get_cases' => true,
			'get_checklists'=>true,
			'get_cases_for_checklist' => true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true
		);

		function __construct()
		{
			parent::__construct();

			//	$GLOBALS['phpgw_info']['flags']['nonavbar'] = true; // menus added where needed via bocommon::get_menu
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];

			$this->bo = CreateObject('property.boentity', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->soadmin_entity = & $this->bo->soadmin_entity;

			$this->entity_id = $this->bo->entity_id;
			$this->cat_id = $this->bo->cat_id;

			$this->start = $this->bo->start;
			$this->query = $this->bo->query;
			$this->sort = $this->bo->sort;
			$this->order = $this->bo->order;
			$this->filter = $this->bo->filter;
			$this->part_of_town_id = $this->bo->part_of_town_id;
			$this->district_id = $this->bo->district_id;
			$this->status = $this->bo->status;
			$this->location_code = $this->bo->location_code;
			$this->p_num = $this->bo->p_num;
			$this->category_dir = $this->bo->category_dir;
			$GLOBALS['phpgw']->session->appsession('entity_id', 'property', $this->entity_id);
			$this->start_date = $this->bo->start_date;
			$this->end_date = $this->bo->end_date;
			$this->allrows = $this->bo->allrows;
			$this->type = $this->bo->type;
			$this->type_app = $this->bo->type_app;
			$this->acl = & $GLOBALS['phpgw']->acl;

			$this->acl_location = ".{$this->type}.$this->entity_id";
			if ($this->cat_id)
			{
				$this->acl_location .= ".{$this->cat_id}";
			}

			$acl_check_location = $this->acl_location;

			$config = CreateObject('phpgwapi.config', 'property')->read();

			if(!empty($config['bypass_acl_at_entity']) && is_array($config['bypass_acl_at_entity']) && in_array($this->entity_id, $config['bypass_acl_at_entity']))
			{
				$acl_check_location = ".{$this->type}.$this->entity_id"; //parent
			}

			$this->acl_read = $this->acl->check($acl_check_location, PHPGW_ACL_READ, $this->type_app[$this->type]);
			$this->acl_add = $this->acl->check($acl_check_location, PHPGW_ACL_ADD, $this->type_app[$this->type]);
			$this->acl_edit = $this->acl->check($acl_check_location, PHPGW_ACL_EDIT, $this->type_app[$this->type]);
			$this->acl_delete = $this->acl->check($acl_check_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]);

			$this->controller_helper = CreateObject('property.controller_helper', array(
				'acl_location' => $acl_check_location,
				'acl_read' => $this->acl_read,
				'acl_add' => $this->acl_add,
				'acl_edit' => $this->acl_edit,
				'acl_delete' => $this->acl_delete,
			));

			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "{$this->type_app[$this->type]}::entity_{$this->entity_id}";
			if ($this->cat_id > 0)
			{
				$GLOBALS['phpgw_info']['flags']['menu_selection'] .= "::{$this->cat_id}";
			}
			if (phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
		}
		/*
		 * Overrides with incoming data from POST
		 */

		private function _populate( $data = array() )
		{
			$values = phpgw::get_var('values');
			$values_attribute = phpgw::get_var('values_attribute');
			$bypass = phpgw::get_var('bypass', 'bool');

			$values['vendor_id'] = phpgw::get_var('vendor_id', 'int', 'POST');
			$values['vendor_name'] = phpgw::get_var('vendor_name', 'string', 'POST');
			$values['date'] = phpgw::get_var('date');

			if (!$bypass)
			{
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');
				$insert_record_entity = $GLOBALS['phpgw']->session->appsession('insert_record_values' . $this->acl_location, $this->type_app[$this->type]);

				if (is_array($insert_record_entity))
				{
					for ($j = 0; $j < count($insert_record_entity); $j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);
			}

			if (isset($values['origin']) && $values['origin'])
			{
				$origin = $values['origin'];
				$origin_id = $values['origin_id'];
			}
			else
			{
				$origin = phpgw::get_var('origin');
				$origin_id = phpgw::get_var('origin_id', 'int');
			}

			$interlink = CreateObject('property.interlink');

			if (isset($origin) && $origin)
			{
				$values['origin_data'][0]['location'] = $origin;
				$values['origin_data'][0]['descr'] = $interlink->get_location_name($origin);
				$values['origin_data'][0]['data'][] = array(
					'id' => $origin_id,
					'link' => $interlink->get_relation_link(array('location' => $origin), $origin_id),
				);
			}

			if (isset($values['save']) && $values['save'])
			{
				if (!$this->cat_id)
				{
					$this->receipt['error'][] = array('msg' => lang('Please select entity type !'));

					return $values;
				}
				$category = $this->soadmin_entity->read_single_category($this->entity_id, $this->cat_id);

				if ($category['org_unit'])
				{
					$values['extra']['org_unit_id'] = phpgw::get_var('org_unit_id', 'int');
					$values['org_unit_id'] = $values['extra']['org_unit_id'];
					$values['org_unit_name'] = phpgw::get_var('org_unit_name', 'string');
				}
				if ($GLOBALS['phpgw']->session->is_repost())
				{
					$this->receipt['error'][] = array('msg' => lang('Hmm... looks like a repost!'));
				}

				if ((!$values['location'] && !$values['p']) && isset($category['location_level']) && $category['location_level'])
				{
					$this->receipt['error'][] = array('msg' => lang('Please select a location !'));
				}

				if (isset($values_attribute) && is_array($values_attribute))
				{
					foreach ($values_attribute as $attribute)
					{
						if ($attribute['nullable'] != 1 && (!$attribute['value'] && !$values['extra'][$attribute['name']]))
						{
							$this->receipt['error'][] = array('msg' => lang('Please enter value for attribute %1', $attribute['input_text']));
						}

						if (isset($attribute['value']) && $attribute['value'] && $attribute['datatype'] == 'I' && !ctype_digit($attribute['value']))
						{
							$this->receipt['error'][] = array('msg' => lang('Please enter integer for attribute %1', $attribute['input_text']));
						}
					}
				}

				if ($this->receipt['error'])
				{
					if ($values['location'])
					{
						$bolocation = CreateObject('property.bolocation');
						$location_code = implode("-", $values['location']);
						$values['extra']['view'] = true;
						$values['location_data'] = $bolocation->read_single($location_code, $values['extra']);
					}
					if ($values['extra']['p_num'])
					{
						$values['p'][$values['extra']['p_entity_id']]['p_num'] = $values['extra']['p_num'];
						$values['p'][$values['extra']['p_entity_id']]['p_entity_id'] = $values['extra']['p_entity_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_id'] = $values['extra']['p_cat_id'];
						$values['p'][$values['extra']['p_entity_id']]['p_cat_name'] = phpgw::get_var('entity_cat_name_' . $values['extra']['p_entity_id']);
					}
				}
			}

			$values['attributes'] = $values_attribute;

			foreach ($data as $key => $original_value)
			{
				if ((!isset($values[$key]) || !$values[$key]) && $data[$key])
				{
					$values[$key] = $original_value;
				}
			}
			return $values;
		}

		private function _handle_files( $values )
		{
			$id = (int)$values['id'];
			if (empty($id))
			{
				throw new Exception('uientity::_handle_files() - missing id');
			}

			$loc1 = isset($values['location']['loc1']) && $values['location']['loc1'] ? $values['location']['loc1'] : 'dummy';
			if ($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}

			$bofiles = CreateObject('property.bofiles');
			if (isset($values['file_action']) && is_array($values['file_action']))
			{
				$bofiles->delete_file("/{$this->category_dir}/{$loc1}/{$id}/" ,$values);
			}

			if (isset($values['file_jasperaction']) && is_array($values['file_jasperaction']))
			{
				$values['file_action'] = $values['file_jasperaction'];
				$bofiles->delete_file("{$this->category_dir}/{$loc1}/{$id}/" ,$values);
			}

			$files = array();
			if (isset($_FILES['file']['name']) && $_FILES['file']['name'])
			{
				$file_name = str_replace(' ', '_', $_FILES['file']['name']);
				$to_file = "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array
						(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
				}
				else
				{
					$files[] = array
						(
						'from_file' => $_FILES['file']['tmp_name'],
						'to_file' => $to_file
					);
				}

				unset($to_file);
				unset($file_name);
			}

			if (isset($_FILES['jasperfile']['name']) && $_FILES['jasperfile']['name'])
			{
				$file_name = 'jasper::' . str_replace(' ', '_', $_FILES['jasperfile']['name']);
				$to_file = "{$bofiles->fakebase}/{$this->category_dir}/{$loc1}/{$id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array
						(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					$this->receipt['error'][] = array('msg' => lang('This file already exists !'));
				}
				else
				{
					$files[] = array
						(
						'from_file' => $_FILES['jasperfile']['tmp_name'],
						'to_file' => $to_file
					);
				}

				unset($to_file);
				unset($file_name);
			}


			foreach ($files as $file)
			{
				$bofiles->create_document_dir("{$this->category_dir}/{$loc1}/{$id}");
				$bofiles->vfs->override_acl = 1;

				if (!$bofiles->vfs->cp(array(
						'from' => $file['from_file'],
						'to' => $file['to_file'],
						'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
				{
					$this->receipt['error'][] = array('msg' => lang('Failed to upload file !'));
				}
				$bofiles->vfs->override_acl = 0;
			}

			unset($loc1);
			unset($files);
			unset($file);
		}

		public function handle_multi_upload_file()
		{
			$id = phpgw::get_var('id');
			$entity_id = phpgw::get_var('entity_id');
			$cat_id = phpgw::get_var('cat_id');
			$type = phpgw::get_var('type');

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php',
					array('menuaction' => 'property.uientity.handle_multi_upload_file',
								'id' => $id,
								'entity_id' => $entity_id,
								'cat_id' => $cat_id,
								'type' => $type));

			phpgw::import_class('property.multiuploader');

			$values = $this->bo->read_single(array('entity_id' => $entity_id, 'cat_id' => $cat_id,
				'id' => $id));

			$loc1 = isset($values['location_data']['loc1']) && $values['location_data']['loc1'] ? $values['location_data']['loc1'] : 'dummy';
			if ($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}

			$options['base_dir'] = "{$this->category_dir}/{$loc1}/{$id}";
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/property/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode($multi_upload_action);
			$upload_handler = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();

			$id = phpgw::get_var('id');
			$entity_id = phpgw::get_var('_entity_id');
			$cat_id = phpgw::get_var('_cat_id');
			$type = phpgw::get_var('_type');

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php',
					array('menuaction' => 'property.uientity.handle_multi_upload_file',
								'id' => $id,
								'entity_id' => $entity_id,
								'cat_id' => $cat_id,
								'type' => $type));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		private function _get_filters( $selected = 0 )
		{
			$values_combo_box = array();
			$combos = array();

			if ($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id, $this->cat_id);

				//this validation comes to previous versions
				if (isset($category['location_level']) && $category['location_level'] > 0)
				{
					$values_combo_box[0] = $this->bocommon->select_district_list('filter', $this->district_id);
					if (count($values_combo_box[0]))
					{
						$default_value = array('id' => '', 'name' => lang('no district'));
						array_unshift($values_combo_box[0], $default_value);

						$link = self::link(array(
								'menuaction' => 'property.uilocation.get_part_of_town',
								'district_id' => $this->district_id,
								'part_of_town_id' => $this->part_of_town_id,
								'phpgw_return_as' => 'json'
						));

						$code = '
							var link = "' . $link . '";
							var data = {"district_id": $(this).val()};
							execute_ajax(link,
								function(result){
									var $el = $("#part_of_town_id");
									$el.empty();
									$.each(result, function(key, value) {
									  $el.append($("<option></option>").attr("value", value.id).text(value.name));
									});
								}, data, "GET", "json"
							);
							';

						$combos[] = array('type' => 'filter',
							'name' => 'district_id',
							'extra' => $code,
							'text' => lang('district'),
							'list' => $values_combo_box[0]
						);

						$values_combo_box[1] = $this->bocommon->select_part_of_town('filter', $this->part_of_town_id, $this->district_id);
						$default_value = array('id' => '', 'name' => lang('no part of town'));
						array_unshift($values_combo_box[1], $default_value);
						$combos[] = array('type' => 'filter',
							'name' => 'part_of_town_id',
							'extra' => '',
							'text' => lang('part of town'),
							'list' => $values_combo_box[1]
						);

					}
					else
					{
						unset($values_combo_box[0]);
					}
				}
			}

			//// ---- USER filter----------------------
			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bocommon->get_user_list_right2('filter', 4, $this->filter, $this->acl_location, array(
				'all'), $default = 'all');
			if (count($values_combo_box[$count]))
			{
				$default_value = array('id' => '', 'name' => lang('no user'));
				array_unshift($values_combo_box[$count], $default_value);
				$combos[] = array('type' => 'filter',
					'name' => 'filter',
					'extra' => '',
					'text' => lang('user'),
					'list' => $values_combo_box[$count]
				);
			}
			else
			{
				unset($values_combo_box[$count]);
			}


			$count = count($values_combo_box);
			$values_combo_box[$count] = $this->bo->get_criteria_list($this->criteria_id);
			$default_value = array('id' => '', 'name' => lang('no criteria'));
			array_unshift($values_combo_box[$count], $default_value);
			$combos[] = array('type' => 'filter',
				'name' => 'criteria_id',
				'extra' => '',
				'text' => lang('search criteria'),
				'list' => $values_combo_box[$count]
			);

			$custom = createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->find($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}", 0, '', '', '', true, true);

			if ($attrib_data)
			{
				$count = count($values_combo_box);
				foreach ($attrib_data as $attrib)
				{
					if (($attrib['datatype'] == 'LB' || $attrib['datatype'] == 'CH' || $attrib['datatype'] == 'R') && $attrib['choice'])
					{
						$values_combo_box[$count][] = array
							(
							'id' => '',
							'name' => lang('select') . " '{$attrib['input_text']}'"
						);

						foreach ($attrib['choice'] as $choice)
						{
							$values_combo_box[$count][] = array
								(
								'id' => $choice['id'],
								'name' => htmlspecialchars($choice['value'], ENT_QUOTES, 'UTF-8'),
							);
						}

						$combos[] = array('type' => 'filter',
							'name' => $attrib['column_name'],
							'extra' => '',
							'text' => lang($attrib['column_name']),
							'list' => $values_combo_box[$count]
						);

						$count++;
					}
				}
			}

			return $combos;
		}

		public function get_documents()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$doc_type = phpgw::get_var('doc_type', 'int');
			$entity_id = phpgw::get_var('entity_id', 'int');
			$cat_id = phpgw::get_var('cat_id', 'int');
			$item_id = phpgw::get_var('item_id');
			$location_id = phpgw::get_var('location_id', 'int');
			$export = phpgw::get_var('export', 'bool');
			$values = array();

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'doc_type' => $doc_type,
				'entity_id' => $entity_id,
				'cat_id' => $cat_id,
				'p_num' => $item_id,
				'location_item_id' => $item_id,
			);

			$document = CreateObject('property.sodocument');
			$documents = $document->read_at_location($params);
			$total_records = $document->total_records;

			foreach ($documents as $item)
			{
				$document_name = '<a href="'.self::link(array('menuaction'=>'property.uidocument.view_file', 'id'=>$item['document_id'])).'" target="_blank">'.$item['document_name'].'</a>';
				$values[] =  array('document_name' => $document_name, 'title'=> $item['title']);
			}

			//$location_id = $GLOBALS['phpgw']->locations->get_id('property', '.location.' . count(explode('-', $location_code)));
			$generic_document = CreateObject('property.sogeneric_document');
			if (empty($location_id))
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}");
			}
			$params['location_id'] = $location_id;
			$params['order'] = 'name';
			$params['cat_id'] = $doc_type;
			$documents2 = $generic_document->read($params);
			$total_records += $generic_document->total_records;
			foreach ($documents2 as $item)
			{
				$document_name = '<a href="'.self::link(array('menuaction'=>'property.uigeneric_document.view_file', 'file_id'=>$item['id'])).'" target="_blank">'.$item['name'].'</a>';
				$values[] =  array('document_name' => $document_name, 'title'=> $item['title']);
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function query()
		{
			$start_date = urldecode($this->start_date);
			$end_date = urldecode($this->end_date);

			if ($start_date && empty($end_date))
			{
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
				$end_date = $GLOBALS['phpgw']->common->show_date(mktime(0, 0, 0, date("m"), date("d"), date("Y")), $dateformat);
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');
			$export = phpgw::get_var('export', 'bool');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1 || $export,
				'start_date' => $start_date,
				'end_date' => $end_date
			);

			$values = $this->bo->read($params);
			if ($export)
			{
				return $values;
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location);
			$custom_config = CreateObject('admin.soconfig', $location_id);
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$remote_image_in_table = false;
			foreach ($_config as $_config_section => $_config_section_data)
			{
				if ($_config_section_data['image_in_table'])
				{
					$remote_image_in_table = true;
					break;
				}
			}

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$img_types = array
				(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$link_data = array
				(
				'menuaction' => 'property.uientity.view',
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type
			);

			foreach ($values as &$entity_entry)
			{
				$_loc1 = isset($entity_entry['loc1']) && $entity_entry['loc1'] ? $entity_entry['loc1'] : 'dummy';

				if ($remote_image_in_table)
				{
					$entity_entry['file_name'] = $entity_entry[$_config_section_data['img_key_local']];
					$entity_entry['img_id'] = $entity_entry[$_config_section_data['img_key_local']];
					$entity_entry['img_url'] = $_config_section_data['url'] . '&' . $_config_section_data['img_key_remote'] . '=' . $entity_entry['img_id'];
					$entity_entry['thumbnail_flag'] = $_config_section_data['thumbnail_flag'];
				}
				else
				{
					$_files = $vfs->ls(array(
						'string' => "/property/{$this->category_dir}/{$_loc1}/{$entity_entry['id']}",
						'relatives' => array(RELATIVE_NONE)));

					$mime_in_array = in_array($_files[0]['mime_type'], $img_types);
					if (!empty($_files[0]) && $mime_in_array)
					{
						$entity_entry['file_name'] = $_files[0]['name'];
						$entity_entry['img_id'] = $_files[0]['file_id'];
						$entity_entry['directory'] = $_files[0]['directory'];
						$entity_entry['img_url'] = self::link(array(
								'menuaction' => 'property.uigallery.view_file',
								'file' => $entity_entry['directory'] . '/' . $entity_entry['file_name']
						));
						$entity_entry['thumbnail_flag'] = 'thumb=1';
					}
				}

				$link_data['id'] = $entity_entry['id'];
				$entity_entry['link'] = self::link($link_data);
			}

			$result_data = array('results' => $values);

			$result_data['total_records'] = $this->bo->total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Saves an entry to the database for new/edit - redirects to view
		 *
		 * @param int  $id  entity id - no id means 'new'
		 *
		 * @return void
		 */
		public function save()
		{
			if (!$_POST)
			{
				return $this->edit();
			}

			$id = phpgw::get_var('id', 'int');

			if ($id)
			{
				$action = 'edit';
			}
			else
			{
				$action = 'add';
			}

			/*
			 * Overrides with incoming data from POST
			 */
			if ($id)
			{
				$data = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
					'id' => $id));
			}

			$data = $this->_populate($data);
			$values = $data;
			$attributes = $data['attributes'];
			unset($values['attributes']);

			if ($this->receipt['error'])
			{
				$this->edit($values);
			}
			else
			{
				try
				{
					$receipt = $this->bo->save($values, $attributes, $action, $this->entity_id, $this->cat_id);
					$values['id'] = $receipt['id'];
					$this->receipt = $receipt;
				}
				catch (Exception $e)
				{
					if ($e)
					{
						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit($values);
						return;
					}
				}

				$this->_handle_files($values);

				//phpgwapi_cache::message_set($receipt, 'message');
				if ($values['apply'])
				{
					if ($id || (isset($receipt['id']) && $receipt['id']))
					{
						$_id = isset($receipt['id']) && $receipt['id'] ? $receipt['id'] : $id;
						self::message_set($this->receipt);
						self::redirect(array('menuaction' => 'property.uientity.edit', 'id' => $_id,
							'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'type' => $this->type));
					}

					$this->edit($values);
					return;
				}
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uientity.index',
					'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'type' => $this->type));
			}
		}

		function save_sessiondata()
		{
			$data = array
				(
				'start' => $this->start,
				'query' => $this->query,
				'sort' => $this->sort,
				'order' => $this->order,
				'filter' => $this->filter,
				'cat_id' => $this->cat_id,
				'part_of_town_id' => $this->part_of_town_id,
				'district_id' => $this->district_id,
				'entity_id' => $this->entity_id,
				'status' => $this->status,
				'start_date' => $this->start_date,
				'end_date' => $this->end_date,
				'criteria_id' => $this->criteria_id
			);
			$this->bo->save_sessiondata($data);
		}

		function download()
		{
			$GLOBALS['phpgw_info']['flags'][noheader] = true;
			$GLOBALS['phpgw_info']['flags'][nofooter] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			//$start_date 	= urldecode($this->start_date);
			//$end_date 	= urldecode($this->end_date);
			//$list = $this->bo->read(array('entity_id'=>$this->entity_id,'cat_id'=>$this->cat_id,'allrows'=>true,'start_date'=>$start_date,'end_date'=>$end_date, 'type' => $this->type));
			$list = $this->query();
			$uicols = $this->bo->uicols;

			$this->bocommon->download($list, $uicols['name'], $uicols['descr'], $uicols['input_type']);
		}

		/*
		function addfiles()
		{
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$id = phpgw::get_var('id', 'int');
			$jasperfile = phpgw::get_var('jasperfile', 'bool');

			$fileuploader = CreateObject('property.fileuploader');


			if (!$this->acl_add && !$this->acl_edit)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			if (!$id)
			{
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$test = false;//true;
			if ($test)
			{
				if (!empty($_FILES))
				{
					$tempFile = $_FILES['Filedata']['tmp_name'];
					$targetPath = "{$GLOBALS['phpgw_info']['server']['temp_dir']}/";
					$targetFile = str_replace('//', '/', $targetPath) . $_FILES['Filedata']['name'];
					move_uploaded_file($tempFile, $targetFile);
					echo str_replace($GLOBALS['phpgw_info']['server']['temp_dir'], '', $targetFile);
				}
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
				'id' => $id));

			$loc1 = isset($values['location_data']['loc1']) && $values['location_data']['loc1'] ? $values['location_data']['loc1'] : 'dummy';
			if ($this->type_app[$this->type] == 'catch')
			{
				$loc1 = 'dummy';
			}

			$fileuploader->upload("{$this->category_dir}/{$loc1}/{$id}");
		}*/

		/**
		 * Function to get related via Ajax-call
		 *
		 */
		function get_related()
		{
			$id = phpgw::get_var('id', 'REQUEST', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$allrows = phpgw::get_var('length', 'int') == -1;

			$related = $this->bo->read_entity_to_link(array('entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id, 'id' => $id));

			$values = array();
			if (isset($related['related']))
			{
				foreach ($related as $related_key => $related_data)
				{
					foreach ($related_data as $entry)
					{
						$values[] = array
							(
							'url' => "<a href=\"{$entry['entity_link']}\" > {$entry['name']}</a>",
						);
					}
				}
			}

			$start = phpgw::get_var('startIndex', 'REQUEST', 'int', 0);
			$total_records = count($values);

			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 0);

			if ($allrows)
			{
				$out = $values;
			}
			else
			{
				if ($total_records > $num_rows)
				{
					$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
					$values_part = array_chunk($values, $num_rows);
					$out = $values_part[$page];
				}
				else
				{
					$out = $values;
				}
			}

			$result_data = array('results' => $out);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Function to get related via Ajax-call
		 *
		 */
		function get_target()
		{
			$id = phpgw::get_var('id', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$allrows = phpgw::get_var('length', 'int') == -1;

			$interlink = CreateObject('property.interlink');
			$target = $interlink->get_relation('property', $this->acl_location, $id, 'target');

			$values = array();
			if ($target)
			{
				foreach ($target as $_target_section)
				{
					foreach ($_target_section['data'] as $_target_entry)
					{
						$values[] = array
							(
							'url' => "<a href=\"{$_target_entry['link']}\" > {$_target_entry['id']}</a>",
							'type' => $_target_section['descr'],
							'title' => $_target_entry['title'],
							'status' => $_target_entry['statustext'],
							'user' => $GLOBALS['phpgw']->accounts->get($_target_entry['account_id'])->__toString(),
							'entry_date' => $GLOBALS['phpgw']->common->show_date($_target_entry['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
						);
					}
				}
			}

			$workorders = CreateObject('property.soworkorder')->get_entity_relation($this->entity_id,$this->cat_id, $id);
			$lang_workorder = lang('workorder');

			foreach ($workorders as $workorder)
			{
				$_link = $GLOBALS['phpgw']->link('/index.php', array
					(
						'menuaction' => "property.uiworkorder.view",
						'id' => $workorder['id']
					)
				);
				$values[] = array
				(
					'url' => "<a href=\"{$_link}\" > {$workorder['id']}</a>",
					'type' => $lang_workorder,
					'title' => $workorder['title'],
					'status' => $workorder['statustext'],
					'user' => $GLOBALS['phpgw']->accounts->get($workorder['user_id'])->__toString(),
					'entry_date' => $GLOBALS['phpgw']->common->show_date($workorder['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				);

			}

//			$controller_cases = array();
//			if (isset($GLOBALS['phpgw_info']['user']['apps']['controller']))
//			{
//
//				$lang_controller = $GLOBALS['phpgw']->translation->translate('controller', array(), false, 'controller');
//				$location_id = $GLOBALS['phpgw']->locations->get_id('property', $this->acl_location);
//				$socase = CreateObject('controller.socase');
//				$controller_cases = $socase->get_cases_by_component($location_id, $id);
//
//				$_statustext = array();
//				$_statustext[0] = lang('open');
//				$_statustext[1] = lang('closed');
//				$_statustext[2] = lang('pending');
//			}
//
//			foreach ($controller_cases as $case)
//			{
//				switch ($case['status'])
//				{
//					case 0:
//					case 2:
//						$_method = 'view_open_cases';
//						break;
//					case 1:
//						$_method = 'view_closed_cases';
//						break;
//					default:
//						$_method = 'view_open_cases';
//				}
//
//				$_link = $GLOBALS['phpgw']->link('/index.php', array
//					(
//					'menuaction' => "controller.uicase.{$_method}",
//					'check_list_id' => $case['check_list_id']
//					)
//				);
//
//				$values[] = array
//					(
//					'url' => "<a href=\"{$_link}\" > {$case['check_list_id']}</a>",
//					'type' => $lang_controller,
//					'title' => $case['descr'],
//					'status' => $_statustext[$case['status']],
//					'user' => $GLOBALS['phpgw']->accounts->get($case['user_id'])->__toString(),
//					'entry_date' => $GLOBALS['phpgw']->common->show_date($case['modified_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//				);
//				unset($_link);
//			}

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$total_records = count($values);

			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 0);

			if ($allrows)
			{
				$out = $values;
			}
			else
			{
				if ($total_records > $num_rows)
				{
					$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
					$values_part = array_chunk($values, $num_rows);
					$out = $values_part[$page];
				}
				else
				{
					$out = $values;
				}
			}

			$result_data = array('results' => $out);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function get_files()
		{
			$id = phpgw::get_var('id', 'REQUEST', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$allrows = phpgw::get_var('length', 'int') == -1;

			$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
				'type' => $this->type,
				'id' => $id));

			$link_file_data = array
				(
				'menuaction' => 'property.uientity.view_file',
				'loc1' => $values['location_data']['loc1'],
				'id' => $id,
				'cat_id' => $this->cat_id,
				'entity_id' => $this->entity_id,
				'type' => $this->type
			);

			if (isset($values['files']) && is_array($values['files']))
			{
				$j = count($values['files']);
				for ($i = 0; $i < $j; $i++)
				{
					$values['files'][$i]['file_name'] = urlencode($values['files'][$i]['name']);
				}
			}

			$content_files = array();
			foreach ($values['files'] as $_entry)
			{
				$content_files[] = array
					(
					'file_name' => '<a href="' . $GLOBALS['phpgw']->link('/index.php', $link_file_data) . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">'
				);
			}

			$start = phpgw::get_var('startIndex', 'REQUEST', 'int', 0);
			$total_records = count($content_files);

			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 0);

			if ($allrows)
			{
				$out = $content_files;
			}
			else
			{
				if ($total_records > $num_rows)
				{
					$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
					$values_part = array_chunk($content_files, $num_rows);
					$out = $values_part[$page];
				}
				else
				{
					$out = $content_files;
				}
			}

			$result_data = array('results' => $out);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		function columns()
		{
			//cramirez: necesary for windows.open . Avoid error JS
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw']->xslttpl->add_file(array('columns'));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$values = phpgw::get_var('values');
			$receipt = array();

			if (isset($values['save']) && $values['save'] && $this->cat_id)
			{
				$GLOBALS['phpgw']->preferences->account_id = $this->account;
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add($this->type_app[$this->type], "entity_columns_" . $this->entity_id . '_' . $this->cat_id, $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();

				$receipt['message'][] = array('msg' => lang('columns is updated'));
			}

			if (!$this->cat_id)
			{
				$receipt['error'][] = array('msg' => lang('Choose a category'));
			}
			$function_msg = lang('Select Column');

			$link_data = array
				(
				'menuaction' => 'property.uientity.columns',
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type
			);


			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'column_list' => $this->bo->column_list($values['columns'], $entity_id = $this->entity_id, $cat_id = $this->cat_id, $allrows = true),
				'function_msg' => $function_msg,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'lang_columns' => lang('columns'),
				'lang_none' => lang('None'),
				'lang_save' => lang('save'),
			);

			$GLOBALS['phpgw_info']['flags']['app_header'] = $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('columns' => $data));
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$file_id = phpgw::get_var('file_id', 'int');
			$jasper = phpgw::get_var('jasper', 'bool');

			$bofiles = CreateObject('property.bofiles');
			$bofiles->get_file($file_id, $jasper);
		}

		function index()
		{
			//redirect. If selected the title of module.
			if ($this->entity_id && !$this->cat_id)
			{
				$categories = $this->soadmin_entity->read_category(array('entity_id' => $this->entity_id));
				foreach ($categories as $category)
				{
					if ($this->acl->check(".{$this->type}.$this->entity_id.{$category['id']}", PHPGW_ACL_READ, $this->type_app[$this->type]))
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uientity.index',
							'entity_id' => $this->entity_id, 'cat_id' => $category['id'], 'type' => $this->type));
					}
				}
				unset($categories);
				unset($category);
			}

			//redirect if no rights
			if (!$this->acl_read && $this->cat_id)
			{
				phpgw::no_access('property', lang('No access') .' :: '. $this->acl_location);
//				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
//					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');

			if ($default_district && !isset($_REQUEST['district_id']))
			{
				$this->bo->district_id = $default_district;
				$this->district_id = $default_district;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			if ($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id, $this->cat_id);
			}

			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.jeditable.js');
			self::add_javascript('phpgwapi', 'jquery', 'editable/jquery.dataTables.editable.js');

			$GLOBALS['phpgw']->jqcal->add_listener('filter_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_end_date');
			phpgwapi_jquery::load_widget('datepicker');

			if ($this->entity_id && $this->cat_id)
			{
				$entity = $this->soadmin_entity->read_single($this->entity_id, false);
				$appname = $entity['name'];
				//$category	 = $this->soadmin_entity->read_single_category($this->entity_id,$this->cat_id);
				$function_msg = 'list ' . $category['name'];
			}

			//$_integration_set = array();

			$data = array(
				'datatable_name' => $appname . ': ' . $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'link',
								'value' => lang('department'),
								'href' => '#',
								'class' => '',
								'onclick' => "JqueryPortico.openPopup({menuaction:'property.uilookup.custom', column:'org_unit_id', type:'org_unit'})"
							),
							array
								(
								'type' => 'label',
								'id' => 'label_org_unit_id'
							),
							array
								(
								'type' => 'hidden',
								'id' => 'org_unit_id',
								'name' => 'org_unit_id',
								'value' => ''
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'start_date',
								'name' => 'start_date',
								'value' => '',
								'text' => lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'end_date',
								'name' => 'end_date',
								'value' => '',
								'text' => lang('to')
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'property.uientity.index',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type,
						'district_id' => $this->district_id,
						'p_num' => $this->p_num,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array(
						'menuaction' => 'property.uientity.download',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type,
						'district_id' => $this->district_id,
						'p_num' => $this->p_num,
						'export' => true,
						'allrows' => true
					)),
					"columns" => array('onclick' => "JqueryPortico.openPopup({menuaction:'property.uientity.columns', entity_id:'{$this->entity_id}', cat_id:'{$this->cat_id}', type:'{$this->type}'}, {closeAction:'reload'})"),
					'new_item' => self::link(array(
						'menuaction' => 'property.uientity.edit',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(),
					'query' => phpgw::get_var('location_code')
				)
			);

			$filters = $this->_get_filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$this->bo->read(array('dry_run' => true));
			$uicols = $this->bo->uicols;

			$uicols['name'][] = 'img_id';
			$uicols['descr'][] = 'dummy';
			$uicols['sortable'][] = false;
			$uicols['sort_field'][] = '';
			$uicols['format'][] = '';
			$uicols['formatter'][] = '';
			$uicols['input_type'][] = 'hidden';

			$uicols['name'][] = 'directory';
			$uicols['descr'][] = 'directory';
			$uicols['sortable'][] = false;
			$uicols['sort_field'][] = '';
			$uicols['format'][] = '';
			$uicols['formatter'][] = '';
			$uicols['input_type'][] = 'hidden';

			$uicols['name'][] = 'file_name';
			$uicols['descr'][] = lang('name');
			$uicols['sortable'][] = false;
			$uicols['sort_field'][] = '';
			$uicols['format'][] = '';
			$uicols['formatter'][] = '';
			$uicols['input_type'][] = 'hidden';

			$uicols['name'][] = 'picture';
			$uicols['descr'][] = '';
			$uicols['sortable'][] = false;
			$uicols['sort_field'][] = '';
			$uicols['format'][] = '';
			$uicols['formatter'][] = 'JqueryPortico.showPicture';
			$uicols['input_type'][] = '';

			$count_uicols_name = count($uicols['name']);

			$type_id = 4;
			for ($i = 1; $i < $type_id; $i++)
			{
				$searc_levels[] = "loc{$i}";
			}

			for ($k = 0; $k < $count_uicols_name; $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				if (!empty($uicols['formatter'][$k]))
				{
					$params['formatter'] = $uicols['formatter'][$k];
				}

				if (in_array($uicols['name'][$k], $searc_levels))
				{
					$params['formatter'] = 'JqueryPortico.searchLink';
				}

				if ($uicols['name'][$k] == 'nhk_link')
				{
					$params['formatter'] = 'JqueryPortico.formatLinkGeneric';
				}

				if ($uicols['name'][$k] == 'num')
				{
					$params['formatter'] = 'JqueryPortico.formatLink';
					$params['hidden'] = false;
				}

				$denied = array('merknad');
				if (in_array($uicols['name'][$k], $denied))
				{
					$params['sortable'] = false;
				}
				else if (isset($uicols['cols_return_extra'][$k]) && ($uicols['cols_return_extra'][$k] != 'T' || $uicols['cols_return_extra'][$k] != 'CH'))
				{
					$params['sortable'] = true;
				}

				array_push($data['datatable']['field'], $params);
			}

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					)
				)
			);

			$parameters2 = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'location_code',
						'source' => 'location_code'
					),
					array
						(
						'name' => 'origin_id',
						'source' => 'id'
					),
					array
						(
						'name' => 'p_num',
						'source' => 'id'
					)
				)
			);

			if ($this->acl_read)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'view',
					'text' => lang('view'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.view',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'parameters' => json_encode($parameters)
				);
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'view',
					'text' => lang('open view in new window'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.view',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_edit)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.edit',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'parameters' => json_encode($parameters)
				);
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('open edit in new window'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.edit',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters)
				);
			}

			if ($category['start_ticket'])
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('start ticket'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uitts.add',
						'p_entity_id' => $this->entity_id,
						'p_cat_id' => $this->cat_id,
						'type' => $this->type,
						'bypass' => true,
						'origin' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}"
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters2)
				);
			}

			$jasper = execMethod('property.sojasper.read', array('location_id' => $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location)));

			foreach ($jasper as $report)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('open JasperReport %1 in new window', $report['title']),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uijasper.view',
						'jasper_id' => $report['id']
					)),
					'target' => '_blank',
					'parameters' => json_encode($parameters)
				);
			}

			if ($this->acl_delete)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.delete',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'type' => $this->type
					)),
					'parameters' => json_encode($parameters)
				);
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl('datatable_jquery', $data);
		}

		function edit( $values = array(), $mode = 'edit' )
		{
			$id = isset($values['id']) && $values['id'] ? $values['id'] : phpgw::get_var('id', 'int');
			$_lean = phpgw::get_var('lean', 'bool');

			if ($mode == 'edit' && (!$this->acl_add && !$this->acl_edit))
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array
					(
					'menuaction' => 'property.uientity.view', 'id' => $id, 'entity_id' => $this->entity_id,
					'cat_id' => $this->cat_id,
					'type' => $this->type));
			}

			if ($mode == 'view')
			{
				if (!$this->acl_read)
				{
					$this->bocommon->no_access();
					return;
				}
			}
			else
			{
				if (!$this->acl_add && !$this->acl_edit)
				{
					$this->bocommon->no_access();
					return;
				}
			}

			$bolocation = CreateObject('property.bolocation');

			$values_attribute = phpgw::get_var('values_attribute');
			$lookup_tenant = phpgw::get_var('lookup_tenant', 'bool');
			$tenant_id = phpgw::get_var('tenant_id', 'int');

			if ($mode == 'edit')
			{
				$location_code = phpgw::get_var('location_code');
				$values['descr'] = phpgw::get_var('descr');
				$p_entity_id = phpgw::get_var('p_entity_id', 'int');
				$p_cat_id = phpgw::get_var('p_cat_id', 'int');

				if ($p_entity_id)
				{
					$values['p'][$p_entity_id]['p_entity_id'] = $p_entity_id;
					$values['p'][$p_entity_id]['p_cat_id'] = $p_cat_id;
					$values['p'][$p_entity_id]['p_num'] = phpgw::get_var('p_num');
				}

				$origin = phpgw::get_var('origin');
				$origin_id = phpgw::get_var('origin_id', 'int');

				if ($p_entity_id && $p_cat_id)
				{
					$entity_category = $this->soadmin_entity->read_single_category($p_entity_id, $p_cat_id);
					$values['p'][$p_entity_id]['p_cat_name'] = $entity_category['name'];
				}

				if ($location_code)
				{
					$values['location_data'] = $bolocation->read_single($location_code, array('tenant_id' => $tenant_id,
						'p_num' => $p_num, 'view' => true));
				}
			}

			if (isset($tenant_id) && $tenant_id)
			{
				$lookup_tenant = true;
			}

			if ($this->cat_id)
			{
				$category = $this->soadmin_entity->read_single_category($this->entity_id, $this->cat_id);
			}
			else
			{
				$cat_list = $this->bo->select_category_list('select', '', PHPGW_ACL_ADD);
			}


			if (empty($id))
			{
				$id = $values['id'];
			}

			if ($id)
			{
				$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
					'id' => $id, 'view' => $mode=='view'));
			}
			else
			{
				if ($this->cat_id)
				{
					$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id), $values);
				}
				$values = $this->_populate($values);
			}

			/* Preserve attribute values from post */
			if (isset($this->receipt['error']) && (isset($values_attribute) && is_array($values_attribute)))
			{
				$values = $this->bocommon->preserve_attribute_values($values, $values_attribute);
			}

			$entity = $this->soadmin_entity->read_single($this->entity_id);

			if ($id)
			{
				$function_msg = lang('edit') . ' ' . $category['name'];
			}
			else
			{
				$function_msg = lang('add') . ' ' . $category['name'];
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$lookup_entity = array();

			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{
				$lookup_entity_id = array_values($entity['lookup_entity']);
			}
			else
			{
				$lookup_entity_id = array();
			}

			if(!empty($category['parent_id']))
			{
				$lookup_entity_id[$category['entity_id']] = $category['parent_id'];
			}

			foreach ($lookup_entity_id as $lookup_id => $lookup_category_id)
			{
				$entity_lookup = $this->soadmin_entity->read_single($lookup_id);
				$lookup_entity[] = array
				(
					'id' => $lookup_id,
					'category_id' => $lookup_category_id,
					'name' => $entity_lookup['name']
				);
			}

			if (isset($category['lookup_tenant']) && $category['lookup_tenant'])
			{
				$lookup_tenant = true;
			}

			if ($location_code)
			{
				$category['location_level'] = count(explode('-', $location_code));
			}

			if ($this->cat_id && (!isset($category['location_level']) || !$category['location_level']))
			{
				$category['location_level'] = -1;
			}

			$_no_link = false;
			if ($lookup_entity && $category['location_link_level'])
			{
				$_no_link = (int)$category['location_link_level'] + 2;
			}

			$location_data = array();

			$lookup_type = $mode == 'edit' ? 'form2' : 'view2';

			if ($entity['location_form'] && $category['location_level'])
			{
				$location_data = $bolocation->initiate_ui_location(array
					(
					'values' => $values['location_data'],
					'type_id' => (int)$category['location_level'],
					'required_level' => 2,
					'no_link' => $_no_link, // disable lookup links for location type less than type_id
					'lookup_type' => $lookup_type,
					'tenant' => $lookup_tenant,
					'lookup_entity' => $lookup_entity,
					'entity_data' => isset($values['p']) ? $values['p'] : ''
				));
			}

			$link_data = array
				(
				'menuaction' => "property.uientity.save",
				'id' => $id,
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type,
				'lean' => $_lean,
				'noframework' => isset($GLOBALS['phpgw_info']['flags']['noframework']) ? $GLOBALS['phpgw_info']['flags']['noframework'] : false
			);

			if (isset($values['files']) && is_array($values['files']))
			{
				$j = count($values['files']);
				for ($i = 0; $i < $j; $i++)
				{
					$values['files'][$i]['file_name'] = urlencode($values['files'][$i]['name']);
				}
			}

			$link_index = array
				(
				'menuaction' => 'property.uientity.index',
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type
			);

			$project_link_data = array
				(
				'menuaction' => 'property.uiproject.edit',
				'bypass' => true,
				'location_code' => $values['location_code'],
				'p_num' => $id,
				'p_entity_id' => $this->entity_id,
				'p_cat_id' => $this->cat_id,
				'tenant_id' => $values['tenant_id'],
				'origin' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
				'origin_id' => $id
			);

			$add_to_project_link_data = array
				(
				'menuaction' => 'property.uiproject.index',
				'from' => 'workorder',
				'lookup' => true,
				'query' => isset($values['location_data']['loc1']) ? $values['location_data']['loc1'] : '',
				//		'p_num'				=> $id,
				//		'p_entity_id'		=> $this->entity_id,
				//		'p_cat_id'			=> $this->cat_id,
				'tenant_id' => $values['tenant_id'],
				'origin' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
				'origin_id' => $id
			);

			$ticket_link_data = array
				(
				'menuaction' => 'property.uitts.add',
				'bypass' => true,
				'location_code' => $values['location_code'],
				'p_num' => $id,
				'p_entity_id' => $this->entity_id,
				'p_cat_id' => $this->cat_id,
				'tenant_id' => $values['tenant_id'],
				'origin' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
				'origin_id' => $id
			);

			$pdf_data = array
				(
				'menuaction' => 'property.uientity.print_pdf',
				'id' => $id,
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type
			);

			$tabs = array();
			$active_tab = phpgw::get_var('active_tab');

			if ($category['location_level'])
			{
				$tabs['location'] = array('label' => lang('location'), 'link' => '#location',
					'disable' => 0);
				$active_tab = $active_tab ? $active_tab : 'location';
			}

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction' => 'property.uientity.attrib_history',
							'acl_location' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
							//		'entity_id'	=> $this->entity_id,
							//		'cat_id'	=> $this->cat_id,
							'attrib_id' => $attribute['id'],
							'id' => $id,
							'edit' => true,
							'type' => $this->type
						);

						$attribute['link_history'] = $GLOBALS['phpgw']->link('/index.php', $link_history_data);
					}

					/*
					 * Hide dummy attributes that act as placeholders
					 */
					if ($attribute['datatype'] == 'R' && isset($attribute['choice']) && !$attribute['choice'])
					{
						$attribute['hide_row'] = true;
					}
				}

				$_enable_controller = !!$category['enable_controller'] || !!$values['entity_group_id'];
				if ($_enable_controller && $id)
				{
					$tabs['controller'] = array('label' => lang('controller'), 'link' => '#controller',
						'function' => "set_tab('controller')");
					$active_tab = $active_tab ? $active_tab : 'location';
					$GLOBALS['phpgw']->jqcal->add_listener('control_start_date');
				}

				$location = ".{$this->type}.{$this->entity_id}.{$this->cat_id}";
				$attributes_groups = $this->bo->get_attribute_groups($location, $values['attributes']);
//				_debug_array($attributes_groups);
				$attributes_general = array();
				$i = -1;
				$attributes = array();

				$_dummy = array(array(
//					'id' => 0,
//					'datatype' => 'R',
//					'nullable' => 1,
				));
				foreach ($attributes_groups as $_key => $group)
				{
					if (!isset($group['attributes']))
					{
						$group['attributes'] = $_dummy;
					}
					if ((isset($group['group_sort']) || !$location_data))
					{
						if ($group['level'] == 0)
						{
							$_tab_name = str_replace(array(' ', '/', '?', '.', '*', '(', ')', '[', ']'), '_', $group['name']);
							$active_tab = $active_tab ? $active_tab : $_tab_name;
							$tabs[$_tab_name] = array('label' => $group['name'], 'link' => "#{$_tab_name}",
								'disable' => 0);
							$group['link'] = $_tab_name;
							$attributes[] = $group;
							$i ++;
						}
						else
						{
							$attributes[$i]['attributes'][] = array
								(
								'datatype' => 'section',
								'descr' => '<H' . ($group['level'] + 1) . "> {$group['descr']} </H" . ($group['level'] + 1) . '>',
								'level' => $group['level'],
							);
							$attributes[$i]['attributes'] = array_merge($attributes[$i]['attributes'], $group['attributes']);
						}
						unset($_tab_name);
					}
					else if (!isset($group['group_sort']) && $location_data)
					{
						$attributes_general = array_merge($attributes_general, $group['attributes']);
					}
				}
				unset($attributes_groups);
				/*
				  if($category['jasperupload'])
				  {
				  $tabs['jasper']	= array('label' => lang('jasper reports'), 'link' => '#jasper');
				  }
				 */
			}

// ---- START INTEGRATION -------------------------

			$custom_config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location));
			$_config = isset($custom_config->config_data) && $custom_config->config_data ? $custom_config->config_data : array();

			$integration = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				if (isset($_config_section_data['tab']) && $values['id'])
				{
					if (!isset($_config_section_data['url']))
					{
						phpgwapi_cache::message_set("'url' is a required setting for integrations, '{$_config_section}' is disabled", 'error');
						break;
					}

					//get session key from remote system
					$arguments = array($_config_section_data['auth_hash_name'] => $_config_section_data['auth_hash_value']);
					$query = http_build_query($arguments);
					$auth_url = $_config_section_data['auth_url'];
					$request = "{$auth_url}?{$query}";

					$aContext = array
						(
						'http' => array
							(
							'request_fulluri' => true,
						),
					);

					if (isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
					{
						$aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
					}

					$cxContext = stream_context_create($aContext);
					$response = trim(file_get_contents($request, False, $cxContext));

					$integration[] = array
						(
						'section' => $_config_section,
						'height' => isset($_config_section_data['height']) && $_config_section_data['height'] ? $_config_section_data['height'] : 500
					);

					$_config_section_data['url'] = htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres'] = htmlspecialchars_decode($_config_section_data['parametres']);

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;

						$__value = false;
						if (!$__value = urlencode($values[str_replace(array('__', '*'), array('', ''), $_substitute)]))
						{
							foreach ($values['attributes'] as $_attribute)
							{
								if (str_replace(array('__', '*'), array('', ''), $_substitute) == $_attribute['name'])
								{
									$__value = urlencode($_attribute['value']);
									break;
								}
							}
						}

						if ($__value)
						{
							$_values[] = $__value;
						}
					}

					unset($output);
					unset($__value);
					$_sep = '?';
					if (stripos($_config_section_data['url'], '?'))
					{
						$_sep = '&';
					}
					$_param = str_replace($_keys, $_values, $_config_section_data['parametres']);
					unset($_keys);
					unset($_values);
					//				$integration_src = phpgw::safe_redirect("{$_config_section_data['url']}{$_sep}{$_param}");
					$integration_src = "{$_config_section_data['url']}{$_sep}{$_param}";
					if ($_config_section_data['action'])
					{
						$_sep = '?';
						if (stripos($integration_src, '?'))
						{
							$_sep = '&';
						}
						$integration_src .= "{$_sep}{$_config_section_data['action']}=" . $_config_section_data["action_{$mode}"];
					}

					$arguments = array($_config_section_data['auth_key_name'] => $response);

					if (isset($_config_section_data['location_data']) && $_config_section_data['location_data'])
					{
						$_config_section_data['location_data'] = htmlspecialchars_decode($_config_section_data['location_data']);
						parse_str($_config_section_data['location_data'], $output);
						foreach ($output as $_dummy => $_substitute)
						{
							$_keys[] = $_substitute;
							$_values[] = urlencode($values['location_data'][trim($_substitute, '_')]);
						}
						$integration_src .= '&' . str_replace($_keys, $_values, $_config_section_data['location_data']);
					}

					$integration_src .= "&{$_config_section_data['auth_key_name']}={$response}";

					$tabs[$_config_section] = array('label' => $_config_section_data['tab'], 'link' => "#{$_config_section}",
						'disable' => 0, 'function' => "document.getElementById('{$_config_section}_content').src = '{$integration_src}';");
				}
			}

// ---- END INTEGRATION -------------------------

			unset($values['attributes']);
			$datatable_def = array();

			if ($id)
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location);

				$check_doc = $this->bocommon->get_lookup_entity('document');
				foreach ($check_doc as $_check)
				{
					if ($_check['id'] == $this->entity_id)
					{
						$get_docs = true;
						break;
					}
				}

				if ($get_docs || !empty($entity['documentation']))
				{
					$get_docs = true;

					$tabs['document'] = array('label' => lang('document'), 'link' => '#document', 'disable' => 0);

					$cats = CreateObject('phpgwapi.categories', -1, 'property', '.document');
					$cats->supress_info = true;
					$categories = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => '',
						'globals' => true, 'use_acl' => true));
					$default_value = array('cat_id' => '', 'name' => lang('no document type'), 'selected' => 'selected');
					array_unshift($categories['cat_list'], $default_value);

					foreach ($categories['cat_list'] as & $_category)
					{
						$_category['id'] = $_category['cat_id'];
					}
					$doc_type_filter = $categories['cat_list'];

					$documents_tabletools = array
						(
						'my_name' => 'add',
						'text' => lang('add new document'),
						'type' => 'custom',
						'className' => 'add',
						'custom_code' => "
								var oArgs = " . json_encode(array(
									'menuaction' => 'property.uidocument.edit',
									'p_entity_id' => $this->entity_id,
									'p_cat_id' => $this->cat_id,
									'p_num' => $values['num']
						)) . ";
								newDocument(oArgs);
							"
					);

					$documents_def = array
						(
						array('key' => 'document_name', 'label' => lang('name'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true)
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_7',
						'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uientity.get_documents',
							'location_id' => $location_id, 'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'item_id' => $id, 'phpgw_return_as' => 'json'))),
						'data' => "",
						'tabletools' => ($mode == 'edit') ? $documents_tabletools : array(),
						'ColumnDefs' => $documents_def,
						'config' => array(
							array('disableFilter' => true)
						)
					);
				}

				if ($category['fileupload'] || (isset($values['files']) && $values['files']))
				{
					$tabs['files'] = array('label' => lang('files'), 'link' => '#files', 'disable' => 0);

					$link_file_data = array
						(
						'menuaction' => 'property.uientity.view_file',
						'loc1' => $values['location_data']['loc1'],
						'id' => $id,
						'cat_id' => $this->cat_id,
						'entity_id' => $this->entity_id,
						'type' => $this->type
					);

					$file_def = array
						(
						array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
							'resizeable' => true)
					);


					$datatable_def[] = array
						(
						'container' => 'datatable-container_0',
						'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uientity.get_files',
								'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'id' => $id, 'type' => $this->type, 'phpgw_return_as' => 'json'))),
						'ColumnDefs' => $file_def,
						'config' => array(
							array('disableFilter' => true),
							array('disablePagination' => true)
						)
					);
				}

				if (!$category['enable_bulk'])
				{
					$tabs['related'] = array('label' => lang('log'), 'link' => '#related', 'disable' => 0);
				}

				$target_def = array
					(
					array('key' => 'url', 'label' => lang('id'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'type', 'label' => lang('type'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
					array('key' => 'user', 'label' => lang('user'), 'sortable' => true, 'resizeable' => true),
					array('key' => 'entry_date', 'label' => lang('entry date'), 'sortable' => false,
						'resizeable' => true)
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_1',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uientity.get_target',
						'entity_id' => $this->entity_id,
						'cat_id' => $this->cat_id,
						'id' => $id,
						'type' => $this->type,
						'phpgw_return_as' => 'json')
						)
					),
					'ColumnDefs' => $target_def,
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$related_def = array
					(
					array('key' => 'url', 'label' => lang('where'), 'sortable' => false, 'resizeable' => true)
				);

				$datatable_def[] = array
					(
					'container' => 'datatable-container_2',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uientity.get_related',
							'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'id' => $id, 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => $related_def,
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				if ($category['enable_bulk'])
				{
					$tabs['inventory'] = array('label' => lang('inventory'), 'link' => '#inventory',
						'disable' => 0);

					$inventory_def = array
						(
						array('key' => 'where', 'label' => lang('where'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'edit', 'label' => lang('edit'), 'sortable' => false, 'resizeable' => true),
						//array('key' => 'delete','label'=>lang('delete'),'sortable'=>false,'resizeable'=>true),
						array('key' => 'unit', 'label' => lang('unit'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'inventory', 'label' => lang('count'), 'sortable' => false,
							'resizeable' => true, 'className' => 'dt-right'),
						array('key' => 'allocated', 'label' => lang('allocated'), 'sortable' => false,
							'resizeable' => true, 'className' => 'dt-right'),
						array('key' => 'bookable', 'label' => lang('bookable'), 'sortable' => false,
							'resizeable' => true, 'className' => 'dt-right'),
						array('key' => 'calendar', 'label' => lang('calendar'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'remark', 'label' => lang('remark'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'location_id', 'hidden' => true),
						array('key' => 'id', 'hidden' => true),
						array('key' => 'inventory_id', 'hidden' => true)
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_3',
						'requestUrl' => json_encode(self::link(array('menuaction' => 'property.uientity.get_inventory',
								'id' => $id, 'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
								'type' => $this->type,
								'phpgw_return_as' => 'json'))),
						'ColumnDefs' => $inventory_def,
						'config' => array(
							array('disableFilter' => true),
							array('disablePagination' => true)
						)
					);
				}

				if ($_enable_controller)
				{
					$_controls = $this->get_controls_at_component($location_id, $id);

					$controls_def = array
						(
						array('key' => 'serie_id', 'label' => 'serie', 'sortable' => false, 'resizeable' => true),
						array('key' => 'control_id', 'label' => lang('controller'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'assigned_to_name', 'label' => lang('user'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'start_date', 'label' => lang('start date'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'repeat_type', 'label' => lang('repeat type'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'repeat_interval', 'label' => lang('interval'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'controle_time', 'label' => lang('controle time'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'service_time', 'label' => lang('service time'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'total_time', 'label' => lang('total time'), 'sortable' => false,
							'resizeable' => true),
						array('key' => 'serie_enabled', 'label' => lang('enabled'), 'sortable' => false,
							'resizeable' => true),
//					array('key' => 'select','label'=>lang('select'),'sortable'=>false,'resizeable'=>true),
						array('key' => 'location_id', 'hidden' => true),
						array('key' => 'component_id', 'hidden' => true),
						array('key' => 'id', 'hidden' => true),
						array('key' => 'assigned_to', 'hidden' => true),
					);
					$tabletools = array
						(
						array(
							'my_name' => 'add',
							'text' => lang('add'),
							'type' => 'custom',
							'className' => 'add',
							'custom_code' => "
										add_control();"
						),
						array(
							'my_name' => 'enable',
							'text' => lang('enable'),
							'type' => 'custom',
							'custom_code' => "
										onActionsClick('enable');"
						),
						array(
							'my_name' => 'disable',
							'text' => lang('disable'),
							'type' => 'custom',
							'custom_code' => "
										onActionsClick('disable');"
						),
						array(
							'my_name' => 'edit',
							'text' => lang('edit'),
							'type' => 'custom',
							'custom_code' => "
										onActionsClick('edit');"
						)
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_4',
						'requestUrl' => "''",
						'tabletools' => $tabletools,
						'ColumnDefs' => $controls_def,
						'data' => json_encode($_controls),
						'config' => array(
							array('disableFilter' => true),
							array('disablePagination' => true)
						)
					);

					$_checklists = $this->get_checklists($location_id, $id, date('Y'));
					$check_lst_time_span = $this->controller_helper->get_check_lst_time_span();

					$_checklists_def = array
						(
						array('key' => 'id', 'label' => lang('id'), 'sortable' => false),
						array('key' => 'control_name', 'label' => lang('name'), 'sortable' => false),
						array('key' => 'status', 'label' => lang('status'), 'sortable' => true),
						array('key' => 'user', 'label' => lang('user'), 'sortable' => false),
						array('key' => 'deadline', 'label' => lang('deadline'), 'sortable' => false),
						array('key' => 'planned_date', 'label' => lang('planned date'), 'sortable' => true),
						array('key' => 'completed_date', 'label' => lang('completed date'), 'sortable' => false),
						array('key' => 'num_open_cases', 'label' => lang('open_cases'), 'sortable' => false),
						array('key' => 'num_pending_cases', 'label' => lang('pending_cases'), 'sortable' => false),
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_5',
						'requestUrl' => "''",
						'ColumnDefs' => $_checklists_def,
						'data' => json_encode($_checklists),
						'config' => array(
							array('disableFilter' => true),
							array('disablePagination' => true),
							array('singleSelect' => true)
						)
					);
					$_cases = $this->get_cases($location_id, $id, date('Y')); // initial search

					$_case_def = array
						(
						array('key' => 'url', 'label' => lang('id'), 'sortable' => true, 'resizeable' => true),
						array('key' => 'type', 'label' => lang('type'), 'sortable' => true, 'resizeable' => true),
						array('key' => 'title', 'label' => lang('title'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'value', 'label' => lang('value'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'status', 'label' => lang('status'), 'sortable' => false, 'resizeable' => true),
						array('key' => 'user', 'label' => lang('user'), 'sortable' => true, 'resizeable' => true),
						array('key' => 'entry_date', 'label' => lang('entry date'), 'sortable' => false,
							'resizeable' => true),
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_6',
						'requestUrl' => "''",
						'ColumnDefs' => $_case_def,
						'data' => json_encode($_cases),
						'config' => array(
							array('disableFilter' => true),
					//		array('disablePagination' => true)
						)
					);
				}

			}

			//$category['org_unit'] =1;
			if ($category['org_unit'] && $mode == 'edit')
			{
				phpgwapi_jquery::load_widget('autocomplete');

				$_autocomplete = <<<JS

					$(document).ready(function ()
					{
						var oArgs = {menuaction:'property.bogeneric.get_autocomplete', type:'org_unit'};
						var strURL = phpGWLink('index.php', oArgs, true);
						JqueryPortico.autocompleteHelper(strURL, 'org_unit_name', 'org_unit_id', 'org_unit_container');
					});
JS;
				$GLOBALS['phpgw']->js->add_code('', $_autocomplete);
			}

			$msgbox_data = $this->bocommon->msgbox_data($this->receipt);

			$repeat_types = array();
//			$repeat_types[] = array('id'=> -1, 'name' => lang('day'));
//			$repeat_types[] = array('id'=> 1, 'name' => lang('weekly'));
			$repeat_types[] = array('id' => 2, 'name' => lang('month'));
			$repeat_types[] = array('id' => 3, 'name' => lang('year'));

			$entity_group_name = '';
			$entity_group_list = execMethod('property.bogeneric.get_list', array('type' => 'entity_group',
				'selected' => $values['entity_group_id'], 'add_empty' => true));
			foreach ($entity_group_list as $entity_group)
			{
				if ($category['entity_group_id'] && $entity_group['id'] == $category['entity_group_id'])
				{
					$entity_group_name = $entity_group['name'];
				}
			}

			$data = array
				(
				'datatable_def' => $datatable_def,
				'repeat_types' => array('options' => $repeat_types),
				'controller' => $_enable_controller && $id,
				'check_lst_time_span' => array('options' => $check_lst_time_span),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_index),
				'enable_bulk' => $category['enable_bulk'],
				'org_unit' => $category['org_unit'],
				'value_org_unit_id' => $values['org_unit_id'],
				'value_org_unit_name' => $values['org_unit_name'],
				'value_org_unit_name_path' => $values['org_unit_name_path'],
				'value_location_id' => $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], $this->acl_location),
				'link_pdf' => $GLOBALS['phpgw']->link('/index.php', $pdf_data),
				'start_project' => $category['start_project'],
				'lang_start_project' => lang('start project'),
				'project_link' => $GLOBALS['phpgw']->link('/index.php', $project_link_data),
				'add_to_project_link' => $GLOBALS['phpgw']->link('/index.php', $add_to_project_link_data),
				'start_ticket' => $category['start_ticket'],
				'lang_start_ticket' => lang('start ticket'),
				'ticket_link' => $GLOBALS['phpgw']->link('/index.php', $ticket_link_data),
				'fileupload' => $category['fileupload'],
				//		'jasperupload'					=> $category['jasperupload'],
				'link_view_file' => $GLOBALS['phpgw']->link('/index.php', $link_file_data),
				//		'link_to_files'					=> $link_to_files,
				'files' => isset($values['files']) ? $values['files'] : '',
				//		'jasperfiles'					=> isset($values['jasperfiles'])?$values['jasperfiles']:'',
				'multiple_uploader' => $id ? true : '',

				'multi_upload_parans' => "{menuaction:'property.uientity.build_multi_upload_file',"
				. "id:'{$id}',"
				. "_entity_id:'{$this->entity_id}',"
				. "_cat_id:'{$this->cat_id}',"
				. "_type:'{$this->type}'}",
				'value_origin' => isset($values['origin_data']) ? $values['origin_data'] : '',
				'value_origin_type' => isset($origin) ? $origin : '',
				'value_origin_id' => isset($origin_id) ? $origin_id : '',
				'lang_no_cat' => lang('no category'),
				'lang_cat_statustext' => lang('Select the category. To do not use a category select NO CATEGORY'),
				'select_name' => 'cat_id',
				'cat_list' => isset($cat_list) ? $cat_list : '',
				'location_code' => isset($location_code) ? $location_code : '',
				'lookup_tenant' => $lookup_tenant,
				'lang_entity' => lang('entity'),
				'entity_name' => $entity['name'],
				'lang_category' => lang('category'),
				'category_name' => $category['name'],
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'attributes_group' => $attributes,
				'attributes_general' => array('attributes' => $attributes_general),
				'lookup_functions' => isset($values['lookup_functions']) ? $values['lookup_functions'] : '',
				'lang_none' => lang('None'),
				'location_data2' => $location_data,
				'lookup_type' => $lookup_type,
				'mode' => $mode,
				'form_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'done_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uientity.index',
					'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'type' => $this->type)),
				'lang_id' => lang('ID'),
				'value_id' => $values['id'],
				'value_num' => $values['num'],
				'error_flag' => isset($error_id) ? $error_id : '',
				'lang_history' => lang('history'),
				'lang_history_help' => lang('history of this attribute'),
				'lang_history_date_statustext' => lang('Enter the date for this reading'),
				'lang_date' => lang('date'),
				'textareacols' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textareacols'] : 40,
				'textarearows' => isset($GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows']) && $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] ? $GLOBALS['phpgw_info']['user']['preferences']['property']['textarearows'] : 6,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'active_tab' => $active_tab,
				'integration' => $integration,
				'doc_type_filter' => array('options' => $doc_type_filter),
				'documents' => $get_docs ? 1 : 0,
				/*'requestUrlDoc' => $requestUrlDoc ? $requestUrlDoc : '',*/

				'lean' => $_lean ? 1 : 0,
				'entity_group_list' => array('options' => $entity_group_list),
				'entity_group_name' => $entity_group_name,
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'))
			);

			//print_r($data['location_data2']);die;

			$appname = $entity['name'];

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;

			self::add_javascript('property', 'portico', 'entity.edit.js');

			$attribute_template = 'attributes_form';
			if($mode == 'view')
			{
				$attribute_template = 'attributes_view';
			}

			self::render_template_xsl(array('entity', 'datatable_inline', $attribute_template,
				'files'), array('edit' => $data));

			$criteria = array
				(
				'appname' => $this->type_app[$this->type],
				'location' => ".{$this->type}.{$this->entity_id}.{$this->cat_id}",
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);


			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/{$this->type_app[$this->type]}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";

				if ($entry['active'] && $entry['client_side'] && is_file($file))
				{
					$GLOBALS['phpgw']->js->add_external_file("{$this->type_app[$this->type]}/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}");
				}
			}
		}

		function attrib_help()
		{
			$t = & $GLOBALS['phpgw']->template;
			$t->set_root(PHPGW_APP_TPL);

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$entity_id = phpgw::get_var('entity_id', 'int');
			$cat_id = phpgw::get_var('cat_id', 'int');
			$attrib_id = phpgw::get_var('attrib_id', 'int');

			$data_lookup = array
				(
				'entity_id' => $entity_id,
				'cat_id' => $cat_id,
				'attrib_id' => $attrib_id
			);

			$entity_category = $this->soadmin_entity->read_single_category($entity_id, $cat_id);

			$help_msg = $this->bo->read_attrib_help($data_lookup);

			$custom = createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->get($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $attrib_id);

			$attrib_name = $attrib_data['input_text'];
			$function_msg = lang('Help');


			$t->set_file('help', 'help.tpl');
			$t->set_var('title', lang('Help') . '<br>' . $entity_category['descr'] . ' - "' . $attrib_name . '"');
			$t->set_var('help_msg', $help_msg);
			$t->set_var('lang_close', lang('close'));

			$GLOBALS['phpgw']->common->phpgw_header();
			$t->pfp('out', 'help');
		}

		function delete()
		{
			$id = phpgw::get_var('id', 'int');

			//cramirez add JsonCod for Delete
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$this->bo->delete($id);
				return "id " . $id . " " . lang("has been deleted");
			}


			if (!$this->acl_delete)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 8, 'acl_location' => $this->acl_location));
			}


			$confirm = phpgw::get_var('confirm', 'bool', 'POST');

			$link_data = array
				(
				'menuaction' => 'property.uientity.index',
				'entity_id' => $this->entity_id,
				'cat_id' => $this->cat_id,
				'type' => $this->type
			);

			if (phpgw::get_var('confirm', 'bool', 'POST'))
			{
				$this->bo->delete($id);
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_data);
			}

			$GLOBALS['phpgw']->xslttpl->add_file(array('app_delete'));

			$data = array
				(
				'done_action' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'delete_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uientity.delete',
					'entity_id' => $this->entity_id, 'cat_id' => $this->cat_id, 'id' => $id, 'type' => $this->type)),
				'lang_confirm_msg' => lang('do you really want to delete this entry'),
				'lang_yes' => lang('yes'),
				'lang_yes_statustext' => lang('Delete the entry'),
				'lang_no_statustext' => lang('Back to the list'),
				'lang_no' => lang('no')
			);

			$appname = lang('entity');
			$function_msg = lang('delete entity');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('delete' => $data));
		}

		function view()
		{
			if (!$this->acl_read)
			{
				$this->bocommon->no_access();
				return;
			}
			$this->edit(null, $mode = 'view');
		}

//		function get_assigned_history()
//		{
//			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
//			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;
//
//			if ($this->acl_read)
//			{
//				$this->bocommon->no_access();
//				return;
//			}
//			$serie_id = phpgw::get_var('serie_id', 'int');
//			$history = execMethod('controller.socontrol.get_assigned_history', array('serie_id' => $serie_id));
//			$lang_user = lang('user');
//			$lang_date = lang('date');
//
//			$ret = <<<HTML
//			<html>
//				<head>
//				</head>
//				<body>
//					<table style="width:90%" align = 'center'>
//						<tr align = 'left'>
//							<th>
//								{$lang_user}
//							</th>
//							<th>
//								{$lang_date}
//							</th>
//						</tr>
//
//HTML;
//			foreach ($history as $entry)
//			{
//				$date = $GLOBALS['phpgw']->common->show_date($entry['assigned_date']);
//				$ret .= <<<HTML
//						<tr align = 'left'>
//							<td>
//								{$entry['assigned_to_name']}
//							</td>
//							<td>
//								{$date}
//							</td>
//						</tr>
//HTML;
//			}
//			$ret .= <<<HTML
//					</table>
//				</body>
//			</html>
//HTML;
//			echo $ret;
//		}

		function attrib_history()
		{
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$acl_location = phpgw::get_var('acl_location', 'string');
			$id = phpgw::get_var('id', 'int');
			$attrib_id = phpgw::get_var('attrib_id', 'int');
			$detail_id = phpgw::get_var('detail_id', 'int');

			$data_lookup = array
				(
				'acl_location' => $acl_location,
				'id' => $id,
				'attrib_id' => $attrib_id,
				'detail_id' => $detail_id
			);

			$delete = phpgw::get_var('delete', 'bool');
			$edit = phpgw::get_var('edit', 'bool');

			if ($delete)
			{
				$data_lookup['history_id'] = phpgw::get_var('history_id', 'int');
				$this->bo->delete_history_item($data_lookup);

				return 'ok';
			}

			$link_data = array
				(
				'menuaction' => 'property.uientity.attrib_history',
				'acl_location' => $acl_location,
				'id' => $id,
				'attrib_id' => $attrib_id,
				'detail_id' => $detail_id,
				'edit' => $edit,
				'type' => $this->type,
				'phpgw_return_as' => 'json'
			);


			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$values = $this->bo->read_attrib_history($data_lookup);
				$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

				$content = array();
				//while (is_array($values) && list(, $entry) = each($values))
                                if (is_array($values))
                                {
                                    foreach($values as $entry)
				{
					$content[] = array
						(
						'id' => $entry['id'],
						'value' => $entry['new_value'],
						'user' => $entry['owner'],
						'time_created' => $GLOBALS['phpgw']->common->show_date($entry['datetime'], "{$dateformat} G:i:s")
					);
				}
                                }

				$draw = phpgw::get_var('draw', 'int');
				$allrows = phpgw::get_var('length', 'int') == -1;

				$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
				$total_records = count($content);

				$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 0);

				if ($allrows)
				{
					$out = $content;
				}
				else
				{
					if ($total_records > $num_rows)
					{
						$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
						$values_part = array_chunk($content, $num_rows);
						$out = $values_part[$page];
					}
					else
					{
						$out = $content;
					}
				}

				$result_data = array('results' => $out);

				$result_data['total_records'] = $total_records;
				$result_data['draw'] = $draw;

				return $this->jquery_results($result_data);
			}

			$tabletools = array();
			if ($edit && $this->acl->check($acl_location, PHPGW_ACL_DELETE, $this->type_app[$this->type]))
			{
				$parameters = array
					(
					'parameter' => array
						(
						array
							(
							'name' => 'history_id',
							'source' => 'id'
						)
					)
				);

				$tabletools[] = array
					(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'confirm_msg' => lang('do you really want to delete this entry'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'property.uientity.attrib_history',
						'acl_location' => $acl_location,
						'id' => $id,
						'attrib_id' => $attrib_id,
						'detail_id' => $detail_id,
						'delete' => true,
						'edit' => true,
						'type' => $this->type
					)),
					'parameters' => json_encode($parameters)
				);
			}

			$history_def = array
				(
				array('key' => 'value', 'label' => lang('value'), 'sortable' => false),
				array('key' => 'time_created', 'label' => lang('time created'), 'sortable' => false),
				array('key' => 'user', 'label' => lang('user'), 'sortable' => false),
				array('key' => 'id', 'hidden' => true)
			);

			$datatable_def = array();
			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link($link_data)),
				'ColumnDefs' => $history_def,
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true)
				)
			);

			$data = array
				(
				'base_java_url' => json_encode(array(menuaction => "property.uientity.attrib_history")),
				'datatable_def' => $datatable_def,
				'link_url' => $GLOBALS['phpgw']->link('/index.php', $link_data),
				'img_path' => $GLOBALS['phpgw']->common->get_image_path('phpgwapi', 'default')
			);

			$custom = createObject('phpgwapi.custom_fields');
			$attrib_data = $custom->get($this->type_app[$this->type], ".{$this->type}.{$entity_id}.{$cat_id}", $attrib_id);
			$appname = $attrib_data['input_text'];
			$function_msg = lang('history');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang($this->type_app[$this->type]) . ' - ' . $appname . ': ' . $function_msg;

			self::render_template_xsl(array('attrib_history', 'datatable_inline'), array(
				'attrib_history' => $data));
		}

		function print_pdf()
		{
			if (!$this->acl_read)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'property.uilocation.stop',
					'perm' => 1, 'acl_location' => $this->acl_location));
			}

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$bolocation = CreateObject('property.bolocation');

			$id = phpgw::get_var('id', 'int');

			if ($id)
			{
				$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id,
					'id' => $id, 'view' => true));
			}
			else
			{
				if ($this->cat_id)
				{
					$values = $this->bo->read_single(array('entity_id' => $this->entity_id, 'cat_id' => $this->cat_id));
				}
				else
				{
					echo 'Nothing';
					return;
				}
			}

			if (isset($values['cat_id']) && $values['cat_id'])
			{
				$this->cat_id = $values['cat_id'];
			}

			$entity = $this->soadmin_entity->read_single($this->entity_id);
			$category = $this->soadmin_entity->read_single_category($this->entity_id, $this->cat_id);

			if (isset($entity['lookup_entity']) && is_array($entity['lookup_entity']))
			{
				for ($i = 0; $i < count($entity['lookup_entity']); $i++)
				{
					if (isset($values['p'][$entity['lookup_entity'][$i]]) && $values['p'][$entity['lookup_entity'][$i]])
					{
						$lookup_entity[$i]['id'] = $entity['lookup_entity'][$i];
						$entity_lookup = $this->soadmin_entity->read_single($entity['lookup_entity'][$i]);
						$lookup_entity[$i]['name'] = $entity_lookup['name'];
					}
				}
			}

			$location_data = $bolocation->initiate_ui_location(array
				(
				'values' => $values['location_data'],
				'type_id' => $category['location_level'],
				'no_link' => false, // disable lookup links for location type less than type_id
				'lookup_type' => 'view',
				'tenant' => $category['lookup_tenant'],
				'lookup_entity' => isset($lookup_entity) ? $lookup_entity : '', // Needed ?
				'entity_data' => isset($values['p']) ? $values['p'] : '' // Needed ?
				)
			);

			//_debug_array($values);
			$pdf = CreateObject('phpgwapi.pdf');

			$date = $GLOBALS['phpgw']->common->show_date('', $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
			$entry_date = $GLOBALS['phpgw']->common->show_date($values['entry_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

			// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
			//error_reporting(7);
			//error_reporting(E_ALL);
			set_time_limit(1800);
			$pdf->ezSetMargins(90, 70, 50, 50);
			$pdf->selectFont(PHPGW_API_INC . '/pdf/fonts/Helvetica.afm');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();
			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 760, 578, 760);

			$pdf->addText(50, 790, 10, $GLOBALS['phpgw']->accounts->id2name($values['user_id']) . ': ' . $entry_date);
			$pdf->addText(50, 770, 16, $entity['name'] . '::' . $category['name'] . ' #' . $id);
			$pdf->addText(300, 28, 10, $date);

			$pdf->restoreState();
			$pdf->closeObject();
			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');
			$pdf->ezStartPageNumbers(500, 28, 10, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);

			$pdf->ezTable($content_heading, '', '', array('xPos' => 220, 'xOrientation' => 'right',
				'width' => 300, 0, 'shaded' => 0, 'fontSize' => 10, 'gridlines' => 0,
				'titleFontSize' => 12, 'outerLineThickness' => 0, 'showHeadings' => 0
				, 'cols' => array('text' => array('justification' => 'left', 'width' => 100),
					'value' => array('justification' => 'left', 'width' => 200))
				)
			);

			$table_header = array(
				'name' => array('justification' => 'left', 'width' => 110),
				'sep' => array('justification' => 'center', 'width' => 15),
				'value' => array('justification' => 'left', 'width' => 300)
			);

			if (is_array($location_data['location']))
			{
				foreach ($location_data['location'] as $entry)
				{
					$value = '';
					if ($entry['input_type'] != 'hidden')
					{
						$value = $entry['value'];
					}
					if (isset($entry['extra']) && is_array($entry['extra']))
					{
						foreach ($entry['extra'] as $extra)
						{
							if ($extra['input_type'] != 'hidden')
							{
								$value .= ' ' . $extra['value'];
							}
						}
					}

					$content[] = array
						(
						'name' => $entry['name'],
						'sep' => '-',
						'value' => trim($value)
					);
				}
			}

			if (is_array($values['attributes']))
			{
				foreach ($values['attributes'] as $entry)
				{
					if (isset($entry['choice']) && is_array($entry['choice']))
					{
						$values = array();
						foreach ($entry['choice'] as $choice)
						{
							if (isset($choice['checked']) && $choice['checked'])
							{
								$values[] = "[*{$choice['value']}*]";
							}
							else
							{
								$values[] = $choice['value'];
							}
						}
						$value = implode(' , ', $values);
					}
					else
					{
						$value = $entry['value'];
					}

					$content[] = array
						(
						'name' => $entry['input_text'],
						'sep' => '-',
						'value' => $value
					);

					if ($entry['datatype'] == 'T' || $entry['datatype'] == 'V')
					{
						$content[] = array
							(
							'name' => '|',
							'sep' => '',
							'value' => ''
						);
						$content[] = array
							(
							'name' => '|',
							'sep' => '',
							'value' => ''
						);
					}
				}
				$pdf->ezTable($content, '', '', array('xPos' => 50, 'xOrientation' => 'right',
					'width' => 500, 0, 'shaded' => 0, 'fontSize' => 10, 'gridlines' => 0,
					'titleFontSize' => 12, 'outerLineThickness' => 2, 'showHeadings' => 0
					, 'cols' => $table_header
					)
				);
			}

			$document = $pdf->ezOutput();
			$pdf->print_pdf($document, $entity['name'] . '_' . str_replace(' ', '_', $GLOBALS['phpgw']->accounts->id2name($this->account)));
		}

		public function get_inventory()
		{
			$id = phpgw::get_var('id', 'int');
			$draw = phpgw::get_var('draw', 'int');
			$allrows = phpgw::get_var('length', 'int') == -1;

			if (!$id)
			{
				$location_id = phpgw::get_var('location_id', 'int');
				$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);
				$location = explode('.', $system_location['location']);
				$this->bo->type = $location[1];
				$this->bo->entity_id = $location[1];
				$this->bo->cat_id = $location[3];
			}
			else
			{
				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$this->type], ".{$this->type}.{$this->entity_id}.{$this->cat_id}");
			}

			$values = $this->bo->get_inventory(array('id' => $id, 'location_id' => $location_id));

			foreach ($values as &$value)
			{
				$value['edit'] = '<a href="javascript:showlightbox_edit_inventory(' . $value['location_id'] . ',' . $value['id'] . ',' . $value['inventory_id'] . ')">' . lang('edit') . '</a>';
				$value['calendar'] = '<a href="javascript:showlightbox_show_calendar(' . $value['location_id'] . ',' . $value['id'] . ',' . $value['inventory_id'] . ')">' . lang('calendar') . '</a>';
				$value['inventory'] = number_format($value['inventory'], 0, ',', ' ');
				$value['allocated'] = number_format($value['allocated'], 0, ',', ' ');
			}

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$total_records = count($values);

			$num_rows = phpgw::get_var('length', 'int', 'REQUEST', 0);

			if ($allrows)
			{
				$out = $values;
			}
			else
			{
				if ($total_records > $num_rows)
				{
					$page = ceil(( $start / $total_records ) * ($total_records / $num_rows));
					$values_part = array_chunk($values, $num_rows);
					$out = $values_part[$page];
				}
				else
				{
					$out = $values;
				}
			}

			$result_data = array('results' => $out);

			$result_data['total_records'] = $total_records;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		public function edit_inventory()
		{
			$location_id = phpgw::get_var('location_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$inventory_id = phpgw::get_var('inventory_id', 'int');

			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);

			$this->acl_add = $this->acl->check($system_location['location'], PHPGW_ACL_ADD, $system_location['appname']);

			if (!$this->acl_add)
			{
				echo lang('No Access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			$unit_id = '';
			if ($inventory = $this->bo->get_inventory(array('id' => $id, 'location_id' => $location_id,
				'inventory_id' => $inventory_id)))
			{
				$unit_id = $inventory[0]['unit_id'];
			}

			$location_code = execMethod('property.solocation.get_location_code', $inventory[0]['p_id']);

			$lock_unit = !!$unit_id;

			$receipt = array();
			$values = phpgw::get_var('values');

			$bolocation = CreateObject('property.bolocation');
			$values['location_data'] = $bolocation->read_single($location_code, array('view' => true));


			$values['unit_id'] = $values['unit_id'] ? $values['unit_id'] : $unit_id;


			if (isset($values['save']) && $values['save'])
			{
				$values['location_id'] = $location_id;
				$values['item_id'] = $id;
				$values['inventory_id'] = $inventory_id;
				if (!isset($receipt['error']))
				{
					$this->bo->edit_inventory($values);
					$receipt['message'][] = array('msg' => 'Ok');
					$values = array();
				}


				if (phpgw::get_var('phpgw_return_as') == 'json')
				{

					if (!$receipt['error'])
					{
						$result = array
							(
							'status' => 'updated'
						);
					}
					else
					{
						$result = array
							(
							'status' => 'error'
						);
					}

					$result['receipt'] = $receipt;
					return $result;
				}
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$unit_list = execMethod('property.bogeneric.get_list', array('type' => 'unit',
				'selected' => $unit_id));

			$location_data = execMethod('property.bolocation.initiate_ui_location', array
				(
				'values' => $values['location_data'],
				'type_id' => 5,
				'no_link' => false,
				'lookup_type' => 'view',
				'tenant' => false,
				'lookup_entity' => $lookup_entity,
				'entity_data' => isset($values['p']) ? $values['p'] : ''
			));

			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data' => $location_data,
				'system_location' => $system_location,
				'location_id' => $location_id,
				'item_id' => $id,
				'inventory_id' => $inventory_id,
				'unit_list' => array('options' => $unit_list),
				'lock_unit' => $lock_unit,
				'value_inventory' => $values['inventory'] ? $values['inventory'] : $inventory[0]['inventory'],
				'value_write_off' => $values['write_off'],
				'bookable' => $values['bookable'] ? $values['bookable'] : $inventory[0]['bookable'],
				'value_active_from' => $values['active_from'] ? $values['active_from'] : $GLOBALS['phpgw']->common->show_date($inventory[0]['active_from'], $dateformat),
				'value_active_to' => $values['active_to'] ? $values['active_to'] : $GLOBALS['phpgw']->common->show_date($inventory[0]['active_to'], $dateformat),
				'value_remark' => $values['remark'] ? $values['remark'] : $inventory[0]['remark'],
			);

			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			self::add_javascript('property', 'portico', 'entity.edit_inventory.js');

			self::render_template_xsl(array('entity', 'attributes_form', 'files'), array(
				'edit_inventory' => $data));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$function_msg = lang('add inventory');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $system_location['appname'] . '::' . $system_location['descr'] . '::' . $function_msg;
		}

		public function add()
		{
			$this->edit();
		}

		public function add_inventory()
		{
			$location_id = phpgw::get_var('location_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);

			$this->acl_add = $this->acl->check($system_location['location'], PHPGW_ACL_ADD, $system_location['appname']);

			if (!$this->acl_add)
			{
				echo lang('No Access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$unit_id = '';
			if ($inventory = $this->bo->get_inventory(array('id' => $id, 'location_id' => $location_id)))
			{
				$unit_id = $inventory[0]['unit_id'];
			}

			$lock_unit = !!$unit_id;

			$receipt = array();
			$values = phpgw::get_var('values');

			$values['unit_id'] = $values['unit_id'] ? $values['unit_id'] : $unit_id;


			if (isset($values['save']) && $values['save'])
			{
				$values['location_id'] = $location_id;
				$values['item_id'] = $id;
				$insert_record = $GLOBALS['phpgw']->session->appsession('insert_record', 'property');

				if (is_array($insert_record_entity))
				{
					for ($j = 0; $j < count($insert_record_entity); $j++)
					{
						$insert_record['extra'][$insert_record_entity[$j]] = $insert_record_entity[$j];
					}
				}

				$values = $this->bocommon->collect_locationdata($values, $insert_record);

				if (!$values['location'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a location !'));
				}

				if (!$values['unit_id'])
				{
					$receipt['error'][] = array('msg' => lang('Please select a unit !'));
				}
				if (!isset($receipt['error']))
				{
					$this->bo->add_inventory($values);
					$receipt['message'][] = array('msg' => 'Ok');
					$values = array();
				}
			}

			$msgbox_data = $this->bocommon->msgbox_data($receipt);

			$unit_list = execMethod('property.bogeneric.get_list', array('type' => 'unit',
				'selected' => $unit_id));

			$location_data = execMethod('property.bolocation.initiate_ui_location', array
				(
				'values' => $values['location_data'],
				'type_id' => 5,
				'no_link' => false,
				'lookup_type' => 'form',
				'tenant' => false,
				'lookup_entity' => $lookup_entity,
				'entity_data' => isset($values['p']) ? $values['p'] : ''
			));

			$data = array
				(
				'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($msgbox_data),
				'location_data' => $location_data,
				'system_location' => $system_location,
				'location_id' => $location_id,
				'item_id' => $id,
				'unit_list' => array('options' => $unit_list),
				'lock_unit' => $lock_unit,
				'value_inventory' => $values['inventory'],
				'value_write_off' => $values['write_off'],
				'bookable' => $values['bookable'],
				'value_active_from' => $values['active_from'],
				'value_active_to' => $values['active_to'],
				'value_remark' => $values['remark']
			);

			$GLOBALS['phpgw']->jqcal->add_listener('active_from');
			$GLOBALS['phpgw']->jqcal->add_listener('active_to');

			self::render_template_xsl(array('entity', 'attributes_form', 'files', 'conditional_function'), array(
				'add_inventory' => $data));

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;

			$function_msg = lang('add inventory');

			$GLOBALS['phpgw_info']['flags']['app_header'] = $system_location['appname'] . '::' . $system_location['descr'] . '::' . $function_msg;
		}

		public function inventory_calendar()
		{
			$location_id = phpgw::get_var('location_id', 'int');
			$id = phpgw::get_var('id', 'int');
			$inventory_id = phpgw::get_var('inventory_id', 'int');

			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);

			$this->acl_add = $this->acl->check($system_location['location'], PHPGW_ACL_ADD, $system_location['appname']);

			if (!$this->acl_add)
			{
				echo lang('No Access');
				$GLOBALS['phpgw']->common->phpgw_exit();
			}
			echo "Planlagt: Visning av kalenderoppføringer for ressursen";
			$GLOBALS['phpgw']->common->phpgw_exit();
		}

//		public function get_controls_at_component( $location_id = 0, $id = 0, $skip_json = false )
//		{
//			if (!$location_id)
//			{
//				$entity_id = phpgw::get_var('entity_id', 'int');
//				$cat_id = phpgw::get_var('cat_id', 'int');
//				$type = phpgw::get_var('type', 'string', 'REQUEST', 'entity');
//
//				$location_id = $GLOBALS['phpgw']->locations->get_id($this->type_app[$type], ".{$type}.{$entity_id}.{$cat_id}");
//			}
//
//			$id = $id ? $id : phpgw::get_var('id', 'int');
//			if (!$id)
//			{
//				return array();
//			}
//
//			if (!$this->acl_read)
//			{
//				echo lang('No Access');
//				$GLOBALS['phpgw']->common->phpgw_exit();
//			}
//
//			$repeat_type_array = array
//				(
//				"0" => lang('day'),
//				"1" => lang('week'),
//				"2" => lang('month'),
//				"3" => lang('year')
//			);
//
//			$lang_history = lang('history');
//			$controls = execMethod('controller.socontrol.get_controls_at_component', array(
//				'location_id' => $location_id, 'component_id' => $id));
//			foreach ($controls as &$entry)
//			{
//				$menuaction = 'controller.uicomponent.index';
//
//				$control_link_data = array
//					(
//					'menuaction' => $menuaction,
//					'location_id' => $location_id,
//					'component_id' => $id,
//				);
//
//				$entry['title_text'] = $entry['title'];
//				$entry['title'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', $control_link_data) . '" target="_blank">' . $entry['title'] . '</a>';
//				$entry['assigned_to_name'] = "<a title=\"{$lang_history}\" onclick='javascript:showlightbox_assigned_history({$entry['serie_id']});'>{$entry['assigned_to_name']}</a>";
//
//				$entry['start_date'] = $GLOBALS['phpgw']->common->show_date($entry['start_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);
//				$entry['repeat_type'] = $repeat_type_array[$entry['repeat_type']];
//				$entry['total_time'] = $entry['service_time'] + $entry['controle_time'];
//			}
//
//			$phpgw_return_as = phpgw::get_var('phpgw_return_as');
//
//			if (($phpgw_return_as == 'json' && $skip_json) || $phpgw_return_as != 'json')
//			{
//				return $controls;
//			}
//
//			$result_data = array
//				(
//				'results' => $controls,
//				'total_records' => count($controls),
//				'draw' => phpgw::get_var('draw', 'int')
//			);
//
//			return $this->jquery_results($result_data);
//		}

		/**
		 * Get controller cases related to this item.
		 * @param integer $location_id
		 * @param integer $id
		 * @param integer $year
		 * @return string
		 */
//		public function get_cases( $location_id = 0, $id = 0, $year = 0 )
//		{
//			if (!$location_id)
//			{
//				$location_id = phpgw::get_var('location_id', 'int');
//			}
//			if (!$id)
//			{
//				$id = phpgw::get_var('id', 'int');
//			}
//			if (!$year)
//			{
//				$year = phpgw::get_var('year', 'int');
//			}
//
////			$year = $year ? $year : -1; //all
//
//			$_controls = $this->get_controls_at_component($location_id, $id, $skip_json = true);
//
//			$socase = CreateObject('controller.socase');
//			$controller_cases = $socase->get_cases_by_component($location_id, $id);
//			$_statustext = array();
//			$_statustext[0] = lang('open');
//			$_statustext[1] = lang('closed');
//			$_statustext[2] = lang('pending');
//
//			$_cases = array();
//			foreach ($controller_cases as $case)
//			{
//				$_case_year = date('Y', $case['modified_date']);
//
//				if ($_case_year != $year && $year != -1)
//				{
//					continue;
//				}
//
//				$socheck_list = CreateObject('controller.socheck_list');
//				$control_id = $socheck_list->get_single($case['check_list_id'])->get_control_id();
//				foreach ($_controls as $_control)
//				{
//					if ($_control['control_id'] == $control_id)
//					{
//						$_control_name = $_control['title_text'];
//						break;
//					}
//				}
////						_debug_array($check_list);die();
//
//				switch ($case['status'])
//				{
//					case 0:
//					case 2:
//						$_method = 'view_open_cases';
//						break;
//					case 1:
//						$_method = 'view_closed_cases';
//						break;
//					default:
//						$_method = 'view_open_cases';
//				}
//
//				$_link = $GLOBALS['phpgw']->link('/index.php', array
//					(
//					'menuaction' => "controller.uicase.{$_method}",
//					'check_list_id' => $case['check_list_id']
//					)
//				);
//
//
//				$_value_arr = array();
//
//				if($case['measurement'])
//				{
//					$_value_arr[] = $case['measurement'];
//				}
//				if($case['descr'])
//				{
//					$_value_arr[] = $case['descr'];
//				}
//
//				$_cases[] = array
//					(
//					'url' => "<a href=\"{$_link}\" > {$case['check_list_id']}</a>",
//					'type' => $_control_name,
//					'title' => "<a href=\"{$_link}\" > {$case['title']}</a>",
//					'value' => implode('</br>', $_value_arr),
//					'status' => $_statustext[$case['status']],
//					'user' => $GLOBALS['phpgw']->accounts->get($case['user_id'])->__toString(),
//					'entry_date' => $GLOBALS['phpgw']->common->show_date($case['modified_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//				);
//				unset($_link);
//			}
//
//			if (phpgw::get_var('phpgw_return_as') == 'json')
//			{
//				$result_data = array
//					(
//					'results' => $_cases,
//					'total_records' => count($_cases),
//					'draw' => phpgw::get_var('draw', 'int')
//				);
//
//				return $this->jquery_results($result_data);
//			}
//			return $_cases;
//		}

		/**
		 * Get controller cases related to this item and a spesific checklist.
		 * @return array
		 */
//		public function get_cases_for_checklist()
//		{
//			$check_list_id = phpgw::get_var('check_list_id', 'int');
//			$so_check_item = CreateObject('controller.socheck_item');
//			$controller_cases = $so_check_item->get_check_items_with_cases($check_list_id, $_type = null, 'all', null, null);
//
//			$_statustext = array();
//			$_statustext[0] = lang('open');
//			$_statustext[1] = lang('closed');
//			$_statustext[2] = lang('pending');
//
//			$_case_years = array();
//			$_cases = array();
//
//			$socheck_list = CreateObject('controller.socheck_list');
//			$socontrol = CreateObject('controller.socontrol');
//
//			foreach ($controller_cases as $check_item)
//			{
//				$checklist_id = $check_item->get_check_list_id();
//				$control_id = $socheck_list->get_single($checklist_id)->get_control_id();
//
//				$_control_name = $socontrol->get_single($control_id)->get_title();
//
//				$cases_array = $check_item->get_cases_array();
//				foreach ($cases_array as $case)
//				{
//					switch ($case->get_status())
//					{
//						case 0:
//						case 2:
//							$_method = 'view_open_cases';
//							break;
//						case 1:
//							$_method = 'view_closed_cases';
//							break;
//						default:
//							$_method = 'view_open_cases';
//					}
//
//					$_link = $GLOBALS['phpgw']->link('/index.php', array
//						(
//						'menuaction' => "controller.uicase.{$_method}",
//						'check_list_id' => $check_list_id
//						)
//					);
//					$_value_arr = array();
//
//					if($case->get_measurement())
//					{
//						$_value_arr[] = $case->get_measurement();
//					}
//					if($case->get_descr())
//					{
//						$_value_arr[] = $case->get_descr();
//					}
//
//					$_cases[] = array
//						(
//						'url' => "<a href=\"{$_link}\" > {$check_list_id}</a>",
//						'type' => $_control_name,
//						'title' => "<a href=\"{$_link}\" >" . $check_item->get_control_item()->get_title() . "</a>",
//						'value' => implode('</br>', $_value_arr),
//						'status' => $_statustext[$case->get_status()],
//						'user' => $GLOBALS['phpgw']->accounts->get($case->get_user_id())->__toString(),
//						'entry_date' => $GLOBALS['phpgw']->common->show_date($case->get_modified_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//					);
//					unset($_link);
//				}
//			}
//
//			if (phpgw::get_var('phpgw_return_as') == 'json')
//			{
//				$result_data = array
//					(
//					'results' => $_cases,
//					'total_records' => count($_cases),
//					'draw' => phpgw::get_var('draw', 'int')
//				);
//
//				return $this->jquery_results($result_data);
//			}
//			return $_cases;
//		}

		/**
		 * Get controller checklists related to this item.
		 * @param integer $location_id
		 * @param integer $id
		 * @param integer $year
		 * @return string
		 */
//		public function get_checklists( $location_id = 0, $id = 0, $year = 0 )
//		{
//			if (!$location_id)
//			{
//				$location_id = phpgw::get_var('location_id', 'int');
//			}
//			if (!$id)
//			{
//				$id = phpgw::get_var('id', 'int');
//			}
//			if (!$year)
//			{
//				$year = phpgw::get_var('year', 'int', 'REQUEST', date('Y'));
//			}
//			$socheck_list = CreateObject('controller.socheck_list');
//
//			$start_and_end = $socheck_list->get_start_and_end_for_component($location_id, $id);
//			$start_year = date('Y', $start_and_end['start_timestamp']);
//			$end_year = date('Y', $start_and_end['end_timestamp']);
//			if (!$year)
//			{
//				$year = $end_year;
//			}
//
//			for ($j = $start_year; $j < ($end_year + 1); $j++)
//			{
//				$this->check_lst_time_span[] = array(
//					'id' => $j,
//					'name' => $j,
//					'selected' => $j == date('Y') ? 1 : 0
//				);
//			}
//
//			$from_date_ts = mktime(0, 0, 0, 1, 1, $year);
//			$to_date_ts = mktime(23, 59, 59, 12, 31, $year);
//			$socontrol = CreateObject('controller.socontrol');
//
//			$control_id_with_check_list_array = $socheck_list->get_check_lists_for_component($location_id, $id, $from_date_ts, $to_date_ts);
//
//			$_statustext = array();
//			$_statustext[0] = lang('open');
//			$_statustext[1] = lang('closed');
//			$_statustext[2] = lang('pending');
//			$_check_list = array();
//			foreach ($control_id_with_check_list_array as $control)
//			{
//				$_control_name = $socontrol->get_single($control->get_id())->get_title();
//				$check_lists = $control->get_check_lists_array();
//
//				foreach ($check_lists as $check_list)
//				{
//					$_link = self::link(array(
//							'menuaction' => "controller.uicheck_list.edit_check_list",
//							'check_list_id' => $check_list->get_id()
//							)
//					);
//					$_check_list[] = array
//						(
//						'id' => $check_list->get_id(),
//						'control_name' => "<a href=\"{$_link}\" >{$_control_name}</a>",
//						'status' => $_statustext[$check_list->get_status()],
//						'user' => $GLOBALS['phpgw']->accounts->get($check_list->get_assigned_to())->__toString(),
//						'deadline' => $GLOBALS['phpgw']->common->show_date($check_list->get_deadline(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//						'planned_date' => $GLOBALS['phpgw']->common->show_date($check_list->get_planned_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//						'completed_date' => $GLOBALS['phpgw']->common->show_date($check_list->get_completed_date(), $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
//						'num_open_cases' => $check_list->get_num_open_cases(),
//						'num_pending_cases' => $check_list->get_num_pending_cases(),
//					);
//					unset($_link);
//				}
//			}
//
//			if (phpgw::get_var('phpgw_return_as') == 'json')
//			{
//				$result_data = array
//					(
//					'results' => $_check_list,
//					'total_records' => count($_check_list),
//					'draw' => phpgw::get_var('draw', 'int')
//				);
//
//				return $this->jquery_results($result_data);
//			}
//			return $_check_list;
//		}

		public function get_controls_at_component( $location_id = 0, $id = 0, $skip_json = false )
		{
			return $this->controller_helper->get_controls_at_component($location_id, $id, $skip_json);
		}

		public function get_cases( $location_id = 0, $id = 0, $year = 0 )
		{
			return $this->controller_helper->get_cases($location_id, $id, $year);
		}

		public function get_cases_for_checklist()
		{
			return $this->controller_helper->get_cases_for_checklist();
		}

		public function get_checklists( $location_id = 0, $id = 0, $year = 0 )
		{
			return $this->controller_helper->get_checklists($location_id, $id, $year);

		}

		function get_assigned_history()
		{
			return $this->controller_helper->get_assigned_history();
		}

	}