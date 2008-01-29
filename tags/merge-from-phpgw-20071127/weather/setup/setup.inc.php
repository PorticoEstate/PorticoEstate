<?php
	/**************************************************************************\
	* phpGroupWare - Weather                                                   *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php 16493 2006-03-11 23:24:02Z skwashd $ */

	/* Basic information about this app */
	$setup_info['weather']['name']      = 'weather';
	$setup_info['weather']['version']   = '0.9.13.002';
	$setup_info['weather']['app_order'] = 14;
	$setup_info['weather']['enable']    = 1;
	$setup_info['weather']['app_group']	= 'accessories';
	$setup_info['weather']['NOTE!!!'] = 
	'COPY weather/setup/default_records_us.inc.php OR
setup/default_records_world.inc.php TO
setup/default_records.inc.php BEFORE INSTALL!';

	$setup_info['weather']['tables'] = array(
		'phpgw_weather_admin',
		'phpgw_weather',
		'phpgw_weather_links',
		'phpgw_weather_metar',
		'phpgw_weather_region',
		'phpgw_us_states'
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['weather']['hooks'][] = 'preferences';
	$setup_info['weather']['hooks'][] = 'admin';

	/* Dependencies for this app to work */
	$setup_info['weather']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.13', '0.9.14', '0.9.15', '0.9.17', '0.9.18')
	);
?>
