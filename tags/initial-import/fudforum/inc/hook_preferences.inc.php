<?php
	/**************************************************************************\
	* phpGroupWare - FUDforum preferences/profil                               *
	* http://www.phpGroupWare.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_preferences.inc.php,v 1.2 2007/01/24 17:07:44 Caeies Exp $ */

	{
		$file = Array
		(
			'Preferences' => $GLOBALS['phpgw']->link('/fudforum/index.php', array('t' => 'register')),
		);

//Do not modify below this line
		display_section($appname,$file);
	}
?>
