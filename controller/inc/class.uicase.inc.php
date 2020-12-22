<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage controller
	 * @version $Id$
	 */
	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon_jquery');
	phpgw::import_class('controller.socase');
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('controller.socheck_item');
	phpgw::import_class('controller.socontrol');
	phpgw::import_class('controller.socontrol_group_list');
	phpgw::import_class('controller.socontrol_item_list');

	include_class('controller', 'check_item_case', 'inc/model/');
	include_class('controller', 'component', 'inc/model/');
	include_class('controller', 'location_finder', 'inc/helper/');

	class controller_uicase extends phpgwapi_uicommon_jquery
	{

		private $so;
		private $so_control;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;
		private $so_control_group_list;
		private $so_control_group;
		private $so_control_item_list;
		private $so_check_list;
		private $location_finder;
		private $read;
		private $add;
		private $edit;
		private $delete;
		private $vfs;
		var $public_functions = array
			(
			'add_case' => true,
			'save_case' => true,
			'save_case_ajax' => true,
			'create_case_message' => true,
			'view_case_message' => true,
			'send_case_message' => true,
			'updateStatusForCases' => true,
			'delete_case' => true,
			'close_case' => true,
			'open_case' => true,
			'view_open_cases' => true,
			'view_closed_cases' => true,
			'get_case_data_ajax' => true,
			'get_image'			=> true,
			'get_case_image'	=> true,
			'add_component_image'=> true,
			'add_case_image'	=> true,
			'edit_component_child' => true,
			'edit_parent_component'	=> true,
			'add_regulation_option'	=> true
		);

		function __construct()
		{
			parent::__construct();

			$this->so = CreateObject('controller.socase');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_item = CreateObject('controller.socheck_item');
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_check_list = CreateObject('controller.socheck_list');

			$this->location_finder = new location_finder();

			$this->read = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller');//1
			$this->add = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');//2
			$this->edit = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller');//4
			$this->delete = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller');//8
			if (phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				phpgwapi_cache::session_set('controller', 'noframework', true);
			}
			else if (phpgwapi_cache::session_get('controller', 'noframework'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
			$this->vfs = CreateObject('phpgwapi.vfs');

//			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

		}

		function add_regulation_option()
		{
			if(!$this->edit)
			{
				phpgw::no_access();
			}

			$control_item_id	 = phpgw::get_var('control_item_id', 'int');
			$new_value		 = phpgw::get_var('new_value');

			$id = createObject('controller.socontrol_item')->add_regulation_reference_options($control_item_id, $new_value);

			$receipt = array(
				'status' => 'ok',
				'choice_id' => 100
			);

			return $receipt;

		}

		function edit_parent_component()
		{
			return $this->edit_component_child(true);
		}

		function edit_component_child($edit_parent = false)
		{
			if(!$this->read )
			{
				phpgw::no_access();
			}

			$get_form = phpgw::get_var('get_form', 'bool');
			$get_edit_form = phpgw::get_var('get_edit_form', 'bool');
			$get_info = phpgw::get_var('get_info', 'bool');
			$enable_add_case = phpgw::get_var('enable_add_case', 'bool');
			$parent_location_id = phpgw::get_var('parent_location_id', 'int');
			$parent_component_id = phpgw::get_var('parent_component_id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$component_id = phpgw::get_var('component_id', 'int');

			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);
			$system_location_arr = explode('.', $system_location['location']);
			$entity_id		= 	$system_location_arr[2];
			$cat_id			= 	$system_location_arr[3];

			$values = array();
			$custom = createObject('property.custom_fields');
			$values['attributes'] = $custom->find('property', $system_location['location'], 0, '', 'ASC', 'attrib_sort', true, true);

			if (isset($values['attributes']) && is_array($values['attributes']))
			{
				foreach ($values['attributes'] as & $attribute)
				{
					if ($attribute['history'] == true)
					{
						$link_history_data = array
							(
							'menuaction'	 => 'property.uientity.attrib_history',
							'acl_location'	 => $system_location['location'],
							'attrib_id'		 => $attribute['id'],
							'id'			 => $component_id,
							'edit'			 => true,
							'type'			 => $this->type
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
			}
			if($get_form || $get_edit_form || $get_info)
			{
				$soentity = CreateObject('property.soentity', $entity_id, $cat_id);

				if($component_id)
				{
					$values = $soentity->read_single(array('location_id' => $location_id, 'id' => $component_id), $values);
				}

				$values = $custom->prepare($values, 'property', $system_location['location'], false);

				$lookup_functions = $values['lookup_functions'];

				$menuaction = $edit_parent ? 'controller.uicase.edit_parent_component' : 'controller.uicase.edit_component_child';

				$attributes_groups	 = $custom->get_attribute_groups('property', ".entity.{$entity_id}.{$cat_id}", $values['attributes']);
				$attributes_general	 = array();
				$i					 = -1;
				$attributes			 = array();

				foreach ($attributes_groups as $_key => $group)
				{
					if (!isset($group['attributes']))
					{
						$group['attributes'] = array(array());
					}

					$i ++;
					$attributes[$i]['attributes'][]	 = array
						(
						'datatype'	 => 'section',
						'descr'		 => '<H' . (int)($group['level'] + 1) . "> {$group['descr']} </H" . ($group['level'] + 1) . '>',
						'level'		 => $group['level'],
					);
					$attributes[$i]['attributes']	 = array_merge($attributes[$i]['attributes'], $group['attributes']);
					
				}
				unset($attributes_groups);
				
				$open_cases = array();
				if($get_info)
				{
					$open_cases = $this->so->get_open_cases_by_component_child($location_id, $component_id);
					
					foreach ($open_cases as &$open_case)
					{
						$open_case['modified_date_text'] = $GLOBALS['phpgw']->common->show_date($open_case['modified_date'], $this->dateFormat);					
						$open_case['open_case_url'] = self::link(array('menuaction' => 'controller.uicase.view_open_cases', 'check_list_id'=> $open_case['check_list_id']));
					}
					
				}

				$data = array(
					'action'=> self::link(array('menuaction' => $menuaction, 'phpgw_return_as'=>'json')),
					'edit_parent' => $edit_parent,
					'parent_location_id' => $parent_location_id,
					'parent_component_id' => $parent_component_id,
					'location_id' => $location_id,
					'component_id'=> $component_id,
//					'attributes_general' => array('attributes' => $values['attributes']),
					'attributes_group'				 => $attributes,
					'attributes_general' => array('attributes' => $attributes_general),
					'get_form'	=> $get_form,
					'get_info'	=> $get_info,
					'enable_add_case' => $enable_add_case,
					'get_edit_form' => $get_edit_form,
					'template_set' => 'bootstrap',
					'supress_history_date' => true,
					'open_cases' => $open_cases
					);

				$xslttemplates = CreateObject('phpgwapi.xslttemplates');

				$xslttemplates->add_file(array(PHPGW_SERVER_ROOT . '/controller/templates/base/new_component'));
				if($get_form || $get_edit_form)
				{
					if(!$this->edit)
					{
						return array(
							'html'				 => '<h2>'. lang('No access') . '</h2>',
							'lookup_functions'	 => ''
							);
							phpgw::no_access();
					}
					
					$xslttemplates->add_file(array(PHPGW_SERVER_ROOT . '/controller/templates/base/attributes_form'));
				}
				else
				{
					$xslttemplates->add_file(array(PHPGW_SERVER_ROOT . '/controller/templates/base/attributes_view'));
				}

				$xslttemplates->set_var('phpgw', array('new_component' => $data));


				$xslttemplates->set_output('html');
				$xslttemplates->xsl_parse();
				$xslttemplates->xml_parse();

				$xml = new DOMDocument;
				$xml->loadXML($xslttemplates->xmldata);
				$xsl = new DOMDocument;
				$xsl->loadXML($xslttemplates->xsldata);

				// Configure the transformer
				$proc = new XSLTProcessor;
				$proc->registerPHPFunctions(); // enable php functions
				$proc->importStyleSheet($xsl); // attach the xsl rules


				$html = trim($proc->transformToXML($xml));
				$html = preg_replace('/<\?xml version([^>])+>/', '', $html);
				$html = preg_replace('/<!DOCTYPE([^>])+>/', '', $html);

				return array(
					'html'				 => $html,
					'lookup_functions'	 => $lookup_functions
					);
			}

			if($_POST && !$parent_location_id && !$parent_component_id && $component_id)
			{
				$soentity = CreateObject('property.soentity', $entity_id, $cat_id);
				$item = $soentity->read_single(array('location_id' => $location_id, 'id' => $component_id));
				$parent_component_id = (int)$values['p_num'];
				$parent_location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.{$values['p_entity_id']}.{$values['p_cat_id']}");
			}

			if($_POST && ( ($parent_location_id && $parent_component_id) || $edit_parent) )
			{
				$values = array();

				$parent_system_location = $GLOBALS['phpgw']->locations->get_name($parent_location_id);

				$parent_system_location_arr = explode('.', $parent_system_location['location']);
				$p_entity_id	= 	$parent_system_location_arr[2];
				$p_cat_id		= 	$parent_system_location_arr[3];

				$soentity = CreateObject('property.soentity', $entity_id, $cat_id);

				if($parent_location_id)
				{
					$parent_item = $soentity->read_single(array('location_id' => $parent_location_id, 'id' => $parent_component_id));
				}
				else//self
				{
					$parent_item = $soentity->read_single(array('location_id' => $location_id, 'id' => $component_id));
				}

				$values['extra']['p_entity_id'] = $p_entity_id;
				$values['extra']['p_cat_id'] = $p_cat_id;
				$values['extra']['p_num'] = $parent_component_id;

				$values['location_code'] = $parent_item['location_code'];

				$location_data = createObject('property.solocation')->get_location_data($parent_item['location_code']);

				$values = array_merge($values, $location_data);

				$_values_attribute = (array)phpgw::get_var('values_attribute');

				foreach ($_values_attribute as $attribute_id => &$attribute)
				{
					$_value = phpgw::get_var($attribute['name']);

					if($_value)
					{
						$attribute['value'] = $_value;

						$values['extra'][$attribute['name']] = $_value;
					}
				}
				unset($_value);

				$values_attribute = $custom->convert_attribute_save($_values_attribute);

				if($component_id)
				{
					$values['id'] = $component_id;
					$receipt = $soentity->edit($values, $values_attribute, $entity_id, $cat_id);
				}
				else
				{
					$receipt = $soentity->add($values, $values_attribute, $entity_id, $cat_id);
				}

				if($receipt['id'])
				{
					$property_soadmin_entity = createObject('property.soadmin_entity');
					$location_children = $property_soadmin_entity->get_children( $p_entity_id, $p_cat_id, 0, '' );

					$component_children = array();
					foreach ($location_children as $key => &$location_children_info)
					{
						$location_children_info['parent_location_id'] = $parent_location_id;
						$location_children_info['parent_component_id'] = $parent_component_id;

						$_component_children = $soentity->get_eav_list(array
						(
							'location_id' => $location_children_info['location_id'],
							'parent_location_id' => $parent_location_id,
							'parent_id' => $parent_component_id,
							'allrows'	=> true
						));

						$component_children = array_merge($component_children, $_component_children);
					}

					if($component_children)
					{
						$sort_key_location = array();
						$short_description = array();
						foreach ($component_children as &$_value)
						{
							$sort_key_location[] = $_value['location_id'];
							$short_description[] = $_value['short_description'];
							
							$_value['selected'] = ($receipt['id'] == $_value['id'] && $location_id == $_value['location_id']) ? 1 : 0;
						}

						array_multisort($sort_key_location, SORT_ASC, $short_description, SORT_ASC, $component_children);


						array_unshift($component_children, array('id' => '', 'short_description' => lang('select')));
					}

					return array(
						'status' => 'saved',
						'message' => lang('saved'),
						'component_children' => $component_children
						);
				}
				else
				{
					return array(
						'status' => 'saved',
						'message' => lang('error')
						);

				}
			}

			return lang('error');

		}

		function add_case_image()
		{
			if(!$this->edit )
			{
				phpgw::no_access();
			}

			$case_id = phpgw::get_var('case_id', 'int');

			if (isset($_FILES['file']['name']) && $_FILES['file']['name'])
			{
				$bofiles = CreateObject('property.bofiles', '/controller/case');

				if ($bofiles->is_image($_FILES['file']['tmp_name']))
				{
					$bofiles->resize_image($_FILES['file']['tmp_name'], $_FILES['file']['tmp_name'], $thumb_size = 800);
				}

				$file_name = str_replace(' ', '_', $_FILES['file']['name']);

				$to_file = "{$bofiles->fakebase}/{$case_id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array
						(
						'string' => $to_file,
						'relatives' => Array(RELATIVE_NONE)
					)))
				{
					return array(
						'status' => 'error',
						'message' => lang('This file already exists !')
						);
				}
				else
				{
					$from_file = $_FILES['file']['tmp_name'];
					$bofiles->create_document_dir("$case_id");
					$bofiles->vfs->override_acl = 1;

					if ($bofiles->vfs->cp(array(
							'from' => $from_file,
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						$bofiles->vfs->override_acl = 0;
						return array(
							'status' => 'saved',
							'message' => lang('saved')
							);
					}
				}

				return array(
					'status' => 'error',
					'message' => lang('Failed to upload file !')
					);

			}
		}

		function add_component_image()
		{
			if(!$this->edit )
			{
				phpgw::no_access();
			}

			$component_arr = explode("_", phpgw::get_var('component'));

			$location_id = $component_arr[0];
			$id	 = $component_arr[1];
			if (empty($id))
			{
				throw new Exception('controller_uicase::_add_component_image() - missing id');
			}

			$soentity = createObject('property.soentity');

			$location_code = $soentity->get_location_code($location_id, $id);
			$location_arr = explode('-', $location_code);
			$loc1 = !empty($location_arr[0]) ? $location_arr[0] : 'dummy';

			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);
			$system_location_arr = explode('.', $system_location['location']);
			$category_dir = "{$system_location_arr[1]}_{$system_location_arr[2]}_{$system_location_arr[3]}";

			$bofiles = CreateObject('property.bofiles');

//			if (isset($values['file_action']) && is_array($values['file_action']))
//			{
//				$bofiles->delete_file("/{$category_dir}/{$loc1}/{$id}/" ,$values);
//			}

			$files = array();
			if (isset($_FILES['file']['name']) && $_FILES['file']['name'])
			{
				if ($bofiles->is_image($_FILES['file']['tmp_name']))
				{
					$bofiles->resize_image($_FILES['file']['tmp_name'], $_FILES['file']['tmp_name'], $thumb_size = 800);
				}

				$file_name = str_replace(' ', '_', $_FILES['file']['name']);
				$to_file = "{$bofiles->fakebase}/{$category_dir}/{$loc1}/{$id}/{$file_name}";
				
				$pathinfo = pathinfo($to_file);
				
				$actual_name	 = "{$pathinfo['dirname']}/{$pathinfo['filename']}";
				$original_name	 = $actual_name;
				$extension		 = $pathinfo['extension'];

				$i = 1;
				while ( file_exists("{$bofiles->rootdir}/{$actual_name}.{$extension}"))
				{
					$actual_name = (string) $original_name . $i;
					$to_file	 = "{$actual_name}.{$extension}";
					$i++;
				}

				$from_file = $_FILES['file']['tmp_name'];
				$bofiles->create_document_dir("{$category_dir}/{$loc1}/{$id}");
				$bofiles->vfs->override_acl = 1;

				if ($bofiles->vfs->cp(array(
						'from' => $from_file,
						'to' => $to_file,
						'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
				{
					$status = 'saved';
					$message = lang('saved');
				}
				else
				{
					$status = 'error';
					$message = lang('Failed to upload file !');
				
				}			
				$bofiles->vfs->override_acl = 0;

				return array(
					'status' => $status,
					'message' => $message
				);

			}
		}

		function get_case_image()
		{
			if(!$this->read )
			{
				phpgw::no_access();
			}

			$dry_run = phpgw::get_var('dry_run', 'bool');
			$case_id = phpgw::get_var('case_id', 'int');

			$this->vfs->override_acl = 1;

			$files = $this->vfs->ls(array(
				'orderby' => 'file_id',
	//			'mime_type'	=> 'image/jpeg',
				'string' => "/controller/case/{$case_id}",
				'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

			$file = end($files);

			if($dry_run)
			{
				if(!empty($file['file_id']))
				{
					return array(
						'status' => '200',
						'message' => lang('file found'),
						'file_id' => $file['file_id']
						);
					}
				else
				{
					return array(
						'status' => '404',
						'message' => lang('file not found')
						);
				}
			}

			if(!empty($file['file_id']))
			{
				ExecMethod('property.bofiles.get_file', $file['file_id']);
			}
		}

		function get_case_images($case_id)
		{
			$this->vfs->override_acl = 1;

			$files = $this->vfs->ls(array(
				'orderby' => 'file_id',
				'string' => "/controller/case/{$case_id}",
				'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;
			return $files;
		}

		function get_image()
		{
			if(!$this->read )
			{
				phpgw::no_access();
			}

			$file_id = phpgw::get_var('file_id', 'int');

			if($file_id)
			{
				ExecMethod('property.bofiles.get_file', $file_id);
				return;
			}

			$dry_run = phpgw::get_var('dry_run', 'bool');
			$component_arr = explode("_", phpgw::get_var('component'));

			$location_id = $component_arr[0];
			$item_id	 = $component_arr[1];

			$soentity = createObject('property.soentity');

			$location_code = $soentity->get_location_code($location_id, $item_id);
			$location_arr = explode('-', $location_code);
			$loc1 = !empty($location_arr[0]) ? $location_arr[0] : 'dummy';

			$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);
			$system_location_arr = explode('.', $system_location['location']);
			$category_dir = "{$system_location_arr[1]}_{$system_location_arr[2]}_{$system_location_arr[3]}";

			$this->vfs->override_acl = 1;

			$files = $this->vfs->ls(array(
				'orderby' => 'file_id',
				'mime_type'	=> 'image/jpeg',
				'string' => "/property/{$category_dir}/{$loc1}/{$item_id}",
				'relatives' => array(RELATIVE_NONE)));

			$this->vfs->override_acl = 0;

			$file = end($files);

			if($dry_run)
			{
				if(!empty($file['file_id']))
				{
					return array(
						'status' => '200',
						'message' => lang('file found')
						);
					}
				else
				{
					return array(
						'status' => '404',
						'message' => lang('file not found')
						);
				}
			}

			if(!empty($file['file_id']))
			{
				ExecMethod('property.bofiles.get_file', $file['file_id']);
			}
		}


		private function _get_case_data()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$case_location_code = phpgw::get_var('location_code');
			$check_list = $this->so_check_list->get_single($check_list_id);

			$repeat_descr = '';
			if ($serie = $this->so_control->get_serie($check_list->get_serie_id()));
			{
				$repeat_type_array = array
					(
					"0" => lang('day'),
					"1" => lang('week'),
					"2" => lang('month'),
					"3" => lang('year')
				);
				if($serie['repeat_type'] == 3)
				{
					$repeat_descr = 'Årskontroll';
				}
				else
				{
					$repeat_descr = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
				}
			}

			$last_completed_checklist = $this->so_check_item->get_last_completed_checklist($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());

			if ($repeat_descr)
			{
				$repeat_descr .= " :: " . $control->get_title();
				$control->set_title($repeat_descr);
			}

			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$control_groups_with_items_array = array();

			$component_id = $check_list->get_component_id();
			$get_locations = false;

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));

					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));
				}
				$component = new controller_component();
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
				$buildings_on_property = array();

				$system_location_arr = explode('.', $location_info['location']);

				$property_soadmin_entity = createObject('property.soadmin_entity');
				$location_children = $property_soadmin_entity->get_children( $system_location_arr[2], $system_location_arr[3], 0, '' );
				$property_soentity = createObject('property.soentity');

				$component_children = array();
				foreach ($location_children as $key => &$location_children_info)
				{
					$location_children_info['parent_location_id'] = $location_id;
					$location_children_info['parent_component_id'] = $component_id;

					$_component_children = $property_soentity->get_eav_list(array
					(
						'location_id' => $location_children_info['location_id'],
						'parent_location_id' => $location_id,
						'parent_id' => $component_id,
						'allrows'	=> true
					));

					$component_children = array_merge($component_children, $_component_children);
				}

				if($location_children)
				{
					$sort_key_location = array();
					$short_description = array();
					foreach ($component_children as $_value)
					{
						$sort_key_location[] = $_value['location_id'];
						$short_description[] = $_value['short_description'];
					}

					array_multisort($sort_key_location, SORT_ASC, $short_description, SORT_ASC, $component_children);
					array_unshift($component_children, array('id' => '', 'short_description' => lang('select')));
				}

			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_code_search_components = $case_location_code ? $case_location_code : $location_code;
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				// Fetches buildings on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
				foreach ($buildings_on_property as &$building)
				{
					$building['selected'] = $building['id'] == $case_location_code ? 1 : 0;
				}

				$location_code = $location_code_search_components;
			}


			//------------- START find already registered cases -------------//

			$cases_at_component_group = array();
			$existing_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, $_type = null, 'all', null, null);//$location_code_search_components);

			foreach ($existing_check_items_and_cases as $check_item)
			{
				foreach ($check_item->get_cases_array() as $case)
				{
					$_component_location_id = $case->get_component_location_id();
					$_component_id = $case->get_component_id();
					if ($_component_id)
					{
						$cases_at_component_group[$check_item->get_control_item()->get_control_group_id()][$_component_location_id][$_component_id] ++;
					}
					unset($_component_location_id);
					unset($_ocation_id);
				}
			}

			//------------- END find already registered cases -------------//
			//Populating array with saved control items for each group
			//Cache result
			$components_at_location = array();
			$org_units_data = array();
			$sogeneric = CreateObject('property.sogeneric');
			$sogeneric->get_location_info('org_unit');

			foreach ($saved_control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_and_options_by_control_and_group($control->get_id(), $control_group->get_id(), "return_array");

				if (count($saved_control_items) > 0)
				{
					$component_location_id = $control_group->get_component_location_id();

					if ($component_location_id && $type == 'location')
					{
						//--- start components -------------//
						$criterias_array = array();
						//	$loc_arr = $GLOBALS['phpgw']->locations->get_name($component_location_id);
						$criterias_array['location_id'] = $component_location_id;
						$criterias_array['location_code'] = $location_code_search_components;
						$criterias_array['allrows'] = true;

						$component_criteria = $control_group->get_component_criteria();
						$conditions = array();
						foreach ($component_criteria as $attribute_id => $condition)
						{
							if ($condition['value'])
							{
								eval('$condition_value = ' . "{$condition['value']};");
								$conditions[] = array
									(
									'attribute_id' => $attribute_id,
									'operator' => $condition['operator'],
									'value' => $condition_value
								);
							}
						}

						$criterias_array['conditions'] = $conditions;

						if (!isset($components_at_location[$component_location_id][$location_code_search_components]) || !$_components_at_location = $components_at_location[$component_location_id][$location_code_search_components])
						{
							$_components_at_location = execMethod('property.soentity.get_eav_list', $criterias_array);


							$components_at_location[$component_location_id][$location_code_search_components] = $_components_at_location;
						}

						//--- end components -------------//

						if ($_components_at_location)
						{
							foreach ($_components_at_location as &$_component_at_location)
							{

								/**
								 * Add org unit to short description
								 */
								if (isset($_component_at_location['org_unit_id']) && $_component_at_location['org_unit_id'])
								{
									if (!isset($org_units_data[$_component_at_location['org_unit_id']]))
									{
										$org_unit = $sogeneric->read_single(array('id' => $_component_at_location['org_unit_id']));
										$org_units_data[$_component_at_location['org_unit_id']]['name'] = $org_unit['name'];
									}
									$_component_at_location['short_description'] .= " [{$org_units_data[$_component_at_location['org_unit_id']]['name']}]";
								}

								if (isset($cases_at_component_group[$control_group->get_id()][$_component_at_location['location_id']][$_component_at_location['id']]))
								{
									$_component_at_location['short_description'] .= ' (' . $cases_at_component_group[$control_group->get_id()][$_component_at_location['location_id']][$_component_at_location['id']] . ')';
								}
							}

							array_unshift($_components_at_location, array('id' => '', 'short_description' => lang('select')));

							$control_groups_with_items_array[] = array
								(
								'control_group' => $control_group->toArray(),
								'control_items' => $saved_control_items,
								'components_at_location' => array('component_options' => $_components_at_location)
							);
						}
					}
					else
					{
						$control_groups_with_items_array[] = array
							(
							'control_group' => $control_group->toArray(),
							'control_items' => $saved_control_items
						);
					}
				}
			}

			$data = array
			(
				'control' => $control,
				'check_list' => $check_list,
				'last_completed_checklist'	=> $last_completed_checklist,
				'buildings_on_property' => $buildings_on_property,
				'component_children'	=> $component_children,
				'location_children'	=> $location_children,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'control_groups_with_items_array' => $control_groups_with_items_array,
				'type' => $type,
				'location_code' => $location_code,
				'get_locations'	=> $get_locations
			);

			return $data;
		}

		function add_case()
		{
			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$mandatory_location = $config->config_data['control_mandatory_location'];
			if ($mandatory_location && $mandatory_location == 1)
			{
				$mandatory_location = true;
			}
			else
			{
				$mandatory_location = false;
			}
			$case_data = $this->_get_case_data();

//			_debug_array($case_data['check_list']->get_id());
//			_debug_array($case_data['component_children']);

			$_component_children = (array)$case_data['component_children'];
			$completed_items = $this->so_check_list->get_completed_item($case_data['check_list']->get_id());

			$component_children = array();
			$completed_list = array();
			foreach ($_component_children as &$component_child)
			{
				if(!empty($completed_items[$component_child['location_id']][$component_child['id']]))
				{
					$component_child['completed_id'] = $completed_items[$component_child['location_id']][$component_child['id']]['completed_id'];
					$completed_list[]= $component_child;
				}
				else
				{
					$component_children[]= $component_child;
				}
			}


			$building_children = array();
			$buildings_on_property = (array)$case_data['buildings_on_property'];
			foreach ($buildings_on_property as &$building_on_property)
			{
				if(!empty($completed_items[$building_on_property['location_id']][$building_on_property['item_id']]))
				{
					$building_on_property['completed_id'] = $completed_items[$building_on_property['location_id']][$building_on_property['item_id']]['completed_id'];
					$completed_list[]= $building_on_property;
				}
				else
				{
					$building_children[]= $building_on_property;
				}
			}
//			_debug_array($buildings_on_property);
//			_debug_array($component_children);
//			_debug_array($completed_list);
//			_debug_array($completed_list);


			$check_list = $case_data['check_list'];

			$last_completed_checklist = $case_data['last_completed_checklist'];

			$last_completed_checklist_date = !empty($last_completed_checklist['completed_date']) ? $GLOBALS['phpgw']->common->show_date($last_completed_checklist['completed_date'], $this->dateFormat) : '';

			$level = $this->location_finder->get_location_level($case_data['location_code']);
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$user_role = true;
//https://www.iconsdb.com/black-icons/undo-4-icon.html

			$inspectors = createObject('controller.sosettings')->get_inspectors($check_list->get_id());

			$administrator_arr = array();
			$administrators = createObject('controller.sosettings')->get_user_with_role($check_list->get_control_id(), $check_list->get_location_code(), 1);

			foreach ($administrators as $administrator)
			{
				$administrator_arr[] = $administrator['name'];
			}

			$supervisor_arr = array();
			$supervisors = createObject('controller.sosettings')->get_user_with_role($check_list->get_control_id(), $check_list->get_location_code(), 4);

			foreach ($supervisors as $supervisor)
			{
				$supervisor_arr[] = $supervisor['name'];
			}
//			_debug_array($check_list);
//			_debug_array($component_children);
			$data = array
			(
				'inspectors' => $inspectors,
				'administrator_list' => implode('; ', $administrator_arr),
				'supervisor_name' => implode('; ', $supervisor_arr),
				'img_add2' => $GLOBALS['phpgw']->common->image('phpgwapi', 'add2'),
				'img_undo' => $GLOBALS['phpgw']->common->image('phpgwapi', 'undo-4-512'),
				'img_green_check' => $GLOBALS['phpgw']->common->image('phpgwapi', 'green-check'),
				'control' => $case_data['control'],
				'check_list' => $check_list,
				'last_completed_checklist_date'	=> $last_completed_checklist_date,
				'buildings_on_property' => $building_children,//$case_data['buildings_on_property'],
				'location_array' => $case_data['location_array'],
				'component_array' => $case_data['component_array'],
				'component_children' => $component_children,
				'completed_list'	=> $completed_list,
				'location_children' => $case_data['location_children'],
				'control_groups_with_items_array' => $case_data['control_groups_with_items_array'],
				'type' => $case_data['type'],
				'location_level' => $level,
				'current_year' => $year,
				'current_month_nr' => $month,
				'mandatory_location' => $mandatory_location,
				'location_required' => $mandatory_location,
				'cases_view' => 'add_case',
				'get_locations'	=> $case_data['get_locations'],
				'degree_list' => array('options' => createObject('property.borequest')->select_degree_list( $degree_value = 2 )),
				'consequence_list' => array('options' => createObject('property.borequest')->select_consequence_list( $consequence_value = 2 )),
				'case_location_code' => $case_data['location_code'],
				'add_img' => $GLOBALS['phpgw']->common->image('phpgwapi', 'add2'),
				'delete_img' => $GLOBALS['phpgw']->common->image('phpgwapi', 'recycle-bin-line')

			);
//			_debug_array($data);die();
			phpgwapi_jquery::load_widget('core');

			
			$js = <<<JS
				var enable_add_case = true;

JS;
			$GLOBALS['phpgw']->js->add_code('', $js);

			
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'custom_ui.js');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::add_javascript('controller', 'base', 'case.js');
			self::add_javascript('controller', 'base', 'check_list.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');
			phpgwapi_jquery::formvalidator_generate(array('location','date', 'security', 'file'));

			self::add_javascript('phpgwapi', 'alertify', 'alertify.min.js');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('controller') . '::' . $check_list->get_id();
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = 'controller::add_case' . '::' . $check_list->get_id();
			self::set_active_menu('controller::add_case');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'case/add_case',
				'check_list/fragments/select_buildings_on_property',
				'check_list/fragments/check_list_change_status',
				'check_list/fragments/select_component_children'), $data);
		}

		public function get_case_data_ajax()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$case_location_code = phpgw::get_var('location_code');
			$case_data = $this->_get_case_data();

			return array("control_groups_with_items_array" => $case_data['control_groups_with_items_array']);
		}

		function save_case_ajax()
		{
			if (!$this->add && !$this->edit)
			{
				return json_encode(array("status" => "not_saved"));
			}

			$check_list_id = phpgw::get_var('check_list_id');
			$control_item_id = phpgw::get_var('control_item_id');
			$case_descr = phpgw::get_var('case_descr');
			$proposed_counter_measure =  phpgw::get_var('proposed_counter_measure');
			$type = phpgw::get_var('type');
			$status = phpgw::get_var('status');
			$location_code = phpgw::get_var('location_code');
			$component_location_id = phpgw::get_var('component_location_id', 'int');
			$component_id = phpgw::get_var('component_id', 'int');


			$component_child =  phpgw::get_var('component_child');
			$condition_degree =  phpgw::get_var('condition_degree');
			$consequence =  phpgw::get_var('consequence');

			if($component_child)
			{
				$component_child_arr = explode('_', $component_child);
				$component_child_location_id = $component_child_arr[0];
				$component_child_item_id = $component_child_arr[1];
			}
			else
			{
				$component_child_location_id = null;
				$component_child_item_id = null;
			}

			$check_list = $this->so_check_list->get_single($check_list_id);

			$control_id = $check_list->get_control_id();

			$location_code = $location_code ? $location_code : $check_list->get_location_code();
			$control = $this->so_control->get_single($control_id);

			$check_item = $this->so_check_item->get_check_item_by_check_list_and_control_item($check_list_id, $control_item_id);

			// Makes a check item if there isn't already made one
			if ($check_item == null)
			{
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id($check_list_id);
				$new_check_item->set_control_item_id($control_item_id);

				$saved_check_item_id = $this->so_check_item->store($new_check_item);
				$check_item = $this->so_check_item->get_single($saved_check_item_id);
			}

			$todays_date_ts = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

			$user_id = $GLOBALS['phpgw_info']['user']['id'];

			$case = new controller_check_item_case();
			$case->set_check_item_id($check_item->get_id());
			$case->set_descr($case_descr);
			$case->set_proposed_counter_measure($proposed_counter_measure);
			$case->set_user_id($user_id);
			$case->set_entry_date($todays_date_ts);
			$case->set_modified_date($todays_date_ts);
			$case->set_modified_by($user_id);
			$case->set_modified_by($user_id);
			$case->set_status($status);
			$case->set_location_code($location_code);
			$case->set_component_location_id($component_location_id);
			$case->set_component_id($component_id);
			$case->set_component_child_location_id($component_child_location_id);
			$case->set_component_child_item_id($component_child_item_id);
			$case->set_condition_degree($condition_degree);
			$case->set_consequence($consequence);

			// Saves selected value from  or measurement
			if ($type == 'control_item_type_2')
			{
				$measurement = phpgw::get_var('measurement');
				$case->set_measurement($measurement);
			}
			else if ($type == 'control_item_type_3')
			{
				$option_value = phpgw::get_var('option_value');
				$case->set_measurement($option_value);
			}
			else if ($type == 'control_item_type_4')
			{
				$option_value = phpgw::get_var('option_value');
				$case->set_measurement($option_value);
			}
			else if ($type == 'control_item_type_5')
			{
				$option_value = phpgw::get_var('option_value');
//				$case->set_measurement(serialize($option_value));
				$case->set_measurement($option_value);
			}

			$regulation_reference = phpgw::get_var('regulation_reference');
			$case->set_regulation_reference($regulation_reference);
			$case_id = $this->so->store($case);

			if ($case_id > 0)
			{
				return json_encode(array
					(
					"status" => "saved",
					'case_id' => $case_id
					));
			}
			else
			{
				return json_encode(array("status" => "not_saved"));
			}
		}

		function save_case()
		{
			if (!$this->add && !$this->edit)
			{
				return json_encode(array("status" => "not_saved"));
			}


			$case_id = phpgw::get_var('case_id');
			$case_descr = phpgw::get_var('case_descr');
			$proposed_counter_measure =  phpgw::get_var('proposed_counter_measure');
			$case_status = phpgw::get_var('case_status');
			$measurement = phpgw::get_var('measurement');
			$regulation_reference = phpgw::get_var('regulation_reference');

			$check_list_id = phpgw::get_var('check_list_id');


			$component_child =  phpgw::get_var('component_child');

			if($component_child)
			{
				$component_child_arr = explode('_', $component_child);
				$component_child_location_id = $component_child_arr[0];
				$component_child_item_id = $component_child_arr[1];
			}
			else
			{
				$component_child_location_id = null;
				$component_child_item_id = null;
			}


			$todays_date_ts = mktime(0, 0, 0, date("m"), date("d"), date("Y"));

			$condition_degree =  phpgw::get_var('condition_degree');
			$consequence =  phpgw::get_var('consequence');

			$case = $this->so->get_single($case_id);
			$case->set_component_child_location_id($component_child_location_id);
			$case->set_component_child_item_id($component_child_item_id);
			$case->set_descr($case_descr);
			$case->set_proposed_counter_measure($proposed_counter_measure);
			$case->set_modified_date($todays_date_ts);
			if($measurement && is_array($measurement))
			{
//				$case->set_measurement(serialize($measurement));
				$case->set_measurement($measurement);
			}
			else
			{
				$case->set_measurement($measurement);
			}

			$case->set_regulation_reference($regulation_reference);

			$case->set_status($case_status);
			$case->set_condition_degree($condition_degree);
			$case->set_consequence($consequence);

			if ($case->validate())
			{
				$case_id = $this->so->store($case);
				$case = $this->so->get_single($case_id);

				if ($case_id > 0)
				{
					$check_item = $this->so_check_item->get_single($case->get_check_item_id());
					$control_item = $this->so_control_item->get_single($check_item->get_control_item_id());

					$type = $control_item->get_type();

					return json_encode(array("status" => "saved", "type" => $type, "caseObj" => $case->toArray()));
				}
				else
				{
					return json_encode(array("status" => "not_saved"));
				}
			}
			else
			{
				return json_encode(array("status" => "error"));
			}
		}

		function create_case_message()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single($check_list_id);

			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", "no_message_registered");

			foreach ($check_items_and_cases as $check_item)
			{
				$control_group = $this->so_control_group->get_single($check_item->get_control_item()->get_control_group_id());
				$check_item->get_control_item()->set_control_group_name($control_group->get_group_name());
				$check_item->get_control_item()->set_control_area_name($control_group->get_control_area_name());

				foreach ($check_item->get_cases_array() as $case)
				{

					$component_location_id = $case->get_component_location_id();
					$component_id = $case->get_component_id();
					if ($component_id)
					{
						$location_info = $GLOBALS['phpgw']->locations->get_name($component_location_id);

						if (substr($location_info['location'], 1, 8) == 'location')
						{
							$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $component_location_id,
								'id' => $component_id), true);
							$location_code = $item_arr['location_code'];
							$short_desc = execMethod('property.bolocation.get_location_name', $location_code);
						}
						else
						{
							$short_desc = execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_location_id, 'id' => $component_id));

							$component_child_location_id = $case->get_component_child_location_id();
							$component_child_item_id = $case->get_component_child_item_id();

							if($component_child_location_id && $component_child_item_id)
							{
								$short_desc .= "<br>" . execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_child_location_id, 'id' => $component_child_item_id));
							}

						}

						$case->set_component_descr($short_desc);
					}
					$case_files = $this->get_case_images($case->get_id());
					$case->set_case_files($case_files);
				}
			}

			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single($control_id);

			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;

			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();

			$ticket_category = $config->config_data['ticket_category'];
			$categories = $catsObj->formatted_xslt_list(array('select_name' => 'values[cat_id]',
				'selected' => $ticket_category, 'use_acl' => $this->_category_acl));

			$component_id = $check_list->get_component_id();

			$get_locations = false;

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));
					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);
					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));
				}

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
				$type = 'component';
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
			}

			$level = $this->location_finder->get_location_level($location_code);

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());


			$data = array
			(

				'categories' => $categories,
				'check_list' => $check_list->toArray(),
				'control' => $control->toArray(),
				'check_items_and_cases' => $check_items_and_cases,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'building_location_code' => $building_location_code,
				'current_year' => $year,
				'current_month_nr' => $month,
				'type' => $type,
				'get_locations'	=> $get_locations,
				'location_level' => $level,
				'degree_list' => array('options' => createObject('property.borequest')->select_degree_list()),
				'consequence_list' => array('options' => createObject('property.borequest')->select_consequence_list())
			);
