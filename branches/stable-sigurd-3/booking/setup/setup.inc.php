<?php
	$setup_info['booking']['name'] = 'booking';
	$setup_info['booking']['version'] = '0.1.52';
	$setup_info['booking']['app_order'] = 9;
	$setup_info['booking']['enable'] = 1;
	$setup_info['booking']['app_group'] = 'office';
	$setup_info['booking']['tables'] = array 
	(
		'bb_building', 
		'bb_contact_person',
		'bb_organization', 
		'bb_activity',
		'bb_resource', 
		'bb_season', 
		'bb_season_boundary', 
		'bb_group', 
		'bb_allocation',
		'bb_booking', 
		'bb_booking_resource', 
		'bb_season_resource', 
		'bb_equipment', 
		'bb_wtemplate_alloc', 
		'bb_wtemplate_alloc_resource', 
		'bb_allocation_resource', 
		'bb_application', 
		'bb_application_resource', 
		'bb_application_comment', 
		'bb_application_date', 
		'bb_agegroup', 
		'bb_application_agegroup', 
		'bb_targetaudience',
		'bb_application_targetaudience', 
		'bb_booking_agegroup', 
		'bb_booking_targetaudience', 
		'bb_document_building',
		'bb_document_resource',
		'bb_permission',
		'bb_permission_root',
		'bb_organization_contact',
		'bb_group_contact',
		'bb_event',
		'bb_event_resource',
		'bb_event_agegroup',
		'bb_event_targetaudience',
	);

	$setup_info['booking']['description'] = 'Bergen kommune booking';

	$setup_info['booking']['author'][] = array
	(
		'name'	=> 'Redpill Linpro',
		'email' => 'info@redpill-linpro.com'
	);

	/* Dependencies for this app to work */
	$setup_info['booking']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['booking']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['booking']['hooks'] = array
	(
		'menu'	=> 'booking.menu.get_menu'
	);
	
	/**** Custom settings *****/
	$x = 0;
	/* The settings for sodocument */
	$setup_info['booking']['document'] = array
	(
		'upload_dir' => '/tmp/booking_documents',
	);
?>
