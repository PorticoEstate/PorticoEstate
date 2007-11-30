
<?php
  /**************************************************************************\
  * phpGroupWare - Weather Functions                                         *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: functions.inc.php 13074 2003-06-19 21:25:41Z gugux $ */

	define("WEATHER_STATIC",    0);
	define("WEATHER_SNARFED",   1);

	define("WEATHER_LARGE",     0);
	define("WEATHER_SMALL",     1);

	define("WEATHER_PNG",       0);
	define("WEATHER_GIF",       1);

	define("WEATHER_WUNDER",    0);
	define("WEATHER_PHPGW",     1);

	define("STD_SUCCESS",       0);
	define("STD_ERROR",         1);
	define("STD_WARNING",       2);

	$g_image_source   = array(
		WEATHER_STATIC  => lang('Remote'),
		WEATHER_SNARFED => lang('Local')
	);
	$g_image_type     = array(
		WEATHER_PNG     => 'png',
		WEATHER_GIF     => 'gif'
	);
	$g_sticker_source = array(
		WEATHER_WUNDER  => 'Wunderground',
		WEATHER_PHPGW   => 'PhpGW'
	);
	$g_sticker_size   = array(
		WEATHER_LARGE   => lang('Large'),
		WEATHER_SMALL   => lang('Small')
	);
	$g_checked        = array(
		0               => '',
		1               => 'checked'
	);

	include(PHPGW_SERVER_ROOT . '/weather/inc/locale_en.inc');
	include(PHPGW_SERVER_ROOT . '/weather/inc/phpweather.inc.php');
	include(PHPGW_SERVER_ROOT . '/weather/inc/phorecast.inc.php');
	include(PHPGW_SERVER_ROOT . '/weather/inc/sticker.inc.php');

	function weather_get_admin_data()
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_weather_admin");

		if (!$GLOBALS['phpgw']->db->num_rows())
		{
			/**********************************************************************
			* Defaults:
			*   gdlib off, png images, images inlined, remote file access off,
			*   filesize of 140000 allowed for file snarfing
			*********************************************************************/
			$GLOBALS['phpgw']->db->lock("phpgw_weather_admin");
			$GLOBALS['phpgw']->db->query("insert into phpgw_weather_admin values "
			."(0,0,0,0,140000)");
			$GLOBALS['phpgw']->db->unlock();

			$GLOBALS['phpgw']->db->query("select * from phpgw_weather_admin");
		}

		$GLOBALS['phpgw']->db->next_record();

		$GLOBALS['weather_admin']["gdlib_enabled"]   = $GLOBALS['phpgw']->db->f("admin_gdlib_e");
		$GLOBALS['weather_admin']["gdtype"]          = $GLOBALS['phpgw']->db->f("admin_gdtype");
		$GLOBALS['weather_admin']["image_source"]    = $GLOBALS['phpgw']->db->f("admin_imgsrc");
		$GLOBALS['weather_admin']["remote_enabled"]  = $GLOBALS['phpgw']->db->f("admin_remote_e");
		$GLOBALS['weather_admin']["filesize"]        = $GLOBALS['phpgw']->db->f("admin_filesize");
	}

	function weather_set_admin_data()
	{
		if (!$GLOBALS['weather_admin']["remote_enabled"])
		{
			$GLOBALS['weather_admin']["image_source"] = WEATHER_STATIC;
		}

		$GLOBALS['phpgw']->db->lock("phpgw_weather_admin");
		$GLOBALS['phpgw']->db->query("update phpgw_weather_admin set "
		."admin_gdlib_e='".$GLOBALS['weather_admin']["gdlib_enabled"]."', "
		."admin_gdtype='".$GLOBALS['weather_admin']["gdtype"]."', "
		."admin_imgsrc='".$GLOBALS['weather_admin']["image_source"]."', "
		."admin_remote_e='".$GLOBALS['weather_admin']["remote_enabled"]."', "
		."admin_filesize='".$GLOBALS['weather_admin']["filesize"]."'");
		$GLOBALS['phpgw']->db->unlock();
	}


	function weather_get_user_data()
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_weather left join "
		."phpgw_us_states on "
		."phpgw_weather.state_id=phpgw_us_states.state_id "
		."WHERE weather_owner='"
		.$GLOBALS['phpgw_info']["user"]["account_id"]."'");

		if (!$GLOBALS['phpgw']->db->num_rows())
		{
			$GLOBALS['phpgw']->db->lock("phpgw_weather");
			$GLOBALS['phpgw']->db->query("insert into phpgw_weather (weather_owner) values ".
			"('".$GLOBALS['phpgw_info']["user"]["account_id"]."')");
			$GLOBALS['phpgw']->db->unlock();

			$GLOBALS['phpgw']->db->query("select * from phpgw_weather left join "
			."phpgw_us_states on "
			."phpgw_weather.state_id=phpgw_us_states.state_id "
			."WHERE weather_owner='"
			.$GLOBALS['phpgw_info']["user"]["account_id"]."'");
		}

		$GLOBALS['phpgw']->db->next_record();

		$GLOBALS['weather_user']["id"]                   = $GLOBALS['phpgw']->db->f("weather_id");
		$GLOBALS['weather_user']["metar"]                = $GLOBALS['phpgw']->db->f("weather_metar");
		$GLOBALS['weather_user']["links"]                = $GLOBALS['phpgw']->db->f("weather_links");
		$GLOBALS['weather_user']["observations_enabled"] = $GLOBALS['phpgw']->db->f("weather_observ_e");
		$GLOBALS['weather_user']["forecasts_enabled"]    = $GLOBALS['phpgw']->db->f("weather_foreca_e");
		$GLOBALS['weather_user']["links_enabled"]        = $GLOBALS['phpgw']->db->f("weather_links_e");
		$GLOBALS['weather_user']["wunderground_enabled"] = $GLOBALS['phpgw']->db->f("weather_wunder_e");
		$GLOBALS['weather_user']["template"]             = $GLOBALS['phpgw']->db->f("weather_template");
		$GLOBALS['weather_user']["city"]                 = $GLOBALS['phpgw']->db->f("weather_city");
		$GLOBALS['weather_user']["state_id"]             = $GLOBALS['phpgw']->db->f("state_id");
		$GLOBALS['weather_user']["state"]                = $GLOBALS['phpgw']->db->f("state_name");
		$GLOBALS['weather_user']["state_code"]           = $GLOBALS['phpgw']->db->f("state_code");
		$GLOBALS['weather_user']["country"]              = $GLOBALS['phpgw']->db->f("weather_country");
		$GLOBALS['weather_user']["global_station"]       = $GLOBALS['phpgw']->db->f("weather_gstation");
		$GLOBALS['weather_user']["title_enabled"]        = $GLOBALS['phpgw']->db->f("weather_title_e");
		$GLOBALS['weather_user']["title_metar"]          = $GLOBALS['phpgw']->db->f("weather_tmetar");
		$GLOBALS['weather_user']["title_size"]           = $GLOBALS['phpgw']->db->f("weather_tsize");
		$GLOBALS['weather_user']["frontpage_enabled"]    = $GLOBALS['phpgw']->db->f("weather_fpage_e");
		$GLOBALS['weather_user']["frontpage_metar"]      = $GLOBALS['phpgw']->db->f("weather_fpmetar");
		$GLOBALS['weather_user']["frontpage_size"]       = $GLOBALS['phpgw']->db->f("weather_fpsize");
		$GLOBALS['weather_user']["sticker_source"]       = $GLOBALS['phpgw']->db->f("weather_sticker");
	}

	function weather_set_user_data()
	{
		$GLOBALS['weather_user']["city"] = ucwords($GLOBALS['weather_user']["city"]);
		$GLOBALS['weather_user']["country"] = ucwords($GLOBALS['weather_user']["country"]);

		$GLOBALS['phpgw']->db->lock("phpgw_weather");

		$GLOBALS['phpgw']->db->query
		("update phpgw_weather set "
		."weather_metar='".$GLOBALS['weather_user']["metar"]."', "
		."weather_links='".$GLOBALS['weather_user']["links"]."', "
		."weather_title_e='".$GLOBALS['weather_user']["title_enabled"]."', "
		."weather_observ_e='".$GLOBALS['weather_user']["observations_enabled"]."', "
		."weather_foreca_e='".$GLOBALS['weather_user']["forecasts_enabled"]."', "
		."weather_links_e='".$GLOBALS['weather_user']["links_enabled"]."', "
		."weather_wunder_e='".$GLOBALS['weather_user']["wunderground_enabled"]."', "
		."weather_fpage_e='".$GLOBALS['weather_user']["frontpage_enabled"]."', "
		."weather_template='".$GLOBALS['weather_user']["template"]."', "
		."weather_city='".$GLOBALS['weather_user']["city"]."', "
		."weather_country='".$GLOBALS['weather_user']["country"]."', "
		."weather_gstation='".$GLOBALS['weather_user']["global_station"]."', "
		."weather_sticker='".$GLOBALS['weather_user']["sticker_source"]."', "
		."weather_tmetar='".$GLOBALS['weather_user']["title_metar"]."', "
		."weather_tsize='".$GLOBALS['weather_user']["title_size"]."', "
		."weather_fpmetar='".$GLOBALS['weather_user']["frontpage_metar"]."', "
		."weather_fpsize='".$GLOBALS['weather_user']["frontpage_size"]."', "
		."state_id='".$GLOBALS['weather_user']["state_id"]."' "
		."where weather_owner='".$GLOBALS['phpgw_info']["user"]["account_id"]."' "
		."and weather_id='".$GLOBALS['weather_user']["id"]."'");
		$GLOBALS['phpgw']->db->unlock();
	}

	function weather_match_bar($start, $indexlimit, $city, &$matchs_c)
	{
		$end  = $start + $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];

		if ($end > $indexlimit)
		{
			$end = $indexlimit;
		}

		switch ($indexlimit)
		{
			case 0:
			case 1:
			{
				$showstring =
				lang("showing %1", $indexlimit);
			}
			break;

			default:
			{
				if ((($start+1) == $end) &&
				($GLOBALS['weather_user']["forecasts_enabled"] == 1))
				{
					$showstring =
					lang("showing # %1 of %2",
					($start + 1), $indexlimit);
				}
				else
				{
					$showstring =
					lang("showing %1 - %2 of %3",
					($start + 1), $end, $indexlimit);
				}
			}
			break;
		}

		$matchs_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$matchs_tpl->set_unknowns("remove");
		$matchs_tpl->set_file(matchs, "matchs.metar.tpl");
		$matchs_tpl->
		set_var
		(array(next_matchs_left  =>
		$GLOBALS['phpgw']->nextmatchs->left("/weather/index.php",$start,$indexlimit,""),
		current_city      => $city,
		next_matchs_label => $showstring,
		next_matchs_right =>
		$GLOBALS['phpgw']->nextmatchs->right("/weather/index.php",$start,$indexlimit,""),
		navbar_bg         => $GLOBALS['phpgw_info']["theme"]["navbar_bg"],
		navbar_text       => $GLOBALS['phpgw_info']["theme"]["navbar_text"]));
		$matchs_tpl->parse(MATCHS, "matchs");
		$matchs_c = $matchs_tpl->get("MATCHS");
	}

	function weather_display_observation($station, &$observation_c)
	{
		$observation_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$observation_tpl->set_unknowns("remove");
		$observation_tpl->set_file(observation, "table.observation.tpl");
		$observation_tpl->
		set_var
		(array(observation_heading => lang("recent observations"),
		th_bg               => $GLOBALS['phpgw_info']["theme"]["th_bg"],
		th_text             => $GLOBALS['phpgw_info']["theme"]["th_text"],
		bg_color            => $GLOBALS['phpgw_info']["theme"]["bg_color"],
		bg_text             => $GLOBALS['phpgw_info']["theme"]["bg_text"]));

		$metar = get_metar($station);
		$pretty_metar = pretty_print_metar($metar);

		$observation_tpl->
		set_var(observation_body, $pretty_metar);
		$observation_tpl->parse(OBSERVATION, "observation");
		$observation_c = $observation_tpl->get("OBSERVATION");
	}

	function weather_display_frontpage()
	{
		if ($GLOBALS['weather_user']["frontpage_enabled"])
		{
			if ($GLOBALS['weather_user']["wunderground_enabled"] == 1)
			{
				$fpage_data = sticker_wunder_link();
			}
			else
			{
				$fpage_data['url'] = $GLOBALS['phpgw']->link();
				$fpage_data['comment'] = lang('Welcome to the PhpGW Weather Center');
			}

			$fpage_data['filename'] = weather_sticker($GLOBALS['weather_user']['frontpage_metar'],$GLOBALS['weather_user']['frontpage_size']);
			
			$fpage_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
			$fpage_tpl->set_unknowns('remove');
			$fpage_tpl->set_file(link, 'row.link.tpl');
			$fpage_tpl->set_var(array(
				'link_url'     => $fpage_data['url'],
				'link_comment' => $fpage_data['comment'],
				'link_file'    => $fpage_data['filename']
			));
			$fpage_tpl->parse('FPAGE', 'link');
			return '<table width="100%" border="0">'.$fpage_tpl->fp('FPAGE','link').'</table>';
		}
	}

	function weather_display_title(&$title_c)
	{
		if ($GLOBALS['weather_user']["title_enabled"])
		{
			if ($GLOBALS['weather_user']["wunderground_enabled"] == 1)
			{
				$title_data = sticker_wunder_link();
			}
			else
			{
				$title_data["url"]
				= $GLOBALS['phpgw']->link();
				$title_data["comment"]
				= lang("Welcome to the PhpGW Weather Center");
			}

			$title_data["filename"] =
			weather_sticker($GLOBALS['weather_user']["title_metar"],
			$GLOBALS['weather_user']["title_size"]);

			$title_tpl = CreateObject('phpgwapi.Template',
			$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
			$title_tpl->set_unknowns("remove");
			$title_tpl->set_file(link, "table.link.tpl");
			$title_tpl->
			set_var
			(array(link_url     => $title_data["url"],
			link_comment => $title_data["comment"],
			link_file    => $title_data["filename"]));
			$title_tpl->parse(TITLE, "link");
			$title_c = $title_tpl->get("TITLE");
		}
	}

	function weather_display_afo($start, &$matchs_c, &$advisory_c,
	&$forecast_c, &$extforecast_c, &$observation_c)
	{
		if ($GLOBALS['weather_admin']["remote_enabled"] == 1)
		{
			if ($GLOBALS['weather_user']["metar"] != "")
			{
				$metar_id  = explode(",", $GLOBALS['weather_user']["metar"]);

				$indexlimit = count($metar_id);

				if (!$start)
				{
					$start = 0;
				}

				$temp = $GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];

				/******************************************************************
				* if forecasts are not enabled then use the "space" to display
				* multiple observations.  (this may go away when radar data becomes
				* available or if offering multiple "links")
				*****************************************************************/
				if ($GLOBALS['weather_user']["forecasts_enabled"] == 0)
				{
					$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"] = 3;

					$metar_city = lang("metar")." ".lang("stations");

					$end = $start +
					$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"];

					if ($end > $indexlimit)
					{
						$end = $indexlimit;
					}

					$sobservation_tpl =
					CreateObject('phpgwapi.Template',
					$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
					$sobservation_tpl->set_unknowns("remove");
					$sobservation_tpl->set_file
					(array(observation => "table.observation2.tpl",
					list_item => "list.observation.tpl"));
					$sobservation_tpl->set_var(
						array(navbar_bg   => $GLOBALS['phpgw_info']["theme"]["navbar_bg"],
						navbar_text => $GLOBALS['phpgw_info']["theme"]["navbar_text"]));

						for ($index=$start; $index < $end; $index++)
						{
							$this_city = weather_list_by_metar($metar_id[$index],
							$advisory_c,
							$forecast_c,
							$extforecast_c,
							$single_observation_c);
							$sobservation_tpl->set_var(
								array(current_city      => $this_city,
								observation_table => $single_observation_c));
								$sobservation_tpl->parse(observation_list,
								"list_item", TRUE);
							}
							$sobservation_tpl->parse(OBSERVATION, "observation");
							$observation_c = $sobservation_tpl->get("OBSERVATION");
						}
						else
						{
							$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"] = 1;

							$metar_city = weather_list_by_metar($metar_id[$start],
							$advisory_c,
							$forecast_c,
							$extforecast_c,
							$observation_c);
						}

						weather_match_bar($start, $indexlimit,
						$metar_city, $matchs_c);

						$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"] = $temp;
					}
				}
			}


	function weather_display_links(&$link_c)
	{
	}

	function weather_list_by_metar($metar_id,&$advisory_c, &$forecast_c, &$extforecast_c,&$observation_c)
	{
		$GLOBALS['phpgw']->db->query("select metar_station, metar_city, metar_forecast "
			."from phpgw_weather_metar "
			."where metar_id='".$metar_id."'");

		if ($GLOBALS['phpgw']->db->next_record())
		{
			$forecast = $GLOBALS['phpgw']->db->f("metar_forecast");
			$station  = $GLOBALS['phpgw']->db->f("metar_station");
			$city     = $GLOBALS['phpgw']->db->f("metar_city");

			if($GLOBALS['weather_user']["observations_enabled"] == 1)
			{
				weather_display_observation($station, $observation_c);
			}

			if (($GLOBALS['weather_user']["forecasts_enabled"] == 1) && ($forecast != ""))
			{
				weather_display_phorecast($forecast, $advisory_c,
				$forecast_c, $extforecast_c);
			}
		}
		return $city;
	}

	function image_source_options($source_selected, &$options_c)
	{
		global $g_image_source;

		/**************************************************************************
		* start our template
		*************************************************************************/
		$source_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$source_tpl->set_unknowns("remove");
		$source_tpl->set_file(options, "option.common.tpl");

		for ($loop = 0; $loop < 2; $loop++)
		{
			$selected = "";
			if ($loop == $source_selected)
			{
				$selected = "selected";
			}
			$source_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $loop,
				OPTION_NAME     => $g_image_source[$loop]
			));
			$source_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $source_tpl->get("option_list");
	}

	function image_type_options($type_selected, &$options_c)
	{
		global $g_image_type;

		/**************************************************************************
		* start our template
		*************************************************************************/
		$type_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$type_tpl->set_unknowns("remove");
		$type_tpl->set_file(options, "option.common.tpl");

		for ($loop = 0; $loop < 2; $loop++)
		{
			$selected = "";
			if ($loop == $type_selected)
			{
				$selected = "selected";
			}
			$type_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $loop,
				OPTION_NAME     => $g_image_type[$loop]
			));
			$type_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $type_tpl->get("option_list");
	}

	function sticker_source_options($source_selected, &$options_c)
	{
		global $g_sticker_source;

		/**************************************************************************
		* start our template
		*************************************************************************/
		$source_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$source_tpl->set_unknowns("remove");
		$source_tpl->set_file(options, "option.common.tpl");

		for ($loop = 0; $loop < 2; $loop++)
		{
			$selected = "";
			if ($loop == $source_selected)
			{
				$selected = "selected";
			}
			$source_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $loop,
				OPTION_NAME     => $g_sticker_source[$loop]
			));
			$source_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $source_tpl->get("option_list");
	}

	function sticker_size_options($size_selected, &$options_c)
	{
		global $g_sticker_size;

		/**************************************************************************
		* start our template
		*************************************************************************/
		$size_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$size_tpl->set_unknowns("remove");
		$size_tpl->set_file(options, "option.common.tpl");

		for ($loop = 0; $loop < 2; $loop++)
		{
			$selected = "";
			if ($loop == $size_selected)
			{
				$selected = "selected";
			}
			$size_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $loop,
				OPTION_NAME     => $g_sticker_size[$loop]
			));
			$size_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $size_tpl->get("option_list");
	}

	function state_options($state_selected, &$options_c)
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_us_states");

		/**************************************************************************
		* start our template
		*************************************************************************/
		$state_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$state_tpl->set_unknowns("remove");
		$state_tpl->set_file(options, "option.common.tpl");

		while ($GLOBALS['phpgw']->db->next_record())
		{
			$state_id = $GLOBALS['phpgw']->db->f("state_id");

			$selected = "";
			if ($state_selected == $state_id)
			{
				$selected = "selected";
			}
			if ($state_id != 1)
			{
				$option_name = $GLOBALS['phpgw']->db->f("state_name");
			}
			else
			{
				$option_name = lang($GLOBALS['phpgw']->db->f("state_name"));
			}
			$state_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $state_id,
				OPTION_NAME     => $option_name
			));
			$state_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $state_tpl->get("option_list");
	}

	function metar_options($metar_ids, &$options_c, $extras=False)
	{
		$GLOBALS['phpgw']->db->query
		("select * from phpgw_weather_metar left join "
		."phpgw_weather_region on "
		."phpgw_weather_metar.region_id=phpgw_weather_region.region_id");

		$metar_ids = explode(",", $metar_ids);
		$index     = 0;

		asort($metar_ids);

		if ($GLOBALS['phpgw']->db->num_rows())
		{
			/**********************************************************************
			* start our template
			*********************************************************************/
			$metar_tpl =  CreateObject('phpgwapi.Template',
			$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
			$metar_tpl->set_unknowns("remove");
			$metar_tpl->set_file(options, "option.common.tpl");

			while ($GLOBALS['phpgw']->db->next_record())
			{
				$metar_id = $GLOBALS['phpgw']->db->f("metar_id");

				$selected = "";
				if ($metar_id == $metar_ids[$index])
				{
					$index++;

					$selected = "selected";
				}

				switch ($extras)
				{
					case True:
					$option_name = sprintf("%s - %s(%s)",
					$GLOBALS['phpgw']->db->f("region_name"),
					$GLOBALS['phpgw']->db->f("metar_city"),
					$GLOBALS['phpgw']->db->f("metar_station"));
					break;
					case False:
					/**************************************************************
					* ideally with a well thought out and cleaned up "location"
					* you wouldn't have to parse this stuff out - neotexan
					*************************************************************/
					$location = eregi_replace("automatic[a-z/ ]*", "",
					$GLOBALS['phpgw']->db->f("metar_city"));
					$location = eregi_replace("aviation[a-z/ ]*", "", $location);
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
					$option_name = substr($location,0,34);
					break;
				}

				$metar_tpl->set_var(array(
					OPTION_SELECTED => $selected,
					OPTION_VALUE    => $metar_id,
					OPTION_NAME     => $option_name));
				$metar_tpl->parse(option_list, "options", TRUE);
			}
			$options_c = $metar_tpl->get("option_list");
		}
	}

	function link_options($link_ids, &$options_c)
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_weather_links");

		$link_ids = explode(",", $link_ids);
		$index     = 0;

		asort($link_ids);

		if ($GLOBALS['phpgw']->db->num_rows())
		{
			/**********************************************************************
			* start our template
			*********************************************************************/
			$link_tpl =  CreateObject('phpgwapi.Template',
			$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
			$link_tpl->set_unknowns("remove");
			$link_tpl->set_file(options, "option.common.tpl");

			while ($GLOBALS['phpgw']->db->next_record())
			{
				$link_id = $GLOBALS['phpgw']->db->f("link_id");

				$selected = "";
				if ($link_id == $link_ids[$index])
				{
					$index++;

					$selected = "selected";
				}

				$option_name = lang($GLOBALS['phpgw']->db->f("link_name"));

				$link_tpl->set_var(array(
					OPTION_SELECTED => $selected,
					OPTION_VALUE    => $link_id,
					OPTION_NAME     => $option_name
				));
				$link_tpl->parse(option_list, "options", TRUE);
			}
			$options_c = $link_tpl->get("option_list");
		}
	}

	function weather_template_options($app_template, &$options_c, &$images_c)
	{
		$appname = $GLOBALS['phpgw_info']["flags"]["currentapp"];

		$directory = opendir(PHPGW_APP_TPL);

		$index=0;

		while ($filename = readdir($directory))
		{
			if (eregi("format[0-9]{2}.$appname.tpl", $filename, $match))
			{
				$file_ar[$index] = $match[0];
				$index++;
			}
		}

		closedir($directory);

		for ($loop=0; $loop < $index; $loop++)
		{
			eregi("[0-9]{2}", $file_ar[$loop], $tid);
			eregi("format[0-9]{2}", $file_ar[$loop], $tname);

			$template_id = "$tid[0]";
			$template_name["$template_id"] = $tname[0];
		}

		asort($template_name);

		/**************************************************************************
		* start our template
		*************************************************************************/
		$image_tpl =  CreateObject('phpgwapi.Template',
		$GLOBALS['phpgw']->common->get_tpl_dir($appname));
		$image_tpl->set_unknowns("remove");
		$image_tpl->set_file(array(
			options => "option.common.tpl",
			rows    => "row.images.tpl",
			cells   => "cell.images.tpl"
		));

		while (list($value, $name) = each($template_name))
		{
			$selected = "";
			if ((int)$value == $app_template)
			{
				$selected = "selected";
			}

			$image_tpl->set_var(array(OPTION_SELECTED => $selected,
			OPTION_VALUE    => (int)$value,
			OPTION_NAME     => $name));

			$image_tpl->parse(option_list, "options", TRUE);
		}
		$options_c = $image_tpl->get("option_list");

		reset($template_name);
		$counter = 0;

		while (list($value, $name) = each($template_name))
		{
			$index--;

			$imgname = $name.".gif";

			$filename_f =
			$GLOBALS['phpgw']->common->get_image_dir($appname)."/".$imgname;
			$filename_a =
			$GLOBALS['phpgw']->common->get_image_path($appname)."/".$imgname;

			if (file_exists($filename_f))
			{
				$counter++;

				$image_tpl->set_var(array(image_number => $name,
				image_url    => $filename_a));
				$image_tpl->parse(image_row, "cells", TRUE);
			}

			if (($counter == 5) || ($index == 0))
			{
				$cells_c = $image_tpl->get("image_row");

				$image_tpl->set_var(image_cells, $cells_c);
				$image_tpl->parse(IMAGE_ROWS, rows, TRUE);

				$counter = 0;
			}
		}
		$images_c = $image_tpl->get("IMAGE_ROWS");
	}
?>