//			_debug_array($check_items_and_cases);die();
			if (count($buildings_array) > 0)
			{
				$data['buildings_array'] = $buildings_array;
			}
			else
			{
				$data['building_array'] = $building_array;
			}

			phpgwapi_jquery::load_widget('core');

			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'custom_ui.js');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');

			self::render_template_xsl(array(
				'check_list/fragments/check_list_menu',
				'check_list/fragments/check_list_change_status',
				'case/create_case_message'), $data);
		}

		function send_case_message()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$location_code = phpgw::get_var('location_code');
			$message_title = phpgw::get_var('message_title');
			$message_cat_id = phpgw::get_var('message_cat_id');
			$case_ids = phpgw::get_var('case_ids');

			if (!$this->add && !$this->edit)
			{
				phpgwapi_cache::message_set('No access', 'error');
				$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list',
					'check_list_id' => $check_list_id));
			}

			$message_ticket_id = $this->send_case_message_step_2($check_list_id,$location_code, $message_title, $message_cat_id, $case_ids );

			$this->redirect(array('menuaction' => 'controller.uicase.view_case_message', 'check_list_id' => $check_list_id,
				'message_ticket_id' => $message_ticket_id));

		}

		function send_case_message_step_2( $check_list_id,$location_code, $message_title, $message_cat_id, $case_ids )
		{

			if(!$case_ids)
			{
				return false;
			}
			$check_list = $this->so_check_list->get_single($check_list_id);

			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single($control_id);

			$message_details = "Kontroll: " . $control->get_title() . "\n";

			$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info = true;

			//liste alle
			$control_areas = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => $control_area_id,
				'globals' => true, 'use_acl' => $_category_acl));

			$control_area_id = $control->get_control_area_id();
			$control_area = $cats->return_single($control_area_id);
			$control_area_name = $control_area[0]['name'];

			$message_details .= "Kontrollområde: " . $control_area_name . "\n\n";

			// Generates message details from comment field in check item

			$counter = 1;
			foreach ($case_ids as $case_id)
			{
				$case = $this->so->get_single($case_id);

				$check_item = $this->so_check_item->get_single($case->get_check_item_id());
				$control_item = $check_item->get_control_item();
				$control_group = $this->so_control_group->get_single($control_item['control_group_id']);
				$group_name = $control_group->get_group_name();
				$component_location_id = $case->get_component_location_id();

				$message_details .= "Ref: {$case_id}: \n";
				$message_details .= "{$group_name}::Gjøremål {$counter}: \n";

				if ($component_id = $case->get_component_id())
				{
					$location_info = $GLOBALS['phpgw']->locations->get_name($component_location_id);

					if (substr($location_info['location'], 1, 8) == 'location')
					{
						$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $component_location_id,
							'id' => $component_id), true);
						$location_code = $item_arr['location_code'];
						$short_desc = execMethod('property.bolocation.get_location_name', $location_code);
					}
					else
					{
						$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $component_location_id,
							'id' => $component_id));
						$component_child_location_id = $case->get_component_child_location_id();
						$component_child_item_id = $case->get_component_child_item_id();

						if($component_child_location_id && $component_child_item_id)
						{
							$short_desc .= "\n" . execMethod('property.soentity.get_short_description', array(
							'location_id' => $component_child_location_id, 'id' => $component_child_item_id));
						}

					}
					$message_details .= "Hvor: {$short_desc}\n";
				}

				if($case->get_condition_degree())
				{
					$message_details .= lang('condition degree') . ': ' . $case->get_condition_degree() . "\n";
				}

				if($case->get_consequence())
				{
					$message_details .= lang('consequence') . ': ' . $case->get_consequence() . "\n";
				}

				$message_details .= 'Hva: ' . $case->get_descr() . "\n";

				if($case->get_proposed_counter_measure())
				{
					$message_details .= lang('proposed counter measure') . ': ' . $case->get_proposed_counter_measure() . "\n";
				}

				$message_details .= "\n";
				$counter++;
			}

			// This value represents the type
			$location_id = $GLOBALS['phpgw']->locations->get_id("controller", ".checklist");

			$ticket = array
			(
				'location_code' => $location_code,
				'cat_id' => $message_cat_id,
				'priority' => $priority, //valgfri (1-3)
				'title' => $message_title,
				'details' => $message_details,
				'file_input_name' => 'file' // navn på felt som inneholder fil
			);

			$botts = CreateObject('property.botts', true);
			$message_ticket_id = $botts->add_ticket($ticket);
			$location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');
			$user_id = $GLOBALS['phpgw_info']['user']['id'];

			$interlink_data = array
				(
				'location1_id' => $location_id,
				'location1_item_id' => $check_list_id,
				'location2_id' => $location_id_ticket,
				'location2_item_id' => $message_ticket_id,
				'account_id' => $user_id
			);

			execMethod('property.interlink.add', $interlink_data);

