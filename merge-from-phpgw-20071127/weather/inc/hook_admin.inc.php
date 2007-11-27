<?php
	/**************************************************************************\
	* phpGroupWare - Weather Center Admin Hook File                            *
	* http://www.phpgroupware.org                                              *
	* This file written by Sam Wynn <neotexan@wynnsite.com>                    *
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
			'Global Options'	=> $GLOBALS['phpgw']->link('/weather/admin_options.php'),
			'Site Links'		=> $GLOBALS['phpgw']->link('/weather/admin_links.php'),
			'Metar Stations'	=> $GLOBALS['phpgw']->link('/weather/admin_stations.php'),
			'Metar Regions'		=> $GLOBALS['phpgw']->link('/weather/admin_regions.php')
		);
//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
