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
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'component', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/component/');
	include_class('controller', 'status_agg_month_info', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
	include_class('controller', 'year_calendar', 'inc/component/');
	include_class('controller', 'year_calendar_agg', 'inc/component/');
	include_class('controller', 'month_calendar', 'inc/component/');

	class controller_uicalendar_planner extends phpgwapi_uicommon_jquery
	{
		private $dayLabels		 = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
		private $currentYear	 = 0;
		private $currentMonth	 = 0;
		private $currentDay		 = 0;
		private $currentDate	 = null;
		private $daysInMonth	 = 0;

		protected $read, $add, $edit, $delete, $so, $so_control, $dateformat;

		public $public_functions = array
			(
			'index'					 => true,
			'monthly'				 => true,
			'send_notification'		 => true,
			'save_send_notification' => true,
			'query'					 => true,
			'update_schedule'		 => true,
			'start_inspection'		 => true,
			'ad_hoc'				 => true,
			'inspection_history'	 => true
		);

		public function __construct()
		{
			parent::__construct();

			$this->read	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller'); //1
			$this->add	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller'); //2
			$this->edit	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller'); //4
			$this->delete	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller'); //8

			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$manage = $GLOBALS['phpgw']->acl->check('.control', 16, 'controller'); //16

			self::set_active_menu('controller::calendar_planner');
		}

		public function index()
		{

			if(!$this->read)
			{
				phpgw::no_access();
			}

			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = (array)phpgw::get_var('part_of_town_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_area_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_control_area_id", $control_area_id, $user_id);
			}
			else
			{
				$control_area_id = (int)phpgwapi_cache::user_get('controller', "calendar_control_area_id", $user_id);
			}
			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);
			}

			if($part_of_town_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_part_of_town", $part_of_town_id, $user_id);
			}
			else if ($_POST)
			{
				phpgwapi_cache::user_clear('controller', "calendar_planner_part_of_town", $user_id);
			}
			else
			{
				$part_of_town_id = (array)phpgwapi_cache::user_get('controller', "calendar_planner_part_of_town", $user_id);
			}

			$entity_group_id = phpgw::get_var('entity_group_id', 'int');
			$current_year	 = phpgw::get_var('current_year', 'int', 'REQUEST', date('Y'));

			if (phpgw::get_var('prev_year', 'bool'))
			{
				$current_year --;
			}
			if (phpgw::get_var('next_year', 'bool'))
			{
				$current_year ++;
			}

			$control_types = $this->so_control->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}


			$first_half_year = array();
			for ($i = 1; $i <= 6; $i++)
			{
				$first_half_year[] = array(
					'id'	 => $i,
					'name'	 => lang(date('F', mktime(0, 0, 0, $i, 1))),
					'url'	 => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
						'year'		 => $current_year,
						'month'		 => $i))
				);
			}

			$second_half_year = array();
			for ($i = 7; $i <= 12; $i++)
			{
				$second_half_year[] = array(
					'id'	 => $i,
					'name'	 => lang(date('F', mktime(0, 0, 0, $i, 1)))
				);
			}


			$entity_groups = createObject('property.bogeneric')->get_list(array('type'		 => 'entity_group',
				'selected'	 => $entity_group_id, 'order'		 => 'name', 'sort'		 => 'asc'));

			$part_of_towns = createObject('property.bogeneric')->get_list(array(
				'type'		 => 'part_of_town',
				'selected'	 => $part_of_town_id,
				'order'		 => 'name',
				'sort'		 => 'asc'
				)
			);

			$part_of_town_list	 = array();
			$part_of_town_list2	 = array();
			foreach ($part_of_towns as &$part_of_town)
			{
				if ($part_of_town['id'] > 0)
				{
					$part_of_town['name']		 = ucfirst(strtolower($part_of_town['name']));
					$part_of_town_list[]		 = $part_of_town;

					if ($part_of_town['selected'])
					{
						/**
						 * By reference
						 */
						$this->get_planned_status($part_of_town, $current_year, $control_id);
						$part_of_town_list2[] = $part_of_town;
					}
				}
			}

			unset($part_of_town);


			$calendar_content1 = array();
			$calendar_content2 = array();


			foreach ($part_of_town_list2 as $key => $part_of_town)
			{
				$calendar_content1[] = array(
					'header' => $part_of_town['name'],
					'cell_data' => array_slice($part_of_town['planned_status'], 0, 6)
					);
				$calendar_content2[] = array(
					'header' => $part_of_town['name'],
					'cell_data' => array_slice($part_of_town['planned_status'], -6)
					);
			}

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));

			$data = array
			(
				'control_area_list'	 => array('options' => $control_area_list),
				'prev_year'			 => $current_year - 1,
				'current_year'		 => $current_year,
				'next_year'			 => $current_year + 1,
				'first_half_year'	 => $first_half_year,
				'second_half_year'	 => $second_half_year,
				'entity_group_list'	 => array('options' => $entity_groups),
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'form_action'		 => self::link(array('menuaction' => 'controller.uicalendar_planner.index')),
				'control_type_list'	 => array('options' => $control_type_list),
				'calendar_content1'	 => array('rows' => $calendar_content1),
				'calendar_content2'	 => array('rows' => $calendar_content2)
			);

			phpgwapi_jquery::load_widget('bootstrap-multiselect');
			self::add_javascript('controller', 'base', 'calendar_planner.start.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner');
			self::render_template_xsl(array('calendar/calendar_planner'), array('start' => $data));
		}

		private function get_planned_status( &$part_of_town, $year, $control_id )
		{
			$part_of_town_id = (int)$part_of_town['id'];

			$items = $this->get_items($year, $i, $control_id, 0, $part_of_town_id);

			$planned_status = array();
			for ($i = 1; $i <= 12; $i++)
			{
				$planned_status[$i]['part_of_town_id'] = $part_of_town_id;
				$planned_status[$i]['month'] = $i;
				$planned_status[$i]['registered'] = 0;
				$planned_status[$i]['planned'] = 0;
				$planned_status[$i]['completed'] = 0;
			}

			foreach ($items as $date => $components)
			{
				$_month = date('n', strtotime($date));
				$planned_status[$_month]['registered'] += count($components);

				foreach ($components as $component)
				{
					if($component['schedule']['info']['planned_date_ts'])
					{
						$planned_status[$_month]['planned'] += 1;
					}
					if($component['schedule']['info']['completed_date_ts'])
					{
						$planned_status[$_month]['completed'] += 1;
					}
				}
			}

			$part_of_town['planned_status'] = $planned_status;
		}


		/**
		 * Note: prelimilary test
		 * @param type $part_of_towns
		 * @param type $month
		 * @param type $year
		 * @param type $control_id
		 */

		private function get_scheduled_controls( $part_of_towns, $month, $year, $control_id  )
		{

			$month = $month ? $month : date('m');
			$year = $year ? $year : date('Y');
			$daysInMonth	 = $this->_daysInMonth($month, $year);

			$items = array();

			foreach ($part_of_towns as $part_of_town_id)
			{
				$_items = $this->get_items($year, $month, $control_id, 0, $part_of_town_id);

				$items = array_merge_recursive($items, $_items);
			}
//			_debug_array($items);
			$sort_names = array();
			$controls = array();

			for ($day = 1; $day <= $daysInMonth; $day++)
			{
				$date_string = date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));

				if(!empty($items[$date_string]))
				{
					$i=1;
					foreach ($items[$date_string] as $item)
					{
						$desc = str_replace(':', ':<br/>', $item['component']['xml_short_desc']);

						if($item['schedule']['info']['check_list_id'])
						{
							$control_link_data = array
								(
								'menuaction' => 'controller.uicase.add_case',
								'check_list_id' => $item['schedule']['info']['check_list_id'],
							);
							$url_target = '_self';
							$link = $GLOBALS['phpgw']->link('/index.php', $control_link_data);
						}
						else
						{
							$link = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'controller.uicheck_list.add_check_list',
									'deadline_ts'	 => $item['schedule']['info']['deadline_date_ts'],
									'control_id'	 => $item['schedule']['info']['control_id'],
									'serie_id'		 => $item['schedule']['info']['serie_id'],
									'location_id'	 => $item['component']['location_id'],
									'component_id'	 => $item['component']['id'],
									'location_code'	 => $item['component']['location_code'],
									'assigned_to'	 => $item['schedule']['info']['assigned_to'],
									'type'			 => 'component'
								));

						}

						switch ($item['schedule']['status'])
						{
							case 'CONTROL_REGISTERED':
								$status = 'Registrert';
								break;

							case 'CONTROL_NOT_DONE_WITH_PLANNED_DATE':
							case 'CONTROL_PLANNED':
								$status = 'Planlagt';
								break;

							case 'CONTROL_NOT_DONE':
								$status = 'Ikke utført';

							case "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS":
							case "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS":
							case "CONTROL_DONE_WITH_ERRORS":
							case 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS':
								$status = 'Utført';
								break;

							case "CONTROL_CANCELED":
								$status = 'Kansellert';
								break;
						}

						$item_content = array(
							'id'				 => "{$item['component']['location_id']}_{$item['component']['id']}",
							'control_id'		 => $item['schedule']['info']['control_id'],
							'serie_id'			 => $item['schedule']['info']['serie_id'],
							'check_list_id'		 => $item['schedule']['info']['check_list_id'],
							'deadline_date_ts'	 => $item['schedule']['info']['deadline_date_ts'],
							'deadline_date'		 => $GLOBALS['phpgw']->common->show_date($item['schedule']['info']['deadline_date_ts'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
							'completed_date_ts'  =>	$component['schedule']['info']['completed_date_ts'],
							'assigned_to'		 => $item['schedule']['info']['assigned_to'],
							'link'				 => $link,
							'name'				 => $desc,
							'status'			 => $status
						);

						$i++;

						$sort_names[] = $desc;
						$controls[] = $item_content;
					}
				}

			}

			array_multisort($sort_names, SORT_ASC, $controls);

			return $controls;
		}

		private function get_inspection_history( $selected_part_of_town, $control_id , $start = 0, $query = '',$deviation = null, $allrows = null)
		{

			$historic_check_lists = $this->so->get_historic_check_lists($control_id, $selected_part_of_town, $start, $query,$deviation, $allrows);

			return $historic_check_lists;
		}


		public function monthly()
		{
			if(!$this->read)
			{
				phpgw::no_access();
			}

			$month	 = phpgw::get_var('month', 'int', 'REQUEST', date("m", time()));
			$year	 = phpgw::get_var('year', 'int', 'REQUEST', date("Y", time()));
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');
			$control_area_id = phpgw::get_var('control_area_id', 'int');
			$control_id		 = phpgw::get_var('control_id', 'int');
			$entity_group_id = phpgw::get_var('entity_group_id', 'int');

			$part_of_town = createObject('property.bogeneric')->get_single_name(array('type' => 'part_of_town',	'id' => $part_of_town_id));

			if (13 == $month)
			{
				$month	 = 1;
				$year	 += 1;
			}

			if ((string)$_REQUEST['month'] === '0')
			{
				$month	 = 12;
				$year	 -= 1;
			}


			$items = $this->get_items($year, $month, $control_id, $entity_group_id, $part_of_town_id);


			$data = array
			(
				'part_of_town_id' => $part_of_town_id,
				'part_of_town' => $part_of_town,
				'current_month'	 => lang(date('F', mktime(0, 0, 0, $month, 1))),
				'current_year'	 => $year,
				'next_month_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
					'year' => $year,
					'month' => ($month + 1),
					'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id)),
				'prev_month_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
					'year' => $year,
					'month' => ($month - 1),
					'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id)),
				'next_month'	 => lang(date('F', mktime(0, 0, 0, $month + 1, 1))),
				'prev_month'	 => lang(date('F', mktime(0, 0, 0, $month - 1, 1))),
				'calendar' => $this->show($year, $month, $items),

				'start_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.index',
					'current_year' => $year,
			//		'month' => $month,
			//		'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id)),
				'send_notification_url'  => self::link(array('menuaction' => 'controller.uicalendar_planner.send_notification',
					'current_year' => $year,
					'month' => $month,
					'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id)),
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'calendar_planner.monthly.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner') . '::' . lang('monthly');
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = 'controller::calendar_planner::monthly';

			self::render_template_xsl(array('calendar/calendar_planner'), array('monthly' => $data));
		}

		private function get_items( $year, $month, $control_id,  $entity_group_id, $part_of_town_id , $items = array(),$from_date_ts = 0, $to_date_ts = 0)
		{

			// Validates year. If year is not set, current year is chosen
			$year = (int)$year ? $year : date('Y');

			// Gets timestamp of first day in month
//			$from_date_ts = strtotime("01/{$month}/$year");
			if(!$from_date_ts)
			{
				$from_date_ts = strtotime("01/01/$year");
			}

			if(!$to_date_ts)
			{
				$daysInMonths	 = $this->_daysInMonth($month, $year);
				// Gets timestamp of last day in month
				$to_date_ts = mktime(23, 59, 59, 12, 31, $year);
			}

			if(!$items)
			{
				$locations = $this->so_control->get_system_locations_related_to_control($control_id);
				foreach ($locations as $location_id)
				{
					$_items		 =  execMethod('property.soentity.read',array(
			//				'entity_group_id' => (int)$entity_group_id,
							'location_id' => $location_id,
							'control_id' => $control_id,
							'district_id' => $district_id,
							'part_of_town_id' => $part_of_town_id,
			//				'location_code'	=> $location_code,
			//				'org_units' => $this->org_units,
							'allrows' => true,
							'control_registered' => true,
							'check_for_control' => true
							)
						);
					$items			 = array_merge($items, $_items);
				}
			}

			$controls = array();

			$all_components = array();
			$control_names = array();

			$item_calendar = array();
			$repeat_type_array = array
				(
				"0" => lang('day'),
				"1" => lang('week'),
				"2" => lang('month'),
				"3" => lang('year')
			);
			$duplicate_calendar = array();
			$edited_desc = array();

//			_debug_array($items);
			foreach ($items as $_item)
			{
				$location_id = $_item['location_id'];

				$item_id = $_item['id'];
				$all_components["{$location_id}_{$item_id}"] = $_item;


				$short_description = $_item['location_name'];
				$short_description = '';

				if(empty($get_locations))
				{
//					$short_description .= '<br/>' . $_item['short_description'];
//					$short_description .= ' [' . $_item['location_name'] . ']';
					$short_description .= $_item['short_description'];
				}
				else
				{
					$short_description .= '<br/>' . $_item['location_code'];
					$short_description .= ' [' . $_item['loc1_name'] . ']';

				}

				if (empty($get_locations) && ($all_items && !$_item['has_control']))
				{
		//			continue;
				}

				if (!$_item['id'])
				{
					continue;
				}

				$controls_at_component = $this->so_control->get_controls_at_component2($_item, $control_id);

				foreach ($controls_at_component as $component)
				{
					$control_relation = $component->get_control_relation();

					if (!$control_relation['serie_enabled'])
					{
						continue;
					}
					$control_id = $control_relation['control_id'];

					if(isset($controls[$control_id]))
					{
						$control = $controls[$control_id];
					}
					else
					{
						$control = $this->so_control->get_single($control_id);
						$controls[$control_id] = $control;
					}

					$control_names[$control_id] = $control->get_title();

//					$repeat_type = $control->get_repeat_type();
					$repeat_type = (int)$control_relation['repeat_type'];

					//FIXME: Not currently supported
					if ($repeat_type <= controller_control::REPEAT_TYPE_WEEK)
					{
						$repeat_type = controller_control::REPEAT_TYPE_MONTH;
					}
					// LOCATIONS: Process aggregated values for controls with repeat type day or week
					if ($repeat_type <= controller_control::REPEAT_TYPE_WEEK)
					{
						//FIXME: Not currently supported

						$component->set_xml_short_desc($short_description);

						$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

						$cl_criteria = new controller_check_list();
						$cl_criteria->set_control_id($control->get_id());
						$cl_criteria->set_component_id($component->get_id());
						$cl_criteria->set_location_id($component->get_location_id());

						$from_month = $this->get_start_month_for_control($control);
						$to_month = $this->get_end_month_for_control($control);

						// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
						$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($cl_criteria, $year, $from_month, $to_month);

						$year_calendar_agg = new year_calendar_agg($control, $year, $location_code, "VIEW_LOCATIONS_FOR_CONTROL");
						$calendar_array = $year_calendar_agg->build_calendar($agg_open_cases_pr_month_array);
					}
					// Process values for controls with repeat type month or year
					else if ($repeat_type > controller_control::REPEAT_TYPE_WEEK)
					{
						$component->set_xml_short_desc($short_description);

						$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);// ,$user_id);

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

						$year_calendar = new year_calendar($control, $year, $component, null, "component", $control_relation);
						$calendar_array = $year_calendar->build_calendar($check_lists_array);
						foreach ($calendar_array as $_month => $_month_info)
						{
							if($month && (int)$month !== $_month)
							{
								continue;
							}

							if($_month_info)
							{
								//deadline

								if(!empty($_month_info['info']['original_deadline_date_ts']))
								{
									$deadline_date_ts = $_month_info['info']['original_deadline_date_ts'];
								}
								else
								{
									$deadline_date_ts = $_month_info['info']['deadline_date_ts'];

								}

								if(!empty($_month_info['info']['completed_date_ts']))
								{
									$shedule_date = date('Y-m-d', $_month_info['info']['completed_date_ts']);
								}
								else if(!empty($_month_info['info']['planned_date_ts']))
								{
									$shedule_date = date('Y-m-d', $_month_info['info']['planned_date_ts']);
								}
								else
								{
									$shedule_date = date('Y-m-d', $_month_info['info']['deadline_date_ts']);
								}

								if(isset($duplicate_calendar[$deadline_date_ts][$component->get_location_id()][$component->get_id()]))
								{
									continue;
								}
								$duplicate_calendar[$deadline_date_ts][$component->get_location_id()][$component->get_id()] = true;

								$serie_id  = $_month_info['info']['serie_id'];

								if($serie_id && !isset($edited_desc[$serie_id][$component->get_id()]))
								{
									$serie = $this->so_control->get_serie($serie_id);
									if($serie['repeat_type'] == 3)
									{
										$repeat_descr = 'Årskontroll';
									}
									else
									{
										$repeat_descr = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
									}
									$component->set_xml_short_desc($component->get_xml_short_desc() . ' (' . $repeat_descr . ')');
									$edited_desc[$serie_id][$component->get_id()] = true;
								}

								$item_calendar["{$shedule_date}"][] = array(
									'component' => $component->toArray(),
									'schedule' => $_month_info
									);
							}
						}
					}
				}
			}
//			_debug_array($item_calendar);
			return $item_calendar;

		}

		public function send_notification()
		{
			if(!$this->read)
			{
				phpgw::no_access();
			}

			$month	 = phpgw::get_var('month', 'int', 'REQUEST', date("m", time()));
			$year	 = phpgw::get_var('year', 'int', 'REQUEST', date("Y", time()));
			$part_of_town_id = phpgw::get_var('part_of_town_id', 'int');
			$control_area_id = phpgw::get_var('control_area_id', 'int');
			$control_id		 = phpgw::get_var('control_id', 'int');
			$entity_group_id = phpgw::get_var('entity_group_id', 'int');


			$items = $this->get_items($year, $month, $control_id, $entity_group_id, $part_of_town_id);
//			_debug_array($items);
			$components = array();

			foreach ($items as $date => $_components)
			{
				$_month = date('n', strtotime($date));

				if($_month != $month)
				{
					continue;
				}

				$components = array_merge($components,$_components );
			}

//			_debug_array($components);

			$soentity = createObject('property.soentity');

			$control_info =array();
			foreach ($components as $component)
			{
				$timestamp = $component['schedule']['info']['planned_date_ts'] ? $component['schedule']['info']['planned_date_ts'] : $component['schedule']['info']['deadline_date_ts'];

				$date = $GLOBALS['phpgw']->common->show_date($timestamp, $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']);

				$control_info[] = array(
					'id' => $component['component']['id'],
					'location_id' => $component['component']['location_id'],
					'location_code' => $component['component']['location_code'],
					'address' => "{$component['component']['address']}:{$component['component']['xml_short_desc']}",
					'deadline_date_ts' => $component['schedule']['info']['deadline_date_ts'],
					'planned_date_ts' => $component['schedule']['info']['planned_date_ts'],
					'date' => $date,
					'timestamp' => $timestamp,
					'email'	=> $soentity->get_json_attribute($component['component']['location_id'], $component['component']['id'], 'notification_email'),
					'selected' => 1
					);
			}

//			_debug_array($control_info);

			$data = array(
				'monthly_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
					'year' => $year,
					'month' => $month,
					'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id)),
				'form_action'  => self::link(array('menuaction' => 'controller.uicalendar_planner.save_send_notification',
					'current_year' => $year,
					'month' => $month,
					'part_of_town_id' => $part_of_town_id,
					'control_id' => $control_id,
					'control_area_id' => $control_area_id,
					'entity_group_id'=> $entity_group_id,
					'phpgw_return_as' => 'json')),
				'control_info' => $control_info,
				'control_id'	=> $control_id,
			);

			phpgwapi_jquery::load_widget('autocomplete');

			self::add_javascript('controller', 'base', 'calendar_planner.send_notification.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner') . '::' . lang('send notification');
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = 'controller::calendar_planner::send_notification';
			self::render_template_xsl(array('calendar/calendar_planner'), array('notification' => $data));
		}


		public function save_send_notification()
		{
			if(!$this->edit)
			{
				phpgw::no_access();
			}

			$email = (array)phpgw::get_var('email');
			$send_email = (array)phpgw::get_var('send_email');
			$timestamp = (array)phpgw::get_var('timestamp');
			$control_id		 = phpgw::get_var('control_id', 'int');

			$soentity = createObject('property.soentity');

			$send = CreateObject('phpgwapi.send');

			$control = $this->so_control->get_single($control_id);

			$subject = $control->get_send_notification_subject();
			$content = $control->get_send_notification_content();

			if(!$subject)
			{
				throw new Exception('Missing subject');
			}
			if(!$content)
			{
				throw new Exception('Missing content');
			}

			$html = <<<HTML
			<!DOCTYPE html>
			<html>
				<head>
				</head>
				<body>
				{$content}
				</body>
			<html>
HTML;

			$receipt = array();
			foreach ($send_email as $item => $value)
			{
				$item_arr = explode('_', $item);
				$component_arr = $soentity->read_single_eav( array(
					'location_id' => $item_arr[0],
					'id' => $item_arr[1]));

				$location_data = explode('-', $component_arr['location_code']);

				$location_name = execMethod('property.bolocation.get_location_name', $location_data[0]);

				$notification_email = $soentity->get_json_attribute($item_arr[0], $item_arr[1], 'notification_email');

				if($notification_email != $email[$item])
				{
					$soentity->update_json_attribute($item_arr[0], $item_arr[1], 'notification_email', $email[$item]);
				}

				if($email[$item])
				{
					$week = date("W", $timestamp[$item]);

					$html = str_replace(array('__location__', '__week__', '__sender__'), array($location_name, $week, $GLOBALS['phpgw_info']['user']['fullname']), $html);

					$toarray = array($email[$item]);
					$to		 = implode(';', $toarray);
					try
					{
						$rc	 = $send->msg('email', $to, $subject, $html, '', $cc = '', $bcc = '', 'IkkeSvar@bergen.kommune.no', 'Ikke svar', 'html');
						$receipt['ok'][] = $item;
					}
					catch (Exception $e)
					{
						$receipt['error'][] = $item;
					}
				}
				else
				{
					$receipt['error'][] = $item;
				}

			}

			return $receipt;
		}

		public function query()
		{

		}

		public function update_schedule()
		{
			if (!$this->add && !$this->edit)
			{
				phpgw::no_access();
			}

			$location_id = phpgw::get_var('location_id', 'int');
			$component_id = phpgw::get_var('component_id', 'int');
			$target_date = phpgw::get_var('target_date');
			$deadline_date_ts = phpgw::get_var('deadline_date_ts');
			$serie_id = phpgw::get_var('serie_id', 'int');
			$check_list_id = phpgw::get_var('check_list_id', 'int');


			$submit_ok = true;

			$control_id = phpgw::get_var('control_id', 'int');
			$status = 0;
			$original_deadline_date_ts = phpgw::get_var('original_deadline_date', 'int');
			$comment = phpgw::get_var('comment', 'string');
			$assigned_to = phpgw::get_var('assigned_to', 'int');

			$planned_date_ts = strtotime($target_date);

			$error = false;

			$completed_date_ts = 0;

			if($submit_ok)
			{
	//			$status = controller_check_list::STATUS_NOT_DONE;
			}

			if ($check_list_id > 0)
			{
				$check_list = $this->so->get_single($check_list_id);

				$serie_id = $check_list->get_serie_id();

				if(!$planned_date_ts)
				{
					$planned_date_ts = $check_list->get_planned_date();
				}
				if(!$original_deadline_date_ts)
				{
					$original_deadline_date_ts = $check_list->get_original_deadline();
				}
				if(!$deadline_date_ts)
				{
					$deadline_date_ts = $check_list->get_deadline();
				}
				if(!$comment)
				{
					$comment = $check_list->get_comment();
				}

			}
			else
			{
				if(!$original_deadline_date_ts)
				{
					$original_deadline_date_ts = $deadline_date_ts;
				}

				$completed_date_ts = 0;

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$serie = $this->so_control->get_serie($serie_id);


				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;

					$type_info = explode('.', $location_info['location']);
					$level = $type_info[2];
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
						'id' => $component_id));

					$location_code = $component_arr['location_code'];
					$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
					$level = count(explode('-',$location_code));

					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));

				}

				$check_list->set_location_code($location_code);
				$check_list->set_serie_id($serie_id);

				$check_list->set_location_id($location_id);
				$check_list->set_component_id($component_id);
			}

			$serie = $this->so_control->get_serie($serie_id);

			if($serie && $serie['repeat_type'] == 3) // Year
			{
				/**
				 * Move deadline to end of year
				 */
				$_deadline_year = date('Y', $deadline_date_ts);
				$deadline_date_ts = strtotime("{$_deadline_year}-12-31");
			}

			$check_list->set_comment($comment);
			$check_list->set_deadline($deadline_date_ts);
			$check_list->set_original_deadline($original_deadline_date_ts);
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);

			$orig_assigned_to = $check_list->get_assigned_to();

			$check_list->set_assigned_to($assigned_to);

			if ($status == controller_check_list::STATUS_CANCELED && !$error)
			{
				$check_list->set_status($status);
			}
			else if ($status == controller_check_list::STATUS_NOT_DONE && !$error)
			{
				$check_list->set_status($status);
			}

			if (!$error && $check_list->validate())
			{
				$check_list_id = $this->so->store($check_list);
				$serie = $this->so_control->get_serie($check_list->get_serie_id());

				if ($check_list_id > 0)
				{
					if($submit_ok)
					{
						$check_list->set_id($check_list_id);
					}

					return array(
						"status" => 'ok',
						'message' => lang('Ok'),
						'check_list_id' => $check_list_id
						);
				}
				else
				{
					if (phpgw::get_var('phpgw_return_as') == 'json')
					{
						return array('status' => 'error', 'message' => $error_message ? $error_message : lang('Error'));
					}
				}
			}
			else
			{
				return array('status' => 'error', 'message' => $error_message ? $error_message : lang('Error'));
			}

		}

		public function inspection_history( )
		{
			if (!$this->read)
			{
				phpgw::no_access();
			}

			self::set_active_menu('controller::inspection_history');
			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = (array)phpgw::get_var('part_of_town_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_area_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_control_area_id", $control_area_id, $user_id);
			}
			else
			{
				$control_area_id = (int)phpgwapi_cache::user_get('controller', "calendar_control_area_id", $user_id);
			}
			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);
			}

			if($part_of_town_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_part_of_town", $part_of_town_id, $user_id);
			}
			else if ($_POST)
			{
				phpgwapi_cache::user_clear('controller', "calendar_planner_part_of_town", $user_id);
			}
			else
			{
				$part_of_town_id = (array)phpgwapi_cache::user_get('controller', "calendar_planner_part_of_town", $user_id);
			}

			$entity_group_id = phpgw::get_var('entity_group_id', 'int');


			$control_types = $this->so_control->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}


			$entity_groups = createObject('property.bogeneric')->get_list(array('type'		 => 'entity_group',
				'selected'	 => $entity_group_id, 'order'		 => 'name', 'sort'		 => 'asc'));

			$part_of_towns = createObject('property.bogeneric')->get_list(array(
				'type'		 => 'part_of_town',
				'selected'	 => $part_of_town_id,
				'order'		 => 'name',
				'sort'		 => 'asc'
				)
			);

			$part_of_town_list	 = array();
			$selected_part_of_town	 = array();
			foreach ($part_of_towns as $part_of_town)
			{
				if ($part_of_town['id'] > 0)
				{
					$part_of_town_list[]		 = $part_of_town;
					if ($part_of_town['selected'])
					{
						$selected_part_of_town[] = $part_of_town['id'];
					}
				}
			}
			unset($part_of_town);

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));

			$query		= phpgw::get_var('query', 'string');
			$start		= phpgw::get_var('start', 'int', 'GET', 0);
			$order		= phpgw::get_var('order', 'string', 'GET', 'account_lid');
			$sort		= phpgw::get_var('sort', 'string', 'GET', 'ASC');
			$allrows	= phpgw::get_var('allrows', 'bool');
			$deviation	= phpgw::get_var('deviation', 'bool');

			$history_content = $this->get_inspection_history($selected_part_of_town, $control_id, $start, $query,$deviation, $allrows);
			$total = $this->so->total_records;


			$condition_degree = 0;

			$soentity = createObject('property.soentity');

			foreach ($history_content as &$entry)
			{
				if($entry['location_id'])
				{
					$entry['loc1_name'] = $entry['loc1_name'] . '::' . $soentity->get_short_description(
						array
						(
							'location_id' => $entry['location_id'],
							'id' => $entry['component_id']
						)
					);
				}

				if(isset($entry['findings_summary']['condition_degree'][1]))
				{
					$entry['findings_summary']['condition_degree_1'] = $entry['findings_summary']['condition_degree'][1];
					$condition_degree ++;
				}
				if(isset($entry['findings_summary']['condition_degree'][2]))
				{
					$entry['findings_summary']['condition_degree_2'] = $entry['findings_summary']['condition_degree'][2];
					$condition_degree ++;
				}
				if(isset($entry['findings_summary']['condition_degree'][3]))
				{
					$entry['findings_summary']['condition_degree_3'] = $entry['findings_summary']['condition_degree'][3];
					$condition_degree ++;
				}
			}

			$link_data = array
			(
				'menuaction' => 'controller.uicalendar_planner.inspection_history',
				'part_of_town_id' => $part_of_town_id,
				'deviation'	=> $deviation
			);

			$nm = array
			(
 				'start'				=> $start == -1 ? 0 : $start,
 				'num_records'		=> count($history_content),
 				'all_records'		=> $total,
				'link_data'			=> $link_data,
				'allow_all_rows'	=> $total < 150 ? true : false,
				'allrows'			=> $allrows,
				'query'				=> $query
			);
			$this->_nextmatches =createObject('phpgwapi.nextmatchs');

			$data = array
			(
				'nm_data'			 => $this->_nextmatches->xslt_nm($nm),
				'query'				 => $query,
				'deviation'			 => $deviation,
				'control_area_list'	 => array('options' => $control_area_list),
				'entity_group_list'	 => array('options' => $entity_groups),
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'form_action'		 => self::link(array('menuaction' => 'controller.uicalendar_planner.inspection_history')),
				'control_type_list'	 => array('options' => $control_type_list),
				'history_content'	 => array('history_rows' => $history_content),
				'condition_degree'	 => !!$condition_degree
			);