//---Sigurd: start register component to ticket
			$component_id = $check_list->get_component_id();

			if ($component_id > 0)
			{
				$component_location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$interlink_data = array
					(
					'location1_id' => $component_location_id,
					'location1_item_id' => $component_id,
					'location2_id' => $location_id_ticket,
					'location2_item_id' => $message_ticket_id,
					'account_id' => $user_id
				);

				execMethod('property.interlink.add', $interlink_data);
			}

//---End register component to ticket
			//Not used
			//$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
			// Registers message and updates check items with message ticket id

			foreach ($case_ids as $case_id)
			{
				$case = $this->so->get_single($case_id);
				$case->set_location_id($location_id_ticket);
				$case->set_location_item_id($message_ticket_id);
				$this->so->store($case);
			}

			/*
			 * Transfer pictures
			 */
			$bofiles = CreateObject('property.bofiles', '');
			$files = array();
			foreach ($case_ids as $case_id)
			{
				$files = $this->get_case_images($case_id);
				foreach ($files as $file)
				{
					$file_name = "{$case_id}_{$file['name']}";

					$to_file = "/property/fmticket/{$message_ticket_id}/{$case_id}_{$file['name']}";
					$from_file = "{$file['directory']}/{$file['name']}";

					if ($bofiles->vfs->file_exists(array(
							'string' => $to_file,
							'relatives' => array(RELATIVE_NONE)
						)))
					{
						$receipt['error'][] = array('msg' => lang('This file already exists !'));
						phpgwapi_cache::message_set($file_name . ': ' . lang('This file already exists !'), 'error');
					}
					else
					{
						$bofiles->create_document_dir("fmticket/{$message_ticket_id}");
						$bofiles->vfs->override_acl = 1;

						if (!$bofiles->vfs->cp(array(
								'from' => $from_file,
								'to' => $to_file,
								'relatives' => array(RELATIVE_NONE, RELATIVE_NONE))))
						{
							phpgwapi_cache::message_set($file_name . ': ' . lang('Failed to upload file !'), 'error');
						}
						$bofiles->vfs->override_acl = 0;
					}
				}
			}

			return $message_ticket_id;
		}

		function view_case_message()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$message_ticket_id = phpgw::get_var('message_ticket_id');

			$check_list = $this->so_check_list->get_single($check_list_id);

			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single($control_id);

			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases_by_message($message_ticket_id);

			$botts = CreateObject('property.botts', true);
			$message_ticket = $botts->read_single($message_ticket_id);
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;

			$category = $catsObj->return_single($message_ticket["cat_id"]);

			$component_id = $check_list->get_component_id();

			$get_locations = false;
			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));
					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);
					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));
				}

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
			}

			$level = $this->location_finder->get_location_level($location_code);
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$data = array
				(
				'control' => $control->toArray(),
				'message_ticket_id' => $message_ticket_id,
				'message_ticket' => $message_ticket,
				'category' => $category[0]['name'],
				'location_array' => $location_array,
				'component_array' => $component_array,
				'control_array' => $control->toArray(),
				'check_list' => $check_list->toArray(),
				'check_items_and_cases' => $check_items_and_cases,
				'current_year' => $year,
				'current_month_nr' => $month,
				'type' => $type,
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'get_locations'	=> $get_locations
			);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'custom_ui.js');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'case/view_case_message'), $data);
		}

		public function updateStatusForCases( $location_id, $location_item_id, $updateStatus = 0 )
		{
			if (!$this->add && !$this->edit)
			{
				return;
			}

			$cases_array = $this->so->get_cases_by_message($location_id, $location_item_id);

			if (!empty($cases_array))
			{
				// Updates status for cases related to message
				foreach ($cases_array as $case)
				{
					$case->set_status($updateStatus);
					$this->so->update($case);
				}

				// Gets first case of cases related to the message
				$case = $cases_array[0];

				// Gets check_item from case
				$check_item_id = $case->get_check_item_id();

				// Gets check_list from check_item
				$check_item = $this->so_check_item->get_single($check_item_id);
				$check_list_id = $check_item->get_check_list_id();
			}
		}

		public function delete_case()
		{
			if (!$this->delete)
			{
				return json_encode(array("status" => "not_deleted"));
			}

			$case_id = phpgw::get_var('case_id');

			$case = $this->so->get_single($case_id);
			$case->set_status(controller_check_item_case::STATUS_CLOSED);

			$status = false;
			if ($this->so->store($case))
			{
				$status = $this->so->delete($case_id);
			}

			if ($status)
			{
				return json_encode(array("status" => "deleted"));
			}
			else
			{
				return json_encode(array("status" => "not_deleted"));
			}
		}

		public function close_case()
		{
			if (!$this->add && !$this->edit)
			{
				return json_encode(array("status" => "false"));
			}

			$case_id = phpgw::get_var('case_id');

			$case = $this->so->get_single($case_id);
			$case->set_status(controller_check_item_case::STATUS_CLOSED);

			$case_id = $this->so->store($case);

			if ($case_id > 0)
			{
				return json_encode(array("status" => "true"));
			}
			else
			{
				return json_encode(array("status" => "false"));
			}
		}

		public function open_case()
		{
			if (!$this->add && !$this->edit)
			{
				return json_encode(array("status" => "false"));
			}

			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');

			$case = $this->so->get_single($case_id);
			$case->set_status(controller_check_item_case::STATUS_OPEN);

			$case_id = $this->so->store($case);

			if ($case_id > 0)
			{
				return json_encode(array("status" => "true"));
			}
			else
			{
				return json_encode(array("status" => "false"));
			}
		}

		function view_open_cases()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$case_location_code = phpgw::get_var('location_code');

			$check_list = $this->so_check_list->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());

			$check_list_location_code = $check_list->get_location_code();

			$component_id = $check_list->get_component_id();
			$get_locations = false;

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));
					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);
					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));
				}

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
				$buildings_on_property = array();
			}
			else
			{

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list_location_code));
				$type = 'location';
				// Fetches locations on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $check_list_location_code, $level);
			}


			$level = $this->location_finder->get_location_level($check_list_location_code);
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$user_role = true;

			$historic_location_code = $case_location_code ? $case_location_code : $check_list_location_code;
			$open_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, $_type = null, 'open_or_waiting', null, $historic_location_code);

			$open_old_cases =  $this->so_check_item->get_check_items_with_cases($check_list_id, $_type = null, 'open_or_waiting_old', null, $historic_location_code, $component_id);

			$open_check_items_and_cases = array_merge($open_check_items_and_cases, $open_old_cases);

			if ($buildings_on_property)
			{
				foreach ($buildings_on_property as &$building)
				{
					$building['selected'] = $building['id'] == $case_location_code ? 1 : 0;
				}
			}

			foreach ($open_check_items_and_cases as $key => $check_item)
			{
				$control_item_with_options = $this->so_control_item->get_single_with_options($check_item->get_control_item_id());

				foreach ($check_item->get_cases_array() as $case)
				{
					$measurement = $case->get_measurement();
					$regulation_reference = $case->get_regulation_reference();

//					if(unserialize($measurement))
//					{
//						$case->set_measurement(unserialize($measurement));
//					}

					$component_location_id = $case->get_component_location_id();
					$component_id = $case->get_component_id();
					if ($component_id)
					{
						$location_info = $GLOBALS['phpgw']->locations->get_name($component_location_id);

						if (substr($location_info['location'], 1, 8) == 'location')
						{
							$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $component_location_id,
								'id' => $component_id), true);
							$location_code = $item_arr['location_code'];
							$short_desc = execMethod('property.bolocation.get_location_name', $location_code);
						}
						else
						{
							$short_desc = execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_location_id, 'id' => $component_id));

							$component_child_location_id = $case->get_component_child_location_id();
							$component_child_item_id = $case->get_component_child_item_id();

							if($component_child_location_id && $component_child_item_id)
							{
								$short_desc .= "<br>" . execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_child_location_id, 'id' => $component_child_item_id));
							}
						}
						$case->set_component_descr($short_desc);

					}
					$case_files = $this->get_case_images($case->get_id());
					$case->set_case_files($case_files);
				}

				$check_item->get_control_item()->set_options_array($control_item_with_options->get_options_array());
				$check_item->get_control_item()->set_regulation_reference_options_array($control_item_with_options->get_regulation_reference_options_array());
				$open_check_items_and_cases[$key] = $check_item;
			}

			$case_data = $this->_get_case_data();

