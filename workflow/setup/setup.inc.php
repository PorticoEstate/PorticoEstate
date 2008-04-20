<?php
	/**************************************************************************\
	* phpGroupWare - PHPBrain                                                    *
	* http://www.phpgroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* Basic information about this app */
	$setup_info['workflow']['name']			= 'workflow';
	$setup_info['workflow']['title']		= 'Workflow management';
	$setup_info['workflow']['version']		= '1.3.00.001';
	$setup_info['workflow']['app_order']		= 10;
	$setup_info['workflow']['enable']		= 1;
	$setup_info['workflow']['author']		= 'Ported from tikiwiki, modified by regis_leroy alpeb & mbartz';
	$setup_info['workflow']['note']			= 'Workflow engine';
	$setup_info['workflow']['license']		= 'GPL';
	$setup_info['workflow']['description']		= 'Workflow management';
	$setup_info['workflow']['maintainer']		= 'Regis Leroy';
	$setup_info['workflow']['maintainer_email']	= 'regis.leroy AT makina-corpus DOT org';
	$setup_info['workflow']['app_group']	= 'office';
	$setup_info['workflow']['tables']		= array(
								'phpgw_wf_activities',
								'phpgw_wf_activity_roles',
								'phpgw_wf_instance_activities',
								'phpgw_wf_instances',
								'phpgw_wf_processes',
								'phpgw_wf_roles',
								'phpgw_wf_instance_supplements',
								'phpgw_wf_transitions',
								'phpgw_wf_user_roles',
								'phpgw_wf_workitems',
								'phpgw_wf_process_config',
								'phpgw_wf_activity_agents',
								'phpgw_wf_agent_mail_smtp',
							);

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['workflow']['hooks'] = array
	(
		'about',
		'admin',
		'add_def_pref',
		'config',
		'manual',
		'preferences',
		'settings',
		'menu'	=> 'workflow.menu.get_menu',
		'acl_manager',
		'deleteaccount'
	);


	/* Dependencies for this app to work */
	$setup_info['workflow']['depends'][] = array(
		'appname' => 'phpgwapi',
		'versions' => Array('0.9.17', '0.9.18')
	);
	$setup_info['workflow']['depends'][] = array(
		'appname' => 'preferences',
		'versions' => Array('0.9.17', '0.9.18')
	);
?>
