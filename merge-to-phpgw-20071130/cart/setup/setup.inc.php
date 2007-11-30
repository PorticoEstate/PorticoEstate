<?php
	/*************************************************************************\
	* phpGroupWare Setup - Shopping cart                                      *
	* http://www.phpgroupware.org                                             *
	* --------------------------------------------                            *
	* This program is free software; you can redistribute it and/or modify it *
	* under the terms of the GNU General Public License as published by the   *
	* Free Software Foundation; either version 2 of the License, or (at your  *
	* option) any later version.                                              *
	\*************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['cart']['name']		= 'cart';
	$setup_info['cart']['version']		= '0.8.1';
	$setup_info['cart']['app_order']	= 16;
	$setup_info['cart']['enable']		= 1;
	$setup_info['cart']['app_group']	= 'other';

	$setup_info['cart']['author'] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['cart']['license']  = 'GPL';
	$setup_info['cart']['description'] = 'An online shopping program.';

	$setup_info['cart']['maintainer'] = $setup_info['cart']['author'];

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['cart']['hooks'] = array
	(
		'manual'
	);

	/* Dependencies for this app to work */
	$setup_info['cart']['depends'][] = array
	(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);
?>
