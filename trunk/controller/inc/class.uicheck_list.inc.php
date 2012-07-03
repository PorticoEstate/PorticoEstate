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
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socontrol_area');
	phpgw::import_class('controller.socheck_list');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
	include_class('controller', 'check_list_status_updater', 'inc/helper/');
	include_class('controller', 'date_helper', 'inc/helper/');
	
	class controller_uicheck_list extends phpgwapi_uicommon
	{
		private $so;
		private $so_control_area;
		private $so_control;
		private $so_control_item;
		private $so_check_item;
		private $so_procedure;
		private $so_control_group_list;
		private $so_control_group;
		private $so_control_item_list;
	
		var $public_functions = array(
										'index' 										=> true,
										'add_check_list' 						=> true,
										'save_check_list' 					=> true,
										'edit_check_list' 					=> true,
										'create_case_message' 			=> true,
										'view_control_info' 				=> true,
										'view_cases_for_check_list'	=> true,
										'print_check_list'					=> true,
										'register_case'							=> true,
										'view_open_cases'						=> true,
										'view_closed_cases'					=> true,
										'view_control_details'			=> true,
										'view_control_items'				=> true,
										'get_check_list_info'				=> true, 
										'get_cases_for_check_list'	=> true
									);

		function __construct()
		{
			parent::__construct();

			$this->so_control_area 				= CreateObject('controller.socontrol_area');
			$this->so_control 						= CreateObject('controller.socontrol');
			$this->so											= CreateObject('controller.socheck_list');
			$this->so_control_item				= CreateObject('controller.socontrol_item');
			$this->so_check_item					= CreateObject('controller.socheck_item');
			$this->so_procedure						= CreateObject('controller.soprocedure');
			$this->so_control_group_list 	= CreateObject('controller.socontrol_group_list');
			$this->so_control_group				= CreateObject('controller.socontrol_group');
			$this->so_control_item_list 	= CreateObject('controller.socontrol_item_list');

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
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->query();
			}
			self::add_javascript('controller', 'yahoo', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');

			$data = array(
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
										'name' =>  lang('PENDING')
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
							'sortable'	=> true,
							'formatter' => 'YAHOO.portico.formatLink'
						),
						array(
							'key'	=>	'title',
							'label'	=>	lang('Control title'),
							'sortable'	=>	false
						),
						array(
							'key' => 'start_date',
							'label' => lang('start_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'planned_date',
							'label' => lang('planned_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'end_date',
							'label' => lang('end_date'),
							'sortable'	=> false
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				),
			);

			self::render_template_xsl('datatable', $data);
		}
		
		/**
		 * Public function for displaying the add check list form
		 * 
		 * @param HTTP:: location code, control id, date
		 * @return data array
		*/
		function add_check_list(){
			$type = phpgw::get_var('type');
			$control_id = phpgw::get_var('control_id');
			$deadline_ts = phpgw::get_var('deadline_ts');
			
			$check_list = new controller_check_list();
			$check_list->set_control_id($control_id);
			$check_list->set_deadline($deadline_ts);
			
			if($type == "component")
			{
				$location_id = phpgw::get_var('location_id');
				$check_list->set_location_id($location_id);
				$component_id = phpgw::get_var('component_id');
				$check_list->set_component_id($component_id);
						
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
    		
				$component = new controller_component();
				$component->set_location_code( $component_arr['location_code'] );
    		$component->set_xml_short_desc( $short_desc );
				
				$component_array = $component->toArray();
				$building_location_code = $this->get_building_location_code($component_arr['location_code']);
				$type = "component";
			}
			else
			{
				$location_code = phpgw::get_var('location_code');	
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$level = $this->get_location_level($location_code);
				$type = "location";
			}
			
			$control = $this->so_control->get_single($control_id);
			
			$year = date("Y", $deadline_ts);
			$month = date("n", $deadline_ts);
		
			
			$data = array
			(
				'location_array'					=> $location_array,
				'component_array'					=> $component_array,
				'control'									=> $control->toArray(),
				'date_format' 						=> $date_format,
				'check_list' 							=> $check_list->toArray(),
				'type'			 							=> $type,
				'current_year' 						=> $year,
				'current_month_nr' 				=> $month,
				'building_location_code' 	=> $building_location_code,
				'location_level' 					=> $level
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/add_check_list'), $data);
		}
		
		/**
		 * Public function for saving a check list
		 * 
		 * @param HTTP:: location code, control id, status etc.. (check list details) 
		 * @return data array
		*/
		function save_check_list()
		{
			$control_id = phpgw::get_var('control_id');
			$status = (int)phpgw::get_var('status');
			$type = phpgw::get_var('type');
			$deadline_date = phpgw::get_var('deadline_date', 'string');
			$planned_date = phpgw::get_var('planned_date', 'string');
			$completed_date = phpgw::get_var('completed_date', 'string');
			$comment = phpgw::get_var('comment', 'string');
			$return_format = phpgw::get_var('phpgw_return_as');
						
			$deadline_date_ts = date_helper::get_timestamp_from_date( $deadline_date, "d/m-Y" );
			
			if($planned_date != ''){
				$planned_date_ts = date_helper::get_timestamp_from_date( $planned_date, "d/m-Y" );
			}else{
				$planned_date_ts = 0;
			} 
			
			if($completed_date != ''){
				$completed_date_ts = date_helper::get_timestamp_from_date( $completed_date, "d/m-Y" );
			}else{
				$completed_date_ts = 0;
			}		

			$check_list = new controller_check_list();
			$check_list->set_location_code($location_code);
			$check_list->set_control_id($control_id);
			$check_list->set_status($status);
			$check_list->set_comment($comment);
			$check_list->set_deadline( $deadline_date_ts );
			$check_list->set_planned_date($planned_date_ts);
			$check_list->set_completed_date($completed_date_ts);
			
			if($type == "component"){
				$location_id = phpgw::get_var('location_id');
				$component_id = phpgw::get_var('component_id');
				$check_list->set_location_id( $location_id );
				$check_list->set_component_id( $component_id );
			}else {
				$location_code = phpgw::get_var('location_code');
				$check_list->set_location_code( $location_code );
			}
			
			$check_list_id = $this->so->store($check_list);
			
			
			if( ($check_list_id > 0) & ($return_format != 'json') )
			{
				$this->redirect(array('menuaction' => 'controller.uicheck_list.edit_check_list', 'check_list_id'=>$check_list_id));	
			}
			else if( ($check_list_id > 0) & ($return_format == 'json') )
			{
				return json_encode( array( "status" => "updated" ) );
			}
			else
			{
				return json_encode( array( "status" => "not_updated" ) );
			} 
		}
		
		/**
		 * Public function for displaying the edit check list form  
		 * 
		 * @param HTTP:: check list id
		 * @return data array
		*/
		function edit_check_list(){
			$check_list_id = phpgw::get_var('check_list_id');

			$cl_status_updater = new check_list_status_updater();
			$cl_status_updater->update_check_list_status( $check_list_id );
		
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
    		
				$component = new controller_component();
				$component->set_location_code( $component_arr['location_code'] );
    		$component->set_xml_short_desc( $short_desc );
				$component_array = $component->toArray();
				
				$type = 'component';
				$building_location_code = $this->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
				$level = $this->get_location_level($location_code);
			}
			
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());
			
			
			
			$data = array
			(
				'control' 								=> $control->toArray(),
				'check_list' 							=> $check_list->toArray(),
				'location_array'					=> $location_array,
				'component_array'					=> $component_array,
				'date_format' 						=> $date_format,
				'type' 										=> $type,
				'current_year' 						=> $year,
				'current_month_nr' 				=> $month,
				'building_location_code' 	=> $building_location_code,
				'location_level' 					=> $level
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/edit_check_list'), $data);
		}
		
		function view_cases_for_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
				
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$location_code = $check_list->get_location_code();
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$level = $this->get_location_level($location_code);
			
			$data = array
			(
				'control' 				=> $control->toArray(),
				'check_list' 			=> $check_list->toArray(),
				'location_array'	=> $location_array,
				'date_format' 		=> $date_format,
				'location_level' 	=> $level
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'check_list/view_cases_for_check_list'), $data);
		}
		
		function create_case_message()
		{
			$check_list_id = phpgw::get_var('check_list_id');
						
			$check_list_with_check_items = $this->so->get_single_with_check_items($check_list_id);
						
			$control_id = $check_list_with_check_items["control_id"];
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list_with_check_items["location_code"];  
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'	=> $location_array,
				'control_array'		=> $control->toArray(),
				'check_list' 			=> $check_list_with_check_items,
				'date_format' 		=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('create_case_messsage', $data);
		}
		
		public function print_check_list()
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
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$data = array
			(
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array,
				'check_list'					=> $check_list->toArray()
			);
			
			self::render_template_xsl('check_list/print_check_list', $data);
		}
		
		public function view_control_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());
			
			$location_code = $check_list->get_location_code();  
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			$level = $this->get_location_level($location_code);
			
			$data = array
			(
				'location_array'				=> $location_array,
				'control'								=> $control->toArray(),
				'check_list'						=> $check_list->toArray(),
				'location_level'				=> $level,
			);

			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu','check_list/view_control_info'), $data);
		}
		
		function view_control_details()
		{
			$control_id = phpgw::get_var('control_id');
			
			$control = $this->so_control->get_single($control_id);
			
			$data = array
			(
				'control'						=> $control->toArray(),
			);
			
			self::render_template_xsl('check_list/view_control_details', $data);
		}
						
		// Displays control groups and control items for a check list
		function register_case()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			$control = $this->so_control->get_single($check_list->get_control_id());			
			
			$saved_control_groups = $this->so_control_group_list->get_control_groups_by_control($control->get_id());
		
			$control_groups_with_items_array = array();
			
			//Populating array with saved control items for each group
			foreach ($saved_control_groups as $control_group)
			{	
				$saved_control_items = $this->so_control_item_list->get_control_items_and_options_by_control_and_group($control->get_id(), $control_group->get_id(), "return_array");

				if(count($saved_control_items) > 0)
				{				
					$control_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
				}
			}
			
			/* ================  Ikke slett!!! Kode som henter ut  utstyr basert på lokasjon  ==================       
			
			//get control items based on control group/component connection
			$control_groups_for_control = $this->so_control_group->get_control_group_ids_for_control($control->get_id());
			//_debug_array($control_groups_for_control);

			foreach($control_groups_for_control as $cg)
			{
				$components_for_control_group[] = array($cg => $this->so_control_group->get_components_for_control_group($cg));
			}
			
			//_debug_array($components_for_control_group);
			
			$control_group_check_items = array();
			foreach($components_for_control_group as $cg_components)
			{
				foreach($control_groups_for_control as $cg_control)
				{
					$components = $cg_components[$cg_control];
					//_debug_array($components);
					$location_has_component = false;
					foreach($components as $comp)
					{
						if(!$location_has_component)
						{
							//check if current location has component
							$location_has_component = $this->so_control_item->location_has_component($comp, $check_list->get_location_code);
						}
					}
					if($location_has_component)
					{
						//the check items for the control group shall be added
						$check_items = $this->so_control_item->get_items_for_control_group($control->get_id(), $cg_control);
						$control_group_check_items[] = $check_items;
					}
				}
			}
			=====================================================================*/
			//_debug_array($control_group_check_items);
			
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();
				
				$component_arr = execMethod('property.soentity.read_single_eav', array('location_id' => $location_id, 'id' => $component_id));
				$short_desc = execMethod('property.soentity.get_short_description', array('location_id' => $location_id, 'id' => $component_id));
    		
				$component = new controller_component();
				$component->set_location_code( $component_arr['location_code'] );
    		$component->set_xml_short_desc( $short_desc );
				$component_array = $component->toArray();
				
				$type = 'component';
				$building_location_code = $this->get_building_location_code($component_arr['location_code']);
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
			}
			
			$level = $this->get_location_level($location_code);
					
			$data = array
			(
				'control' 													=> $control->toArray(),
				'check_list' 												=> $check_list->toArray(),
				'location_array'										=> $location_array,
				'component_array'										=> $component_array,
				'control_groups_with_items_array' 	=> $control_groups_with_items_array,
				'type' 															=> $type,
				'location_level' 										=> $level
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'check_list/register_case'), $data);
		}
		
		function view_open_cases()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);

			$open_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, $type = null, 'open_or_waiting', null, 'return_array');

			foreach($open_check_items_and_cases as $key => $check_item)
			{
				$control_item_with_options = $this->so_control_item->get_single_with_options($check_item['control_item_id'], "return_array");
				$check_item['control_item']['options_array'] = $control_item_with_options['options_array'];
				$open_check_items_and_cases[$key] = $check_item;
			}

			$data = array
			(
				'open_check_items_and_cases'	=> $open_check_items_and_cases,
				'check_list' 									=> $check_list->toArray()
			);
			
			self::render_template_xsl( array('check_list/cases_tab_menu', 'check_list/view_open_cases'), $data );			
		}
		
		function view_closed_cases()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			
			$check_list = $this->so->get_single($check_list_id);
			
			$closed_check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_1", 'closed', null, 'return_array');
			$closed_check_items_and_measurements = $this->so_check_item->get_check_items_with_cases($check_list_id, "control_item_type_2", 'closed', null, 'return_array');

			$data = array
			(
				'closed_check_items_and_cases'				=> $closed_check_items_and_cases,
				'closed_check_items_and_measurements'	=> $closed_check_items_and_measurements,
				'check_list' 													=> $check_list->toArray()
			);
			
			self::render_template_xsl( array('check_list/cases_tab_menu', 'check_list/view_closed_cases'), $data );
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
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}
			
			$data = array
			(
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array,
				'check_list'					=> $check_list->toArray()
			);
			
			self::render_template_xsl('check_list/view_control_items', $data);
		}
	
		// Returns check list info as JSON
		public function get_check_list_info()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single_with_check_items($check_list_id, "open");
			
			return json_encode( $check_list );
		}
		
		// Returns open cases for a check list as JSON 
		public function get_cases_for_check_list()
		{
			$check_list_id = phpgw::get_var('check_list_id');

			$check_items_with_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", null, "return_array");
			
			return json_encode( $check_items_with_cases );
		}
		
		function get_building_location_code($location_code)
		{
			if( strlen( $location_code ) == 6 )
			{
				$location_code_arr = explode('-', $location_code, 2);
				$building_location_code = $location_code_arr[0];
			}
			else if( strlen( $location_code ) > 6 )
			{
				$location_code_arr = explode('-', $location_code, 3);
				$building_location_code = $location_code_arr[0] . "-" . $location_code_arr[1];
			}
			else
			{
				$building_location_code = $location_code;
			}
			
			return $building_location_code; 
		}
		
		function get_location_level($location_code)
		{
			$level = count(explode('-', $location_code));

			return $level;
		}	
		
		public function query(){}
	}
