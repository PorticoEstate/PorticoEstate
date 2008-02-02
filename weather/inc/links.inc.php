<?php
  /**************************************************************************\
  * phpGroupWare - Weather Link Functions                                    *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	function weather_resolove_comment(&$comment, $city, $state, $country, $gstation)
	{
		if (preg_match_all("/{[A-Za-z]*}/", $comment, $strings))
		{
			/**********************************************************************
			* replace matches
			*********************************************************************/
			for ($loop = 0; $loop <= sizeof($strings[0]); $loop++)
			{
				switch($strings[0][$loop])
				{
					case '{city}':
					case '{City}':
					case '{CITY}':
						$comment = str_replace($strings[0][$loop], ucwords($city),$filename);
						break;
					case '{state}':
					case '{State}':
					case '{STATE}':
						$comment = str_replace($strings[0][$loop], ucwords($state),$filename);
						break;
					case '{country}':
					case '{Country}':
					case '{COUNTRY}':
						$comment = str_replace($strings[0][$loop], ucwords($country),$filename);
						break;
					case '{gstation}':
						$comment = str_replace($strings[0][$loop], ucwords($gstation),$filename);
						break;
				}
			}
		}
	}

	function weather_resolve_filename($city, $state, $statecode,$country, $countrycode, $gstation, &$filename)
	{
		if (preg_match_all("/{[A-Za-z]*}/", $filename, $strings))
		{
			/**********************************************************************
			* replace matches
			*********************************************************************/
			for ($loop = 0; $loop <= sizeof($strings[0]); $loop++)
			{
				switch($strings[0][$loop])
				{
					case '{city}':
						$filename = str_replace($strings[0][$loop], strtolower($city),$filename);
						break;
					case '{City}':
					$filename = str_replace($strings[0][$loop], ucwords($city),$filename);
						break;
					case '{CITY}':
						$filename = str_replace($strings[0][$loop], strtoupper($city),$filename);
						break;
					case '{state}':
						$filename = str_replace($strings[0][$loop], strtolower($state),$filename);
						break;
					case '{State}':
						$filename = str_replace($strings[0][$loop], ucwords($state),$filename);
						break;
					case '{STATE}':
						$filename = str_replace($strings[0][$loop], strtoupper($state),$filename);
						break;
					case '{statecode}':
						$filename = str_replace($strings[0][$loop], strtolower($statecode),$filename);
						break;
					case '{STATECODE}':
						$filename = str_replace($strings[0][$loop], strtoupper($statecode),$filename);
						break;
					case '{country}':
						$filename = str_replace($strings[0][$loop], strtolower($country),$filename);
						break;
					case '{Country}':
						$filename = str_replace($strings[0][$loop], ucwords($country),$filename);
						break;
					case '{COUNTRY}':
						$filename = str_replace($strings[0][$loop], strtoupper($country),$filename);
						break;
					case '{countrycode}':
						$filename = str_replace($strings[0][$loop], strtolower($countrycode),$filename);
						break;
					case '{COUNTRYCODE}':
						$filename = str_replace($strings[0][$loop], strtoupper($countrycode),$filename);
						break;
					case '{gstation}':
						$filename = str_replace($strings[0][$loop], $gstation,$filename);
						break;
				}
			}
			/**********************************************************************
			* have a space replacement rule
			*********************************************************************/
			$filename = str_replace(' ', '_', $filename);
		}
	}

	function weather_link($city, $state, $statecode, $country, $countrycode,
		$gstation, $image_source, $imagesize,
		$links_imgl_url, $links_imgs_url,
		$gdtype, $gdlib_enabled, $metar_enabled, $image_metar,
		$linkid = 0)
	{
		$link_data = "";

		switch($linkid)
		{
			case 0:
				$link_data = weather_wunderground($statecode,$city,$country,$globalstation);
				break;
			default:
				/*
				$link_data["url"] = "http://www.intellicast.com/LocalWeather/World"
				."/UnitedStates/SouthCentral/Texas/Dallas/BaseReflectivity/";
				$link_data["comment"] =
				"Base Reflectivitiy for Dallas, Texas";
				*/

				/**********************************************************************
				* generate the url
				*********************************************************************/
				switch ($image_source)
				{
					case WEATHER_STATIC:
						$filename = $links_imgl_url;
						if ($imagesize == WEATHER_SMALL)
						{
							$filename = $links_imgs_url;
						}
						weather_resolve_filename($city, $state, $statecode,
						$country, $countrycode,
						$gstation, $filename);
						break;
					case WEATHER_SNARFED:
						$filename = $links_imgl_url;
						if ($imagesize == WEATHER_SMALL)
						{
							$filename = $links_imgs_url;
						}
						weather_resolve_filename($city, $state, $statecode,
							$country, $countrycode,
							$gstation, $filename);
						weather_snarf_image($filename);
						break;
					case WEATHER_GENERATED:
						$filename  = 'images/' . weather_graphic($gdtype, $gdlib_enabled,
							$metar_enabled, $image_metar,$imagesize);
						break;
					case WEATHER_TEMPLATE:
						$filename = 'images/' . weather_graphic($gdtype,0,0,'template',$imagesize);
						break;
					default:
						break;
				}

				/**********************************************************************
				* generate the comment
				*********************************************************************/
				$comment = lang($links_comment);
				weather_resolve_comment($comment,
				$city, $state, $country, $gstation);

				/**********************************************************************
				* send it back
				*********************************************************************/
				$link_data['url']     = $filename;
				$link_data['comment'] = $comment;
				break;
		}
		return $link_data;
	}

	function linkage($weather_id, $metar_enabled, $gdlib_enabled, $gdtype, &$link_c)
	{
		$no_records = TRUE;

		$link_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
		$link_tpl->set_unknowns('remove');
		/* stop the current carnage :)
		$GLOBALS['phpgw']->db->query("select * from phpgw_weather_images "
		."left join phpgw_us_states on "
		."phpgw_weather_images.state_id=phpgw_us_states.state_id "
		."left join phpgw_countries on "
		."phpgw_weather_images.country_id=phpgw_countries.country_id "
		."left join phpgw_weather_links on "
		."phpgw_weather_images.links_id=phpgw_weather_links.links_id "
		."WHERE weather_id='$weather_id'");
		*/
		while($GLOBALS['phpgw']->db->next_record())
		{
			$no_records = FALSE;

			$image_source     = $GLOBALS['phpgw']->db->f('images_src'); // snarfed or static::generated or template
			$image_url        = $GLOBALS['phpgw']->db->f('images_url');
			$image_loc        = $GLOBALS['phpgw']->db->f('images_loc'); // top, bottom, left, right
			$image_city       = $GLOBALS['phpgw']->db->f('images_city');
			$image_gstation   = $GLOBALS['phpgw']->db->f('images_gstation');
			$image_metar      = $GLOBALS['phpgw']->db->f('images_metar');
			$image_region     = $GLOBALS['phpgw']->db->f('images_region');
			$state            = $GLOBALS['phpgw']->db->f('state_name');
			$state_code       = $GLOBALS['phpgw']->db->f('state_code');
			$country          = $GLOBALS['phpgw']->db->f('country');
			$country_code     = $GLOBALS['phpgw']->db->f('country_code');
			$links_url        = $GLOBALS['phpgw']->db->f('links_url');
			$links_imgl_url   = $GLOBALS['phpgw']->db->f('links_imgl_url'); // large
			$links_imgs_url   = $GLOBALS['phpgw']->db->f('links_imgs_url'); // small
			$links_comment    = $GLOBALS['phpgw']->db->f('links_comment');
			$links_repchar    = $GLOBALS['phpgw']->db->f('links_repchar');

			/* use arrays links['..'] image['..'] state and country
			*/
			$link_tpl->set_file(link, 'table.link.tpl');

			switch ($image_loc)
			{
				case WEATHER_TOP:
				case WEATHER_BOTTOM:
					$imagesize = WEATHER_LARGE;
					break;
				case WEATHER_LEFT:
				case WEATHER_RIGHT:
				default:
					$imagesize = WEATHER_SMALL;
					break;
			}

			$link_data = weather_link(
				$image_city,
				$state, $state_code,
				$country, $country_code,
				$image_gstation, $image_source, $imagesize,
				$links_imgl_url, $links_imgs_url,
				$gdtype, $gdlib_enabled,
				$metar_enabled, $image_metar
			);
			$link_tpl->set_var(array(
				'link_url'     => $link_data['url'],
				'link_comment' => $link_data['comment'],
				'link_file'    => $filename
			));
		}

		if ($no_records)
		{
			$link_tpl->set_file('link', 'table.image.tpl');
			$filename  = 'images/' . weather_graphic($gdtype);
			$link_tpl->set_var(array(
				'link_comment' => 'PhpGroupWare Weather Center',
				'link_file'    => $filename
			));
		}

		$link_tpl->parse('LINK', 'link');
		$link_c = $link_tpl->get('LINK');
	}

	function weather_snarf_image(&$filename)
	{
	}
?>
