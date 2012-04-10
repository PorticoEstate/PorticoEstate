<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['registration']['name']			= 'registration';
	$setup_info['registration']['version']		= '0.8.4';
	$setup_info['registration']['app_order']	= '90';
	$setup_info['registration']['enable']		= 2;
	$setup_info['registration']['app_group']	= 'other';

	/* The tables this app creates */
	$setup_info['registration']['tables']		= array(
		'phpgw_reg_accounts',
		'phpgw_reg_fields'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['registration']['hooks'] = array
	(
		'menu'		=> 'registration.menu.get_menu',
		'config',
		'logout'
	);


	/* Dependencies for this app to work */
	$setup_info['registration']['depends'][] = array
	(
		'appname'  => 'phpgwapi',
		'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
