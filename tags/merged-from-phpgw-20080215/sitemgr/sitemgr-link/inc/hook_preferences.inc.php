<?php
 /**********************************************************************\
 * phpGroupWare - SiteMgr 						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Written by Dave Hall - skwashd at phpgroupware.org			*
 * --------------------------------------------			*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id$ */

{
	$file = array(
		'Preferences'     => $GLOBALS['phpgw']->link('/preferences/preferences.php','appname='.$appname),
	);
	display_section($appname,lang($appname),$file);
}

?>
