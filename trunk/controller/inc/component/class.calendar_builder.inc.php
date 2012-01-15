<?php

include_class('controller', 'date_generator', 'inc/component/');

class calendar_builder {
	
	private $period_start_date;
	private $period_end_date;

	public function __construct($period_start_date, $period_end_date){
        $this->period_start_date = $period_start_date;
        $this->period_end_date = $period_end_date;
   	}
	
	public function build_calendar_array( $control_array, $controls_calendar_array, $num, $period_type ){
		
		foreach($control_array as $control){
						
			// Initialises twelve_months_array
			for($i=1;$i<=$num;$i++){
				$calendar_array[$i] = null;
			}

			$date_generator = new date_generator($control->get_start_date(), $control->get_end_date(), $this->period_start_date, $this->period_end_date, $control->get_repeat_type(), $control->get_repeat_interval());
			$dates_array = $date_generator->get_dates();
		
			// Inserts dates on behalf of repeat type and repeat interval
			foreach($dates_array as $date){
				
				$todays_date = mktime(0,0,0,date("m"), date("d"), date("Y"));
				
				if($date < $todays_date){
					$status = "control_not_accomplished";
				}else{
					$status = "control_registered";
				}
				
				if( $period_type == "view_months" )
				{
					$calendar_array[ date("n", $date) ]["status"]  = $status;
					$calendar_array[ date("n", $date) ]["info"]  = array("date" => $date, "control_id" => $control->get_id());
				}
				else if( $period_type == "view_days" )
				{
					$calendar_array[ date("j", $date) ]["status"]  = $status;
					$calendar_array[ date("j", $date) ]["info"]  = array("date" => $date, "control_id" => $control->get_id());	
				}
			}
			
			// Inserts check_list object on deadline month in twelve_months_array
			foreach($control->get_check_lists_array() as $check_list){
				
				$check_list_status_info = new check_list_status_info();
				$check_list_status_info->set_check_list_id( $check_list->get_id() );
		
				$todays_date = mktime(0,0,0,date("m"), date("d"), date("Y"));
				
				if( $check_list->get_status() == 0 & $check_list->get_planned_date() > 0 & $check_list->get_deadline() > $todays_date)
				{
					$status = "control_planned";
					$check_list_status_info->set_info_text("Planlagt utført: " . $check_list->get_planned_date());
				}
				else if( $check_list->get_status() == 0 & $check_list->get_planned_date() > 0 & $check_list->get_deadline() < $todays_date)
				{
					$status = "control_not_accomplished_with_info";
				}
				else if( $check_list->get_status() == 0 & $check_list->get_deadline() > $todays_date )
				{
					$status = "control_not_accomplished";
				}
				else if( $check_list->get_status() == 1 & $check_list->get_completed_date() > $check_list->get_deadline() )
				{
					$status = "control_accomplished_over_time_without_errors";
				}
				else if( $check_list->get_status() == 1 & $check_list->get_completed_date() < $check_list->get_deadline() )
				{
					$status = "control_accomplished_in_time_without_errors";
				}
				else if( $check_list->get_status() == 2  ){
					$status = "control_accomplished_with_errors";
					$check_list_status_info->set_num_open_cases($check_list->get_num_open_cases());
				}
				else if( $check_list->get_status() == 3 )
				{
					$status = "control_canceled";
				}
				
				$check_list_status_info->set_deadline_date( date("d/m-Y", $check_list->get_deadline()) );
				
				if($period_type == "view_months")
				{
					$calendar_array[ date("n", $check_list->get_deadline()) ]["status"] = $status;
					$calendar_array[ date("n", $check_list->get_deadline()) ]["info"] = $check_list_status_info->serialize();	
				}
				else if( $period_type == "view_days" )
				{
					$calendar_array[ date("j", $check_list->get_deadline()) ]["status"] = $status;
					$calendar_array[ date("j", $check_list->get_deadline()) ]["info"] = $check_list_status_info->serialize();
				}
			}
			
			$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
		}

		return $controls_calendar_array;
	}
	
	// Function receives array with control objects that each contain check_lists for a certain period
	public function build_agg_calendar_array( $controls_array ){
					
		$calendar_array = array();
		
		foreach($controls_array as $control_array){
			
			$control_info = $control_array['control'];
			$check_list_array = $control_array['check_list'];
			
			$control_id = $control_info['id'];
			 
			// Initialises twelve_months_array
			for($i=0;$i<12;$i++){
				$calendar_array[$i] = null;
			}
			
			$status = "control_agg_accomplished_with_errors";
			
			// Inserts check_list object on deadline month in twelve_months_array
			foreach($check_list_array as $check_list){
				$calendar_array[ date("m", $check_list['deadline']) - 1 ] ["status"] = $status;
				$calendar_array[ date("m", $check_list['deadline']) - 1 ] ["info"] = $check_list['count']; 
			}
			
			$control_calendar_array[] = array("control" => $control_info, "calendar_array" => $calendar_array);
		}

		return $control_calendar_array;
	}
}