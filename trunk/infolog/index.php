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
 /* $Id$ */
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'	=> 'infolog', 
		'noheader'		=> True,
		'nonavbar'		=> True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php',array(
		'menuaction' => 'infolog.uiinfolog.index',
		'filter'     => isset($GLOBALS['phpgw_info']['user']['preferences']['infolog']['defaultFilter']) ? $GLOBALS['phpgw_info']['user']['preferences']['infolog']['defaultFilter'] : ''
	));
	$GLOBALS['phpgw']->common->phpgw_exit();
?>
