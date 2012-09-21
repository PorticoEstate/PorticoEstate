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
 	* @version $Id: class.uicheck_list.inc.php 8419 2011-12-23 09:54:15Z vator $
	*/
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('phpgwapi.uicommon');
	phpgw::import_class('controller.socase');
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('controller.socheck_item');
	phpgw::import_class('controller.socontrol');
	
	include_class('controller', 'check_item_case', 'inc/model/');
	include_class('controller', 'component', 'inc/model/');
	include_class('controller', 'check_list_status_updater', 'inc/helper/');
			
	class controller_uicase extends phpgwapi_uicommon
	{
		private $so;
		private $so_check_list;
		private $so_control;
		private $so_control_item;
		private $so_check_item;
		
		var $public_functions = array(
									'add_case' 							=> true,
									'save_case' 						=> true,
									'create_case_message' 	=> true,
									'view_case_message' 		=> true,
									'send_case_message' 		=> true,
									'updateStatusForCases' 	=> true,
									'delete_case' 					=> true,
									'close_case' 						=> true,
									'open_case' 						=> true
								);

		function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socase');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_check_item = CreateObject('controller.socheck_item');
			$this->so_control_item = CreateObject('controller.socontrol_item');
		}	
		
		function add_case(){
			$check_list_id = phpgw::get_var('check_list_id');
			$control_item_id = phpgw::get_var('control_item_id');
			$case_descr = phpgw::get_var('case_descr');
			$type = phpgw::get_var('type');
			$status = phpgw::get_var('status');
			 
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$check_item = $this->so_check_item->get_check_item_by_check_list_and_control_item($check_list_id, $control_item_id);
							
			// Makes a check item if there isn't already made one  
			if($check_item == null){
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				$new_check_item->set_control_item_id( $control_item_id );
							
				$saved_check_item_id = $this->so_check_item->store( $new_check_item );
				$check_item = $this->so_check_item->get_single($saved_check_item_id);
			}
			
			$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));

			$user_id = $GLOBALS['phpgw_info']['user']['id'];
						
			$case = new controller_check_item_case();
			$case->set_check_item_id( $check_item->get_id() );
			$case->set_descr($case_descr);
			$case->set_user_id($user_id);
			$case->set_entry_date($todays_date_ts);
			$case->set_modified_date($todays_date_ts);
			$case->set_modified_by($user_id);
			$case->set_modified_by($user_id);
			$case->set_status($status);

			// Saves selected value from  or measurement
			if($type == 'control_item_type_2'){
				$measurement = phpgw::get_var('measurement');
				$case->set_measurement( $measurement );
			}else if($type == 'control_item_type_3'){
				$option_value = phpgw::get_var('option_value');
				$case->set_measurement( $option_value );
			}else if($type == 'control_item_type_4'){
				$option_value = phpgw::get_var('option_value');
				$case->set_measurement( $option_value );
			}
			
			$case_id = $this->so->store($case);
			
			if($case_id > 0){
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "saved" ) );
			}
			else{
				return json_encode( array( "status" => "not_saved" ) );
			}	
		}
		
		function save_case(){
			$case_id = phpgw::get_var('case_id');
			$case_descr = phpgw::get_var('case_descr');
			$case_status = phpgw::get_var('case_status');
			$measurement = phpgw::get_var('measurement');
			$check_list_id = phpgw::get_var('check_list_id');
			
			$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
			
			$case = $this->so->get_single($case_id);
			$case->set_descr($case_descr);
			$case->set_modified_date($todays_date_ts);
			$case->set_measurement($measurement);
			$case->set_status($case_status);
			
			$case_id = $this->so->store($case);
			$case = $this->so->get_single($case_id);
			
			if($case_id > 0){
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );
						
				$check_item = $this->so_check_item->get_single($case->get_check_item_id());
				$control_item = $this->so_control_item->get_single($check_item->get_control_item_id());
				
				$type = $control_item->get_type();
				
				return json_encode( array( "status" => "saved", "type" => $type, "caseObj" => $case->toArray() ) );
			}
			else{
				return json_encode( array( "status" => "not_saved" ) );
			}
		}
		
		function create_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", "no_message_registered", "return_array");

			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );

			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$categories	= $catsObj->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id, 'use_acl' => $this->_category_acl));

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
							
				$building_location_code = $this->get_building_location_code($component_arr['location_code']);
				$type = 'component';
			}
			else
			{
				$location_code = $check_list->get_location_code();
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				$type = 'location';
			}

			$level = $this->get_location_level();
			
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());
			
			$data = array
			(
				'categories'				=> $categories,
				'check_list'				=> $check_list->toArray(),
				'control'					=> $control->toArray(),
				'check_items_and_cases'		=> $check_items_and_cases,
				'date_format' 				=> $date_format,
				'location_array'			=> $location_array,
				'component_array'			=> $component_array,
				'building_location_code'	=> $building_location_code,
		//		'current_year' 				=> $year,
		//		'current_month_nr' 			=> $month,
				'type' 						=> $type,
				'location_level' 			=> $level,
				'dateformat' 				=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
				'action'					=>	$GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'controller.uicase.send_case_message')),
				'url_calendar_for_year'		=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'controller.uicalendar.view_calendar_for_year', 
																					'year'			=> $year,
																					'location_code'	=> $type == 'component' ? $building_location_code : $location_array['location_code']
																				)
																			),
				'url_calendar_for_month'	=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'controller.uicalendar.view_calendar_for_month', 
																					'year'			=> $year,
																					'month'			=> $month,
																					'location_code'	=> $type == 'component' ? $building_location_code : $location_array['location_code']
																				)
																			),
			);
						
			if(count( $buildings_array ) > 0)
			{
				$data['buildings_array']  = $buildings_array;
			}
			else
			{
				$data['building_array'] = $building_array;
			}
						
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'case/create_case_message'), $data);
		}
		
		function send_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$location_code = phpgw::get_var('location_code');
			$message_title = phpgw::get_var('message_title');
			$message_cat_id = phpgw::get_var('message_cat_id');
			$case_ids = phpgw::get_var('case_ids');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$message_details = "Kontroll: " .  $control->get_title() . "\n";
			
			$cats = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	= true;

			//liste alle
			$control_areas = $cats->formatted_xslt_list(array('format'=>'filter','selected' => $control_area_id,'globals' => true,'use_acl' => $_category_acl));

			$control_area_id = $control->get_control_area_id();
			$control_area = $cats->return_single($control_area_id);
			$control_area_name = $control_area[0]['name'];
			
			$message_details .= "Kontrollområde: " .  $control_area_name . "\n\n";
			
			// Generates message details from comment field in check item 
			$counter = 1;
			foreach($case_ids as $case_id){
				$case = $this->so->get_single($case_id);
				$message_details .= "Gjøremål $counter: ";
				$message_details .=  $case->get_descr() . "<br>";
				$counter++;
			}
			
			// This value represents the type 
			$location_id = $GLOBALS['phpgw']->locations->get_id("controller", ".checklist");
			
			$ticket = array
			(
				'origin_id' 			=> $location_id,
				'origin_item_id'	=> $check_list_id, 
				'location_code' 	=> $location_code,
				'cat_id'					=> $message_cat_id,
				'priority'				=> $priority, //valgfri (1-3)
				'title'						=> $message_title,
				'details'					=> $message_details,
				'file_input_name'	=> 'file' // navn på felt som inneholder fil
			);
			
			$botts = CreateObject('property.botts',true);
			$message_ticket_id = $botts->add_ticket($ticket);
			$location_id_ticket = $GLOBALS['phpgw']->locations->get_id('property', '.ticket');


