<?php
	/**************************************************************************\
	* phpGroupWare - MediaDB Admin Hook File                                   *
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
			'Edit Artists'		=> $GLOBALS['phpgw']->link('/mediadb/artist.php'),
			'Edit Categories'	=> $GLOBALS['phpgw']->link('/mediadb/category.php'),
			'Edit Features'		=> $GLOBALS['phpgw']->link('/mediadb/feature.php'),
			'Edit Formats'		=> $GLOBALS['phpgw']->link('/mediadb/format.php'),
			'Edit Genres'		=> $GLOBALS['phpgw']->link('/mediadb/genre.php'),
			'Edit Publishers'	=> $GLOBALS['phpgw']->link('/mediadb/publisher.php'),
			'Edit Ratings'		=> $GLOBALS['phpgw']->link('/mediadb/rating.php')
		);

//Do not modify below this line
		$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
	}
?>
