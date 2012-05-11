<?php
phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');
include_class('controller', 'check_list_status_info', 'inc/helper/');
include_class('controller', 'check_list_status_manager', 'inc/helper/');
		
/* This class transforms controls with checklists or controls with aggregated number of open cases, 
*  and puts these values in a calendar array for each control
*/  

class month_calendar {
	
	private $period_start_date_ts;
    private $period_end_date_ts;
	private $year;
	private $month;
	private $control;
	private $calendar_array = array();
	
	public function __construct($control, $year, $month){
        $this->year = $year;
        $this->month = $month;
        $this->control = $control;
        
      	$from_month = $month;
			
		$from_date_ts = strtotime("$from_month/01/$year");
		
		if(($from_month + 1) > 12){
			$to_month = 1;
			$to_year = $year + 1;
		}else{
			$to_month = $from_month + 1;
			$to_year = $year;
		}
		
		$to_date_ts = strtotime("$to_month/01/$to_year");
        
		$this->period_start_date_ts = $from_date_ts;
		$this->period_end_date_ts = $to_date_ts;
			
        $this->init_calendar();
   	}
	
	function init_calendar(){

		$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
		
		for($i=1;$i<=$num_days_in_month;$i++){
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
			
			$this->calendar_array[ date("j", $date) ]["status"]  = $status;
			$this->calendar_array[ date("j", $date) ]["info"]  = array("date" => $date, "control_id" => $this->control->get_id());	
		}
	}
   	
	public function build_calendar( $check_lists_array ){
		
		foreach($check_lists_array as $check_list){
			$check_list_status_manager = new check_list_status_manager( $check_list );
			$check_list_status_info = $check_list_status_manager->get_status_for_check_list(); 
							
			$this->calendar_array[ date("j", $check_list->get_deadline()) ]["status"] = $check_list_status_info->get_status();
			$this->calendar_array[ date("j", $check_list->get_deadline()) ]["info"] = $check_list_status_info->serialize();
		}
		
		return $this->calendar_array;
	}
	
	public static function get_heading_array($year, $month){
		$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);		
		$heading_array = array();
		
		for($i=1;$i<=$num_days_in_month;$i++){
			$heading_array[$i] = "$i";	
		}
		
		return $heading_array;
	}
	
	public static function get_start_month_date_ts($year, $from_month){
		return strtotime("$from_month/01/$year");
	}
	
	public static function get_end_month_date_ts($year, $from_month){
		if(($from_month + 1) > 12){
			$to_month = 1;
			$to_year = $year + 1;
		}else{
			$to_month = $from_month + 1;
			$to_year = $year;
		}
		
		$to_date_ts = strtotime("$to_month/01/$to_year");
		
		return $to_date_ts; 
	}
}