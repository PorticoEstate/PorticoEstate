<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
	* @version $Id$
	*/


	$select_property_filter =array
	(
		''	=> lang('Owner type'),
		'owner'	=> lang('Owner')

	);
	create_select_box('Choose property filter','property_filter',$select_property_filter,'Filter by owner or owner type');

	$yes_and_no =array
	(
		'yes' => 'Yes',
		'no'  => 'No'
	);

	create_select_box('Group filters in single query','group_filters',$yes_and_no,'Group filters - means that one has to hit the search button to apply the filter');

	$status_list_tts = execMethod('property.botts._get_status_list');
	$status_list_workorder = execMethod('property.soworkorder.select_status_list');
	$status_list_project = execMethod('property.soproject.select_status_list');

	if ($status_list_tts)
	{
		foreach ( $status_list_tts as $entry )
		{
			$_status_tts[$entry['id']] = $entry['name'];
		}
	}

	if ($status_list_workorder)
	{
		foreach ( $status_list_workorder as $entry )
		{
			$_status_workorder[$entry['id']] = $entry['name'];
		}
	}

	if ($status_list_project)
	{
		foreach ( $status_list_project as $entry )
		{
			$_status_project[$entry['id']] = $entry['name'];
		}
	}


	create_select_box('show new/updated tickets on main screen','mainscreen_show_new_updated_tts',$yes_and_no,'Link to tickets you are assigned to');
	create_select_box('Default ticket status','tts_status',$_status_tts,'The default status when entering the helpdesk and mainscreen');
	create_input_box('Custom title on main screen tickets','mainscreen_tts_title');

	create_select_box('show updated tickets on main screen 2','mainscreen_show_new_updated_tts_2',$yes_and_no,'Link to tickets you are assigned to');
	create_select_box('Default ticket status 2','tts_status_2',$_status_tts,'The default status when entering the helpdesk and mainscreen');
	create_input_box('Custom title on main screen tickets','mainscreen_tts_title_2');

	create_select_box('show updated tickets on main screen 3','mainscreen_show_new_updated_tts_3',$yes_and_no,'Link to tickets you are assigned to');
	create_select_box('Default ticket status 3','tts_status_3',$_status_tts,'The default status when entering the helpdesk and mainscreen');
	create_input_box('Custom title on main screen tickets','mainscreen_tts_title_3');

	create_select_box('show updated tickets on main screen 4','mainscreen_show_new_updated_tts_4',$yes_and_no,'Link to tickets you are assigned to');
	create_select_box('Default ticket status 4','tts_status_4',$_status_tts,'The default status when entering the helpdesk and mainscreen');
	create_input_box('Custom title on main screen tickets','mainscreen_tts_title_4');

	create_select_box('show pending vendor reminders on main screen','mainscreen_showvendor_reminder',$yes_and_no,'Reminder issued to vendors');
	create_input_box('Custom title on pending vendor reminders','mainscreen_showvendor_reminder_title');

	create_select_box('show your pending request for approvals on main screen','mainscreen_showapprovals_request',$yes_and_no,'Your requests for Approvals waiting decisions');
	create_input_box('Custom title on pending request for approvals','mainscreen_showapprovals_request_title');

	create_select_box('show pending approvals on main screen','mainscreen_showapprovals',$yes_and_no,'Approvals waiting for your decisions');
	create_input_box('Custom title on pending approvals','mainscreen_showapprovals_title');

	create_select_box('Default updated ticket status when creating project','tts_status_create_project',$_status_tts,'The default status when entering the helpdesk and mainscreen');
	create_select_box('Autocreate project from ticket','auto_create_project_from_ticket',$yes_and_no);

	create_select_box('your projects on main screen - list 1','mainscreen_project_1',$yes_and_no,'Link to your projects');
	create_select_box('Default project status 1','project_status_mainscreen_1',$_status_project,'The default status for list 1 when entering the mainscreen');
	create_input_box('Custom title on projects on main screen - list 1','mainscreen_projects_1_title');

	create_select_box('your workorders on main screen - list 1','mainscreen_workorder_1',$yes_and_no,'Link to your workorders');
	create_select_box('Default workorder status 1','workorder_status_mainscreen_1',$_status_workorder,'The default status for list 1 when entering the mainscreen');
	create_input_box('Custom title on workorders on main screen - list 1','mainscreen_workorders_1_title');

	create_select_box('your workorders on main screen - list 2','mainscreen_workorder_2',$yes_and_no,'Link to your workorders');
	create_select_box('Default workorder status 2','workorder_status_mainscreen_2',$_status_workorder,'The default status for list 2 when entering the mainscreen');
	create_input_box('Custom title workorders on main screen - list 2','mainscreen_workorders_2_title');

	create_select_box('show quick link for changing status for tickets','tts_status_link',$yes_and_no,'Enables to set status wihout entering the ticket');

	$acc = & $GLOBALS['phpgw']->accounts;
	$group_list = $acc->get_list('groups');
	foreach ( $group_list as $entry )
	{
		$_groups[$entry->id] = $entry->lid;
	}
	create_select_box('Default group TTS','groupdefault',$_groups,'The default group to assign a ticket in Helpdesk-submodule');

	$account_list = $acc->get_list('accounts',-1,'ASC','account_lastname');

	foreach ( $account_list as $entry )
	{
		if($entry->enabled == true)
		{
			$_accounts[$entry->id] = $entry->__toString();
		}
	}
	create_select_box('Default assign to TTS','assigntodefault',$_accounts,'The default user to assign a ticket in Helpdesk-submodule');

	$priority_list_tts = execMethod('property.botts.get_priority_list');

	if ($priority_list_tts)
	{
		foreach ( $priority_list_tts as $entry )
		{
			$_priority_tts[$entry['id']] = $entry['name'];
		}
	}

	create_select_box('Default Priority TTS','prioritydefault',$_priority_tts,'The default priority for tickets in the Helpdesk-submodule');

	$cats		= CreateObject('phpgwapi.categories', -1, 'property', '.ticket');

	$cat_data	= $cats->formatted_xslt_list(array('globals' => true, 'link_data' =>array()));
	$cat_list = $cat_data['cat_list'];

	if (is_array($cat_list))
	{
		foreach ( $cat_list as $entry )
		{
			$_categories_tts[$entry['cat_id']] = $entry['name'];
		}
	}

	unset($sotts);
	create_select_box('default ticket categories','tts_category',$_categories_tts,'The default category for TTS');

	$yes_and_no =array
	(
		'1' => 'Yes',
		'2' => 'No'
	);


	$degree = array();
	// Choose the correct degree to display
	$degree_comment[0]=' - '.lang('None');
	$degree_comment[1]=' - '.lang('Minor');
	$degree_comment[2]=' - '.lang('Medium');
	$degree_comment[3]=' - '.lang('Serious');
	for ($i=0; $i<=3; $i++)
	{
		$degree[$i] = $i . $degree_comment[$i];
	}



	create_select_box('Notify me by mail when ticket is assigned or altered','tts_notify_me',$yes_and_no,'');
	create_select_box('Send e-mail from TTS','tts_user_mailnotification',$yes_and_no,'Send e-mail from TTS as default');
	create_input_box('Refresh TTS every (seconds)','refreshinterval','The intervall for Helpdesk refresh - cheking for new tickets');

	create_select_box('Set myself as contact when adding a ticket','tts_me_as_contact',$yes_and_no,'');

	create_select_box('Default Degree Request safety','default_safety',$degree,'The degree of seriousness');
	create_select_box('Default Degree Request aesthetics','default_aesthetics',$degree);
	create_select_box('Default Degree Request indoor climate','default_climate',$degree);
	create_select_box('Default Degree Request consequential damage','default_consequential_damage',$degree);
	create_select_box('Default Degree Request user gratification','default_gratification',$degree);
	create_select_box('Default Degree Request residential environment','default_environment',$degree);

	create_select_box('Send order receipt as email ','order_email_rcpt',$yes_and_no,'Send the order as BCC to the user');
	create_select_box('Notify owner of project/order on change','notify_project_owner',$yes_and_no,'By email');
	create_select_box('request an email receipt','request_order_email_rcpt',$yes_and_no,'request a confirmation email when your email is opened by the recipient');

	$default_start_page =array
	(
		'location'   => lang('Location'),
		'project' => lang('Project'),
		'tts' => lang('Ticket'),
		'invoice' => lang('Invoice'),
		'document'=> lang('Document')
	);
	create_select_box('Default start page','default_start_page',$default_start_page,'Select your start-submodule');

	$socommon= CreateObject('property.socommon');

	$district_list= $socommon->select_district_list();	

	$cats->set_appname('property', '.project');

	$cat_data	= $cats->formatted_xslt_list(array('globals' => true, 'link_data' =>array()));
	$cat_list = $cat_data['cat_list'];

	if (is_array($cat_list))
	{
		foreach ( $cat_list as $entry )
		{
			$_categories_project[$entry['cat_id']] = $entry['name'];
		}
	}

	if ($district_list)
	{
		foreach ( $district_list as $entry )
		{
			$_districts[$entry['id']] = $entry['name'];
		}
	}

	unset($soworkorder);
	unset($socommon);


	$default_project_type =array
	(
		'1'	=> lang('operation'),
		'2' => lang('investment'),
		'3' => lang('buffer')
	);

	create_select_box('Default project type','default_project_type',$default_project_type,'Select your default project type');

	$default_project_filter_year =array
	(
		'current_year'	=> lang('current year'),
		'all' 			=> lang('all'),
	);

	create_select_box('Default project year filter','default_project_filter_year',$default_project_filter_year,'Select your default project year filter');

	create_select_box('Default project status','project_status',$_status_project,'The default status for your projects');
	create_select_box('Default workorder status','workorder_status',$_status_workorder,'The default status for your workorders');
	create_select_box('Default project categories','project_category',$_categories_project,'The default category for your projects and workorders');
	create_select_box('Default district-filter','default_district',$_districts,'Your default district-filter ');

	create_input_box('Your Cellphone','cellphone');
	create_input_box('RessursNr','ressursnr');
	$ecodimb	= CreateObject('property.sogeneric');
	$ecodimb->get_location_info('dimb',false);
	$values_dimb = $ecodimb->read(array('sort' => 'ASC','order' => 'id', 'allrows'=>true));

	foreach ( $values_dimb as $entry )
	{
		$_dimb[$entry['id']] = "{$entry['id']} - {$entry['descr']}";
	}


	create_select_box('dimb','dimb',$_dimb,'default dimb');
	unset($_dimb);
	unset($ecodimb);
	unset($values_dimb);

	create_select_box('Workorder Approval From','approval_from',$_accounts,'If you need approval from your supervisor for projects/workorders');

	//	if(!$email_org)
	{
		create_input_box('Your Email','email','Insert your email address');
	}

	$email_property=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
	$GLOBALS['phpgw']->preferences->add("email","address",$email_property);
	$GLOBALS['phpgw']->preferences->save_repository();

	$cats->set_appname('property', '.vendor');
	$cat_data	= $cats->formatted_xslt_list(array('globals' => true, 'link_data' =>array()));
	$cat_list = $cat_data['cat_list'];

	if (is_array($cat_list))
	{
		foreach ( $cat_list as $entry )
		{
			$_categories_vendor[$entry['cat_id']] = $entry['name'];
		}
	}

	
	create_select_box('branch TTS','tts_branch_list',$yes_and_no,'enable branch in TTS-orders');
	create_select_box('Default vendor type','default_vendor_category',$_categories_vendor,'which agreement');
	create_input_box('With of textarea','textareacols','With of textarea in forms');
	create_input_box('Height of textarea','textarearows','Height of textarea in forms');

	create_select_box('show horisontal menues','horisontal_menus',array('no' => 'No','yes' => 'Yes'),'Horisontal menues are shown in top of page');
	create_select_box('remove navbar','nonavbar',array('no' => 'No','yes' => 'Yes'),'Navigation bar is removed');
	create_select_box('Tabel export format','export_format',array('excel' => 'Excel','csv' => 'CSV', 'ods' => 'ODS'),'Choose which format to export from the system for tables');

	$default = 'Til: __vendor_name__';
	$default .= "\n";
	$default .= "\n" . 'Fra: __organisation__';
	$default .= "\n" . 'Saksbehandler: __user_name__, ressursnr: __ressursnr__';
	$default .= "\n";
	$default .= "\n" . '__location__';
	$default .= "\n";
	$default .= "\n" . '[b]Beskrivelse av oppdraget:[/b]';
	$default .= "\n" . '__order_description__';
	$default .= "\n";
	$default .= "\n" . '[b]Kontakt på bygget:[/b]';
	$default .= "\n";
	$default .= "\n" . '__contact_name__';
	$default .= "\n" . '__contact_email__';
	$default .= "\n" . '__contact_phone__';
	$default .= "\n";
	$default .= "\n" . '[b]Faktura må merkes med ordrenummer: __order_id__ og ressursnr.: __ressursnr__[/b]';
	$default .= "\n";	
	$default .= "\n" . 'Med hilsen';
	$default .= "\n" . '__user_name__';
	$default .= "\n" . '__user_phone__';
	$default .= "\n" . '__user_email__';
	$default .= "\n" . '__organisation__';

	create_text_area('order email','order_email_template',10,60,'',$default);
