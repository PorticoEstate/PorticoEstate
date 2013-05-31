<?php
	$setup_info['bookingfrontend']['name'] = 'bookingfrontend';
	$setup_info['bookingfrontend']['version'] = '0.1';
	$setup_info['bookingfrontend']['app_order'] = 9;
	$setup_info['bookingfrontend']['enable'] = 1;
	$setup_info['bookingfrontend']['app_group']	= 'office';

	$setup_info['bookingfrontend']['description'] = 'Stavanger kommune bookingfrontend';

	$setup_info['bookingfrontend']['author'][] = array
	(
		'name'	=> 'Redpill Linpro',
		'email'	=> 'info@redpill-linpro.com'
	);

	/* Dependencies for this app to work */
	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'booking',
		'versions' => Array('0.2.00', '0.2.01','0.2.02','0.2.03','0.2.04','0.2.05','0.2.06','0.2.07','0.2.08','0.2.09','0.2.10')
	);

	$setup_info['bookingfrontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['bookingfrontend']['hooks'] = array
	(
		'menu'	=> 'bookingfrontend.menu.get_menu',
		'config'
	);
?>
