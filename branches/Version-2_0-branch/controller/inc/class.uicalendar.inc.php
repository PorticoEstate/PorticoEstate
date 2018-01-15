<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012,2013,2014,2015 Free Software Foundation, Inc. http://www.fsf.org/
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

	class controller_uicalendar extends phpgwapi_uicommon_jquery
	{

		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;
		private $location_finder;
		var $type_id;
		private $bo;
		private $bocommon;
		public $public_functions = array
			(
			'view_calendar_for_month' => true,
			'view_calendar_for_year' => true,
			'view_calendar_for_year2' => true,
			'view_calendar_year_for_locations' => true,
			'view_calendar_month_for_locations' => true,
			'update_bookmark' => true,
			'query' => true
		);

		public function __construct()
		{
			parent::__construct();

			$read = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller'); //1
			$add = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller'); //2
			$edit = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller'); //4
			$delete = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller'); //8

			$manage = $GLOBALS['phpgw']->acl->check('.control', 16, 'controller'); //16
			
			$this->bo = CreateObject('property.bolocation', true);
			$this->bocommon = & $this->bo->bocommon;
			$this->type_id = $this->bo->type_id;

			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');

			$this->location_finder = new location_finder();

			self::set_active_menu('controller::location_check_list');

			if (phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				phpgwapi_cache::session_set('controller', 'noframework', true);
			}
			else if (phpgwapi_cache::session_get('controller', 'noframework'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}
//			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
		}

		public function view_calendar_for_month()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			$month = phpgw::get_var('month');
			$role = phpgw::get_var('role', 'int', 'REQUEST', -1);
			$repeat_type = phpgw::get_var('repeat_type');

			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Validates month. If year is not set, current month in current year is chosen
			$month = $this->validate_month($month);

			// Validates year.
			$repeat_type = $this->validate_repeat_type($repeat_type);

			// Validates role.
//			$role = $this->validate_role($role);
			// Gets timestamp value of first day in month
			$from_date_ts = month_calendar::get_start_date_month_ts($year, intval($month));

			// Gets timestamp value of first day in month
			$to_date_ts = month_calendar::get_next_start_date_month_ts($year, intval($month));

			// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);

			if ($location_code != null && $location_code != "")
			{
				$level = $this->location_finder->get_location_level($location_code);

				$user_role = true;

				// Fetches buildings on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

				// Fetches controls for location within specified time period
				$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_object", $role);

				if ($level == 1)
				{
					// Fetches all controls for the components for a location within time period
//					$filter = "bim_item.location_code = '$location_code' ";
					$filter = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array = $this->so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_object", $role, $filter);
				}
				else
				{
					// Fetches all controls for the components for a location within time period
					$filter = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array = $this->so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_object", $role, $filter);
				}

				// Fetches all control ids with check lists for specified time period
				$control_id_with_check_list_array = $this->so->get_check_lists_for_location($location_code, $from_date_ts, $to_date_ts);

				// Loops through all controls for location and populates controls with check lists
				$controls_with_check_list_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);

				$controls_calendar_array = array();
				foreach ($controls_with_check_list_array as $control)
				{
					$month_calendar = new month_calendar($control, $year, $month, null, $location_code, "location");
					$calendar_array = $month_calendar->build_calendar($control->get_check_lists_array());

					$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
				}

				// COMPONENTS
				foreach ($components_with_controls_array as $component)
				{
					$location_id = $component->get_location_id();
					$component_id = $component->get_id();

					$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $location_id,
						'id' => $component_id));
					$component->set_xml_short_desc($short_desc);

					$controls_for_component_array = $component->get_controls_array();
					$controls_components_calendar_array = array();

					foreach ($controls_for_component_array as $control)
					{
						// Fetches control ids with check lists for specified time period
						$control_id_with_check_list_array = $this->so->get_check_lists_for_component($component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type = ">=0");

						// Loops through all controls for location and populates controls with check lists
						$controls_for_component_array = $this->populate_controls_with_check_lists($controls_for_component_array, $control_id_with_check_list_array);

						$month_calendar = new month_calendar($control, $year, $month, $component, null, "component");
						$calendar_array = $month_calendar->build_calendar($control->get_check_lists_array());

						$controls_components_calendar_array[] = array("control" => $control->toArray(),
							"calendar_array" => $calendar_array);
					}

					$components_calendar_array[] = array("component" => $component->toArray(), "controls_calendar" => $controls_components_calendar_array);
				}

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

				$property_array = execMethod('property.solocation.read', array('type_id' => 1,
					'allrows' => true));

				// Gets array of locations assigned to current user
				$my_locations = $this->get_my_assigned_locations($location_code);

				$heading_array = month_calendar::get_heading_array($year, $month);

				$roles_array = $this->so_control->get_roles();

				$repeat_type_array = array(
					array('id' => "0", 'value' => "Dag"),
					array('id' => "1", 'value' => "Uke"),
					array('id' => "2", 'value' => "Måned"),
					array('id' => "3", 'value' => "År")
				);

				$data = array
					(
					'buildings_on_property' => $buildings_on_property,
					'my_locations' => $my_locations,
					'property_array' => $property_array,
					'current_location' => $location_array,
					'heading_array' => $heading_array,
					'controls_calendar_array' => $controls_calendar_array,
					'components_calendar_array' => $components_calendar_array,
					'location_level' => $level,
					'roles_array' => $roles_array,
					'repeat_type_array' => $repeat_type_array,
					'current_year' => $year,
					'current_month_nr' => $month,
					'current_role' => $role,
					'current_repeat_type' => $repeat_type
				);

				phpgwapi_jquery::load_widget('autocomplete');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::render_template_xsl(array('calendar/view_calendar_month', 'calendar/check_list_status_manager',
					'calendar/icon_color_map', 'calendar/select_my_locations',
					'calendar/select_buildings_on_property', 'calendar/nav_calendar_month',
					'calendar/calendar_filters'), $data);
			}
			else
			{
				$data = array(
					'current_year' => $year,
					'current_month_nr' => $month
				);

				phpgwapi_jquery::load_widget('autocomplete');
				self::add_javascript('controller', 'controller', 'ajax.js');

				self::render_template_xsl('calendar/calendar_month_no_loc', $data);
			}
		}

		public function view_calendar_for_year()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			$role = phpgw::get_var('role', 'int');

			$repeat_type = phpgw::get_var('repeat_type');
			
			//show only controls that are not done
			$selected_ctrl_status = phpgw::get_var('current_ctrl_status');
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Validates repeat type.
			$repeat_type = $this->validate_repeat_type($repeat_type);

			// Validates role.