//---Sigurd: start register component to ticket
			$component_id = $check_list->get_component_id();

			if($component_id > 0)
			{
				$user_id = $GLOBALS['phpgw_info']['user']['id'];
				$component_location_id = $check_list->get_location_id();
				$component_id = $check_list->get_component_id();

				$interlink_data = array
				(
					'location1_id'		=> $component_location_id,
					'location1_item_id' => $component_id,
					'location2_id'		=> $location_id_ticket,
					'location2_item_id' => $message_ticket_id,
					'account_id'		=> $user_id
				);

				execMethod('property.interlink.add', $interlink_data);
			}

//---End register component to ticket

			//Not used
			//$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
						
			// Registers message and updates check items with message ticket id

			foreach($case_ids as $case_id){
				$case = $this->so->get_single($case_id);
				$case->set_location_id($location_id_ticket);
				$case->set_location_item_id($message_ticket_id);
				$this->so->store($case);
			}			
			
			$this->redirect(array('menuaction' => 'controller.uicase.view_case_message', 'check_list_id'=>$check_list_id, 'message_ticket_id'=>$message_ticket_id));
		}
		
		function view_case_message()
		{
			$check_list_id = phpgw::get_var('check_list_id');
			$message_ticket_id = phpgw::get_var('message_ticket_id');
				
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
		
			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases_by_message($message_ticket_id, "return_array");
						
			$botts = CreateObject('property.botts',true);
			$message_ticket = $botts->read_single($message_ticket_id);
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$category = $catsObj->return_single($message_ticket["cat_id"]);
			
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
			$year = date("Y", $check_list->get_deadline());
			$month = date("n", $check_list->get_deadline());
			
			$data = array
			(
				'control'						=> $control->toArray(),
			//	'message_ticket_id'				=> $message_ticket_id,
				'message_ticket'				=> $message_ticket,
				'category'						=> $category[0]['name'],
				'location_array'				=> $location_array,
				'component_array'				=> $component_array,
				'control_array'					=> $control->toArray(),
				'check_list'					=> $check_list->toArray(),
				'check_items_and_cases'			=> $check_items_and_cases,
			//	'current_year' 					=> $year,
			//	'current_month_nr' 				=> $month,
				'date_format' 					=> $date_format,
				'type'				 			=> $type,
				'building_location_code' 		=> $building_location_code,
				'location_level'			 	=> $level,
				'dateformat' 					=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'],
				'url_calendar_for_year'			=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'controller.uicalendar.view_calendar_for_year', 
																					'year'			=> $year,
																					'location_code'	=> $type == 'component' ? $building_location_code : $location_array['location_code']
																				)
																			),
				'url_calendar_for_month'		=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'controller.uicalendar.view_calendar_for_month', 
																					'year'			=> $year,
																					'month'			=> $month,
																					'location_code'	=> $type == 'component' ? $building_location_code : $location_array['location_code']
																				)
																			),
				'url_ticket_view'				=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'property.uitts.view', 
																					'id'			=> $message_ticket_id
																				)
																			),

				'url_ticket_new'				=>	$GLOBALS['phpgw']->link('/index.php', array
																				(
																					'menuaction'	=> 'controller.uicase.create_case_message', 
																					'check_list_id'	=> $check_list->get_id()
																				)
																			),
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'case/view_case_message'), $data);
		}
		
		public function updateStatusForCases($location_id, $location_item_id, $updateStatus = 0){
			
			$cases_array = $this->so->get_cases_by_message( $location_id, $location_item_id );

			if(!empty ( $cases_array ) ){
				// Updates status for cases related to message  
				foreach($cases_array as $case){
					$case->set_status( $updateStatus );
					$this->so->update( $case );
				}
				
				// Gets first case of cases related to the message
				$case = $cases_array[0];

				// Gets check_item from case
				$check_item_id = $case->get_check_item_id();

				// Gets check_list from check_item
				$check_item = $this->so_check_item->get_single( $check_item_id );
				$check_list_id = $check_item->get_check_list_id(); 
				
				// Updates number of open cases for check list 
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );	
			}
		}
		
		public function delete_case()
		{
			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');
				
			$status = $this->so->delete($case_id);
		
			if($status){
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "deleted" ) );
			}
			else{
				return json_encode( array( "status" => "not_deleted" ) );
			}
		}
		
		public function close_case()
		{
			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');
				
			$case = $this->so->get_single($case_id);
			$case->set_status(controller_check_item_case::STATUS_CLOSED);
			
			$case_id = $this->so->store($case);
					
			if($case_id > 0){
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "true" ) );
			}
			else{
				return json_encode( array( "status" => "false" ) );
			}
		}
		
		public function open_case()
		{
			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');
				
			$case = $this->so->get_single($case_id);
			$case->set_status(controller_check_item_case::STATUS_OPEN);
			
			$case_id = $this->so->store($case);
					
			if($case_id > 0){
				$cl_status_updater = new check_list_status_updater();
				$cl_status_updater->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "true" ) );
			}
			else{
				return json_encode( array( "status" => "false" ) );
			}
		}
		
		function get_location_level($location_code)
		{
			$level = count(explode('-', $location_code));

			return $level;
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
		
		public function query(){}
	}
