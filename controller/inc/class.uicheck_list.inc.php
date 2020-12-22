<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
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
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('phpgwapi.datetime');

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'date_converter', 'inc/helper/');
	include_class('controller', 'location_finder', 'inc/helper/');

	class controller_uicheck_list extends phpgwapi_uicommon_jquery
	{

		protected $so;
		protected $so_control;
		protected $so_control_item;
		protected $so_check_item;
		protected $so_procedure;
		protected $so_control_group_list;
		protected $so_control_group;
		protected $so_control_item_list;
		protected $location_finder;
		private $read;
		private $add;
		private $edit;
		private $delete;
		private $acl_location;
		var $public_functions = array(
			'index' => true,
			'add_check_list' => true,
			'save_check_list' => true,
			'edit_check_list' => true,
			'print_check_list' => true,
			'view_control_info' => true,
			'view_control_details' => true,
			'view_control_items' => true,
			'get_check_list_info' => true,
			'get_cases_for_check_list' => true,
			'update_status' => true,
			'build_multi_upload_file' => true,
			'handle_multi_upload_file'	=> true,
			'get_files3' => true,
			'view_file'	=> true,
			'get_report' => true,
			'set_completed_item'	=> true,
			'undo_completed_item'	=> true,
			'add_billable_hours'	=> true,
			'set_inspector'			=> true,
			'view_image'			=> true
		);

		function __construct()
		{
			parent::__construct();

			$this->so_control = CreateObject('controller.socontrol');
			$this->so = CreateObject('controller.socheck_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_item = CreateObject('controller.socheck_item');
			$this->so_procedure = CreateObject('controller.soprocedure');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_item_list = CreateObject('controller.socontrol_item_list');
			$this->so_case = CreateObject('controller.socase');
			$this->vfs = CreateObject('phpgwapi.vfs');

			$this->location_finder = new location_finder();

			$this->acl_location = '.checklist';

			$this->read = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller');//1
			$this->add = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');//2
			$this->edit = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller');//4
			$this->delete = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller');//8

			self::set_active_menu('controller::control::check_list');

			if (phpgw::get_var('noframework', 'bool'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
				phpgwapi_cache::session_set('controller', 'noframework', true);
			}
			else if (phpgwapi_cache::session_get('controller', 'noframework'))
			{
				$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			}

			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('Check_list');

//			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/base.css');
			$GLOBALS['phpgw']->js->validate_file('alertify', 'alertify.min', 'phpgwapi');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/alertify.min.css');
			$GLOBALS['phpgw']->css->add_external_file('phpgwapi/js/alertify/css/themes/bootstrap.min.css');

		}

		/**
		 * Public function for displaying checklists
		 *
		 * @param HTTP:: phpgw_return_as
		 * @return data array
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable_name' => 'Sjekkliste (Ikke i bruk)',
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'filter',
								'name' => 'status',
								'text' => lang('Status'),
								'list' => array(
									array(
										'id' => 'none',
										'name' => lang('Not selected')
									),
									array(
										'id' => 'NEW',
										'name' => lang('NEW')
									),
									array(
										'id' => 'PENDING',
										'name' => lang('PENDING')
									),
									array(
										'id' => 'REJECTED',
										'name' => lang('REJECTED')
									),
									array(
										'id' => 'ACCEPTED',
										'name' => lang('ACCEPTED')
									)
								)
							),
							array('type' => 'text',
								'text' => lang('searchfield'),
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'title',
							'label' => lang('Control title'),
							'sortable' => false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable' => false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable' => false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable' => false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Public function for displaying the add check list form
		 *
		 * @param HTTP:: location code, control id, date
		 * @return data array
		 */
		function add_check_list( $check_list = null )
		{
			if ($check_list == null)
			{
				$type = phpgw::get_var('type');
				$control_id = phpgw::get_var('control_id');
				$deadline_ts = phpgw::get_var('deadline_ts');
				$original_deadline_date_ts = phpgw::get_var('deadline_ts');
				$deadline_current = phpgw::get_var('deadline_current', 'bool');
				$serie_id = phpgw::get_var('serie_id', 'int');
				$check_list_error_array = phpgw::get_var('check_list_errors');

				if ($deadline_current)
				{
					$year = date('Y');
					$month = date('m');
					$a_date = "{$year}-{$month}-23";
					$deadline_ts = mktime(00, 00, 00, $month, date('t', strtotime($a_date)), $year);
					unset($year);
					unset($month);
					unset($a_date);

					/* look for checklist with $deadline_ts = $deadline_current */

					$check_list_id = $this->so_control->get_check_list_id_for_deadline($serie_id, $deadline_ts);
					if ($check_list_id)
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list',
							'check_list_id' => $check_list_id));
					}
				}

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$check_list->set_deadline($deadline_ts);
				$check_list->set_original_deadline($original_deadline_date_ts);
				$check_list->set_error_msg_array($check_list_error_array);
			}
			else
			{
				if ($check_list->get_component_id() > 0)
				{
					$type = "component";
				}
				else
				{
					$type = "location";
				}
			}

			if (!$location_code = $check_list->get_location_code())
			{
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
				$level = $this->location_finder->get_location_level($location_code);
			}

			$get_locations = false;

			if ($type == "component")
			{
				if ($check_list != null)
				{
					$location_id = phpgw::get_var('location_id');
					$check_list->set_location_id($location_id);
					$component_id = phpgw::get_var('component_id');
					$check_list->set_component_id($component_id);
				}


				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;

					$type_info = explode('.', $location_info['location']);
					$level = $type_info[2];
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

					$location_code = $component_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
					$level = $this->location_finder->get_location_level($location_code);

					$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

					$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
							'location_id' => $location_id, 'id' => $component_id));

				}

				$component = new controller_component();
				$component->set_id($component_id);
				$component->set_location_id($location_id);
				$component->set_location_code($location_code);
				$component->set_xml_short_desc($short_desc);

				$component_array = $component->toArray();
				$building_location_code = $this->location_finder->get_building_location_code($location_code);

				$type = "component";
			}
			else
			{
				$type = "location";
			}

			$repeat_descr = '';
			if ($serie = $this->so_control->get_serie($serie_id))
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

			$control = $this->so_control->get_single($check_list->get_control_id());

			if ($repeat_descr)
			{
				$repeat_descr .= " :: " . $control->get_title();
				$control->set_title($repeat_descr);
			}

			if (!$responsible_user_id = phpgw::get_var('assigned_to', 'int'))
			{
				$responsible_user_id = execMethod('property.soresponsible.get_responsible_user_id', array
					(
					'responsibility_id' => $control->get_responsibility_id(),
					'location_code' => $location_code
					)
				);
			}

			$year = date("Y", $deadline_ts);
			$month_nr = date("n", $deadline_ts);

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			if ($type == "location")
			{
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
			}
			else
			{
				$buildings_on_property = null;
			}

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
					(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected' => $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			$data = array
				(
				'user_list' => array('options' => $user_list_options),
				'location_array' => $location_array,
				'component_array' => $component_array,
				'get_locations' => $get_locations,
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month_nr,
				'current_month_name' => lang("month {$month_nr} capitalized"),
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'check_list_type' => 'add_check_list',
				'serie_id' => $serie_id,
				'required_actual_hours' => $required_actual_hours,
				'integration' => $this->_get_component_integration($location_id, $component_arr)
			);

			$number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m', $check_list->get_deadline()), date('Y', $check_list->get_deadline())) -1;

			$GLOBALS['phpgw']->jqcal2->add_listener('planned_date', 'date', 0, array(
					'min_date' => date('Y/m/d', $check_list->get_deadline() - 3600 * 24 * $number_of_days_in_month), //a month
//					'max_date' => date('Y/m/d',  $check_list->get_deadline())
				)
			);
			$GLOBALS['phpgw']->jqcal2->add_listener('completed_date', 'date', 0, array(
					'min_date' => date('Y/m/d', $check_list->get_deadline() - 3600 * 24 * $number_of_days_in_month), //a month
//					'max_date' => date('Y/m/d',  $check_list->get_deadline())
				)
			);
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'custom_ui.js');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::add_javascript('controller', 'base', 'check_list.js');

			self::render_template_xsl(array('check_list/add_check_list', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'check_list/fragments/add_check_list_menu',
				'check_list/fragments/select_buildings_on_property'), $data);
		}



		function set_completed_item()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$item_string = phpgw::get_var('item_string');
			$location_code = phpgw::get_var('location_code');

			if(!$item_string && $location_code)
			{
				$location_arr = explode('-', $location_code);
				$type_id = count($location_arr);
				$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".location.{$type_id}");
				$item_id = createObject('property.solocation')->get_item_id( $location_code );
			}
			else
			{
				$item_arr = explode('_', $item_string);
				$location_id = $item_arr[0];
				$item_id = $item_arr[1];
			}

			if($this->edit)
			{
				$this->so->set_completed_item($check_list_id, $location_id, $item_id);
			}

			$this->redirect(array('menuaction' => 'controller.uicase.add_case',
					'check_list_id' => $check_list_id));

		}

		function undo_completed_item()
		{
			$completed_id = phpgw::get_var('completed_id', 'int');
			if($this->edit)
			{
				$ok = $this->so->undo_completed_item($completed_id);
			}

			return array(
				'status' => $ok ? 'ok' : 'error'
			);
		}

		function set_inspector()
		{
			$check_list_id = phpgw::get_var('check_list_id', 'int');
			$checked = phpgw::get_var('checked', 'bool');
			$user_id = phpgw::get_var('user_id', 'int');

			if($this->edit)
			{
				$ok = $this->so->set_inspector($check_list_id, $user_id, $checked);
			}

			return array(
				'status' => $ok ? 'ok' : 'error'
			);

		}

		/**
		 * Public function for displaying the edit check list form
		 *
		 * @param HTTP:: check list id
		 * @return data array
		 */
		function edit_check_list( $check_list = null )
		{
			if ($check_list == null)
			{
				$check_list_id = phpgw::get_var('check_list_id');
				$check_list = $this->so->get_single($check_list_id);
			}
			else
			{
				$check_list_id = $check_list->get_id();
			}

			$number_of_cases = $this->so_case->get_number_of_cases($check_list_id);


			$current_time = time();
			$absolute_deadline = time() + (14 * 24 * 60 * 60);
			$check_list_locked = false;
			if($check_list->get_deadline() < $absolute_deadline)
			{
				//check list was due two weeks ago, and is locked
				//FIXME - funkar inte
				//Eksempel: frist 30/11/2017, dagens dato 20/11/2017, absolutt frist blir 4/12/2017...
//				$check_list_locked = true;
			}
//			echo 'tid: '.$current_time.'abs: '.$absolute_deadline;

			$repeat_descr = '';
			if ($serie = $this->so_control->get_serie($check_list->get_serie_id()))
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

			$control = $this->so_control->get_single($check_list->get_control_id());

			if ($repeat_descr)
			{
				$repeat_descr .= " :: " . $control->get_title();
				$control->set_title($repeat_descr);
			}

			$component_id = $check_list->get_component_id();
			$get_locations = false;
			$buildings_on_property = array();
			$get_buildings_on_property = false;

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$system_location = $GLOBALS['phpgw']->locations->get_name($location_id);

				$system_location_arr = explode('.', $system_location['location']);

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

				if ($location_children)
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

//				_debug_array($component_children);

				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;
					$type_info = explode('.', $location_info['location']);
					$level = $type_info[2];
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
					$location_code = $component_arr['location_code'];
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);

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
				$building_location_code = $this->location_finder->get_building_location_code($location_code);
			}
			else
			{

				$get_buildings_on_property = true;
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->location_finder->get_location_level($location_code);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			if($get_buildings_on_property)
			{
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
			}

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$responsible_user_id = $check_list->get_assigned_to();

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
					(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected' => $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			$file_def = array
			(
				array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,'resizeable' => true),
				array('key' => 'picture', 'label' => '', 'sortable' => false,'resizeable' => false, 'formatter' => 'JqueryPortico.showPicture')
			);

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => "controller.uicheck_list.get_files3",
					'id' => $check_list_id,	'phpgw_return_as' => 'json'))),
				'ColumnDefs' => $file_def,
				'data' => json_encode(array()),
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);
			$last_completed_checklist = $this->so_check_item->get_last_completed_checklist($check_list_id);
			$last_completed_checklist_date = !empty($last_completed_checklist['completed_date']) ? $GLOBALS['phpgw']->common->show_date($last_completed_checklist['completed_date'], $this->dateFormat) : '';
