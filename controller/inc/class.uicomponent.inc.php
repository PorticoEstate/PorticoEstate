<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erink Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2015 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @version $Id: class.uicomponent.inc.php 12210 2014-10-21 07:41:31Z erikhl $
	 */
	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.jquery');

	phpgw::import_class('phpgwapi.uicommon');

	phpgw::import_class('controller.socheck_list');

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'component', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'status_agg_month_info', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
	include_class('controller', 'year_calendar', 'inc/component/');
	include_class('controller', 'year_calendar_agg', 'inc/component/');
	include_class('controller', 'month_calendar', 'inc/component/');


	class controller_uicomponent extends phpgwapi_uicommon
	{

		private $so;
		private $_category_acl;
		private $read;
		private $add;
		private $edit;
		private $delete;
		private $org_units;
		public $public_functions = array
			(
			'index'						=> true,
			'add_controll_from_master'	=> true
		);

		public function __construct()
		{
			parent::__construct('controller');

			$this->read		= $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller');//1
			$this->add		= $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller');//2
			$this->edit		= $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller');//4
			$this->delete	= $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller');//8
			$this->manage	= $GLOBALS['phpgw']->acl->check('.control', 16, 'controller');//16

//			$this->so					 = CreateObject('controller.socontrol');

			$config				 = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$this->_category_acl = isset($config->config_data['acl_at_control_area']) && $config->config_data['acl_at_control_area'] == 1 ? true : false;

			self::set_active_menu('controller::status_components');
			$this->account			= $GLOBALS['phpgw_info']['user']['account_id'];

			if(phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
		}

		public function add_controll_from_master()
		{
			$master_component = phpgw::get_var('master_component', 'string');
			$target = phpgw::get_var('target', 'string');//array of strings
			$result = array('status' => 'error', 'message' => '');

			if($this->manage)
			{
				$so_control	= CreateObject('controller.socontrol');
				try
				{
					$result['status'] = $so_control->add_controll_to_component_from_master($master_component, $target);
					$result['message'] = count($target) . ' ' . lang('added');
				}
				catch(Exception $e)
				{
					if ( $e )
					{
						$result['message'] = $e->getMessage();
					}
				}
			}
			else
			{
				$result['message'] = 'Go away';
			}
			return $result;
		}

		private function get_location_filter()
		{
			$entity_group_id = phpgw::get_var('entity_group_id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');

			$location_filter = phpgwapi_cache::session_get('controller', "location_filter_{$entity_group_id}");

			if(!$location_filter)
			{
				$this->soadmin_entity	= CreateObject('property.soadmin_entity');
				$entity_list 	= $this->soadmin_entity->read(array('allrows' => true));

				$location_filter = array();
				foreach($entity_list as $entry)
				{
					$categories = $this->soadmin_entity->read_category(array('entity_id' => $entry['id'],'order' => 'name','sort' => 'asc','enable_controller' => true, 'allrows' => true));
					foreach($categories as $category)
					{

						if($category['enable_controller'])
						{
							if($entity_group_id && $category['entity_group_id'] != $entity_group_id)
							{
								continue;
							}
							$sort_arr = explode(' ', $category['name']);
							$location_filter[] = array
							(
								'id' => $category['location_id'],
								'name'=> "{$entry['name']}::{$category['name']}",
								'sort_key' => trim($sort_arr[0])
							);
						}
					}
				}
				// Obtain a list of columns
				foreach ($location_filter as $key => $row)
				{
					$id[$key]  = $row['sort_key'];
				}

				array_multisort($id,SORT_ASC, SORT_STRING, $location_filter);
				phpgwapi_cache::session_set('controller', "location_filter_{$entity_group_id}", $location_filter);
			}
			foreach($location_filter as &$location)
			{
				$location['selected'] = $location['id'] == $location_id ? 1 : 0;
			}
			return $location_filter;
		}

		/**
		 * Fetches controls and returns to datatable
		 *
		 * @param HTTP::phpgw_return_as	specifies how data should be returned
		 * @return data array
		 */
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			phpgwapi_jquery::load_widget('core');

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.checklist');
			$user_list = array();
			foreach($users as $user)
			{
				$user_list[] = array
				(
					'id'		=> $user['account_id'],
					'name'		=> "{$user['account_lastname']}, {$user['account_firstname']}",
					'selected'	=> 0 //$this->account == $user['account_id'] ? 1 : 0
				);
			}

			$_my_negative_self = -1 * $this->account;

			$default_value = array
			(
				'id'		=> $_my_negative_self,
				'name'		=> lang('my assigned controls'),
				'selected' 	=> $_my_negative_self == ($this->account * -1)
			);

			/*Unselect user if filter on component*/
			if(phpgw::get_var('component_id', 'int'))
			{
				$default_value['selected'] = 0;
			}

			unset($_my_negative_self);
			array_unshift ($user_list,$default_value);
			array_unshift($user_list, array('id' => '', 'name' => lang('select')));

			// Sigurd: Start categories
			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_areas		 = $cats->formatted_xslt_list(array('format' => 'filter', 'selected' => $control_area_id,
				'globals' => true, 'use_acl' => $this->_category_acl));
			array_unshift($control_areas['cat_list'], array('cat_id' => '', 'name' => lang('select value')));
			$control_areas_array = array();
			foreach($control_areas['cat_list'] as $cat_list)
			{
				$control_areas_array[] = array
					(
					'id'	 => $cat_list['cat_id'],
					'name'	 => $cat_list['name'],
				);
			}
			// END categories
			// start district
			$property_bocommon	 = CreateObject('property.bocommon');
			$district_list		 = $property_bocommon->select_district_list('dummy', $this->district_id);
			array_unshift($district_list, array('id' => '', 'name' => lang('no district')));
			// end district

			$year_list	= array();

			$year = date('Y');
			for ( $_year= ($year - 2); $_year < ($year + 5); $_year++ )
			{
				$year_list[] = array
				(
					'id'	 => $_year,
					'name'	 => $_year,
					'selected'	=> $_year == $year ? 1 : 0
				);
			}
			$status_list = array(
				array('id' => '', 'name' => lang('select value')),
				array('id' => 'not_performed', 'name' => lang('status not done')),
				array('id' => 'done_with_open_deviation', 'name' => lang('done with open deviation')),
			);

			$filter_component = '';
			if(phpgw::get_var('component_id', 'int'))
			{
				$filter_component = phpgw::get_var('location_id', 'int') . '_' . phpgw::get_var('component_id', 'int');
			}
			$data = array(
				'datatable_name' => lang('status components'),
				'form'			 => array(
					'action'	=> self::link(array('menuaction' => 'controller.uicomponent.index')),
					'method'	=> 'POST',
					'toolbar' => array(
						'item' => array(
							array('type'	 => 'filter',
								'name'	 => 'report_type',
								'text'	 => lang('report type'),
								'list'	 => array(array('id' => 'components', 'name' => lang('components')), array('id' => 'summary', 'name' => lang('summary'))),
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'entity_group_id',
								'text'	 => lang('entity group'),
								'list'	 => execMethod('property.bogeneric.get_list',array('type' => 'entity_group', 'selected' => phpgw::get_var('entity_group_id'), 'add_empty' => true)),
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'hidden',
								'name'	 => 'location_id',
								'value'	 => phpgw::get_var('location_id', 'int')
							),
							array('type'	 => 'filter',
								'name'	 => 'location_id',
								'text'	 => lang('component'),
								'list'	 => array(),
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'org_unit_id',
								'text'	 => lang('department'),
								'list'	 => execMethod('property.bogeneric.get_list',array('type' => 'org_unit', 'selected' => phpgw::get_var('org_unit_id'), 'add_empty' => true)),
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'user_id',
								'text'	 => lang('User'),
								'list'	 => $user_list,
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'checkbox',
								'name'	 => 'user_only',
								'text'	 => 'Filtrer bruker',
								'value'	 => 1,
								'onclick'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'district_id',
								'text'	 => lang('district'),
								'list'	 => $district_list,
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'year',
								'text'	 => lang('year'),
								'list'	 => $year_list,
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'status',
								'text'	 => lang('status'),
								'list'	 => $status_list,
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'checkbox',
								'name'	 => 'all_items',
								'text'	 => 'List uten kontroller',
								'value'	 => 1,
								'onclick'	=> 'update_table();'
							),
							array('type'	 => 'hidden',
								'name'	 => 'filter_component',
								'text'	 => '',
								'value'	 => $filter_component
							),
							array('type'	 => 'hidden',
								'name'	 => 'custom_frontend',
								'text'	 => '',
								'value'	 => isset($GLOBALS['phpgw_info']['flags']['custom_frontend']) && $GLOBALS['phpgw_info']['flags']['custom_frontend'] ? 1 : 0
							)
						)
					)
				),
				'datatable'		 => array(
					'source' => self::link(array('menuaction' => 'controller.uicomponent.index',
						'phpgw_return_as' => 'json')),
					'field'	 =>  $this->get_fields($filter_component),
				),
			);
			self::render_template_xsl(array('component','calendar/icon_color_map'), $data);
		}

		private function get_fields($filter_component = '')
		{
			$fields = array
			(
				array(
					'key'		 => 'choose',
					'label'		 => '',
					'sortable'	 => false,
				),
				array(
					'key'		 => 'component_url',
					'label'		 => lang('component'),
					'sortable'	 => true,
				),
				array(
					'key'		 => 'year',
					'label'		 => lang('year'),
					'sortable'	 => true,
				),
				array(
					'key'		 => 'descr',
					'label'		 => '',
					'sortable'	 => true,
				),
				array(
					'key'		 => '1',
					'label'		 => lang('short_month 1 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '2',
					'label'		 => lang('short_month 2 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '3',
					'label'		 => lang('short_month 3 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '4',
					'label'		 => lang('short_month 4 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '5',
					'label'		 => lang('short_month 5 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '6',
					'label'		 => lang('short_month 6 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '7',
					'label'		 => lang('short_month 7 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '8',
					'label'		 => lang('short_month 8 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '9',
					'label'		 => lang('short_month 9 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '10',
					'label'		 => lang('short_month 10 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '11',
					'label'		 => lang('short_month 11 capitalized'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '12',
					'label'		 => lang('short_month 12 capitalized'),
					'sortable'	 => true,
				),
			);

			return $fields;
		}

		/**
		* Get the sublevels of the org tree into one arry
		*/
		private function _get_children($data = array() )
		{
			foreach ($data as $entry)
			{
				$this->org_units[]= $entry['id'];
				if(isset($entry['children']) && $entry['children'])
				{
					$this->_get_children($entry['children']);
				}
			}
		}

		public function query()
		{
			$entity_group_id = phpgw::get_var('entity_group_id', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$control_area = phpgw::get_var('control_area', 'int');
			$user_id = phpgw::get_var('user_id', 'int');
			$district_id = phpgw::get_var('district_id', 'int');
			$query = phpgw::get_var('query', 'string');
			$year = phpgw::get_var('year', 'int');
			$all_items = phpgw::get_var('all_items', 'bool');
			$user_only = phpgw::get_var('user_only', 'bool');
			$filter_status = phpgw::get_var('status', 'string');
			$report_type = phpgw::get_var('report_type', 'string');
			if($filter_component_str = phpgw::get_var('filter_component', 'string'))
			{
				$filter_component_arr = explode('_', $filter_component_str);
				$location_id = $filter_component_arr[0];
				$filter_component = $filter_component_arr[1];
			}
			if($org_unit_id = phpgw::get_var('org_unit_id', 'int'))
			{
				$_subs = execMethod('property.sogeneric.read_tree',array('node_id' => $org_unit_id, 'type' => 'org_unit'));
				$this->org_units[]= $org_unit_id;
				foreach($_subs as $entry)
				{
					$this->org_units[]= $entry['id'];
					if(isset($entry['children']) && $entry['children'])
					{
						$this->_get_children($entry['children']);
					}
				}
				unset($entry);
				unset($_subs);
			}

			$so_control			 = CreateObject('controller.socontrol');
			$this->so			= CreateObject('controller.socheck_list');

			// Validates year. If year is not set, current year is chosen
			$year = execMethod('controller.uicalendar.validate_year',$year);

			// Gets timestamp of first day in year
			$from_date_ts = execMethod('controller.uicalendar.get_start_date_year_ts',$year);

			// Gets timestamp of first day in next year
			$to_date_ts = execMethod('controller.uicalendar.get_end_date_year_ts',$year);

			$location_filter = $this->get_location_filter();

			foreach ($location_filter as $_location)
			{
				$location_type_name[$_location['id']] = $_location['name'];
			}
//			_debug_array($location_type_name);
			$components = array();
			$keep_only_assigned_to = 0;

//			$lookup_stray_items = false;
			$lookup_stray_items = !!$entity_group_id;

			if($user_id < 0)
			{
				$user_id = $user_id * -1;
				$all_items = false;
				$user_only = true;
//				$keep_only_assigned_to = $user_id;
//				$assigned_components = $so_control->get_assigned_control_components($from_date_ts, $to_date_ts, $assigned_to = $user_id);
//				foreach($assigned_components as $_location_id => $component_list)
//				{
//					$_components = execMethod('property.soentity.read',array(
//						'filter_entity_group'		=> $entity_group_id,
//						'location_id'				=> $_location_id,
//						'district_id'				=> $district_id,
//						'allrows'					=> true,
//						'filter_item'				=> $component_list
//						)
//					);
//					$components = array_merge($components, $_components);
//				}
			}
			if(!$location_id)
			{
				//nothing
			}
			else if($location_id == -1 && !$entity_group_id)
			{
				//nothing
			}
			else if(!$location_id && $entity_group_id)
			{
				//still nothing
			}
			else
			{
				$exclude_locations = array();

				foreach($location_filter as $_location_filter)
				{
					if($location_id > 0 && $_location_filter['id'] != $location_id)
					{
						continue;
					}
					$_location_id = (int)$_location_filter['id'];
					$exclude_locations[] = $_location_id;

					$_components = execMethod('property.soentity.read',array(
						'filter_entity_group'		=> $entity_group_id,
						'location_id'				=> $_location_id,
						'district_id'				=> $district_id,
						'org_units'					=> $this->org_units,
						'allrows'					=> true,
						'control_registered'		=> !$all_items,
						'check_for_control'			=> true,
						'filter_item'				=> $filter_component ? array($filter_component) : array()
						)
					);
					$components = array_merge($components, $_components);
				}
				
				if($lookup_stray_items)
				{
					$_components = execMethod('property.soentity.read_entity_group',array(
						'entity_group_id'			=> $entity_group_id,
						'exclude_locations'			=> $exclude_locations,
						'location_id'				=> $_location_id,
						'district_id'				=> $district_id,
						'org_units'					=> $this->org_units,
						'allrows'					=> true,
						'control_registered'		=> !$all_items,
						'check_for_control'			=> true
						)
					);
					$components = array_merge($components, $_components);
				}
			}

			$all_components = array();
			$components_with_calendar_array = array();
//			_debug_array($components);
			foreach($components as $_component)
			{
				$location_id = $_component['location_id'];
				$component_id = $_component['id'];
				$all_components["{$location_id}_{$component_id}"] = $_component;

				$short_description = $_component['short_description'];
				$short_description .= ' [' . $_component['location_name']. ']';

				if ($all_items && !$_component['has_control'])
				{
					continue;
				}
				$controls_at_component = $so_control->get_controls_at_component2($_component);

				foreach($controls_at_component as $component)
				{
					$_control_relation = $component->get_control_relation();

					if(!$_control_relation['serie_enabled'])
					{
	//					continue;
					}
					$control_id						= $_control_relation['control_id'];
					$control						= $so_control->get_single($control_id);

					$repeat_type				 = $control->get_repeat_type();
					
					//FIXME: Not currently supported
					if($repeat_type <= controller_control::REPEAT_TYPE_WEEK)
					{
						$repeat_type = controller_control::REPEAT_TYPE_MONTH;
					}
					// LOCATIONS: Process aggregated values for controls with repeat type day or week
					if($repeat_type <= controller_control::REPEAT_TYPE_WEEK)
					{
						//FIXME: Not currently supported

						$component->set_xml_short_desc(" {$location_type_name[$location_id]}</br>{$short_description}");

						$component_with_check_lists	 = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

						$cl_criteria = new controller_check_list();
						$cl_criteria->set_control_id($control->get_id());
						$cl_criteria->set_component_id($component->get_id());
						$cl_criteria->set_location_id($component->get_location_id());

						$from_month	 = $this->get_start_month_for_control($control);
						$to_month	 = $this->get_end_month_for_control($control);

						// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
						$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

						$year_calendar_agg					 = new year_calendar_agg($control, $year, $location_code, "VIEW_LOCATIONS_FOR_CONTROL");
						$calendar_array						 = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);
						$components_with_calendar_array["{$location_id}_{$component_id}"][]	 = array("component" => $component->toArray(),
							"calendar_array" => $calendar_array);
						
					}
					// Process values for controls with repeat type month or year
					else if($repeat_type > controller_control::REPEAT_TYPE_WEEK)
					{
						$component->set_xml_short_desc(" {$location_type_name[$location_id]}</br>{$short_description}");

						$component_with_check_lists	 = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);// ,$user_id);

						$check_lists_array = $component_with_check_lists["check_lists_array"];

						/*
						 * start override control with data from serie
						 */
						$control_relation = $component->get_control_relation();
						if(isset($control_relation['start_date']) && $control_relation['start_date'])
						{
							$control->set_start_date($control_relation['start_date']);
						}

						if(isset($control_relation['end_date']) && $control_relation['end_date'])
						{
							$control->set_end_date($control_relation['end_date']);
						}
						if(isset($control_relation['repeat_type']) && $control_relation['repeat_type'])
						{
							$control->set_repeat_type($control_relation['repeat_type']);
						}
						if(isset($control_relation['repeat_interval']) && $control_relation['repeat_interval'])
						{
							$control->set_repeat_interval($control_relation['repeat_interval']);
						}

						$year_calendar	 = new year_calendar($control, $year, $component, null, "component", $control_relation);
						$calendar_array	 = $year_calendar->build_calendar($check_lists_array);

						if($user_only && $user_id)
						{
							$keep_only_assigned_to = $user_id;

							$found_assigned_to = false;

							if($calendar_array)
							{
								foreach ($calendar_array as $_month => $_month_info)
								{
									if(isset($_month_info['info']['assigned_to']) && $_month_info['info']['assigned_to'] == $user_id)
									{
										$found_assigned_to = true;
										break;
									}
								}
							}
							if(!$found_assigned_to)
							{
								unset($all_components["{$location_id}_{$component_id}"]);
								continue;
							}
						}

						$components_with_calendar_array["{$location_id}_{$component_id}"][] = array("component" => $component->toArray(),
							"calendar_array" => $calendar_array);

					}
				}
			}
			$total_records = count($all_components);

//			_debug_array($components_with_calendar_array);
			unset($component_id);
//			_debug_array($components_with_calendar_array[1]);
//			_debug_array($components);
//			_debug_array(array_keys($components_with_calendar_array));
			$repeat_type_array = array
				(
					"0"=> lang('day'),
					"1"=> lang('week'),
					"2"=> lang('month'),
					"3"=> lang('year')
				);

			$values = array();
			foreach($components_with_calendar_array as $dummy => $entry)
			{
				$location_id = $entry[0]['component']['location_id'];
				$component_id = $entry[0]['component']['id'];

				unset($all_components["{$location_id}_{$component_id}"]);
				$data = array();
				$component_link_data = array
				(
					'menuaction'	=> 'property.uientity.edit',
					'location_id'	=> $location_id,
					'id'			=> $component_id,
					'active_tab'	=> 'controller'
				);
			
				$data['component_url'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$component_link_data)."\" target='_blank'>{$component_id}{$entry[0]['component']['xml_short_desc']}</a>";
				$data['component_id'] = $component_id;
				$data['location_id'] = $location_id;


				$max_interval_length = 0; //number of months

				$_data = array();
				foreach($entry as $dataset)
				{
					$repeat_type = (int)$dataset['component']['control_relation']['repeat_type'];
					$repeat_interval = (int)$dataset['component']['control_relation']['repeat_interval'];

				/*
						REPEAT_TYPE_DAY = 0;
						REPEAT_TYPE_WEEK = 1;
						REPEAT_TYPE_MONTH = 2;
						REPEAT_TYPE_YEAR = 3;
				 */

					switch($repeat_type)
					{
						case controller_control::REPEAT_TYPE_DAY:
							$interval_length = ceil($repeat_interval/30);
							break;
						case controller_control::REPEAT_TYPE_WEEK:
							$interval_length = ceil($repeat_interval/4);
							break;
						case controller_control::REPEAT_TYPE_MONTH:
							$interval_length = $repeat_interval;
							break;
						case controller_control::REPEAT_TYPE_YEAR:
							$interval_length = $repeat_interval * 12;
							$interval_length = $interval_length > 12 ? 12 : $interval_length;
							break;
						default:
							$interval_length = 0;
							break;
					}

					$service_time = $dataset['component']['control_relation']['service_time'];
					$controle_time = $dataset['component']['control_relation']['controle_time'];

					if($interval_length > $max_interval_length)
					{
						$max_interval_length = $interval_length;
					}
					foreach($dataset['calendar_array'] as $month => $calendar)
					{
						if($calendar)
						{
//							$repeat_type = $calendar['info']['repeat_type'] ? (int)$calendar['info']['repeat_type'] : $repeat_type;
							$calendar['info']['service_time'] = $calendar['info']['service_time'] ? $calendar['info']['service_time'] : $service_time;
							$calendar['info']['controle_time'] = $calendar['info']['controle_time'] ? $calendar['info']['controle_time'] : $controle_time;
							$_data[$month][$interval_length] = $calendar;
							$_data[$month][$interval_length]['repeat_type'] = $repeat_type;
							$_data[$month][$interval_length]['repeat_interval'] = $repeat_interval;
						}
					}
				}

				for ( $_month=1; $_month < 13; $_month++ )
				{
					for ( $i = $max_interval_length; $i > -1; $i-- )
					{
						if(isset($_data[$_month][$i]))
						{
							$data[$_month] = $_data[$_month][$i];
							$data[$_month]['repeat_type'] = "{$repeat_type_array[$_data[$_month][$i]['repeat_type']]}/{$_data[$_month][$i]['repeat_interval']}";//FIXME
							break 1;
						}
						else
						{
							$data[$_month] = array();
						}
					}
				}
				
				$values[] = $data;
			}
			unset( $entry);
			unset($component_id);
			unset($component);

			if($report_type == 'summary')
			{
				return array(
					'components' => null,
					'summary' => $this->get_summary($values),
					'location_filter' => $location_filter
				);
			}

			$choose_master = false;
			if($all_components && count($all_components))
			{
				$choose_master = true;
				foreach($all_components as $dummy => $component)
				{
					$data = array();
					$location_id = $component['location_id'];
					$component_id = $component['id'];

					$component_link_data = array
					(
						'menuaction'	=> 'property.uientity.edit',
						'location_id'	=> $location_id,
						'id'			=> $component_id,
						'active_tab'	=> 'controller'
					);

					$short_description = $component['short_description'];
					$short_description .= "[ {$component['location_name']} ]";

					$data['component_url'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$component_link_data)."\" target='_blank'>{$component_id} {$location_type_name[$location_id]}</br>{$short_description}</a>";
					$data['component_id'] = $component_id;
					$data['location_id'] = $location_id;
					$data['missing_control'] = true;
					$values[] = $data;

				}
			}
//_debug_array($values);
			$data_set = array();
			$total_time = array();
			foreach ($values as $entry)
			{
				$row		= array();
				$row_sum	= array();
				$row_sum_actual	= array();//billable_hours
				$row['component_url'] = $entry['component_url'];
				$row['year'] = '';
				$row['descr'] = '';

				if(!isset($entry['missing_control']))
				{
					if($filter_component_str)
					{
						$row['choose'] = '<input id="selected_component" type="checkbox" name="selected_component" checked = "checked" onclick="deselect_component();">';
					}
					else if ($choose_master)
					{
						$row['choose'] = "<input id=\"master_component\" type=\"radio\" name=\"master_component\" value = \"{$entry['location_id']}_{$entry['component_id']}\" >";
					}
					$row['year'] = $year;
					$row['descr'] = "Frekvens<br/>Status<br/>Utførende<br/>Tidsbruk";
				}
				else if ($choose_master)
				{
					$row['choose'] = "<input id=\"selected_components\" class=\"mychecks\" type=\"checkbox\" name=\"selected_components[]\" value = \"{$entry['location_id']}_{$entry['component_id']}\">";
				}

				$found_at_least_one = false;
				for ( $_month=1; $_month < 13; $_month++ )
				{
					$row[$_month] = $this->translate_calendar_info($entry[$_month],$year, $_month, $filter_status, $found_at_least_one, $keep_only_assigned_to);
					if($row[$_month] &&( !$user_id || $entry[$_month]['info']['assigned_to'] == $user_id))
					{
						$row_sum[$_month] = $entry[$_month]['info']['service_time'] + $entry[$_month]['info']['controle_time'];
						$row_sum_actual[$_month] =  + $entry[$_month]['info']['billable_hours'];
					}
					else
					{
						$row_sum[$_month] = 0;
						$row_sum_actual[$_month] = 0;
					}
				}
				if(!$filter_status || $found_at_least_one)
				{
					$total_time[] = $row_sum;
					$total_time_actual[] = $row_sum_actual;
					$data_set[] = $row;
				}
			}
			$fields	= $this->get_fields($filter_component_str);
			$class = '';
			$tbody = '';
			foreach($data_set as $row_data )
			{
				$tbody .= "<tr {$class}>";
				foreach($fields as $field )
				{
					$tbody .= '<td>';
					$tbody .= $row_data[$field['key']];
					$tbody .= '</td>';
				}
				$tbody .= '</tr>';
				$class = $class ? '' : 'class="alt"';

			}

			$result = array
			(
				'tbody' => $tbody
			);

			unset($_month);

			$sum_year = 0;
			$sum_year_actual = 0;

			if(!$total_time)
			{
				for ( $_month=1; $_month < 13; $_month++ )
				{
					$result['time_sum'][$_month] = 0;
				}
			}
			else
			{
				foreach ($total_time as $_row)
				{
					for ( $_month=1; $_month < 13; $_month++ )
					{
						$result['time_sum'][$_month] += $_row[$_month];
						$sum_year += $_row[$_month];
					}
				}
				unset($_row);
			}
			$result['time_sum'][0] = $sum_year;

			if(!$total_time_actual)
			{
				for ( $_month=1; $_month < 13; $_month++ )
				{
					$result['time_sum_actual'][$_month] = 0;
				}
			}
			else
			{
				foreach ($total_time_actual as $_row)
				{
					for ( $_month=1; $_month < 13; $_month++ )
					{
						$result['time_sum_actual'][$_month] += $_row[$_month];
						$sum_year_actual += $_row[$_month];
					}
				}
			}
			$result['time_sum_actual'][0] = $sum_year_actual;
			$result['total_records'] = $total_records;
			$result['location_filter'] = $location_filter;
			if ($choose_master)
			{
				$lang_save = lang('add');
				$lang_select = lang('select');
				$result['checkall'] = "<input type=\"button\" value = '{$lang_save}' title = '{$lang_save}' onclick=\"add_from_master('mychecks');\">";
				$result['checkall'] .= '</br>';
				$result['checkall'] .= "<input type=\"checkbox\" title = '{$lang_select}' onclick=\"checkAll('mychecks');\">";
			}
			else
			{
				$result['checkall'] = '';
			}

			return array(
				'components'		=> $result,
				'summary'			=> null,
				'location_filter'	=> $location_filter
				);
		}

		private function translate_calendar_info($param = array(), $year, $month, $filter_status = '', &$found_at_least_one = false, $keep_only_assigned_to)
		{
			if(!isset($param['repeat_type']))
			{
				return '';
			}

			if($keep_only_assigned_to && $keep_only_assigned_to != $param['info']['assigned_to'])
			{
				return '';
			}

			if($filter_status)
			{
				if($filter_status == 'not_performed')
				{
					switch($param['status'])
					{
						case "CONTROL_NOT_DONE":
			//			case "CONTROL_REGISTERED":
			//			case "CONTROL_PLANNED":
						case "CONTROL_NOT_DONE_WITH_PLANNED_DATE":
							break;//continues
						default:
							return;
					}
				}
				else if($filter_status == 'done_with_open_deviation')
				{
					switch($param['status'])
					{
					//	case "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS":
					//	case "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS":
						case "CONTROL_DONE_WITH_ERRORS":
					//	case "CONTROL_CANCELED":
							break;//continues
						default:
							return;
					}
				}
			}

			$found_at_least_one = true;

			switch($param['status'])
			{
				case "CONTROL_NOT_DONE":
					$status = 'Ikke utført';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_red_cross.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_REGISTERED":
					$status = 'Registrert';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_yellow_ring.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_PLANNED":
					$status = 'Planlagt';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_yellow.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_NOT_DONE_WITH_PLANNED_DATE":
					$status = 'Forsinket, Ikke utført';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_red_cross.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS":
					$status = 'Senere enn planagt';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_light_green.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS":
					$status = 'Utført uten avvik';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_dark_green.png\" title=\"{$status}\"/>";
					break;
				case "CONTROL_DONE_WITH_ERRORS":
					$status = "Utført med {$param['info']['num_open_cases']} åpne avvik";
					$img = "<img height=\"15\" src=\"controller/images/status_icon_red_empty.png\" title=\"{$status}\"/> ({$param['info']['num_open_cases']})";
					break;
				case "CONTROL_CANCELED":
					$status = 'Kansellert';
					$img = "<img height=\"15\" src=\"controller/images/status_icon_black_cross.png\" title=\"{$status}\"/>";
					break;
				default:
					$status = '';
					break;
			}
			if($param['info']['check_list_id'])
			{
				$control_link_data = array
				(
					'menuaction'	=> 'controller.uicheck_list.edit_check_list',
					'check_list_id'	=> $param['info']['check_list_id'],
				);				
			}
			else
			{
				$menuaction	= 'controller.uicheck_list.add_check_list';
				$a_date = "{$year}-{$month}-23";
				$control_link_data = array
				(
					'menuaction'	=> $menuaction,
					'control_id'	=> $param['info']['control_id'],
					'location_id'	=> $param['info']['location_id'],
					'component_id'	=> $param['info']['component_id'],
					'serie_id'		=> $param['info']['serie_id'],
					'deadline_ts'	=> mktime(23, 59, 00, $month, date('t', strtotime($a_date)), $year),
					'type'			=> $param['info']['component_id'] ? 'component' : '',
					'assigned_to'	=> $param['info']['assigned_to']
				);
			}
			$link = "<a href=\"".$GLOBALS['phpgw']->link('/index.php',$control_link_data)."\" target=\"_blank\">{$img}</a>";

			$repeat_type = $param['repeat_type'];
		//	$responsible = '---';
			$assigned_to = $param['info']['assigned_to'] > 0 ? $GLOBALS['phpgw']->accounts->id2lid($param['info']['assigned_to']) : '&nbsp;';
		//	$service_time = $param['info']['service_time'] ? $param['info']['service_time'] : '&nbsp;';
		//	$controle_time = $param['info']['controle_time'] ? $param['info']['controle_time'] : '&nbsp;';
			$time = $param['info']['service_time'] + $param['info']['controle_time'];
			$time = $time ? $time : '&nbsp;';
			$billable_hours = (float)$param['info']['billable_hours'];
			{
				$time .= " / {$billable_hours}";
			}

			return "{$repeat_type}<br/>{$link}<br/>{$assigned_to}<br/>{$time}";
		}

		private function get_summary($data)
		{


			$summary = array(
				 "CONTROL_NOT_DONE" => array(
					'name' => 'Ikke utført',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_red_cross.png\" title=\"Ikke utført\"/>"
					),
				"CONTROL_REGISTERED" => array(
					'name' => 'Registrert',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_yellow_ring.png\" title=\"Registrert\"/>"
					),
				"CONTROL_PLANNED" => array(
					'name' => 'Planlagt',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_yellow.png\" title=\"Planlagt\"/>"
					),
				"CONTROL_NOT_DONE_WITH_PLANNED_DATE" => array(
					'name' => 'Forsinket, Ikke utført',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_red_cross.png\" title=\"Forsinket, Ikke utført\"/>"
					),
				"CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS" => array(
					'name' => 'Senere enn planagt',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_light_green.png\" title=\"Senere enn planagt\"/>"
					),
				"CONTROL_DONE_IN_TIME_WITHOUT_ERRORS" => array(
					'name' => 'Utført uten avvik',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_dark_green.png\" title=\"Utført uten avvik\"/>"
					),
				"CONTROL_DONE_WITH_ERRORS" => array(
					'name' => 'Utført med åpne avvik',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_red_empty.png\" title=\"Utført med åpne avvik\"/>"
					),
				"CONTROL_CANCELED" => array(
					'name' => 'kansellert',
					'img' => "<img height=\"15\" src=\"controller/images/status_icon_black_cross.png\" title=\"kansellert\"/>"
					)
				);


			foreach($data as $entry)
			{

				for ( $_month=1; $_month < 13; $_month++ )
				{
					if(isset($entry[$_month]['status']))
					{
						$summary[$entry[$_month]['status']][$_month]['count'] +=1;
						$summary[$entry[$_month]['status']][$_month]['billable_hours'] += $entry[$_month]['info']['billable_hours'];
					}

				}
			}

			$sum = array();
			$fields = $this->get_fields();
			$html = <<<HTML

			<table id="summary">
				<thead>
				<tr>
				<th>
				</th>
				<th>
				
				</th>
				<th>
					Status
				</th>
HTML;

			foreach($fields as $field)
			{
				if((int) $field['key'])
				{
					$html .= <<<HTML

					<th>
						{$field['label']}
					</th>
HTML;
				}
			}
					$html .= <<<HTML

					</tr>
				</thead>
				<tbody>
HTML;
			unset($_month);
			foreach($summary as $status => $values)
			{
				$html .= <<<HTML

					<tr>
						<td>
							{$values['img']}
						</td>
						<td>
							Antall:</br>Tidsbruk:
						</td>
						<td>
							{$values['name']}
						</td>
HTML;
				for ( $_month=1; $_month < 13; $_month++ )
				{
					$value = '';
					if(isset($values[$_month]))
					{
						$value = "{$values[$_month]['count']}</br>{$values[$_month]['billable_hours']}";
						$sum[$_month]['count'] += $values[$_month]['count'];
						$sum[$_month]['billable_hours'] += $values[$_month]['billable_hours'];
					}
					$html .= <<<HTML

					<td>
						{$value}
					</td>
HTML;
				}
			
			}
					$html .= <<<HTML

				</tr>
			</tbody>
HTML;
			$html .= <<<HTML
  <tfoot>
    <tr>
		<td>
		</td>
		<td>
			Totalt
		</td>
		<td>
		</td>
HTML;
 			foreach($fields as $field)
			{
				if((int) $field['key'])
				{
					$html .= <<<HTML

					<td>
						{$sum[$field['key']]['count']}</br>{$sum[$field['key']]['billable_hours']}
					</td>
HTML;
				}
			}
			$html .= <<<HTML
    </tr>
  </tfoot>

		</table>
HTML;

			return $html;
		}

		function get_start_month_for_control($control)
		{
			// Checks if control starts in the year that is displayed
			if(date("Y", $control->get_start_date()) == $year)
			{
				$from_month = date("n", $control->get_start_date());
			}
			else
			{
				$from_month = 1;
			}

			return $from_month;
		}

		function get_end_month_for_control($control)
		{
			// Checks if control ends in the year that is displayed
			if(date("Y", $control->get_end_date()) == $year)
			{
				$to_month = date("n", $control->get_end_date());
			}
			else
			{
				$to_month = 12;
			}

			return $to_month;
		}

	}
