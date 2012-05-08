<?php
phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');
include_class('controller', 'check_list_status_info', 'inc/helper/');
include_class('controller', 'check_list_status_manager', 'inc/helper/');
		
/* This class transforms controls with checklists or controls with aggregated number of open cases, 
*  and puts these values in a calendar array for each control
*/  

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
				$status = "CONTROL_NOT_DONE";
			}else{
				$status = "CONTROL_REGISTERED";
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
   	
	// Function that puts checklists into a twelve months array for displaying a year or a days array for displaying a month
	public function build_calendar_array( $controls_with_check_lists_array, $num, $period_type ){
		
		foreach($controls_with_check_lists_array as $control){
			
			// ========================  DISPLAY STATUS FOR A MONTH OR DAYS IN A MONTH  ==========================
			if($period_type == "view_days" | ($period_type == "view_months" & $control->get_repeat_type() == 2 | $control->get_repeat_type() == 3))
			{
				$check_list_array = $control->get_check_lists_array();
				
				$calendar_array = $this->init_calendar( $control, $num, $period_type );
								
				foreach($control->get_check_lists_array() as $check_list)
				{
					$check_list_status_manager = new check_list_status_manager( $check_list );
					
					$check_list_status_info = $check_list_status_manager->get_status_for_check_list(); 
					
					if( $period_type == "view_months" )
					{
						$calendar_array[ date("n", $check_list_status_info->get_deadline_date_ts()) ]["status"]  = $check_list_status_info->get_status();
						$calendar_array[ date("n", $check_list_status_info->get_deadline_date_ts()) ]["info"]  = $check_list_status_info->serialize();
					}
					else if( $period_type == "view_days" )
					{
						$calendar_array[ date("j", $check_list->get_deadline()) ]["status"] = $check_list_status_info->get_status();
						$calendar_array[ date("j", $check_list->get_deadline()) ]["info"] = $check_list_status_info->serialize();
					}
				}
				
				$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
			}
			// ========================  DISPLAY AGGREGATE STATUS FOR A MONTH  ==========================
			else if($period_type == "view_months" & ($control->get_repeat_type() == 0 | $control->get_repeat_type() == 1))
			{
				$calendar_array = array();
				
				for($i=1;$i<=12;$i++)
					$calendar_array[$i] = "";
				
				foreach($control->get_agg_open_cases_pr_month_array() as $status_agg_month_info)
				{
					$status = "controls_accomplished_with_errors";
					
					$calendar_array[$status_agg_month_info->get_month_nr()]["status"] = $status;
					$calendar_array[$status_agg_month_info->get_month_nr()]["info"] = $status_agg_month_info->get_agg_open_cases();
				}
					
				$controls_calendar_array[] = array("control" => $control->toArray(), "calendar_array" => $calendar_array);
			}
		}

		return $controls_calendar_array;
	}
}