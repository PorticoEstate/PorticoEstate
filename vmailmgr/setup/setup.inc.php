<?php
	/**************************************************************************\
	* phpGroupWare - VMailMgr                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['vmailmgr']['name']      = 'vmailmgr';
	$setup_info['vmailmgr']['version']   = '0.9.15.001';
	$setup_info['vmailmgr']['app_order'] = 30;
	$setup_info['vmailmgr']['enable']    = 2;
	$setup_info['vmailmgr']['app_group']	= 'systools';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['vmailmgr']['hooks'][] = 'admin';
	$setup_info['vmailmgr']['hooks'][] = 'addaccount';
	$setup_info['vmailmgr']['hooks'][] = 'deleteaccount';
	$setup_info['vmailmgr']['hooks'][] = 'changepassword';

	/* Dependacies for this app to work */
	$setup_info['vmailmgr']['depends'][] = array(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16','0.9.17','0.9.18')
	);
?>
