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
	include_class('controller', 'calendar_builder', 'inc/components/');
		
	class controller_uilocation_check_list extends controller_uicommon
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
			'view_check_lists_for_control'		=>	true,
			'save_check_list'					=>	true,
			'view_check_list'					=>	true,
			'edit_check_list'					=>	true,
			'save_check_items'					=>	true,
			'view_check_lists_for_location'		=>	true,
			'view_calendar'						=>	true
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
		
		public function view_calendar()
		{
			$control_id = phpgw::get_var('control_id');
			$location_code = phpgw::get_var('location_code');
			$year = phpgw::get_var('year');
			$month = phpgw::get_var('month');
			
			if( empty($month) ){
				$year = intval($year);
			
				$from_date = strtotime("01/01/$year");
				$to_year = $year + 1;
				$to_date = strtotime("01/01/$to_year");	
			}
			else{
				$year = intval($year);
				$from_month = intval($month);
				
				$from_date = strtotime("$from_month/01/$year");
				$to_month = $from_month + 1;
				$to_date = strtotime("$to_month/01/$year+1");
			}
			
			$control = $this->so_control->get_single($control_id);
			
			if(empty($location_code)){
				$location_code = "1101";	
			}
			
			// Get check lists for a YEAR
			if( empty($month) )
			{
				$this->calendar_builder = new calendar_builder($from_date, $to_date);
				
				// Gets an array of controls that contains check_lists for the specified location
				$agg_check_list_array = $this->so->get_agg_check_lists_for_location( $location_code, $from_date, $to_date );
				$controls_calendar_array = $this->calendar_builder->build_agg_calendar_array( $agg_check_list_array );
			
				$repeat_type = 2;
				$check_list_array = $this->so->get_check_lists_for_location( $location_code, $from_date, $to_date, $repeat_type );
				$controls_calendar_array = $this->calendar_builder->build_calendar_array( $check_list_array, $controls_calendar_array, 12, "view_months" );
				
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				
				$heading_array = array("Jan", "Feb", "Mar", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Des");
								
				$data = array
				(
					'location_array'		  => $location_array,
					'heading_array'		  	  => $heading_array,
					'controls_calendar_array' => $controls_calendar_array,
					'date_format' 			  => $date_format,
					'period' 			  	  => $year,
					'year' 			  	  	  => $year
				);
				self::render_template_xsl('view_calendar_year', $data);
			}
			// Get check lists for a MONTH
			else
			{
				$this->calendar_builder = new calendar_builder($from_date, $to_date);
				
				$repeat_type = 0;
				$check_list_array = $this->so->get_check_lists_for_location( $location_code, $from_date, $to_date, $repeat_type);
				$controls_calendar_array = $this->calendar_builder->build_calendar_array( $check_list_array, null, 31, "view_days" );
								
				$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
				
				for($i=1;$i<=31;$i++){
					$heading_array[$i] = "$i";	
				}
								
				$data = array
				(
					
					'location_array'		  => $location_array,
					'heading_array'		  	  => $heading_array,
					'controls_calendar_array' => $controls_calendar_array,
					'date_format' 			  => $date_format,
					'period' 			  	  => $month,
					'year' 			  	  	  => $year
				);
				self::render_template_xsl('view_calendar_month', $data);	
			}
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
		}
		
		
		public function view_check_lists_for_location()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
			
			$location_code = "1101";
						
			$from_date = strtotime("01/01/2011");
			$num_days_in_dec = cal_days_in_month(CAL_GREGORIAN, 12, 2011);
			$to_date =  strtotime("12/$num_days_in_dec/2011");
			
			// Gets an array of controls that contains check_lists for the specified location 
			$control_array = $this->so->get_check_lists_for_location( $location_code, $from_date, $to_date );
			
			$controls_calendar_array = $this->calendar_builder->build_calendar_array( $control_array, $from_date, $to_date );
			
			$location_array = execMethod('property.bolocation.read_single', array('location_code' => $location_code));
			
			$data = array
			(
				'location_array'		  => $location_array,
				'controls_calendar_array' => $controls_calendar_array,
				'date_format' 			  => $date_format,
				'from_date' 			  => $from_date,
				'to_date' 			  	  => $to_date
			);
			
			self::add_javascript('controller', 'controller', 'jquery.js');
			self::add_javascript('controller', 'controller', 'ajax.js');
			self::render_template_xsl('view_check_lists_for_location', $data);
		}
				
		public function view_check_lists_for_control()
		{
			$control_id = phpgw::get_var('id');
			$control = $this->so_control->get_single($control_id);
			
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
		
			$check_list_array = $this->so->get_check_lists_for_control( $control_id );	
			
			$data = array
			(
				'control_as_array'	=> $control->toArray(),
				'check_list_array'	=> $check_list_array,
				'date_format' 		=> $date_format
			);
			
			self::render_template_xsl('view_check_lists', $data);
		}
		
		public function view_control_items_for_control()
		{
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);
						
			$control_groups_array = $this->so_control_group_list->get_control_groups_by_control_id( $control_id );

			$saved_groups_with_items_array = array();
			
			foreach ($control_groups_array as $control_group)
			{	
				$control_group_id = $control_group->get_id();
				$saved_control_items = $this->so_control_item->get_control_items_by_control_id_and_group($control_id, $control_group_id);
				
				$saved_groups_with_items_array[] = array("control_group" => $control_group->toArray(), "control_items" => $saved_control_items);
			}	
		
			$data = array
			(
				'control_as_array'				=> $control->toArray(),
				'saved_groups_with_items_array'	=> $saved_groups_with_items_array
			);
								
			self::render_template_xsl('view_check_list', $data);
		}
		
		public function save_check_items(){
			$check_item_ids = phpgw::get_var('check_item_ids');
			$check_list_id = phpgw::get_var('check_list_id');
			
			foreach($check_item_ids as $check_item_id){
				$status = phpgw::get_var('status_' . $check_item_id);
				$comment = phpgw::get_var('comment_' . $check_item_id);
				
				$check_item = $this->so_check_item->get_single($check_item_id);
				
				$check_item->set_status( $status );
				$check_item->set_comment( $comment );
				
				$this->so_check_item->store( $check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list', 'check_list_id'=>$check_list_id));	
		}
		
		public function save_check_list(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = true;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = true;
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));	
		}
		
		public function make_check_list_for_control(){
			$control_id = phpgw::get_var('control_id');
			$control = $this->so_control->get_single($control_id);

			$start_date = $control->get_start_date();
			$end_date = $control->get_end_date();
			$repeat_type = $control->get_repeat_type();
			$repeat_interval = $control->get_repeat_interval();
			
			$status = true;
			$comment = "Kommentar for sjekkliste";
			$deadline = $start_date;
			
			// Saving check_list
			$new_check_list = new controller_check_list();
			$new_check_list->set_control_id( $control_id );
			$new_check_list->set_status( $status );
			$new_check_list->set_comment( $comment );
			$new_check_list->set_deadline( $deadline );
			
			$check_list_id = $this->so_check_list->store( $new_check_list );
			
			$control_items_list = $this->so_control_item->get_control_items_by_control_id($control_id);
			
			foreach($control_items_list as $control_item){
				
				$status = true;
				$comment = "Kommentar for sjekk item";
				
				// Saving check_items for a list
				$new_check_item = new controller_check_item();
				$new_check_item->set_check_list_id( $check_list_id );
				
				$new_check_item->set_control_item_id( $control_item->get_id() );
				$new_check_item->set_status( $status );
				$new_check_item->set_comment( $comment );

				$saved_check_item = $this->so_check_item->store( $new_check_item );
			}
			
			$this->redirect(array('menuaction' => 'controller.uicheck_list.view_check_list_for_control', 'control_id'=>$control_id));	
		}
		
		public function query()
		{
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);
			
			$search_for = phpgw::get_var('query');

			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			if($sort_field == null)
			{
				$sort_field = 'control_id';
			}
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			//Create an empty result set
			$records = array();
			
			//Retrieve a contract identifier and load corresponding contract
/*			$control_id = phpgw::get_var('control_id');
			if(isset($control_id))
			{
				$control = $this->so->get_single($control_id);
			}
*/
			$result_objects = $this->so->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$object_count = $this->so->get_count($search_for, $search_type, $filters);
			//var_dump($result_objects);
								
			$results = array();
			
			foreach($result_objects as $check_list_obj)
			{
				$results['results'][] = $check_list_obj->serialize();	
			}
			
			$results['total_records'] = $object_count;
			$results['start'] = $params['start'];
			$results['sort'] = $params['sort'];
			$results['dir'] = $params['dir'];

			array_walk($results["results"], array($this, "_add_links"), "controller.uicheck_list.view_check_lists_for_control");

			return $this->yui_results($results);
		}
	}