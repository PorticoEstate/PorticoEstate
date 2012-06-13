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

	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socheck_list');
	
	include_class('controller', 'check_list', 'inc/model/');
	include_class('controller', 'check_item', 'inc/model/');
	include_class('controller', 'check_list_status_info', 'inc/helper/');
	include_class('controller', 'status_agg_month_info', 'inc/helper/');
	include_class('controller', 'location_finder', 'inc/helper/');
	include_class('controller', 'year_calendar', 'inc/component/');
	include_class('controller', 'month_calendar', 'inc/component/');
		
	class controller_uicalendar extends controller_uicommon
	{
		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;
				
		public $public_functions = array
		(
			'view_calendar_for_month'			      =>	true,
			'view_calendar_for_year'			      =>	true,
			'view_calendar_year_for_locations'	=>  true,
			'view_calendar_month_for_locations'	=>  true
		);

		public function __construct()
		{
			parent::__construct();
			
			$read    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller'); //1 
			$add     = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller'); //2 
			$edit    = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller'); //4 
			$delete  = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller'); //8 
			
			$manage  = $GLOBALS['phpgw']->acl->check('.control', 16, 'controller'); //16
			
			$this->so 										= CreateObject('controller.socheck_list');
			$this->so_control 						= CreateObject('controller.socontrol');
			$this->so_control_group 			= CreateObject('controller.socontrol_group');
			$this->so_control_group_list 	= CreateObject('controller.socontrol_group_list');
			$this->so_control_item 				= CreateObject('controller.socontrol_item');
			$this->so_check_list 					= CreateObject('controller.socheck_list');
			$this->so_check_item 					= CreateObject('controller.socheck_item');
			
			self::set_active_menu('controller::location_check_list');
		}
		
		public function view_calendar_for_month()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			$month = phpgw::get_var('month');
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);
			
			// Validates month. If year is not set, current month in current year is chosen
			$month = $this->validate_month($month);
			
			// Gets timestamp value of first day in month
			$from_date_ts = month_calendar::get_start_month_date_ts($year, intval( $month ));

			// Gets timestamp value of first day in month
			$to_date_ts = month_calendar::get_end_month_date_ts($year, intval( $month ));

			// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);
			
			$level = $this->get_location_level($location_code);
						
      $user_role = true;

      // Fetches buildings on property
      $buildings_on_property = $this->get_buildings_on_property($user_role, $location_code, $level);
			
			// Fetches controls for location within specified time period
			$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts);

			// Fetches all control ids with check lists for specified time period
			$control_id_with_check_list_array = $this->so->get_check_lists_for_location_2($location_code, $from_date_ts, $to_date_ts);
			
			// Loops through all controls for location and populates controls with check lists
			$controls_with_check_list_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);
			
			$controls_calendar_array = array();
			foreach($controls_with_check_list_array as $control){
				$month_calendar = new month_calendar($control, $year, $month);
				$calendar_array = $month_calendar->build_calendar( $control->get_check_lists_array() );

				$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
			}
			
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
		
			$property_array = execMethod('property.solocation.read', array('type_id' => 1, 'allrows' => true));
			
			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations();
			
			$heading_array = month_calendar::get_heading_array($year, $month);
			
			$data = array
			(		
				'buildings_on_property'		=> $buildings_on_property,
				'my_locations'	  		  	=> $my_locations,
				'property_array'	  	  	=> $property_array,
				'current_location'		  	=> $location_array,
				'heading_array'		  	  	=> $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  		=> $date_format,
				'current_year' 			  		=> $year,
				'current_month_nr' 		  	=> $month,
				'location_level'		  		=> $level,
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui-1.8.20.custom.min.js');
			self::add_stylesheet('controller/templates/base/css/jquery-ui-1.8.20.custom.css');
			
			self::render_template_xsl(array('calendar/view_calendar_month', 'calendar/check_list_status_checker', 
																			'calendar/icon_color_map', 'calendar/select_my_locations', 
																			'calendar/select_buildings_on_property'), $data);
		}
		
		public function view_calendar_for_year()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);
			
			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);

			// Array that will be populated with controls and calendar objects that will be sent to view
			$controls_calendar_array = array();
				
     	// Validates location_code. If not set, first location among assigned locations
			$location_code = $this->validate_location_code($location_code);
			
			$level = $this->get_location_level($location_code);
						
      $user_role = true;

      // Fetches buildings on property
      $buildings_on_property = $this->get_buildings_on_property($user_role, $location_code, $level);
			
			// Fetches all controls for the location within time period
			$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type = null);
			
			// Fetches all controls for the components on location within time period
			$controls_for_component_array = $this->so_control->get_controls_by_component($location_code, $from_date_ts, $to_date_ts, $repeat_type = null);
			
			$controls_calendar_array = array();
			
			// Loops through controls with repeat type day or week in controls_for_location_array
			// and populates array that contains aggregate open cases pr month.   		
			foreach($controls_for_location_array as $control){
				if($control->get_repeat_type() == 0 | $control->get_repeat_type() == 1){
					
					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($control, $location_code, $year);
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_agg_month_calendar($agg_open_cases_pr_month_array);
						
					$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
				}
			}
			
			$repeat_type = 2;
			// Fetches control ids with check lists for specified time period
			$control_id_with_check_list_array = $this->so->get_check_lists_for_location_2($location_code, $from_date_ts, $to_date_ts, $repeat_type);
			
			// Loops through all controls for location and populates controls with check lists
			$controls_for_location_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);
			
			$repeat_type = 3;
			// Fetches control ids with check lists for specified time period
			$control_id_with_check_list_array = $this->so->get_check_lists_for_location_2($location_code, $from_date_ts, $to_date_ts, $repeat_type);
			
			// Loops through all controls for location and populates controls with check lists
			$controls_for_location_array = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);

			foreach($controls_for_location_array as $control){
				if($control->get_repeat_type() == 2 | $control->get_repeat_type() == 3){
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_calendar( $control->get_check_lists_array() );
											
					$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
				}
			}
			
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));

			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations();
			
			$heading_array = year_calendar::get_heading_array();
			
			$data = array
			(
				'buildings_on_property'		=> $buildings_on_property,
				'my_locations'						=> $my_locations,
				'current_location'  	  	=> $location_array,
				'heading_array'		  	  	=> $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  		=> $date_format,
				'current_year' 			  		=> $year,
				'location_level'		  		=> $level,
			);
			
			self::render_template_xsl(array('calendar/view_calendar_year', 'calendar/check_list_status_checker', 
																			'calendar/icon_color_map', 'calendar/select_my_locations', 
																			'calendar/select_buildings_on_property'), $data);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::add_javascript('controller', 'controller', 'jquery-ui-1.8.20.custom.min.js');
			self::add_stylesheet('controller/templates/base/css/jquery-ui-1.8.20.custom.css');
		}

		public function view_calendar_year_for_locations()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
			$year = phpgw::get_var('year');
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
				$components_for_control_array = $this->so_control->get_components_for_control($control_id);
			}
			
			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);
			
			// Gets timestamp of first day in year
			$from_date_ts = $this->get_start_date_year_ts($year);

			// Gets timestamp of first day in next year
			$to_date_ts = $this->get_end_date_year_ts($year);
			
			$locations_with_calendar_array = array();
			
			if($control->get_repeat_type() <= 1 ){
				foreach($locations_for_control_array as $location){
					$curr_location_code = $location['location_code'];
					
					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($control, $curr_location_code, $year);
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_agg_month_calendar($agg_open_cases_pr_month_array);
					$locations_with_calendar_array[] = array("location" => $location, "calendar_array" => $calendar_array);
				}
				
			    foreach($components_for_control_array as $component){
					$curr_component_id = $component['component_id'];
					
					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($control, $curr_component_id, $year, true);
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_agg_calendar($agg_open_cases_pr_month_array);
					$components_with_calendar_array[] = array("component" => $component, "calendar_array" => $calendar_array);
				}
				
			    foreach($components_for_control_array as $component){
					$curr_component_id = $component['component_id'];
					
					// Loops through controls in controls_for_location_array and populates aggregate open cases pr month array.
					$agg_open_cases_pr_month_array = $this->build_agg_open_cases_pr_month_array($control, $curr_component_id, $year, true);
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_agg_calendar($agg_open_cases_pr_month_array);
					$components_with_calendar_array[] = array("component" => $component, "calendar_array" => $calendar_array);
				}
			}else if($control->get_repeat_type() > 1){
				foreach($locations_for_control_array as $location){
					$curr_location_code = $location['location_code'];
					
					$repeat_type = $control->get_repeat_type();
					$location_with_check_lists = $this->so->get_check_lists_for_control_and_location($control_id, $curr_location_code, $from_date_ts, $to_date_ts, $repeat_type);	
					
					$check_lists_array = $location_with_check_lists["check_lists_array"];
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_calendar( $check_lists_array );

					$locations_with_calendar_array[] = array("location" => $location, "calendar_array" => $calendar_array);
				}
				
			  foreach($components_for_control_array as $component){
					$curr_component_id = $component['component_id'];
					
					$repeat_type = $control->get_repeat_type();
					$component_with_check_lists = $this->so->get_check_lists_for_control_and_component($control_id, $curr_component_id, $from_date_ts, $to_date_ts, $repeat_type);	
					
					$check_lists_array = $component_with_check_lists["check_lists_array"];
					
					$year_calendar = new year_calendar($control, $year);
					$calendar_array = $year_calendar->build_calendar( $check_lists_array );

					$components_with_calendar_array[] = array("component" => $component, "calendar_array" => $calendar_array);
				}
			}
			
			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations();
			
			$heading_array = year_calendar::get_heading_array();
			
			$data = array
			(
				'my_locations'	  		  					=> $my_locations,
				'control'			  	  							=> $control->toArray(),
				'heading_array'		  	  					=> $heading_array,
				'locations_with_calendar_array' 	=> $locations_with_calendar_array,
			  'components_with_calendar_array'	=> $components_with_calendar_array,
				'date_format' 			  						=> $date_format,
				'current_year'	  	  	  				=> $year,
			);
			
			self::render_template_xsl( array('calendar/view_calendar_year_for_locations', 'calendar/check_list_status_checker', 
											 								 'calendar/icon_color_map', 'calendar/select_my_locations'), $data);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}
		
		public function view_calendar_month_for_locations()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
			$year = intval( phpgw::get_var('year') );
			$month = intval( phpgw::get_var('month') );
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
				$components_for_control_array = $this->so_control->get_components_for_control($control_id);
			}

			// Validates year. If year is not set, current year is chosen
			$year = $this->validate_year($year);
			
			// Validates month. If year is not set, current month in current year is chosen
			$month = $this->validate_month($month);
			
			// Gets timestamp value of first day in month
			$from_date_ts = month_calendar::get_start_month_date_ts($year, intval( $month ));

			// Gets timestamp value of first day in month
			$to_date_ts = month_calendar::get_end_month_date_ts($year, intval( $month ));
			
			$locations_with_calendar_array = array();
			
			foreach($locations_for_control_array as $location){
				$curr_location_code = $location['location_code'];
					
				$repeat_type = $control->get_repeat_type();
				$location_with_check_lists = $this->so->get_check_lists_for_control_and_location($control_id, $curr_location_code, $from_date_ts, $to_date_ts, $repeat_type);	
					
				$check_lists_array = $location_with_check_lists["check_lists_array"];
					
				$month_calendar = new month_calendar($control, $year, $month);
				$calendar_array = $month_calendar->build_calendar( $check_lists_array );

				$locations_with_calendar_array[] = array("location" => $location, "calendar_array" => $calendar_array);
			}
			
			// Gets array of locations assigned to current user
			$my_locations = $this->get_my_assigned_locations();
 			
			$heading_array = month_calendar::get_heading_array($year, $month);
			
			$data = array
			(		
				'control'	  		  							=> $control->toArray(),
				'my_locations'	  		  				=> $my_locations,
				'property_array'	  	  				=> $property_array,
				'location_array'		  					=> $location_array,
				'heading_array'		  	  				=> $heading_array,
				'locations_with_calendar_array' => $locations_with_calendar_array,
				'date_format' 			  					=> $date_format,
				'current_month_nr' 			  			=> $month,
				'current_year' 			  	  			=> $year,
			);
			
			self::render_template_xsl( array('calendar/view_calendar_month_for_locations', 'calendar/check_list_status_checker', 
											 'calendar/icon_color_map', 'calendar/select_my_locations'), $data);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}
		
		public function populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array){
			$controls_with_check_list = array();
			
			foreach($controls_for_location_array as $control){
				foreach($control_id_with_check_list_array as $control_id){
					if($control->get_id() == $control_id->get_id())
						$control->set_check_lists_array($control_id->get_check_lists_array());						
				}
					
				$controls_with_check_list[] = $control;
			}
			
			return $controls_with_check_list;
		}
		
		// Generates array of aggregated number of open cases for each month in time period 
		function build_agg_open_cases_pr_month_array($control, $location_code, $year, $component=false ){
				
			// Checks if control starts in the year that is displayed 
			if( date("Y", $control->get_start_date()) == $year ){
				$from_month = date("n", $control->get_start_date());	
			}else{
				$from_month = 1;
			}
			
			// Checks if control ends in the year that is displayed
			if( date("Y", $control->get_end_date()) == $year ){
				$to_month = date("n", $control->get_end_date());
			}else{
				$to_month = 12;
			}
					
			$agg_open_cases_pr_month_array = array();
			
			// Fetches aggregate value for open cases in each month in time period 			
			for($from_month;$from_month<=$to_month;$from_month++){
					
				$month_start_ts = strtotime("$from_month/01/$year");
				$end_month = $from_month + 1;
				
				if($end_month > 12){
					$year = $year + 1;
					$end_month = 1;
				}
				
				$month_end_ts = strtotime("$end_month/01/$year");
				
				$num_open_cases_for_control_array = array();
				
				// Fetches aggregate value for open cases in a month from db 	
				$num_open_cases_for_control_array = $this->so_check_list->get_num_open_cases_for_control( $control->get_id(), $location_code, $month_start_ts, $month_end_ts, $component );	
				
				// If there is a aggregated value for the month, add aggregated status object to agg_open_cases_pr_month_array
				if( !empty($num_open_cases_for_control_array) ){
					$status_agg_month_info = new status_agg_month_info();
					$status_agg_month_info->set_month_nr($from_month);
					$status_agg_month_info->set_agg_open_cases( $num_open_cases_for_control_array["count"] );
					$agg_open_cases_pr_month_array[] = $status_agg_month_info;
				} 
			}
						
			return $agg_open_cases_pr_month_array;
		}
		
		function get_buildings_on_property($user_role, $location_code, $level){
					
			// Property level
			if($level == 1){
				$property_location_code = $location_code;
			}
			// Building level
			else if($level > 1){
				$split_loc_code_array = explode('-', $location_code);
				$property_location_code = $split_loc_code_array[0];
			}	
		
		  if($user_role){
				$criteria = array();
				$criteria['location_code'] = $property_location_code;
				$criteria['field_name'] = 'loc2_name';
				$criteria['child_level'] = '2';
				
      	$buildings_on_property = execMethod('property.solocation.get_children', $criteria);
      }else{
        $buildings_on_property = execMethod('property.solocation.get_children', $property_location_code);
      }
			
      return $buildings_on_property;
		}
		
		function get_location_level($location_code){
			$level = count(explode('-', $location_code));

			return $level;
		}	
		
		function validate_location_code($location_code){
			$criteria = array
			(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'],
				'type_id' => 1,
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);
		
			$location_finder = new location_finder();
			$my_locations = $location_finder->get_responsibilities( $criteria );

			if(empty($location_code)){
				$location_code = $my_locations[0]["location_code"];
			}
			
			return $location_code;
		}
		
		function get_my_assigned_locations($location_code){
			$criteria = array
			(
				'user_id' => $GLOBALS['phpgw_info']['user']['account_id'], // 
				'type_id' => 1, // Nivå i bygningsregisteret 1:eiendom
				'role_id' => 0, // For å begrense til en bestemt rolle - ellers listes alle roller for brukeren
				'allrows' => false
			);
		
			$location_finder = new location_finder();
			$my_locations = $location_finder->get_responsibilities( $criteria );
			
			return $my_locations;
		}
		
		function get_start_date_year_ts($year){
			$start_date_year_ts = strtotime("01/01/$year");
			
			return $start_date_year_ts;
		}
		
		function get_end_date_year_ts($year){
			$to_year = $year + 1;
			$end_date_year_ts = strtotime("01/01/$to_year");
			
			return $end_date_year_ts;
		}
		
		function validate_year($validate_year){
			
			if( empty( $validate_year ) ){
				$validate_year = date("Y");
			}
			
			$validate_year = intval($validate_year);
			
			return $validate_year;
		}
		
		function validate_month($month){
			
			if( empty( $month ) ){
				$month = date("n");
			}
			
			$month = intval($month);
			
			return $month;
		}

		public function query(){}
	}