//			_debug_array($component_children);
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

			$data = array
				(
				'inspectors' => createObject('controller.sosettings')->get_inspectors($check_list->get_id()),
				'administrator_list' => implode('; ', $administrator_arr),
				'supervisor_name' => implode('; ', $supervisor_arr),
				'user_list' => array('options' => $user_list_options),
				'control' => $control,
				'check_list' => $check_list,
				'number_of_cases'	=> $number_of_cases,
				'last_completed_checklist_date'	=> $last_completed_checklist_date,
				'buildings_on_property' => $buildings_on_property,
				'component_children'	=> $component_children,
				'location_children' => $location_children,
				'get_locations' => $get_locations,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month,
				'current_month_name' => lang("month {$month} capitalized"),
				'check_list_locked' => $check_list_locked,
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'required_actual_hours' => $required_actual_hours,
				'integration' => $this->_get_component_integration($location_id, $component_arr),
				'datatable_def'	=> $datatable_def,
				'multiple_uploader' => true,
				'multi_upload_parans' => "{menuaction:'controller.uicheck_list.build_multi_upload_file', id:'{$check_list_id}'}",
				'multi_upload_action' => self::link(array('menuaction' => "controller.uicheck_list.handle_multi_upload_file", 'id' => $check_list_id))
			);
//			_debug_array($check_list); die();
			$GLOBALS['phpgw']->jqcal2->add_listener('planned_date');
			$number_of_days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m', $check_list->get_deadline()), date('Y', $check_list->get_deadline())) -1;

			$GLOBALS['phpgw']->jqcal2->add_listener('completed_date'
//				, 'date', 0, array(
//					'min_date' => date('Y/m/d', $check_list->get_deadline() - 3600 * 24 * $number_of_days_in_month), //start of month
//					'max_date' => date('Y/m/d', $check_list->get_planned_date())
//				)
			);
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'custom_ui.js');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::add_javascript('controller', 'base', 'check_list.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');
			phpgwapi_jquery::load_widget('file-upload-minimum');

			self::render_template_xsl(array(
				'check_list/fragments/check_list_menu',
				'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section',
				'check_list/edit_check_list',
				'check_list/fragments/select_buildings_on_property',
				'check_list/fragments/select_component_children',
				'files',
				'multi_upload_file_inline',
				'datatable_inline'
				), $data);
		}

		public function view_file()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (!$this->read)
			{
				phpgw::no_access();
			}

			$thumb = phpgw::get_var('thumb', 'bool');
			$file_id = phpgw::get_var('file_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if($file_id)
			{
				$file_info = $bofiles->vfs->get_info($file_id);
				$file = "{$file_info['directory']}/{$file_info['name']}";
			}
			else
			{
				$file = urldecode(phpgw::get_var('file'));
			}

			$source = "{$bofiles->rootdir}{$file}";
			$thumbfile = "$source.thumb";

			// prevent path traversal
			if (preg_match('/\.\./', $source))
			{
				return false;
			}

			$re_create = false;
			if ($bofiles->is_image($source) && $thumb && $re_create)
			{
				$bofiles->resize_image($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($thumb && is_file($thumbfile))
			{
				readfile($thumbfile);
			}
			else if ($bofiles->is_image($source) && $thumb)
			{
				$bofiles->resize_image($source, $thumbfile, $thumb_size = 50);
				readfile($thumbfile);
			}
			else if ($file_id)
			{
				$bofiles->get_file($file_id);
			}
			else
			{
				$bofiles->view_file('', $file);
			}
		}

		function get_files3()
		{
			$id = phpgw::get_var('id', 'int');

			if (empty($this->read))
			{
				return array();
			}

			$link_file_data = array
				(
				'menuaction' => "controller.uicheck_list.view_file",
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$files = $vfs->ls(array(
				'string' => "/controller/check_list/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$img_types = array(
				'image/jpeg',
				'image/png',
				'image/gif'
			);

			$content_files = array();

			$z = 0;
			foreach ($files as $_entry)
			{

				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
				);
				if ( in_array($_entry['mime_type'], $img_types))
				{
					$content_files[$z]['file_name'] = $_entry['name'];
					$content_files[$z]['img_id'] = $_entry['file_id'];
					$content_files[$z]['img_url'] = self::link(array(
							'menuaction' => "controller.uicheck_list.view_file",
							'file_id'	=>  $_entry['file_id'],
							'file' => $_entry['directory'] . '/' . urlencode($_entry['name'])
					));
					$content_files[$z]['thumbnail_flag'] = 'thumb=1';
				}
				$z ++;
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($content_files);

				return array
					(
					'data' => $content_files,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}
			return $content_files;
		}
		public function handle_multi_upload_file()
		{
			if (!$this->add)
			{
				phpgw::no_access();
			}

			$id = phpgw::get_var('id', 'int', 'GET');

			phpgw::import_class('property.multiuploader');

			if(!$id)
			{
				$response = array(files => array(array('error' => 'missing id in request')));
				$upload_handler->generate_response($response);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			$options['fakebase'] = "/controller";
			$options['base_dir'] = "check_list/{$id}";
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/controller/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => "controller.uicheck_list.handle_multi_upload_file", 'id' => $id)));
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
			$id = phpgw::get_var('id', 'int');

			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;
			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;

			$multi_upload_action = self::link(array('menuaction' => "controller.uicheck_list.handle_multi_upload_file", 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		/**
		 * Get linked information from external systems - as pictures
		 * @param integer $location_id
		 * @param array $component_arr
		 * @return array integration info
		 */
		private function _get_component_integration( $location_id, $_component_arr = array() )
		{
			if (isset($_component_arr['id']) && $_component_arr['id'])
			{
				$component_id = $_component_arr['id'];
			}
			else
			{
				return array();
			}
			$attributes = $GLOBALS['phpgw']->custom_fields->find2($location_id, 0, '', 'ASC', 'attrib_sort', $allrows = true);

			$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
				'id' => $component_id, 'values' => array('attributes' => $attributes)));

			$_custom_config = CreateObject('admin.soconfig', $location_id);
			$_config = isset($_custom_config->config_data) && $_custom_config->config_data ? $_custom_config->config_data : array();

			$integration = array();
			foreach ($_config as $_config_section => $_config_section_data)
			{
				if (isset($_config_section_data['tab']) && $component_arr['id'])
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

					$_config_section_data['url'] = htmlspecialchars_decode($_config_section_data['url']);
					$_config_section_data['parametres'] = htmlspecialchars_decode($_config_section_data['parametres']);

					parse_str($_config_section_data['parametres'], $output);

					foreach ($output as $_dummy => $_substitute)
					{
						$_keys[] = $_substitute;

						$__value = false;
						if (!$__value = urlencode($component_arr[str_replace(array('__', '*'), array(
								'', ''), $_substitute)]))
						{
							foreach ($component_arr['attributes'] as $_attribute)
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

					if (isset($_config_section_data['location_data']) && $_config_section_data['location_data'])
					{
						$_config_section_data['location_data'] = htmlspecialchars_decode($_config_section_data['location_data']);
						parse_str($_config_section_data['location_data'], $output);
						foreach ($output as $_dummy => $_substitute)
						{
							$_keys[] = $_substitute;
							$_values[] = urlencode($component_arr['location_data'][trim($_substitute, '_')]);
						}
						$integration_src .= '&' . str_replace($_keys, $_values, $_config_section_data['location_data']);
					}

					$integration_src .= "&{$_config_section_data['auth_key_name']}={$response}";

					$integration[] = array
						(
						'section' => $_config_section,
						'height' => isset($_config_section_data['height']) && $_config_section_data['height'] ? $_config_section_data['height'] : 500,
						'src' => $integration_src
					);
				}
			}
			return $integration;
		}

		/**
		 * Public function for saving a check list
		 *
		 * @param HTTP:: location code, control id, status etc.. (check list details)
		 * @return data array
		 */
		function save_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			if (!$this->add && !$this->edit)
			{
				phpgwapi_cache::message_set('No access', 'error');
				$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list',
					'check_list_id' => $check_list_id));
			}

			$submit_deviation = phpgw::get_var('submit_deviation', 'bool');
			$submit_ok = phpgw::get_var('submit_ok', 'bool');
			$save_check_list = phpgw::get_var('save_check_list', 'bool');

			$control_id = phpgw::get_var('control_id', 'int');
			$serie_id = phpgw::get_var('serie_id', 'int');
			$status = (int)phpgw::get_var('status');
			$type = phpgw::get_var('type');
			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$original_deadline_date_ts = phpgw::get_var('original_deadline_date', 'int');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			$comment = phpgw::get_var('comment', 'string');
			$assigned_to = phpgw::get_var('assigned_to', 'int');
			$billable_hours = phpgw::get_var('billable_hours', 'float');


			//From direct url
			$deadline_ts = phpgw::get_var('deadline_ts', 'int');

			//From datefield in edit form
			$deadline_date_ts = date_converter::date_to_timestamp($deadline_date);

			if(!$deadline_date_ts)
			{
				$deadline_date_ts = $deadline_ts;
			}

			$error = false;

			if ($planned_date != '')
			{
				$planned_date_ts = date_converter::date_to_timestamp($planned_date);
			}
			else
			{
				$planned_date_ts = $deadline_date_ts;
			}

			if($submit_deviation)
			{
//				$completed_date_ts = 0;
//				$status = controller_check_list::STATUS_NOT_DONE;
				$assigned_to = $assigned_to ? $assigned_to : $GLOBALS['phpgw_info']['user']['account_id'];
			}
			if ($completed_date != '')
			{
				$completed_date_ts = phpgwapi_datetime::date_to_timestamp($completed_date);
				$status = controller_check_list::STATUS_DONE;
			}
			else
			{
				$completed_date_ts = 0;
				$status = controller_check_list::STATUS_NOT_DONE;
			}

			if($submit_ok)
			{
				if(empty($completed_date))
				{
					$completed_date_ts = time();
				}
				$status = controller_check_list::STATUS_DONE;
				$assigned_to = $GLOBALS['phpgw_info']['user']['account_id'];
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

				if ($status == controller_check_list::STATUS_DONE && !$submit_ok)
				{
					if (!$this->_check_for_required($check_list))
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list',
							'check_list_id' => $check_list_id));
					}
				}
			}
			else
			{
				if(!$original_deadline_date_ts)
				{
					$original_deadline_date_ts = $deadline_date_ts;
				}

				if ($status == controller_check_list::STATUS_DONE && !$submit_ok)
				{
					$status = controller_check_list::STATUS_NOT_DONE;
					$completed_date_ts = 0;
					$error_message = "Status kunne ikke settes til utført - prøv igjen";
					$error = true;
					phpgwapi_cache::message_set($error_message, 'error');
				}

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);
				$check_list->set_serie_id($serie_id);


				if ($type == "component")
				{
					$location_id = phpgw::get_var('location_id');
					$component_id = phpgw::get_var('component_id');
					$check_list->set_location_id($location_id);
					$check_list->set_component_id($component_id);
				}
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

			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			if ($status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0 && !$billable_hours)
			{
				$error_message = lang("Please enter billable hours");
				if (phpgw::get_var('phpgw_return_as') != 'json')
				{
					phpgwapi_cache::message_set($error_message, 'error');
				}
				$error = true;
			}
			else
			{
				$check_list->set_delta_billable_hours($billable_hours);
			}

			if ($status == controller_check_list::STATUS_DONE && !$error)
			{
				if( $submit_ok || $this->_check_for_required($check_list))
				{
					$check_list->set_status($status);
				}
			}
			else if ($status == controller_check_list::STATUS_CANCELED && !$error)
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

				/**
				 * Add an iCal-event if there is a serie - and the checklist is visited the first time - or assigned is changed
				 */
				if (!$submit_deviation &&
					(($check_list_id && $serie && !phpgw::get_var('check_list_id')) || ($serie && $orig_assigned_to != $assigned_to))
				)
				{
					$bocommon = CreateObject('property.bocommon');
					$current_prefs_user = $bocommon->create_preferences('common', $GLOBALS['phpgw_info']['user']['account_id']);
					$from_address = "{$GLOBALS['phpgw_info']['user']['fullname']}<{$current_prefs_user['email']}>";
					$from_name = $GLOBALS['phpgw_info']['user']['fullname'];

					$to_name = $GLOBALS['phpgw']->accounts->id2name($assigned_to);
					$prefs_target = $bocommon->create_preferences('common', $assigned_to);
					$to_address = $prefs_target['email'];

					if (!$start_date = $check_list->get_planned_date())
					{
						$start_date = $check_list->get_deadline();
					}
					$startTime = $start_date + 8 * 3600;

					$endTime = $startTime + ( (float)$serie['service_time'] * 3600 ) + ( (float)$serie['controle_time'] * 3600 );

					if ($check_list->get_component_id() > 0)
					{
						$location_info = $GLOBALS['phpgw']->locations->get_name($check_list->get_location_id());

						if (substr($location_info['location'], 1, 8) == 'location')
						{
							$type_info = explode('.', $location_info['location']);
							$level = $type_info[2];
							$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $check_list->get_location_id(),
								'id' => $component_id), true);
							$location_code = $item_arr['location_code'];
							$location_name = execMethod('property.bolocation.get_location_name', $location_code);
							$short_desc = $location_name;
						}
						else
						{
							$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $check_list->get_location_id(),
								'id' => $check_list->get_component_id()));

							$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

							$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array(
								'location_id' => $check_list->get_location_id(), 'id' => $check_list->get_component_id()));
						}

					}

					$repeat_type_array = array
						(
						"0" => lang('day'),
						"1" => lang('week'),
						"2" => lang('month'),
						"3" => lang('year')
					);

					$subject = "{$repeat_type_array[$serie['repeat_type']]}/{$serie['repeat_interval']}";
					$subject .= "::{$serie['title']}::{$short_desc}";

					$link_backend = $GLOBALS['phpgw']->link('/index.php', array(
						'menuaction' => 'controller.uicheck_list.add_check_list',
						'control_id' => $check_list->get_control_id(),
						'location_id' => $check_list->get_location_id(),
						'component_id' => $check_list->get_component_id(),
						'serie_id' => $check_list->get_serie_id(),
						'type' => 'component',
						'assigned_to' => $check_list->get_assigned_to(),
						'deadline_current' => true
						), false, true, true);

					$link_mobilefrontend = $GLOBALS['phpgw']->link('/mobilefrontend/index.php', array(
						'menuaction' => 'controller.uicheck_list.add_check_list',
						'control_id' => $check_list->get_control_id(),
						'location_id' => $check_list->get_location_id(),
						'component_id' => $check_list->get_component_id(),
						'serie_id' => $check_list->get_serie_id(),
						'type' => 'component',
						'assigned_to' => $check_list->get_assigned_to(),
						'deadline_current' => true
						), false, true, true);

					$html_description = "<a href ='{$link_mobilefrontend}'>Serie#" . $check_list->get_serie_id() . '::Mobilefrontend</a><br/><br/>';
					$html_description .= "<a href ='{$link_backend}'>Serie#" . $check_list->get_serie_id() . '::Backend</a>';

					$_serie_id = $check_list->get_serie_id();
					$text_description = str_replace('&amp;', '&', "Serie#{$_serie_id}::Mobilefrontend:\\n{$link_mobilefrontend}\\n\\nSerie#{$_serie_id}::Backend:\\n{$link_backend}");