//			$role = $this->validate_role($role);
			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);

			// Array that will be populated with controls and calendar objects that will be sent to view
			$controls_calendar_array = array();

			// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);

			if ($location_code != null && $location_code != "")
			{
				$level = $this->location_finder->get_location_level($location_code);

				$user_role = true;

				// Fetches buildings on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

				// Fetches all controls for the location within time period
				$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_object", $role);

				if ($level == 1)
				{
					// Fetches all controls for the components for a location within time period
//					$filter = "bim_item.location_code = '$location_code' ";
					$filter = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array = $this->so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_object", $role, $filter);
				}
				else
				{
					// Fetches all controls for the components for a location within time period
					$filter = "bim_item.location_code LIKE '$location_code%' ";
					$components_with_controls_array = $this->so_control->get_controls_by_component($from_date_ts, $to_date_ts, $repeat_type, "return_object", $role, $filter);
				}

				// Loops through controls with repeat type day or week
				// and populates array that contains aggregated open cases pr month.
				foreach ($controls_for_location_array as $control)
				{
					if ($control->get_repeat_type() == controller_control::REPEAT_TYPE_DAY | $control->get_repeat_type() == controller_control::REPEAT_TYPE_WEEK)
					{
						$cl_criteria = new controller_check_list();
						$cl_criteria->set_control_id($control->get_id());
						$cl_criteria->set_location_code($location_code);

						$from_month = $this->get_start_month_for_control($control);
						$to_month = $this->get_end_month_for_control($control);

						// Loops through controls and populates aggregate open cases pr month array.
						$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

						$year_calendar_agg = new year_calendar_agg($control, $year, $location_code, "VIEW_CONTROLS_FOR_LOCATION");
						$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);

						$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
					}
				}

				$repeat_type_expr = ">=2";
				// Fetches control ids with check lists for specified time period
				$control_id_with_check_list_array = $this->so->get_check_lists_for_location($location_code, $from_date_ts, $to_date_ts, $repeat_type_expr);

				// Loops through all controls for location and populates controls with check lists
				$controls_for_location_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);

				foreach ($controls_for_location_array as $control)
				{
					if ($control->get_repeat_type() == controller_control::REPEAT_TYPE_MONTH | $control->get_repeat_type() == controller_control::REPEAT_TYPE_YEAR)
					{
						$year_calendar = new year_calendar($control, $year, null, $location_code, "location");
						$calendar_array = $year_calendar->build_calendar($control->get_check_lists_array(), $selected_ctrl_status);

						$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
					}
				}

				// COMPONENTS
				foreach ($components_with_controls_array as $component)
				{
					$location_id = $component->get_location_id();
					$id = $component->get_id();

					$short_desc_arr = execMethod('property.soentity.get_short_description', array(
						'location_id' => $location_id, 'id' => $id));
					$component->set_xml_short_desc($short_desc_arr);

					$controls_for_component_array = $component->get_controls_array();
					$controls_components_calendar_array = array();

					// AGGREGATED VALUES PR MONTH: Puts aggregated number of open cases for days and weeks in calendar array
					foreach ($controls_for_component_array as $control)
					{
						if ($control->get_repeat_type() == controller_control::REPEAT_TYPE_DAY | $control->get_repeat_type() == controller_control::REPEAT_TYPE_WEEK)
						{
							$cl_criteria = new controller_check_list();
							$cl_criteria->set_control_id($control->get_id());
							$cl_criteria->set_component_id($component->get_id());
							$cl_criteria->set_location_id($component->get_location_id());

							$from_month = $this->get_start_month_for_control($control);
							$to_month = $this->get_end_month_for_control($control);

							$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

							$year_calendar_agg = new year_calendar_agg($control, $year, $location_code, "VIEW_CONTROLS_FOR_LOCATION");
							$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);

							$controls_components_calendar_array[] = array("control" => $control->toArray(),
								"calendar_array" => $calendar_array);
						}
						else
						{
							// Fetches control ids with check lists for specified time period
							$control_id_with_check_list_array = $this->so->get_check_lists_for_component($component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type = ">=2");

							// Loops through all controls for location and populates controls with check lists
							$controls_for_component_array = $this->populate_controls_with_check_lists($controls_for_component_array, $control_id_with_check_list_array);

							$year_calendar = new year_calendar($control, $year, $component, null, "component");
							$calendar_array = $year_calendar->build_calendar($control->get_check_lists_array());

							$controls_components_calendar_array[] = array("control" => $control->toArray(),
								"calendar_array" => $calendar_array);
						}
					}

					$components_calendar_array[] = array("component" => $component->toArray(), "controls_calendar" => $controls_components_calendar_array);
				}

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

				// Gets array of locations assigned to current user
				$my_locations = $this->get_my_assigned_locations($location_code);

				$heading_array = year_calendar::get_heading_array();

				$roles_array = $this->so_control->get_roles();

				$repeat_type_array = array(
					array('id' => "0", 'value' => "Dag"),
					array('id' => "1", 'value' => "Uke"),
					array('id' => "2", 'value' => "Måned"),
					array('id' => "3", 'value' => "År")
				);
				
				$ctrl_status_array = array(
					array('id' => "CONTROL_REGISTERED", 'value' => "CONTROL_REGISTERED"),
					array('id' => "CONTROL_PLANNED", 'value' => "CONTROL_PLANNED"),
					array('id' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS"),
					array('id' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS"),
					array('id' => "CONTROL_DONE_WITH_ERRORS", 'value' => "CONTROL_DONE_WITH_ERRORS"),
					array('id' => "CONTROL_NOT_DONE", 'value' => "CONTROL_NOT_DONE"),
					//array('id' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE", 'value' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE"),
					array('id' => "CONTROL_CANCELED", 'value' => "CONTROL_CANCELED")
				);

				$data = array
					(
					'buildings_on_property' => $buildings_on_property,
					'my_locations' => $my_locations,
					'current_location' => $location_array,
					'heading_array' => $heading_array,
					'controls_calendar_array' => $controls_calendar_array,
					'components_calendar_array' => $components_calendar_array,
					'location_level' => $level,
					'roles_array' => $roles_array,
					'repeat_type_array' => $repeat_type_array,
					'ctrl_status_array' => $ctrl_status_array,
					'current_year' => $year,
					'current_role' => $role,
					'current_repeat_type' => $repeat_type,
					'current_ctrl_status' => $selected_ctrl_status
				);

				phpgwapi_jquery::load_widget('autocomplete');
				self::add_javascript('controller', 'controller', 'ajax.js');

				self::render_template_xsl(array('calendar/view_calendar_year', 'calendar/check_list_status_manager',
					'calendar/icon_color_map', 'calendar/select_my_locations',
					'calendar/select_buildings_on_property', 'calendar/nav_calendar_year',
					'calendar/calendar_filters'), $data);
			}
			else
			{
				$data = array(
					'current_year' => $year
				);

				phpgwapi_jquery::load_widget('autocomplete');
				self::add_javascript('controller', 'controller', 'ajax.js');

				self::render_template_xsl('calendar/calendar_year_no_loc', $data);
			}
		}
		
		public function view_calendar_for_year2($location_array = null, $year = null)
		{
			//$location_array = phpgw::get_var('location_array');
			//_debug_array($location_array['data']);
//			if(!empty($location_array))
//			{
//				_debug_array($location_array['data']);
////				foreach ($location_array as $loc_no => $currval)
////				{
////					var_dump($loc_no);
////				}
//			}
//			
			$location_code = phpgw::get_var('location_code');
			if($year == null)
				$year = phpgw::get_var('year');
			$role = phpgw::get_var('role', 'int');

			$repeat_type = phpgw::get_var('repeat_type');
			
			//show only controls that are not done
			$selected_ctrl_status = phpgw::get_var('current_ctrl_status');
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Validates repeat type.
			$repeat_type = $this->validate_repeat_type($repeat_type);

			// Validates role.
//			$role = $this->validate_role($role);
			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);

			// Array that will be populated with controls and calendar objects that will be sent to view
			$controls_calendar_array = array();

			// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);

