<?php
 /**********************************************************************\
 * phpGroupWare - JavaSSH						*
 * http://www.phpgroupware.org						*
 * This program is part of the GNU project, see http://www.gnu.org/	*
 *									*
 * Copyright 2002, 2003 Free Software Foundation, Inc.			*
 *									*
 * Originally Written by Dave Hall - <skwashd at phpgroupware.org>	*
 * --------------------------------------------				*
 *  Development Sponsored by Advantage Business Systems - abcsinc.com	*
 * --------------------------------------------				*
 * This program is Free Software; you can redistribute it and/or modify *
 * it under the terms of the GNU General Public License as published by *
 * the Free Software Foundation; either version 2 of the License, or 	*
 * at your option) any later version.					*
 \**********************************************************************/
 /* $Id$ */

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'currentapp'	=> 'javassh',
		'noheader'	=> True,
		'noappheader'	=> True,
		'nonavbar'	=> True,
		'noappfooter'	=> True,
		'nofooter'	=> True,
	);
	include('../header.inc.php');
	
	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'javassh.ui_jssh.index'));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