/*
					if ($from_address && $to_address)
					{
						$this->sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $html_description, $text_description, $location);
					}
					else
					{
						phpgwapi_cache::message_set("Mangler epostadresse til avsender eller addresat - eller begge", 'error');
					}
*/				}

				if ($check_list_id > 0)
				{
					if($submit_ok)
					{
						$check_list->set_id($check_list_id);
						$this->_set_required_control_items($check_list);
					}

					$ret = array();
					if(!$submit_deviation)
					{
						$ret = $this->notify_supervisor($check_list);
					}

					if (phpgw::get_var('phpgw_return_as') == 'json')
					{
						if($ret)
						{
							return $ret;
						}
						else
						{
							return array("status" => 'ok', 'message' => lang('Ok'));
						}
					}
					else if($submit_deviation)
					{
						$this->redirect(array('menuaction' => 'controller.uicase.add_case',
							'check_list_id' => $check_list_id));
					}
					else
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list',
							'check_list_id' => $check_list_id));
					}
				}
				else
				{
					if (phpgw::get_var('phpgw_return_as') == 'json')
					{
						return array('status' => 'error', 'message' => $error_message ? $error_message : lang('Error'));
					}
					$this->edit_check_list($check_list);
				}
			}
			else
			{
				if (phpgw::get_var('phpgw_return_as') == 'json')
				{
					return array('status' => 'error', 'message' => $error_message ? $error_message : lang('Error'));
				}
				else if ($check_list->get_id() > 0)
				{
					$this->edit_check_list($check_list);
				}
				else
				{
					$this->redirect(array('menuaction' => 'controller.uicheck_list.add_check_list',
						'control_id' => $control_id,
						'location_id' => $location_id,
						'component_id' => $component_id,
						'serie_id' => $serie_id,
						'deadline_ts' => $deadline_date_ts,
						'type' => $type,
						'assigned_to' => $assigned_to,
						'status' => $status,
						'check_list_errors' => $check_list->get_error_msg_array(),
						//	'billable_hours' => $billable_hours
					));
				}
			}
		}

		function get_files2( $location_id, $data )
		{
			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$doc_types = isset($config->config_data['document_cat']) && $config->config_data['document_cat'] ? $config->config_data['document_cat'] : array();
			$sodocument = CreateObject('property.sodocument');

			$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);
			$type_arr = explode('.', $loc_arr['location']);
			if (count($type_arr) != 4)
			{
				return array();
			}

			$type = $type_arr[1];
			$entity_id = $type_arr[2];
			$cat_id = $type_arr[3];

			$document_list = array();
			foreach ($doc_types as $doc_type)
			{
				if ($doc_type)
				{
					$document_list = array_merge($document_list, $sodocument->read_at_location(array(
							'entity_id' => $entity_id, 'cat_id' => $cat_id, 'p_num' => $data['id'],
							'doc_type' => $doc_type, 'allrows' => true)));
				}
			}