//			_debug_array($data['control_type_list']);
//			_debug_array($data['history_content']);
			phpgwapi_jquery::load_widget('bootstrap-multiselect');
			self::add_javascript('controller', 'base', 'calendar_planner.inspection_history.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('inspection history');
			self::render_template_xsl(array('calendar/calendar_planner'), array('inspection_history' => $data));
		}
		/**
		 * print out the calendar
		 */
		private function show( $year, $month, $items )
		{
//			_debug_array($items);
			if (null == $year)
			{
				$year = date("Y", time());
			}

			if (null == $month)
			{
				$month = date("m", time());
			}

			$this->items		 = $items;
			$this->currentYear	 = $year;
			$this->currentMonth	 = $month;
			$this->daysInMonth	 = $this->_daysInMonth($month, $year);

			$content = '<table class="mt-2 table table-hover-cells">' .
				'<thead>' .
				'<tr">' . $this->_createLabels() . '</tr>' .
				'</thead>' .
				'<tbody id="calendar">';

			$weeksInMonth	 = $this->_weeksInMonth($month, $year);
			$week			 = (int)date('W', mktime(0, 0, 0, $month, 1, $year));
			// Create weeks in a month
			for ($i = 0; $i < $weeksInMonth; $i++)
			{

				$content .= '<tr class="target_row">';
				$content .= '<th scope="row" style="writing-mode: vertical-rl;text-orientation: upright;">' . lang('week') . ' ' . ($week) . '</th>';
				$week++;
				//Create days in a week
				//$work_days = count($this->dayLabels);
				for ($j = 1; $j <= 7; $j++)
				{
					$content .= $this->_showDay($i * 7 + $j);
				}
				$content .= '</tr>';
			}

			$content .= '</tbody>';
			$content .= '</table>';
			return $content;
		}
		/*		 * ******************* PRIVATE ********************* */

		/**
		 * create the li element for ul
		 */
		private function _showDay( $cellNumber )
		{

			if ($this->currentDay == 0)
			{
				$firstDayOfTheWeek = date('N', strtotime($this->currentYear . '-' . $this->currentMonth . '-01'));

				if (intval($cellNumber) == intval($firstDayOfTheWeek))
				{
					$this->currentDay = 1;
				}
			}

			if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth))
			{
				$this->currentDate	 = date('Y-m-d', strtotime($this->currentYear . '-' . $this->currentMonth . '-' . ($this->currentDay)));
				$cellContent		 = $this->currentDay;
				$this->currentDay++;
			}
			else
			{
				$this->currentDate	 = null;
				$cellContent		 = null;
			}
	//		_debug_array($this->currentDate);


			$item_content = "";

			if(!empty($this->items[$this->currentDate]))
			{
				$i=1;
				foreach ($this->items[$this->currentDate] as $item)
				{
//				_debug_array($item);
					$desc = str_replace(':', ':<br/>', $item['component']['xml_short_desc']);

					if($item['schedule']['info']['check_list_id'])
					{
						$control_link_data = array
							(
							'menuaction' => 'controller.uicase.add_case',
							'check_list_id' => $item['schedule']['info']['check_list_id'],
						);
						$url_target = '_self';
						$link_to_checklist = "<a href=\"" . $GLOBALS['phpgw']->link('/index.php', $control_link_data) . "\" target=\"{$url_target}\"><kbd><i class='fas fa-link'></i></kbd></a>";
					}
					else
					{
						$link_to_checklist = '';
					}

					switch ($item['schedule']['status'])
					{
						case 'CONTROL_REGISTERED':
						case 'CONTROL_NOT_DONE_WITH_PLANNED_DATE':
						case 'CONTROL_NOT_DONE':
						case 'CONTROL_PLANNED':
							$class = 'badge-primary';
							$draggable =  'draggable="true"';
							break;

						case 'CONTROL_DONE_IN_TIME_WITHOUT_ERRORS':
							$class = 'badge-secondary';
							$draggable =  '';
							break;

						default:
							$class = 'badge-secondary';
							$draggable =  '';
							break;
					}

					$item_content .= <<<HTML

					<div class="mb-1 card badge event {$class}" style="width: 8rem;" {$draggable}
						id="{$item['component']['location_id']}_{$item['component']['id']}"
						control_id="{$item['schedule']['info']['control_id']}"
						serie_id="{$item['schedule']['info']['serie_id']}"
						check_list_id="{$item['schedule']['info']['check_list_id']}"
						deadline_date_ts="{$item['schedule']['info']['deadline_date_ts']}"
						assigned_to="{$item['schedule']['info']['assigned_to']}">
						<span class="float-left link_to_checklist">
							{$link_to_checklist}
						</span>
						<span class="float-right">
							{$desc}
						</span>

					</div>
HTML;

				$i++;
				}
			}

			return '<td id="' . $this->currentDate . '" class="' . ($cellNumber % 7 == 1 ? ' start ' : ($cellNumber % 7 == 0 ? ' end ' : ' ')) .
				($cellContent == null ? 'bg-light' : 'table-active') . '"><div class="clearfix"><span class="float-left">' . $cellContent . "</span>{$item_content}</div></td>";
		}

		/**
		 * create calendar week labels
		 */
		private function _createLabels()
		{

			$content = '';

			$content .= '<th>#</th>';
			foreach ($this->dayLabels as $index => $label)
			{
				$content .= '<th>' . lang($label) . '</th>';
			}

			return $content;
		}

		/**
		 * calculate number of weeks in a particular month
		 */
		private function _weeksInMonth( $month = null, $year = null )
		{

			if (null == ($year))
			{
				$year = date("Y", time());
			}

			if (null == ($month))
			{
				$month = date("m", time());
			}

			// find number of days in this month
			$daysInMonths	 = $this->_daysInMonth($month, $year);
			$numOfweeks		 = ($daysInMonths % 7 == 0 ? 0 : 1) + intval($daysInMonths / 7);
			$monthEndingDay	 = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));
			$monthStartDay	 = date('N', strtotime($year . '-' . $month . '-01'));
			if ($monthEndingDay < $monthStartDay)
			{
				$numOfweeks++;
			}

			return $numOfweeks;
		}

		/**
		 * calculate number of days in a particular month
		 */
		private function _daysInMonth( $month = null, $year = null )
		{

			if (null == ($year))
			{
				$year = date("Y", time());
			}

			if (null == ($month))
			{
				$month = date("m", time());
			}

			return date('t', strtotime($year . '-' . $month . '-01'));
		}

		public function start_inspection()
		{
			if(!$this->edit)
			{
				phpgw::no_access();
			}

			self::set_active_menu('controller::start_inspection');
			$month	 = phpgw::get_var('month', 'int', 'REQUEST', date("m", time()));
			$year	 = phpgw::get_var('year', 'int', 'REQUEST', date("Y", time()));
			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = (array)phpgw::get_var('part_of_town_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_area_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_control_area_id", $control_area_id, $user_id);
			}
			else
			{
				$control_area_id = (int)phpgwapi_cache::user_get('controller', "calendar_control_area_id", $user_id);
			}
			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);
			}

			if($part_of_town_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_part_of_town", $part_of_town_id, $user_id);
			}
			else if ($_POST)
			{
				phpgwapi_cache::user_clear('controller', "calendar_planner_part_of_town", $user_id);
			}
			else
			{
				$part_of_town_id = (array)phpgwapi_cache::user_get('controller', "calendar_planner_part_of_town", $user_id);
			}

			$entity_group_id = phpgw::get_var('entity_group_id', 'int');
			$current_day_timestamp	 = phpgw::get_var('current_day_str', 'date', 'REQUEST', time());
			$current_day = new DateTime(date('Y-m-d', $current_day_timestamp));

			$current_day_str = date('Y-m-d', $current_day_timestamp);
