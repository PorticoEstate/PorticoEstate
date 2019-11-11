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
		'versions' => array('0.2.31', '0.2.43','0.2.44', '0.2.45',
			'0.2.46', '0.2.47', '0.2.48', '0.2.49', '0.2.50')
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
