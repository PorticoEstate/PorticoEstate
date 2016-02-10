<?php
	$setup_info['logistic']['name'] = 'logistic';
	$setup_info['logistic']['version'] = '0.0.7';
	$setup_info['logistic']['app_order'] = 70;
	$setup_info['logistic']['enable'] = 1;
	$setup_info['logistic']['app_group']	= 'office';

	$setup_info['logistic']['tables'] = array
	(
			'lg_project',
			'lg_project_type',
			'lg_activity',
			'lg_requirement',
			'lg_resource_type_requirement',
			'lg_requirement_value',
			'lg_calendar',
			'lg_requirement_resource_allocation',
	);

	$setup_info['logistic']['description'] = 'Bergen kommune logistics module';

	$setup_info['logistic']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['logistic']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => array('0.9.17', '0.9.18','0.9.19')
	);

	$setup_info['logistic']['depends'][] = array(
		'appname' => 'property',
		'versions' => array('0.9.17')
	);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['logistic']['hooks'] = array
	(
		'menu'	=> 'logistic.menu.get_menu'
	);