//			$valid_types = isset($config->config_data['document_valid_types']) && $config->config_data['document_valid_types'] ? str_replace ( ',' , '|' , $config->config_data['document_valid_types'] ) : '.pdf';

			$values = array();

			$lang_view = lang('click to view file');
			foreach ($document_list as $entry)
			{
				$link_file_data = array
					(
					'menuaction' => 'property.uidocument.view_file',
					'id' => $entry['document_id'],
					'p_num' => $data['id'],
					'cat_id' => $cat_id,
					'entity_id' => $entity_id,
				);

				$values[] = array
					(
					'document_id' => $entry['document_id'],
					'file_name' => $entry['document_name'],
					'file_name' => '<a href="' . $GLOBALS['phpgw']->link('/index.php', $link_file_data) . "\" target='_blank' title='{$lang_view}'>{$entry['document_name']}</a>",
					'link' => $entry['link'],
					'title' => $entry['title'],
					'doc_type' => $entry['doc_type'],
					'document_date' => $GLOBALS['phpgw']->common->show_date($entry['document_date'], $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']),
				);
			}

			$generic_document = CreateObject('property.sogeneric_document');
			$params['location_id'] = $location_id;
			$params['order'] = 'name';
			$params['allrows'] = true;
			$generic_document_list = array();
			foreach ($doc_types as $doc_type)
			{
				if ($doc_type)
				{
					$params['cat_id'] = $doc_type;
					$generic_document_list = array_merge($generic_document_list, $generic_document->read($params));
				}
			}

			foreach ($generic_document_list as $entry)
			{
				$link_file_data = array
					(
					'menuaction' => 'property.uigeneric_document.view_file',
					'file_id' => $entry['id']
				);

				$values[] = array
					(
					'document_id' => $entry['id'],
					'file_name' => $entry['name'],
					'file_name' => '<a href="' . $GLOBALS['phpgw']->link('/index.php', $link_file_data) . "\" target='_blank' title='{$lang_view}'>{$entry['name']}</a>"
				);
			}


			return $values;
		}

		function get_files( $location_id, $data )
		{
			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$location_data = explode('-', $data['location_code']);
			$loc1 = isset($location_data[0]) && $location_data[0] ? $location_data[0] : 'dummy';

			$loc_arr = $GLOBALS['phpgw']->locations->get_name($location_id);

			$type_arr = explode('.', $loc_arr['location']);

			if (count($type_arr) != 4)
			{
				return array();
			}

			$type = $type_arr[1];
			$entity_id = $type_arr[2];
			$cat_id = $type_arr[3];
			$category_dir = "{$type}_{$entity_id}_{$cat_id}";

			$files = $vfs->ls(array(
				'string' => "/property/{$category_dir}/{$loc1}/{$data['id']}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$values = array();
			foreach ($files as $file)
			{
				$values[] = array
					(
					'name' => $file['name'],
					'directory' => $file['directory'],
					'file_id' => $file['file_id'],
					'mime_type' => $file['mime_type']
				);
			}

			$link_file_data = array
				(
				'menuaction' => 'property.uientity.view_file',
				'loc1' => $loc1,
				'id' => $data['id'],
				'cat_id' => $cat_id,
				'entity_id' => $entity_id,
				'type' => $type
			);

			foreach ($values as &$_entry)
			{
				$_entry['file_name'] = '<a href="' . $GLOBALS['phpgw']->link('/index.php', $link_file_data) . '&amp;file_name=' . urlencode($_entry['name']) . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>';
			}

			return $values;
		}

		function view_control_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());

			$component_id = $check_list->get_component_id();
			$get_locations = false;
			$get_buildings_on_property = false;

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
				$get_buildings_on_property = true;
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->location_finder->get_location_level($location_code);
			}
			// Fetches buildings on property
			if($get_buildings_on_property)
			{
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			$data = array
				(
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'get_locations'	=> $get_locations,
				'current_year' => $year,
				'current_month_nr' => $month,
				'building_location_code' => $building_location_code,
				'location_level' => $level
			);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'base', 'edit_component.js');
			self::add_javascript('controller', 'base', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/check_list_top_section',
				'check_list/fragments/nav_control_plan', 'check_list/view_control_info',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		function view_control_details()
		{
			$control_id = phpgw::get_var('control_id');

			$control = $this->so_control->get_single($control_id);

			$check_list_id = phpgw::get_var('check_list_id', 'int');

			$check_list = $this->so->get_single($check_list_id);

			$component_id = $check_list->get_component_id();
			$files = array();

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id,
					'id' => $component_id));
				$files = $this->get_files2($location_id, $component_arr);
			}

			$data = array
				(
				'control' => $control,
				'files' => $files
			);

			self::render_template_xsl('check_list/view_control_details', $data);
		}

		function view_control_items()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);

			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$saved_groups_with_items_array = array();

			//Populating array with saved control items for each group
			foreach ($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(),
					"control_items" => $saved_control_items);
			}

			$data = array
				(
				'saved_groups_with_items_array' => $saved_groups_with_items_array,
				'check_list' => $check_list
			);

			self::render_template_xsl('check_list/view_control_items', $data);
		}

		public function print_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so->get_single($check_list_id);

			$control = $this->so_control->get_single($check_list->get_control_id());
			$control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$location_code = $check_list->get_location_code();
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			$level = $this->location_finder->get_location_level($location_code);
			//var_dump($location_array);

			$saved_groups_with_items_array = array();

			//Populating array with saved control items for each group
			foreach ($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array(
					'control_group' => $control_group->toArray(),
					'control_items' => $saved_control_items
					);
			}

			$data = array
				(
				'saved_groups_with_items_array' => $saved_groups_with_items_array,
				'check_list' => $check_list,
				'control' => $control->toArray(),
				'location_array' => $location_array,
				'location_level' => $level
			);

			self::render_template_xsl('check_list/print_check_list', $data);
		}

		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id, "open");

			return json_encode($check_list);
		}

		// Returns open cases for a check list as JSON
		public function get_cases_for_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_items_with_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", null);

			return json_encode($check_items_with_cases);
		}


		function add_billable_hours()
		{
			$error = '';

			if (!$this->add && !$this->edit)
			{
				return json_encode(array(
					"status" => 'not_saved',
					'message' => '',
					'error'	=> 'no_access'
					));
			}
			$check_list_id = phpgw::get_var('check_list_id');
			$billable_hours = phpgw::get_var('billable_hours', 'float');
			$check_list = $this->so->get_single($check_list_id);
			$check_list->set_delta_billable_hours($billable_hours);

			if ($this->so->store($check_list))
			{
				return array('status' => 'ok');
			}
			else
			{
				return array("status" => 'not_saved', 'message' => '', 'error'=> '');
			}
		}

		/**
		 * Public function for updateing status for a check list
		 *
		 * @return json encoded array with status saved or not saved
		 */
		public function update_status()
		{
			$error = '';

			if (!$this->add && !$this->edit)
			{
				return json_encode(array(
					"status" => 'not_saved',
					'message' => '',
					'error'	=> 'no_access'
					));
			}

			$check_list_id = phpgw::get_var('check_list_id');
			$check_list_status = phpgw::get_var('status');
			$check_list = $this->so->get_single($check_list_id);

//
			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();
			$ok = true;

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;
			if ($check_list_status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0)
			{
				phpgwapi_cache::message_set(lang("Please enter billable hours"), 'error');
				$ok = false;
				$error = 'missing_billable_hours';
			}
//
			if (!$this->_check_for_required($check_list) || !$ok)
			{
				$messages = phpgwapi_cache::message_get(true);
				$message = '';
				foreach ($messages as $_type => $_message)
				{
					if ($_type == 'error')
					{
						$i = 1;
						foreach ($_message as $__message)
						{
							$message.= "#{$i}: " . preg_replace("/<\/br[^>]*>\s*\r*\n*/is", "\n", $__message['msg']) . "\n";
							$i++;
						}
					}
				}
				return array(
					"status" => 'not_saved',
					'message' => $message,
					'error'=> $error ? $error : 'missing_required',
					'input_text' => lang("Please enter billable hours"),
					'lang_new_value' => lang('new value')
					);
			}

			if ($check_list_status == controller_check_list::STATUS_DONE)
			{
				$completed_date = $check_list->get_deadline() < time() ? $check_list->get_deadline() : time();
				$check_list->set_completed_date($completed_date);
			}
			else
			{
				$check_list_status = controller_check_list::STATUS_NOT_DONE;
				$check_list->set_completed_date(0);
			}

			$check_list->set_status($check_list_status);

			if ($this->so->store($check_list))
			{

				/**
				 * create message....
				 */

				return $this->notify_supervisor($check_list);

			}
			else
			{
				return array("status" => 'not_saved', 'message' => '', 'error'=> 'missing_required');
			}
		}


		private function notify_supervisor($check_list)
		{
			$message = '';
			if($check_list->get_status() == controller_check_list::STATUS_DONE)
			{
				$contacts	 = CreateObject('phpgwapi.contacts');

				$message_ret = $this->create_messages($check_list->get_control_id(), $check_list->get_id(), $check_list->get_location_code());

				/**
				 * Sigurd: in case ticket alert in not sufficient - some extra magic, not implemented so far
				 */
				/**
				  $location		 = array();
				  $_location_arr	 = explode('-', $data['location_code']);
				  $i				 = 1;
				  foreach ($_location_arr as $_loc)
				  {
				  $location["loc{$i}"] = $_loc;
				  $i++;
				  }

				  $ticket_cat_id = $this->so_control->get_single($check_list->get_control_id())->get_ticket_cat_id();

				  $assignedto = execMethod('property.boresponsible.get_responsible', array('location'	 => $location,
				  'cat_id'	 => $ticket_cat_id));

				  if ($assignedto)
				  {
				  $group_or_user = get_class($GLOBALS['phpgw']->accounts->get($assignedto));
				  }

				  if ($group_or_user == "phpgwapi_group")
				  {
				  $group_id = $assignedto;
				  $assignedto		 = 0;
				  }
				 */


				/**
				 * "1" => supervisor
				 * "2" => operator
				 * "4" => District - supervisor
				 */
				if($message_ret['message_ticket_id'])
				{
					$role = 1 | 4;
					$message = lang('%1 case(s) sent as message %2', $message_ret['num_cases'], $message_ret['message_ticket_id']);
				}
				else
				{
					$role = 1;
				}

				$to_notify = createObject('controller.sosettings')->get_user_with_role($check_list->get_control_id(), $check_list->get_location_code(), $role);
				$validator = CreateObject('phpgwapi.EmailAddressValidator');
				$toarray = array();
				foreach ($to_notify as $entry)
				{
					$account_lid = $GLOBALS['phpgw']->accounts->get($entry['id'])->lid;
					$person_id = $GLOBALS['phpgw']->accounts->get($entry['id'])->person_id;

					if ($validator->check_email_address($account_lid))
					{
						$toarray[] = $account_lid;
					}
					else
					{
						$prefs = CreateObject('property.bocommon')->create_preferences('common',$entry['id']);
						if ($validator->check_email_address($prefs['email']))
						{
							$toarray[] = $prefs['email'];
						}
						else
						{
							$contact_data	 = $contacts->read_single_entry($person_id, array('email'));
							$contact['value_contact_email']	 = $contact_data[0]['email'];
							if ($validator->check_email_address($contact['value_contact_email']))
							{
								$toarray[] = $contact['value_contact_email'];
							}
						}
					}
				}

				$rc = false;
				if($toarray)
				{
					$to = implode(';',$toarray);

					$from_name = 'NoReply';
					$config = CreateObject('phpgwapi.config', 'controller')->read();
					if(!empty($config['from_email']))
					{
						$from_address =$config['from_email'];
					}
					else
					{
						$from_address = "NoReply@{$GLOBALS['phpgw_info']['server']['hostname']}";
					}

					$config_frontend = CreateObject('phpgwapi.config', 'mobilefrontend')->read();

					$enforce_ssl = $GLOBALS['phpgw_info']['server']['enforce_ssl'];
					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = !!$config_frontend['backend_ssl'];
					$ticket_link = self::link(array('menuaction' => "property.uitts.view", 'id' => $message_ret['message_ticket_id']), false, true, true);
					$check_list_link = self::link(array('menuaction' => "controller.uicase.view_open_cases", 'check_list_id' => $check_list->get_id()), false, true, true);
					$GLOBALS['phpgw_info']['server']['enforce_ssl'] = $enforce_ssl;

					$control = $this->so_control->get_single($check_list->get_control_id());
					$control_title = $control->get_title();
					$location_desc = $this->get_location_desc($check_list);

					$html = <<<HTML
						<p>$control_title</p>
						<p>{$location_desc['location_name']}</p>
						<p>{$location_desc['short_desc']}</p>
						<p>$message</p>
						<br/>
						<a href="$check_list_link">Sjekkliste (backend)</a>

HTML;

					if(!empty($message_ret['message_ticket_id']))
					{
						$html .= <<<HTML
							<br/>
							<a href="$ticket_link">Melding (backend)</a>
HTML;
					}


					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						$send = CreateObject('phpgwapi.send');
						try
						{
							$subject = "Kontroll gjennomført";
							$rc = $send->msg('email', $to, $subject, $html, '', $cc='', $bcc='',$from_address, $from_name,'html');
						}
						catch (Exception $e)
						{
							$receipt['error'][] = array('msg' => $e->getMessage());
						}
					}
					else
					{
						$receipt['error'][] = array('msg'=>lang('SMTP server is not set! (admin section)'));
					}

					if($rc)
					{
						$message .= "\nVarslet:\n" . implode("\n",$toarray);
					}
				}
			}

			return array(
				'status' => $check_list->get_status(),
				'message' => $message
				);
		}
		public function get_location_desc( $check_list )
		{
			$location_id = $check_list->get_location_id();
			$component_id = $check_list->get_component_id();

			$short_desc = '';
			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$type_info = explode('.', $location_info['location']);
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$short_desc = execMethod('property.soentity.get_short_description', array(
						'location_id' => $location_id, 'id' => $component_id));
				}
			}

			$location_name = execMethod('property.bolocation.get_location_name', $check_list->get_location_code());


			return array(
				'location_name' => $location_name,
				'short_desc' => $short_desc,
				);

		}

		private function create_messages($control_id, $check_list_id, $location_code)
		{

			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", "no_message_registered");

			$case_ids = array();
			foreach ($check_items_and_cases as $check_item)
			{
				foreach ($check_item->get_cases_array() as $case)
				{
					$case_ids[] = $case->get_id();
				}
			}

//			$config = CreateObject('phpgwapi.config', 'controller')->read();
//			$ticket_cat_id = $config['ticket_category'];

			$control = $this->so_control->get_single($control_id);
			$ticket_cat_id = $control->get_ticket_cat_id();
			$message_title = $control->get_title();

			$uicase = createObject('controller.uicase');
			$message_ticket_id = $uicase->send_case_message_step_2($check_list_id,$location_code, $message_title, $ticket_cat_id, $case_ids );

			return array(
				'message_ticket_id'	 => $message_ticket_id,
				'num_cases'			 => count($case_ids)
			);

		}

		public function query()
		{

		}


		/**
		 * Get required items on all groups and for all components registered to the location.
		 * @param object $check_list
		 * @return array
		 */
		private function _get_required_control_items( $check_list )
		{
			$control = $this->so_control->get_single($check_list->get_control_id());

			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());

			$required_control_items = array();
			foreach ($saved_control_groups as $control_group)
			{
				$control_items = $this->so_control_item_list->get_control_items_and_options_by_control_and_group($control->get_id(), $control_group->get_id(), "return_array");
				$component_location_id = $control_group->get_component_location_id();
				$component_criteria = $control_group->get_component_criteria();

				foreach ($control_items as $control_item)
				{
					if ($control_item['required'])
					{
						$control_item['component_location_id'] = $component_location_id;
						$control_item['component_criteria'] = $component_criteria;
						$required_control_items[] = $control_item;
					}
				}
			}
			return $required_control_items;
		}

		private function _set_required_control_items( $check_list )
		{
			$required_control_items = $this->_get_required_control_items($check_list);
			$component_id = $check_list->get_component_id();

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);


				foreach ($required_control_items as $required_control_item)
				{
					$_ok = $this->so_case->get_cases_by_component($location_id, $component_id, $required_control_item['id'], $check_list->get_id());
					if (!$_ok)
					{
						$this->_save_required_case($check_list, $required_control_item);
					}
				}

			}

		}

		private function _save_required_case($check_list, $control_item)
		{
			if (!$this->add && !$this->edit)
			{
				return json_encode(array("status" => "not_saved"));
			}

			$check_list_id = $check_list->get_id();
			$control_item_id = $control_item['id'];
			$case_descr = phpgw::get_var('case_descr');
			$type = $control_item['type'];
			$status = controller_check_list::STATUS_DONE;
			$location_code = $check_list->get_location_code();
			$component_location_id = $check_list->get_location_id();
			$component_id = $check_list->get_component_id();

			$control_id = $check_list->get_control_id();

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
			$case->set_user_id($user_id);
			$case->set_entry_date($todays_date_ts);
			$case->set_modified_date($todays_date_ts);
			$case->set_modified_by($user_id);
			$case->set_status($status);
			$case->set_location_code($location_code);
			$case->set_component_location_id($component_location_id);
			$case->set_component_id($component_id);

			$option = (array)end($control_item['options_array']);
			$option_value = $option['option_value'];

			// Saves selected value from  or measurement
			if ($type == 'control_item_type_2')
			{
				$measurement = 'Dummy values';
				$case->set_measurement($measurement);
			}
			else if ($type == 'control_item_type_3')
			{
				$case->set_measurement($option_value);
			}
			else if ($type == 'control_item_type_4')
			{
				$case->set_measurement($option_value);
			}
			else if ($type == 'control_item_type_5')
			{
				$case->set_measurement($option_value);
			}

			$regulation_reference_option = (array)end($control_item['regulation_reference_options_array']);
			$regulation_reference_option_value = $regulation_reference_option['option_value'];
			$case->set_regulation_reference($regulation_reference_option_value);

			$case_id = CreateObject('controller.socase')->store($case);

			if ($case_id > 0)
			{
				return json_encode(array("status" => "saved"));
			}
			else
			{
				return json_encode(array("status" => "not_saved"));
			}
		}

		/**
		 * Check for required items on all groups and for all components registered to the location.
		 * @param object $check_list
		 * @return bool
		 * */
		private function _check_for_required( $check_list )
		{
			$ok = true;

			$required_control_items = $this->_get_required_control_items($check_list);

			$components_at_location = array();

			$component_id = $check_list->get_component_id();

			if ($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				$location_info = $GLOBALS['phpgw']->locations->get_name($location_id);

				if (substr($location_info['location'], 1, 8) == 'location')
				{
					$get_locations = true;

					$type_info = explode('.', $location_info['location']);
					$level = $type_info[2];
					$item_arr = createObject('property.solocation')->read_single('', array('location_id' => $location_id,
						'id' => $component_id), true);
					$location_code = $item_arr['location_code'];
					$check_list->set_location_code($location_code);
					$location_name = execMethod('property.bolocation.get_location_name', $location_code);
					$short_desc = $location_name;
				}
				else
				{
					$short_desc = execMethod('property.soentity.get_short_description', array(
						'location_id' => $location_id, 'id' => $component_id));
				}

				foreach ($required_control_items as $required_control_item)
				{
					$_ok = $this->so_case->get_cases_by_component($location_id, $component_id, $required_control_item['id'], $check_list->get_id());
					if (!$_ok)
					{
						$error_message = lang('missing value for required') . "</br>";
						$error_message .= "\"{$required_control_item['title']}\"</br>";
						$error_message .= $short_desc;
						$error_message .= "</br>";
						phpgwapi_cache::message_set($error_message, 'error');
//						echo $error_message;
						$ok = false;
					}
				}

			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_code_search_components = $location_code;
				$type = 'location';

				foreach ($required_control_items as $required_control_item)
				{
					$criterias_array = array();

					$component_location_id = $required_control_item['component_location_id'];
					$criterias_array['location_id'] = $component_location_id;
					$criterias_array['location_code'] = $location_code_search_components;
					$criterias_array['allrows'] = true;

					$component_criteria = $required_control_item['component_criteria'];

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

					if ($_components_at_location)
					{
						foreach ($_components_at_location as &$_component_at_location)
						{
							$_ok = $this->so_case->get_cases_by_component($_component_at_location['location_id'], $_component_at_location['id'], $required_control_item['id'], $check_list->get_id());

							if (!$_ok)
							{
								$error_message = "mangler registrering for required</br>";
								$error_message .= "{$required_control_item['title']}</br>";
								$error_message .= execMethod('property.soentity.get_short_description', array(
									'location_id' => $_component_at_location['location_id'], 'id' => $_component_at_location['id']));
								$error_message .= "</br>";
								phpgwapi_cache::message_set($error_message, 'error');
//								echo $error_message;
								$ok = false;
							}
						}
					}
				}
			}
			$config = CreateObject('phpgwapi.config', 'controller');
			$config->read();

			$required_actual_hours = isset($config->config_data['required_actual_hours']) && $config->config_data['required_actual_hours'] ? $config->config_data['required_actual_hours'] : false;

			if ($check_list->get_status == controller_check_list::STATUS_DONE && $required_actual_hours && $check_list->get_billable_hours() == 0)
			{
				phpgwapi_cache::message_set(lang("Please enter billable hours"), 'error');
				$ok = false;
			}

			return $ok;
		}

		/**
		 *
		 * @param string $from_name
		 * @param string $from_address
		 * @param string $to_name
		 * @param string $to_address
		 * @param int $startTime
		 * @param int $endTime
		 * @param string $subject
		 * @param string $html_description
		 * @param string $text_description
		 * @param string $location
		 * @return type
		 */
		function sendIcalEvent( $from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $html_description, $text_description, $location )
		{
//			https://www.exchangecore.com/blog/sending-outlookemail-calendar-events-php/

			$domain = $GLOBALS['phpgw_info']['server']['hostname'];

			//Create Email Headers
			$mime_boundary = "----Meeting Booking----" . md5(time());

			//Create Email Body (HTML)
			$message = <<<HTML
			--{$mime_boundary}
			Content-Type: text/html; charset=UTF-8
			Content-Transfer-Encoding: 8bit

			<html>
			<body>
			<p>Dear {$to_name}</p>
			<p>{$html_description}</p>
			</body>
			</html>
			--{$mime_boundary}
HTML;
			//Create Email Body (HTML)
			$message = <<<HTML
			<html>
			<body>
			<p>Dear {$to_name}</p>
			<p>{$html_description}</p>
			</body>
			</html>
HTML;

			$last_modified = date("Ymd\TGis");
			$uid = date("Ymd\TGis", $startTime) . rand() . "@" . $domain;
			$dtstamp = date("Ymd\TGis");
			$dtstart = date("Ymd\THis", $startTime);
			$dtend = date("Ymd\THis", $endTime);
			$timezone = $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'];


			$ical = <<<HTML
BEGIN:VCALENDAR
PRODID: controller {$domain}
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
X-WR-TIMEZONE:Europe/Oslo
BEGIN:VEVENT
ORGANIZER;CN="{$to_name}":MAILTO:{$to_address}
ATTENDEE;CN="{$to_name}";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:{$to_address}
DTSTAMP:{$dtstamp}
DTSTART:{$dtstart}
DTEND:{$dtend}
SEQUENCE:0
STATUS:TENTATIVE
SUMMARY:{$subject}
LOCATION:{$location}
DESCRIPTION:{$text_description}
UID:{$uid}
BEGIN:VALARM
TRIGGER:-PT15M
ACTION:DISPLAY
DESCRIPTION:Reminder
END:VALARM
END:VEVENT
END:VCALENDAR
HTML;

//ORGANIZER;CN="{$from_name}":MAILTO:{$from_address}
//ATTENDEE;CN="{$to_name}";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:{$to_address}
//			$message .= $ical;
			$message = $ical;


			//Might work....
			$attachment = array
				(
				//	'content'		=> base64_encode($ical),
				'content' => $ical,
				'name' => 'meeting.ics',
				//	'encoding'		=> 'base64',//'7bit',
				'encoding' => '7bit',
				'type' => "text/calendar;charset=utf-8; method=REQUEST",
				'disposition' => 'inline'
			);

//test
			$mail = createObject('phpgwapi.mailer_smtp');
			$mail->Subject = $subject;
			$mail->Body = <<<HTML
			<html>
			<body>
			<p>{$to_name}:</p>
			<p>{$html_description}</p>
			</body>
			</html>
HTML;
			$mail->AltBody = $text_description; // For non HTML email client
			$mail->Ical = $ical; //Your manually created ical code
			$mail->IsHTML(true);
			$mail->isSMTP();
			$mail->AddAddress($to_address);


			$from = str_replace(array('[', ']'), array('<', '>'), $from_address);
			$from_array = explode('<', $from);
			unset($from);
			if (count($from_array) == 2)
			{
				$mail->From = trim($from_array[1], '>');
				$mail->FromName = $from_array[0];
			}
			else
			{
				$mail->From = $from_array[0];
				$mail->FromName = $from_name;
			}

			try
			{
				$mail->Send();
			}
			catch (Exception $e)
			{
				phpgwapi_cache::message_set($e->getMessage(), 'error');
			}
			return;
//test
			$rc = false;
			if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
			{
				$send = CreateObject('phpgwapi.send');
				try
				{
					$rc = $send->msg('email', $to_address, $subject, $message, $msgtype = 'Ical', $cc = '', $bcc = '', $from_address, $from_name, 'html', $mime_boundary);//, array($attachment));
				}
				catch (Exception $e)
				{
					phpgwapi_cache::message_set($e->getMessage(), 'error');
				}
			}
			else
			{
				phpgwapi_cache::message_set(lang('SMTP server is not set! (admin section)'), 'error');
			}

			return $rc;
		}

		function get_report_()
		{
			$config = createObject('phpgwapi.config', 'property')->read();


			$check_list_id = phpgw::get_var('check_list_id');
			$case_location_code = phpgw::get_var('location_code');

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

//			$preview = true;

			$report_info = $this->_get_report_info($check_list_id, $case_location_code);

			$report_intro = $report_info['control']->get_report_intro();

			$_component_children = (array)$report_info['component_children'];

			$component_children = array();
			foreach ($_component_children as $component_child)
			{
				$component_children[$component_child['location_id']][$component_child['id']] = $component_child;
			}
			unset($component_child);

			$completed_items = $this->so->get_completed_item($check_list_id);

			$location_code = $report_info['check_list']->get_location_code();
			$completed_date = $report_info['check_list']->get_completed_date();


			$loction_name_info = createObject('property.solocation')->get_part_of_town($location_code);
	//		_debug_array($location_code);


	//		die();

			$location_id = $report_info['check_list']->get_location_id();
			$item_id	 = $report_info['check_list']->get_component_id();

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



			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date		 = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);

			$pdf = CreateObject('phpgwapi.pdf');
			// Modified to use the local file if it can
			$pdf->openHere('Fit');

			$pdf->ezSetMargins(50, 70, 50, 50);
			$pdf->selectFont('Helvetica');

			// put a line top and bottom on all the pages
			$all = $pdf->openObject();
			$pdf->saveState();


			$pdf->setStrokeColor(0, 0, 0, 1);
			$pdf->line(20, 40, 578, 40);
			//	$pdf->line(20,820,578,820);
			//	$pdf->addText(50,823,6,lang('order'));
			$pdf->addText(50, 28, 6, $config['org_name']);
			$pdf->addText(300, 28, 6, $date);

			if ($preview)
			{
				$pdf->setColor(1, 0, 0);
				$pdf->addText(200, 400, 40, lang('DRAFT'), -10);
				$pdf->setColor(1, 0, 0);
			}

			$pdf->restoreState();
			$pdf->closeObject();

			// note that object can be told to appear on just odd or even pages by changing 'all' to 'odd'
			// or 'even'.
			$pdf->addObject($all, 'all');

