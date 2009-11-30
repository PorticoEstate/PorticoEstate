<?php
	/*************************************************************************\
	* phpGroupWare - SiteMgr Preferences						                  *
	* http://www.phpgroupware.org											  *
	* --------------------------------------------							  *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.											  *
	\*************************************************************************/
	/* $Id$ */

	{
// Only Modify the $file variable.....

		$file = Array
		(
			'Define Websites' => $GLOBALS['phpgw']->link('/index.php','menuaction=sitemgr.Sites_UI.list_sites'),
		);

//Do not modify below this line
		if (intval(substr($GLOBALS['phpgw_info']['server']['versions']['phpgwapi'],4)) > 16)
		{
			$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
		}
		else
		{
			display_section($appname,$title,$file);
		}
	}
?>
