<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir LDAP Servers                                     *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: servers.php 8325 2001-11-19 16:08:46Z milosch $ */

	$GLOBALS['phpgw_info']["flags"] = array(
		'currentapp' => 'eldaptir',
		'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();

	}

	$GLOBALS['phpgw']->template->set_file(array('server_list_t' => 'listservers.tpl'));
	$GLOBALS['phpgw']->template->set_block('server_list_t','server_list','list');

	$common_hidden_vars = "<input type=\"hidden\" name=\"sort\" value=\"$sort\">\n"
		. "<input type=\"hidden\" name=\"order\" value=\"$order\">\n"
		. "<input type=\"hidden\" name=\"query\" value=\"$query\">\n"
		. "<input type=\"hidden\" name=\"start\" value=\"$start\">\n"
		. "<input type=\"hidden\" name=\"filter\" value=\"$filter\">\n";

	$GLOBALS['phpgw']->template->set_var('lang_action',lang('Server List'));
	$GLOBALS['phpgw']->template->set_var('add_action',$GLOBALS['phpgw']->link('/eldaptir/addserver.php'));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
	$GLOBALS['phpgw']->template->set_var('title_servers',lang('LDAP Servers'));
	$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
	$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/eldaptir/servers.php'));
	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
	$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/admin/index.php'));

	if (! $start) { $start = 0; }

	if (!$sort)
	{
		$sort = "ASC";
	}
	if ($order)
	{
		$ordermethod = "order by $order $sort ";
	}
	else
	{
		$ordermethod = " order by name asc ";
	}

	if ($query)
	{
		$querymethod = " WHERE name like '%$query%' OR basedn like '%$query%'";
	}

	$db2 = $GLOBALS['phpgw']->db;

	$sql = "SELECT * FROM phpgw_eldaptir_servers $querymethod $ordermethod";
	$db2->query($sql,__LINE__,__FILE__);
	$total_records = $db2->num_rows();
	$GLOBALS['phpgw']->db->limit_query($sql,$start,__LINE__,__FILE__);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$servers[] = array(
			'id'         => $GLOBALS['phpgw']->db->f('id'),
			'name'       => $GLOBALS['phpgw']->db->f('name'),
			'type'       => $GLOBALS['phpgw']->db->f('type'),
			'rootdn'     => $GLOBALS['phpgw']->db->f('rootdn'),
			'basedn'     => $GLOBALS['phpgw']->db->f('basedn'),
			'is_default' => $GLOBALS['phpgw']->db->f('is_default')
		);
	}

	$left = $GLOBALS['phpgw']->nextmatchs->left('/eldaptir/servers.php',$start,$total_records);
	$right = $GLOBALS['phpgw']->nextmatchs->right('/eldaptir/servers.php',$start,$total_records);
	$GLOBALS['phpgw']->template->set_var('left',$left);
	$GLOBALS['phpgw']->template->set_var('right',$right);
	$hits = $GLOBALS['phpgw']->nextmatchs->show_hits($total_records,$start);
	$GLOBALS['phpgw']->template->set_var('lang_showing',$hits);

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']["theme"][th_bg]);
	$GLOBALS['phpgw']->template->set_var('sort_name',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'name',$order,'/eldaptir/servers.php',lang('Name')));
	$GLOBALS['phpgw']->template->set_var('sort_type',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'type',$order,'/eldaptir/servers.php',lang('Type')));
	$GLOBALS['phpgw']->template->set_var('sort_basedn',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'basedn',$order,'/eldaptir/servers.php',lang('basedn')));
	$GLOBALS['phpgw']->template->set_var('sort_rootdn',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'rootdn',$order,'/eldaptir/servers.php',lang('rootdn')));
	$GLOBALS['phpgw']->template->set_var('lang_default',lang('Default'));
	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

	for ($i=0;$i<count($servers);$i++)
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);
		$server_id = $servers[$i]['id'];
		$is_default = $servers[$i]['is_default'];
		$server_name  = $GLOBALS['phpgw']->strip_html($servers[$i]['name']);
		$server_type  = $GLOBALS['phpgw']->strip_html($servers[$i]['type']);
		$server_basedn = $GLOBALS['phpgw']->strip_html($servers[$i]['basedn']);
		$server_rootdn = $GLOBALS['phpgw']->strip_html($servers[$i]['rootdn']);
		if (!$server_basedn) { $server_basedn= '&nbsp;'; }

		$GLOBALS['phpgw']->template->set_var(array(
			'server_name'   => $server_name,
			'server_type'   => $server_type,
			'server_basedn' => $server_basedn,
			'server_rootdn' => $server_rootdn
		));
		if ($is_default)
		{
			$GLOBALS['phpgw']->template->set_var('is_default',lang('Yes'));
		}
		else
		{
			$GLOBALS['phpgw']->template->set_var('is_default',lang('No'));
		}

		$GLOBALS['phpgw']->template->set_var('edit',$GLOBALS['phpgw']->link('/eldaptir/editserver.php',"server_id=$server_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
		$GLOBALS['phpgw']->template->set_var('lang_edit_entry',lang('Edit'));

		$GLOBALS['phpgw']->template->set_var('delete',$GLOBALS['phpgw']->link('/eldaptir/deleteserver.php',"server_id=$server_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
		$GLOBALS['phpgw']->template->set_var('lang_delete_entry',lang('Delete'));
		$GLOBALS['phpgw']->template->parse('list','server_list',True);
	}

	$GLOBALS['phpgw']->template->parse('out','server_list_t',True);
	$GLOBALS['phpgw']->template->p('out');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
