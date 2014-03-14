<?php
phpgw::import_class('controller.socheck_list');
include_class('controller', 'date_generator', 'inc/component/');
include_class('controller', 'check_list_status_info', 'inc/component/');
include_class('controller', 'check_list_status_manager', 'inc/component/');
include_class('controller', 'check_list', 'inc/model/');
	

class year_calendar_agg {
	private $year;
	private $control;
	private $location_code;
	private $view;
	
	private $calendar_array = array();
	
  public function __construct($control, $year, $location_code, $view)
  {
    $this->year = $year;
    $this->control = $control;
    $this->location_code = $location_code;
    $this->view = $view;
         
    $this->init_calendar();
  }
   	   	 
	function init_calendar()
	{
		$start_month_nr = $this->get_start_month_for_control( $this->control, $this->year );
		$end_month_nr = $this->get_end_month_for_control( $this->control, $this->year ); 
		
    for($month_nr = 1;$month_nr <= 12;$month_nr++)
    {
    	if( ($month_nr < $start_month_nr) || ($month_nr > $end_month_nr) )
    	{
    		$this->calendar_array[ $month_nr ] = null;
    	}
     	else if( ($month_nr < date("m"))  && (date("Y", $this->control->get_start_date()) == $this->year) )
    	{
    		$this->calendar_array[ $month_nr ]["status"] = "CONTROLS_NOT_DONE";
    		$this->calendar_array[ $month_nr ]["info"] = array("view" => $this->view, "control_id" => $this->control->get_id(), "location_code" =>  $this->location_code, "year" => $this->year, "month" => $month_nr);
    	}
    	else
    	{
    		$this->calendar_array[ $month_nr ]["status"] = "CONTROLS_REGISTERED";
    		$this->calendar_array[ $month_nr ]["info"] = array("view" => $this->view, "control_id" => $this->control->get_id(), "location_code" =>  $this->location_code, "year" => $this->year, "month" => $month_nr);
    	}
    }
	}
	
	public function build_calendar( $agg_open_cases_pr_month_array )
	{
		foreach($agg_open_cases_pr_month_array as $status_agg_month_info)
		{
			$status = "CONTROLS_DONE_WITH_ERRORS";
			
			$this->calendar_array[$status_agg_month_info->get_month_nr()]["status"] = $status;
			$this->calendar_array[$status_agg_month_info->get_month_nr()]["info"]["agg_open_errors"] = $status_agg_month_info->get_agg_open_cases();
		}
					
		return $this->calendar_array;
	}
	
	public static function get_heading_array(){
			
		$heading_array = array();
		
		for($i=1;$i<=12;$i++){
			$heading_array[$i] = "$i";	
		}
		
		return $heading_array;
	}
	
	function get_start_month_for_control($control, $year)
	{
		  // Checks if control starts in the year that is displayed 
			if( date("Y", $control->get_start_date()) == $year )
			{
				$from_month = date("n", $control->get_start_date());	
			}
			else
			{
				$from_month = 1;
			}
			
			return $from_month;
		}
		
		function get_end_month_for_control($control, $year)
		{	
			// Checks if control ends in the year that is displayed
			if( date("Y", $control->get_end_date()) == $year )
			{
				$to_month = date("n", $control->get_end_date());
			}
			else
			{
				$to_month = 12;
			}
			
			return $to_month;
	  }
	
	public static function get_start_date_year_ts($year){
	  $start_date_year_ts = strtotime("01/01/$year");
			
		return $start_date_year_ts;
	}
}