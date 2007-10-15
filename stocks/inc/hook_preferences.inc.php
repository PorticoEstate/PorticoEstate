<?php
	/**************************************************************************\
	* phpGroupWare - Stock Quotes                                              *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: hook_preferences.inc.php,v 1.11 2007/01/24 16:13:29 Caeies Exp $ */

	{
		$file = Array
		(
			'Preferences' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'stocks.uistock.preferences'))
		);

//Do not modify below this line

		display_section($appname,$file);
	}
?>