//			_debug_array($case_data['check_list']->get_id());
//			_debug_array($case_data['component_children']);

			$component_children = (array)$case_data['component_children'];
//			_debug_array($open_check_items_and_cases);die();
			$data = array
				(
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'component_children' => $component_children,
				'type' => $type,
				'location_level' => $level,
				'get_locations'	=> $get_locations,
				//	'building_location_code' 		=> $case_location_code,
				'current_year' => $year,
				'current_month_nr' => $month,
				'open_check_items_and_cases' => $open_check_items_and_cases,
				'cases_view' => 'open_cases',
				'degree_list' => array('options' => createObject('property.borequest')->select_degree_list()),
				'consequence_list' => array('options' => createObject('property.borequest')->select_consequence_list())
			);
//			_debug_array($open_check_items_and_cases); die();
			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'case.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');

			self::render_template_xsl(array(
				'check_list/fragments/check_list_menu',
				'case/cases_tab_menu',
				'case/view_open_cases',
				'case/case_row',
				'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		function view_closed_cases()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$case_location_code = phpgw::get_var('location_code');

			$check_list = $this->so_check_list->get_single($check_list_id);

			// Check list top section info
			$control = $this->so_control->get_single($check_list->get_control_id());
			$location_code = $check_list->get_location_code();

			$component_id = $check_list->get_component_id();

			$get_locations = false;

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));
					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));

				}
				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);
				$component_array = $component->toArray();

				$type = 'component';
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);
				$buildings_on_property = array();
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				// Fetches buildings on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
			}
			// Check list top section info

			$level = $this->location_finder->get_location_level($location_code);
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$user_role = true;


			$closed_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, 'closed', null, $case_location_code ? $case_location_code : $check_list->get_location_code());

			if ($buildings_on_property)
			{
				foreach ($buildings_on_property as &$building)
				{
					$building['selected'] = $building['id'] == $case_location_code ? 1 : 0;
				}
			}