//			$pdf->ezSetDy(-100);

			$pdf->ezStartPageNumbers(500, 28, 6, 'right', '{PAGENUM} ' . lang('of') . ' {TOTALPAGENUM}', 1);


			$pdf->ezText($report_info['control']->get_control_area_name(), 20);
			$pdf->ezText($report_info['control']->get_title(), 20);
//			$pdf->selectFont('Courier');


			$inspectors = createObject('controller.sosettings')->get_inspectors($check_list_id);
//			_debug_array($inspectors);die();

			$selected_inspectors = array();
			foreach ($inspectors as $inspector)
			{
				if($inspector['selected'])
				{
					$prefs =CreateObject('phpgwapi.preferences',$inspector['id'])->read();

					$inspector_name = $inspector['name'];

					if(!empty($prefs['controller']['certificate']))
					{
						$inspector_name .= " ( {$prefs['controller']['certificate']} )";
					}

					$selected_inspectors[] = $inspector_name;
				}
			}
			unset($inspector);

			$data = array(
				array
				(
					'col1' => "<b>Rapportnummer:</b>\n" . $check_list_id
							. "\n<b>Kontrolldato:</b>\n" . $GLOBALS['phpgw']->common->show_date($completed_date, $dateformat),
					'col2' => "<b>Sted:</b>\n" . $report_info['component_array']['xml_short_desc']
				),
				array
				(
					'col1' => "<b>Inspektør:</b>\n" . implode("\n", $selected_inspectors),
					'col2' => "<b>Bydel:</b>\n" . $loction_name_info['part_of_town']
				)
			);


			$pdf->ezTable($data, '', '',
				array(
				'gridlines'=> EZ_GRIDLINE_ALL,
				'shadeHeadingCol'=> [0.9,0.9,0.7],
				'showHeadings'	 => 0,
				'shaded'		 => 0,
//				'fontSize'		=> 10,
				'xPos' => 0,
				'xOrientation'	 => 'right',
				'width' => 500,
				'cols'			 => array
				(
					'col1'	 => array('width' => 200, 'justification' => 'left'),
					'col2'	 => array('width' => 300, 'justification' => 'left'),
				)
			));

			$data = array(
				array
				(
					'col1' => "<C:showimage:{$this->vfs->basedir}/{$file['directory']}/{$file['name']} 500>",
				),
			);

			$pdf->ezTable($data, '', '', array(
				'showHeadings' => 0,
				'shaded'	 => 0,
				'xPos' => 0,
				'xOrientation'	 => 'right',
				'width' => 500,
				'gridlines'		 => EZ_GRIDLINE_HEADERONLY,
				'cols'			 => array
				(
					'col1'	 => array('width' => 500, 'justification' => 'left'),
				)
			));
