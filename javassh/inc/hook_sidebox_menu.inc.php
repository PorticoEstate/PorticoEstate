<?php
  /**************************************************************************\
  * phpGroupWare - Calendar's Sidebox-Menu for idots-template                *
  * http://www.phpgroupware.org                                              *
  * Written by Pim Snel <pim@lingewoud.nl>                                   *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_sidebox_menu.inc.php 17826 2006-12-28 14:29:54Z skwashd $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$file = array();
	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$bo_jssh = createObject('javassh.bo_jssh');
	$servers = $bo_jssh->get_servers();
	if ( !count($servers))
	{
		if(isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
		{
			$file[] = array
			(
				'text'	=> 'no servers available - please add one',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'javassh.ui_jssh.admin_list'))
			);
		}
		else
		{
			$file = array('text' => 'javassh is not configured');
		}
		$servers = array();
	}
	foreach($servers as $server)
	{
		$file[] = array
		(
			'text'	=> $server['title'],
			'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'javassh.ui_jssh.connect', 'server'	=> $server['server_id']))
		);
	}
	display_sidebox($appname,$menu_title,$file);

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file = array
		(
			array
			(
				'text'	=> 'Site Configuration',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index&appname=javassh'))
			),
			
			array
			(
				'text'	=> 'Manage Servers',
				'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'javassh.ui_jssh.admin_list'))
			)
		);							
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
