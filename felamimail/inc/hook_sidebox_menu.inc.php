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

  /* $Id: hook_sidebox_menu.inc.php 17724 2006-12-18 20:28:00Z sigurdne $ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');
	$file[] = Array(
		'text'	=> 'Compose',
		'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uicompose.compose')));
		#'_NewLine_'=>'', // give a newline
		#'INBOX'=>$GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uifelamimail.index'))

	display_sidebox($appname,$menu_title,$file);
	unset($file);

	if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$sieveLinkData = array
		(
			'menuaction'	=> 'felamimail.uisieve.mainScreen',
			'action'	=> 'updateFilter'
		);
                                        
		
		$file[] = array('text'	=> 'Preferences',
				'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=>'felamimail')));
		$file[] = array('text'	=> 'Manage Sieve',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',$sieveLinkData));
		$file[] = array('text'	=> 'Manage Folders',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uipreferences.listFolder')));
		
		display_sidebox($appname,$menu_title,$file);
		unset($file);
	}

	if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file[] = array('text'	=> 'Configuration',
				'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiconfig.index','appname'=>'felamimail')));
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
