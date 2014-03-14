<?php
	/**
	* Datetime class that contains common date/time functions
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Mark Peters <skeeter@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001 Joseph Engo, Mark Peters
	* @copyright Portions Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/*
	 * We set the default timezone here to prevent any notices
	 *
	 * Everything in phpGroupWare is based on UTC, so lets set it here
	 */
	date_default_timezone_set('UTC');

	/**
	* Datetime class that contains common date/time functions
	*
	* @package phpgwapi
	* @subpackage utilities
	*/
	class phpgwapi_datetime
	{
		/**
		* Seconds in a day
		*/
		const SECONDS_IN_DAY = 86400; // 60 * 60 * 24

		/**
		* One hour in seconds
		*/
		const SECONDS_IN_HOUR = 3600; // 60 * 60

		var $tz_offset;
		var $days = array();
		var $users_localtime;
		var $cv_gmtdate;

		/**
		* @var array $month_fullnames Full names of months
		*/
		public static $month_fullnames = array
		(
			'',
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December'
		);

		/**
		* @var array $month_shortnames Short names of months
		*/
		public static $month_shortnames = array
		(
			'',
			'Jan',
			'Feb',
			'Mar',
			'Apr',
			'May',
			'Jun',
			'Jul',
			'Aug',
			'Sep',
			'Oct',
			'Nov',
			'Dec'
		);

		/**
		* @var array $dow_fullnames Full names of days of week
		*
		* @internal this complies with ISO 8601 for numeric values for weekdays
		*/
		public static $dow_fullnames = array
		(
			'',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
			'Sunday'
		);

		/**
		* @var array $dow_mednames Medium length names of days of week
		*
		* @internal this complies with ISO 8601 for numeric values for weekdays
		*/
		public static $dow_mednames = array
		(
			'',
			'Mon',
			'Tue',
			'Wed',
			'Thu',
			'Fri',
			'Sat',
			'Sun'
		);

		/**
		* @var array $dow_shortnames Short names of days of week
		*
		* @internal this complies with ISO 8601 for numeric values for weekdays
		*/
		public static $dow_shortnames = array
		(
			'',
			'Mo',
			'Tu',
			'We',
			'Th',
			'Fr',
			'Sa',
			'Su'
		);

		/**
		* Get the current GMT time as a unixtime stamp
		*
		* @return int unixtime stamp
		*/
		public static function gmtnow()
		{
			static $offset = null;
			if ( is_null($offset) )
			{
				if ( isset($GLOBALS['phpgw_info']['server']['tz_offset']))
				{
					$offset = (int) $GLOBALS['phpgw_info']['server']['tz_offset'];
				}
				else
				{
					$offset = self::getbestguess();
				}
				$offset = $offset * self::SECONDS_IN_HOUR;
			}
			return time() + $offset;
		}

		/**
		* Gets the current user's UTC offset in seconds
		*
		* @return int offset in seconds
		*/
		public static function user_timezone()
		{
				return isset($GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset']) 
					? (int) $GLOBALS['phpgw_info']['user']['preferences']['common']['tz_offset'] * self::SECONDS_IN_HOUR : 0;
		}

		/**
		* Get the current user's localtime as a unix timestmap
		*
		* @return int unix timestmap
		*/
		public static function user_localtime()
		{
			return time() + self::user_timezone();
		}
		
		/**
		* Get the current server UTC offset using an NTP server
		*
		* @return int offset in hours
		*/
		public static function getntpoffset()
		{
			$error_occured = False;
			if ( !isset($GLOBALS['phpgw']->network) || !is_object($GLOBALS['phpgw']->network) )
			{
				$GLOBALS['phpgw']->network = createObject('phpgwapi.network');
			}
			$server_time = time();

			$ip = gethostbyname('pool.ntp.org');
			if ( !$ip )
			{
				$ip = '129.6.15.28';
			}

			if ( $GLOBALS['phpgw']->network->open_port($ip, 13, 5) )
			{
				$line = $GLOBALS['phpgw']->network->bs_read_port(64);
				$GLOBALS['phpgw']->network->close_port();

				$array = explode(' ',$line);
				// host: 129.6.15.28
				// Value returned is 52384 02-04-20 13:55:29 50 0 0   9.2 UTC(NIST) *
				print_debug('Server datetime',time(),'api');
				print_debug('Temporary NTP datetime',$line,'api');
				if ($array[5] == 4)
				{
					$error_occured = True;
				}
				else
				{
					$date = explode('-',$array[1]);
					$time = explode(':',$array[2]);
					$gmtnow = mktime((int) $time[0], (int) $time[1], (int) $time[2], (int) $date[1], (int) $date[2], (int) $date[0] + 2000);
					print_debug('Temporary RFC epoch',$gmtnow,'api');
					print_debug('GMT',date('Ymd H:i:s',$gmtnow),'api');
				}
			}
			else
			{
				$error_occured = True;
			}
			
			if($error_occured == True)
			{
				return self::getbestguess();
			}
			else
			{
				return (int) ($server_time - $gmtnow) / self::SECONDS_IN_HOUR;
			}
		}

		/**
		* Get the current server UTC offset using a NIST's time information webpage
		*
		* @return int offset in hours
		*/
		public static function gethttpoffset()
		{
			$error_occured = false;
			if ( !isset($GLOBALS['phpgw']->network) 
				|| !is_object($GLOBALS['phpgw']->network) )
			{
				$GLOBALS['phpgw']->network = createObject('phpgwapi.network');
			}
			$server_time = time();

			$ip = gethostbyname('nist.time.gov');
			if ( !$ip )
			{
				$ip = '132.163.4.213';
			}

			$filename = "http://{$ip}/timezone.cgi?UTC/s/0";
			$file = $GLOBALS['phpgw']->network->gethttpsocketfile($filename);
			if(!$file)
			{
				return self::getbestguess();
			}
			$time = strip_tags($file[55]);
			$date = strip_tags($file[56]);

			print_debug('GMT DateTime',$date.' '.$time,'api');
			$dt_array = explode(' ',$date);
			$temp_datetime = $dt_array[0].' '.substr($dt_array[2],0,-1).' '.substr($dt_array[1],0,3).' '.$dt_array[3].' '.$time.' GMT';
			print_debug('Reformulated GMT DateTime',$temp_datetime,'api');
			$gmtnow = self::convert_rfc_to_epoch($temp_datetime);
			print_debug('gmtnow',$gmtnow,'api');
			print_debug('server time',$server_time,'api');
			print_debug('server DateTime',date('D, d M Y H:i:s',$server_time),'api');
			return (int) ($server_time - $gmtnow) / self::SECONDS_IN_HOUR;
		}

		/**
		* Get the current server UTC offset using a guess
		*
		* @return int offset in hours
		*/
		public static function getbestguess()
		{
			print_debug('datetime::datetime::debug: Inside getting from local server','api');
			$server_time = time();
			// Calculate GMT time...
			// If DST, add 1 hour...
			//  - (date('I') == 1?3600:0)
			$gmtnow = self::convert_rfc_to_epoch(gmdate('D, d M Y H:i:s', $server_time).' GMT');
			return (int) ($server_time - $gmtnow) / self::SECONDS_IN_HOUR;
		}

		/**
		* Format the time takes settings from user preferences
		*
		* @param integer $hour Hour
		* @param integer $min Minute
		* @param integer $sec Second
		* @return string Time formatted as hhmmss with am/pm
		*/
		public static function formattime($hour, $min = 0, $sec = null)
		{
			$h12 = $hour;
			$ampm = '';
			if ( $GLOBALS['phpgw_info']['user']['preferences']['common']['timeformat'] == '12' )
			{
				if ($hour >= 12) 
				{
					$ampm = ' pm';
				}
				else
				{
					$ampm = ' am';
				}

				$h12 %= 12;

				if ( $h12 == 0 && $hour )
				{
					$h12 = 12;
				}
				if ( $h12 == 0 && $hour == 0 )
				{
					$h12 = 0;
				}
			}
			else 
			{
				$h12 = $hour;
			}

			if ( $sec )
			{
				$sec = ":$sec";
			}

			return "{$h12}:{$min}{$sec}{$ampm}";
		}

		/**
		* Converts a RFC 2822 date string to a unix timestamp
		*
		* @param string $date_str RFC 2822 date string
		* @return int unix timestamp
		*/ 
		public static function convert_rfc_to_epoch($date_str)
		{
			$comma_pos = strpos($date_str,',');
			if($comma_pos)
			{
				$date_str = substr($date_str,$comma_pos+1);
			}

			// This may need to be a reference to the different months in native tongue....
			$month = array_flip(self::$month_shortnames);
			
			$dta = array();
			$ta = array();

			// Convert "15 Jul 2000 20:50:22 +0200" to unixtime
			$dta = explode(' ',$date_str);
			$ta = explode(':',$dta[4]);

			if ( substr($dta[5],0,3) != 'GMT' )
			{
				$tzoffset = substr($dta[5], 0, 1);
				$tzhours = (int) substr($dta[5], 1, 2);
				$tzmins = (int) substr($dta[5], 3, 2);
				switch ($tzoffset)
				{
					case '-':
						(int)$ta[0] += $tzhours;
						(int)$ta[1] += $tzmins;
						break;
					case '+':
						(int)$ta[0] -= $tzhours;
						(int)$ta[1] -= $tzmins;
						break;
				}
			}

			return mktime($ta[0],$ta[1],$ta[2],$month[$dta[2]],$dta[1],$dta[3]);
		}

		/**
		* Get the first day of the current week
		*
		* @param int $year the year to check
		* @param int $month the month to check
		* @param int $day the day to check
		* @return int starting weekday
		*/
		function get_weekday_start($year, $month, $day)
		{
			$weekday = self::day_of_week($year, $month, $day);
			switch($GLOBALS['phpgw_info']['user']['preferences']['calendar']['weekdaystarts'])
			{
				// Saturday is for arabic support
				case 'Saturday':
					$days = array
					(
						0 => 'Sat',
						1 => 'Sun',
						2 => 'Mon',
						3 => 'Tue',
						4 => 'Wed',
						5 => 'Thu',
						6 => 'Fri'
					);
					switch($weekday)
					{
						case 6:
							break;
						case 0:
								if ($day == 1)
								{
								 	if ($month == 1)
									{
								  		--$year;
								  		$month = 12;
								 	}
								 	else
								 	{
								 		--$month;
								 	}
								 	$day = self::days_in_month($month, $year);
								}
								else
								{
									--$day;
								}
							break;
						default:
								if ($day <= ($weekday + 1))
								{
								 	if ($month == 1)
									{
								  		--$year;
								  		$month = 12;
								 	}
								 	else
								 	{
								 		--$month;
								 	}
								 	$day = self::days_in_month($month,$year) - $weekday;
								}
								else
								{
									$day -= ($weekday + 1);
								}
					}
					break;
				case 'Monday':
					$days = array
					(
						0 => 'Mon',
						1 => 'Tue',
						2 => 'Wed',
						3 => 'Thu',
						4 => 'Fri',
						5 => 'Sat',
						6 => 'Sun'
					);
					switch($weekday)
					{
						case 1:
							break;
						case 0:
								if ($day <= 6)
								{
								 	if ($month == 1)
									{
								  		--$year;
								  		$month = 12;
								 	}
								 	else
								 	{
								 		--$month;
								 	}
								 	$day = self::days_in_month($month,$year) + ($day - 6);
								}
								else
								{
									$day -= 6;
								}
							break;
						default:
								if ($day <= ($weekday == 0) ? 6 : ($weekday-1))
								{
						 			if ($month == 1)
									{
						  				--$year;
						  				$month = 12;
						 			}
						 			else
						 			{
						 				--$month;
						 			}
						 			$day = self::days_in_month($month,$year) + ($day - (($weekday == 0) ? 6 : ($weekday-1)));
					   			}
					   			else
					  			{
									$day -= ($weekday-1);
								}
					}
					break;
				case 'Sunday':
				default:
					$days = array
					(
						0 => 'Sun',
						1 => 'Mon',
						2 => 'Tue',
						3 => 'Wed',
						4 => 'Thu',
						5 => 'Fri',
						6 => 'Sat'
					);
						if ($day <= $weekday)
						{
						 	if ($month == 1)
							{
						  		--$year;
						  		$month = 12;
						 	}
						 	else
						 	{
						 		--$month;
						 	}
						 	$day = self::days_in_month($month,$year) + ($day - $weekday);
						}
						else
						{
							$day -= $weekday;
						}
			}
			// This little hack makes sure that DST doesn't impact on date
			$sday = mktime(13, 0, 0, $month, $day, $year);
			return $sday - ( self::SECONDS_IN_HOUR * 13 );
		}

		/**
		* Check if a year is a leap year
		*
		* @param int $year the year to test
		* @param return bool is it a leap year?
		*/
		public static function is_leap_year($year)
		{
			return !!date('L', mktime(13, 0, 0, 1, 1, (int)$year));
		}

		/**
		* How many days are a given month?
		*
		* @param int $month the month to test
		* @param int $year the year to test
		* @return int the number of days in the month
		*/
		function days_in_month($month, $year)
		{
			return cal_days_in_month(CAL_GREGORIAN, (int) $month, (int) $year);
		}

		/**
		* Check if a date is valid
		*
		* @param int $year the year to check
		* @param int $month the month to check
		* @param int $day the day to check
		* @return bool is the date valid?
		*/
		public static function date_valid($year, $month, $day)
		{
			return checkdate((int)$month, (int)$day, (int)$year);
		}

		/**
		* Check if the time is valid
		*
		* @param int $hour the hour to test
		* @param int $minutes the minutes to test
		* @param int $seconds the seconds to test
		* @return bool is the time valid?
		*/
		public static function time_valid($hour, $minutes, $seconds)
		{
			$hour = (int) $hour;
			if( $hour < 0 || $hour > 24)
			{
				return false;
			}

			$minutes = (int) $minutes;
			if ( $minutes < 0 || $minutes > 59)
			{
				return false;
			}

			$seconds = (int) $seconds;
			if( $seconds < 0 || $seconds > 59)
			{
				return false;
			}

			return true;
		}

		public static function day_of_week($year,$month,$day)
		{
			return date('N', mktime(13, 0, 0, $month, $day, $year));
		}
	
		/**
		* Get the day of the year
		*
		* @param int $year the year to check
		* @param int $month the month to check
		* @param int $day the day to check
		* @return int the day of the year
		*/
		public static function day_of_year($year,$month,$day)
		{
			//date(z) starts at 0
			return date('z', mktime(13, 0, 0, $month, $day, $year)) + 1; 
		}

		/**
		* Get the number of days between two dates
		*
		* @author Steven Cramer/Ralf Becker
		* @param $m1 - Month of first date
		* @param $d1 - Day of first date
		* @param $y1 - Year of first date
		* @param $m2 - Month of second date
		* @param $d2 - Day of second date
		* @param $y2 - Year of second date
		* @return integer Date 2 minus Date 1 in days
		* @internal the last param == 0, ensures that the calculation is always done without daylight-savings
		*/
		public static function days_between($m1,$d1,$y1,$m2,$d2,$y2)
		{
			return (mktime(0, 0, 0, $m2, $d2, $y2, 0) - mktime(13, 0, 0, $m1, $d1, $y1, 0) ) / self::SECONDS_IN_DAY;
		}

		/**
		* Compare 2 dates
		*
		* @internal see http://php.net/strcmp
		* @param int $a_year the year of the first date
		* @param int $a_month the month of the first date
		* @param int $a_day the day of the first date
		* @param int $b_year the year of the second date
		* @param int $b_month the month of the second date
		* @param int $b_day the day of the second date
		* @return int comparsion result - same as php's native strcmp()
		*/ 
		public static function date_compare($a_year, $a_month, $a_day, $b_year, $b_month, $b_day)
		{
			$a_date = mktime(13, 0, 0, (int)$a_month, (int)$a_day, (int)$a_year);
			$b_date = mktime(13, 0, 0, (int)$b_month, (int)$b_day, (int)$b_year);
			if ( $a_date == $b_date )
			{
				return 0;
			}
			else if ( $a_date > $b_date )
			{
				return 1;
			}
			return -1;
		}

		/**
		* Compare 2 dates
		*
		* @internal see http://php.net/strcmp
		* @param int $a_hour the hour of the first time
		* @param int $a_minute the minutes of the first time
		* @param int $a_second the seconds of the first time
		* @param int $b_hour the hour of the second time
		* @param int $b_minute the minutes of the second time
		* @param int $b_second the seconds of the second time
		* @return int comparsion result - same as php's native strcmp()
		*/
		public static function time_compare($a_hour, $a_minute, $a_second, $b_hour, $b_minute, $b_second)
		{
			// I use the 1970/1/2 to compare the times, as the 1. can get via TZ-offest still 
			// before 1970/1/1, which is the earliest date allowed on windows
			$a_time = mktime( (int)$a_hour, (int)$a_minute, (int)$a_second, 1, 2, 1970);
			$b_time = mktime( (int)$b_hour, (int)$b_minute, (int)$b_second, 1, 2, 1970);
			if ( $a_time == $b_time )
			{
				return 0;
			}
			else if ( $a_time > $b_time )
			{
				return 1;
			}
			return -1;
		}

		/**
		* Convert a local date and time to UTC
		*
		* @param int $hour the hour to convert
		* @param int $minute the minute to convert
		* @param int $second the second to convert
		* @param int $month the month to convert
		* @param int $day the day to convert
		* @oaram int $year the year to convert
		* @return int the localtime as a UTC unix timestamp
		*/
		public static function makegmttime($hour,$minute,$second,$month,$day,$year)
		{
			return self::gmtdate(mktime($hour, $minute, $second, $month, $day, $year));
		}

		/**
		* Convert a unix timestamp to an array of date information
		*
		* @param int $localtime the current user's local time as a unix timestamp
		* @return array date information - keys 'raw', 'day', 'month', 'year', 'full', 'dow', 'dm' & 'bd'
		*/
		public static function localdates($localtime)
		{
			$date = Array('raw', 'day', 'month', 'year', 'full', 'dow', 'dm', 'bd');
			$date['raw'] = $localtime;
			$date['year'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'Y');
			$date['month'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'m');
			$date['day'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'d');
			$date['full'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'Ymd');
			$date['bd'] = mktime(13, 0, 0, $date['month'], $date['day'], $date['year']);
			$date['dm'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'dm');
			$date['dow'] = self::day_of_week($date['year'],$date['month'],$date['day']);
			$date['hour'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'H');
			$date['minute'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'i');
			$date['second'] = (int) $GLOBALS['phpgw']->common->show_date($date['raw'],'s');
		
			return $date;
		}

		/**
		* Convert user's current local time to a UTC unix timestamp
		*
		* @param int $locatime the user's local time as a unix timestamp
		* @return int UTC unix timestamp
		*/
		public static function gmtdate($localtime)
		{
			return self::localdates($localtime - self::user_timezone());
		}

		/**
		 * Convert a date from one format to another
		 * 
		 * @param string $date Date in source format representation
		 * @param string $formatSource Format of the passed date
		 * @param string $formatTarget Target date format
		 * @return string Date in target format representation
		 */
		public static function convertDate($date, $formatSource, $formatTarget)
		{
			// get format separator character
			$formatSourceSepChar = substr($formatSource,1,1);
			$formatSourceArray   = explode($formatSourceSepChar, $formatSource);
			$dateSourceArray     = explode($formatSourceSepChar, $date);

			$keyNum = count($formatSourceArray);
			$valNum = count($dateSourceArray);
			if($keyNum != $valNum)
			{
				return false;
			}

			$map_date = array();
			for($i=0; $i<$keyNum; $i++)
			{
				$key = $formatSourceArray[$i];
				$val = $dateSourceArray[$i];

				if($key == 'M')
				{
					$map_date['m'] = self::convert_m_to_int($val);
				}
				else
				{
					$map_date[strtolower($key)] = (int) $val;
				}
			}
			return date($formatTarget, mktime(0, 0, 0, $map_date['m'], $map_date['d'], $map_date['y']));
		}

		/**
		* Convert a date string to an array containing date parts
		*
		* @param string $datestr the date string to convert - must match user's preferred date format
		* @return array date parts: year,month and day
		*/
		public static function date_array($datestr)
		{
			$dateformat =& $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$pattern = '/[\.\/\-]/';
			$fields = preg_split($pattern, $datestr);
			foreach(preg_split($pattern, $dateformat) as $n => $field)
			{
				$date[$field] = (int) $fields[$n];

				if ( $field == 'M' )
				{
					$date['m'] = self::convert_M_to_int($fields[$n]);
				}
			}

			return array
			(
				'year'  => $date['Y'],
				'month' => $date['m'],
				'day'   => $date['d']
			);
		}

		/**
		* Convert a date array to a unix timestamp
		*
		* @param string $date the date to convert, must contain keys day, month & year
		* @return int unix timestamp
		*/
		public static function date_to_timestamp($datestr = '')
		{
			if ( !$datestr )
			{
				return 0;
			}

			$hour	= 13;
			$minute	= 0;
			$second	= 0;

			if( strpos($datestr, ':') )
			{
				$date_part = explode(' ', $datestr);
				$time_part = explode(':', $date_part[1]);

				$hour	= (int) $time_part[0];
				$minute	= (int) $time_part[1];
				$second	= isset($time_part[2]) && $time_part[2] ? (int)$time_part[2] : 0;
			}


			if( version_compare(PHP_VERSION, '5.3.0') >= 0  && strpos($datestr, ':'))
			{
				return self::datetime_to_timestamp($datestr);
			}

			$date_array	= self::date_array($datestr);
			return mktime ($hour, $minute, $second, $date_array['month'], $date_array['day'], $date_array['year']);
		}


		/**
		* Convert a datetime to a unix timestamp
		*
		* @param string $date the date convert
		* @return int unix timestamp
		*/
		public static function datetime_to_timestamp($datestr = '')
		{
			if ( !$datestr )
			{
				return 0;
			}

			$format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			if(substr_count($datestr, ':') == 1 )
			{
				$format .= ' H:i';
			}
			else if(substr_count($datestr, ':') == 2 )
			{
				$format .= ' H:i:s';
			}
			
			$date = DateTime::createFromFormat("{$format}", $datestr);
			if($date)
			{
				return $date->getTimestamp();
			}
			else
			{
				return 0;
			}
		}

		/**
		* Convert a M month string to an int
		*
		* @param string $str abbreviated month name string
		* @return int the month number - 0 is returned for invalid input
		*/
		private static function convert_M_to_int($str)
		{
			for($i=1; $i <=12; ++$i)
			{
				if ( date('M', mktime(0, 0, 0, $i, 1, 2000)) == $str )
				{
					return $i;
				}
			}
			return 0;
		}

		/**
		* Get a list of translated day names
		*
		* @return array list of day names
		*/
		public static function get_dow_fullnames()
		{
			static $dow_list = null;
			if ( is_null($dow_list) )
			{
				$dow_list = array();
				foreach ( self::$dow_fullnames as $id => $dow_name )
				{
					$dow_list[$id] = lang($dow_name);
				}
			}
			return $dow_list;
		}

		/**
		* Get a list of translated month names
		*
		* @return array list of month names
		*/
		public static function get_month_fullnames()
		{
			static $month_list = null;
			if ( is_null($month_list) )
			{
				$raw_list =  self::$month_fullnames;
				unset($raw_list[0]); // WAR month index hack
				
				$month_list = array();
				foreach ( $raw_list as $id => $month )
				{
					if ( $id == 0 )
					{
						continue;
					}
					$month_list[$id] = lang($month);
				}
			}
			return $month_list;
		}

		/**
		* Convert an ISO 8601 day of week number to a local name
		*
		* @param int $dow ISO 8601 day of week number
		* @return string local say of week name
		*/
		public static function nr2weekday($dow = 0)
		{
			$dow_list = self::get_dow_fullnames();
			if ( isset($dow_list[$dow]) )
			{
				return $dow_list[$dow];
			}
			return lang('Unknown');
		}
	}