//---------
			foreach ($closed_check_items_and_cases as $key => $check_item)
			{
				//		$control_item_with_options = $this->so_control_item->get_single_with_options( $check_item->get_control_item_id() );

				foreach ($check_item->get_cases_array() as $case)
				{
					$component_location_id = $case->get_component_location_id();
					$component_id = $case->get_component_id();
					if ($component_id)
					{
						$location_info = $GLOBALS['phpgw']->locations->get_name($component_location_id);

						if (substr($location_info['location'], 1, 8) == 'location')
						{
							$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $component_location_id,
								'id' => $component_id), true);
							$location_code = $item_arr['location_code'];
							$short_desc = execMethod('property.bolocation.get_location_name', $location_code);
						}
						else
						{
							$short_desc = execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_location_id, 'id' => $component_id));

							$component_child_location_id = $case->get_component_child_location_id();
							$component_child_item_id = $case->get_component_child_item_id();

							if($component_child_location_id && $component_child_item_id)
							{
								$short_desc .= "<br>" . execMethod('property.soentity.get_short_description', array(
								'location_id' => $component_child_location_id, 'id' => $component_child_item_id));
							}

						}
						$case->set_component_descr($short_desc);
					}
					$case_files = $this->get_case_images($case->get_id());
					$case->set_case_files($case_files);
				}
				//		$check_item->get_control_item()->set_options_array( $control_item_with_options->get_options_array() );
				//		$closed_check_items_and_cases[$key] = $check_item;
			}
