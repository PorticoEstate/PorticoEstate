<?php
	/**************************************************************************\
	* phpGroupWare - Project Prefs                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: hook_preferences.inc.php 14131 2003-12-01 16:31:35Z ceb $ */

	{
		$title = $appname;
		$file = Array
		(
			'Preferences'     => $GLOBALS['phpgw']->link('/index.php','menuaction=bookkeeping.uibookkeeping.preferences'),
			'Grant Access'    => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname),
			'Edit categories' => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uicategories.index&cats_app=projects&cats_level=True&global_cats=True')
		);
		display_section($appname,$title,$file);
	}
?>
