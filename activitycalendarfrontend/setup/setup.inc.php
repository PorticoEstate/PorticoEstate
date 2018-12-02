<?php
	$setup_info['activitycalendarfrontend']['name'] = 'activitycalendarfrontend';
	$setup_info['activitycalendarfrontend']['version'] = '0.1.1';
	$setup_info['activitycalendarfrontend']['app_order'] = 61;
	$setup_info['activitycalendarfrontend']['enable'] = 1;
	$setup_info['activitycalendarfrontend']['app_group'] = 'office';

	$setup_info['activitycalendarfrontend']['description'] = 'Bergen kommune activitycalendarfrontend';

	$setup_info['activitycalendarfrontend']['author'][] = array
		(
		'name' => 'Bouvet ASA',
		'email' => 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['activitycalendarfrontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['activitycalendarfrontend']['depends'][] = array(
		'appname' => 'booking',
		'versions' => array('0.2.31', '0.2.43')
	);

	$setup_info['activitycalendarfrontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	$setup_info['activitycalendarfrontend']['depends'][] = array(
		'appname' => 'activitycalendar',
		'versions' => Array('0.1.11')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['activitycalendarfrontend']['hooks'] = array
		(
		'menu' => 'activitycalendarfrontend.menu.get_menu',
		'set_cookie_domain' => 'activitycalendarfrontend.hook_helper.set_cookie_domain',
		'config'
	);
