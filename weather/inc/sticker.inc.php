<?php
  /**************************************************************************\
  * phpGroupWare - Weather Sticker Functions                                 *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: sticker.inc.php 8454 2001-12-03 18:01:51Z milosch $ */

/******************************************************************************
 * this function tries to figure out the users global station from wunderground
 *****************************************************************************/
	function sticker_wunder_gstation()
	{
		$status = STD_SUCCESS;

		if (($GLOBALS['weather_user']['city'] != '') || ($GLOBALS['weather_user']['country'] != '') &&
		($GLOBALS['weather_admin']['remote_enabled']))
		{
			$cityp    = str_replace(' ', '+', $GLOBALS['weather_user']['city']);
			$countryp = str_replace(' ', '+', $GLOBALS['weather_user']['country']);

			$backend =
			'http://www.wunderground.com/cgi-bin/findweather/'
			.'getForecast?query='.$cityp.'%2C'.$countryp;

			$lines_array = $GLOBALS['phpgw']->network->gethttpsocketfile($backend);
			$lines_string = implode('', $lines_array);

			if (eregi("[0-9]{4,}.html", $lines_string, $match))
			{
				$GLOBALS['weather_user']['global_station'] =
				substr($match[0], 0, strlen($match[0]) - 5);
			}
			else
			{
				/* we failed by not finding the data in the search page*/
				$status = STD_WARNING;
			}
		}
		else
		{
			/* we failed due to lack of remote access or data */
			$status = STD_WARNING;
		}
		return $status;
	}

	/******************************************************************************
	* this function returns the data associated with linking the wunderground
	*****************************************************************************/
	function sticker_wunder_link()
	{
		if (($GLOBALS['weather_user']['state_code'] == '') ||
			($GLOBALS['weather_user']['global_station'] != ''))
		{
			$status       = STD_SUCCESS;

			if ($GLOBALS['weather_user']['global_station'] == '')
			{
				$status   = sticker_wunder_gstation();
			}

			if ($status == STD_SUCCESS)
			{
				$city     = $GLOBALS['weather_user']['city'];
				$country  = $GLOBALS['weather_user']['country'];
				$gstation = $GLOBALS['weather_user']['global_station'];
			}
			else
			{
				/******************************************************************
				* give them Bamako, Mali (as close as
				* wunderground gets to Timbuktu)
				*****************************************************************/
				$city     = 'Timbuktu';
				$country  = 'Mali';
				$gstation = 61291;
			}

			$link_data['url']     =
				'http://www.wunderground.com/global/stations/'
				.$gstation.'.html';

			$link_data['comment'] =
				lang('Click for %1, %2 Forecast',
					$city, $country);
		}
		else
		{
			$country_code = 'US';
			$cityu        = str_replace(' ', '_', $GLOBALS['weather_user']['city']);

			$link_data['url']     = 'http://www.wunderground.com/'
				.$country_code.'/'.$GLOBALS['weather_user']['state_code'].'/'.$cityu.'.html';

			$link_data["comment"] =
				lang("Click for %1, %2 Forecast",
					$GLOBALS['weather_user']["city"], $GLOBALS['weather_user']["state_code"]);
		}
		return $link_data;
	}

	/******************************************************************************
	* this function returns both remote and (appropriate) local
	* image filenames for a wunderground sticker
	*****************************************************************************/
	function sticker_wunder_image($image_size, &$local_filename)
	{
		$size_type = "bigwx_cond";
		$size_ext  = '';
		if ($image_size == WEATHER_SMALL)
		{
			$size_type = "infoboxtr";
			$size_ext  = "_sm";
		}

		$base =
		"http://banners.wunderground.com/banner/".
		$size_type.
		"/language/www/";

		if (($GLOBALS['weather_user']["state_code"] == '') ||
			($GLOBALS['weather_user']["global_station"] != ''))
		{
			$status       = STD_SUCCESS;

			if (!$GLOBALS['weather_user']["global_station"])
			{
				$status   = sticker_wunder_gstation();
			}

			if ($status == STD_SUCCESS)
			{
				$gstation = $GLOBALS['weather_user']["global_station"];
			}
			else
			{
				/******************************************************************
				* give them Bamako, Mali (as close as
				* wunderground gets to Timbuktu)
				*****************************************************************/
				$gstation = 61291;
			}

			$end  = "global/stations/" . $gstation . ".gif";

			$local_filename = "images/".$gstation.$size_ext.".gif";
		}
		else
		{
			$country_code = "US";
			$cityu        = str_replace(" ", "_", $GLOBALS['weather_user']["city"]);

			$end  = $country_code . "/" . $GLOBALS['weather_user']["state_code"]."/" . $cityu . ".gif";

			$local_filename = "images/".$cityu."_" . $GLOBALS['weather_user']["state_code"].$size_ext.".gif";
		}

		$filename = $base.$end;

		return $filename;
	}

	/******************************************************************************
	* generate the metar data for the sticker
	*****************************************************************************/
	function sticker_metar($metar_id)
	{
		global $strings;

		$GLOBALS['phpgw']->db->query("select metar_station, metar_city, metar_forecast "
			."from phpgw_weather_metar "
			."where metar_id='".$metar_id."'");

		if ($GLOBALS['phpgw']->db->next_record())
		{
			$metar_station = $GLOBALS['phpgw']->db->f("metar_station");
			$location      = $GLOBALS['phpgw']->db->f("metar_city");
			$forecast      = $GLOBALS['phpgw']->db->f("metar_forecast");

			$metar_data    = get_metar($metar_station);
			$data          = process_metar($metar_data);

			extract($data);

			if (isset($cloud_layer1_condition))
			{
				if($cloud_layer1_condition == 'CAVOK')
				{
					$sky_str = lang("Clear");
				}
				else
				{
					$sky_str = lang($cloud_layer1_condition);
				}
			}
			else
			{
				$sky_str = lang("Clear");
			}

			$img_data["sky"]      = $sky_str;

			$img_data["temp"]     = $temp_f."\260 F";

			if (isset($wind_miles_per_hour) && $wind_miles_per_hour > 0)
			{
				$img_data["wind"]  = lang("Winds %1 at %2 mph",
				$wind_dir_text_short,
				$wind_miles_per_hour);
			}
			else
			{
				$img_data["wind"]  = lang("Winds Calm");
			}

			$img_data["pressure"]  = lang("Pressure %1 in",$altimeter_inhg);

			if ($GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"] > 0)
			{
				$gmtoffset
				= "+"
				.$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"];
			}
			if ($GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"] < 0)
			{
				$gmtoffset
				= $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["tz_offset"];
			}


			$img_data["time"]  = $GLOBALS['phpgw']->common->show_date($time,"h:i A ")
			. strftime("%Z")
			. $gmtoffset;

			$img_data["location"] = lang("Observed at %1", $location);

			/**********************************************************************
			* ideally with a well thought out and cleaned up "location"
			* you wouldn't have to parse this stuff out - neotexan
			*********************************************************************/
			//$location = ereg_replace("[A-Za-z]*,", '', $location);
			//$location = ereg_replace("[A-Za-z]*/", '', $location);
			$location = eregi_replace("automatic[a-z/ ]*", '', $location);
			$location = str_replace("Air Force Base", "AFB", $location);
			$location = str_replace("Naval Air Facility", "NAF", $location);
			$location = str_replace("Naval Air Station",  "NAS", $location);

			if (strlen($location) > 30)
			{
				$location = str_replace("International", "Intl", $location);
				$location = str_replace("Regional", "Reg", $location);
				$location = str_replace("Municipal", "Mun", $location);
				$location = str_replace("Airport", "Apt", $location);
			}

			$elements = explode(" ", $location);

			$img_data["location_short"] = chop(
				$elements[0]." ".
				$elements[1]." ".
				$elements[2]." ".
				$elements[3]
			);

			$img_data["advisory"] = sticker_advisory($forecast);
		}
		return $img_data;
	}

	/******************************************************************************
	*  generate the  advisory label for the sticker
	*****************************************************************************/
	function sticker_advisory($forecast)
	{
		$advisory = '';

		/**************************************************************************
		* get the filenames for the forecast data
		*************************************************************************/
		$filename = phorecast_file_name($forecast);

		/**************************************************************************
		* fetch the forecast data
		*************************************************************************/
		$file_data = phorecast_file_data($filename, FALSE);

		/**************************************************************************
		* first pass finds forcast lines and marks them in $arr_marks
		*************************************************************************/
		for($i=0; $i<count($file_data);$i++)
		{
			/**********************************************************************
			* look for an advisory or watch
			*********************************************************************/
			if(ereg('(^\.\.\.)',$file_data[$i]) &&
			eregi('(watch|warning|advisory)',$file_data[$i]))
			{
				$advisory = lang("Advisory");
			}
		}
		return $advisory;
	}


	/******************************************************************************
	* create the working sticker image from the template files
	*****************************************************************************/
	function sticker_create($image_size = WEATHER_LARGE)
	{
		global $g_image_type;

		$filename = PHPGW_SERVER_ROOT."/weather/images/template";
		$size     = '';

		if ($image_size == WEATHER_SMALL)
		{
			$size = "_sm";
		}
		$filename = $filename.$size.".".$g_image_type[$GLOBALS['weather_admin']["gdtype"]];

		switch($GLOBALS['weather_admin']["gdtype"])
		{
			case WEATHER_PNG:
			$im_data = imagecreatefrompng($filename);
			break;
			case WEATHER_GIF:
			$im_data = imagecreatefromgif($filename);
			break;
		}
		return $im_data;
	}

	/******************************************************************************
	* write the finished sticker image to a file
	*****************************************************************************/
	function sticker_write($im, $cache_file)
	{
		switch($GLOBALS['weather_admin']["gdtype"])
		{
			case WEATHER_PNG:
				ImagePng($im,$cache_file);
				break;
			case WEATHER_GIF:
				ImageGif($im,$cache_file);
				break;
		}
	}

	/******************************************************************************
	* name of the local file
	*****************************************************************************/
	function sticker_file($metar_id, $image_size)
	{
		global $g_image_type;

		$extent = '';
		$size   = '';

		if ($image_size == WEATHER_SMALL)
		{
			$size = "_sm";
		}

		if (($GLOBALS['weather_admin']["gdlib_enabled"]) &&
		($metar_id != "template"))
		{
			$extent = "_m";
		}

		$filename = $metar_id.$size.$extent .".".$g_image_type[$GLOBALS['weather_admin']["gdtype"]];

		return $filename;
	}

	/******************************************************************************
	* main function for returning a weather sticker
	*****************************************************************************/
	function weather_sticker($metar_id, $image_size)
	{
		switch ($GLOBALS['weather_user']["sticker_source"])
		{
			case WEATHER_PHPGW:
				$filename = sticker_image_phpgw($metar_id, $image_size);
				break;
			case WEATHER_WUNDER:
			default:
				$filename = sticker_image_wunder($image_size);
				break;
		}
		return $filename;
	}

	/******************************************************************************
	* produce the wunderground image by remote or local reference
	*****************************************************************************/
	function sticker_image_wunder($image_size)
	{
		$filename = sticker_wunder_image($image_size, $local_filename);

		if ($GLOBALS['weather_admin']["image_source"] == WEATHER_SNARFED)
		{
			$status = STD_SUCCESS;

			$filename_w = PHPGW_SERVER_ROOT."/weather/".$local_filename;

			/**********************************************************************
			* needs to use network class - neotexan (content is image/gif)
			*********************************************************************/
			if($fpread = @fopen($filename, 'r'))
			{
				$file = fread($fpread, $GLOBALS['weather_admin']["filesize"]);

				/******************************************************************
				* if succeed, put it in our local file
				*****************************************************************/
				if ($fp = fopen($filename_w,"w"))
				{
					fwrite($fp, $file);

					fclose($fp);
				}
				else
				{
					$status = STD_ERROR;
				}

				fclose($fpread);
			}
			else
			{
				$status = STD_ERROR;
			}

			if ($status == STD_SUCCESS)
			{
				$filename = $GLOBALS['phpgw_info']["server"]["webserver_url"]
					."/weather/" . $local_filename;
			}
			else
			{
				$filename = $GLOBALS['phpgw_info']["server"]["webserver_url"]
					."/weather/" . sticker_file("template", $image_size);
			}
		}
		return $filename;
	}

	/******************************************************************************
	* function for generating our phpgw weather sticker
	*****************************************************************************/
	function sticker_image_phpgw($metar_id, $image_size)
	{
		/**************************************************************************
		* if have metar_id, remote enabled and gdlib enabled, we'll map something
		*************************************************************************/
		if (($metar_id != '') &&
			($GLOBALS['weather_admin']["remote_enabled"]) &&
			($GLOBALS['weather_admin']["gdlib_enabled"]))
		{
			$filename     = sticker_file($metar_id, $image_size);

			$cache_file   = PHPGW_SERVER_ROOT . "/weather/images/".$filename;
			$cache_time   = 3600;  // 1 hour
			$current_time = split(" ", microtime());

			/**********************************************************************
			* see if we need to update the file
			*********************************************************************/
			if ((!(file_exists($cache_file))) || 
				(($current_time[1] - filectime($cache_file)) > $cache_time) ||
				(!(filesize($cache_file))))
			{
				/******************************************************************
				* create our working image
				*****************************************************************/
				$im    = sticker_create($image_size);

				/******************************************************************
				* pick our colors
				*****************************************************************/
				$red   = ImageColorAllocate($im, 255,   0,   0);
				$blue  = ImageColorAllocate($im,   0,   0, 255);
				$black = ImageColorAllocate($im,   0,   0,   0);

				/******************************************************************
				* get the metar data to map
				*****************************************************************/
				$img_data   = sticker_metar($metar_id);

				switch($image_size)
				{
					case WEATHER_LARGE:
						/**************************************************************
						* time of the metar
						*************************************************************/
						$py = 0;
						$px = (480 - (7.2*(strlen($img_data["time"]))));
						ImageString($im,3,$px,$py,$img_data["time"],$blue);

						/**************************************************************
						* advisory information (from forecast actually)
						*************************************************************/
						$py = 28;
						$px = (105 - ((7.2*(strlen($img_data["advisory"])))/2));
						ImageString($im,3,$px,$py,$img_data["advisory"],$red);

						$sky_temp = $img_data["sky"].", ".$img_data["temp"];

						/**************************************************************
						* adjust x position based on wind or sky temp length
						*************************************************************/
						if (strlen($img_data["wind"]) > strlen($sky_temp))
						{
							$px = (385 -  ((7.2 * strlen($img_data["wind"]))/2));
						}
						else
						{
							$px = (385 -  ((7.2 * strlen($sky_temp))/2));
						}

						/**************************************************************
						* sky, temperature
						*************************************************************/
						$py = 12;
						ImageString($im,3,$px,$py,$sky_temp,$black);

						/**************************************************************
						* winds
						*************************************************************/
						$py = 22;
						ImageString($im,3,$px,$py,$img_data["wind"],$black);

						/**************************************************************
						* pressure
						*************************************************************/
						$py = 32;
						ImageString($im,3,$px,$py,$img_data["pressure"],$black);

						/**************************************************************
						* location
						*************************************************************/
						$py = 44;
						$px = ((imagesx($im) - (7.2*(strlen($img_data["location"]))))/2);
						ImageString($im,3,$px,$py,$img_data["location"],$blue);

						/**************************************************************
						* click for forecast if we're linking
						*************************************************************/
						if ($GLOBALS['weather_user']["wunderground_enabled"])
						{
							$string = lang('Click for forecast');
							$py =  18;
							$px = (105 - ((7.2*(strlen($string)))/2));
							ImageString($im,3,$px,$py,$string,$blue);
						}
						break;
					case WEATHER_SMALL:
						/**************************************************************
						* location
						*************************************************************/
						$py = 28;
						$px = (74 - (6.0*(strlen($img_data["location_short"])))/2);
						ImageString($im,2,$px,$py,$img_data["location_short"],$black);

						/**************************************************************
						* advisory information (from forecast actually)
						*************************************************************/
						$py = 45;
						$px = (74 - ((7.2*(strlen($img_data["advisory"])))/2));
						ImageString($im,3,$px,$py,$img_data["advisory"],$red);

						/**************************************************************
						* time of the metar
						*************************************************************/
						$py = 62;
						$px = (74 - ((7.2*(strlen($img_data["time"])))/2));
						ImageString($im,3,$px,$py,$img_data["time"],$blue);

						/**************************************************************
						* sky of the metar
						*************************************************************/
						$py = 79;
						$px = (74 - ((7.2*(strlen($img_data["sky"])))/2));
						ImageString($im,3,$px,$py,$img_data["sky"],$blue);

						/**************************************************************
						* temp of the metar
						*************************************************************/
						$py = 96;
						$px = (74 - ((7.2*(strlen($img_data["temp"])))/2));
						ImageString($im,3,$px,$py,$img_data["temp"],$blue);

						/**************************************************************
						* click for forecast if we're linking
						*************************************************************/
						if ($GLOBALS['weather_user']["wunderground_enabled"])
						{
							$string = lang('Click for forecast');
							$py =  113;
							$px = (74 - ((6.0*(strlen($string)))/2));
							ImageString($im,2,$px,$py,$string,$black);
						}
						break;
				}

				/******************************************************************
				* write our mapped image file
				*****************************************************************/
				sticker_write($im, $cache_file);
				ImageDestroy($im);
			}
		}
		else
		{
			/**********************************************************************
			* just use the default
			*********************************************************************/
			$filename   = sticker_file("template", $image_size);
		}

		return $GLOBALS['phpgw_info']["server"]["webserver_url"]
			."/weather/images/" . $filename;
	}
?>
