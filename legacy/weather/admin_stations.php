<?php
  /**************************************************************************\
  * phpGroupWare - Weather Center Metar Stations Admin                       *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

    /* $Id$ */
    
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'   => 'weather',
		'admin_header' => True,
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');
	include('inc/metar_stations.inc.php');

	$title             = lang('Weather Metar Stations');
	$done_label        = lang('Done');
	$doneurl           = $GLOBALS['phpgw']->link('/admin/index.php');
	$message           = '';

	$con    = $HTTP_POST_VARS['con'] ? $HTTP_POST_VARS['con'] : $HTTP_GET_VARS['con'];
	$act    = $HTTP_POST_VARS['act'] ? $HTTP_POST_VARS['act'] : $HTTP_GET_VARS['act'];
	$submit = $HTTP_POST_VARS['submit'] ? $HTTP_POST_VARS['submit'] : $HTTP_GET_VARS['submit'];
	$query  = $HTTP_POST_VARS['query']  ? $HTTP_POST_VARS['query']  : $HTTP_GET_VARS['query'];
	$sort   = $HTTP_POST_VARS['sort']   ? $HTTP_POST_VARS['sort']   : $HTTP_GET_VARS['sort'];
	$order  = $HTTP_POST_VARS['order']  ? $HTTP_POST_VARS['order']  : $HTTP_GET_VARS['order'];
	$filter = $HTTP_POST_VARS['filter'] ? $HTTP_POST_VARS['filter'] : $HTTP_GET_VARS['filter'];
	
	if ($submit)
	{
		switch($act)
		{
			case 'edit':
				$message = 'modification';
				break;
			case 'delete':
				$message = 'deletion';
				break;
			case 'add':
				$message = 'addition';
				break;
		}
		$message = lang('Performed %1 of element', $message);
	}

	$other_c           = '';

	if ($metar_station)
	{
		$metar_station = strtoupper($metar_station);
	}

	if ($metar_city)
	{
		$metar_city = ucwords($metar_city);
	}

	if ($metar_forecast)
	{
		$metar_forecast = strtoupper($metar_forecast);
	}

	switch($act)
	{
		case 'edit':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_metar');
				$GLOBALS['phpgw']->db->query("update phpgw_weather_metar set "
					."metar_station='".$metar_station."',"
					."metar_city='".$metar_city."',"
					."metar_forecast='".$metar_forecast."',"
					."region_id='".$region_id."' "
					."where metar_id='".$metar_id."'");
				$GLOBALS['phpgw']->db->unlock();

				station_table($order, $sort, $filter, $start, $query, $qfield,
					$table_c);
				station_entry('', 'add', $order, $sort, $filter,
					$start, $query, $qfield, $add_c);
			}
			else
			{
				station_table($order, $sort, $filter, $start, $query, $qfield,
					$table_c);
				station_entry('', 'add', $order, $sort, $filter,
					$start, $query, $qfield, $add_c);
				station_entry($con, $act, $order, $sort, $filter,
					$start, $query, $qfield, $other_c);
			}
			break;
		case 'delete':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_metar');
				$GLOBALS['phpgw']->db->query("delete from phpgw_weather_metar where metar_id='"
					.$metar_id."'");
				$GLOBALS['phpgw']->db->unlock();

				station_table($order, $sort, $filter, $start, $query, $qfield,
					$table_c);
				station_entry('', 'add', $order, $sort, $filter,
					$start, $query, $qfield, $add_c);
			}
			else
			{
				station_table($order, $sort, $filter, $start, $query, $qfield,
					$table_c);
				station_entry('', 'add', $order, $sort, $filter,
					$start, $query, $qfield, $add_c);
				station_entry($con, $act, $order, $sort, $filter,
					$start, $query, $qfield, $other_c);
			}
			break;
		case 'add':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_metar');
				$GLOBALS['phpgw']->db->query("insert into phpgw_weather_metar (metar_station, metar_city, metar_forecast, region_id)"
					."values ('"
					.$metar_station."','"
					.$metar_city."','"
					.$metar_forecast."','"
					.$region_id."')");
				$GLOBALS['phpgw']->db->unlock();
			}
			station_table($order, $sort, $filter, $start, $query, $qfield,$table_c);
			station_entry('', 'add', $order, $sort, $filter,
				$start, $query, $qfield, $add_c);
			break;
		default:
			station_table($order, $sort, $filter, $start, $query, $qfield,$table_c);
			station_entry('', 'add', $order, $sort, $filter,
				$start, $query, $qfield, $add_c);
			break;
	}

	$stations_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$stations_tpl->set_unknowns('remove');
	$stations_tpl->set_file(array(
		'message'  => 'message.common.tpl',
		'stations' => 'admin.datalist.tpl'
	));
	$stations_tpl->set_var(array(
		'messagename' => $message,
		'title'       => $title,
		'done_url'    => $doneurl,
		'done_label'  => $done_label,
		'data_table'  => $table_c,
		'add_form'    => $add_c,
		'other_form'  => $other_c
	));

	$stations_tpl->parse(message_part, 'message');
	$message_c = $stations_tpl->get('message_part');

	$stations_tpl->parse(body_part, 'stations');
	$body_c = $stations_tpl->get('body_part');

	/**************************************************************************
	* pull it all together
	*************************************************************************/
	$body_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$body_tpl->set_unknowns('remove');
	$body_tpl->set_file('body', 'admin.common.tpl');
	$body_tpl->set_var(array(
		'admin_message' => $message_c,
		'admin_body'    => $body_c
	));
	$body_tpl->parse('BODY', 'body');
	$body_tpl->p('BODY');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
