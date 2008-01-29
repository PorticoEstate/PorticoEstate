<?php
  /**************************************************************************\
  * phpGroupWare - Weather Center Metar Region Functions                     *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: metar_regions.inc.php 12483 2003-04-22 20:34:50Z gugux $ */

	function region_table($order, $sort, $filter, $start, $query, &$table_c)
	{
		$edit_label   = lang('Edit');
		$delete_label = lang('Delete');

		if ($order)
		{
			$ordermethod = "ORDER BY $order $sort ";
		}
		else
		{
			$ordermethod = "ORDER BY region_name ASC ";
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

		if (!$query)
		{
			$GLOBALS['phpgw']->db->query("SELECT COUNT(*) FROM phpgw_weather_region ");
		}
		else
		{
			$GLOBALS['phpgw']->db->query("SELECT COUNT(*) FROM phpgw_weather_region "
				. "WHERE region_name LIKE '%$query%' "
			);
		}

		$GLOBALS['phpgw']->db->next_record();

		if ($GLOBALS['phpgw']->db->f(0) >
			$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
		{
			$match_comment = 
				lang('showing %1 - %2 of %3',($start + 1),
					($start +
					$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']),
					$GLOBALS['phpgw']->db->f(0)
				);
		}
		else
		{
			$match_comment = lang('showing %1',$GLOBALS['phpgw']->db->f(0));
		}

		$match_bar =
			$GLOBALS['phpgw']->nextmatchs->show_tpl('/weather/admin_regions.php',
				$start,$GLOBALS['phpgw']->db->f(0), '',
				'85%', $GLOBALS['phpgw_info']['theme']['th_bg'],0,0
			);
		$region_id_link_label =
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'region_id',$order,
				'/weather/admin_regions.php',
				lang('ID')
			);
		$region_link_label = 
			$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'region_name',$order,
				'/weather/admin_regions.php',
				lang('Region')
			);

		if (! $query)
		{
			$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_weather_region "
				.$ordermethod,
				$start
			);
		}
		else
		{
			$GLOBALS['phpgw']->db->limit_query("SELECT * FROM phpgw_weather_region "
				. "WHERE region_name LIKE '%$query%' "
				. $ordermethod,
				$start
			);
		}

		$table_tpl =
			CreateObject('phpgwapi.Template',
				$GLOBALS['phpgw']->common->get_tpl_dir('weather')
			);
		$table_tpl->set_unknowns('remove');
		$table_tpl->set_file(array(
			'table' => 'table.regions.tpl',
			'row'   => 'row.regions.tpl'
		));

		while ($GLOBALS['phpgw']->db->next_record()) 
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

			$region_id = $GLOBALS['phpgw']->db->f('region_id');
			$region_encoded = urlencode($region_id);

			if (! $region_id)
			{
				$region_id = '&nbsp;';
			}

			$region = $GLOBALS['phpgw']->db->f('region_name');
			if (! $region)
			{
				$region = '&nbsp;';
			}

			$table_tpl->set_var(array(
				'row_color'    => $tr_color,
				'region_id'    => $region_id,
				'region_name'  => $region,
				'edit_url'     => $GLOBALS['phpgw']->link('/weather/admin_regions.php',
					"con=".$region_encoded
					."&act=edit"
					."&start=$start"
					."&order=$order"
					."&filter=$filter"
					."&sort=$sort"
					."&query="
					.urlencode($query)
				),
				'edit_label'   => $edit_label,
				'delete_url'   => $GLOBALS['phpgw']->link('/weather/admin_regions.php',
					"con=".$region_encoded
					."&act=delete"
					."&start=$start"
					."&order=$order"
					."&filter=$filter"
					."&sort=$sort"
					."&query="
					.urlencode($query)
				),
				'delete_label' => $delete_label)
			);
			$table_tpl->parse('region_rows', 'row', True);
		}

		$table_tpl->set_var(array(
			'th_bg'                => $GLOBALS['phpgw_info']['theme']['th_bg'],
			'total_matchs'         => $match_comment,
			'next_matchs'          => $match_bar,
			'region_id_link_label' => $region_id_link_label,
			'region_link_label'    => $region_link_label,
			'edit_label'           => $edit_label,
			'delete_label'         => $delete_label,
			'action_url'           => $action_url,
			'action_label'         => lang($act),
			'reset_label'          => lang('Reset')
		));

		$table_tpl->parse('table_part', 'table');
		$table_c = $table_tpl->get('table_part');
	}

	function region_entry($con, $act, $order, $sort, $filter, $start, $query, &$form_c)
	{
		$action_url   = $GLOBALS['phpgw']->link(
			'/weather/admin_regions.php',
			"act=$act"
			."&start=$start&order=$order&filter=$filter"
			."&sort=$sort"
			."&query=".urlencode($query)
		);

		switch($act)
		{
			case 'add':
				$bg_color = $GLOBALS['phpgw_info']['theme']['th_bg'];
				break;
			case 'delete':
				$bg_color = $GLOBALS['phpgw_info']['theme']['bg07'];
				break;
			default:
				$bg_color = $GLOBALS['phpgw_info']['theme']['table_bg'];
				break;
		}

		$region_name = '';

		if ($con != '')
		{
			$GLOBALS['phpgw']->db->query("select * from phpgw_weather_region where region_id=$con");

			$GLOBALS['phpgw']->db->next_record();

			$region_name = $GLOBALS['phpgw']->db->f('region_name');
		}

		$modify_tpl =
			CreateObject('phpgwapi.Template',
				$GLOBALS['phpgw']->common->get_tpl_dir('weather')
			);
		$modify_tpl->set_unknowns('remove');
		$modify_tpl->set_file('form', 'form.regions.tpl');

		$modify_tpl->set_var(array(
			'bg_color'     => $bg_color,
			'region_id'    => $con,
			'region_label' => lang('Region'),
			'region_name'  => $region_name,
			'action_url'   => $action_url,
			'action_label' => lang($act),
			'reset_label'  => lang('Reset')
		));

		$modify_tpl->parse('form_part', 'form');
		$form_c = $modify_tpl->get('form_part');
	}
?>
