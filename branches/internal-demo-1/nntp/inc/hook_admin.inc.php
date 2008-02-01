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
	/* $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $ */

	{
		$file = Array
		(
			'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'nntp') ),
			'Network News'			=> $GLOBALS['phpgw']->link('/nntp/admin.php')
		);
//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
