<?php
	/**************************************************************************\
	* phpGroupWare - Comic Prefs                                               *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_preferences.inc.php,v 1.6 2001/11/23 20:04:21 milosch Exp $ */
   
{
// Only Modify the $file and $title variables.....
	$title = 'Daily Comic';
	$file = Array(
		'Preferences' => $GLOBALS['phpgw']->link('/comic/preferences.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
