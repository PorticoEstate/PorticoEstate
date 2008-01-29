<?php
	/**************************************************************************\
	* phpGroupWare - Forum                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/

	/* $Id: hook_preferences.inc.php 17909 2007-01-24 17:26:17Z Caeies $ */
{
	display_section('forum','Forum',array(
		'Preferences' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'forum'))
	));
}
?>
