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
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socase');
	phpgw::import_class('controller.socheck_list');
	phpgw::import_class('controller.socheck_item');
	phpgw::import_class('controller.socontrol');
	
	include_class('controller', 'check_item_case', 'inc/model/');
	include_class('controller', 'status_checker', 'inc/helper/');
			
	class controller_uicase extends controller_uicommon
	{
		private $so;
		private $so_check_list;
		private $so_control;
		
		var $public_functions = array(
									'register_case' 			=> true,
									'save_case' 				=> true,
									'create_case_message' 		=> true,
									'view_case_message' 		=> true,
									'register_case_message' 	=> true,
									'register_measurement_case' => true,
									'updateStatusForCases' 		=> true,
									'delete_case' 				=> true,
									'close_case' 				=> true
								);

		function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socase');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_check_item = CreateObject('controller.socheck_item');
		}	
		
		function register_case(){
			$check_list_id = phpgw::get_var('check_list_id');
			$control_item_id = phpgw::get_var('control_item_id');
			$case_descr = phpgw::get_var('case_descr');
			$type = phpgw::get_var('type');
			$measurement = phpgw::get_var('measurement');
			$status = phpgw::get_var('status');
			 
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$check_item = $this->so_check_item->get_check_item_by_check_list_and_control_item($check_list_id, $control_item_id);
						
			/*
			
			$db_check_item = $this->so_check_item->get_db();
			$db_check_item->transaction_begin();

			$db_check_item->transaction_commit();
			$db_check_item->transaction_abort();
			
			*/
			
			if($check_item == null){
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				$new_check_item->set_control_item_id( $control_item_id );
				if($status == 0)
					$new_check_item->set_status( 0 );
				else
					$new_check_item->set_status( 1 );
				$new_check_item->set_comment( null );
				
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
			$case->set_measurement($measurement);
			$case->set_status($status);
				
			$case_id = $this->so->store($case);
			
			if($case_id > 0){
				$status_checker = new status_checker();
				$status_checker->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "saved" ) );
			}
			else
				return json_encode( array( "status" => "not_saved" ) );	
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
				$status_checker = new status_checker();
				$status_checker->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "saved", "case" => $case->toArray() ) );
			}
			else
				return json_encode( array( "status" => "not_saved" ) );
			
		}
		
		function create_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases($check_list_id, null, "open", "no_message_registered", "return_array");

			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list->get_location_code();

			$level = count(explode('-',location_code));
			
			if($level == 1)
				$buildings_array = execMethod('property.solocation.get_children',$location_code);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$building = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$categories	= $catsObj->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id, 'use_acl' => $this->_category_acl));

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'	=> $location_array,
				'categories'			=> $categories,
				'check_list'			=> $check_list->toArray(),
				'control'				=> $control->toArray(),
				'check_items_and_cases'	=> $check_items_and_cases,
				'buildings_array'		=> $buildings_array,
				'building'				=> $building,
				'date_format' 			=> $date_format
			);
			
			if(count( $buildings_array ) > 0){
				$data['buildings_array']  = $buildings_array;
			}else{
				$data['building_array'] = $building_array;
			}
						
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl(array('check_list/check_list_tab_menu', 'case/create_case_message'), $data);
		}
		
		function register_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$location_code = phpgw::get_var('location_code');
			$message_title = phpgw::get_var('message_title');
			$message_cat_id = phpgw::get_var('message_cat_id');
			$case_ids = phpgw::get_var('case_ids');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list->get_location_code();
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

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
			
			$location_id	= $GLOBALS['phpgw']->locations->get_id("controller", ".checklist");
			
			$ticket = array
			(
				'origin' 			=> $location_id,
				'origin_id'			=> $check_list_id, 
				'location_code' 	=> $location_code,
				'cat_id'			=> $message_cat_id,
				'priority'			=> $priority, //valgfri (1-3)
				'title'				=> $message_title,
				'details'			=> $message_details,
				'file_input_name'	=> 'file' // navn på felt som inneholder fil
			);
			
			$botts = CreateObject('property.botts',true);
			$message_ticket_id = $botts->add_ticket($ticket);

			$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));

			$user_id = $GLOBALS['phpgw_info']['user']['id'];
						
			// Registers message and updates check items with message ticket id
			foreach($case_ids as $case_id){
				$case = $this->so->get_single($case_id);
				$case->set_location_item_id($message_ticket_id);
				$this->so->store($case);
			}			
			
			$this->redirect(array('menuaction' => 'controller.uicase.view_case_message', 'check_list_id'=>$check_list_id, 'message_ticket_id'=>$message_ticket_id));
		}
		
		function view_case_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$message_ticket_id = phpgw::get_var('message_ticket_id');
				
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list->get_location_code();
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			$check_items_and_cases = $this->so_check_item->get_check_items_with_cases_by_message($message_ticket_id, "return_array");
						
			$botts = CreateObject('property.botts',true);
			$message_ticket = $botts->read_single($message_ticket_id);
			
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$category = $catsObj->return_single($message_ticket["cat_id"]);
			
			$data = array
			(
				'message_ticket'					=> $message_ticket,
				'category'							=> $category[0]['name'],
				'location_array'					=> $location_array,
				'control_array'						=> $control->toArray(),
				'check_list'						=> $check_list->toArray(),
				'check_items_and_cases'				=> $check_items_and_cases,
				'date_format' 						=> $date_format
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
			
			foreach($cases_array as $case){
				$case->set_status( $updateStatus );
				$this->so->update( $case );	
			}
			
			$check_items = $this->so_check_item->get_check_items_by_message($location_id, $location_item_id, "return_object");
			
			if($updateStatus == 0){

				foreach($check_items as $check_item){
					$check_item->set_status(0);
					$this->so_check_item->update($check_item);
				}
			}
			else if($updateStatus == 1){
				
				foreach($check_items as $check_item){
					$check_item = $this->so_check_item->get_single_with_cases($check_item->get_id());
					
					if($check_item->get_status() == 0){
						
						$cases_array = $check_item->get_cases_array();	
						
						if(count($cases_array) == 0){ 
							$check_item->set_status(1);
							$this->so_check_item->update($check_item);
						}
						else{
						 	$all_cases_status = 1;
						 	
							foreach($cases_array as $case){
								if($case->get_status() == 0)
									$all_cases_status = 0;		
							}
							
							if($all_cases_status == 1){
								$check_item->set_status(1);
								$this->so_check_item->update($check_item);
							}
						}
					}
				}
			}
		}
		
		public function delete_case()
		{
			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');
				
			$status = $this->so->delete($case_id);
		
			if($status){
				$status_checker = new status_checker();
				$status_checker->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "deleted" ) );
			}
			else
				return json_encode( array( "status" => "not_deleted" ) );
		}
		
		public function close_case()
		{
			$case_id = phpgw::get_var('case_id');
			$check_list_id = phpgw::get_var('check_list_id');
				
			$case = $this->so->get_single($case_id);
			$case->set_status(1);
			
			$case_id = $this->so->store($case);
					
			if($case_id > 0){
				$status_checker = new status_checker();
				$status_checker->update_check_list_status( $check_list_id );
						
				return json_encode( array( "status" => "closed" ) );
			}
			else
				return json_encode( array( "status" => "not_closed" ) );
		}
		
		public function query(){}
	}
