<?php
	/**************************************************************************\
	* phpGroupWare - MediaDataBase                                             *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	/* Basic information about this app */
	$setup_info['mediadb']['name']      = 'mediadb';
	$setup_info['mediadb']['version']   = '0.0.4';
	$setup_info['mediadb']['app_order'] = 25;
	$setup_info['mediadb']['enable']    = 1;
	$setup_info['mediadb']['app_group']	= 'multimedia';

	/* The tables this app creates */
	$setup_info['mediadb']['tables']    = array(
		'phpgw_mediadb',
		'phpgw_mediadb_artist',
		'phpgw_mediadb_cat',
		'phpgw_mediadb_data',
		'phpgw_mediadb_feature',
		'phpgw_mediadb_format',
		'phpgw_mediadb_genre',
		'phpgw_mediadb_loan',
		'phpgw_mediadb_lookup',
		'phpgw_mediadb_publisher',
		'phpgw_mediadb_rating',
		'phpgw_mediadb_region',
		'phpgw_mediadb_request'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['mediadb']['hooks'][] = 'preferences';
	$setup_info['mediadb']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['mediadb']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16', '0.9.17', '0.9.18')
	);
?>
