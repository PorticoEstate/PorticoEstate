<?php
	$setup_info['frontend']['name'] = 'frontend';
	$setup_info['frontend']['version'] = '0.6';
	$setup_info['frontend']['app_order'] = 9;
//	$setup_info['frontend']['tables'] = array();
	$setup_info['frontend']['enable'] = 1;
	$setup_info['frontend']['app_group'] = 'office';

	$setup_info['frontend']['description'] = 'Bergen kommune rental frontend';

	$setup_info['frontend']['author'][] = array
		(
		'name' => 'Bouvet ASA',
		'email' => 'info@bouvet.no'
	);

	$setup_info['property']['maintainer'] = array
		(
		'name' => 'Sigurd Nes',
		'email' => 'sigurd.nes@bergen.kommune.no'
	);

	/* Dependencies for this app to work */
	$setup_info['frontend']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18')
	);

	$setup_info['frontend']['depends'][] = array(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

	$setup_info['frontend']['depends'][] = array(
		'appname' => 'rental',
		'versions' => array('0.1.0')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['frontend']['hooks'] = array
		(
		'menu' => 'frontend.menu.get_menu',
		'auto_addaccount' => 'frontend.hook_helper.auto_addaccount',
		'config'
	);
