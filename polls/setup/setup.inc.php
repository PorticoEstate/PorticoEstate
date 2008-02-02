<?php
	/**************************************************************************\
	* phpGroupWare - Polls                                                     *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/* Basic information about this app */
	$setup_info['polls']['name']      = 'polls';
	$setup_info['polls']['version']   = '0.8.1';
	$setup_info['polls']['app_order'] = 15;
	$setup_info['polls']['enable']    = 1;
	$setup_info['polls']['app_group']	= 'other';

	/* The tables this app creates */
	$setup_info['polls']['tables']    = array(
		'phpgw_polls_data',
		'phpgw_polls_desc',
		'phpgw_polls_user',
		'phpgw_polls_settings'
	);

	$setup_info['polls']['hooks'][] = 'admin';
	$setup_info['polls']['hooks'][] = 'website_left';
	$setup_info['polls']['hooks'][] = 'website_right';

	/* Dependencies for this app to work */
	$setup_info['polls']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16','0.9.17','0.9.18')
	);
?>
