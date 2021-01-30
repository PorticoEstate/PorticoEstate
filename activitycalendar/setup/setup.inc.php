<?php
	$setup_info['activitycalendar']['name'] = 'activitycalendar';
	$setup_info['activitycalendar']['version'] = '0.1.11';
	$setup_info['activitycalendar']['app_order'] = 60;
	$setup_info['activitycalendar']['enable'] = 1;
	$setup_info['activitycalendar']['app_group'] = 'office';

	$setup_info['activitycalendar']['tables'] = array
		(
		'activity_activity',
		'activity_arena',
		'activity_organization',
		'activity_group',
		'activity_contact_person'
	);

	$setup_info['activitycalendar']['description'] = 'Bergen kommune activitycalendar';

	$setup_info['activitycalendar']['author'][] = array
		(
		'name' => 'Bouvet ASA',
		'email' => 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18', '0.9.19')
	);

	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'booking',
		'versions' => array(
			'0.2.57', '0.2.58','0.2.59','0.2.60','0.2.61','0.2.62','0.2.61','0.2.62','0.2.63','0.2.64','0.2.65','0.2.66','0.2.67')
	);

	$setup_info['activitycalendar']['depends'][] = array(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

	$setup_info['activitycalendar']['tables'] = array(
		'activity_activity',
		'activity_arena',
		'activity_organization',
		'activity_group',
		'activity_contact_person'
	);


	/* The hooks this app includes, needed for hooks registration */
	$setup_info['activitycalendar']['hooks'] = array
		(
		'menu' => 'activitycalendar.menu.get_menu'
	);
