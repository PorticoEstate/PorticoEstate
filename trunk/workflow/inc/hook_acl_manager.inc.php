<?php
	/**************************************************************************\
	* phpGroupWare                                                               *
	* http://www.phpgroupware.org                                                *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	$GLOBALS['acl_manager']['workflow']['admin_workflow'] = array(
		'name' => 'Grant access to the administration and development of workflow processes and activities.',
		'rights' => array(
			'administer processes'   => 1
		)
	);

	$GLOBALS['acl_manager']['workflow']['monitor_workflow'] = array(
		'name' => 'Grant access to the monitoring of workflow elements',
		'rights' => array(
			'monitoring'    => 1,
		)
	);


	$GLOBALS['acl_manager']['workflow']['admin_instance_workflow'] = array(
		'name' => 'Grant access to the administration of workflow instances in monitor screens',
		'rights' => array(
			'administer instances'   => 1
		)
	);

	$GLOBALS['acl_manager']['workflow']['cleanup_workflow'] = array(
		'name' => 'Grant access to the cleanup/deletion of workflow instances in monitor screens',
		'rights' => array(
			'cleanup instances'   => 1
		)
	);


	$GLOBALS['acl_manager']['workflow']['cleanup_workflow'] = array(
		'name' => 'Grant access to the cleanup/deletion of workflow aborted instances in monitor screens',
		'rights' => array(
			'cleanup aborted instances'   => 1
		)
	);


