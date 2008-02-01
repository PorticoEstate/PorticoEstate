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
 /* $Id: hook_admin.inc.php 17949 2007-02-13 15:02:07Z sigurdne $ */

	{
		$file = Array
		(
			'Site configuration' => $GLOBALS['phpgw']->link('/index.php',array(
				'menuaction' => 'infolog.uiinfolog.admin' )),
			'Global Categories'  => $GLOBALS['phpgw']->link('/index.php',array(
				'menuaction' => 'admin.uicategories.index',
				'appname'    => $appname,
				'global_cats'=> True)),
			'Custom fields, typ and status' => $GLOBALS['phpgw']->link('/index.php',array(
				'menuaction' => 'infolog.uicustomfields.edit')),
			'CSV-Import'         => $GLOBALS['phpgw']->link('/infolog/csv_import.php')
		);

//Do not modify below this line
		if ( isset($GLOBALS['phpgw']->common->public_functions['display_mainscreen']) )
		{
			$GLOBALS['phpgw']->common->display_mainscreen($appname,$file);
		}
		else
		{
			display_section($appname,lang($appname),$file);	// for .14/6
		}
	}
?>
