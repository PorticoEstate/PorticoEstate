<?php
	class date_helper {
	
	public function __construct(){}
	 	
	public static function get_timestamp_from_date( $date_string, $format  ){
		
		if( $format == "d/m-Y" ){
			$pos_day = strpos($date_string, "/"); 
			$day = date_helper::substring($date_string, 0, $pos_day);
			
			$pos_month = strpos($date_string, "-");
			$month = date_helper::substring($date_string, $pos_day+1, $pos_month);
			
			$year = date_helper::substring($date_string, $pos_month+1, strlen($date_string));

			return mktime(0, 0, 0, $month, $day, $year);
		}
	}
	
	public function substring($string, $from, $to){
    	return substr($string, $from, $to - $from);
	}
}