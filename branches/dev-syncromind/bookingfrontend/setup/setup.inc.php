<?php
	$setup_info['bookingfrontend']['name'] = 'bookingfrontend';
	$setup_info['bookingfrontend']['version'] = '0.1.1';
	$setup_info['bookingfrontend']['app_order'] = 9;
	$setup_info['bookingfrontend']['enable'] = 1;
	$setup_info['bookingfrontend']['app_group'] = 'office';

	$setup_info['bookingfrontend']['description'] = 'Bergen kommune bookingfrontend';

	$setup_info['bookingfrontend']['author'][] = array
		(
		'name' => 'Redpill Linpro',
		'email' => 'info@redpill-linpro.com'
	);

	/* Dependencies for this app to work */
	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'booking',
		'versions' => array('0.2.20', '0.2.21', '0.2.22', '0.2.23', '0.2.24', '0.2.25',
			'0.2.26', '0.2.27', '0.2.28', '0.2.29')
	);

	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['bookingfrontend']['hooks'] = array
		(
		'menu' => 'bookingfrontend.menu.get_menu',
		'set_cookie_domain' => 'bookingfrontend.hook_helper.set_cookie_domain',
		'config'
	);
