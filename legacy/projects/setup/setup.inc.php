<?php
  /*************************************************************************\
  * phpGroupWare Setup - Projects                                           *
  * http://www.phpgroupware.org                                             *
  * --------------------------------------------                            *
  * This program is free software; you can redistribute it and/or modify it *
  * under the terms of the GNU General Public License as published by the   *
  * Free Software Foundation; either version 2 of the License, or (at your  *
  * option) any later version.                                              *
  \*************************************************************************/
  /* $Id$ */

	$setup_info['projects']['name']      = 'projects';
	$setup_info['projects']['title']	 = 'Projects';
	$setup_info['projects']['version']   = '0.8.7.055';
	$setup_info['projects']['app_order'] = 13;
	$setup_info['projects']['enable']    = 1;

	$setup_info['projects']['author'] = array
	(
		'name'	=> 'Bettina Gille',
		'email'	=> 'ceb@phpgroupware.org'
	);

	$setup_info['projects']['license']  = 'GPL';
	$setup_info['projects']['description'] = 'Advanced project management';

	$setup_info['projects']['maintainer'] = $setup_info['projects']['author'];

	$setup_info['projects']['tables'] = array
	(
		'phpgw_p_projects',
		'phpgw_p_activities',
		'phpgw_p_projectactivities',
		'phpgw_p_hours',
		'phpgw_p_projectmembers',
		'phpgw_p_invoice',
		'phpgw_p_invoicepos',
		'phpgw_p_delivery',
		'phpgw_p_deliverypos',
		'phpgw_p_mstones',
		'phpgw_p_roles',
		'phpgw_p_ttracker',
		'phpgw_p_events',
		'phpgw_p_alarm',
		'phpgw_p_surcharges',
		'phpgw_p_locations'
	);

/* The hooks this app includes, needed for hooks registration */

	$setup_info['projects']['hooks'] = array
	(
		'preferences',
		'admin',
		'manual',
		'add_def_pref',
		'deleteaccount',
		'home',
		'sidebox_menu'
	);

	$setup_info['projects']['hooks']['getFolderContent'] = 'projects.boprojects.get_folder_content';
	$setup_info['projects']['hooks']['getFolderLinkData'] = 'projects.boprojects.get_folder_linkdata';

/* Dependencies for this app to work */

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16','0.9.17','0.9.18')
	);

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'admin',
		 'versions' => Array('0.9.16','0.9.17')
	);

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'preferences',
		 'versions' => Array('0.9.16','0.9.17')
	);

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'addressbook',
		 'versions' => Array('0.9.16','0.9.17')
	);

	$setup_info['projects']['depends'][] = array
	(
		 'appname' => 'email',
		 'versions' => Array('0.9.13','0.9.17')
	);
?>
