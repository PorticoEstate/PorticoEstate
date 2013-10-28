<?php
	/**************************************************************************\
	* phpGroupWare - PHPSysInfo                                                *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	* Originally Ported to phpGroupWare by Lars Knesche			   *
	*  Currently Maintained by Dave Hall - dave.hall@mbox.com.au		   *
	* --------------------------------------------				   *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$setup_info['phpsysinfo']['name']      = 'phpsysinfo';
	$setup_info['phpsysinfo']['title']     = 'phpsysinfo';
	$setup_info['phpsysinfo']['version']   = '3.1.7';
	$setup_info['phpsysinfo']['app_order'] = 99;
	$setup_info['phpsysinfo']['enable']    = 2;
	$setup_info['phpsysinfo']['tables']    =  array();

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['phpsysinfo']['hooks'] = array
	(
		'menu'	=> 'phpsysinfo.menu.get_menu',
	);


	/* Dependacies for this app to work */
	$setup_info['phpsysinfo']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
