<?php
	
class date_generator 
{		
	private $start_date;
	private $end_date;
	private $period_start_date;
	private $period_end_date;
	private $repeat_type;
	private $repeat_interval;
	
	private $calendar_array = array();

	public function __construct($start_date, $end_date, $period_start_date, $period_end_date, $repeat_type, $repeat_interval){
		$this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->period_start_date = $period_start_date;
        $this->period_end_date = $period_end_date;
        $this->repeat_type = $repeat_type;
        $this->repeat_interval = $repeat_interval;
          	
        $this->generate_calendar();
   	}
   		
	function generate_calendar(){
	
		$control_start_date = $this->find_control_start_date();
		
		$period_start_date = $this->find_start_date_for_period( $control_start_date );
	
		$interval_date = $period_start_date;
		
		while($interval_date < $this->period_end_date){
			
			$this->calendar_array[] = $interval_date; 
						
			if($this->repeat_type == 1 || $this->repeat_type == 0)
			{
				$interval_date = mktime(0,0,0, date("m", $interval_date), date("d", $interval_date)+$this->repeat_interval, date("Y", $interval_date));
			}
			else if($this->repeat_type == 2)
			{
				$month = date("m", $interval_date)+$this->repeat_interval;
				$year = date("Y", $interval_date);
				if($month > 12){
					$month = $month % 12;
					$year += 1;
				}
				
				$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$interval_date = mktime(0,0,0, $month, $num_days_in_month, $year);
			}
			else if($this->repeat_type == 3)
			{
				$interval_date = mktime(0,0,0, date("m", $interval_date), date("d", $interval_date), date("Y", $interval_date)+$this->repeat_interval);
			}
		}
	
   	}
   	
   	public function find_control_start_date(){
   	
   		if( $this->repeat_type == 0 ){
			$search_date = $this->start_date;
		}
		else if( $this->repeat_type == 1 ){
			$search_date = $this->start_date;
	
			while(date("l", $search_date) != "Sunday")
			{
				$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, date("m", $search_date), date("y", $search_date));
				
				if($num_days_in_month <= date("d", $search_date) )
				{
					$search_date = mktime(0,0,0, date("m", $search_date)+1, 1, date("Y", $search_date));
				}	
				else
				{
					$search_date = mktime(0,0,0, date("m", $search_date), date("d", $search_date)+1, date("Y", $search_date));
				}
			}
		}
		else if( $this->repeat_type == 2 ){
			$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, date("m", $this->start_date), date("y", $this->start_date));
			$search_date = mktime(0,0,0, date("m", $this->start_date), $num_days_in_month, date("y", $this->start_date));
		}
		
		return $search_date;
   	}
   	
   	public function find_start_date_for_period( $trail_date ){
   		
   		while( $trail_date < $this->period_start_date ){

			if($this->repeat_type == 1 || $this->repeat_type == 0)
			{
				$trail_date = mktime(0,0,0, date("m", $trail_date), date("d", $trail_date)+$this->repeat_interval, date("Y", $trail_date));
			}
			else if($this->repeat_type == 2)
			{
				$month = date("m", $trail_date) + $this->repeat_interval;
				$year = date("Y", $trail_date);
				
				if($month > 12){
					$month = $month % 12;
					$year += 1;
				}

				$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$trail_date = mktime(0,0,0, $month, $num_days_in_month, $year);
			}
			else if($this->repeat_type == 3)
			{
				$trail_date = mktime(0,0,0, date("m", $trail_date), date("d", $trail_date), date("Y", $trail_date)+$this->repeat_interval);	
			}
		}
		
		return $trail_date;
   	}
   	
   		
	public function get_dates(){
		return $this->calendar_array;
	}
}
