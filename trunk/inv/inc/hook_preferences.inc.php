<?php
	/**************************************************************************\
	* phpGroupWare - Inventory Preferences                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	{
		$link_data = array
		(
			'menuaction'	=> 'preferences.uicategories.index',
			'cats_app'		=> $appname,
			'cats_level'	=> True,
			'extra'			=> 'tax,number'
		);

		$file = Array
		(
			'Preferences'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=inv.uiinvoice.preferences'),
			'Grant Access'		=> $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname),
			'Edit Categories'	=> $GLOBALS['phpgw']->link('/index.php',$link_data)
		);

//Do not modify below this line
		display_section($appname,$file);
	}
?>
