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
		public $public_functions = array
			(
			'index'							 => true,
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


			$component_type = phpgwapi_cache::session_get('controller', 'component_type');
			if(!$component_type)
			{
				$this->soadmin_entity	= CreateObject('property.soadmin_entity');
				$entity_list 	= $this->soadmin_entity->read(array('allrows' => true));

				$component_type = array();
				foreach($entity_list as $entry)
				{
					$categories = $this->soadmin_entity->read_category(array('entity_id' => $entry['id'],'order' => 'name','sort' => 'asc','enable_controller' => true, 'allrows' => true));
					foreach($categories as $category)
					{

						if($category['enable_controller'])
						{
							$component_type[] = array
							(
								'id' => $category['location_id'],
								'name'=> "{$entry['name']}::{$category['name']}"
							);
						}
					}
				}
				array_unshift($component_type, array('id' => '', 'name' => lang('select value')));
				phpgwapi_cache::session_set('controller', 'component_type', $component_type);
			}

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, '.control');
			$user_list = array();
			foreach($users as $user)
			{
				$user_list[] = array
				(
					'id'		=> $user['account_id'],
					'name'		=> "{$user['account_lastname']}, {$user['account_firstname']}",
					'selected'	=> $this->account == $user['account_id'] ? 1 : 0
				);
			}

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


			$data = array(
				'datatable_name' => lang('status components'),
				'form'			 => array(
					'action'	=> self::link(array('menuaction' => 'controller.uicomponent.index')),
					'method'	=> 'POST',
					'toolbar' => array(
						'item' => array(
							//as categories
							array('type'	 => 'filter',
								'name'	 => 'location_id',
								'text'	 => lang('component'),
								'list'	 => $component_type,
								'onchange'	=> 'update_table();'
							),/*
							array('type'	 => 'filter',
								'name'	 => 'control_area',
								'text'	 => lang('Control_area'),
								'list'	 => $control_areas_array,
								'onchange'	=> 'update_table();'
							),
							array('type'	 => 'filter',
								'name'	 => 'user_id',
								'text'	 => lang('User'),
								'list'	 => $user_list,
								'onchange'	=> 'update_table();'
							),*/
							array('type'	 => 'filter',
								'name'	 => 'district_id',
								'text'	 => lang('district'),
								'list'	 => $district_list,
								'onchange'	=> 'update_table();'
							),/*
							array('type'	 => 'text',
								'text'	 => lang('searchfield'),
								'name'	 => 'query'
							),
							array(
								'type'	 => 'button',
								'name'	 => 'search',
								'value'	 => lang('Search'),
								'onclick'=> 'update_table();'
							),*/
						),
					),
				),
				'datatable'		 => array(
					'source' => self::link(array('menuaction' => 'controller.uicomponent.index',
						'phpgw_return_as' => 'json')),
					'field'	 =>  $this->get_fields(),
				),
			);
			self::render_template_xsl(array('component'), $data);
		}

		private function get_fields()
		{
			$fields = array
			(
				array(
					'key'		 => 'component_id',
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
					'label'		 => lang('jan'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '2',
					'label'		 => lang('feb'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '3',
					'label'		 => lang('mar'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '4',
					'label'		 => lang('apr'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '5',
					'label'		 => lang('may'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '6',
					'label'		 => lang('june'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '7',
					'label'		 => lang('july'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '8',
					'label'		 => lang('aug'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '9',
					'label'		 => lang('sept'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '10',
					'label'		 => lang('oct'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '11',
					'label'		 => lang('nov'),
					'sortable'	 => true,
				),
				array(
					'key'		 => '12',
					'label'		 => lang('dec'),
					'sortable'	 => true,
				),
			);
			return $fields;
		}

		public function query()
		{
			static $_location_name = array();
			$location_id = phpgw::get_var('location_id', 'int');
			$control_area = phpgw::get_var('control_area', 'int');
			$user_id = phpgw::get_var('user_id', 'int');
			$district_id = phpgw::get_var('district_id', 'int');
			$query = phpgw::get_var('query', 'string');
			$year = phpgw::get_var('year', 'int');

			$so_control			 = CreateObject('controller.socontrol');
			$this->so			= CreateObject('controller.socheck_list');

			// Validates year. If year is not set, current year is chosen
			$year = execMethod('controller.uicalendar.validate_year',$year);

			// Gets timestamp of first day in year
			$from_date_ts = execMethod('controller.uicalendar.get_start_date_year_ts',$year);

			// Gets timestamp of first day in next year
			$to_date_ts = execMethod('controller.uicalendar.get_end_date_year_ts',$year);

			$components = execMethod('property.soentity.read',array('location_id' => $location_id, 'district_id' => $district_id, 'allrows' => true));
			$all_components = array();
			$components_with_calendar_array = array();

			foreach($components as $_component)
			{
				$component_id = $_component['id'];
				$all_components[$component_id] = $_component;
				$controls = execMethod('controller.socontrol.get_controls_at_component', array('location_id' => $location_id, 'component_id' => $component_id));
				foreach($controls as $_control)
				{
					$control_id						= $_control['control_id'];
					$control						= $so_control->get_single($_control['control_id']);
					$components_for_control_array	= $so_control->get_components_for_control($control_id, $location_id, $component_id);

					// LOCATIONS: Process aggregated values for controls with repeat type day or week
					if($control->get_repeat_type() <= controller_control::REPEAT_TYPE_WEEK)
					{

						// COMPONENTS: Process aggregated values for controls with repeat type day or week
						foreach($components_for_control_array as $component)
						{
							$short_desc_arr = execMethod('property.soentity.get_short_description', array(
								'location_id' => $component->get_location_id(), 'id' => $component->get_id()));
							if(!isset($_location_name[$component->get_location_code()]))
							{
								$_location		 = execMethod('property.solocation.read_single', $component->get_location_code());
								$location_arr	 = explode('-', $component->get_location_code());
								$i				 = 1;
								$name_arr		 = array();
								foreach($location_arr as $_dummy)
								{
									$name_arr[] = $_location["loc{$i}_name"];
									$i++;
								}

								$_location_name[$component->get_location_code()] = implode('::', $name_arr);
							}

							$short_desc_arr .= ' [' . $_location_name[$component->get_location_code()] . ']';

							$component->set_xml_short_desc($short_desc_arr);

							$repeat_type				 = $control->get_repeat_type();
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
							$components_with_calendar_array[$component_id][]	 = array("component" => $component->toArray(),
								"calendar_array" => $calendar_array);
						}
					}
					// Process values for controls with repeat type month or year
					else if($control->get_repeat_type() > controller_control::REPEAT_TYPE_WEEK)
					{
						foreach($components_for_control_array as $component)
						{
							$short_desc_arr = execMethod('property.soentity.get_short_description', array(
								'location_id' => $component->get_location_id(), 'id' => $component->get_id()));

							//FIXME - make generic

							/* => */
							if(!isset($_location_name[$component->get_location_code()]))
							{
								$_location		 = execMethod('property.solocation.read_single', $component->get_location_code());
								$location_arr	 = explode('-', $component->get_location_code());
								$i				 = 1;
								$name_arr		 = array();
								foreach($location_arr as $_dummy)
								{
									$name_arr[] = $_location["loc{$i}_name"];
									$i++;
								}

								$_location_name[$component->get_location_code()] = implode('::', $name_arr);
							}

							$short_desc_arr .= ' [' . $_location_name[$component->get_location_code()] . ']';
							/* <= */

							$component->set_xml_short_desc($short_desc_arr);

							$repeat_type				 = $control->get_repeat_type();
							$component_with_check_lists	 = $this->so->get_check_lists_for_control_and_component($control_id, $component->get_location_id(), $component->get_id(), $from_date_ts, $to_date_ts, $repeat_type);

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

							/*
							 * End override control with data from serie
							 */

							$year_calendar	 = new year_calendar($control, $year, $component, null, "component", $control_relation);
							$calendar_array	 = $year_calendar->build_calendar($check_lists_array);

							$components_with_calendar_array[$component_id][] = array("component" => $component->toArray(),
								"calendar_array" => $calendar_array);
						}
					}



				}
			}
			unset($component_id);
			_debug_array($components_with_calendar_array[1]);
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
			foreach($components_with_calendar_array as $component_id => $entry)
			{
				unset($all_components[$component_id]);
				$data = array();

				$component_link_data = array
				(
					'menuaction'	=> 'property.uientity.edit',
					'location_id'	=> $location_id,
					'id'			=> $component_id,
					'active_tab'	=> 'controller'
				);
			
				$data['component_id'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$component_link_data)."\" target='_blank'>{$component_id}{$entry[0]['component']['xml_short_desc']}</a>";

				$max_repeat_type = 0;
				$_data = array();
				foreach($entry as $dataset)
				{
					$repeat_type = (int)$dataset['component']['control_relation']['repeat_type'];
					if($repeat_type > $max_repeat_type)
					{
						$max_repeat_type = $repeat_type;
					}
					foreach($dataset['calendar_array'] as $month => $calendar)
					{
						if($calendar)
						{
							$_data[$month][$repeat_type] = $calendar;
						}
					}
				}
				for ( $_month=1; $_month < 13; $_month++ )
				{

					for ( $i = $max_repeat_type; $i > -1; $i-- )
					{
						if(isset($_data[$_month][$i]))
						{
							$data[$_month] = $_data[$_month][$i];
							$data[$_month]['repeat_type'] = $repeat_type_array[$i];
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

			if($all_components && count($all_components))
			{
				foreach($all_components as $component_id => $component)
				{
					$data = array();

					$component_link_data = array
					(
						'menuaction'	=> 'property.uientity.edit',
						'location_id'	=> $location_id,
						'id'			=> $component_id,
						'active_tab'	=> 'controller'
					);


					///////
					$short_desc_arr = execMethod('property.soentity.get_short_description', array(
						'location_id' => $location_id, 'id' => $component['id']));

					if(!isset($_location_name[$component['location_code']]))
					{
						$_location		 = execMethod('property.solocation.read_single', $component['location_code']);
						$location_arr	 = explode('-', $component['location_code']);
						$i				 = 1;
						$name_arr		 = array();
						foreach($location_arr as $_dummy)
						{
							$name_arr[] = $_location["loc{$i}_name"];
							$i++;
						}

						$_location_name[$component['location_code']] = implode('::', $name_arr);
					}

					$short_desc_arr .= ' [' . $_location_name[$component['location_code']] . ']';

					//////					
					$data['component_id'] = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$component_link_data)."\" target='_blank'>{$component_id}{$short_desc_arr}</a>";
					$data['missing_control'] = true;
					$values[] = $data;

				}
			}
//_debug_array($values);
			$data_set = array();
			foreach ($values as $entry)
			{
				$row['component_id'] = $entry['component_id'];
				$row['year'] = '';
				$row['descr'] = '';

				if(!isset($entry['missing_control']))
				{
					$row['year'] = $year;
					$row['descr'] = "Frekvens<br/>Status<br/>Ansvarlig<br/>Utførende<br/>service tid<br/>kontroll tid";
				}

				for ( $_month=1; $_month < 13; $_month++ )
				{
					$row[$_month] = $this->translate_calendar_info($entry[$_month],$year, $_month);
				}

				$data_set[] = $row;
			}
			$fields	= $this->get_fields();

			$tbody = '';
			foreach($data_set as $row_data )
			{
				$tbody .= '<tr>';
				foreach($fields as $field )
				{
					$tbody .= '<td>';
					$tbody .= $row_data[$field['key']];
					$tbody .= '</td>';
				}
				$tbody .= '</tr>';
			}

			$result = array
			(
				'tbody' => $tbody
			);
			return $result;
		}

		private function translate_calendar_info($param = array(), $year, $month)
		{
			if(!isset($param['repeat_type']))
			{
				return '';
			}
			switch($param['status'])
			{
				case "CONTROL_NOT_DONE":
					$status = 'Ikke utført';
					break;
				case "CONTROL_REGISTERED":
					$status = 'Registrert';
					break;
				case "CONTROL_PLANNED":
					$status = 'Planlagt';
					break;
				case "CONTROL_NOT_DONE_WITH_PLANNED_DATE":
					$status = 'Forsinket, Ikke utført';
					break;
				case "CONTROL_DONE_OVER_TIME_WITHOUT_ERRORS":
					$status = 'Senere enn planagt';
					break;
				case "CONTROL_DONE_IN_TIME_WITHOUT_ERRORS":
					$status = 'Utført uten avvik';
					break;
				case "CONTROL_DONE_WITH_ERRORS":
					$status = "Utført med {$param['info']['num_open_cases']} åpne avvik";
					break;
				case "CONTROL_CANCELED":
					$status = 'Kansellert';
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
				$menuaction	= 'controller.uicalendar.view_calendar_year_for_locations';
				if($param['info']['repeat_type'] < 2)
				{
					$menuaction	= 'controller.uicalendar.view_calendar_month_for_locations';
				}

				$control_link_data = array
				(
					'menuaction'	=> $menuaction,
					'control_id'	=> $param['info']['control_id'],
					'location_id'	=> $param['info']['location_id'],
					'component_id'	=> $param['info']['component_id'],
					'serie_id'		=> $param['info']['serie_id'],
					'year'			=> $year,
					'month'			=> $month
				);
			}
			$repeat_type = '<a href="'.$GLOBALS['phpgw']->link('/index.php',$control_link_data).'" target="_blank">'. $param['repeat_type'].'</a>';

		//	$repeat_type = $param['repeat_type'];
			$responsible = '---';
			$assigned_to = $param['info']['assigned_to'] > 0 ? $GLOBALS['phpgw']->accounts->id2name($param['info']['assigned_to']) : '&nbsp;';
			$service_time = $param['info']['service_time'] ? $param['info']['service_time'] : '&nbsp;';
			$controle_time = $param['info']['controle_time'] ? $param['info']['controle_time'] : '&nbsp;';

			return "{$repeat_type}<br/>{$status}<br/>{$responsible}<br/>{$assigned_to}<br/>{$service_time}<br/>{$controle_time}";
		}
	}