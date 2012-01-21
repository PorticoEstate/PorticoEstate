<?php
phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');

	
class calendar_builder {
	
	private $period_start_date;
	private $period_end_date;

	public function __construct($period_start_date, $period_end_date){
        $this->period_start_date = $period_start_date;
        $this->period_end_date = $period_end_date;
   	}
	
	public function build_calendar_array( $controls_calendar_array, $control_array, $num, $period_type ){
		
		foreach($control_array as $control){

			$calendar_array = $this->init_calendar( $control, $calendar_array, $num, $period_type );

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
				else if( $check_list->get_status() == 1 & $check_list->get_completed_date() > $check_list->get_deadline() & $check_list->get_num_open_cases() == 0)
				{
					$status = "control_accomplished_over_time_without_errors";
				}
				else if( $check_list->get_status() == 1 & $check_list->get_completed_date() < $check_list->get_deadline() & $check_list->get_num_open_cases() == 0)
				{
					$status = "control_accomplished_in_time_without_errors";
				}
				else if( $check_list->get_status() == 1 & $check_list->get_num_open_cases() > 0){
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
	
	public function build_agg_calendar_array($controls_calendar_array, $control, $location_code, $year){
				
		if( date("Y", $control->get_start_date()) == $year ){
			$from_month = date("n", $control->get_start_date());	
		}else{
			$from_month = 1;
		}
		
		if( date("Y", $control->get_end_date()) == $year ){
			$to_month = date("n", $control->get_end_date());
		}else{
			$to_month = 12;
		}
		
		$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
		
		$twelve_month_array = array();
		
		for($i=1;$i<=12;$i++){
			$trail_date_ts = strtotime("$i/01/$year");

			if($trail_date_ts > $control->get_start_date() & $trail_date_ts < $todays_date_ts){
				$status = "controls_not_accomplished";
			}else if($trail_date_ts > $control->get_start_date() & $trail_date_ts > $todays_date_ts){
				$status = "controls_registered";
			}	

			$twelve_month_array[$i-1]["status"] = $status;
		}
		
		print_r($twelve_month_array);
				
		for($from_month;$from_month<=$to_month;$from_month++){
	
			$trail_from_date_ts = strtotime("$from_month/01/$year");
			
			$trail_to_date_ts = strtotime("$to_month/01/$year");
			$so_check_list = CreateObject('controller.socheck_list');
				
			$num_open_cases_for_control_array = array();
			$num_open_cases_for_control_array = $so_check_list->get_num_open_cases_for_control( $control->get_id(), $location_code, $trail_from_date_ts, $trail_to_date_ts );	
	
			if( !empty($num_open_cases_for_control_array) ){
				$status = "controls_accomplished_with_errors";
				
				$twelve_month_array[$from_month-1]["status"] = $status;
				$twelve_month_array[$from_month-1]["info"] = $num_open_cases_for_control_array["count"];
			}else{
				$status = "controls_accomplished_without_errors";
				
				$twelve_month_array[$from_month-1]["status"] = $status;
			}
		}
	
		$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $twelve_month_array);
		 
		return $controls_calendar_array;
	}
		
	function init_calendar( $control, $calendar_array, $num, $period_type ){
		
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
		
		return $calendar_array;
	} 
}