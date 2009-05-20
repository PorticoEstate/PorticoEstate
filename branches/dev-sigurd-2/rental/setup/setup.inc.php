<?php
	$setup_info['rental']['name'] = 'rental';
	$setup_info['rental']['version'] = '0.0.1';
	$setup_info['rental']['app_order'] = 51;
	$setup_info['rental']['tables'] = array();
	$setup_info['rental']['enable'] = 1;
	$setup_info['rental']['app_group']	= 'office';
	$setup_info['rental']['description'] = 'Bergen kommune rental';
	$setup_info['rental']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['rental']['depends'][] = array
	(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);

	$setup_info['rental']['depends'][] = array
	(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['rental']['hooks'] = array
	(
		'menu'	=> 'rental.menu.get_menu'
	);
?>
