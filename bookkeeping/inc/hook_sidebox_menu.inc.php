<?php
	/**************************************************************************\
	* phpGroupWare - projects's Sidebox-Menu for idots-template                *
	* http://www.phpgroupware.org                                              *
	* Written by Pim Snel <pim@lingewoud.nl>                                   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id$ */
	{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */
 		/* this is majorly broken so commenting it out for now, feel free to fix it

		$appname = 'bookkeeping';
		$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Menu');

		$file = array
		(
			'Billing'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uibilling.list_projects&action=mains'),
			'Deliveries'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=projects.uideliveries.list_projects&action=mains')
		);

		display_sidebox($appname,$menu_title,$file);

		if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
		{
			$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Preferences');
			$file = Array(
				'Preferences'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibookkeeping.preferences'),
				'Grant Access'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname),
				'Edit categories'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=bookkeeping&cats_level=True&global_cats=True')
			);
			display_sidebox($appname,$menu_title,$file);
		}

		if ($GLOBALS['phpgw_info']['user']['apps']['admin'])
		{
			$menu_title = $GLOBALS['phpgw_info']['apps'][$appname]['title'] . ' '. lang('Administration');
			$file = Array
			(
				'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uiconfig.index&appname=' . $appname),
				'Global Categories'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=admin.uicategories.index&appname=' . $appname)
			);
			display_sidebox($appname,$menu_title,$file);
		}

		*/
	}
?>
