<?php
	/**************************************************************************\
	* phpGroupWare                                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_admin.inc.php 12493 2003-04-23 01:52:18Z ceb $ */

	{
		$file = Array
		(
			'Edit LDAP Servers List' => $GLOBALS['phpgw']->link('/eldaptir/servers.php')
		);

//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
