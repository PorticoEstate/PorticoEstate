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
	include_class('controller', 'date_generator', 'inc/component/');
		
	class controller_uierror_report_message extends controller_uicommon
	{
		private $so_control_area;
		private $so_control;
		private $so_check_list;
		private $so_control_item;
		private $so_check_item;
	
		var $public_functions = array(
										'create_error_report_message' => true,
										'save_error_report_message' => true
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
		
		function create_error_report_message(){
			$check_list_id = phpgw::get_var('check_list_id');
						
			$check_list_with_check_items = $this->so_check_list->get_single_with_check_items($check_list_id);
						
			$control_id = $check_list_with_check_items["control_id"];
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list_with_check_items["location_code"];  
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'		=> $location_array,
				'control_array'			=> $control->toArray(),
				'check_list' 			=> $check_list_with_check_items,
				'date_format' 			=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('create_error_report_message', $data);
		}
		
		function save_error_report_message(){
			$check_list_id = phpgw::get_var('check_list_id');
			$check_item_ids = phpgw::get_var('check_item_ids');
			$location_code = phpgw::get_var('location_code');
			$message_title = phpgw::get_var('message_title');
			
			$check_list_with_check_items = $this->so_check_list->get_single_with_check_items($check_list_id);
						
			$control_id = $check_list_with_check_items["control_id"];
			$control = $this->so_control->get_single( $control_id );
			
			$location_code = $check_list_with_check_items["location_code"];  
				 
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
	
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			foreach($check_item_ids as $check_item_id){
				$check_item = $this->so_check_item->get_single($check_item_id);
				
				$details .= $check_item->get_comment(); 
			}
			
			$ticket = array
			(
				'origin' 		=> $location_id,
				'origin_id'		=> $location_item_id,
				'location_code' => $location_code,
				'cat_id'		=> $cat_id,
				'priority'		=> $priority, //valgfri (1-3)
				'title'			=> $message_title,
				'details'		=> $details,
				'file_input_name'	=> 'file' // default, men valgfri
			);
			
			$botts = CreateObject('property.botts',true);
			$ticket_id = $botts->add_ticket($ticket);

			
			
			$data = array
			(
				'location_array'		=> $location_array,
				'control_array'			=> $control->toArray(),
				'check_list' 			=> $check_list_with_check_items,
				'date_format' 			=> $date_format
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'jquery-ui.custom.min.js');
			self::add_javascript('controller', 'controller', 'custom_ui.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			$GLOBALS['phpgw']->css->add_external_file('controller/templates/base/css/jquery-ui.custom.css');
			
			self::render_template_xsl('create_error_report_message', $data);
		}
		
		public function query(){}
	}
