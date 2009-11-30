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
	
	// Call ged data manager
	$ged_dm=CreateObject('ged.ged_dm', True);
	
	
	$yes_and_no=array(
		'True'=>'Yes',
		'False'=>'No'
	);
	
	create_select_box('Show Ged applet on home page','mainscreen_show_ged_news',$yes_and_no, 'Should Ged display the list of new documents - approved since your last login - and warnings for documents about to expire/needing approval');

 	create_input_box('Show documents needing approval within (days)','warn_approval_within');
 	
	if ( function_exists('create_multi_selection'))
	{
 		$values=$ged_dm->list_available_projects();
		create_multi_selection ('Show projects','show_projects',$values, 'Choose the projects you want to follow on home board');
	}
	
?>