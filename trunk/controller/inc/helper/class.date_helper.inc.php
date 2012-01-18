<?php
	class date_helper {
	
	public function __construct(){}
	 	
	public static function get_timestamp_from_date( $date_string ){
		$pos_day = strpos($date_string, "/"); 
		$day =  substr($date_string, 0, $pos_day);
		
		$pos_month = strpos($date_string, "-");
		$len_month = $pos_month - $pos_day -1;
		$month = substr($date_string, $pos_day+1, $len_month);
		
		$year = substr($date_string, $pos_month + $len_month-1, strlen($date_string)-1);
		
		return mktime(0, 0, 0, $month, $day, $year);
	}
}