//			_debug_array($report_info['check_list']);die();

			$pdf->ezSetDy(-20);


			$html2text	 = createObject('phpgwapi.html2text', $report_intro, array('width' => 0));
			$pdf->ezText($html2text->getText(), 10, array('justification' => 'left'));

			$pdf->ezSetDy(-20);

			$pdf->ezText("Kontrollen er gjennomført av:");

			foreach ($selected_inspectors as $selected_inspector)
			{
				$pdf->ezText($selected_inspector);
			}

			$pdf->ezNewPage();

			$count_completed = 0;

			foreach ($completed_items as $completed_item_location)
			{
				$count_completed += count($completed_item_location);
			}
			unset($completed_item_location);


			$data = array(
				array
				(
					'col1' => "Antall kontrollerte objekter",
					'col2' => $count_completed . ' av ' . count($_component_children),
				),
				array
				(
					'col1' => "Antall åpne saker",
					'col2' => $report_info['check_list']->get_num_open_cases(),
				),
				array
				(
					'col1' => "Antall korrigerte saker",
					'col2' => $report_info['check_list']->get_num_corrected_cases(),
				),
			);

			$pdf->ezTable($data, '', '', array(
				'showHeadings' => 0,
				'shaded'	 => 0,
				'xPos' => 0,
				'xOrientation'	 => 'right',
				'width' => 400,
				'gridlines'		 => EZ_GRIDLINE_HEADERONLY,
				'cols'			 => array
				(
					'col1'	 => array('width' => 300, 'justification' => 'left'),
					'col2'	 => array('width' => 100, 'justification' => 'left'),
				)
			));

			$pdf->ezSetDy(-20);

			//Summary of findings:

			$findings = $this->so->get_findings_summary($check_list_id);

	//		_debug_array($findings);
	//		die();


			$data = array();

			for ($i = 0; $i < 5; $i++)
			{
				$data[] = array(
					'col1' => lang('condition degree') . ' - ' .$i,
					'col2' => (int)$findings['condition_degree'][$i],
				);
			}

			for ($i = 0; $i < 5; $i++)
			{
				$data[] = array(
					'col1' => lang('consequence') . ' - ' .$i,
					'col2' => (int)$findings['consequence'][$i],
				);
			}


			if(array_sum(array_values($findings['consequence'])) > 0 || array_sum(array_values($findings['condition_degree'])) > 0 )
			{
				$pdf->ezTable($data, array('col1' => '<b>Klassifisering</b>', 'col2' => '<b>Antall</b>'), '', array(
					'showHeadings' => 1,
					'shaded'	 => 0,
					'xPos' => 0,
					'xOrientation'	 => 'right',
					'width' => 400,
					'gridlines'		 => EZ_GRIDLINE_ALL,
					'cols'			 => array
					(
						'col1'	 => array('width' => 300, 'justification' => 'left'),
						'col2'	 => array('width' => 200, 'justification' => 'left'),
					)
				));

				$pdf->ezSetDy(-20);
			}


//
//			foreach ($report_info['component_array'] as $key => $value)
//			{
//				_debug_array($value);
//			}

