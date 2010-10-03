<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */
{
// Only Modify the $file and $title variables.....
	$title = $appname;
	$file = array
	(
		'Site Configuration'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'calendar') ),
		'Custom fields and sorting'		=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicustom_fields.index') ),
		'Calendar Holiday Management'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiholiday.admin') ),
		'Global Categories'				=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'calendar') )
	);
//Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
}
?>
