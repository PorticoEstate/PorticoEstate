<?php
  /**************************************************************************\
  * phpGroupWare - Weather Center Metar Station Functions                    *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: metar_stations.inc.php 12483 2003-04-22 20:34:50Z gugux $ */

	function station_table($order, $sort, $filter, $start, $query, $qfield, &$table_c)
	{
		$edit_label   = lang('Edit');
		$delete_label = lang('Delete');

		$searchobj = array(
			array('metar_station',  'Station'),
			array('metar_forecast', 'Forecast Zone'),
			array('metar_city',     'City'),
			array('region_name',    'Region')
		);

		if ($order)
		{
			$ordermethod = "ORDER BY $order $sort ";
		}
		else
		{
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$ordermethod = "ORDER BY metar_city ASC ";
					break;
				default:
					$ordermethod = "ORDER BY M.metar_city ASC ";
					break;
			}
		}

		if (! $sort)
		{
			$sort = 'DESC';
		}

		if (! $start)
		{
			$start = 0;
		}

		if (! $filter)
		{
			$filter = 'none';
		}

		if (!$qfield)
		{
			$qfield = 'metar_city';
		}


		if (!$query)
		{
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$GLOBALS['phpgw']->db->limit_query("SELECT COUNT(*) FROM phpgw_weather_metar "
						. "LEFT JOIN phpgw_weather_region ON "
						. "phpgw_weather_metar.region_id="
						. "phpgw_weather_region.region_id "
						. $ordermethod,
						$start
					);
					break;
				default:
					$GLOBALS['phpgw']->db->limit_query("SELECT COUNT(R.region_id) FROM "
						. "phpgw_weather_metar M, phpgw_weather_region R "
						. "WHERE R.region_id=M.region_id",
						$start
					);
					break;
			}
		}
		else
		{
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$GLOBALS['phpgw']->db->limit_query("SELECT COUNT(*) FROM phpgw_weather_metar "
						. "LEFT JOIN phpgw_weather_region ON "
						. "phpgw_weather_metar.region_id="
						. "phpgw_weather_region.region_id "
						. "WHERE $qfield like '%$query%' "
						. $ordermethod,
						$start
					);
					break;
				default:
					$GLOBALS['phpgw']->db->limit_query("SELECT R.region_id FROM "
						. "phpgw_weather_metar M, phpgw_weather_region R "
						. "WHERE (R.region_id=M.region_id AND "
						. "M." . $qfield . " like '%$query%') "
						. $ordermethod,
						$start
					);
					break;
			}
		}

		$GLOBALS['phpgw']->db->next_record();
		if ($GLOBALS['phpgw']->db->f(0) >
			$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"])
		{
			$match_comment = 
			lang("showing %1 - %2 of %3",($start + 1),
			($start +
			$GLOBALS['phpgw_info']["user"]["preferences"]["common"]["maxmatchs"]),
			$GLOBALS['phpgw']->db->f(0));
		}
		else
		{
			$match_comment = lang("showing %1",$GLOBALS['phpgw']->db->f(0));
		}

		$match_bar =
			$GLOBALS['phpgw']->nextmatchs->show_tpl("/weather/admin_stations.php",
				$start,$GLOBALS['phpgw']->db->f(0), '',
				"85%", $GLOBALS['phpgw_info']["theme"]["th_bg"],
				$searchobj,0
			);

		$station_link_label =
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"metar_station",$order,
				"/weather/admin_stations.php",
				lang("Station")
			);
		$fzone_link_label   =
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"metar_forecast",$order,
				"/weather/admin_stations.php",
				lang("FZone")
			);
		$city_link_label    =
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"metar_city",$order,
				"/weather/admin_stations.php",
				lang("City")
			);
		$region_link_label  =
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,"region_name",$order,
				"/weather/admin_stations.php",
				lang("Region")
			);

		if (! $query)
		{
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_weather_metar "
						. "LEFT JOIN phpgw_weather_region ON "
						. "phpgw_weather_metar.region_id="
						. "phpgw_weather_region.region_id " 
						. $ordermethod,
						$start
					);
					break;
				default:
					$GLOBALS['phpgw']->db->limit_query("SELECT * FROM "
						. "phpgw_weather_metar M, phpgw_weather_region R "
						. "WHERE R.region_id=M.region_id",
						$start
					);
					break;
			}
		}
		else
		{
			switch ($GLOBALS['phpgw_info']['server']['db_type'])
			{
				case 'mysql':
					$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_weather_metar "
						. "LEFT JOIN phpgw_weather_region ON "
						. "phpgw_weather_metar.region_id="
						. "phpgw_weather_region.region_id "
						. "WHERE $qfield LIKE '%$query%' "
						. $ordermethod,
						$start
					);
					break;
				default:
					$GLOBALS['phpgw']->db->limit_query("SELECT * FROM "
						. "phpgw_weather_metar M, phpgw_weather_region R "
						. "WHERE (R.region_id=M.region_id AND "
						. "M." . $qfield . " LIKE '%$query%') "
						. $ordermethod,
						$start
					);
					break;
			}
		}

		$table_tpl =
			CreateObject('phpgwapi.Template',
				$GLOBALS['phpgw']->common->get_tpl_dir('weather')
			);
		$table_tpl->set_unknowns("remove");
		$table_tpl->set_file(array(
			table => "table.stations.tpl",
			row   => "row.stations.tpl"
		));

		while ($GLOBALS['phpgw']->db->next_record()) 
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

			$station = $GLOBALS['phpgw']->db->f("metar_station");
			if (! $station)
			{
				$station = "&nbsp;";
			}

			$forecast = $GLOBALS['phpgw']->db->f("metar_forecast");
			if (! $forecast)
			{
				$forecast = "&nbsp;";
			}

			$city = $GLOBALS['phpgw']->db->f("metar_city");
			if (! $city)
			{
				$city = "&nbsp;";
			}

			$region = $GLOBALS['phpgw']->db->f("region_name");
			if (! $region)
			{
				$region = "&nbsp;";
			}

			$metar_encoded = urlencode($GLOBALS['phpgw']->db->f("metar_id"));

			$table_tpl->set_var(array(
				row_color    => $tr_color,
				station      => $station,
				forecast     => $forecast,
				city         => $city,
				region       => $region,
				edit_url     => $GLOBALS['phpgw']->link(
					"/weather/admin_stations.php",
					"con=" . $metar_encoded
					. "&act=edit"
					. "&start=$start"
					. "&order=$order"
					. "&filter=$filter"
					. "&sort=$sort"
					. "&query="
					. urlencode($query)
					. "&qfield=$qfield"
				),
				edit_label   => $edit_label,
				delete_url   => $GLOBALS['phpgw']->link(
					"/weather/admin_stations.php",
					"con=" . $metar_encoded
					. "&act=delete"
					. "&start=$start"
					. "&order=$order"
					. "&filter=$filter"
					. "&sort=$sort"
					. "&query="
					. urlencode($query)
					. "&qfield=$qfield"
				),
				delete_label => $delete_label
			));
				$table_tpl->parse(station_rows, "row", True);
		}

		$table_tpl->set_var(array(
			'th_bg'              => $GLOBALS['phpgw_info']["theme"]["th_bg"],
			'total_matchs'       => $match_comment,
			'next_matchs'        => $match_bar,
			'station_link_label' => $station_link_label,
			'fzone_link_label'   => $fzone_link_label,
			'city_link_label'    => $city_link_label,
			'region_link_label'  => $region_link_label,
			'edit_label'         => $edit_label,
			'delete_label'       => $delete_label,
			'action_url'         => $action_url,
			'action_label'       => lang($act),
			'reset_label'        => lang("Reset")
		));

		$table_tpl->parse(table_part, "table");
		$table_c = $table_tpl->get("table_part");
	}

	function station_entry($con, $act,$order, $sort, $filter, $start, $query, $qfield, &$form_c)
	{
		$action_url   =  $GLOBALS['phpgw']->link(
			"/weather/admin_stations.php",
			"act=$act"
			. "&start=$start&order=$order&filter=$filter"
			. "&sort=$sort"
			. "&query=".urlencode($query)
			. "&qfield=$qfield"
		);

		switch($act)
		{
			case "add":
				$bg_color = $GLOBALS['phpgw_info']["theme"]["th_bg"];
				break;
			case "delete":
				$bg_color = $GLOBALS['phpgw_info']["theme"]["bg07"];
				break;
			default:
				$bg_color = $GLOBALS['phpgw_info']["theme"]["table_bg"];
				break;
		}

		$station   = '';
		$city      = '';
		$region_id = '';
		$forecast  = '';

		if ($con != '')
		{
			$GLOBALS['phpgw']->db->query("select * from phpgw_weather_metar where metar_id=$con");
			$GLOBALS['phpgw']->db->next_record();

			$station   = $GLOBALS['phpgw']->db->f("metar_station");
			$city      = $GLOBALS['phpgw']->db->f("metar_city");
			$region_id = $GLOBALS['phpgw']->db->f("region_id");
			$forecast  = $GLOBALS['phpgw']->db->f("metar_forecast");
		}

		$modify_tpl =
			CreateObject('phpgwapi.Template',
				$GLOBALS['phpgw']->common->get_tpl_dir('weather')
			);
		$modify_tpl->set_unknowns("remove");
		$modify_tpl->set_file(array(
			'form'   => "form.stations.tpl",
			'option' => "option.common.tpl"
		));

		$GLOBALS['phpgw']->db->query("select * from phpgw_weather_region");
		while ($GLOBALS['phpgw']->db->next_record())
		{
			$cur_region_id =  $GLOBALS['phpgw']->db->f("region_id");

			$selected = '';
			if ($region_id == $cur_region_id)
			{
				$selected = "selected";
			}

			$modify_tpl->set_var(array(
				OPTION_SELECTED => $selected,
				OPTION_VALUE    => $cur_region_id,
				OPTION_NAME     => $GLOBALS['phpgw']->db->f("region_name")
			));
			$modify_tpl->parse(region_options, "option", True);
		}

		$modify_tpl->set_var(array(
			'bg_color'       => $bg_color,
			'metar_id'       => $con,
			'station_label'  => lang('Station'),
			'metar_station'  => $station,
			'city_label'     => lang('City'),
			'metar_city'     => $city,
			'forecast_label' => lang('Forecast Zone'),
			'metar_forecast' => $forecast,
			'region_label'   => lang('Region'),
			'action_url'     => $action_url,
			'action_label'   => lang($act),
			'reset_label'    => lang('Reset')
		));

		$modify_tpl->parse('form_part', 'form');
		$form_c = $modify_tpl->get('form_part');
	}
?>
