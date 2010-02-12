<?php
	$setup_info['rentalfrontend']['name'] = 'rentalfrontend';
	$setup_info['rentalfrontend']['version'] = '0.1';
	$setup_info['rentalfrontend']['app_order'] = 9;
	$setup_info['rentalfrontend']['tables'] = array();
	$setup_info['rentalfrontend']['enable'] = 1;
	$setup_info['rentalfrontend']['app_group']	= 'office';

	$setup_info['rentalfrontend']['description'] = 'Bergen kommune rental frontend';

	$setup_info['rentalfrontend']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['rentalfrontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);

	$setup_info['rentalfrontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

    $setup_info['rentalfrontend']['depends'][] = array(
		'appname' => 'rental',
		'versions' => array('0.1.0')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['rentalfrontend']['hooks'] = array
	(
		'menu'	=> 'rentalfrontend.menu.get_menu'
	);
?>