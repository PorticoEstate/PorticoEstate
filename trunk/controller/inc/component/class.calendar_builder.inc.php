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
	
	function init_calendar( $control, $num, $period_type ){
		
		$calendar_array = array();
		
		for($i=1;$i<=$num;$i++){
			$calendar_array[$i] = null;
		}
		
		$date_generator = new date_generator($control->get_start_date(), $control->get_end_date(), $this->period_start_date, $this->period_end_date, $control->get_repeat_type(), $control->get_repeat_interval());
		$dates_array = $date_generator->get_dates();
		
		// Inserts dates 
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
   	
	public function build_calendar_array( $controls_for_location_array, $num, $period_type ){
		
		foreach($controls_for_location_array as $control){

			$calendar_array = $this->init_calendar( $control, $num, $period_type );

			if($period_type == "view_days" | ($period_type == "view_months" & $control->get_repeat_type() == 2 | $control->get_repeat_type() == 3))
			{
				foreach($control->get_check_lists_array() as $check_list)
				{
					$check_list_status_info = new check_list_status_info();
					$check_list_status_info->set_check_list_id( $check_list->get_id() );
			
					$todays_date_ts = mktime(0,0,0,date("m"), date("d"), date("Y"));
	
					if( $check_list->get_status() == 0 & $check_list->get_planned_date() > 0 & $check_list->get_deadline() > $todays_date_ts)
					{
						$status = "control_planned";
					}
					else if( $check_list->get_status() == 0 & $check_list->get_planned_date() > 0 & $check_list->get_deadline() < $todays_date_ts )
					{
						$status = "control_not_accomplished_with_info";
					}
					else if( $check_list->get_status() == 0 & $check_list->get_deadline() < $todays_date_ts )
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
					
					$calendar_array[ date("j", $check_list->get_deadline()) ]["status"] = $status;
					$calendar_array[ date("j", $check_list->get_deadline()) ]["info"] = $check_list_status_info->serialize();
				}
				
				$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
			}
			else if($period_type == "view_months" & ($control->get_repeat_type() == 0 | $control->get_repeat_type() == 1))
			{
				$twelve_month_array = array();
				
				foreach($control->get_agg_open_cases_for_month_array() as $status_agg_month_info)
				{
					$status = "controls_accomplished_with_errors";
						
					$twelve_month_array[$status_agg_month_info->get_month_nr()]["status"] = $status;
					$twelve_month_array[$status_agg_month_info->get_month_nr()]["info"] = $status_agg_month_info->get_agg_open_cases();
				}
					
				$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $twelve_month_array);
			}
		}

		return $controls_calendar_array;
	}
}