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

  /* $Id$ */
{

 /*
	This hookfile is for generating an app-specific side menu used in the idots 
	template set.

	$menu_title speaks for itself
	$file is the array with link to app functions

	display_sidebox can be called as much as you like
 */

	$menu_title = lang($appname) . ' '. lang('Menu');
	$file = Array(
			array('text'  => 'New Entry',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.add'))),
			array('text'  => '_NewLine_'),
			array('text'  => 'Today',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.day'))),
			array('text'  => 'This week',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week'))),
			array('text'  => 'This week (detailed)',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week_new'))),
			array('text'  => 'This month',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.month'))),
			array('text'  => 'This year',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.year'))),
			array('text'  => '_NewLine_'), // give a newline
			array('text'  => 'Group Planner',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.planner'))),
			array('text'  => 'Daily Matrix View',
				'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.matrixselect'))),
			array('text'  => 'Import',
				'url' =>$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiicalendar.import'))),
	);
	display_sidebox($appname,$menu_title,$file);

	if ($GLOBALS['phpgw_info']['user']['apps']['preferences'])
	{
		$menu_title = lang('Preferences');
		$file = Array(
				array('text'  => 'Calendar preferences',
					'url' =>$GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'calendar'))),
				array('text'  => 'Grant Access',
					'url' =>$GLOBALS['phpgw']->link('/index.php',array
										(
											'menuaction' => 'preferences.uiaclprefs.index', 
											'acl_app' => 'calendar'
										))),
				array('text'  => 'Edit Categories',
					'url' =>$GLOBALS['phpgw']->link('/index.php', 
										array
										(
											'menuaction'	=> 'preferences.uicategories.index',
											'cats_app'	=> 'calendar',
											'cats_level'	=> '1',
											'global_cats'	=> 1
										))),
		);
		display_sidebox($appname,$menu_title,$file);
	}

	if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && $GLOBALS['phpgw_info']['user']['apps']['admin'])
	{
		$menu_title = lang('Administration');
		$file = Array(
				array('text'  => 'Configuration',
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index','appname'=>'calendar'))),
				array('text'  => 'Custom Fields',
					'url' =>$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicustom_fields.index'))),
				array('text'  => 'Holiday Management',
					'url' =>$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiholiday.admin'))),
				array('text'  => 'Global Categories',
					'url' =>$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'calendar'))),
		);
		display_sidebox($appname,$menu_title,$file);
	}
}
?>
