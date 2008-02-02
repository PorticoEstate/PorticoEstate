<?php
	/*************************************************************************\
	* phpGroupWare - SiteMgr Preferences						              *
	* http://www.phpgroupware.org											  *
	* --------------------------------------------							  *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.											  *
	\*************************************************************************/
	/* $Id$ */

	{
// Only Modify the $file and $title variables.....

		$title = 'Web Content Manager';
		$file = Array
		(
			'Manage Categories'    => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Categories_UI.manage'),
			'Manage Pages' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Pages_UI.manage')
		);

//Do not modify below this line
		display_section($appname,$title,$file);
	}
?>
