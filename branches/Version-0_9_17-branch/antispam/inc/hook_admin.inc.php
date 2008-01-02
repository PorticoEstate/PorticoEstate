<?php
	/**************************************************************************\
	* phpGroupWare - Antispam                                                  *
	* http://www.phpgroupware.org                                              *
	* This application written by:                                             *
	*                             Marco Andriolo-Stagno <stagno@prosa.it>      *
	*                             PROSA <http://www.prosa.it>                  *
	* -------------------------------------------------------------------------*
	* Funding for this program was provided by http://www.seeweb.com           *
	* -------------------------------------------------------------------------*
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: hook_admin.inc.php 17106 2006-09-09 09:04:58Z skwashd $ */

	{
		$file = array
		(
			'DefaultRules'	=>	$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'antispam.checker.default_settings', 'id' => '@GLOBAL') ),
			'Rules'			=>	$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'antispam.main_manager.handle_rules') )
		);
	  	//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
