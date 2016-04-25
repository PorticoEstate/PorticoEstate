<?php
	/**************************************************************************\
	* phpGroupWare - administration                                            *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$setup_info['admin']['name']		= 'admin';
	$setup_info['admin']['version']		= '0.9.17.001';
	$setup_info['admin']['app_order']	= 1;
	$setup_info['admin']['tables']		= '';
	$setup_info['admin']['enable']		= 1;
	$setup_info['admin']['app_group']	= 'systools';

	$setup_info['admin']['author'][] = array
	(
		'name'	=> 'phpGroupWare coreteam',
		'email' => 'phpgroupware-developers@gnu.org'
	);

	$setup_info['admin']['maintainer'][] = array
	(
		'name'	=> 'phpGroupWare coreteam',
		'email' => 'phpgroupware-developers@gnu.org',
		'url'	=> 'www.phpgroupware.org/coredevelopers'
	);

	$setup_info['admin']['license']  = 'GPL';
	$setup_info['admin']['description'] = 'phpGroupWare administration application';

	/* The hooks this app includes, needed for hooks registration */

	$setup_info['admin']['hooks'] = array
	(
		'acl_manager',
		'add_def_pref',
		'after_navbar',
		'config',
		'deleteaccount',
		'manual',
		'view_user',
		'menu'					=> 'admin.menu.get_menu',
		'cat_add'				=> 'admin.cat_hooks.cat_add',
		'cat_delete'			=> 'admin.cat_hooks.cat_delete',
		'cat_edit'				=> 'admin.cat_hooks.cat_edit'
	);

	/* Dependencies for this app to work */
	$setup_info['admin']['depends'][] = array
	(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);