//			if ($location_code != null && $location_code != "")
//			{
			if(is_array($location_array))
			{
				$locations_with_control = array();
				foreach ($location_array as $loc_code =>&$curr_pos)
				{
					$curr_pos['location_code'] = $loc_code;
					//_debug_array($curr_pos);
				}
				
				foreach ($location_array as $curr_loc)
				{
					//$curr = array();
					//$curr['location_code'] = $curr_loc['location_code'];
					//$curr['controls_calendar_array'] = $curr_loc['controls_calendar_array'];
					if(is_array($curr_loc['controls_calendar_array']) && !empty($curr_loc['controls_calendar_array']))
					{
						$locations_with_control[] = $curr_loc;
					}
				}
				//_debug_array($locations_with_control);
				
				$heading_array = year_calendar::get_heading_array();
//				_debug_array($heading_array);

				$roles_array = $this->so_control->get_roles();

				$repeat_type_array = array(
					array('id' => "0", 'value' => "Dag"),
					array('id' => "1", 'value' => "Uke"),
					array('id' => "2", 'value' => "Måned"),
					array('id' => "3", 'value' => "År")
				);
				
				$ctrl_status_array = array(
					array('id' => "CONTROL_REGISTERED", 'value' => "CONTROL_REGISTERED"),
					array('id' => "CONTROL_PLANNED", 'value' => "CONTROL_PLANNED"),
					array('id' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS"),
					array('id' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS"),
					array('id' => "CONTROL_DONE_WITH_ERRORS", 'value' => "CONTROL_DONE_WITH_ERRORS"),
					array('id' => "CONTROL_NOT_DONE", 'value' => "CONTROL_NOT_DONE"),
					//array('id' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE", 'value' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE"),
					array('id' => "CONTROL_CANCELED", 'value' => "CONTROL_CANCELED")
				);
				
				$GLOBALS['phpgw']->translation->add_app('property');

				$district_list = $this->bocommon->select_district_list('filter', $this->district_id);

				$part_of_town_list = execMethod('property.bogeneric.get_list', array('type' => 'part_of_town',
					'selected' => $part_of_town_id));
				$location_type_list = execMethod('property.soadmin_location.select_location_type');

				array_unshift($district_list, array('id' => '', 'name' => lang('select')));
				array_unshift($part_of_town_list, array('id' => '', 'name' => lang('select')));
				array_unshift($location_type_list, array('id' => '', 'name' => lang('select')));


				$data = array
					(
					'heading_array' => $heading_array,
					'location_calendar_array' => $locations_with_control,
					'roles_array' => $roles_array,
					'repeat_type_array' => $repeat_type_array,
					'ctrl_status_array' => $ctrl_status_array,
					'current_year' => $year,
					'multiple_locations' => 'yes',
					'filter_form' => array
						(
						'district_list' => array('options' => $district_list),
						'part_of_town_list' => array('options' => $part_of_town_list),
						'location_type_list' => array('options' => $location_type_list),
					),
					'update_action' => self::link(array('menuaction' => 'controller.uicalendar.query'))
				);

				phpgwapi_jquery::load_widget('autocomplete');
				self::add_javascript('controller', 'controller', 'ajax.js');
				self::add_javascript('controller', 'controller', 'ajax_calendar_for_locations.js');

				self::render_template_xsl(array('calendar/view_calendar_aggregated', 'calendar/check_list_status_manager',
					'calendar/icon_color_map', 'calendar/nav_calendar_year',
					'calendar/calendar_filters'), $data);
			}
			else
			{
				$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
				$receipt = array();

				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					return $this->query();
				}

				$msgbox_data = array();
				if (phpgw::get_var('phpgw_return_as') != 'json' && $receipt = phpgwapi_cache::session_get('phpgwapi', 'phpgw_messages'))
				{
					phpgwapi_cache::session_clear('phpgwapi', 'phpgw_messages');
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox_data($receipt);
					$msgbox_data = $GLOBALS['phpgw']->common->msgbox($msgbox_data);
				}

				$GLOBALS['phpgw']->translation->add_app('property');

				$district_list = $this->bocommon->select_district_list('filter', $this->district_id);

				$part_of_town_list = execMethod('property.bogeneric.get_list', array('type' => 'part_of_town',
					'selected' => $part_of_town_id));
				$location_type_list = execMethod('property.soadmin_location.select_location_type');

				array_unshift($district_list, array('id' => '', 'name' => lang('select')));
				array_unshift($part_of_town_list, array('id' => '', 'name' => lang('select')));
				array_unshift($location_type_list, array('id' => '', 'name' => lang('select')));


				$data = array
					(
					'msgbox_data' => $msgbox_data,
					'filter_form' => array
						(
						'district_list' => array('options' => $district_list),
						'part_of_town_list' => array('options' => $part_of_town_list),
						'location_type_list' => array('options' => $location_type_list),
					),
					'update_action' => self::link(array('menuaction' => 'controller.uicalendar.query'))
				);

				self::add_javascript('controller', 'controller', 'ajax_calendar_for_locations.js');

				self::render_template_xsl(array('calendar/view_calendar_aggregated'), $data);
			}
		}
		
		public function get_location_calendar_for_year($location_code, $year = null)
		{
			$role = phpgw::get_var('role', 'int');
			$repeat_type = phpgw::get_var('repeat_type');
			
			//show only controls that are not done
			$selected_ctrl_status = phpgw::get_var('current_ctrl_status');
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Validates repeat type.
			$repeat_type = $this->validate_repeat_type($repeat_type);

			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);

			// Array that will be populated with controls and calendar objects that will be sent to view
			$controls_calendar_array = array();

			// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);

			$level = $this->location_finder->get_location_level($location_code);

			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			// Fetches all controls for the location within time period
			$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type, "return_object", $role);

			// Loops through controls with repeat type day or week
			// and populates array that contains aggregated open cases pr month.
			foreach ($controls_for_location_array as $control)
			{
				if ($control->get_repeat_type() == controller_control::REPEAT_TYPE_DAY | $control->get_repeat_type() == controller_control::REPEAT_TYPE_WEEK)
				{
					$cl_criteria = new controller_check_list();
					$cl_criteria->set_control_id($control->get_id());
					$cl_criteria->set_location_code($location_code);

					$from_month = $this->get_start_month_for_control($control);
					$to_month = $this->get_end_month_for_control($control);

					// Loops through controls and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

					$year_calendar_agg = new year_calendar_agg($control, $year, $location_code, "VIEW_CONTROLS_FOR_LOCATION");
					$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);

					$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
				}
			}

			$repeat_type_expr = ">=2";
			// Fetches control ids with check lists for specified time period
			$control_id_with_check_list_array = $this->so->get_check_lists_for_location($location_code, $from_date_ts, $to_date_ts, $repeat_type_expr);

			// Loops through all controls for location and populates controls with check lists
			$controls_for_location_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);

			foreach ($controls_for_location_array as $control)
			{
				if ($control->get_repeat_type() == controller_control::REPEAT_TYPE_MONTH | $control->get_repeat_type() == controller_control::REPEAT_TYPE_YEAR)
				{
					$year_calendar = new year_calendar($control, $year, null, $location_code, "location");
					$calendar_array = $year_calendar->build_calendar($control->get_check_lists_array(), $selected_ctrl_status);

					$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
				}
			}

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations($location_code);

			$heading_array = year_calendar::get_heading_array();

			$roles_array = $this->so_control->get_roles();

			$repeat_type_array = array(
				array('id' => "0", 'value' => "Dag"),
				array('id' => "1", 'value' => "Uke"),
				array('id' => "2", 'value' => "Måned"),
				array('id' => "3", 'value' => "År")
			);

			$ctrl_status_array = array(
				array('id' => "CONTROL_REGISTERED", 'value' => "CONTROL_REGISTERED"),
				array('id' => "CONTROL_PLANNED", 'value' => "CONTROL_PLANNED"),
				array('id' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS"),
				array('id' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS", 'value' => "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS"),
				array('id' => "CONTROL_DONE_WITH_ERRORS", 'value' => "CONTROL_DONE_WITH_ERRORS"),
				array('id' => "CONTROL_NOT_DONE", 'value' => "CONTROL_NOT_DONE"),
				//array('id' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE", 'value' => "CONTROL_NOT_DONE_WITH_PLANNED_DATE"),
				array('id' => "CONTROL_CANCELED", 'value' => "CONTROL_CANCELED")
			);

			$data = array
				(
				'heading_array' => $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'roles_array' => $roles_array,
				'repeat_type_array' => $repeat_type_array,
				'ctrl_status_array' => $ctrl_status_array,
				'current_year' => $year
			);

			return $data;
		}

		public function view_calendar_year_for_locations()
		{
			static $_location_name = array();

			$control_id = phpgw::get_var('control_id', 'int');
			$control = $this->so_control->get_single($control_id);
			$year = phpgw::get_var('year', 'int');
			$location_id = phpgw::get_var('location_id', 'int');
			$component_id = phpgw::get_var('component_id', 'int');
			$serie_id = phpgw::get_var('serie_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$bookmarks = phpgwapi_cache::user_get('controller', "location_bookmark", $user_id);
			if (is_array($bookmarks))
			{
				$bookmark_locations = array_keys($bookmarks);
			}

			$location_code = phpgw::get_var('location_code', 'string');

			if ($location_code && $location_code != 'all')
			{
				$bookmark_locations[] = $location_code;
			}

			$locations_list = array();

			if (is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
				$components_for_control_array = $this->so_control->get_components_for_control($control_id, $location_id, $component_id, $serie_id);
				foreach ($locations_for_control_array as $location)
				{
					$locations_list[] = array
						(
						'id' => $location['location_code'],
						'name' => "{$location['location_code']} - {$location['loc_name']}",
						'selected' => $location_code == $location['location_code'] ? 1 : 0
					);
				}

				reset($locations_for_control_array);
				unset($location);
			}

			// Sort the data with account_lastname ascending, account_firstname ascending
			// Add $data as the last parameter, to sort by the common key
			if ($locations_list)
			{
				$locations_location_code = array();
				foreach ($locations_list as $key => $row)
				{
					$locations_location_code[$key] = $row['location_code'];
				}
				array_multisort($locations_location_code, SORT_ASC, $locations_list);

				array_unshift($locations_list, array(
						'id' => 'all',
						'name' => lang('all'),
						'selected' => $location_code == 'all' ? 1 : 0
				));
			}


			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);

			$locations_with_calendar_array = array();
			$locations_location_code = array();


			// LOCATIONS: Process aggregated values for controls with repeat type day or week
			if ($control->get_repeat_type() <= controller_control::REPEAT_TYPE_WEEK)
			{
				foreach ($locations_for_control_array as $location)
				{
					$curr_location_code = $location['location_code'];

					if($location_code != 'all')
					{
						if (!$bookmark_locations || !in_array($curr_location_code, $bookmark_locations))
						{
							continue;
						}
					}

					$cl_criteria = new controller_check_list();
					$cl_criteria->set_control_id($control->get_id());
					$cl_criteria->set_location_code($curr_location_code);

					$from_month = $this->get_start_month_for_control($control);
					$to_month = $this->get_end_month_for_control($control);

					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

					$year_calendar_agg = new year_calendar_agg($control, $year, $curr_location_code, "VIEW_LOCATIONS_FOR_CONTROL");
					$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);

					$locations_with_calendar_array[] = array(
						'location' => $location,
						'calendar_array' => $calendar_array,
						'selected' => $bookmarks && isset($bookmarks[$curr_location_code])
					);
					$locations_location_code[] = $location;

				}

				// COMPONENTS: Process aggregated values for controls with repeat type day or week
				foreach ($components_for_control_array as $component)
				{
					$short_desc_arr = execMethod('property.soentity.get_short_description', array(
						'location_id' => $component->get_location_id(), 'id' => $component->get_id()));
					if (!isset($_location_name[$component->get_location_code()]))
					{
						$_location = execMethod('property.solocation.read_single', $component->get_location_code());
						$location_arr = explode('-', $component->get_location_code());
						$i = 1;
						$name_arr = array();
						foreach ($location_arr as $_dummy)
						{
							$name_arr[] = $_location["loc{$i}_name"];
							$i++;
						}

						$_location_name[$component->get_location_code()] = implode('::', $name_arr);
					}

					$short_desc_arr .= ' [' . $_location_name[$component->get_location_code()] . ']';

					$component->set_xml_short_desc($short_desc_arr);

					$repeat_type = $control->get_repeat_type();
					$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

					$cl_criteria = new controller_check_list();
					$cl_criteria->set_control_id($control->get_id());
					$cl_criteria->set_component_id($component->get_id());
					$cl_criteria->set_location_id($component->get_location_id());

					$from_month = $this->get_start_month_for_control($control);
					$to_month = $this->get_end_month_for_control($control);

					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

					$year_calendar_agg = new year_calendar_agg($control, $year, $curr_location_code, "VIEW_LOCATIONS_FOR_CONTROL");
					$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);
					$components_with_calendar_array[] = array("component" => $component->toArray(),
						"calendar_array" => $calendar_array);
				}
			}
			// Process values for controls with repeat type month or year
			else if ($control->get_repeat_type() > controller_control::REPEAT_TYPE_WEEK)
			{
				foreach ($locations_for_control_array as $location)
				{
					$curr_location_code = $location['location_code'];

					if($location_code != 'all')
					{
						if (!$bookmark_locations || !in_array($curr_location_code, $bookmark_locations))
						{
							continue;
						}
					}

					$repeat_type = $control->get_repeat_type();
					$check_lists_array = $this->so->get_check_lists_for_control_and_location($control_id, $curr_location_code, $from_date_ts, $to_date_ts, $repeat_type);

					$year_calendar = new year_calendar($control, $year, null, $curr_location_code, "location");
					$calendar_array = $year_calendar->build_calendar($check_lists_array);

					$locations_with_calendar_array[] = array(
						'location' => $location,
						'calendar_array' => $calendar_array,
						'selected' => $bookmarks && isset($bookmarks[$curr_location_code])
					);
					$locations_location_code[] = $location;
				}

				foreach ($components_for_control_array as $component)
				{
					$short_desc_arr = execMethod('property.soentity.get_short_description', array(
						'location_id' => $component->get_location_id(), 'id' => $component->get_id()));

					//FIXME - make generic

					/* => */
					if (!isset($_location_name[$component->get_location_code()]))
					{
						$_location = execMethod('property.solocation.read_single', $component->get_location_code());
						$location_arr = explode('-', $component->get_location_code());
						$i = 1;
						$name_arr = array();
						foreach ($location_arr as $_dummy)
						{
							$name_arr[] = $_location["loc{$i}_name"];
							$i++;
						}

						$_location_name[$component->get_location_code()] = implode('::', $name_arr);
					}

					$short_desc_arr .= ' [' . $_location_name[$component->get_location_code()] . ']';
					/* <= */

					$component->set_xml_short_desc($short_desc_arr);

					$repeat_type = $control->get_repeat_type();
					$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

					$check_lists_array = $component_with_check_lists["check_lists_array"];

					/*
					 * start override control with data from serie
					 */
					$control_relation = $component->get_control_relation();
					if (isset($control_relation['start_date']) && $control_relation['start_date'])
					{
						$control->set_start_date($control_relation['start_date']);
					}

					if (isset($control_relation['end_date']) && $control_relation['end_date'])
					{
						$control->set_end_date($control_relation['end_date']);
					}
					if (isset($control_relation['repeat_type']) && $control_relation['repeat_type'])
					{
						$control->set_repeat_type($control_relation['repeat_type']);
					}
					if (isset($control_relation['repeat_interval']) && $control_relation['repeat_interval'])
					{
						$control->set_repeat_interval($control_relation['repeat_interval']);
					}

					/*
					 * End override control with data from serie
					 */

					$year_calendar = new year_calendar($control, $year, $component, null, "component");
					$calendar_array = $year_calendar->build_calendar($check_lists_array);

					$components_with_calendar_array[] = array("component" => $component->toArray(),
						"calendar_array" => $calendar_array);
				}
			}

			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations($location_code);

			$heading_array = year_calendar::get_heading_array();
			array_multisort($locations_location_code, SORT_ASC, $locations_with_calendar_array);

			$data = array
				(
				'locations_list' => $locations_list,
				'my_locations' => $my_locations,
				'control' => $control->toArray(),
				'heading_array' => $heading_array,
				'locations_with_calendar_array' => $locations_with_calendar_array,
				'components_with_calendar_array' => $components_with_calendar_array,
				'current_year' => $year,
				'location_code' => $location_code,
				'serie_id' => $serie_id,
			);

			self::render_template_xsl(array('calendar/view_calendar_year_for_locations', 'calendar/check_list_status_manager',
				'calendar/icon_color_map', 'calendar/select_my_locations', 'calendar/nav_calendar_year'), $data);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}

		public function view_calendar_month_for_locations()
		{
			static $_location_name = array();
			$control_id = phpgw::get_var('control_id', 'int');
			$control = $this->so_control->get_single($control_id);
			if (!$year = intval(phpgw::get_var('year')))
			{
				$year = date('Y');
			}
			if (!$month = intval(phpgw::get_var('month')))
			{
				$month = date('m');
			}

			$location_id = phpgw::get_var('location_id', 'int');
			$component_id = phpgw::get_var('component_id', 'int');
			$serie_id = phpgw::get_var('serie_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$bookmark_locations = array();
			$bookmarks = phpgwapi_cache::user_get('controller', "location_bookmark", $user_id);
			if (is_array($bookmarks))
			{
				$bookmark_locations = array_keys($bookmarks);
			}

			$location_code = phpgw::get_var('location_code', 'string');

			if ($location_code && $location_code != 'all')
			{
				$bookmark_locations[] = $location_code;
			}

			$locations_list = array();
			if (is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
				$components_for_control_array = $this->so_control->get_components_for_control($control_id, $location_id, $component_id, $serie_id);
				foreach ($locations_for_control_array as $location)
				{
					$locations_list[] = array
						(
						'id' => $location['location_code'],
						'name' => "{$location['location_code']} - {$location['loc_name']}",
						'selected' => $location_code == $location['location_code'] ? 1 : 0
					);
				}

				reset($locations_for_control_array);
				unset($location);
			}
			// Sort the data with account_lastname ascending, account_firstname ascending
			// Add $data as the last parameter, to sort by the common key
			if ($locations_list)
			{
				$locations_location_code = array();
				foreach ($locations_list as $key => $row)
				{
					$locations_location_code[$key] = $row['location_code'];
				}
				array_multisort($locations_location_code, SORT_ASC, $locations_list);

				array_unshift($locations_list, array(
						'id' => 'all',
						'name' => lang('all'),
						'selected' => $location_code == 'all' ? 1 : 0
				));
			}

			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);

			// Validates month. If year is not set, current month in current year is chosen
			$month = $this->validate_month($month);

			// Gets timestamp value of first day in month
			$from_date_ts = month_calendar::get_start_date_month_ts($year, intval($month));

			// Gets timestamp value of first day in month
			$to_date_ts = month_calendar::get_next_start_date_month_ts($year, intval($month));

			$locations_with_calendar_array = array();
			$locations_location_code = array();
			foreach ($locations_for_control_array as $location)
			{
				$curr_location_code = $location['location_code'];

				if($location_code != 'all')
				{
					if (!$bookmark_locations || !in_array($curr_location_code, $bookmark_locations))
					{
						continue;
					}
				}

				$repeat_type = $control->get_repeat_type();
				$check_lists_array = $this->so->get_check_lists_for_control_and_location($control_id, $curr_location_code, $from_date_ts, $to_date_ts, $control->get_repeat_type());

				$month_calendar = new month_calendar($control, $year, $month, null, $curr_location_code, "location");
				$calendar_array = $month_calendar->build_calendar($check_lists_array);

				$locations_with_calendar_array[] = array
					(
					'location' => $location,
					'calendar_array' => $calendar_array,
					'selected' => $bookmarks && isset($bookmarks[$curr_location_code])
				);

				$locations_location_code[] = $location;

			}

			foreach ($components_for_control_array as $component)
			{
				$short_desc_arr = execMethod('property.soentity.get_short_description', array(
					'location_id' => $component->get_location_id(), 'id' => $component->get_id()));
				if (!isset($_location_name[$component->get_location_code()]))
				{
					$_location = execMethod('property.solocation.read_single', $component->get_location_code());
					$location_arr = explode('-', $component->get_location_code());
					$i = 1;
					$name_arr = array();
					foreach ($location_arr as $_dummy)
					{
						$name_arr[] = $_location["loc{$i}_name"];
						$i++;
					}

					$_location_name[$component->get_location_code()] = implode('::', $name_arr);
				}

				$short_desc_arr .= ' [' . $_location_name[$component->get_location_code()] . ']';

				$component->set_xml_short_desc($short_desc_arr);

				$repeat_type = $control->get_repeat_type();
				$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

				$check_lists_array = $component_with_check_lists["check_lists_array"];

				/*
				 * Start override control with data from serie
				 */

				$control_relation = $component->get_control_relation();
				if (isset($control_relation['start_date']) && $control_relation['start_date'])
				{
					$control->set_start_date($control_relation['start_date']);
				}

				if (isset($control_relation['end_date']) && $control_relation['end_date'])
				{
					$control->set_end_date($control_relation['end_date']);
				}
				if (isset($control_relation['repeat_type']) && $control_relation['repeat_type'])
				{
					$control->set_repeat_type($control_relation['repeat_type']);
				}
				if (isset($control_relation['repeat_interval']) && $control_relation['repeat_interval'])
				{
					$control->set_repeat_interval($control_relation['repeat_interval']);
				}

				/*
				 * End override control with data from serie
				 */

				$month_calendar = new month_calendar($control, $year, $month, $component, null, "component");
				$calendar_array = $month_calendar->build_calendar($check_lists_array);


				$components_with_calendar_array[] = array("component" => $component->toArray(),
					"calendar_array" => $calendar_array);
			}

			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations($location_code);

			$heading_array = month_calendar::get_heading_array($year, $month);

			array_multisort($locations_location_code, SORT_ASC, $locations_with_calendar_array);

			$data = array
				(
				'control' => $control->toArray(),
				'my_locations' => $my_locations,
				'property_array' => $property_array,
				'location_array' => $location_array,
				'heading_array' => $heading_array,
				'locations_with_calendar_array' => $locations_with_calendar_array,
				'components_with_calendar_array' => $components_with_calendar_array,
				'current_year' => $year,
				'current_month_nr' => $month,
				'locations_list' => $locations_list,
				'location_code' => $location_code,
				'serie_id' => $serie_id,
			);

			self::render_template_xsl(array('calendar/view_calendar_month_for_locations',
				'calendar/check_list_status_manager',
				'calendar/icon_color_map', 'calendar/select_my_locations', 'calendar/nav_calendar_month'), $data);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}

		public function populate_controls_with_check_lists( $controls_for_location_array, $control_id_with_check_list_array )
		{
			$controls_with_check_list = array();

			foreach ($controls_for_location_array as $control)
			{
				foreach ($control_id_with_check_list_array as $control_id)
				{
					if ($control->get_id() == $control_id->get_id())
					{
						$control->set_check_lists_array($control_id->get_check_lists_array());
					}
				}

				$controls_with_check_list[] = $control;
			}

			return $controls_with_check_list;
		}

		// Generates array of aggregated number of open cases for each month in time period
		function build_agg_open_cases_pr_month_array( $cl_criteria, $year, $from_month, $to_month )
		{

			$agg_open_cases_pr_month_array = array();

			// Fetches aggregate value for open cases in each month in time period
			for ($from_month; $from_month <= $to_month; $from_month++)
			{
				$month_start_ts = $this->get_month_start_ts($year, $from_month);
				$month_end_ts = $this->get_month_start_ts($year, $from_month + 1);

				$num_open_cases_for_control_array = array();

				// Fetches aggregate value for open cases in a month from db
				$num_open_cases_for_control_array = $this->so_check_list->get_num_open_cases_for_control($cl_criteria, $month_start_ts, $month_end_ts);

				// If there is a aggregated value for the month, add aggregated status object to agg_open_cases_pr_month_array
				if (!empty($num_open_cases_for_control_array))
				{
					$status_agg_month_info = new status_agg_month_info();
					$status_agg_month_info->set_month_nr($from_month);
					$status_agg_month_info->set_agg_open_cases($num_open_cases_for_control_array["count"]);
					$agg_open_cases_pr_month_array[] = $status_agg_month_info;
				}
			}

			return $agg_open_cases_pr_month_array;
		}

		function get_start_month_for_control( $control )
		{
			// Checks if control starts in the year that is displayed
			if (date("Y", $control->get_start_date()) == $year)
			{
				$from_month = date("n", $control->get_start_date());
			}
			else
			{
				$from_month = 1;
			}

			return $from_month;
		}

		function get_end_month_for_control( $control )
		{
			// Checks if control ends in the year that is displayed
			if (date("Y", $control->get_end_date()) == $year)
			{
				$to_month = date("n", $control->get_end_date());
			}
			else
			{
				$to_month = 12;
			}

			return $to_month;
		}

		function validate_location_code( $location_code )
		{
			$criteria = array
				(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id' => 1,
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);

			if (empty($location_code))
			{
				$my_locations = $this->location_finder->get_responsibilities($criteria);
				if (count($my_locations) > 0)
				{
					$location_code = $my_locations[0]["location_code"];
				}
			}

			return $location_code;
		}

		function get_my_assigned_locations( $current_location_code )
		{
			$criteria = array
				(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'], //
				'type_id' => 1, // Nivå i bygningsregisteret 1:eiendom
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);

			$my_locations = $this->location_finder->get_responsibilities($criteria);

			$my_washed_locations = array();

			foreach ($my_locations as $location)
			{
				if ($location['location_code'] != $current_location_code && $current_location_code != 'all')
				{
					$my_washed_locations[] = $location;
				}
			}

			return $my_washed_locations;
		}

		function get_month_start_ts( $year, $month )
		{
			if ($month > 12)
			{
				$year = $year + 1;
				$month = $month % 12;
			}

			return strtotime("$month/01/$year");
		}

		function get_start_date_year_ts( $year )
		{
			return strtotime("01/01/$year");
		}

		function get_end_date_year_ts( $year )
		{
			$to_year = $year + 1;
			$end_date_year_ts = strtotime("01/01/$to_year");

			return $end_date_year_ts;
		}

		function validate_year( $validate_year )
		{
			if (empty($validate_year))
			{
				$validate_year = date("Y");
			}

			$validate_year = intval($validate_year);

			return $validate_year;
		}

		function validate_repeat_type( $validate_repeat_type )
		{
			if ($validate_repeat_type != 0 & (empty($validate_repeat_type) | ($validate_repeat_type > 3)))
			{
				$validate_repeat_type = '';
			}

			return $validate_repeat_type;
		}

		function validate_role( $validate_role )
		{
			if (empty($validate_role) | (!is_numeric($validate_role)) | ($validate_role < 1))
			{
				$validate_role = '';
			}

			return $validate_role;
		}

		function validate_month( $month )
		{
			if (empty($month))
			{
				$month = date("n");
			}

			$month = intval($month);

			return $month;
		}

		public function query()
		{
			$year = date('Y');
			$from_date_ts = mktime(0, 0, 0, 1, 1, $year);
			$to_date_ts = mktime(0, 0, 0, 1, 1, $year+1);
			$loc_array = array();
			$loc_checklist_array = array();

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'order' => $columns[$order[0]['column']]['data'],
				'sort' => $order[0]['dir'],
				'allrows' => true,
				'district_id' => phpgw::get_var('district_id', 'int'),
				'cat_id' => phpgw::get_var('cat_id', 'int'),
				'status' => phpgw::get_var('status'),
				'part_of_town_id' => phpgw::get_var('part_of_town_id', 'int'),
				'location_code' => phpgw::get_var('location_code'),
				'type_id' => phpgw::get_var('location_level', 'int', 'REQUEST', 1)
			);

			$values = $this->bo->read($params);

			foreach ($values as &$entry)
			{
				$loc_array[] = $entry['location_code'];
			}
			
			foreach ($loc_array as $curr_loc)
			{
				//get checklists for found locations
				$loc_checklist_array[$curr_loc] = $this->get_location_calendar_for_year($curr_loc, $year);
				$loc_checklist_array[$curr_loc]['loc_name'] = $this->bo->get_location_name($curr_loc);
				
			}
			
			foreach ($loc_checklist_array as &$curr_loc_checklist)
			{
				if(empty($curr_loc_checklist['controls_calendar_array']))
				{
					$curr_loc_checklist = [];
				}
			}
//			_debug_array($loc_checklist_array);
		//die();
			
			$result_data = array
				(
				'results' => $loc_checklist_array
			);
			
			$this->view_calendar_for_year2($loc_checklist_array, $year);
			
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'controller.uicalendar.view_calendar_for_year2', 'location_array' => $loc_checklist_array));
			
			

			//return $this->jquery_results($result_data);
			//return $this->jquery_results($loc_checklist_array);
		}

		public function update_bookmark()
		{
			$location_code = phpgw::get_var('location_code', 'string');
			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			$bookmarks = phpgwapi_cache::user_get('controller', "location_bookmark", $user_id);
			if ($bookmarks && is_array($bookmarks) && isset($bookmarks[$location_code]))
			{
				unset($bookmarks[$location_code]);
				$status = lang('deleted');
			}
			else
			{
				if (!is_array($bookmarks))
				{
					$bookmarks = array();
				}

				$bookmarks[$location_code] = true;
				$status = lang('added');
			}

			phpgwapi_cache::user_set('controller', "location_bookmark", $bookmarks, $user_id);

			return array
				(
				'status' => $status
			);
		}
	}