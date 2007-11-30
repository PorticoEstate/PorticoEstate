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

	$tmp_app_inc = $GLOBALS['phpgw']->common->get_inc_dir('ged');

	if ( isset($GLOBALS['phpgw_info']['user']['preferences']['ged']['mainscreen_show_ged_news'])
		&& $GLOBALS['phpgw_info']['user']['preferences']['ged']['mainscreen_show_ged_news'] == 'True' )
	{
		// Template
		$template = $GLOBALS['phpgw']->template;
		define ( 'GED_TPL_DIR', $GLOBALS['phpgw']->common->get_tpl_dir('ged'));
		$template->set_root(GED_TPL_DIR);
		$template->set_file(array('home_tpl'=>'home.tpl'));
		
		// Styles
		if(!@is_object($GLOBALS['phpgw']->css))
		{
			$GLOBALS['phpgw']->css = createObject('phpgwapi.css');
		}
		$GLOBALS['phpgw']->css->validate_file('home','ged');
		
		// Portalbox data
		$title=lang('ged');
		
		$portalbox=CreateObject('phpgwapi.listbox',
			@Array(
				'title'=>$title,
				'primary'=>$GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'secondary'=>$GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'tertiary'=>$GLOBALS['phpgw_info']['theme']['navbar_bg'],
				'width'=>'100%',
				'outerborderwidth'=>'0',
				'header_background_image'=>$GLOBALS['phpgw']->common->image('phpgwapi','bg_filler', '.png', False)
			)
		);
			
		$app_id=$GLOBALS['phpgw']->applications->name2id('ged');
		$GLOBALS['portal_order'][]=$app_id;
		 
		
		$var=Array(
		//	'up'=>Array('url'=>'/set_box.php', 'app'=>$app_id),
		//	'down'=>Array('url'=>'/set_box.php', 'app'=>$app_id),
		//	'close'=>Array('url'=>'/set_box.php', 'app'=>$app_id),
		//	'question'=>Array('url'=>'/set_box.php', 'app'=>$app_id),
		//	'edit'=>Array('url'=>'/set_box.php', 'app'=>$app_id)
		);

		while(list($key,$value)=each($var))
		{
			$portalbox->set_controls($key,$value);
		}

		$portalbox->data=Array();
			
		if ( isset($data))
		{
			if(is_array($data))
			{
				$portalbox->data=$data;
			}
		}
			
		/* partie interessante */
			
		// Call ged data manager
		$ged_dm=CreateObject('ged.ged_dm', True);
		
		// Get info
		$myprojects=$ged_dm->list_wanted_projects();
		
		$template->set_block('home_tpl', 'ged_projects', 'ged_projects_handle');
		$template->set_block('ged_projects', 'new_docs_list', 'new_docs_list_handle');
		$template->set_block('ged_projects', 'working_docs_list', 'working_docs_list_handle');
		$template->set_block('ged_projects', 'pending_docs_list', 'pending_docs_list_handle');
		$template->set_block('ged_projects', 'alert_docs_list', 'alert_docs_list_handle');
		$template->set_block('ged_projects', 'refused_docs_list', 'refused_docs_list_handle');

		
		foreach ( $myprojects as $my_element_id => $myproject )
		{
			// DEBUG
			//print( $my_element_id . ":" . $myproject . "<br/>\n");
			
			$new_docs=$ged_dm->list_new_documents($my_element_id);		
			
			$template->set_var('project_name', $myproject );
			$template->set_var('new_docs_list_handle', "" );
			$template->set_var('working_docs_list_handle', "" );
			$template->set_var('pending_docs_list_handle', "" );
			$template->set_var('alert_docs_list_handle', "" );
			$template->set_var('refused_docs_list_handle', "" );
			
			$tr_class='';		
			if ( is_array($new_docs))
			{
				foreach ($new_docs as $new_doc )
				{
					if ( $tr_class=='row_off' )
						$tr_class='row_on';
					else
						$tr_class='row_off';
					
					$template->set_var('tr_class', $tr_class);
					
					$template->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $new_doc['status']."-16"));
					$template->set_var('version', $new_doc['major'].".".$new_doc['minor']);
					$template->set_var('doc_name', $new_doc['name']);
					$template->set_var('doc_reference', $new_doc['reference']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$new_doc['element_id'];			
					$template->set_var('doc_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
					$template->fp('new_docs_list_handle', 'new_docs_list', True);
				}
			}
			
			$working_docs=$ged_dm->list_working_documents($my_element_id);
			
			$file_odd_even='odd';		
			if ( is_array($working_docs))
			{
				foreach ($working_docs as $working_doc )
				{
					if ( $tr_class=='row_off' )
						$tr_class='row_on';
					else
						$tr_class='row_off';
					
					$template->set_var('tr_class', $tr_class);
					
					$template->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $working_doc['status']."-16"));
					$template->set_var('version', $working_doc['major'].".".$working_doc['minor']);
					$template->set_var('doc_name', $working_doc['name']);
					$template->set_var('doc_reference', $working_doc['reference']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$working_doc['element_id'];			
					$template->set_var('doc_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
					$template->fp('working_docs_list_handle', 'working_docs_list', True);
				}
			}
			
			$pending_docs=$ged_dm->list_pending_documents($my_element_id);
			
			$file_odd_even='odd';		
			if ( is_array($pending_docs))
			{
				foreach ($pending_docs as $pending_doc )
				{
					if ( $tr_class=='row_off' )
						$tr_class='row_on';
					else
						$tr_class='row_off';
					
					$template->set_var('tr_class', $tr_class);
					
					$template->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $pending_doc['status']."-16"));
					$template->set_var('version', $pending_doc['major'].".".$pending_doc['minor']);
					$template->set_var('doc_name', $pending_doc['name']);
					$template->set_var('doc_reference', $pending_doc['reference']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$pending_doc['element_id'];			
					$template->set_var('doc_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
					$template->fp('pending_docs_list_handle', 'pending_docs_list', True);
				}
			}
	
			$alert_docs=$ged_dm->list_alert_documents($my_element_id);
			
			$file_odd_even='odd';		
			if ( is_array($alert_docs))
			{
				foreach ($alert_docs as $alert_doc )
				{
					if ( $tr_class=='row_off' )
						$tr_class='row_on';
					else
						$tr_class='row_off';
					
					$template->set_var('tr_class', $tr_class);
					
					$template->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $alert_doc['status']."-16"));
					$template->set_var('version', $alert_doc['major'].".".$alert_doc['minor']);
					$template->set_var('doc_name', $alert_doc['name']);
					$template->set_var('doc_reference', $alert_doc['reference']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$alert_doc['element_id'];			
					$template->set_var('doc_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
					$template->fp('alert_docs_list_handle', 'alert_docs_list', True);
				}
			}
	
			$refused_docs=$ged_dm->list_refused_documents($my_element_id);
			
			$file_odd_even='odd';		
			if ( is_array($refused_docs))
			{
				foreach ($refused_docs as $refused_doc )
				{
					if ( $tr_class=='row_off' )
						$tr_class='row_on';
					else
						$tr_class='row_off';
					
					$template->set_var('tr_class', $tr_class);
					
					$template->set_var('status_image', $GLOBALS['phpgw']->common->image('ged', $refused_doc['status']."-16"));
					$template->set_var('version', $refused_doc['major'].".".$refused_doc['minor']);
					$template->set_var('doc_name', $refused_doc['name']);
					$template->set_var('doc_reference', $refused_doc['reference']);
					
					$link_data=null;
					$link_data['menuaction']='ged.ged_ui.browse';
					$link_data['focused_id']=$refused_doc['element_id'];			
					$template->set_var('doc_link', $GLOBALS['phpgw']->link('/index.php', $link_data));
				
					$template->fp('refused_docs_list_handle', 'refused_docs_list', True);
				}
			}
			$template->fp('ged_projects_handle', 'ged_projects', True);
		}
		//$to_expire_docs=$ged_dm->list_documents_to_expire();
		
			
		/* fin partie interessante */
			
		$GLOBALS['extra_data']=$template->fp('out', 'home_tpl');
		
		// output the portalbox
		echo $portalbox->draw($GLOBALS['extra_data']);
				
		flush();
	}
 
?>
