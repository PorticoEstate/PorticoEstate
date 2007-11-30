<?php
	/**************************************************************************\
	* phpGroupWare - Daily Comics Admin Hook File                              *
	* http://www.phpgroupware.org                                              *
	* This file written by Sam Wynn <neotexan@wynnsite.com>                    *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $ */
{
// Only Modify the $file and $title variables.....
	$file = array
	(
		'Global Options'	=> $GLOBALS['phpgw']->link('/comic/admin_options.php'),
		'Global Comics'		=> $GLOBALS['phpgw']->link('/comic/admin_comics.php'),
		'Reset Comic Data'	=> $GLOBALS['phpgw']->link('/comic/admin_comics_reset.php')
	);
//Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
}
?>
