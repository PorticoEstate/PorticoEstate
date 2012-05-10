<?php
phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');
include_class('controller', 'check_list_status_info', 'inc/helper/');
include_class('controller', 'check_list_status_manager', 'inc/helper/');
	

class year_calendar {
	
	private $period_start_date_ts;
    private $period_end_date_ts;
	private $year;
	private $control;
	private $calendar_array = array();
	
	public function __construct($control, $year){
        $this->year = $year;
        $this->control = $control;
        
        $this->period_start_date_ts = strtotime("01/01/$year");
		$to_year = $year + 1;
		$this->period_end_date_ts = strtotime("01/01/$to_year");
        
        $this->init_calendar();
   	}
   	   	
	function init_calendar(){

		for($i = 1;$i <= 12;$i++){
			$this->calendar_array[$i] = null;
		}
		
		$date_generator = new date_generator($this->control->get_start_date(), $this->control->get_end_date(), $this->period_start_date_ts, $this->period_end_date_ts, $this->control->get_repeat_type(), $this->control->get_repeat_interval());
		$dates_array = $date_generator->get_dates();
		
		// Inserts dates 
		foreach($dates_array as $date){
			
			$todays_date = mktime(0,0,0,date("m"), date("d"), date("Y"));
			
			if($date < $todays_date){
				$status = "CONTROL_NOT_DONE";
			}else{
				$status = "CONTROL_REGISTERED";
			}
			
			$this->calendar_array[ date("n", $date) ]["status"]  = $status;
			$this->calendar_array[ date("n", $date) ]["info"]  = array("date" => $date, "control_id" => $this->control->get_id());
		}
	}
   	
	public function build_calendar( $check_lists_array ){
		
		foreach($check_lists_array as $check_list){
								
			$check_list_status_manager = new check_list_status_manager( $check_list );
			$check_list_status_info = $check_list_status_manager->get_status_for_check_list(); 
							
			$this->calendar_array[ date("n", $check_list_status_info->get_deadline_date_ts()) ]["status"]  = $check_list_status_info->get_status();
			$this->calendar_array[ date("n", $check_list_status_info->get_deadline_date_ts()) ]["info"]  = $check_list_status_info->serialize();
		}
		
		return $this->calendar_array;
	}
	
	public function build_agg_calendar( $agg_open_cases_pr_month_array ){
		
		foreach($agg_open_cases_pr_month_array as $status_agg_month_info)
		{
			$status = "CONTROLS_DONE_WITH_ERRORS";
					
			$this->calendar_array[$status_agg_month_info->get_month_nr()]["status"] = $status;
			$this->calendar_array[$status_agg_month_info->get_month_nr()]["info"]["agg_open_errors"] = $status_agg_month_info->get_agg_open_cases();
		}
					
		return $this->calendar_array;
	}
	
}