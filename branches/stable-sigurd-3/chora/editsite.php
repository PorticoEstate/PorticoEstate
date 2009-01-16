<?php
  /**************************************************************************\
  * phpGroupWare - Admin                                                     *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

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

	if(!$site_id)
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/chora/sites.php',"sort=$sort&order=$order&query=$query&start=$start"
			. "&filter=$filter"));
	}

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('form' => 'site_form.tpl'));
	$t->set_block('form','add','addhandle');
	$t->set_block('form','edit','edithandle');

	$hidden_vars = "<input type=\"hidden\" name=\"sort\" value=\"$sort\">\n"
		. "<input type=\"hidden\" name=\"order\" value=\"$order\">\n"
		. "<input type=\"hidden\" name=\"query\" value=\"$query\">\n"
		. "<input type=\"hidden\" name=\"start\" value=\"$start\">\n"
		. "<input type=\"hidden\" name=\"filter\" value=\"$filter\">\n"
		. "<input type=\"hidden\" name=\"site_id\" value=\"$site_id\">\n";

	if($submit)
	{
		$errorcount = 0;
		if (!$site_name) { $error[$errorcount++] = lang('Please enter a name for that site !'); }

		$GLOBALS['phpgw']->db->query("SELECT count(*) from phpgw_chora_sites WHERE name='$site_name' AND id !='$site_id'");
		$GLOBALS['phpgw']->db->next_record();
		if($GLOBALS['phpgw']->db->f(0) != 0)
		{
			$error[$errorcount++] = lang('That site name has been used already !');
		}

		$site_name     = addslashes($site_name);
		$site_title    = addslashes($site_title);
		$site_location = addslashes($site_location);
		$site_intro    = addslashes($site_intro);

		if($is_default == 'on')
		{
			$default_checked = True;
		}
		else
		{
			$default_checked = 0;
		}

		if(!$error)
		{
			if($default_checked)
			{
				$GLOBALS['phpgw']->db->query("UPDATE phpgw_chora_sites SET is_default=0");
			}
			$GLOBALS['phpgw']->db->query("UPDATE phpgw_chora_sites SET"
				. " name='" . $site_name
				. "',title='" . $site_title
				. "',location='" . $site_location
				. "',intro='" . $site_intro
				. "',is_default=". $default_checked
				. " WHERE id=" . $site_id);
		}
	}

	if ($errorcount) { $t->set_var('message',$GLOBALS['phpgw']->common->error_list($error)); }
	if (($submit) && (! $error) && (! $errorcount)) { $t->set_var('message',lang('Repository %1 has been updated !',$site_name)); }
	if ((! $submit) && (! $error) && (! $errorcount)) { $t->set_var('message',''); }

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_chora_sites WHERE id=".$site_id);
	while($GLOBALS['phpgw']->db->next_record())
	{
		$sites[] = array(
			'id'          => $GLOBALS['phpgw']->db->f('id'),
			'name'        => $GLOBALS['phpgw']->db->f('name'),
			'location'    => $GLOBALS['phpgw']->db->f('location'),
			'title'       => $GLOBALS['phpgw']->db->f('title'),
			'intro'       => $GLOBALS['phpgw']->db->f('intro'),
			'is_default'  => $GLOBALS['phpgw']->db->f('is_default')
		);
	}

	$t->set_var('title_sites',lang('Edit repository'));
	$t->set_var('actionurl',$GLOBALS['phpgw']->link('/chora/editsite.php'));
	$t->set_var('deleteurl',$GLOBALS['phpgw']->link('/chora/deletesite.php',"site_id=$site_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
	$t->set_var('doneurl',$GLOBALS['phpgw']->link('/chora/sites.php',"start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));

	$t->set_var('hidden_vars',$hidden_vars);
	$t->set_var('lang_name',lang('Repository name'));
	$t->set_var('lang_title',lang('Repository title'));
	$t->set_var('lang_location',lang('Repository location'));
	$t->set_var('lang_intro',lang('Repository description file'));
	$t->set_var('lang_done',lang('Done'));
	$t->set_var('lang_default',lang('Default'));
	$t->set_var('lang_edit',lang('Edit'));
	$t->set_var('lang_delete',lang('Delete'));

	$site_id = $sites[0]['id'];

	$t->set_var('site_name',$GLOBALS['phpgw']->strip_html($sites[0]['name']));
	$t->set_var('site_title',$GLOBALS['phpgw']->strip_html($sites[0]['title']));
	$t->set_var('site_location',$GLOBALS['phpgw']->strip_html($sites[0]['location']));
	$t->set_var('site_intro',$GLOBALS['phpgw']->strip_html($sites[0]['intro']));

	if($sites[0]['is_default'])
	{
		$t->set_var('is_default',' checked');
	}
	else
	{
		$t->set_var('is_default','');
	}

	$t->set_var('edithandle','');
	$t->set_var('addhandle','');

	$t->pparse('out','form');
	$t->pparse('edithandle','edit');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
