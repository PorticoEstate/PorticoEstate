<?php
	/**************************************************************************\
	* phpGroupWare - MediaDB Prefs                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */
   
{
// Only Modify the $file and $title variables.....
	$title = 'Media DB';
	$file = Array(
		'Category Table Preferences' => $GLOBALS['phpgw']->link('/mediadb/category.php','cat_app='.$appname),
		'Request Preferences'        => $GLOBALS['phpgw']->link('/mediadb/request.php','act=pref')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
