<?php
	/**************************************************************************\
	* phpGroupWare - Preferences                                                 *
	* http://www.phpgroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: hook_settings.inc.php 22630 2006-10-14 12:28:56Z regis_glc $ */

	// ui_userinstance preferences
	$GLOBALS['settings'] = array(
		'globalworkprefs' => array(
		'type' => 'section',
			'title' => 'Global Workflow Preferences',
			'xmlrpc' => True,
			'admin'  => False
		),
		'startpage' => array(
			'type'  => 'select',
			'label' => 'Starting page',
			'name'	=> 'startpage',
			'help'  => 'This is the first screen shown when you click on the workflow application icon',
			'values' => array(
				'workflow.ui_userprocesses'     => 'My processes',
				'workflow.ui_useractivities'    => 'My activities',
				'workflow.ui_userinstances'    => 'My instances',
				'workflow.ui_useractivities2'    => 'Global activities',
				'workflow.ui_useropeninstance'    => 'Open Instances'
			),
			'xmlrpc' => True,
			'admin'  => False
		),


		'userfilt' => array(
			'type' => 'section',
			'title' => 'User Instances form: filters and actions'
		),
		'wf_instances_show_instance_search' => array(
			'type'   => 'check',
			'label' => 'Search instance filter in the bottom of instance lists',
			'name'  => 'wf_instances_show_instance_search',
			'help'  => 'Do you want the search instance button in the last row of instances list.',
			'default' => 0,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_advanced_mode' => array(
			'type'   => 'check',
			'label' => 'Always show advanced mode',
			'name'  => 'wf_instances_show_advanced_mode',
			'help'  => 'Should we always give you the advanced search row on instances lists?',
			'default' => 0,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_advanced_actions' => array(
			'type'   => 'check',
			'label' => 'Always show advanced actions',
			'name'  => 'wf_instances_show_advanced_actions',
			'help'  => 'When in advanced mode, should we show you advanced actions by default (resume, exception, grab, etc.)?',
			'default' => 0 ,
			'xmlrpc' => True,
			'admin'  => False
		),


		'usercols' => array(
			'type' => 'section',
			'title' => 'User Instances form: columns',
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_instance_id_column' => array(
			'type'   => 'check',
			'label' => 'Column Instance Id in instance lists',
			'name'  => 'wf_instances_show_instance_id_column',
			'help'  => 'Do you want the instance id column on instances lists. This is the unique identifier of an instance',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_priority_column' => array(
			'type'   => 'check',
			'label' => 'Column Priority in instance lists',
			'name'  => 'wf_instances_show_priority_column',
			'help'  => 'Do you want the priority column on instances lists. Priority can be set with activities forms',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_instance_status_column' => array(
			'type'   => 'check',
			'label' => 'Column Instance Status in instance lists',
			'name'  => 'wf_instances_show_instance_status_column',
			'help'  => 'Do you want the instance status on instances lists. The instance status is usefull to disting beteween aborted, completed, exception or active instances',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_instance_name_column' => array(
			'type'   => 'check',
			'label' => 'Column Instance Name in instance lists',
			'name'  => 'wf_instances_show_instance_name_column',
			'help'  => 'Do you want the instance name column on instances lists. If your instances have name you should really use this',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_process_name_column' => array(
			'type'   => 'check',
			'label' => 'Column Process Name in instance lists',
			'name'  => 'wf_instances_show_process_name_column',
			'help'  => 'Do you want the process column on instances lists. Usefull if you have different processes and/or versions of theses processes',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_activity_status_column' => array(
			'type'   => 'check',
			'label' => 'Column Activity Status in instance lists',
			'name'  => 'wf_instances_show_activity_status_column',
			'help'  => 'Do you want the activity status on instances lists. Most of the time it is "running" but if you use non-autorouted transitions you will have some completed activities.',
			'default' => 0,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_owner_column' => array(
			'type'   => 'check',
			'label' => 'Column Owner in instance lists',
			'name'  => 'wf_instances_show_owner_column',
			'help'  => 'Do you want the owner column on instances lists. This will show you the actual owner, especially usefull if ownership is defined with special rights',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_category_column' => array(
			'type'   => 'check',
			'label' => 'Column Category in instance lists',
			'name'  => 'wf_instances_show_category_column',
			'help'  => 'Do you want the category name column on instances lists, if your instances use categories you should use it',
			'default' => 1,
			'xmlrpc' => True,
			'admin'  => False
		),
		'wf_instances_show_started_column' => array(
			'type'   => 'check',
			'label' => 'Column start date in instance lists',
			'name' => 'wf_instances_show_started_column',
			'help' => 'Do you want the started date column on instances lists.',
			'default' => 1,
			'xmlrpc' => True,
			'admin' => False
		),
	);
?>
