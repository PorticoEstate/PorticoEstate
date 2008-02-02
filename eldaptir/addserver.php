<?php
  /**************************************************************************\
  * phpGroupWare - eldaptir                                                  *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eldaptir';
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();

	}

	$GLOBALS['phpgw']->template->set_file(array('form' => 'server_form.tpl'));
	$GLOBALS['phpgw']->template->set_block('form','add','addhandle');
	$GLOBALS['phpgw']->template->set_block('form','edit','edithandle');

	if ($submit)
	{
		$errorcount = 0;

		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_eldaptir_servers WHERE name='".$server_name."'");
		if ($GLOBALS['phpgw']->db->next_record())
		{
			$error[$errorcount++] = lang('That server name has been used already !');
		}

		if (!$server_name)
		{
			$error[$errorcount++] = lang('Please enter a name for that server !');
		}

		if (! $error)
		{
			if ($is_default == 'on')
			{
				$default_checked = True;
			}
			else
			{
				$default_checked = 0;
			}

			if($default_checked)
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_eldaptir_servers SET is_default=0");
			}

			$server_name     = addslashes($server_name);
			$server_type     = addslashes($server_type);
			$server_basedn   = addslashes($server_basedn);
			$server_rootdn   = addslashes($server_rootdn);
			$server_rootpw   = addslashes($server_rootpw);

			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_eldaptir_servers (name,type,basedn,rootdn,rootpw,is_default) VALUES ("
				. "'" . $server_name . "','" . $server_type . "','" . $server_basedn . "','"
				. $server_rootdn . "','" . $server_rootpw . "'," . $default_checked .")");
		}
	}

	if ($errorcount)
	{
		$GLOBALS['phpgw']->template->set_var('message',$GLOBALS['phpgw']->common->error_list($error));
	}
	if (($submit) && (! $error) && (! $errorcount))
	{
		$GLOBALS['phpgw']->template->set_var('message',lang('Server %1 has been added !', $server_name));
	}
	if ((! $submit) && (! $error) && (! $errorcount))
	{
		$GLOBALS['phpgw']->template->set_var('message','');
	}

	$GLOBALS['phpgw']->template->set_var('title_servers',lang('Add LDAP Server'));
	$GLOBALS['phpgw']->template->set_var('actionurl',$GLOBALS['phpgw']->link('/eldaptir/addserver.php'));
	$GLOBALS['phpgw']->template->set_var('doneurl',$GLOBALS['phpgw']->link('/eldaptir/servers.php'));
	$GLOBALS['phpgw']->template->set_var('hidden_vars','<input type="hidden" name="server_id" value="' . $server_id . '">');

	$GLOBALS['phpgw']->template->set_var('lang_name',lang('Server name'));
	$GLOBALS['phpgw']->template->set_var('lang_type',lang('Server type'));
	$GLOBALS['phpgw']->template->set_var('lang_basedn',lang('Server basedn'));
	$GLOBALS['phpgw']->template->set_var('lang_rootdn',lang('Server rootdn'));
	$GLOBALS['phpgw']->template->set_var('lang_rootpw',lang('Server rootpw'));

	$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add'));
	$GLOBALS['phpgw']->template->set_var('lang_default',lang('Default'));
	$GLOBALS['phpgw']->template->set_var('lang_reset',lang('Clear Form'));
	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));

	$GLOBALS['phpgw']->template->set_var('server_name',$server_name);
	$GLOBALS['phpgw']->template->set_var('server_basedn',$server_basedn);
	$GLOBALS['phpgw']->template->set_var('server_type',server_types($server_type));
	$GLOBALS['phpgw']->template->set_var('server_rootdn',$GLOBALS['phpgw']->strip_html($server_rootdn));
	$GLOBALS['phpgw']->template->set_var('server_rootpw',$GLOBALS['phpgw']->strip_html($server_rootpw));

	$GLOBALS['phpgw']->template->set_var('edithandle','');
	$GLOBALS['phpgw']->template->set_var('addhandle','');
	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->template->pparse('addhandle','add');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
