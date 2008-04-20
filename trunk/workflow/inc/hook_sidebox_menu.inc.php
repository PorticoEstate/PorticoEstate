<?php
    /**************************************************************************\
    * phpGroupWare - Knowledge Base                                              *
    * http://www.phpgroupware.org                                                *
    * -----------------------------------------------                          *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

	/* $Id: hook_sidebox_menu.inc.php 19830 2005-11-14 19:37:58Z regis_glc $ */
{
	$apptitle = $GLOBALS['phpgw_info']['apps'][$appname]['title'];
	// Configuration
	$file = Array();
	$menu_title = lang('%1 Configuration', $apptitle);
	// checking for workflow admin acl
	if ( ($GLOBALS['phpgw']->acl->check('admin_workflow',1,'workflow')) || ($GLOBALS['phpgw']->acl->check('run',1,'admin')) )
	{
		$file['Admin Processes'] 	= $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_adminprocesses.form');
		$file['Default config values'] 	= $GLOBALS['phpgw']->link('/index.php',array(
			'menuaction' 	=> 'admin.uiconfig.index',
			'appname' 	=> $appname,
		));
		$file['Global Categories'] = $GLOBALS['phpgw']->link('/index.php', array(
			'menuaction'    =>      'admin.uicategories.index',
			'appname'       =>      $appname,
		));

	}
	$file['Workflow Preferences'] =  $GLOBALS['phpgw']->link('/index.php', array(
		'menuaction'    => 'preferences.uisettings.index',
		'appname'       =>  $appname
	));
	display_sidebox($appname,$menu_title,$file);

	//Monitoring
	//checking for workflow monitoring acl
	if ( ($GLOBALS['phpgw']->acl->check('monitor_workflow',1,'workflow')) || ($GLOBALS['phpgw']->acl->check('run',1,'admin')) )
	{
		$file = Array();
		$menu_title 		= lang('%1 Monitoring', $apptitle);
		$file['Monitors'] 	= $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_monitors.form');
		display_sidebox($appname,$menu_title,$file);
	}

	// no acl
	$file = Array();
	$menu_title = lang('%1 Menu', $apptitle);
	$file['New Instance']      = $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_useropeninstance.form');
	$file['Global activities'] = $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_useractivities.form&show_globals=1');
	$file['My Processes']      = $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_userprocesses.form');
	$file['My Activities']     = $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_useractivities.form');
	$file['My Instances']      = $GLOBALS['phpgw']->link('/index.php','menuaction=workflow.ui_userinstances.form');

	display_sidebox($appname,$menu_title,$file);
}
?>