//_debug_array($current_day_str);


			if (phpgw::get_var('prev_day', 'bool'))
			{
				$current_day->modify('-1 day');
				$current_day_str = $current_day->format('Y-m-d');
			}

			if (phpgw::get_var('next_day', 'bool'))
			{
				$current_day->modify('+1 day');
				$current_day_str = $current_day->format('Y-m-d');
			}

			$control_types = $this->so_control->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}



			$entity_groups = createObject('property.bogeneric')->get_list(array('type'		 => 'entity_group',
				'selected'	 => $entity_group_id, 'order'		 => 'name', 'sort'		 => 'asc'));

			$part_of_towns = createObject('property.bogeneric')->get_list(array(
				'type'		 => 'part_of_town',
				'selected'	 => $part_of_town_id,
				'order'		 => 'name',
				'sort'		 => 'asc'
				)
			);


//			$from_date_ts = strtotime("{$current_day_str} 0:00:00");
//			$to_date_ts = strtotime("{$current_day_str} 23:59:59");

			$from_date = new DateTime("{$current_day_str} 0:00:00");
			$from_date->modify('first day of this month');

			$to_date = new DateTime("{$current_day_str} 23:59:59");
			$to_date->modify('last day of this month');

			$from_date_ts = $from_date->getTimestamp();
			$to_date_ts = $to_date->getTimestamp();

			$selected_part_of_towns = array();
			$part_of_town_list	 = array();
			$part_of_town_list2	 = array();
			$_items = array();
			foreach ($part_of_towns as &$part_of_town)
			{
				if ($part_of_town['id'] > 0)
				{
					$part_of_town['name']		 = ucfirst(strtolower($part_of_town['name']));
					$part_of_town_list[]		 = $part_of_town;

					if ($part_of_town['selected'])
					{
						$selected_part_of_towns[] = $part_of_town['id'];
						$checklist_items = $this->so_control->get_checklist_at_time_and_place($part_of_town['id'] , $control_id, $from_date_ts, $to_date_ts);
						$_items = $_items + $checklist_items;
					}
				}
			}
			unset($part_of_town);

			$items = array();
			foreach ($_items as $location_id => $entry)
			{

				$__items =  execMethod('property.soentity.read',array(
						'location_id' => $location_id,
						'custom_condition'	=> 'fm_bim_item.id IN(' . implode(',' , $entry) . ')',
						'control_id' => $control_id,
						'allrows' => true,
						)
					);
				$items			 = array_merge($items, $__items);
			}

			if($items)
			{
				$item_schedule = $this->get_items(0, 0, $control_id,  $entity_group_id, $part_of_town_id , $items, $from_date_ts, $to_date_ts);
			}

			$begin = clone $from_date;
			$end = clone $to_date;

			$interval = DateInterval::createFromDateString('1 day');
			$period = new DatePeriod($begin, $interval, $end);

			$disabled_dates = array();
			foreach ($period as $dt)
			{
				$test = $dt->format("Y-m-d");
				if(empty($item_schedule[$test]))
				{
					$disabled_dates[] = $dt->format("Y/m/d");
				}
			}

			$todo_list = array();
			$completed_list = array();

			if(!empty($item_schedule[$current_day_str]))
			{
				foreach ($item_schedule[$current_day_str] as $check_list)
				{
					if(!empty($check_list['schedule']['info']['completed_date_ts']))
					{
						$completed_list[] = "{$check_list['schedule']['info']['check_list_id']}::{$check_list['component']['address']} {$check_list['component']['xml_short_desc']}";
					}
					else
					{
						$todo_list[] = array(
							'id' => $check_list['schedule']['info']['check_list_id'],
							'name' => "{$check_list['component']['xml_short_desc']}"
						);
					}
				}
			}

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));

			$GLOBALS['phpgw']->jqcal2->add_listener('current_day_str', 'date', $current_day->getTimestamp(), array('disabled_dates' => $disabled_dates)	);
			$data = array
			(
				'current_day_str' => $current_day->format('Y-m-d'),
				'current_day' => $current_day->getTimestamp(),
				'img_green_check' => $GLOBALS['phpgw']->common->image('phpgwapi', 'green-check'),
				'control_area_list'	 => array('options' => $control_area_list),
				'entity_group_list'	 => array('options' => $entity_groups),
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'todo_list'			=> array('options' => $todo_list),
				'completed_list'	=> $completed_list,
				'form_action'		 => self::link(array('menuaction' => 'controller.uicalendar_planner.start_inspection')),
				'control_type_list'	 => array('options' => $control_type_list),
			);

			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('bootstrap-multiselect');

			self::add_javascript('controller', 'base', 'calendar_planner.start_inspection.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner') . '::' . lang('start inspection');
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = 'controller::calendar_planner::start_inspection';
			self::render_template_xsl(array('calendar/calendar_planner'), array('start_inspection' => $data));

		}

		public function ad_hoc()
		{
			if(!$this->edit)
			{
				phpgw::no_access();
			}

			self::set_active_menu('controller::ad_hoc');
			$month	 = phpgw::get_var('month', 'int', 'REQUEST', date("m", time()));
			$year	 = phpgw::get_var('year', 'int', 'REQUEST', date("Y", time()));

			if (13 == $month)
			{
				$month	 = 1;
				$year	 += 1;
			}

			if ((string)$_REQUEST['month'] === '0')
			{
				$month	 = 12;
				$year	 -= 1;
			}

			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = (array)phpgw::get_var('part_of_town_id', 'int');


			$user_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if($control_area_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_control_area_id", $control_area_id, $user_id);
			}
			else
			{
				$control_area_id = (int)phpgwapi_cache::user_get('controller', "calendar_control_area_id", $user_id);
			}
			if($control_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_control_id", $control_id, $user_id);
			}
			else
			{
				$control_id = (int)phpgwapi_cache::user_get('controller', "calendar_planner_control_id", $user_id);
			}

			if($part_of_town_id)
			{
				phpgwapi_cache::user_set('controller', "calendar_planner_part_of_town", $part_of_town_id, $user_id);
			}
			else if ($_POST)
			{
				phpgwapi_cache::user_clear('controller', "calendar_planner_part_of_town", $user_id);
			}
			else
			{
				$part_of_town_id = (array)phpgwapi_cache::user_get('controller', "calendar_planner_part_of_town", $user_id);
			}

			$entity_group_id = phpgw::get_var('entity_group_id', 'int');

			$control_types = $this->so_control->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}

			$entity_groups = createObject('property.bogeneric')->get_list(array('type'		 => 'entity_group',
				'selected'	 => $entity_group_id, 'order'		 => 'name', 'sort'		 => 'asc'));

			$part_of_towns = createObject('property.bogeneric')->get_list(array(
				'type'		 => 'part_of_town',
				'selected'	 => $part_of_town_id,
				'order'		 => 'name',
				'sort'		 => 'asc'
				)
			);


			$from_date_ts = strtotime("{$current_day_str} 0:00:00");
			$to_date_ts = strtotime("{$current_day_str} 23:59:59");


			$selected_part_of_towns = array();
			$part_of_town_list	 = array();
			$_items = array();
			foreach ($part_of_towns as &$part_of_town)
			{
				if ($part_of_town['id'] > 0)
				{
					$part_of_town['name']		 = ucfirst(strtolower($part_of_town['name']));
					$part_of_town_list[]		 = $part_of_town;

					if ($part_of_town['selected'])
					{
						$selected_part_of_towns[] = $part_of_town['id'];
					}
				}
			}
			unset($part_of_town);

			$scheduled_controls = $this->get_scheduled_controls( $selected_part_of_towns, $month, $year, $control_id );

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));

			$data = array
			(
				'current_month'	 => lang(date('F', mktime(0, 0, 0, $month, 1))),
				'current_year'	 => $year,
				'next_month_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.ad_hoc',
					'year' => $year,
					'month' => ($month + 1),
					)),
				'prev_month_url' => self::link(array('menuaction' => 'controller.uicalendar_planner.ad_hoc',
					'year' => $year,
					'month' => ($month - 1),
					)),
				'next_month'	 => lang(date('F', mktime(0, 0, 0, $month + 1, 1))),
				'prev_month'	 => lang(date('F', mktime(0, 0, 0, $month - 1, 1))),
				'img_green_check' => $GLOBALS['phpgw']->common->image('phpgwapi', 'green-check'),
				'control_area_list'	 => array('options' => $control_area_list),
				'entity_group_list'	 => array('options' => $entity_groups),
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'form_action'		 => self::link(array('menuaction' => 'controller.uicalendar_planner.ad_hoc')),
				'control_type_list'	 => array('options' => $control_type_list),
				'scheduled_controls'	=> array('ad_hoc_rows' => $scheduled_controls),
			);

			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::load_widget('bootstrap-multiselect');

			self::add_javascript('controller', 'base', 'calendar_planner.start_inspection.js');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner') . '::Ad hoc';
			$GLOBALS['phpgw_info']['flags']['breadcrumb_selection'] = 'controller::calendar_planner::ad_hoc';
			self::render_template_xsl(array('calendar/calendar_planner'), array('ad_hoc' => $data));

		}

	}