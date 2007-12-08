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
 	* @version $Id: hook_settings.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/

	'property'	= & $GLOBALS['phpgw_info']['flags']['currentapp'];

	$select_property_filter = array(
		''	=> lang('Owner type'),
		'owner'	=> lang('Owner')

	);
	create_select_box('Choose property filter','property_filter',$select_property_filter,'Filter by owner or owner type');

	$yes_and_no = array(
		'True' => 'Yes',
		''     => 'No'
	);
	create_select_box('show new/updated tickets on main screen','mainscreen_show_new_updated',$yes_and_no,'Dont think this is working - yet');

	create_select_box('Group filters in single query','group_filters',$yes_and_no,'Group filters - means that one has to hit the search button to apply the filter');

	$tts_status = array(
		'' 		=> lang('Open'),
		'closed' 	=> lang('Closed'),
		'all' 		=> lang('All')
	);

	create_select_box('Default ticket status','tts_status',$tts_status,'The default status when entering the helpdesk');

	create_select_box('show quick link for changing status for tickets','tts_status_link',$yes_and_no,'Enables to set status wihout entering the ticket');

	$acc = CreateObject('phpgwapi.accounts');
	$group_list = $acc->get_list('groups');
	foreach ( $group_list as $entry )
	{
		$_groups[$entry['account_id']] = $entry['account_lid'];
	}
	create_select_box('Default group TTS','groupdefault',$_groups,'The default group to assign a ticket in Helpdesk-submodule');

	$account_list = $acc->get_list('accounts',-1,'ASC','account_lastname');

	foreach ( $account_list as $entry )
	{
		if($entry['account_status'] == 'A')
		{
			$_accounts[$entry['account_id']] = $entry['account_firstname'] . ' ' . $entry['account_lastname'];
		}
	}
	create_select_box('Default assign to TTS','assigntodefault',$_accounts,'The default user to assign a ticket in Helpdesk-submodule');

	// Choose the correct priority to display
	$priority_comment[1]  = ' - ' . lang('Lowest');
	$priority_comment[5]  = ' - ' . lang('Medium');
	$priority_comment[10] = ' - ' . lang('Highest');
	for ($i=1; $i<=10; $i++)
	{
		$priority[$i] = $i . $priority_comment[$i];
	}


	// Choose the correct degree to display
		$degree_comment[0]=' - '.lang('None');
		$degree_comment[1]=' - '.lang('Minor');
		$degree_comment[2]=' - '.lang('Medium');
		$degree_comment[3]=' - '.lang('Serious');
	for ($i=0; $i<=3; $i++)
	{
		$degree[$i] = $i . $degree_comment[$i];
	}
	create_select_box('Default Priority TTS','prioritydefault',$priority,'The default priority for tickets in the Helpdesk-submodule');

	$socategory = CreateObject('property.socategory');

	$category_tts= $socategory->select_category_list(array('type'=>'ticket'));

	if (is_array($category_tts))
	{
		foreach ( $category_tts as $entry )
		{
			$_categories_tts[$entry['id']] = $entry['name'];
		}
	}

	unset($sotts);
	create_select_box('Default TTS categories','tts_category',$_categories_tts,'The default category for TTS');

	$yes_and_no = array(
		'1' => 'Yes',
		'2' => 'No'
	);

	create_select_box('Send e-mail from TTS','tts_user_mailnotification',$yes_and_no,'Send e-mail from TTS as default');
	create_input_box('Refresh TTS every (seconds)','refreshinterval','The intervall for Helpdesk refresh - cheking for new tickets');

	create_select_box('Default Degree Request safety','default_safety',$degree,'The degree of seriousness');
	create_select_box('Default Degree Request aesthetics','default_aesthetics',$degree);
	create_select_box('Default Degree Request indoor climate','default_climate',$degree);
	create_select_box('Default Degree Request consequential damage','default_consequential_damage',$degree);
	create_select_box('Default Degree Request user gratification','default_gratification',$degree);
	create_select_box('Default Degree Request residential environment','default_environment',$degree);

	create_select_box('Send order receipt as email ','order_email_rcpt',$yes_and_no,'Send the order as BCC to the user');

	$default_start_page = array(
		'location'   => lang('Location'),
		'project' => lang('Project'),
		'tts' => lang('Ticket'),
		'invoice' => lang('Invoice'),
		'document'=> lang(Document)
		);
	create_select_box('Default start page','default_start_page',$default_start_page,'Select your start-submodule');

	$soworkorder= CreateObject('property.soworkorder');
	$socommon= CreateObject('property.socommon');

	$status_list= $soworkorder->select_status_list();
	$category_list= $socategory->select_category_list(array('type'=>'wo'));

	$district_list= $socommon->select_district_list();

	if ($status_list)
	{
		foreach ( $status_list as $entry )
		{
			$_status[$entry['id']] = $entry['name'];
		}
	}
	if ($category_list)
	{
		foreach ( $category_list as $entry )
		{
			$_categories[$entry['id']] = $entry['name'];
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
	create_select_box('Default project status','project_status',$_status,'The default status for your projects and workorders');
	create_select_box('Default project categories','project_category',$_categories,'The default category for your projects and workorders');
	create_select_box('Default district-filter','default_district',$_districts,'Your default district-filter ');

	create_input_box('Your Cellphone','cellphone');

	create_select_box('Workorder Approval From','approval_from',$_accounts,'If you need approval from your supervisor for projects/workorders');

	if(!$email_org)
	{
		create_input_box('Your Email','email','Insert your email address');
	}

	$email_property=$GLOBALS['phpgw_info']['user']['preferences']['property']['email'];
	$GLOBALS['phpgw']->preferences->add("email","address",$email_property);
	$GLOBALS['phpgw']->preferences->save_repository();

	$cats		= CreateObject('phpgwapi.categories');
	$cats->app_name = 'fm_vendor';
	$cat_data	= $cats->formatted_xslt_list(array('globals' => True, 'link_data' =>array()));
	$cat_list = $cat_data['cat_list'];

	if (is_array($cat_list))
	{
		foreach ( $cat_list as $entry )
		{
			$_categories_vendor[$entry['cat_id']] = $entry['name'];
		}
	}

	create_select_box('Default vendor type','default_vendor_category',$_categories_vendor,'which agreement');
	create_input_box('With of textarea','textareacols','With of textarea in forms');
	create_input_box('Height of textarea','textarearows','Height of textarea in forms');
	

