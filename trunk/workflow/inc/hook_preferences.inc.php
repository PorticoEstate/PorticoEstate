<?php
  /**************************************************************************\
  * phpGroupWare                                                               *
  * http://www.phpgroupware.org                                                *
  * Written first by Joseph Engo <jengo@phpgroupware.org>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_preferences.inc.php 19830 2005-11-14 19:37:58Z regis_glc $ */

{
// Only Modify the $file and $title variables.....
	$title = $appname;
	# old syntax unavaible with the new preferences application $GLOBALS['phpgw']->link('/preferences/preferences.php','appname=workflow')
	$file = array(
		'Preferences'	=> $GLOBALS['phpgw']->link('/index.php', array(
			'menuaction'	=> 'preferences.uisettings.index',
			'appname'	=>  $appname
			))
	);

//	$workflowPreferences = ExecMethod('workflow.bopreferences.getPreferences');
//Do not modify below this line
	display_section($appname,$title,$file);
}
?>