//-------


			$status_list = array(
				array('id' => controller_check_item_case::STATUS_OPEN, 'name' => lang('open')),
				array('id' => controller_check_item_case::STATUS_CLOSED, 'name' => lang('closed')),
				array('id' => controller_check_item_case::STATUS_PENDING, 'name' => lang('pending')),
				array('id' => controller_check_item_case::STATUS_CORRECTED_ON_CONTROL, 'name' => lang('corrected on controll')),
			);
			$data = array
				(
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'location_level' => $level,
				'get_locations'	=> $get_locations,
				//	'building_location_code' 		=> $building_location_code,
				'current_year' => $year,
				'current_month_nr' => $month,
				'closed_check_items_and_cases' => $closed_check_items_and_cases,
				'check_list' => $check_list,
				'cases_view' => 'closed_cases',
				'building_location_code' => $building_location_code,
				'degree_list' => array('options' => createObject('property.borequest')->select_degree_list()),
				'consequence_list' => array('options' => createObject('property.borequest')->select_consequence_list()),
				'status_list' => array('options' => $status_list)
			);
//			_debug_array($closed_check_items_and_cases);
			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'case.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'case/cases_tab_menu',
				'case/view_closed_cases', 'case/case_row',
				'check_list/fragments/nav_control_plan', 'check_list/fragments/check_list_top_section',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		public function query()
		{

		}
	}