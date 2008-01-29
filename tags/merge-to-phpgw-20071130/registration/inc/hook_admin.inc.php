<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $ */

	$file = array
	(
		'Site Configuration'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'registration') ),
		'Manage Fields'			=> $GLOBALS['phpgw']->link ('/index.php', array('menuaction' => 'registration.uimanagefields.admin') )
	);
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
?>
