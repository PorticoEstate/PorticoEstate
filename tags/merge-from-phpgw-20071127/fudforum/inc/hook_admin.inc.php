<?php
	/**************************************************************************\
	* phpGroupWare - FUDforum administration                                   *
	* http://www.phpGroupWare.org                                              *
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
			'Site configuration' => $GLOBALS['phpgw']->link('/fudforum/adm/admglobal.php'),
		);

//Do not modify below this line
	$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
