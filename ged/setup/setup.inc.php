<?php
	/**************************************************************************
	* phpGroupWare - ged
	* http://www.phpgroupware.org
	* Written by Pascal Vilarem <pascal.vilarem@steria.org>
	*
	* --------------------------------------------------------------------------
	*  This program is free software; you can redistribute it and/or modify it
	*  under the terms of the GNU General Public License as published by the
	*  Free Software Foundation; either version 2 of the License, or (at your
	*  option) any later version
	***************************************************************************/

	$setup_info['ged']['name']='ged';
	$setup_info['ged']['title']='Document Management System';
	$setup_info['ged']['version']='0.9.18.008';
	$setup_info['ged']['app_order']=17;
	$setup_info['ged']['enable']=1;
	$setup_info['calendar']['app_group']    = 'office';

	$setup_info['calendar']['author'] = 'Pascal Vilarem';
	$setup_info['calendar']['license']  = 'GPL';
	$setup_info['calendar']['description'] =
	  'Powerful document management system with life cycle functions and ACL security.';
	$setup_info['calendar']['note'] =
 	  'Inspired from MyDMS. More to come here.';
	$setup_info['calendar']['maintainer'] = array(
	  'name'  => 'Pascal Vilarem',
    	  'email' => 'maat@phpgroupware.org'
  );
	

	/* The hooks this app includes, needed for hooks registration */
//	$setup_info['ged']['hooks'][]='about';
	$setup_info['ged']['hooks'][]='admin';
//	$setup_info['ged']['hooks'][]='manual';
	$setup_info['ged']['hooks'][]='preferences';
	$setup_info['ged']['hooks'][]='settings';
	$setup_info['ged']['hooks'][]='home';
//	$setup_info['ged']['hooks'][]='sidebox_menu';
		$setup_info['ged']['hooks']['menu']	= 'ged.menu.get_menu';

// Tables in database

	$setup_info['ged']['tables']=array (
		'ged_comments',
		'ged_elements',
		'ged_history',
		'ged_relations',
		'ged_mimes',
		'ged_versions',
		'ged_acl',
		'ged_doc_types',
		'ged_types_places',
		'ged_periods',
		'phpgw_flows',
		'phpgw_flows_roles',
		'phpgw_flows_statuses',
		'phpgw_flows_transitions',
		'phpgw_flows_transitions_custom_values',
		'phpgw_flows_triggers',
		'phpgw_flows_conditions'
	);

//	* Dependacies for this app to work
	$setup_info['ged']['depends'][]=array(
		 'appname'=>'phpgwapi',
		 'versions'=>Array('0.9.16','0.9.17', '0.9.18')
	);

	$setup_info['ged']['depends'][]=array(
		 'appname'=>'admin',
		 'versions'=>Array('0.9.16','0.9.17', '0.9.18')
	);

	$setup_info['ged']['depends'][]=array(
		 'appname'=>'preferences',
		 'versions'=>Array('0.9.16','0.9.17', '0.9.18')
	);
?>
