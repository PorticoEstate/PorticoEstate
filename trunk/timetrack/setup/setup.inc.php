<?php
	/**************************************************************************\
	* phpGroupWare - timetrack                                                  *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: setup.inc.php,v 1.10 2006/03/11 23:20:48 skwashd Exp $ */

	$setup_info['timetrack']['name']    = 'timetrack';
	$setup_info['timetrack']['version'] = '0.9.13.001';
	$setup_info['timetrack']['app_order'] = 3;
	$setup_info['timetrack']['enable']  = 1;
	$setup_info['timetrack']['app_group']	= 'office';

	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_customers';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_emplyprof';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_job_details';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_job_status';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_jobs';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_locations';
	$setup_info['timetrack']['tables'][] = 'phpgw_ttrack_wk_cat';

	/* The hooks this app includes, needed for hooks registration */
	$setup_info['timetrack']['hooks'][] = 'preferences';
	$setup_info['timetrack']['hooks'][] = 'admin';
	$setup_info['timetrack']['hooks'][] = 'about';

	/* Dependencies for this app to work */
	$setup_info['timetrack']['depends'][] = array(
		 'appname' => 'phpgwapi',
		 'versions' => Array('0.9.16','0.9.17','0.9.18')
	);
?>
