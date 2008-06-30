<?php
  /**************************************************************************\
  * phpGroupWare - email/addressbook                                         *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

	$phpgw_info["flags"] = array(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => 'felamimail',
		'enable_nextmatchs_class' => True
	);

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',$phpgw->common->get_tpl_dir('felamimail'));
	$t->set_file(array(
		'addressbook_list_t' => 'addressbook.tpl',
		'addressbook_list' => 'addressbook.tpl'
	));

	$t->set_block('addressbook_list_t','addressbook_list','list');
	$t->set_block('addressbook_list_t', 'theme_stylesheet','css');
	$template_set = $GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'];

	if(file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/templates/{$template_set}/css/' . $template_set . '.css'))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/{$template_set}/css/{$template_set}.css";
	}
	if(file_exists(PHPGW_SERVER_ROOT . "/phpgwapi/templates/{$template_set}/css/styles.css"))
	{
		$theme_styles[] = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/phpgwapi/templates/{$template_set}/css/styles.css";
	}
	if(isset($theme_styles) && is_array($theme_styles))
	{
		foreach ( $theme_styles as $style )
		{
			$t->set_var('theme_style', $style);
			$t->parse('css','theme_stylesheet',True);
		}
	}

	$d = CreateObject('phpgwapi.contacts');
	$c = CreateObject('phpgwapi.categories');
	$c->app_name = 'addressbook';

	$charset = 'UTF8';
	$t->set_var('charset',$charset);
	$t->set_var('title',(isset($phpgw_info["site_title"])?$phpgw_info["site_title"]:''));
//	$t->set_var('bg_color',$phpgw_info["theme"]["bg_color"]);
	$t->set_var('lang_addressbook_action',lang('Address book'));
//	$t->set_var('font',$phpgw_info["theme"]["font"]);

	$t->set_var('lang_search',lang('Search'));
	$t->set_var('search_action',$phpgw->link('/'.$phpgw_info['flags']['currentapp'].'/addressbook.php'));
	$t->set_var('lang_select_cats',lang('Select category'));

	$start		= get_var('start',array('POST','GET'));
	$query		= get_var('query',array('POST','GET'));
	$sort		= get_var('sort',array('POST','GET'));
	$order		= get_var('order',array('POST','GET'));
	$filter		= get_var('filter',array('POST','GET'));
	$cat_id		= get_var('cat_id',array('POST','GET'));

	if (! $start) { $start = 0; }

	if (!$filter) { $filter = 'none'; }

	if (!$cat_id)
	{
		if ($filter == 'none') { $qfilter  = 'tid=n'; }
		elseif ($filter == 'private') { $qfilter  = 'tid=n,owner='.$phpgw_info["user"]["account_id"]; }
		else { $qfilter = 'tid=n,owner='.$filter; }
	}
	else
	{
		if ($filter == 'none') { $qfilter  = 'tid=n,cat_id='.$cat_id; }
		elseif ($filter == 'private') { $qfilter  = 'tid=n,owner='.$phpgw_info["user"]["account_id"].',cat_id='.$cat_id; }
		else { $qfilter = 'tid=n,owner='.$filter.'cat_id='.$cat_id; }
	}

	if($phpgw_info["user"]["preferences"]["common"]["maxmatchs"] && $phpgw_info["user"]["preferences"]["common"]["maxmatchs"] > 0)
	{
		$offset = $phpgw_info["user"]["preferences"]["common"]["maxmatchs"];
	}
	else
	{
		$offset = 15;
	}

	$account_id = $phpgw_info['user']['account_id'];

	$cols = array (
		'n_given'    => 'n_given',
		'n_family'   => 'n_family',
		'email'      => 'email',
		'email_home' => 'email_home'
	);

//	$entries = $d->read($start,$offset,$cols,$query,$qfilter,$sort,$order,$account_id);

	$fields = array ('contact_id', 'per_first_name', 'per_last_name', 'email', 'email_home','n_given','n_family');

	if($query)
	{
		$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_first_name', $query);
		$criteria_search[] = phpgwapi_sql_criteria::token_begin('per_last_name', $query);
		$criteria_search[] = phpgwapi_sql_criteria::token_has('email', $query);	
		$criteria[] = phpgwapi_sql_criteria::_append_or($criteria_search);
	}

	$criteria[] = $d->criteria_for_index((int) $GLOBALS['phpgw_info']['user']['account_id']);

	if ($cat_id)
	{
		$criteria[] = phpgwapi_sql_criteria::_equal('cat_id', $cat_id);
	}

	$criteria_token = phpgwapi_sql_criteria::_append_and($criteria);
	$entries = $d->get_persons($fields, 0, 0, 'per_first_name, per_last_name', 'ASC', '', $criteria_token);

	//------------------------------------------- nextmatch --------------------------------------------
	$left = $phpgw->nextmatchs->left('/'.$phpgw_info['flags']['currentapp'].'/addressbook.php',$start,$d->total_records,array('order'=>$order,'filter'=>$filter,'sort'=>$sort,'query'=>$query));
	$right = $phpgw->nextmatchs->right('/'.$phpgw_info['flags']['currentapp'].'/addressbook.php',$start,$d->total_records,array('order'=>$order,'filter'=>$filter,'sort'=>$sort,'query'=>$query));
	$t->set_var('left',$left);
	$t->set_var('right',$right);

	if ($d->total_records > $phpgw_info["user"]["preferences"]["common"]["maxmatchs"])
	{
		$t->set_var('lang_showing',lang("showing %1 - %2 of %3",($start + 1),($start + $phpgw_info["user"]["preferences"]["common"]["maxmatchs"]),$d->total_records));
	}
	else
	{
		$t->set_var('lang_showing',lang("showing %1",$d->total_records));
	}
	// --------------------------------------- end nextmatch ------------------------------------------

	// ------------------- list header variable template-declaration -----------------------
	$t->set_var('sort_firstname',$phpgw->nextmatchs->show_sort_order($sort,'n_given',$order,'/'.$phpgw_info['flags']['currentapp'].'/addressbook.php',lang('Firstname')));
	$t->set_var('sort_lastname',$phpgw->nextmatchs->show_sort_order($sort,'n_family',$order,'/'.$phpgw_info['flags']['currentapp'].'/addressbook.php',lang('Lastname')));
	$t->set_var('lang_email',lang('Select work email address'));
	$t->set_var('lang_hemail',lang('Select home email address'));
	$t->set_var('cats_action',$phpgw->link('/'.$phpgw_info['flags']['currentapp'].'/addressbook.php',array('sort'=>$sort,'order'=>$order,'filter'=>$filter,'start'=>$start,'query'=>$query,'cat_id'=>$cat_id)));
	$t->set_var('cats_list',$c->formated_list('select','all',$cat_id,'True'));
	$t->set_var('lang_select',lang('Select'));

	// --------------------------- end header declaration ----------------------------------
	$tr_class = 'row_on';
	for ($i=0;$i<count($entries);$i++)
	{
		$tr_class = $phpgw->nextmatchs->alternate_row_class($tr_class);
		$t->set_var('tr_class',$tr_class);
		$firstname = $entries[$i]['n_given'];
		if (!$firstname) { $firstname = '&nbsp;'; }
		$lastname = $entries[$i]['n_family'];
		if (!$lastname) { $lastname = '&nbsp;'; }
		$fullname = trim($firstname." ".$lastname);
	//	$id     = $entries[$i]['id'];
		$email  = $entries[$i]['email'];
		$hemail = $entries[$i]['email_home'];
		// --------------------- template declaration for list records --------------------------
		$t->set_var(array(
			'firstname' => $firstname,
			'lastname'  => $lastname
		));

	//	$t->set_var('id',$id);
		$t->set_var('email',$email);
		$t->set_var('hemail',$hemail);
		$t->set_var('realName',$fullname);

		$t->parse('list','addressbook_list',True);
	}
	// --------------------------- end record declaration ---------------------------

	$t->set_var('lang_done',lang('Done'));
	$t->parse('out','addressbook_list_t',True);
	$t->p('out');

	$phpgw->common->phpgw_exit();
