<?php
	$setup_info['controller']['name'] = 'controller';
	$setup_info['controller']['version'] = '0.1.48';
	$setup_info['controller']['app_order'] = 100;
	$setup_info['controller']['enable'] = 1;
	$setup_info['controller']['app_group']	= 'office';

	$setup_info['controller']['description'] = 'Bergen kommune controller';

	$setup_info['controller']['author'][] = array
	(
		'name'	=> 'Bouvet ASA',
		'email'	=> 'info@bouvet.no'
	);

	/* Dependencies for this app to work */
	$setup_info['controller']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);

	$setup_info['controller']['depends'][] = array(
		'appname' => 'property',
		'versions' => Array('0.9.17')
	);
	
	/* The hooks this app includes, needed for hooks registration */
	$setup_info['controller']['hooks'] = array
	(
		'menu'					=> 'controller.menu.get_menu',
		'config',
		'home'              	=> 'controller.hook_helper.home_backend',
		'home_mobilefrontend'	=> 'controller.hook_helper.home_mobilefrontend',
		'settings',
		'cat_add'				=> 'controller.cat_hooks.cat_add',
		'cat_delete'			=> 'controller.cat_hooks.cat_delete',
		'cat_edit'				=> 'controller.cat_hooks.cat_edit'
	);
	
	$setup_info['controller']['tables'] = array 
	(
		'controller_control',
		'controller_control_item_list',
		'controller_control_item',
		'controller_control_group',
		'controller_check_item',
		'controller_check_list',
		'controller_procedure',
		'controller_control_group_list',
		'controller_control_location_list',
		'controller_control_component_list',
		'controller_control_group_component_list',
		'controller_document',
		'controller_document_types',
		'controller_check_item_case',
		'controller_check_item_status',
		'controller_control_item_option'
 	);