//				_debug_array($report_info['component_array']);
//				_debug_array($report_info);



			$i = 1;

			foreach ($report_info['open_check_items_and_cases'] as $check_item)
			{
//				_debug_array($check_item->get_control_item());

				$cases_array = $check_item->get_cases_array();

//				_debug_array($cases_array);die();
				$data_case = array();

//				_debug_array($component_children);
//				_debug_array($completed_items);

//die();


				foreach ($check_item->get_cases_array() as $case)
				{
					$n = 1;

					$entry = array
					(
						'col1' => "<b>#{$i}</b>",
						'col2' => $check_item->get_control_item()->get_title(),
					);

					$location_identificator = 0;

					if($case->get_component_child_item_id())
					{

						$location_identificator = $case->get_component_child_location_id() . '_' .$case->get_component_child_item_id();
					}

					$data_case[$location_identificator][] = $entry;

					$case->get_component_child_location_id();//:protected] => 456
                    $case->get_component_child_item_id();//:protected] => 2835

					if($case->get_component_child_item_id())
					{
						$entry = array
						(
							'col1' => 'komponent',
							'col2' => $component_children[$case->get_component_child_location_id()][$case->get_component_child_item_id()]['short_description']
						);
						$data_case[$location_identificator][] = $entry;
					}


					$case_files = $case->get_case_files();
 					$status_text = lang('closed');

					if($case->get_status() == controller_check_item_case::STATUS_OPEN)
					{
						$status_text = lang('open');
					}
					else if($case->get_status() == controller_check_item_case::STATUS_PENDING)
					{
						$status_text = lang('pending');
					}
					else if($case->get_status() == controller_check_item_case::STATUS_CORRECTED_ON_CONTROL)
					{
						$status_text = lang('corrected on controll');
					}

					$entry = array
					(
						'col1' => lang('status'),
						'col2' => $status_text
					);
 					$data_case[$location_identificator][] = $entry;
					$entry = array
					(
						'col1' => lang('condition degree'),
						'col2' => $case->get_condition_degree()
					);
					$data_case[$location_identificator][] = $entry;
					$entry = array
					(
						'col1' => lang('consequence'),
						'col2' => $case->get_consequence()
					);
					$data_case[$location_identificator][] = $entry;

					$entry = array
					(
						'col1' => 'Verdi',//lang('measurement'),
						'col2' => $case->get_measurement()
					);

					$entry = array
					(
						'col1' => 'Verdi',//lang('regulation_reference'),
						'col2' => $case->get_regulation_reference()
					);

					$data_case[$location_identificator][] = $entry;

					$entry = array
					(
						'col1' => lang('descr'),
						'col2' => $case->get_descr()
					);
					$data_case[$location_identificator][] = $entry;

					$entry = array
					(
						'col1' => lang('proposed counter measure'),
						'col2' => $case->get_proposed_counter_measure()
					);
					$data_case[$location_identificator][] = $entry;

					foreach ($case_files as $case_file)
					{
						$entry = array
						(
							'col1' => lang('picture') . " #{$i}_{$n}",
							'col2' => "<C:showimage:{$this->vfs->basedir}/{$case_file['directory']}/{$case_file['name']} 90>",
						);
						$data_case[$location_identificator][] = $entry;
						$n ++;
					}

					$i++;

//					_debug_array($case);

//die();
				}


				if(false)//$case_files)
				{
	//				_debug_array($case_files);die();
					$data = array();

					foreach ($case_files as $case_file)
					{
						$entry = array
						(
							'col1' => "<C:showimage:{$this->vfs->basedir}/{$case_file['directory']}/{$case_file['name']} 90>",
						);
						$data[] = $entry;
					}

					$pdf->ezTable($data, '', '', array(
						'showHeadings' => 0,
						'shaded'	 => 0,
						'xPos' => 0,
						'xOrientation'	 => 'right',
						'width' => 500,
						'gridlines'		 => EZ_GRIDLINE_ALL,
						'cols'			 => array
						(
							'col1'	 => array('width' => 500, 'justification' => 'left'),
						)
					));
				}

				$pdf->ezSetDy(-20);

			}


			$custom = createObject('property.custom_fields');

			$soentity = CreateObject('property.soentity');
	//		$component_children = array();
			$completed_list = array();
			$reported_cases = array();
			foreach ($_component_children as &$component_child)
			{
				$data = array();
				$location_identificator = $component_child['location_id'] . '_' . $component_child['id'];

	//			$component_children[$component_child['location_id']][$component_child['id']] = $component_child;

				$loc1 = !empty($component_child['loc1']) ? $component_child['loc1'] : 'dummy';
				$system_location = $GLOBALS['phpgw']->locations->get_name($component_child['location_id']);
				$system_location_arr = explode('.', $system_location['location']);
				$category_dir = "{$system_location_arr[1]}_{$system_location_arr[2]}_{$system_location_arr[3]}";

				$this->vfs->override_acl = 1;

				$files = $this->vfs->ls(array(
					'orderby' => 'file_id',
					'mime_type'	=> 'image/jpeg',
					'string' => "/property/{$category_dir}/{$loc1}/{$component_child['id']}",
					'relatives' => array(RELATIVE_NONE)));

				$this->vfs->override_acl = 0;

				$file = end($files);

//				_debug_array($component_child);die();

				if(!empty($completed_items[$component_child['location_id']][$component_child['id']]))
				{
					$entry = array
					(
						'col1' => "<C:showimage:{$this->vfs->basedir}/{$file['directory']}/{$file['name']} 90>",
						'col2' => "<b>{$component_child['short_description']}</b>",
						'col3' => 'kontrollert: ' .$GLOBALS['phpgw']->common->show_date( $completed_items[$component_child['location_id']][$component_child['id']]['completed_ts'], $this->dateFormat)
					);

				}
				else
				{

					$entry = array
					(
						'col1' => "<C:showimage:{$this->vfs->basedir}/{$file['directory']}/{$file['name']} 90>",
						'col2' => "<b>{$component_child['short_description']}</b>",
						'col3' => 'Ikke kontrollert'
					);

				}
 				$data[] = $entry;

				$values = array();
				$values['attributes'] = $custom->find('property', $system_location['location'], 0, '', 'ASC', 'attrib_sort', true, true);
				$values = $soentity->read_single(array(
					'location_id' => $component_child['location_id'],
					'id' => $component_child['id'],
					'entity_id' => $system_location_arr[2],
					'cat_id' => $system_location_arr[3],
					), $values
				);
				$values = $custom->prepare($values, 'property', $system_location['location'], true);
//				_debug_array($values);die();
				foreach ($values['attributes'] as  $attribute)
				{

					if($attribute['short_description'])
					{
						continue;
					}

					if($attribute['value'])
					{
						$_value = $attribute['value'];

						if( in_array($attribute['datatype'], array('LB', 'R')) )
						{
							$_choice = array();
							foreach ($attribute['choice'] as $choice)
							{
								if($_value == $choice['id'])
								{
									$_choice[] = $choice['value'];
								}
							}
							$_value = implode(',', $_choice);
						}

						if ($attributes['datatype'] == 'CH')
						{
							$_selected = explode(',', trim($_value, ','));

							$_choice = array();
							foreach ($attribute['choice'] as $choice)
							{
								if(in_array($choice['id'], $_selected))
								{
									$_choice[] = $choice['value'];
								}
							}
							$_value = implode(',', $_choice);
						}


						$entry = array
						(
							'col1' => "",
							'col2' => $attribute['input_text'],
							'col3' => $_value
						);

						$data[] = $entry;
					}

				}

				if($data)
				{
					$pdf->ezTable($data, array('col1' => '<b>Bilde</b>','col2' => '<b>Hva</b>', 'col3' => '<b>Verdi</b>'), 'Delsystem', array(
						'showHeadings' => 1,
						'shaded'	 => 0,
						'xPos' => 0,
						'xOrientation'	 => 'right',
						'width' => 400,
						'gridlines'		 => EZ_GRIDLINE_ALL,
						'cols'			 => array
						(
							'col1'	 => array('width' => 100, 'justification' => 'left'),
							'col2'	 => array('width' => 200, 'justification' => 'left'),
							'col3'	 => array('width' => 200, 'justification' => 'left'),
						)
					));
					$pdf->ezSetDy(-10);

				}

				if(isset($data_case[$location_identificator]))//$cases_array)
				{
					//FIXME
					//$check_item->get_control_item()->get_title();
					$pdf->ezTable($data_case[$location_identificator], '','', array(
						'showHeadings' => 0,
						'shaded'	 => 0,
						'xPos' => 0,
						'xOrientation'	 => 'right',
						'width' => 400,
						'gridlines'		 => EZ_GRIDLINE_ALL,
						'cols'			 => array
						(
							'col1'	 => array('width' => 100, 'justification' => 'left'),
							'col2'	 => array('width' => 400, 'justification' => 'left'),
						)
					));
					$reported_cases[] = $location_identificator;

					$pdf->ezSetDy(-20);
				}
			}


			foreach ($data_case as $key => $values)
			{
				if(in_array($key, $reported_cases ))
				{
					continue;
				}

				//$check_item->get_control_item()->get_title();
				$pdf->ezTable($values, '', '', array(
					'showHeadings' => 0,
					'shaded'	 => 0,
					'xPos' => 0,
					'xOrientation'	 => 'right',
					'width' => 400,
					'gridlines'		 => EZ_GRIDLINE_ALL,
					'cols'			 => array
					(
						'col1'	 => array('width' => 100, 'justification' => 'left'),
						'col2'	 => array('width' => 400, 'justification' => 'left'),
					)
				));

			}



	//		die();


			// Output the pdf as stream, but uncompress
			$pdf->ezStream(array('compress' => 0));
		}

		function get_report($check_list_id = null)
		{
			$inline_images = false;

			$config = createObject('phpgwapi.config', 'property')->read();

			if(!$check_list_id)
			{
				$check_list_id = phpgw::get_var('check_list_id');
			}
			$case_location_code = phpgw::get_var('location_code');

			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			$report_info = $this->_get_report_info($check_list_id, $case_location_code);

			$report_intro = $report_info['control']->get_report_intro();

			$_component_children = (array)$report_info['component_children'];

			$component_children = array();
			foreach ($_component_children as $component_child)
			{
				$component_children[$component_child['location_id']][$component_child['id']] = $component_child;
			}
			unset($component_child);

			$completed_items = $this->so->get_completed_item($check_list_id);

			$location_code = $report_info['check_list']->get_location_code();
			$completed_date = $report_info['check_list']->get_completed_date();

			$loction_name_info = createObject('property.solocation')->get_part_of_town($location_code);

			$location_id = $report_info['check_list']->get_location_id();
			$item_id	 = $report_info['check_list']->get_component_id();

			$dateformat	 = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$date		 = $GLOBALS['phpgw']->common->show_date(time(), $dateformat);

			$report_data = array();

			$webserver_url = $GLOBALS['phpgw_info']['server']['webserver_url'];
			$stylesheets = array();
			$stylesheets[] = "{$webserver_url}/phpgwapi/js/bootstrap/css/bootstrap.min.css";
			$stylesheets[] = "{$webserver_url}/phpgwapi/templates/bookingfrontend/css/fontawesome.all.css";

			$javascripts = array();
			$javascripts[]	 = "{$webserver_url}/phpgwapi/js/popper/popper.min.js";
			$javascripts[]	 = "{$webserver_url}/phpgwapi/js/bootstrap/js/bootstrap.min.js";

			$report_data['stylesheets'] = $stylesheets;
			$report_data['javascripts'] = $javascripts;
			$report_data['inline_images'] = $inline_images;
			$report_data['control_area_name'] = $report_info['control']->get_control_area_name();
			$report_data['title'] = $report_info['control']->get_title();


			$inspectors = createObject('controller.sosettings')->get_inspectors($check_list_id);

			$selected_inspectors = array();
			foreach ($inspectors as $inspector)
			{
				if($inspector['selected'])
				{
					$prefs =CreateObject('phpgwapi.preferences',$inspector['id'])->read();

					$inspector_name = $inspector['name'];

					if(!empty($prefs['controller']['certificate']))
					{
						$inspector_name .= " ( {$prefs['controller']['certificate']} )";
					}

					$selected_inspectors[] = $inspector_name;
				}
			}
			unset($inspector);

			$report_data['inspectors'] = $selected_inspectors;
			$report_data['check_list_id'] = $check_list_id;
			$report_data['completed_date'] = $GLOBALS['phpgw']->common->show_date($completed_date, $dateformat);
			$report_data['where'] = $report_info['component_array']['xml_short_desc'];
			$report_data['part_of_town'] = $loction_name_info['part_of_town'];

//			$location_code = $soentity->get_location_code($location_id, $item_id);
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


			$report_data['location_image'] = $file ? self::link(array('menuaction'=>'controller.uicase.get_image', 'component' =>"{$location_id}_{$item_id}")) : '';

			if($file && $inline_images)
			{
				$report_data['image_data'] = base64_encode(file_get_contents("{$this->vfs->basedir}/{$file['directory']}/{$file['name']}"));
			}

			$report_data['report_intro'] = $report_intro;

			$count_completed = 0;

			foreach ($completed_items as $completed_item_location)
			{
				$count_completed += count($completed_item_location);
			}
			unset($completed_item_location);

			$report_data['count_completed'] = $count_completed;
			$report_data['num_open_cases'] = $report_info['check_list']->get_num_open_cases();
			$report_data['num_corrected_cases'] = $report_info['check_list']->get_num_corrected_cases();


			//Summary of findings:

			$findings = $this->so->get_findings_summary($check_list_id);


			$data_condition = array();
			$data_consequence = array();

			for ($i = 0; $i < 5; $i++)
			{
				$data_condition[] = array(
					'text' => lang('condition degree') . ' - ' .$i,
					'value' => (int)$findings['condition_degree'][$i],
				);
			}

			for ($i = 0; $i < 5; $i++)
			{
				$data_consequence[] = array(
					'text' => lang('consequence') . ' - ' .$i,
					'value' => (int)$findings['consequence'][$i],
				);
			}

			$report_data['findings'] = array(
				array('name' => lang('condition degree'), 'values' => $data_condition),
				array('name' => lang('consequence'), 'values' => $data_consequence)
			);

			$i = 1;

			$findings_map = array();
			$include_condition_degree = 0;
			$location_identificator_fallback = 0;
			$data_case = array();
			foreach ($report_info['open_check_items_and_cases'] as $check_item)
			{

				$findings_map[$check_item->get_control_item()->get_id()] = $check_item->get_control_item()->get_title();

//				_debug_array($check_item->get_control_item());

				$include_condition_degree += (int)$check_item->get_control_item()->get_include_condition_degree();

				if($check_item->get_control_item()->get_report_summary())
				{

					$_temp_options = $check_item->get_control_item()->get_options_array();
					$findings_options = array();

					if($_temp_options)
					{
						foreach ($_temp_options as $option_entry)
						{
							$findings_options[$check_item->get_control_item()->get_title()][$option_entry->get_option_value()] = 0;
						}
					}
				}


				foreach ($check_item->get_cases_array() as $case)
				{
//						_debug_array($case);

					if(isset($findings_map[$case->get_control_item_id()]) && $check_item->get_control_item()->get_report_summary())
					{

						foreach ($findings_options[$findings_map[$case->get_control_item_id()]] as $key => &$value)
						{

							if($key == $case->get_measurement())
							{
								$value +=1;
							}
						}
						unset($value);

//					_debug_array($findings_map);
//	_debug_array($findings_options);
//	die();

					}

					$n = 1;

					$entry = array();

					$entry[] = array
					(
						'text' => "#{$i}",
						'value' => $check_item->get_control_item()->get_title(),
					);


					if($case->get_component_child_item_id())
					{

						$location_identificator = $case->get_component_child_location_id() . '_' .$case->get_component_child_item_id();
					}
					else
					{
						$location_identificator = $location_identificator_fallback;
						$location_identificator_fallback ++;
					}

					$case->get_component_child_location_id();//:protected] => 456
                    $case->get_component_child_item_id();//:protected] => 2835

					if($case->get_component_child_item_id())
					{
						$entry[] = array
						(
							'text' => 'komponent',
							'value' => $component_children[$case->get_component_child_location_id()][$case->get_component_child_item_id()]['short_description']
						);
					}

					$case_files = $case->get_case_files();
 					$status_text = lang('closed');

					if($case->get_status() == controller_check_item_case::STATUS_OPEN)
					{
						$status_text = lang('open');
					}
					else if($case->get_status() == controller_check_item_case::STATUS_PENDING)
					{
						$status_text = lang('pending');
					}
					else if($case->get_status() == controller_check_item_case::STATUS_CORRECTED_ON_CONTROL)
					{
						$status_text = lang('corrected on controll');
					}

					$entry[] = array
					(
						'text' => lang('status'),
						'value' => $status_text
					);

					if($check_item->get_control_item()->get_include_condition_degree())
					{
						$entry[] = array
						(
							'text' => lang('condition degree'),
							'value' => $case->get_condition_degree()
						);
						$entry[] = array
						(
							'text' => lang('consequence'),
							'value' => $case->get_consequence()
						);
					}


					$entry[] = array
					(
						'text' => 'Verdi',//lang('measurement'),
						'value' => $case->get_measurement()
					);

					if($case->get_regulation_reference())
					{
						$entry[] = array
						(
							'text' => lang('regulation reference'),
							'value' => $case->get_regulation_reference()
						);
					}

					$entry[] = array
					(
						'text' => lang('descr'),
						'value' => $case->get_descr()
					);

					$entry[] = array
					(
						'text' => lang('proposed counter measure'),
						'value' => $case->get_proposed_counter_measure()
					);

					foreach ($case_files as &$case_file)
					{
						$case_file['text'] = lang('picture') . " #{$i}_{$n}";
//						$case_file['link'] = "{$this->vfs->basedir}/{$case_file['directory']}/{$case_file['name']}";
						$case_file['link'] = self::link(array('menuaction' => 'controller.uicheck_list.view_image', 'img_id' => $case_file['file_id']));
						if($inline_images)
						{
							$case_file['image_data'] = base64_encode(file_get_contents("{$this->vfs->basedir}/{$case_file['directory']}/{$case_file['name']}"));
						}
						$n ++;
					}
					unset($case_file);
					$data_case[$location_identificator][] = array('data' => $entry, 'files' => $case_files);

					$i++;
				}

			}
//			_debug_array($data_case);
			$custom = createObject('property.custom_fields');

			$soentity = CreateObject('property.soentity');
			$completed_list = array();
			$reported_cases = array();
			$$component_child_data = array();
			foreach ($_component_children as &$component_child)
			{
				$data = array();
				$location_identificator = $component_child['location_id'] . '_' . $component_child['id'];

				$loc1 = !empty($component_child['loc1']) ? $component_child['loc1'] : 'dummy';
				$system_location = $GLOBALS['phpgw']->locations->get_name($component_child['location_id']);
				$system_location_arr = explode('.', $system_location['location']);
				$category_dir = "{$system_location_arr[1]}_{$system_location_arr[2]}_{$system_location_arr[3]}";

				$this->vfs->override_acl = 1;

				$files = $this->vfs->ls(array(
					'orderby' => 'file_id',
					'mime_type'	=> 'image/jpeg',
					'string' => "/property/{$category_dir}/{$loc1}/{$component_child['id']}",
					'relatives' => array(RELATIVE_NONE)));

				$this->vfs->override_acl = 0;

				$file = end($files);

				$entry = array
				(
					'text' => lang('name'),
					'value' => $component_child['short_description']
				);

				$data[] = $entry;

				$controlled_text = 'Ikke kontrollert';

				if(!empty($completed_items[$component_child['location_id']][$component_child['id']]))
				{
					$controlled_text = 'kontrollert: ' .$GLOBALS['phpgw']->common->show_date( $completed_items[$component_child['location_id']][$component_child['id']]['completed_ts'], $this->dateFormat);
				}

				$entry = array
				(
					'text' => 'Kontrollstatus',
					'value' => $controlled_text
				);

 				$data[] = $entry;

				$values = array();
				$values['attributes'] = $custom->find('property', $system_location['location'], 0, '', 'ASC', 'attrib_sort', true, true);
				$values = $soentity->read_single(array(
					'location_id' => $component_child['location_id'],
					'id' => $component_child['id'],
					'entity_id' => $system_location_arr[2],
					'cat_id' => $system_location_arr[3],
					), $values
				);
				$values = $custom->prepare($values, 'property', $system_location['location'], true);
//				_debug_array($values);die();
				foreach ($values['attributes'] as  $attribute)
				{

					if($attribute['short_description'])
					{
						continue;
					}

					if($attribute['value'])
					{
						$_value = $attribute['value'];

						if( in_array($attribute['datatype'], array('LB', 'R')) )
						{
							$_choice = array();
							foreach ($attribute['choice'] as $choice)
							{
								if($_value == $choice['id'])
								{
									$_choice[] = $choice['value'];
								}
							}
							$_value = implode(',', $_choice);
						}

						if ($attributes['datatype'] == 'CH')
						{
							$_selected = explode(',', trim($_value, ','));

							$_choice = array();
							foreach ($attribute['choice'] as $choice)
							{
								if(in_array($choice['id'], $_selected))
								{
									$_choice[] = $choice['value'];
								}
							}
							$_value = implode(',', $_choice);
						}


						$entry = array
						(
							'text' => $attribute['input_text'],
							'value' => $_value
						);

						$data[] = $entry;
					}

				}

				if($data)
				{

					$component_child_data[] = array(
						'location_id' => $component_child['location_id'],
						'name'	=> $component_child['short_description'],
						'image_link' => $file ? self::link(array('menuaction'=>'controller.uicase.get_image', 'component' => "{$component_child['location_id']}_{$component_child['id']}")) : '',
						'image_data' => $inline_images ? base64_encode(file_get_contents("{$this->vfs->basedir}/{$file['directory']}/{$file['name']}")) : '',
						'data' => $data,
						'cases' => $data_case[$location_identificator]
						);

				}

				if(isset($data_case[$location_identificator]))//$cases_array)
				{
					$reported_cases[] = $location_identificator;
				}
			}

//			$report_data['component_child_data'] = $component_child_data;

			$section_id = 0;
			$section_location_id = false;
			foreach ($component_child_data as $data_set)
			{
				if($section_location_id != $data_set['location_id'])
				{
					$section_location_id = $data_set['location_id'];
					$system_location = $GLOBALS['phpgw']->locations->get_name($section_location_id);
					$section_id ++;
				}

				$report_data['component_child_data'][$section_id]['section'] = $section_id;
				$report_data['component_child_data'][$section_id]['section_descr'] = $system_location['descr'];
				$report_data['component_child_data'][$section_id]['data'][] = $data_set;
			}

			$report_data['stray_cases'] = array();
			foreach ($data_case as $key => $value_set)
			{
				if(in_array($key, $reported_cases ))
				{
					continue;
				}
				$report_data['stray_cases'] = array_merge($report_data['stray_cases'], $value_set);
			}

			$findings = array();

			foreach ($findings_options as $key => $options)
			{
				$values = array();
				foreach ($options as $text => $value)
				{
					$values[] = array(
						'text' => $text,
						'value' => $value,
					);
				}
				$findings[] = array('name' => $key, 'values' => $values);
			}

			if(!$include_condition_degree)
			{
				$report_data['findings'] = array();
			}

			$report_data['responsible_organization'] = $report_info['control']->get_responsible_organization();//'Barnas Byrom - Forvaltningsavdelingen - Bymiljøetaten';
			$report_data['responsible_logo'] = $report_info['control']->get_responsible_logo();

			$report_data['findings'] = array_merge($report_data['findings'],$findings);
//			_debug_array($report_data['findings']);die();

			$this->render_report($report_data);
		}


		function render_report($report_data)
		{
			$xslttemplates = CreateObject('phpgwapi.xslttemplates');
			$xslttemplates->add_file(array(PHPGW_SERVER_ROOT . '/controller/templates/base/report'));
			$xslttemplates->set_var('phpgw', array('report' => $report_data));

			$xslttemplates->set_output('html5');
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

			echo $html;

//			$this->makePDF($html);
		}

		public function makePDF($stringData)
		{
			include PHPGW_SERVER_ROOT . '/rental/inc/SnappyMedia.php';
			include PHPGW_SERVER_ROOT . '/rental/inc/SnappyPdf.php';
			$tmp_dir = $GLOBALS['phpgw_info']['server']['temp_dir'];
			$myFile = $tmp_dir . "/temp_report_" . strtotime(date('Y-m-d')) . ".html";
			$fh = fopen($myFile, 'w') or die("can't open file");
			fwrite($fh, $stringData);
			fclose($fh);

			$pdf_file_name = $tmp_dir . "/temp_contract_" . strtotime(date('Y-m-d')) . ".pdf";

			//var_dump($config->config_data['path_to_wkhtmltopdf']);
			//var_dump($GLOBALS['phpgw_info']);
			$wkhtmltopdf_executable = '/usr/local/bin/wkhtmltopdf';
			if (!is_file($wkhtmltopdf_executable))
			{
				throw new Exception('wkhtmltopdf not configured correctly');
			}
			$snappy = new SnappyPdf();
			$snappy->setExecutable($wkhtmltopdf_executable); // or whatever else
			$snappy->save($myFile, $pdf_file_name);

			if (!is_file($pdf_file_name))
			{
				throw new Exception('pdf-file not produced');
			}
			$filesize = filesize($pdf_file_name);
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header('report.pdf', 'application/pdf', $filesize);

			readfile($pdf_file_name);

		}
		function view_image()
		{
			$GLOBALS['phpgw_info']['flags']['noheader'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = false;

			if (!$this->read)
			{
				phpgw::no_access();
			}

			$img_id = phpgw::get_var('img_id', 'int');

			$bofiles = CreateObject('property.bofiles');

			if ($img_id)
			{
				$bofiles->get_file($img_id);
			}
			else
			{
				return false;
			}
		}

		/**
		 *
		 * @param type $check_list_id
		 * @param type $case_location_code
		 */
		private function _get_report_info( $check_list_id,  $case_location_code = '')
		{
			$uicase = createObject('controller.uicase');
			$check_list = $this->so->get_single($check_list_id);

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

				}
			}
			else
			{
				$user_role = false;

				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list_location_code));
				$type = 'location';
				// Fetches locations on property
				$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $check_list_location_code, $level);
			}


			$level = $this->location_finder->get_location_level($check_list_location_code);
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());


			$open_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, $_type = null, '', null, $case_location_code);

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
					$case_files = $uicase->get_case_images($case->get_id());
					$case->set_case_files($case_files);
				}

				$check_item->get_control_item()->set_options_array($control_item_with_options->get_options_array());
				$open_check_items_and_cases[$key] = $check_item;
			}

			$data = array
			(
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'component_children'	=> $component_children,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'location_level' => $level,
				'get_locations'	=> $get_locations,
				'current_year' => $year,
				'current_month_nr' => $month,
				'open_check_items_and_cases' => $open_check_items_and_cases,
				'cases_view' => 'open_cases',
				'degree_list' => array('options' => createObject('property.borequest')->select_degree_list()),
				'consequence_list' => array('options' => createObject('property.borequest')->select_consequence_list())
			);
			return $data;
		}

	}