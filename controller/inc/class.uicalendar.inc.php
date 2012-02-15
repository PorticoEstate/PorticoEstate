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
	include_class('controller', 'calendar_builder', 'inc/component/');
	include_class('controller', 'location_finder', 'inc/helper/');
		
	class controller_uicalendar extends controller_uicommon
	{
		private $so;
		private $so_control;
		private $so_control_group;
		private $so_control_group_list;
		private $so_control_item;
		private $so_check_list;
		private $so_check_item;
		private $calendar_builder;
				
		public $public_functions = array
		(
			'index'	=>	true,
			'view_calendar_for_month'			=>	true,
			'view_calendar_for_year'			=>	true,
			'view_calendar_for_locations'		=>  true
		);

		public function __construct()
		{
			parent::__construct();
			
			$this->so = CreateObject('controller.socheck_list');
			$this->so_control = CreateObject('controller.socontrol');
			$this->so_control_group = CreateObject('controller.socontrol_group');
			$this->so_control_group_list = CreateObject('controller.socontrol_group_list');
			$this->so_control_item = CreateObject('controller.socontrol_item');
			$this->so_check_list = CreateObject('controller.socheck_list');
			$this->so_check_item = CreateObject('controller.socheck_item');
			
			self::set_active_menu('controller::location_check_list');
		}
		
		public function view_calendar_for_month()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			$month = phpgw::get_var('month');
			
			$year = intval( $year );
			$from_month = intval( $month );
				
			$from_date_ts = strtotime("$from_month/01/$year");
			
			if(($from_month + 1) > 12){
				$to_month = 1;
				$year++;
			}else{
				$to_month = $from_month + 1;
			}
			
			$to_date_ts = strtotime("$to_month/01/$year");
												
			$this->calendar_builder = new calendar_builder($from_date_ts, $to_date_ts);
			
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
			
			$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			
			// Fetches controls for location within specified time period
			$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts);

			// Fetches control ids with check lists for specified time period
			$control_id_with_check_list_array = $this->so->get_check_lists_for_location_2($location_code, $from_date_ts, $to_date_ts);
			
			// Loops through all controls for location and populates controls with check lists
			$controls_with_check_list = $this->populate_controls_with_check_lists($controls_for_location_array, $control_id_with_check_list_array);
			
			$controls_calendar_array = array();
			$controls_calendar_array = $this->calendar_builder->build_calendar_array( $controls_calendar_array, $controls_with_check_list, $num_days_in_month, "view_days" );
			
			foreach($controls_calendar_array as &$inst)
			{	
				$curr_control = &$inst['control'];

				if($curr_control['repeat_type'] == 0)
					$curr_control['repeat_type'] = "Dag";
				else if($curr_control['repeat_type'] == 1)
					$curr_control['repeat_type'] = "Uke";
				else if($curr_control['repeat_type'] == 2)
					$curr_control['repeat_type'] = "Måned";
				else if($curr_control['repeat_type'] == 3)
					$curr_control['repeat_type'] = "År";
			}

			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$month_array = array("Januar", "Februar", "Mars", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Desember");
			
			for($i=1;$i<=$num_days_in_month;$i++){
				$heading_array[$i] = "$i";	
			}
 			
			$data = array
			(		
				'my_locations'	  		  => $my_locations,
				'view_location_code'	  => $location_code,
				'location_array'		  => $location_array,
				'heading_array'		  	  => $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  => $date_format,
				'period' 			  	  => $month_array[ $month - 1],
				'month_nr' 			  	  => $month,
				'year' 			  	  	  => $year,
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			
			self::render_template_xsl('calendar/view_calendar_month', $data);
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
		
		public function view_calendar_for_year()
		{
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			
			if(empty($year)){
				$year = date("Y");
			}
			
			$year = intval($year);

			$from_date_ts = strtotime("01/01/$year");
			$to_year = $year + 1;
			$to_date_ts = strtotime("01/01/$to_year");	
			
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
			
			$repeat_type = null;
			
			$controls_for_location_array = $this->so_control->get_controls_by_location($location_code, $from_date_ts, $to_date_ts, $repeat_type);
			 
			$this->calendar_builder = new calendar_builder($from_date_ts, $to_date_ts);
		
			$controls_calendar_array = array();

			// Puts aggregate values for daily controls in a twelve month array
			foreach($controls_for_location_array as $control)
			{
				if($control->get_repeat_type() == 0 | $control->get_repeat_type() == 1)
				{
					$controls_calendar_array = $this->calendar_builder->build_agg_calendar_array($controls_calendar_array, $control, $location_code, $year);
				}
			}
			
			$repeat_type = 2;
			$control_check_list_array_for_month = $this->so->get_check_lists_for_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type );

			$repeat_type = 3;
			$control_check_list_array_for_year = $this->so->get_check_lists_for_location( $location_code, $from_date_ts, $to_date_ts, $repeat_type );
			
			$control_check_list_array = array_merge($control_check_list_array_for_month, $control_check_list_array_for_year);
			
			$in_array = 0;
			foreach($controls_for_location_array as $control_loc){
				foreach($control_check_list_array as $control_check_list){
					if($control_loc->get_id() == $control_check_list->get_id()){
						$in_array = 1;	
					}
				}	

				if($in_array == 0 & $control_loc->get_repeat_type() != 0 & $control_loc->get_repeat_type() != 1 ){
					$control_check_list_array[] = $control_loc;
				}
			}
									
			$controls_calendar_array = $this->calendar_builder->build_calendar_array( $controls_calendar_array, $control_check_list_array, 12, "view_months" );
	
			
			foreach($controls_calendar_array as &$inst)
			{	
				$curr_control = &$inst['control'];

				if($curr_control['repeat_type'] == 0)
					$curr_control['repeat_type'] = "Dag";
				else if($curr_control['repeat_type'] == 1)
					$curr_control['repeat_type'] = "Uke";
				else if($curr_control['repeat_type'] == 2)
					$curr_control['repeat_type'] = "Måned";
				else if($curr_control['repeat_type'] == 3)
					$curr_control['repeat_type'] = "År";
			}
			
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$heading_array = array("Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des");
			
			$data = array
			(
				'my_locations'	  		  => $my_locations,
				'view_location_code'	  => $location_code,
				'location_array'		  => $location_array,
				'heading_array'		  	  => $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  => $date_format,
				'period' 			  	  => $year,
				'year' 			  	  	  => $year
			);
			
			self::render_template_xsl('calendar/view_calendar_year', $data);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}

		public function view_calendar_for_locations()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
			
			if(is_numeric($control_id) & $control_id > 0)
			{
				$locations_for_control_array = $this->so_control->get_locations_for_control($control_id);
			}
			
			$year = date("Y");
			
			$year = intval($year);
						
			$from_date_ts = strtotime("01/01/$year");
			$to_year = $year + 1;
			$to_date_ts = strtotime("01/01/$to_year");	

			$this->calendar_builder = new calendar_builder($from_date_ts, $to_date_ts);
		
			$controls_calendar_array = array();
			foreach($locations_for_control_array as $location)
			{
				$control->set_location_code($location["location_code"]);
				$controls_calendar_array = $this->calendar_builder->build_agg_calendar_array($controls_calendar_array, $control, $location["location_code"], $year);
				//_debug_array($controls_calendar_array);
				$control_check_list_array = $this->so->get_check_lists_for_location( $location["location_code"], $from_date_ts, $to_date_ts, $control->get_repeat_type(), $control->get_id() );
				//_debug_array($controls_check_list_array);
			}
			
			$controls_calendar_array = $this->calendar_builder->build_calendar_array( $controls_calendar_array, $control_check_list_array, 12, "view_months" );
			//_debug_array($controls_calendar_array);
			
			foreach($controls_calendar_array as &$inst)
			{	
				$curr_control = &$inst['control'];
				//var_dump($control['location_code']);
				foreach($locations_for_control_array as $loc1)
				{
					if($curr_control["location_code"] == $loc1["location_code"])
						$curr_control["location_name"] = $loc1["loc1_name"];
				}

				if($curr_control['repeat_type'] == 0)
					$curr_control['repeat_type'] = "Dag";
				else if($curr_control['repeat_type'] == 1)
					$curr_control['repeat_type'] = "Uke";
				else if($curr_control['repeat_type'] == 2)
					$curr_control['repeat_type'] = "Måned";
				else if($curr_control['repeat_type'] == 3)
					$curr_control['repeat_type'] = "År";
			}
			
			//_debug_array($controls_calendar_array);
			
			$heading_array = array("Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des");
			
			$data = array
			(
				'my_locations'	  		  => $locations_for_control_array,
				'view_location_code'	  => null,
				'location_array'		  => $locations_for_control_array,
				'heading_array'		  	  => $heading_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  => $date_format,
				'period' 			  	  => $year,
				'year' 			  	  	  => $year,
				'show_location'			  => 'yes',
				'control_name'			  => $control->get_title()
			);
			
			self::render_template_xsl('calendar/view_calendar_year', $data);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}

		public function query(){}
	}