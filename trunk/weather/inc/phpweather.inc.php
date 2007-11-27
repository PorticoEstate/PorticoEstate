<?php
/*
$Id: phpweather.inc.php 8454 2001-12-03 18:01:51Z milosch $ 
Copyright (c) 2000, Martin Geisler <gimpster@gimpster.com>.
Licensed under the GPL, see the file COPYING.
Also see http://www.gimpster.com for updates and further instructions
on how to use PHP Weather.
*/

  /****************************************************************************
   * the metar functions in this file are courtesy of Martin Geisler
   * they come from his phpweather application.
   * Contributors to this code are in the AUTHORS.phpweather file.
   * Initial porting effort to phpgroupware has been performed by
   * Sam Wynn (neotexan@wynnsite.com).  Also, included is a number
   * of other "weather center" functions.
   ***************************************************************************/

/*
 * Various convenience functions
 */

	function store_temp($temp,&$destination,$temp_cname,$temp_fname)
	{
		/*
		* Given a numerical temperature $temp in Celsius, coded to tenth of
		* degree, store in $destination[$temp_cname], convert to Fahrenheit
		* and store in $decoded_metar[$temp_fname]
		* Note: &$destination is call by reference
		* Note: $temp is converted to negative if $temp > 100.0 (See
		* Federal Meteorological Handbook for groups T, 1, 2 and 4) For
		* example, a temperature of 2.6°C and dew point of -1.5°C would be
		* reported in the body of the report as
		* "03/M01" and the TsnT'T'T'snT'dT'dT'd group as "T00261015").
		*/

		/* Temperature measured in Celsius, coded to tenth of degree */
		$temp = number_format($temp/10, 1);
		if ($temp >100.0)
		{
			/* first digit = 1 means minus temperature */
			$temp = -($temp - 100.0);
		}
		$destination[$temp_cname] = $temp;
		/* The temperature in Fahrenheit. */
		$destination[$temp_fname] = number_format($temp * (9/5) + 32, 1);
	}

	function pretty_print_precip($precip_mm, $precip_in)
	{
		global $strings;
		/* 
		* Returns amount if $precip_mm > 0, otherwise "trace" (see Federal
		* Meteorological Handbook No. 1 for code groups P, 6 and 7) used in
		* several places, so standardized in one function.
		*/
		if ($precip_mm>0)
		{
			$amount = sprintf($strings['mm_inches'], $precip_mm, $precip_in);
		}
		else
		{
			$amount = $strings['a_trace'];
		}
		return sprintf($strings['precip_there_was'], $amount);
	}

	function store_speed($value, $windunit, &$meterspersec, &$knots, &$milesperhour) {
		/*
		* Helper function to convert and store speed based on unit.
		* &$meterspersec, &$knots and &$milesperhour are passed on
		* reference
		*/
		if ($windunit == 'KT')
		{
			/* The windspeed measured in knots: */
			$knots = number_format($value);
			/* The windspeed measured in meters per second, rounded to one decimal place: */
			$meterspersec = number_format($value * 0.51444, 1);
			/* The windspeed measured in miles per hour, rounded to one decimal place: */
			$milesperhour = number_format($value * 1.1507695060844667, 1);
		}
		elseif ($windunit == 'MPS')
		{
			/* The windspeed measured in meters per second: */
			$meterspersec = number_format($value);
			/* The windspeed measured in knots, rounded to one decimal place: */
			$knots = number_format($value / 0.51444, 1);
			/* The windspeed measured in miles per hour, rounded to one decimal place: */
			$milesperhour = number_format($value / 0.51444 * 1.1507695060844667, 1);
		}
		elseif ($windunit == 'KMH')
		{
			/* The windspeed measured in kilometers per hour: */
			$meterspersec = number_format($value * 1000 / 3600, 1);
			$knots = number_format($value * 1000 / 3600 / 0.51444, 1);
			/* The windspeed measured in miles per hour, rounded to one decimal place: */
			$milesperhour = number_format($knots * 1.1507695060844667, 1);
		}
	}

	function pretty_print_metar($metar)
	{
		global $strings;
		/*
		* The main pretty-print function.
		* You should pass a metar. That produces something like this:
		*
		*   14 minutes ago, at 12:20 UTC, the wind was blowing at a speed
		*   of 4.6 meters per second (10.4 miles per hour) from the West.
		*   The temperature was 15 degrees Celsius (59 degrees Fahrenheit),
		*   and the pressure was 1,018 hPa (30.06
		*   inHg). The relative humidity was 47.7%. The clouds were few at
		*   a height of 1067 meter (3500 feet) and scattered at a height of
		*   6096 meter (20000 feet). The visibility was >11.3 kilometers
		*   (>7 miles).
		*
		* Neat isn't it? :-)
		*/

		if (!$metar)
		{
			/* We don't want to display all sorts of silly things if the metar
			is empty. */
			return $strings['no_data'];
		}


		$data = process_metar($metar);
		extract($data);

		$minutes_old = round((time() - $time)/60);

		/* system time w/ offset, current zone, and offset applied */
		$gmtime = $GLOBALS['phpgw']->common->show_date($time,"h:i A ");
		$gmtzone = strftime("%Z");
		if ($GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"] > 0)
		{
			$gmtoffset = "+".$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"];
		}
		if ($GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"] < 0)
		{
			$gmtoffset = $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"];
		}

		/* Cloudlayers. */
		if($cloud_layer1_condition == 'CAVOK')
		{
			$sky_str = $strings['sky_cavok'];
		}
		elseif (isset($cloud_layer1_altitude_ft))
		{
			$sky_str = sprintf($strings['sky_str_format1'], $cloud_layer1_condition, $cloud_layer1_altitude_m, $cloud_layer1_altitude_ft);
			if(isset($cloud_layer2_altitude_ft))
			{
				if(isset($cloud_layer3_altitude_ft))
				{
					$sky_str .= sprintf($strings['sky_str_format2'], $cloud_layer2_condition, $cloud_layer2_altitude_m, $cloud_layer2_altitude_ft, $cloud_layer3_condition, $cloud_layer3_altitude_m, $cloud_layer3_altitude_ft);
				}
				else
				{
					$sky_str .= sprintf($strings['sky_str_format3'], $cloud_layer2_condition, $cloud_layer2_altitude_m, $cloud_layer2_altitude_ft);
				}
			}
		}
		else
		{
			$sky_str = $strings['sky_str_clear'];
		}
		$sky_str .= ".";

		/* Visibility. */
		if(isset($visibility_miles))
		{
			$visibility = sprintf($strings['visibility_format'], $visibility_km, $visibility_miles);
		}

		/* Wind. */
		if (isset($wind_meters_per_second) && $wind_meters_per_second > 0)
		{
			$wind_str = sprintf($strings['wind_str_format1'], $wind_meters_per_second, $wind_miles_per_hour);
			if (isset($wind_gust_meters_per_second) && $wind_gust_meters_per_second > 0)
			{
				$wind_str .= sprintf($strings['wind_str_format2'], $wind_gust_meters_per_second, $wind_gust_miles_per_hour);
			}
			$wind_str .= sprintf($strings['wind_str_format3'], $wind_dir_text);
		}
		else
		{
			$wind_str = $strings['wind_str_calm'];
		}

		/* Precipitation. */
		$prec_str = "";
		if (isset($precip_in))
		{
			$prec_str .= pretty_print_precip($precip_mm, $precip_in) . $strings['precip_last_hour'];
		}
		if (isset($precip_6h_in))
		{
			$prec_str .= pretty_print_precip($precip_6h_mm,$precip_6h_in) . $strings['precip_last_6_hours'];
		}
		if (isset($precip_24h_in))
		{
			$prec_str .= pretty_print_precip($precip_24h_mm,$precip_24h_in) . $strings['precip_last_24_hours'];
		}
		if (isset($snow_in))
		{
			$prec_str .= sprintf($strings['precip_snow'], $snow_mm, $snow_in);
		}

		/* Min and max temperatures. */
		$temp_str = "";
		if (isset($temp_max6h_c) && isset($temp_min6h_c))
		{
			$temp_str .= sprintf($strings['temp_min_max_6_hours'], $temp_max6h_c, $temp_min6h_c, $temp_max6h_f, $temp_min6h_f);
		}
		else
		{
			if (isset($temp_max6h_c))
			{
				$temp_str .= sprintf($strings['temp_max_6_hours'], $temp_max6h_c, $temp_max6h_f);
			}
			if (isset($temp_min6h_c))
			{
				$temp_str .= sprintf($strings['temp_max_6_hours'], $temp_min6h_c, $temp_min6h_f);
			}
		}
		if (isset($temp_max24h_c))
		{
			$temp_str .= sprintf($strings['temp_min_max_24_hours'], $temp_max24h_c, $temp_min24h_c, $temp_max24h_f, $temp_min24h_f);
		}

		/* Runway information. */
		if (isset($runway_vis_meter))
		{
			$runway_str = sprintf($strings['runway_vis'], $runway_nr, $runway_vis_meter, $runway_vis_ft);
		}

		if (isset($runway_vis_min_meter))
		{
			$runway_str = sprintf($strings['runway_vis_min_max'], $runway_nr, $runway_vis_min_meter, $runway_vis_min_ft, $runway_vis_max_meter, $runway_vis_max_ft);
		}

		/* Current weather. */
		if (isset($weather))
		{
			$weather_str = sprintf($strings['current_weather'], $weather);
		}
		else
		{
			$weather_str = '';
		}

		$pretty_metar = sprintf($strings['pretty_print_metar'], $minutes_old, $gmtime ." ($gmtzone$gmtoffset)", $wind_str, $temp_c, $temp_f, $altimeter_hpa, $altimeter_inhg, $rel_humidity, $sky_str, $visibility, $runway_str, $weather_str, $prec_str, $temp_str);

		return $pretty_metar;
	}

	function pretty_print_metar_wap($metar, $location)
	{
		/*
		* The wap pretty-print function.
		* You should pass a metar and a location, eg. 'Aalborg,
		* Denmark'. That produces something like this:
		*
		*/

		$data = process_metar($metar);
		extract($data);
		$minutes_old = round((time() - $time)/60);
		echo "
		<p>
		$location, $minutes_old min ago<br/>
		Wind: $wind_meters_per_second mps $wind_dir_text_short<br/>
		Temp: $temp_c C<br/>
		Clouds: $cloud_layer1_coverage
		</p>
		";
	}

	function get_metar($station) 
	{
		/*
		* Looks in the database, and fetches a new metar is nesceary. You
		* should pass a ICAO station identifier, eg. 'EKYT' for Aalborg,
		* Denmark.  
		*/
		switch ($GLOBALS['phpgw_info']['server']['db_type'])
		{
			case 'mysql':
				$tstamp = 'UNIX_TIMESTAMP(metar_timestamp)';
				break;
			default:
				$tstamp = 'metar_timestamp';
				break;
		}

		$GLOBALS['phpgw']->db->query("SELECT metar_weather, $tstamp "
			."from phpgw_weather_metar where metar_station='$station'");
		if ($GLOBALS['phpgw']->db->next_record())
		{
			if ($GLOBALS['phpgw']->db->f("metar_weather"))
			{
				 switch ($GLOBALS['phpgw_info']['server']['db_type'])
				 {
					case 'mysql':
					 	$metar_timestamp = $GLOBALS['phpgw']->db->f('metar_timestamp');
						break;
					default:
						$metar_timestamp = time($GLOBALS['phpgw']->db->f('metar_timestamp'));
						break;
				}
				if ($metar_timestamp > time() -3600 - date('Z'))
				{
					/* We found the station, and the data is less than 1 hour old. */
					return $GLOBALS['phpgw']->db->f("metar_weather");
				}
				else
				{
					/* The data is old, we fetch a new METAR */
					return fetch_metar($station, 0);
				}
			}
			else
			{
				/* The empty data, we fetch a new METAR */
				return fetch_metar($station, 0);
			}
		}
		else
		{
			/* The station is new - we fetch a new METAR */
			return fetch_metar($station, 1);
		}
	}

	function fetch_metar($station, $new) 
	{
		/*
		* Fetches a new metar from weather.noaa.gov. If the $new variable
		* is true, the metar is inserted, else it will replace the old
		* metar.
		*/

		$metar = "";
		/* Retrieves the METAR from weather.noaa.gov and insert the data
		* into the database. If $new is true, insert the metar, else just
		* update. Returns the METAR.
		*/
		$station = strtoupper($station);

		/* We use the @file notation, because it might fail. */
		$file  = $GLOBALS['phpgw']->network->gethttpsocketfile("http://weather.noaa.gov/pub/data/observations/metar/stations/$station.TXT");
		/* Here we test to see if we actually got a meter. */
		if (is_array($file))
		{
			/************************************************************************
			* network class comes with http crap at head of file...remove it
			***********************************************************************/
			for ($loop = 0; $loop < 8; $loop++)
			{
				list($i, $line) = each($file);
			}

			list($i, $date) = each($file);
			$date = trim($date);
			while (list($i, $line) = each($file))
			{
				$metar .= ' ' . trim($line);
			}

			$metar = str_replace('  ', ' ', $metar);

			/* The date is in the form 2000/10/09 14:50 UTC. This seperates
			the different parts. */
			$date_parts = explode(':', strtr($date, '/ ', '::'));
			$date_unixtime = gmmktime($date_parts[3], $date_parts[4], 0, $date_parts[1], $date_parts[2], $date_parts[0]);

			/* It might seam strange, that we make a local date, but MySQL
			expects a local when we insert the METAR. The same applies for
			the other $date = date('Y/m/d H:i') statements. */
			$date = date('Y/m/d H:i', $date_unixtime);

			if (!ereg('[0-9]{6}Z', $metar))
			{
				/* Some reports dont even have a time-part, so we insert the
				* current time. This might not be the time of the report, but
				* it was broken anyway :-) */
				$metar = gmdate('dHi', $date_unixtime) . 'Z ' . $metar;
			}

			if (ereg('orbidden', $metar))
			{
				/* metar didn't make it through a proxy */
				$metar = '';
			}

			if ($date_unixtime < (time() - 3300))
			{
				/* The timestamp in the metar is more than 55 minutes old.  We
				adjust the timestamp, so that we won't try to fetch a new
				METAR within the next 5 minutes. After 5 minutes, the
				timestamp will again be more than 1 hour old. */
				$date = date('Y/m/d H:i', time() - 3300);
			}

		}
		else
		{
			/* If we end up here, it means that there was no file, we then set
			the metar to and empty string and. We set the date to time() -
			3000 to give the server 10 minutes of peace. If the file is
			unavailable, we don't want to stress the server. */
			$metar = '';
			$date = date('Y/m/d H:i', time() - 3000);
		}

		 switch ($GLOBALS['phpgw_info']['server']['db_type'])
		 {
			case 'mysql':
			 	$date = $date;
				break;
			default:
				$date = ereg_replace('/','-',$date);
				break;
		}

		if ($new)
		{
			/* Insert the new record */
			$query = "INSERT INTO phpgw_weather_metar SET metar_station = '$station', "
			."metar_weather = '$metar', metar_timestamp = '$date'";
		}
		else
		{
			/* Update the old record */
			$query = "UPDATE phpgw_weather_metar SET metar_weather = '$metar', "
			."metar_timestamp = '$date' WHERE metar_station = '$station'";
		}
		$GLOBALS['phpgw']->db->query($query);

		return $metar;
	}

	function process_metar($metar)
	{
		/* initialization */
		global $strings, $wind_dir_text_short_array, $wind_dir_text_array, $cloud_condition_array, $weather_array;
		$decoded_metar['temp_visibility_miles'] = "";
		$cloud_layers = 0;
		$decoded_metar['remarks'] = "";

		$cloud_coverage = array(
			'SKC' => '0',
			'CLR' => '0',
			'VV'  => '8/8',
			'FEW' => '1/8 - 2/8',
			'SCT' => '3/8 - 4/8',
			'BKN' => '5/8 - 7/8',
			'OVC' => '8/8'
		);

		$decoded_metar['metar'] = $metar;
		$parts = explode(' ', $metar);
		$num_parts = count($parts);
		for ($i = 0; $i < $num_parts; $i++)
		{
			$part = $parts[$i];

			if (ereg('RMK|TEMPO|BECMG', $part))
			{
				/* The rest of the METAR is either a remark or temporary
				information. We skip the rest of the METAR. */
				$decoded_metar['remarks'] .= ' ' . $part;
				break;
			}
			elseif ($part == 'METAR')
			{
				/*
				* Type of Report: METAR
				*/
				$decoded_metar['type'] = 'METAR';
			}
			elseif ($part == 'SPECI')
			{
				/*
				* Type of Report: SPECI
				*/
				$decoded_metar['type'] = 'SPECI';
			}
			elseif (ereg('^[A-Z]{4}$', $part) && ! isset($decoded_metar['station']))
			{
				/*
				* Station Identifier
				*/
				$decoded_metar['station'] = $part;
			}
			elseif (ereg('([0-9]{2})([0-9]{2})([0-9]{2})Z', $part, $regs))
			{
				/*
				* Date and Time of Report
				* We return a standard Unix UTC/GMT timestamp suitable for
				* gmdate()
				* There has been a report about the time beeing wrong. If you
				* experience this, then change the next line. You should
				* add/subtract some hours to $regs[2], e.g. if all your times
				* are 960 minutes off (16 hours) then add 16 to $regs[2].
				*/
				$decoded_metar['time'] = gmmktime($regs[2], $regs[3], 0, gmdate('m'), $regs[1], gmdate('Y'));
			}
			elseif (ereg('(AUTO|COR|RTD|CC[A-Z]|RR[A-Z])', $part, $regs))
			{
				/*
				* Report Modifier: AUTO, COR, CCx or RRx
				*/
				$decoded_metar['report_mod'] = $regs[1];
			}
			elseif (ereg('([0-9]{3}|VRB)([0-9]{2,3}).*(KT|MPS|KMH)', $part, $regs))
			{
				/* Wind Group */
				$windunit = $regs[3];  /* do ereg in two parts to retrieve unit first */
				/* now do ereg to get the actual values */
				ereg("([0-9]{3}|VRB)([0-9]{2,3})(G([0-9]{2,3})?$windunit)", $part, $regs);
				if ($regs[1] == 'VRB')
				{
					$decoded_metar['wind_deg'] = $strings['wind_vrb_long'];
					$decoded_metar['wind_dir_text'] = $strings['wind_vrb_long'];
					$decoded_metar['wind_dir_text_short'] = $strings['wind_vrb_short'];
				}
				else
				{
					$decoded_metar['wind_deg'] = $regs[1];
					$decoded_metar['wind_dir_text'] = $wind_dir_text_array[round($regs[1]/22.5)];
					$decoded_metar['wind_dir_text_short'] = $wind_dir_text_short_array[round($regs[1]/22.5)];
				}
				store_speed($regs[2],
					$windunit,
					$decoded_metar['wind_meters_per_second'],
					$decoded_metar['wind_knots'],
					$decoded_metar['wind_miles_per_hour']
				);

				if (isset($regs[4]))
				{
					/* We have a report with information about the gust.
					First we have the gust measured in knots: */
					store_speed($regs[4],$windunit,
						$decoded_metar['wind_gust_meters_per_second'],
						$decoded_metar['wind_gust_knots'],
						$decoded_metar['wind_gust_miles_per_hour']
					);
				}
			}
			elseif (ereg('^([0-9]{3})V([0-9]{3})$', $part, $regs))
			{
				/*
				* Variable wind-direction
				*/
				$decoded_metar['wind_var_beg'] = $regs[1];
				$decoded_metar['wind_var_end'] = $regs[2];
			}
			elseif ($part == 9999)
			{
				/* A strange value. When you look at other pages you see it
				interpreted like this (where I use > to signify 'Greater
				than'): */
				$decoded_metar['visibility_miles'] = '>7';
				$decoded_metar['visibility_km']    = '>11.3';
			}
			elseif(ereg('^([0-9]{4})$', $part, $regs))
			{
				/* 
				* Visibility in meters (4 digits only)
				*/
				/* The visibility measured in kilometers, rounded to one decimal place. */
				$decoded_metar['visibility_km'] = number_format($regs[1]/1000, 1);
				/* The visibility measured in miles, rounded to one decimal place. */
				$decoded_metar['visibility_miles'] = number_format( ($regs[1]/1000) / 1.609344, 1);
			}
			elseif (ereg('^[0-9]$', $part))
			{
				/*
				* Temp Visibility Group, single digit followed by space
				*/
				$decoded_metar['temp_visibility_miles'] = $part;
			}
			elseif (ereg('^M?(([0-9]?)[ ]?([0-9])(/?)([0-9]*))SM$', $decoded_metar['temp_visibility_miles'].' '.$parts[$i], $regs))
			{
				/*
				* Visibility Group
				*/
				if ($regs[4] == '/')
				{
					$vis_miles = $regs[2] + $regs[3]/$regs[5];
				}
				else
				{
					$vis_miles = $regs[1];
				}
				if ($regs[0][0] == 'M')
				{
					/* The visibility measured in miles, prefixed with < to indicate 'Less than' */
					$decoded_metar['visibility_miles'] = '<' . number_format($vis_miles, 1);
					/* The visibility measured in kilometers. The value is rounded
					to one decimal place, prefixed with < to indicate 'Less than' */
					$decoded_metar['visibility_km']    = '<' . number_format($vis_miles * 1.609344, 1);
				}
				else
				{
					/* The visibility measured in mile.s */
					$decoded_metar['visibility_miles'] = number_format($vis_miles, 1);
					/* The visibility measured in kilometers, rounded to one decimal place. */
					$decoded_metar['visibility_km']    = number_format($vis_miles * 1.609344, 1);
				}
			}
			elseif ($part == 'CAVOK')
			{
				/* CAVOK: Used when the visibility is greather than 10
				kilometers, the lowest cloud-base is at 5000 feet and there
				is no significant weather. */
				$decoded_metar['visibility_km']    = '>10';
				$decoded_metar['visibility_miles'] = '>6.2';
				$decoded_metar['cloud_layer1_condition'] = 'CAVOK';
			}
			elseif (ereg('^R([0-9][0-9][RLC]?)/([MP]?[0-9]{4})V?(P?[0-9]{4})?F?T?$', $part, $regs))
			{
				$decoded_metar['runway_nr'] = $regs[1];
				if ($regs[3])
				{
					/* We have both min and max visibility. */
					$prefix = '';
					if ($regs[2][0] == 'M')
					{
						/* Less than. */
						$prefix = '<';
						$regs[2] = substr($regs[2], 1);
					}
					$decoded_metar['runway_vis_min_ft']    = $prefix . number_format($regs[2]);
					$decoded_metar['runway_vis_min_meter'] = $prefix . number_format($regs[2] * 0.3048);

					$prefix = '';
					if ($regs[3][0] == 'P')
					{
						/* Greather than. */
						$prefix = '>';
						$regs[3] = substr($regs[3], 1);
					}
					$decoded_metar['runway_vis_max_ft']    = $prefix . number_format($regs[3]);
					$decoded_metar['runway_vis_max_meter'] = $prefix . number_format($regs[3] * 0.3048);

				}
				else
				{
					/* We only have a single visibility. */
					$prefix = '';
					if ($regs[2][0] == 'M')
					{
						$prefix = '<';
						$regs[2] = substr($regs[2], 1);
					}
					elseif ($regs[2][0] == 'P')
					{
						$prefix = '>';
						$regs[2] = substr($regs[2], 1);
					}
					$decoded_metar['runway_vis_ft']    = $prefix . number_format($regs[2]);
					$decoded_metar['runway_vis_meter'] = $prefix . number_format($regs[2] * 0.3048);
				}
			}
			elseif (ereg('^(-|\+|VC)?(TS|SH|FZ|BL|DR|MI|BC|PR|RA|DZ|SN|SG|GR|GS|PE|IC|UP|BR|FG|FU|VA|DU|SA|HZ|PY|PO|SQ|FC|SS|DS)+$', $part))
			{
				/*
				* Current weather-group
				*/ 
				if ($part[0] == '-')
				{
					/* A light phenomenon */
					$decoded_metar['weather'] .= $strings['light'];
					$part = substr($part, 1);
				}
				elseif ($part[0] == '+')
				{
					/* A heavy phenomenon */
					$decoded_metar['weather'] .= $strings['heavy'];
					$part = substr($part, 1);
				}
				elseif ($part[0].$part[1] == 'VC')
				{
					/* Proximity Qualifier */
					$decoded_metar['weather'] .= $strings['nearby'];
					$part = substr($part, 2);
				}
				else
				{
					/* no intensity code => moderate phenomenon */
					$decoded_metar['weather'] .= $strings['moderate'];
				}

				while ($bite = substr($part, 0, 2))
				{
					/* Now we take the first two letters and determine what they
					mean. We append this to the variable so that we gradually
					build up a phrase. */
					$decoded_metar['weather'] .= $weather_array[$bite];
					/* Here we chop off the two first letters, so that we can take
					a new bite at top of the while-loop. */
					$part = substr($part, 2);
				}
			}
			elseif (ereg('(SKC|CLR)', $part, $regs))
			{
				/*
				* Cloud-layer-group.
				* There can be up to three of these groups, so we store them as
				* cloud_layer1, cloud_layer2 and cloud_layer3.
				*/
				$cloud_layers++;
				/* Again we have to translate the code-characters to a
				meaningful string. */
				$decoded_metar['cloud_layer'. $cloud_layers.'_condition']  = $cloud_condition_array[$regs[1]];
				$decoded_metar['cloud_layer'.$cloud_layers.'_coverage']    = $cloud_coverage_array[$regs[1]];
			}
			elseif (ereg('^(VV|FEW|SCT|BKN|OVC)([0-9]{3})(CB|TCU)?$', $part, $regs))
			{
				/* We have found (another) a cloud-layer-group. There can be up
				to three of these groups, so we store them as cloud_layer1,
				cloud_layer2 and cloud_layer3. */
				$cloud_layers++;
				/* Again we have to translate the code-characters to a meaningful string. */
				if ($regs[1] == 'OVC')
				{
					$clouds_str_temp = '';
				}
				else
				{
					$clouds_str_temp = $strings['clouds'];
				}
				if ($regs[3] == 'CB')
				{
					/* cumulonimbus (CB) clouds were observed. */
					$decoded_metar['cloud_layer'.$cloud_layers.'_condition'] =
					$cloud_condition_array[$regs[1]] . $strings['clouds_cb'];
				}
				elseif ($regs[3] == 'TCU')
				{
					/* towering cumulus (TCU) clouds were observed. */
					$decoded_metar['cloud_layer'.$cloud_layers.'_condition'] =
					$cloud_condition_array[$regs[1]] . $strings['clouds_tcu'];
				}
				else
				{
					$decoded_metar['cloud_layer'.$cloud_layers.'_condition'] =
					$cloud_condition_array[$regs[1]] . $clouds_str_temp;
				}
				$decoded_metar['cloud_layer'.$cloud_layers.'_coverage']    = $cloud_coverage[$regs[1]];
				$decoded_metar['cloud_layer'.$cloud_layers.'_altitude_ft'] = $regs[2] *100;
				$decoded_metar['cloud_layer'.$cloud_layers.'_altitude_m']  = round($regs[2] * 30.48);
			}
			elseif (ereg('^(M?[0-9]{2})/(M?[0-9]{2})?$', $part, $regs))
			{
				/*
				* Temperature/Dew Point Group
				* The temperature and dew-point measured in Celsius.
				*/
				$decoded_metar['temp_c'] = number_format(strtr($regs[1], 'M', '-'));
				$decoded_metar['dew_c']  = number_format(strtr($regs[2], 'M', '-'));
				/* The temperature and dew-point measured in Fahrenheit, rounded to the nearest degree. */
				$decoded_metar['temp_f'] = round(strtr($regs[1], 'M', '-') * (9/5) + 32);
				$decoded_metar['dew_f']  = round(strtr($regs[2], 'M', '-') * (9/5) + 32);
			}
			elseif(ereg('A([0-9]{4})', $part, $regs))
			{
				/*
				* Altimeter
				* The pressure measured in inHg
				*/
				$decoded_metar['altimeter_inhg'] = number_format($regs[1]/100, 2);
				/* The pressure measured in mmHg, hPa and atm */
				$decoded_metar['altimeter_mmhg'] = number_format($regs[1] * 0.254, 1);
				$decoded_metar['altimeter_hpa']  = number_format($regs[1] * 0.33863881578947);
				$decoded_metar['altimeter_atm']  = number_format($regs[1] * 3.3421052631579e-4, 3);
			}
			elseif(ereg('Q([0-9]{4})', $part, $regs))
			{
				/*
				* Altimeter
				* This is strange, the specification doesnt say anything about
				* the Qxxxx-form, but it's in the METARs.
				*/
				/* The pressure measured in hPa */
				$decoded_metar['altimeter_hpa']  = number_format($regs[1]);
				/* The pressure measured in mmHg, inHg and atm */
				$decoded_metar['altimeter_mmhg'] = number_format($regs[1] * 0.7500616827, 1);
				$decoded_metar['altimeter_inhg'] = number_format($regs[1] * 0.0295299875, 2);
				$decoded_metar['altimeter_atm']  = number_format($regs[1] * 9.869232667e-4, 3);
			}
			elseif (ereg('^T([0-9]{4})([0-9]{4})', $part, $regs))
			{
				/*
				* Temperature/Dew Point Group, coded to tenth of degree.
				* The temperature and dew-point measured in Celsius.
				*/
				store_temp($regs[1],$decoded_metar,'temp_c','temp_f');
				store_temp($regs[2],$decoded_metar,'dew_c','dew_f');
			}
			elseif (ereg('^T([0-9]{4}$)', $part, $regs))
			{
				store_temp($regs[1],$decoded_metar,'temp_c','temp_f');
			}
			elseif (ereg('^1([0-9]{4}$)', $part, $regs))
			{
				/*
				* 6 hour maximum temperature Celsius, coded to tenth of degree
				*/
				store_temp($regs[1],$decoded_metar,'temp_max6h_c','temp_max6h_f');
			}
			elseif (ereg('^2([0-9]{4}$)', $part, $regs))
			{
				/*
				* 6 hour minimum temperature Celsius, coded to tenth of degree
				*/
				store_temp($regs[1],$decoded_metar,'temp_min6h_c','temp_min6h_f');
			}
			elseif (ereg('^4([0-9]{4})([0-9]{4})$', $part, $regs))
			{
				/*
				* 24 hour maximum and minimum temperature Celsius, coded to
				* tenth of degree
				*/
				store_temp($regs[1],$decoded_metar,'temp_max24h_c','temp_max24h_f');
				store_temp($regs[2],$decoded_metar,'temp_min24h_c','temp_min24h_f');
			}
			elseif(ereg('^P([0-9]{4})', $part, $regs))
			{
				/*
				* Precipitation during last hour in hundredths of an inch
				* (store as inches)
				*/
				$decoded_metar['precip_in'] = number_format($regs[1]/100, 2);
				$decoded_metar['precip_mm'] = number_format($regs[1]*0.254, 2);
			}
			elseif(ereg('^6([0-9]{4})', $part, $regs))
			{
				/*
				* Precipitation during last 3 or 6 hours in hundredths of an
				* inch  (store as inches)
				*/
				$decoded_metar['precip_6h_in'] = number_format($regs[1]/100, 2);
				$decoded_metar['precip_6h_mm'] = number_format($regs[1]*0.254, 2);
			}
			elseif(ereg('^7([0-9]{4})', $part, $regs))
			{
				/*
				* Precipitation during last 24 hours in hundredths of an inch
				* (store as inches)
				*/
				$decoded_metar['precip_24h_in'] = number_format($regs[1]/100, 2);
				$decoded_metar['precip_24h_mm'] = number_format($regs[1]*0.254, 2);
			}
			elseif(ereg('^4/([0-9]{3})', $part, $regs))
			{
				/*
				* Snow depth in inches
				*/
				$decoded_metar['snow_in'] = number_format($regs[1]);
				$decoded_metar['snow_mm'] = number_format($regs[1] * 25.4);
			}
			else
			{
				/*
				* If we couldn't match the group, we assume that it was a
				* remark.
				*/
				$decoded_metar['remarks'] .= ' ' . $part;
			}
		}
		/*
		* Relative humidity
		*/
		$decoded_metar['rel_humidity'] = number_format(100 * 
		(
			610.710701 + 
			44.4293573 * $decoded_metar['dew_c'] +
			1.41696846 * pow($decoded_metar['dew_c'], 2) + 
			0.0274759545 * pow($decoded_metar['dew_c'], 3) + 
			2.61145937E-4 * pow($decoded_metar['dew_c'], 4) + 
			2.85993708E-6 * pow($decoded_metar['dew_c'], 5)
		)
		/
		(
			610.710701 + 
			44.4293573 * $decoded_metar['temp_c'] +
			1.41696846 * pow($decoded_metar['temp_c'], 2) + 
			0.0274759545 * pow($decoded_metar['temp_c'], 3) + 
			2.61145937E-4 * pow($decoded_metar['temp_c'], 4) + 
			2.85993708E-6 * pow($decoded_metar['temp_c'], 5)
		), 1);

		return $decoded_metar;
	}

	function update_metars_db()
	{
		/* Updates all the metars in the database. You should use it like
		* this:
		*
		* <?php
			* include('phpweather.inc');
			* register_shutdown_function('update_metars_db');
		* ?>
		*  
		* This will update all the metars *after* the script has
		* finished. This means that the user won't know that PHP is still
		* running, and most important, they won't have to wait when the
		* script fetches a new METAR, because it is done afterwards.
		*
		* You can pass en extra argument to get_metar(), so that it never
		* tries to fetch a new meter. This ensures that the page will load
		* quickly, but the weather might be a little old. If the user the
		* refreshed the page, the new weather will be shown.
		*/

		$GLOBALS['phpgw']->db->$query = ("SELECT metar_station FROM phpgw_weather_metar");

		while ($GLOBALS['phpgw']->db->next_record())
		{
			fetch_metar($GLOBALS['phpgw']->db->f("metar_station"), 0);
		}
	}
?>
