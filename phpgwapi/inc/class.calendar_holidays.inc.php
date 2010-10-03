<?php
/**
 * Calendar holidays calculation
 * @author Kai Hofmann <khofmann@probusiness.de>
 * @copyright Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
 * @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
 * @package phpgwapi
 * @subpackage utilities
 * @version $Id$
 * @internal Original ANSI-C code (C) 2003 Dipl.-Inform. Kai Hofmann <hofmann@hofmann-int.de>
 * @internal For more about calendar calculations see http://www.datelib.de/
 */

/**
 * Calendar holidays calculation
 *
 * @package phpgwapi
 * @subpackage utilities
 */
 class calendar_holidays
 {
	/**
	 * @var array $religions Supported religions
	 * @static
	 */
   var $religions = array('Atheistic','Evangeli','Catholic');
	/**
	 * @var array $countries Supported countries
	 * @static
	 */
   var $countries = array('Germany');
	/**
	 * @var array $federal_states Supported federal states of supported countries
	 * @static
	 */
   var $federal_states = array('Germany' => array('Baden-Wuerttemberg','Bavaria','Berlin','Brandenburg','Hansestadt Bremen','Hansestadt Hamburg','Hesse','Mecklenburg-Western Pomerania','Lower Saxony','North Rhine-Westphalia','Rhineland-Palatinate','Saarland','Saxony','Saxony-Anhalt','Schleswig-Holstein','Thuringia'));

	/**
	 * Constructor
	 *
	 * @static
	 */
   function calendar_holidays()
   {
   }
   

	/**
	* Count the number of occurences for a fixed date within a given start/end date range
	*
	* The start/end date range is not allowed to be larger than 1 year (i.e. 365/366 days)
	* @param integer $startday Day of start date
	* @param integer $startmonth Month of start date
	* @param integer $startyear Year of start date
	* @param integer $endday Day of end date
	* @param integer $endmonth Month of end date
	* @param integer $endyear Year of end date
	* @param integer $day Day of fixed date to count
	* @param integer $month Month of fixed date to count
	* @param integer &$weekday Weekday of the fixed date when it occurs (1=Monday,....,7=Sunday), 0 on error
	* @return integer 0|1 fixed date occurs zero or one time within date range
	* @access private
	* @static
	*/
   function countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$day,$month,&$weekday)
   {
	 $weekday = jddayofweek(gregoriantojd($month,$day,$startyear),0);
	 $weekday = ($weekday == 0) ? 7 : $weekday ;
	 if ($startyear == $endyear)
	 {
	   if (((gregoriantojd($startmonth,$startday,$startyear) - gregoriantojd($month,$day,$startyear)) > 0) ||
		   ((gregoriantojd($month,$day,$endyear) - gregoriantojd($endmonth,$endday,$endyear)) > 0)
		  )
	   {
		 return(0);
	   }
	   else
	   {
		 return(1);
	   }
	 }
	 else if ($startyear < $endyear)
	 {
	   if ((gregoriantojd($startmonth,$startday,$startyear) - gregoriantojd($month,$day,$startyear)) > 0) /* date1 > date2 */
	   {
		 if ((gregoriantojd($month,$day,$endyear) - gregoriantojd($endmonth,$endday,$endyear)) > 0)
		 {
		   return(0);
		 }
		 else
		 {
		   $weekday = jddayofweek(gregoriantojd($month,$day,$endyear),0); /* 0 = Sun; 1 = Mon; ... */
		   $weekday = ($weekday == 0) ? 7 : $weekday ;
		   return(1);
		 }
	   }
	   else /* date1 <= date2 */
	   {
		 return(1);
	   }
	 }
	 else /* startyear > endyear*/
	 {
	   $weekday = -1;
	   return(0);
	 }
   }


	/**
	* Count the number of occurences for a variable date within a given start/end date range
	*
	* The start/end date range is not allowed to be larger than 1 year (i.e. 365/366 days)
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $startday Day of start date
	* @param integer $startmonth Month of start date
	* @param integer $startyear Year of start date
	* @param integer $endday Day of end date
	* @param integer $endmonth Month of end date
	* @param integer $endyear Year of end date
	* @param string $func Function name of the function that calculates the variable holiday 
	* @return integer 0|1|2 variable date occurs zero,one or two times within date range
	* @access private
	* @static
	* @see goodFriday(), easterMonday(), ascension(), whitmonday(), corpusChristi()
	*/
   function countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$func)
   {
	 $day = 0;
	 $month = 0;

	 if ($startyear <= $endyear)
	 {
	   if ($startyear < $endyear)
	   {
		 $counter = 0;

		 $this->$func($startyear,$day,$month);
		 if ((gregoriantojd($startmonth,$startday,$startyear) - gregoriantojd($month,$day,$startyear)) <= 0)
		 {
		   ++$counter;
		 }
		 $this->$func($endyear,$day,$month);
		 if ((gregoriantojd($month,$day,$endyear) - gregoriantojd($endmonth,$endday,$endyear)) <= 0)
		 {
		   ++$counter;
		 }
		 return($counter);
	   }
	   else
	   {
		 $this->$func($startyear,$day,$month);
		 if (((gregoriantojd($startmonth,$startday,$startyear) - gregoriantojd($month,$day,$startyear)) > 0) ||
			 ((gregoriantojd($month,$day,$endyear) - gregoriantojd($endmonth,$endday,$endyear)) > 0)
			)
		 {
		   return(0);
		 }
		 else
		 {
		   return(1);
		 }
	   }
	 }
	 else
	 {
	   return(0);
	 }
   }


	/**
	* Calculate the "good friday" i.e. the friday before easter sunday
	*
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $year Year for which to calculate the holiday
	* @param integer &$day Day of the holiday within the given year
	* @param integer &$month Month of the holiday within the given year
	* @access private
	* @static
	*/
   function goodFriday($year,&$day,&$month)
   {
	 $easter_timestamp = jdtounix(unixtojd(easter_date($year))-2);
	 $day = date("d",$easter_timestamp);
	 $month = date("m",$easter_timestamp);
   }


	/**
	* Calculate the "easter monday" i.e. the monday after easter sunday
	*
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $year Year for which to calculate the holiday
	* @param integer &$day Day of the holiday within the given year
	* @param integer &$month Month of the holiday within the given year
	* @access private
	* @static
	*/
   function easterMonday($year, &$day, &$month)
   {
	 $easter_timestamp = jdtounix(unixtojd(easter_date($year))+1);
	 $day = date("d",$easter_timestamp);
	 $month = date("m",$easter_timestamp);
   }


	/**
	* Calculate the "ascension" i.e. 39 days after easter sunday
	*
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $year Year for which to calculate the holiday
	* @param integer &$day Day of the holiday within the given year
	* @param integer &$month Month of the holiday within the given year
	* @access private
	* @static
	*/
   function ascension($year, &$day, &$month)
   {
	 $easter_timestamp = jdtounix(unixtojd(easter_date($year))+39);
	 $day = date("d",$easter_timestamp);
	 $month = date("m",$easter_timestamp);
   }


	/**
	* Calculate the "whitemonday" i.e. 50 days after easter sunday
	*
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $year Year for which to calculate the holiday
	* @param integer &$day Day of the holiday within the given year
	* @param integer &$month Month of the holiday within the given year
	* @access private
	* @static
	*/
   function whitmonday($year, &$day, &$month)
   {
	 $easter_timestamp = jdtounix(unixtojd(easter_date($year))+50);
	 $day = date("d",$easter_timestamp);
	 $month = date("m",$easter_timestamp);
   }


	/**
	* Calculate the "corpus christi" i.e. 60 days after easter sunday
	*
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $year Year for which to calculate the holiday
	* @param integer &$day Day of the holiday within the given year
	* @param integer &$month Month of the holiday within the given year
	* @access private
	* @static
	*/
   function corpusChristi($year, &$day, &$month)
   {
	 $easter_timestamp = jdtounix(unixtojd(easter_date($year))+60);
	 $day = date("d",$easter_timestamp);
	 $month = date("m",$easter_timestamp);
   }


	/**
	* Count the number of holidays with the given start/end date range
	*
	* The start/end date range is not allowed to be larger than 1 year (i.e. 365/366 days)
	* @param integer $startday Day of start date
	* @param integer $startmonth Month of start date
	* @param integer $startyear Year of start date
	* @param integer $endday Day of end date
	* @param integer $endmonth Month of end date
	* @param integer $endyear Year of end date
	* @param integer $country Country for which to calculate the working days
	* @param integer $federal_state Federal state of given country for which to calculate the working days
	* @param integer $religion Religion for which to calculate the working days
	* @return Number of holidays in start/end date range that are not on a saturday or sunday.
	* @static
	*/
   function get_number_of_holidays($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$country,$federal_state,$religion)
   {
   	 $days = 0;
	 if ($country == 'Germany')
	 {
	   /* New year */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,1,1,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   } 

	   /* 1. May */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,1,5,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   }

	   /* German Unification Day */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,3,10,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   }

	   /* Christmas Eve and New Year's eve as half days */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,24,12,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   } 

	   /* 1. Christmas day */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,25,12,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   }

	   /* 2. Christmas day */
	   if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,26,12,$weekday) > 0) &&
		   ($weekday <= 5 /* Friday */))
	   {
		 ++$days;
	   }
  
	   /* Good Friday */
	   $days += $this->countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'goodFriday');

	   /* Easter Monday */
	   $days += $this->countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'easterMonday');

	   /* Ascension */
	   $days += $this->countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'ascension');

	   /* Whitemonday */
	   $days += $this->countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'whitmonday');
	 
	   if (in_array($federal_state,array('Baden-Wuerttemberg','Bavaria','Saxony-Anhalt')))
	   {
		 /* Epiphany */
		 if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,6,1,$weekday) > 0) &&
			 ($weekday <= 5 /* Friday */))
		 {
		   ++$days;
		 } 
	   }
	 
	   if (in_array($federal_state,array('Brandenburg','Mecklenburg-Western Pomerania','Saxony','Saxony-Anhalt','Thuringia')))
	   {
		 /* Reformation Day */
		 if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,31,10,$weekday) > 0) &&
			 ($weekday <= 5 /* Friday */))
		 {
		   ++$days;
		 } 
	   }
	 
	   if (in_array($federal_state,array('Baden-Wuerttemberg','Bavaria','North Rhine-Westphalia','Rhineland-Palatinate','Saarland')))
	   {
		 /* All Hallows Day */
		 if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,1,11,$weekday) > 0) &&
			 ($weekday <= 5 /* Friday */))
		 {
		   ++$days;
		 } 
	   }

	   if (in_array($federal_state,array('Saarland')) || (in_array($federal_state,array('Bavaria')) && ($religion == 'Catholic')))
	   {
		 /* Assumption Day */
		 if (($this->countFixDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,15,8,$weekday) > 0) &&
			 ($weekday <= 5 /* Friday */))
		 {
		   ++$days;
		 } 
	   }
	 
	   if (in_array($federal_state,array('Baden-Wuerttemberg','Bavaria','Hesse','North Rhine-Westphalia','Rhineland-Palatinate','Saarland')) || (($religion == 'Catholic') && in_array($federal_state,array('Saxony','Thuringia'))))
	   {
		/* Corpus Christi */
		$days += $this->countVarDateInRange($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'corpusChristi');
	   }
	 
	 }
	 return($days);
   }


	/**
	* Calculate the number of working days within a given start/end date range
	*
	* The start/end date range is not allowed to be larger than 1 year (i.e. 365/366 days)
	* @param integer $startday Day of start date
	* @param integer $startmonth Month of start date
	* @param integer $startyear Year of start date
	* @param integer $endday Day of end date
	* @param integer $endmonth Month of end date
	* @param integer $endyear Year of end date
	* @param integer $country Country for which to calculate the working days
	* @param integer $federal_state Federal state of given country for which to calculate the working days
	* @param integer $religion Religion for which to calculate the working days
	* @return integer Number of working days within the given date range or -1 on error
	* @static
	* @see countFixDateInRange(), countVarDateInRange()
	*/
   function get_number_of_workdays($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$country,$federal_state,$religion)
   {
	 $firstweekday = jddayofweek(gregoriantojd($startmonth,$startday,$startyear),0); /* 0 = Sun; 1 = Mon; ... */
	 $firstweekday = ($firstweekday == 0) ? 7 : $firstweekday; /* 1 = Mon; ... 7 = Sun */
	 $days = gregoriantojd($endmonth,$endday,$endyear) - gregoriantojd($startmonth,$startday,$startyear) + 1;
	 if ($days > 366)
	 {
	   // echo "range to large!\n";
	   return(-1);
	 }
	 $rest = $days % 7;
	 $lwd = $firstweekday + $rest - 1;
	 $lastweekday = (($lwd <= 7) ? $lwd : ($lwd - 7));

	 // echo 'days: ' . $days . "\n";
	 // echo 'days / 7: ' . floor($days / 7) . "\n";
	 // echo 'days % 7: ' . ($days % 7) . "\n";
	 // echo $firstweekday . ':' . $lastweekday . "\n";

	 $days -= floor($days / 7) * 2; // Subtract number of weekend days (Sat/Sun) in range
	 $days -= (($firstweekday <= $lastweekday) ?
	 (($lastweekday <= 5 /* Friday */) ? 0 : (1 + ((($firstweekday < $lastweekday) && ($lastweekday == 7)) ? 1 : 0))) : // Subtract 1 for Saturday or two for Sat/Sun when lastweekday falls on the weekend; for firstweekday <= lastweekday; week starts with monday as 1
	 (($firstweekday - $lastweekday == 1) ? 0 : (1 + (($firstweekday <= 6 /* Saturday */) ? 1 : 0))) // Subtract weekend days; for firstweekday > lastweekday
	 );

	 // echo 'mo-fr days: ' . $days . "\n";

	 $days -= $this->get_number_of_holidays($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,$country,$federal_state,$religion);
	 return($days);
   }
	
	
	/**
	* Get the first workday in a month
	*
	* @param integer $month Month for which we want to know the first workday
	* @param integer $year Year corresponding to the month
	* @param integer $country Country for which to calculate the working days
	* @param integer $federal_state Federal state of given country for which to calculate the working days
	* @param integer $religion Religion for which to calculate the working days
	* @return integer First workday in given month/year
	* @static
	*/
	function get_first_workday($month,$year,$country,$federal_state,$religion)
	{
	  for ($day = 1; $day <= 31; ++$day)
	  {
		$weekday = jddayofweek(gregoriantojd($month,$day,$year),0);
		$weekday = ($weekday == 0) ? 7 : $weekday ;
		if (($weekday <= 5 /* Friday */) && ($this->get_number_of_holidays($day,$month,$year,$day,$month,$year,$country,$federal_state,$religion) == 0))
		{
		  break;
		}
	  }
	  return($day);
	}


	/**
	* Add a number of workdays to a start date
	*
	* The number of workdays is not allowed to be larger than 1 year (i.e. 365/366 days)
	* The year must be within the unix epoche (Gregorian years between 1970 and 2037 or 2440588 <= jday <= 2465342)
	* @param integer $startday Day of start date
	* @param integer $startmonth Month of start date
	* @param integer $startyear Year of start date
	* @param integer $workdays Number of workdays to add to startdate (must be >= 0)
	* @param integer $country Country for which to calculate the working days
	* @param integer $federal_state Federal state of given country for which to calculate the working days
	* @param integer $religion Religion for which to calculate the working days
	* @param integer &$newday Day of end date
	* @param integer &$newmonth Month of end date
	* @param integer &$newyear Year of end date
	* @static
	*/
	function add_number_of_workdays($startday,$startmonth,$startyear,$workdays,$country,$federal_state,$religion)
	{
	  $jd = gregoriantojd($startmonth,$startday,$startyear)+1;
	  $timestamp  = jdtounix($jd);
	  $startday   = date("d",$timestamp);
	  $startmonth = date("m",$timestamp);
	  $startyear  = date("Y",$timestamp);
	  $addwdays = $workdays;
	  for (;;)
	  {
		$jd += $addwdays;
		$timestamp = jdtounix($jd);
		$newday    = date("d",$timestamp);
		$newmonth  = date("m",$timestamp);
		$newyear   = date("Y",$timestamp);
		$wdays = $this->get_number_of_workdays($startday,$startmonth,$startyear,$newday,$newmonth,$newyear,$country,$federal_state,$religion);

		// echo $newyear . '-' . $newmonth . '-' . $newday . ':' . $wdays . "\n";

		if ($wdays == $workdays)
		{
		  break;
		}
		$addwdays = $workdays - $wdays;
	  }
	  
	  $days = array
	  	(
	  		'newday'=>$newday,
	  		'newmonth'=>$newmonth,
	  		'newyear'=>$newyear,
	  		
	  	);
	  return $days;
	}


   	function is_workday($timestamp)
	{
		$month = date('m',$timestamp);
		$day = date('d',$timestamp);
		$year = date('Y',$timestamp);
		
		return $this->get_number_of_workdays($day, $month, $year,
											 $day, $month, $year,
											 'Germany', 'Lower Saxony', 'Evangeli');
	}

	
 }


 /*
 Examples:

 $startday   = 1;
 $startmonth = 7;
 $startyear  = 2003;
 $endday     = 30;
 $endmonth   = 6;
 $endyear    = 2004;
 $workdays   = 253;
 */
 /*
 $startday   = 1;
 $startmonth = 1;
 $startyear  = 2004;
 $endday     = 31;
 $endmonth   = 12;
 $endyear    = 2004;
 $workdays   = 256;
 */
 /*
 $startday   = 1;
 $startmonth = 7;
 $startyear  = 2004;
 $endday     = 30;
 $endmonth   = 6;
 $endyear    = 2005;
 $workdays   = 256;

 $cal_workdays = new calendar_holidays();
 $days = $cal_workdays->get_number_of_workdays($startday,$startmonth,$startyear,$endday,$endmonth,$endyear,'Germany','Lower Saxony','Evangelic');
 echo 'work days: ' . $days . "\n";
 */
 /*
 $cal_workdays = new calendar_holidays();
 $cal_workdays->add_number_of_workdays(31,5,2004,3,'Germany','Lower Saxony','Evangelic',$day,$month,$year);
 echo $year . '-' . $month . '-' . $day . "\n";
 */
?>