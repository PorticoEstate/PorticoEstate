<?php
  /**************************************************************************\
  * phpGroupWare - Admin                                                     *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: editserver.php 12863 2003-05-25 21:34:36Z gugux $ */

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eldaptir';
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();

	}

	if (! $server_id)
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/eldaptir/servers.php',"sort=$sort&order=$order&query=$query&start=$start"
			. "&filter=$filter"));
	}

	$GLOBALS['phpgw']->template->set_file(array('form' => 'server_form.tpl'));
	$GLOBALS['phpgw']->template->set_block('form','add','addhandle');
	$GLOBALS['phpgw']->template->set_block('form','edit','edithandle');

	$hidden_vars =
		  '<input type="hidden" name="sort"   value="' . $sort . '">' . "\n"
		. '<input type="hidden" name="order"  value="' . $order . '">' . "\n"
		. '<input type="hidden" name="query"  value="' . $query . '">' . "\n"
		. '<input type="hidden" name="start"  value="' . $start . '">' . "\n"
		. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n"
		. '<input type="hidden" name="server_id" value="' . $server_id . '">' . "\n";

	$submit = $HTTP_POST_VARS['submit'];
	if ($submit)
	{
		$errorcount = 0;
		if (!$server_name)
		{
			$error[$errorcount++] = lang('Please enter a name for that server !');
		}

		$GLOBALS['phpgw']->db->query("SELECT COUNT(*) from phpgw_eldaptir_servers WHERE name='$server_name' AND id !='$server_id'");
		$GLOBALS['phpgw']->db->next_record();
		if ($GLOBALS['phpgw']->db->f(0) != 0)
		{
			$error[$errorcount++] = lang('That server name has been used already !');
		}

		$server_name   = addslashes($server_name);
		$server_type   = addslashes($server_type);
		$server_basedn = addslashes($server_basedn);
		$server_rootdn = addslashes($server_rootdn);
/*
		if($server_rootpw)
		{
			$server_rootpw = $GLOBALS['phpgw']->common->encrypt_password($server_rootpw);
		}
*/
		if ($is_default == 'on')
		{
			$default_checked = True;
		}
		else
		{
			$default_checked = 0;
		}

		if (! $error)
		{
			if($server_rootpw)
			{
				$chgpass = "',rootpw='"    . $server_rootpw;
			}
			if($default_checked) $GLOBALS['phpgw']->db->query("UPDATE phpgw_eldaptir_servers SET is_default=0");
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_eldaptir_servers SET"
				. " name='"       . $server_name
				. "',type='"      . $server_type
				. "',basedn='"    . $server_basedn
				. "',rootdn='"    . $server_rootdn
				. $chgpass
				. "',is_default=" . $default_checked
				. " WHERE id="    . $server_id);
		}
	}

	if ($errorcount)
	{
		$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
	}
	if (($submit) && (! $error) && (! $errorcount))
	{
		$GLOBALS['phpgw']->template->set_var('message',lang('Server %1 has been updated !',$server_name));
	}
	if ((! $submit) && (! $error) && (! $errorcount))
	{
		$GLOBALS['phpgw']->template->set_var('message','');
	}

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_eldaptir_servers WHERE id=".$server_id);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$servers[] = array(
			'id'          => $GLOBALS['phpgw']->db->f('id'),
			'name'        => $GLOBALS['phpgw']->db->f('name'),
			'type'        => $GLOBALS['phpgw']->db->f('type'),
			'basedn'      => $GLOBALS['phpgw']->db->f('basedn'),
			'rootdn'      => $GLOBALS['phpgw']->db->f('rootdn'),
			'is_default'  => $GLOBALS['phpgw']->db->f('is_default')
		);
	}

	$GLOBALS['phpgw']->template->set_var('title_servers',lang('Edit Server'));
	$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/eldaptir/editserver.php'));
	$GLOBALS['phpgw']->template->set_var('deleteurl',$GLOBALS['phpgw']->link('/eldaptir/deleteserver.php',"server_id=$server_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
	$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/eldaptir/servers.php',"start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));

	$GLOBALS['phpgw']->template->set_var('hidden_vars',$hidden_vars);
	$GLOBALS['phpgw']->template->set_var('lang_name',lang('Server name'));
	$GLOBALS['phpgw']->template->set_var('lang_type',lang('Server type'));
	$GLOBALS['phpgw']->template->set_var('lang_basedn',lang('Server basedn'));
	$GLOBALS['phpgw']->template->set_var('lang_rootdn',lang('Server rootdn'));
	$GLOBALS['phpgw']->template->set_var('lang_rootpw',lang('Server rootpw'));

	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
	$GLOBALS['phpgw']->template->set_var('lang_default',lang('Default'));
	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));

	$server_id = $servers[0]['id'];

	$GLOBALS['phpgw']->template->set_var('server_name',$GLOBALS['phpgw']->strip_html($servers[0]['name']));
	$GLOBALS['phpgw']->template->set_var('server_type',server_types($servers[0]['type']));
	$GLOBALS['phpgw']->template->set_var('server_basedn',$GLOBALS['phpgw']->strip_html($servers[0]['basedn']));
	$GLOBALS['phpgw']->template->set_var('server_rootdn',$GLOBALS['phpgw']->strip_html($servers[0]['rootdn']));
	$GLOBALS['phpgw']->template->set_var('server_rootpw','');

	if ($servers[0]['is_default'])
	{
		$GLOBALS['phpgw']->template->set_var('is_default',' checked');
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('is_default','');
	}

	$GLOBALS['phpgw']->template->set_var('edithandle','');
	$GLOBALS['phpgw']->template->set_var('addhandle','');

	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->template->pparse('edithandle','edit');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
