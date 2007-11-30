<?php
 /**********************************************************************\
 * phpGroupWare - InfoLog						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Ralf Becker - <RalfBecker@outdoor-training.de>	*
 * Based on ToDo Written by Joseph Engo <jengo at phpgroupware.org>	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id: hook_preferences.inc.php 17949 2007-02-13 15:02:07Z sigurdne $ */

{
// Only Modify the $file and $title variables.....
	$file = array(
		'Preferences'     => $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=>$appname)),
		'Grant Access'    => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uiaclprefs.index','acl_app'=>$appname)),
		'Edit Categories' => $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index','cats_app'=>$appname,'cats_level'=>'True','global_cats'=>'True'))
	);
//Do not modify below this line
	display_section($appname,lang($appname),$file);	// leave 2. $appname for compatibilty with .14
}

?>
