<?php

/* This class generates dates based on start date, end date, 
 * repeat type(day, week, month, year) and repeat interval
 */

class date_generator 
{		
	private $start_date;
	private $end_date;
	private $period_start_date;
	private $period_end_date;
	private $repeat_type;
	private $repeat_interval;
	
	private $calendar_array = array();

	public function __construct($start_date, $end_date, $period_start_date, $period_end_date, $repeat_type, $repeat_interval)
	{
		$this->start_date = $start_date;
		$this->end_date = $end_date;
		$this->period_start_date = $period_start_date;
		$this->period_end_date = $period_end_date;
		$this->repeat_type = $repeat_type;
		$this->repeat_interval = $repeat_interval;

		$this->generate_calendar();
	}
   		
	function generate_calendar()
	{
		$control_start_date = $this->find_control_start_date();
		$control_end_date = $this->end_date;

		if($control_end_date == null)
		{
			$control_end_date = $this->period_end_date;
		}
/*		
_debug_array($control_start_date);
_debug_array($control_end_date);
*/
		$period_start_date = $this->find_start_date_for_period( $control_start_date );
	  
		$interval_date = $period_start_date;
		
		while(($interval_date < $this->period_end_date) && ($interval_date <= $control_end_date))
		{
			$this->calendar_array[] = $interval_date; 
						
			if($this->repeat_type == 0)
			{
				$interval_date = mktime(0,0,0, date("m", $interval_date), date("d", $interval_date)+$this->repeat_interval, date("Y", $interval_date));
			}
			else if($this->repeat_type == 1)
			{
				$interval_date = mktime(0,0,0, date("m", $interval_date), date("d", $interval_date) + ($this->repeat_interval * 7), date("Y", $interval_date));
			}
			else if($this->repeat_type == 2)
			{
				$month = date("m", $interval_date)+$this->repeat_interval;
				$year = date("Y", $interval_date);
				if($month > 12)
				{
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
   	
	public function find_control_start_date()
	{
	   	if( $this->repeat_type == 0 )
	   	{
			$control_start_date = $this->start_date;
		}
		else if( $this->repeat_type == 1 )
		{
			$control_start_date = $this->start_date;
	
			while(date("l", $control_start_date) != "Sunday")
			{
				$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, date("m", $control_start_date), date("y", $control_start_date));
				
				if($num_days_in_month <= date("d", $control_start_date) )
				{
					$control_start_date = mktime(0,0,0, date("m", $control_start_date)+1, 1, date("Y", $control_start_date));
				}	
				else
				{
					$control_start_date = mktime(0,0,0, date("m", $control_start_date), date("d", $control_start_date)+1, date("Y", $control_start_date));
				}
			}
		}
		else if( $this->repeat_type == 2 )
		{
			$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, date("m", $this->start_date), date("y", $this->start_date));
			$control_start_date = mktime(0,0,0, date("m", $this->start_date), $num_days_in_month, date("y", $this->start_date));
		}
		else if( $this->repeat_type == 3 )
		{
//			$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, 12, date("y", $this->start_date));
			$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, date("m", $this->start_date), date("y", $this->start_date));
//			$control_start_date = mktime(0,0,0, 12, $num_days_in_month, date("y", $this->start_date));
			$control_start_date = mktime(0,0,0, date("m", $this->start_date), $num_days_in_month, date("y", $this->start_date));
		}
		
		return $control_start_date;
  }
   	
	public function find_start_date_for_period( $trail_period_start_date )
	{		   		
		while( $trail_period_start_date < $this->period_start_date )
		{
			if($this->repeat_type == 0)
			{
				$trail_period_start_date = mktime(0,0,0, date("m", $trail_period_start_date), date("d", $trail_period_start_date) + $this->repeat_interval, date("Y", $trail_period_start_date));
			}
   			else if($this->repeat_type == 1)
			{
				$trail_period_start_date = mktime(0,0,0, date("m", $trail_period_start_date), date("d", $trail_period_start_date) + ($this->repeat_interval * 7), date("Y", $trail_period_start_date));
			}
			else if($this->repeat_type == 2)
			{
				$month = date("m", $trail_period_start_date) + $this->repeat_interval;
				$year = date("Y", $trail_period_start_date);
				
				if($month > 12)
				{
					$month = $month % 12;
					$year += 1;
				}

				$num_days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
				$trail_period_start_date = mktime(0,0,0, $month, $num_days_in_month, $year);
			}
			else if($this->repeat_type == 3)
			{
				$trail_period_start_date = mktime(0,0,0, date("m", $trail_period_start_date), date("d", $trail_period_start_date), date("Y", $trail_period_start_date)+$this->repeat_interval);	
			}
		}
		
		return $trail_period_start_date;
	}
   		
	public function get_dates()
	{
		return $this->calendar_array;
	}
}
