<?php
  /**************************************************************************\
  * phpGroupWare - Weather Center Metar Regions Admin                        *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

    /* $Id: admin_regions.php 8454 2001-12-03 18:01:51Z milosch $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'weather',
		'admin_header' => True,
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');
	include('inc/metar_regions.inc.php');

	$title      = lang('Weather Metar Regions');
	$done_label = lang('Done');
	$doneurl    = $GLOBALS['phpgw']->link('/admin/index.php');
	$message    = '';

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

	if ($region_name)
	{
		$region_name = ucwords($region_name);
	}

	switch($act)
	{
		case 'edit':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_region');
				$GLOBALS['phpgw']->db->query("update phpgw_weather_region set "
					."region_name='".$region_name."' "
					."where region_id='".$region_id."'");
				$GLOBALS['phpgw']->db->unlock();

				region_table($order, $sort, $filter, $start, $query, $table_c);
				region_entry('', 'add', $order, $sort, $filter,
					$start, $query, $add_c);
			}
			else
			{
				region_table($order, $sort, $filter, $start, $query, $table_c);
				region_entry('', 'add', $order, $sort, $filter,
				$start, $query, $add_c);
				region_entry($con, $act, $order, $sort, $filter,
					$start, $query, $other_c);
			}
			break;
		case 'delete':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_region');
				$GLOBALS['phpgw']->db->query("delete from phpgw_weather_region "
					."where region_id='".$region_id."'");
				$GLOBALS['phpgw']->db->unlock();

				region_table($order, $sort, $filter, $start, $query, $table_c);
				region_entry('', "add", $order, $sort, $filter,
				$start, $query, $add_c);
			}
			else
			{
				region_table($order, $sort, $filter, $start, $query, $table_c);
				region_entry('', 'add', $order, $sort, $filter,
					$start, $query, $add_c);
				region_entry($con, $act, $order, $sort, $filter,
					$start, $query, $other_c);
			}
			break;
		case 'add':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_weather_region');
				$GLOBALS['phpgw']->db->query("insert into phpgw_weather_region (region_name)"
					."values ('"
					.$region_name."')");
				$GLOBALS['phpgw']->db->unlock();
			}
			region_table($order, $sort, $filter, $start, $query, $table_c);
			region_entry('', 'add', $order, $sort, $filter,
			$start, $query, $add_c);
			break;
		default:
			region_table($order, $sort, $filter, $start, $query, $table_c);
			region_entry('', 'add', $order, $sort, $filter,
				$start, $query, $add_c);
			break;
	}

	$regions_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('weather'));
	$regions_tpl->set_unknowns('remove');
	$regions_tpl->set_file(array(
		'message' => 'message.common.tpl',
		'regions' => 'admin.datalist.tpl'
	));
	$regions_tpl->set_var(array(
		'messagename' => $message,
		'title'       => $title,

		'done_url'    => $doneurl,
		'done_label'  => $done_label,

		'data_table'  => $table_c,
		'add_form'    => $add_c,
		'other_form'  => $other_c
	));

	$regions_tpl->parse(message_part, 'message');
	$message_c = $regions_tpl->get('message_part');

	$regions_tpl->parse(body_part, 'regions');
	$body_c = $regions_tpl->get('body_part');

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
