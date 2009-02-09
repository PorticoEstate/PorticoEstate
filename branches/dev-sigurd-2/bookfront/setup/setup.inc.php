<?php
	$setup_info['booking-frontend']['name'] = 'booking-frontend';
	$setup_info['booking-frontend']['version'] = '0.1';
	$setup_info['booking-frontend']['app_order'] = 9;
	$setup_info['booking-frontend']['tables'] = array();
	$setup_info['booking-frontend']['enable'] = 1;
	$setup_info['booking-frontend']['app_group']	= 'office';

	$setup_info['booking-frontend']['description'] = 'Bergen kommune booking-frontend';

	$setup_info['booking-frontend']['author'][] = array
	(
		'name'	=> 'Redpill Linpro',
		'email'	=> 'info@redpill-linpro.com'
	);

	/* Dependencies for this app to work */
	$setup_info['booking-frontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['booking-frontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['booking-frontend']['hooks'] = array
	(
		'menu'	=> 'booking-frontend.menu.get_menu'
	);
?>
