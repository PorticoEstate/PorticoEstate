<?php
	/**************************************************************************\
	* phpGroupWare - Setup                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/
	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	$setup_info['backup']['name']		= 'backup';
	$setup_info['backup']['version']	= '0.0.1.001';
	$setup_info['backup']['app_order']	= 72;
	$setup_info['backup']['enable'] 	= 1;
	$setup_info['backup']['app_group']	= 'systools';

	$setup_info['backup']['author'] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['backup']['license']  = 'GPL';
	$setup_info['backup']['description'] =
		'phpGroupWare data backup for sql, ldap and email.<br>
		An online configurable backup app to store data offline.';

	$setup_info['backup']['maintainer'] = $setup_info['backup']['author'];

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['backup']['hooks'] = array
	(
		'admin',
		'manual'
	);

	/* Dependencies for this app to work */
	$setup_info['backup']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['backup']['depends'][] = array
	(
		'appname'  => 'admin',
		'versions' => Array('0.9.17', '0.9.18')
	);
?>
