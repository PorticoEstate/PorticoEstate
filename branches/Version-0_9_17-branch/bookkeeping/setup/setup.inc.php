<?php
	/*************************************************************************\
	* phpGroupWare app (Bookkeeping)                                          *
	* http://www.phpgroupware.org                                             *
	* Written by Bettina Gille [ceb@phpgroupware.org]                         *
	* -----------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/
	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['bookkeeping']['name']		= 'bookkeeping';
	$setup_info['bookkeeping']['version']		= '0.5.1.001';
	$setup_info['bookkeeping']['app_order']		= 13;
	$setup_info['bookkeeping']['enable']		= 1;
	$setup_info['bookkeeping']['app_group']		= 'office';

	$setup_info['bookkeeping']['author']		= 'Bettina Gille';
	$setup_info['bookkeeping']['license']		= 'GPL';
	$setup_info['bookkeeping']['description']	= 'accounting programm for projects and products';
	$setup_info['bookkeeping']['maintainer']	= 'Bettina Gille';
	$setup_info['bookkeeping']['maintainer_email']	= 'ceb@phpgroupware.org';

	/*$setup_info['bookkeeping']['tables'] = array(); */

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['bookkeeping']['hooks'] = array
	(
		'admin',
		'preferences',
		'sidebox_menu'
	);

	/* Dependencies for this app to work */
	$setup_info['bookkeeping']['depends'][] = array
	(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bookkeeping']['depends'][] = array(
		 'appname' => 'projects',
		 'versions' => Array('0.8.7')
	);
?>
