<?php

  class date_converter
  {
  
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

			$hour	= 0;
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
  
?>
