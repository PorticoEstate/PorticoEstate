<?php
	/**************************************************************************\
	* phpGroupWare - Time Track Admin Hook File                                *
	* http://www.phpgroupware.org                                              *
	* This file written by Bob Schader <bobs@product-des.com>                  *
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
			'Edit Location Table'		=> $GLOBALS['phpgw']->link('/timetrack/admin1.php'),
			'Edit Job Status ID Table'	=> $GLOBALS['phpgw']->link('/timetrack/admin2.php'),
			'Edit Work Catagory Table'	=>	$GLOBALS['phpgw']->link('/timetrack/admin3.php'),
			'Site Setup'				=> $GLOBALS['phpgw']->link('/timetrack/admin5.php')
		);
//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
