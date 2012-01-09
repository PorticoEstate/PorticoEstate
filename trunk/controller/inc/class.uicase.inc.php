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
 	* @version $Id: class.uicheck_list_for_location.inc.php 8419 2011-12-23 09:54:15Z vator $
	*/
	
	phpgw::import_class('phpgwapi.yui');
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socontrol_area');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item_case', 'inc/model/');
	include_class('controller', 'date_generator', 'inc/component/');
		
	class controller_uicase extends controller_uicommon
	{
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
	
		var $public_functions = array(
										'create_case' => true,
										'save_case' => true
									);

		function __construct()
		{
			parent::__construct();

			$this->bo					= CreateObject('property.bolocation',true);
			$this->bocommon				= & $this->bo->bocommon;
			$this->so_control_area 		= CreateObject('controller.socontrol_area');
			$this->so_control 			= CreateObject('controller.socontrol');
			$this->so_check_list		= CreateObject('controller.socheck_list');
			$this->so_control_item		= CreateObject('controller.socontrol_item');
			$this->so_check_item		= CreateObject('controller.socheck_item');
			
			$this->type_id				= $this->bo->type_id;
			
			self::set_active_menu('controller::control::location_for_check_list');
		}	
		
		function create_case(){
			$check_list_id = phpgw::get_var('check_list_id');
						
			$check_list_with_check_items = $this->so_check_list->get_single_with_check_items($check_list_id, null, 'control_item_type_1');
						
			$control_id = $check_list_with_check_items["control_id"];
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list_with_check_items["location_code"];

			$level = count(explode('-',location_code));
			
			if($level == 1)
				$buildings_array = execMethod('property.solocation.get_children',$location_code);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$catsObj = CreateObject('phpgwapi.categories', -1, 'property', '.ticket');
			$catsObj->supress_info = true;
			
			$categories	= $catsObj->formatted_xslt_list(array('select_name' => 'values[cat_id]','selected' => $this->cat_id, 'use_acl' => $this->_category_acl));

			$data = array
			(
				'categories'			=> $categories,
				'control_array'			=> $control->toArray(),
				'check_list' 			=> $check_list_with_check_items,
				'buildings_array' 		=> $buildings_array,
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
			
			self::render_template_xsl('case/create_case', $data);
		}
		
		function save_case(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_item_ids = phpgw::get_var('check_item_ids');
			$location_code = phpgw::get_var('location_code');
			$message_title = phpgw::get_var('message_title');
			$message_cat_id = phpgw::get_var('message_cat_id');
			
			$check_list = $this->so_check_list->get_single($check_list_id);
						
			$control_id = $check_list->get_control_id();
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list->get_location_code();
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			// Generates message details from comment field in check item 
			foreach($check_item_ids as $check_item_id){
				$check_item = $this->so_check_item->get_single($check_item_id);
				$message_details .= "Gjøremål: ";
				$message_details .=  $check_item->get_comment() . "<br>";
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

			$todays_date = mktime(0,0,0,date("m"), date("d"), date("Y"));
			
			
			// Registers message and updates check items with message ticket id
			foreach($check_item_ids as $check_item_id){
				$check_item = $this->so_check_item->get_single($check_item_id);
				$user_id = 
				
				
				$case = CreateObject('controller.check_item_case');
				$case->set_check_item_id($check_item_id);
				$case->set_status(0);
				$case->set_location_id($location_id);
				$case->set_location_item_id($message_ticket_id);
				$case->set_user_id($user_id);
				$case->set_entry_date($todays_date);
				$case->set_modified_date($todays_date);
				$case->set_modified_by($modified_by);

				
				
				
				
				$this->so_check_item->update($check_item);
			}			
			
			$registered_message_check_items = $this->so_check_item->get_check_items_by_message($message_ticket_id);
			
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
				'registered_message_check_items'	=> $registered_message_check_items,
				'date_format' 						=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('case/view_case', $data);
		}
		
		public function query(){}
	}
