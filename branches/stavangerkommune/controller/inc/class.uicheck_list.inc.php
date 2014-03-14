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

	/**
	 * Import the yui class
	 */
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('phpgwapi.datetime');

	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'date_converter', 'inc/helper/');
	include_class('controller', 'location_finder', 'inc/helper/');

	class controller_uicheck_list extends phpgwapi_uicommon
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
			'update_status' => true
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
			$this->so_case	= CreateObject('controller.socase');
			$this->location_finder = new location_finder();

			$this->acl_location = '.checklist';

			$this->read	= $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_READ, 'controller');//1 
			$this->add	 = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_ADD, 'controller');//2 
			$this->edit	= $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_EDIT, 'controller');//4 
			$this->delete  = $GLOBALS['phpgw']->acl->check('.checklist', PHPGW_ACL_DELETE, 'controller');//8 

			self::set_active_menu('controller::control::check_list');
		}

		/**
		 * Public function for displaying checklists  
		 * 
		 * @param HTTP:: phpgw_return_as
		 * @return data array
		 */
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}
			self::add_javascript('phpgwapi', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

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
					'source' => self::link(array('menuaction' => 'controller.uicheck_list.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'sortable' => true,
							'formatter' => 'YAHOO.portico.formatLink'
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

			self::render_template_xsl('datatable_common', $data);
		}

		/**
		 * Public function for displaying the add check list form
		 * 
		 * @param HTTP:: location code, control id, date
		 * @return data array
		 */
		function add_check_list($check_list = null)
		{
			if($check_list == null)
			{
				$type = phpgw::get_var('type');
				$control_id = phpgw::get_var('control_id');
				$deadline_ts = phpgw::get_var('deadline_ts');

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$check_list->set_deadline($deadline_ts);
			}
			else
			{
				if($check_list->get_component_id() > 0)
				{
					$type = "component";
				}
				else
				{
					$type = "location";
				}
			}

			if(!$location_code = $check_list->get_location_code())
			{
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
				$level = $this->location_finder->get_location_level($location_code);
			}


			if($type == "component")
			{
				if($check_list != null)
				{
					$location_id = phpgw::get_var('location_id');
					$check_list->set_location_id($location_id);
					$component_id = phpgw::get_var('component_id');
					$check_list->set_component_id($component_id);
				}

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));

				$location_code = $component_arr['location_code'];

				$check_list->set_location_code($location_code);
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $check_list->get_location_code()));
				$level = $this->location_finder->get_location_level($location_code);

				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
				$component->set_location_code($component_arr['location_code']);
				$component->set_xml_short_desc($short_desc);

				$component_array = $component->toArray();
				$building_location_code = $this->location_finder->get_building_location_code($component_arr['location_code']);

				$type = "component";
			}
			else
			{
				$type = "location";
			}

			$control = $this->so_control->get_single($check_list->get_control_id());

			$responsible_user_id = execMethod('property.soresponsible.get_responsible_user_id',
					array
					(
						'responsibility_id' => $control->get_responsibility_id(),
						'location_code' => $location_code
					)
				);

			$year = date("Y", $deadline_ts);
			$month_nr = date("n", $deadline_ts);

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
				(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected'	=> $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$data = array
			(
				'user_list' => array('options' => $user_list_options),
				'location_array' => $location_array,
				'component_array' => $component_array,
				'control' => $control,
				'check_list' => $check_list,
				'buildings_on_property' => $buildings_on_property,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month_nr,
				'building_location_code' => $building_location_code,
				'location_level' => $level,
				'check_list_type' => 'add_check_list'
			);

			$GLOBALS['phpgw']->jqcal->add_listener('planned_date');
			$GLOBALS['phpgw']->jqcal->add_listener('completed_date');

			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'check_list.js');

			self::render_template_xsl(array('check_list/add_check_list', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'check_list/fragments/add_check_list_menu',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		/**
		 * Public function for displaying the edit check list form  
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		 */
		function edit_check_list($check_list = null)
		{
			if($check_list == null)
			{
				$check_list_id = phpgw::get_var('check_list_id');
				$check_list = $this->so->get_single($check_list_id);
			}

			$control = $this->so_control->get_single($check_list->get_control_id());

			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
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
				$level = $this->location_finder->get_location_level($location_code);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$users = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $this->acl_location);

			$responsible_user_id = $check_list->get_assigned_to();

			$user_list_options = array();
			foreach ($users as $user)
			{
				$user_list_options[] = array
				(
					'id' => $user['account_id'],
					'name' => $user['account_lastname'] . ', ' . $user['account_firstname'],
					'selected'	=> $responsible_user_id == $user['account_id'] ? 1 : 0
				);
			}

			$data = array
			(
				'user_list' => array('options' => $user_list_options),
				'control' => $control,
				'check_list' => $check_list,
				'$buildings_on_property' => $buildings_on_property,
				'location_array' => $location_array,
				'component_array' => $component_array,
				'type' => $type,
				'current_year' => $year,
				'current_month_nr' => $month,
				'building_location_code' => $building_location_code,
				'location_level' => $level
			);

			$GLOBALS['phpgw']->jqcal->add_listener('planned_date');
			$GLOBALS['phpgw']->jqcal->add_listener('completed_date');
			$GLOBALS['phpgw']->jqcal->add_listener('deadline_date');

			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'check_list.js');
			self::add_javascript('controller', 'controller', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/nav_control_plan',
				'check_list/fragments/check_list_top_section', 'check_list/edit_check_list',
				'check_list/fragments/select_buildings_on_property'), $data);
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
			if(!$this->add && !$this->edit)
			{
				phpgwapi_cache::message_set('No access', 'error');
				$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
			}

			$control_id = phpgw::get_var('control_id');
			$status = (int) phpgw::get_var('status');
			$type = phpgw::get_var('type');
			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			$comment = phpgw::get_var('comment', 'string');
			$assigned_to = phpgw::get_var('assigned_to', 'int');
			$billable_hours = phpgw::get_var('billable_hours', 'float');

			$deadline_date_ts = date_converter::date_to_timestamp($deadline_date);

			if($planned_date != '')
			{
				$planned_date_ts = date_converter::date_to_timestamp($planned_date);
			}
			else
			{
				$planned_date_ts = $deadline_date_ts;
			}

			if($completed_date != '')
			{
				$completed_date_ts = phpgwapi_datetime::date_to_timestamp($completed_date);
				$status = controller_check_list::STATUS_DONE;
			}
			else
			{
				$completed_date_ts = 0;
			}

			if($check_list_id > 0)
			{
				$check_list = $this->so->get_single($check_list_id);
				
				if($status == controller_check_list::STATUS_DONE)
				{
					if(! $this->_check_for_required($check_list) )
					{
						$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
					}
				}
			}
			else
			{
				if($status == controller_check_list::STATUS_DONE)
				{
					$status = controller_check_list::STATUS_NOT_DONE;
					$completed_date_ts = 0;
					$error_message =  "Status kunne ikke settes til utført - prøv igjen";
					phpgwapi_cache::message_set($error_message, 'error');
				}

				$check_list = new controller_check_list();
				$check_list->set_control_id($control_id);
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code($location_code);

				if($type == "component")
				{
					$location_id = phpgw::get_var('location_id');
					$component_id = phpgw::get_var('component_id');
					$check_list->set_location_id($location_id);
					$check_list->set_component_id($component_id);
				}
			}

			$check_list->set_status($status);
			$check_list->set_comment($comment);
			$check_list->set_deadline($deadline_date_ts);
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);
			$check_list->set_assigned_to($assigned_to);
			$check_list->set_billable_hours($billable_hours);


			if($check_list->validate())
			{
				$check_list_id = $this->so->store($check_list);

				if($check_list_id > 0)
				{
					$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id' => $check_list_id));
				}
				else
				{
					$this->edit_check_list($check_list);
				}
			}
			else
			{
				if($check_list->get_id() > 0)
				{
					$this->edit_check_list($check_list);
				}
				else
				{
					$this->add_check_list($check_list);
				}
			}
		}

		function view_control_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());

			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));

				$location_name = execMethod('property.bolocation.get_location_name', $component_arr['location_code']);

				$short_desc = $location_name . '::' . execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));

				$component = new controller_component();
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
				$level = $this->location_finder->get_location_level($location_code);
			}

			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());

			$level = $this->location_finder->get_location_level($location_code);
			$user_role = true;

			// Fetches buildings on property
			$buildings_on_property = $this->location_finder->get_buildings_on_property($user_role, $location_code, $level);

			$data = array
			(
				'control'					=> $control,
				'check_list'				=> $check_list,
				'buildings_on_property'		=> $buildings_on_property,
				'location_array'			=> $location_array,
				'component_array'			=> $component_array,
				'type'						=> $type,
				'current_year'				=> $year,
				'current_month_nr'			=> $month,
				'building_location_code'	=> $building_location_code,
				'location_level'			=> $level
			);

			phpgwapi_jquery::load_widget('core');
			self::add_javascript('controller', 'controller', 'check_list_update_status.js');

			self::render_template_xsl(array('check_list/fragments/check_list_menu', 'check_list/fragments/check_list_top_section',
				'check_list/fragments/nav_control_plan', 'check_list/view_control_info',
				'check_list/fragments/select_buildings_on_property'), $data);
		}

		function view_control_details()
		{
			$control_id = phpgw::get_var('control_id');

			$control = $this->so_control->get_single($control_id);

			$data = array
			(
				'control' => $control,
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
			foreach($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
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

			$saved_groups_with_items_array = array();

			//Populating array with saved control items for each group
			foreach($control_groups as $control_group)
			{
				$saved_control_items = $this->so_control_item_list->get_control_items_by_control_and_group($control->get_id(), $control_group->get_id());

				$control_item = $this->so_control_item->get_single($control_item_id);

				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}

			$data = array
			(
				'saved_groups_with_items_array' => $saved_groups_with_items_array,
				'check_list' => $check_list
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

		/**
		 * Public function for updateing status for a check list
		 * 
		 * @return json encoded array with status saved or not saved
		 */
		public function update_status()
		{
			if(!$this->add && !$this->edit)
			{
				return json_encode( array( "status" => 'not_saved') );
			}

			$check_list_id = phpgw::get_var('check_list_id');
			$check_list_status = phpgw::get_var('status');

			$check_list = $this->so->get_single($check_list_id);
			if ( !$this->_check_for_required($check_list) )
			{
				return json_encode( array( "status" => 'not_saved') );			
			}

			if($check_list_status == controller_check_list::STATUS_DONE)
			{
				$check_list->set_completed_date(time());
			}
			else
			{
				$check_list_status = controller_check_list::STATUS_NOT_DONE;
				$check_list->set_completed_date(0);
			}

			$check_list->set_status( $check_list_status );

			if($this->so->store($check_list))
			{
	   			return json_encode( array( 'status' => $check_list_status) );
			}
			else
			{
				return json_encode( array( "status" => 'not_saved') );
			}
		}

		public function query()
		{

		}

		/**
		* Check for required items on all groups and for all components registered to the location.
		* @param object $check_list
		* @return bool
		**/
		private function _check_for_required($check_list)
		{
			$ok = true;
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
					if($control_item['required'])
					{
						$control_item['component_location_id'] = $component_location_id;
						$control_item['component_criteria'] = $component_criteria;
						$required_control_items[] = $control_item;
					}
				}
			}

			$components_at_location = array();
			$control_groups_with_items_array = array();
			
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				
				foreach ($required_control_items as $required_control_item)
				{
					$_ok = $this->so_case->get_cases_by_component($location_id, $component_id, $required_control_item['id']);
					if(!$_ok)
					{
						$error_message =  "mangler registrering for required</br>";
						$error_message .=  "{$required_control_item['title']}</br>";
						$error_message .= execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
						$error_message .=  "</br>";
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
						if($condition['value'])
						{
							eval('$condition_value = ' . "{$condition['value']};");
							$conditions[] = array
							(
								'attribute_id'	=> $attribute_id,
								'operator'		=> $condition['operator'],
								'value'			=> $condition_value
							);
						}
					}

					$criterias_array['conditions'] = $conditions;

					if( !isset($components_at_location[$component_location_id][$location_code_search_components])  || !$_components_at_location = $components_at_location[$component_location_id][$location_code_search_components])
					{
						$_components_at_location = execMethod('property.soentity.get_eav_list', $criterias_array);
						$components_at_location[$component_location_id][$location_code_search_components] = $_components_at_location;
					}
						
					if($_components_at_location)
					{
						foreach($_components_at_location as &$_component_at_location)
						{
							$_ok = $this->so_case->get_cases_by_component($_component_at_location['location_id'], $_component_at_location['id'], $required_control_item['id'], $check_list->get_id());

							if(!$_ok)
							{
								$error_message =  "mangler registrering for required</br>";
								$error_message .=  "{$required_control_item['title']}</br>";
								$error_message .= execMethod('property.soentity.get_short_description', array('location_id' => $_component_at_location['location_id'], 'id' => $_component_at_location['id']));
								$error_message .=  "</br>";
								phpgwapi_cache::message_set($error_message, 'error');
//								echo $error_message;
								$ok = false;
							}
						}
					}
				}
			}
			return $ok;
		}
	}
