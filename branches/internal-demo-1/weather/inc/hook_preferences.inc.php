<?php
  /**************************************************************************\
  * phpGroupWare - Weather Prefs                                             *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
/* $Id: hook_preferences.inc.php 8348 2001-11-21 03:22:09Z skeeter $ */
   
{
// Only Modify the $file and $title variables.....
	$title = 'Weather Center';
	$file = Array(
		'Weather Preferences'	=> $GLOBALS['phpgw']->link('/weather/preferences.php')
	);
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
