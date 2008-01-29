<?php
  /**************************************************************************\
  * phpGroupWare - Chora                                                     *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
/* $Id: addsite.php 11830 2003-02-28 15:30:44Z ralfbecker $ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'chora';
	$GLOBALS['phpgw_info']['flags']['enable_config_class'] = True;
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('form' => 'site_form.tpl'));
	$t->set_block('form','add','addhandle');
	$t->set_block('form','edit','edithandle');

	if($submit)
	{
		$errorcount = 0;

		$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chora_sites WHERE name='".$site_name."'");
		if($GLOBALS['phpgw']->db->next_record())
		{
			$error[$errorcount++] = lang('That site name has been used already !');
		}

		if(!$site_name)
		{
			$error[$errorcount++] = lang('Please enter a name for that site !');
		}

		if(!$error)
		{
			if($is_default == 'on')
			{
				$default_checked = True;
			}
			else
			{
				$default_checked = 0;
			}

			if($default_checked)
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_chora_sites SET is_default=0");
			}

			$site_name     = addslashes($site_name);
			$site_title    = addslashes($site_title);
			$site_location = addslashes($site_location);
			$site_intro    = addslashes($site_intro);

			$GLOBALS['phpgw']->db->query("INSERT INTO phpgw_chora_sites (name,title,location,intro,is_default) VALUES ("
				. "'" . $site_name . "','" .$site_title . "','" .$site_location . "','" . $site_intro  . "'," . $default_checked .")");
		}
	}

	if ($errorcount) { $t->set_var('message',$GLOBALS['phpgw']->common->error_list($error)); }
	if (($submit) && (! $error) && (! $errorcount)) { $t->set_var('message',lang('Repository %1 has been added !', $site_name)); }
	if ((! $submit) && (! $error) && (! $errorcount)) { $t->set_var('message',''); }

	$t->set_var('title_sites',lang('Add CVS Repository'));
	$t->set_var('actionurl',$GLOBALS['phpgw']->link('/chora/addsite.php'));
	$t->set_var('doneurl',$GLOBALS['phpgw']->link('/chora/sites.php'));
	$t->set_var('hidden_vars','<input type="hidden" name="site_id" value="' . $site_id . '">');

	$t->set_var('lang_name',lang('Repository name'));
	$t->set_var('lang_title',lang('Repository title'));
	$t->set_var('lang_location',lang('Repository location'));
	$t->set_var('lang_intro',lang('Repository description file'));
	$t->set_var('lang_add',lang('Add'));
	$t->set_var('lang_default',lang('Default'));
	$t->set_var('lang_reset',lang('Clear Form'));
	$t->set_var('lang_done',lang('Done'));

	$t->set_var('site_name',$site_name);
	$t->set_var('site_title',$site_title);
	$t->set_var('site_location',$GLOBALS['phpgw']->strip_html($site_location));
	$t->set_var('site_intro',$GLOBALS['phpgw']->strip_html($sites[0]['intro']));

	$t->set_var('edithandle','');
	$t->set_var('addhandle','');
	$t->pparse('out','form');
	$t->pparse('addhandle','add');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
