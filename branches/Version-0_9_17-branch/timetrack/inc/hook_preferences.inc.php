<?php
  /**************************************************************************\
  * phpGroupWare - Time Track Preferences Hook File                          *
  * http://www.phpgroupware.org                                              *
  * This file written by Bob Schader <bobs@product-des.com>                  *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id: hook_preferences.inc.php 9111 2002-01-12 15:46:47Z milosch $ */
{
	$title = $appname;
	$file = Array(
		'Preferences'     => $GLOBALS['phpgw']->link('/timetrack/preferences.php')
//		'Grant Access'    => $GLOBALS['phpgw']->link('/index.php','menuaction=preferences.uiaclprefs.index&acl_app='.$appname),
//		'Edit categories' => $GLOBALS['phpgw']->link('/preferences/categories.php','cats_app='.$appname.'&cats_level=True&global_cats=True')
	);
	display_section($appname,$title,$file);
} 
?>
