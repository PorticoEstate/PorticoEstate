<?php
	/**************************************************************************\
	* phpGroupWare - Headlines Administration                                  *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only' => True,
		'currentapp' => 'headlines',
		'nonavbar'   => True,
		'noheader'   => True
	);
	include('../header.inc.php');

	$submit = get_var('submit',array('POST'));

	if($submit)
	{
		$n_display   = get_var('n_display',array('POST'));
		$n_base_url  = get_var('n_base_url',array('POST'));
		$n_newsfile  = get_var('n_newsfile',array('POST'));
		$n_cachetime = get_var('n_cachetime',array('POST'));
		$n_listings  = get_var('n_listings',array('POST'));
		$n_cachetime = get_var('n_cachetime',array('POST'));
		$n_newstype  = get_var('n_newstype',array('POST'));

		if(!$n_display)
		{
			$errors[] = lang('You must enter a display');
		}

		if(!$n_base_url)
		{
			$errors[] = lang('You must enter a base url');
		}

		if(!$n_newsfile)
		{
			$errors[] = lang('You must enter a news url');
		}

		if(!$n_cachetime)
		{
			$errors[] = lang('You must enter the number of minutes between reload');
		}

		if(!$n_listings)
		{
			$errors[] = lang('You must enter the number of listings display');
		}

		if($n_listings && !ereg('^[0-9]+$',$n_listings))
		{
			$errors[] = lang('You can only enter numbers for listings display');
		}

		if($n_cachetime && !ereg('^[0-9]+$',$n_cachetime))
		{
			$errors[] = lang('You can only enter numbers minutes between refresh');
		}

		$GLOBALS['phpgw']->db->query("select display from phpgw_headlines_sites where base_url='"
			. addslashes(strtolower($n_base_url)) . "' and newsfile='"
			. addslashes(strtolower($n_newsfile)) . "'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();
		if($GLOBALS['phpgw']->db->f('display'))
		{
			$errors[] = lang('That site has already been entered');
		}

		if(!is_array($errors))
		{
			$sql = "INSERT INTO phpgw_headlines_sites (display,base_url,newsfile,"
				. "lastread,newstype,cachetime,listings) "
				. "VALUES('" . addslashes($n_display) . "','"
				. addslashes(strtolower($n_base_url)) . "','" 
				. addslashes(strtolower($n_newsfile)) . "',0,'"
				. $n_newstype . "',$n_cachetime,$n_listings)";

			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);

			$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/headlines/admin.php','cd=28'));
		}
	}

	$GLOBALS['phpgw']->common->phpgw_header();

	// This is done for a reason (jengo)
	$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('headlines'));

	$GLOBALS['phpgw']->template->set_file(array(
		'admin_form' => 'admin_form.tpl'
	));
	$GLOBALS['phpgw']->template->set_block('admin_form','form');
	$GLOBALS['phpgw']->template->set_block('admin_form','buttons');

	if(@is_array($errors))
	{
		$GLOBALS['phpgw']->template->set_var('messages',$GLOBALS['phpgw']->common->error_list($errors));
	}

	$GLOBALS['phpgw']->template->set_var('title',lang('Headlines Administration'));
	$GLOBALS['phpgw']->template->set_var('lang_header',lang('Create new headline'));
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
	$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
	$GLOBALS['phpgw']->template->set_var('lang_display',lang('Display'));
	$GLOBALS['phpgw']->template->set_var('lang_base_url',lang('Base URL'));
	$GLOBALS['phpgw']->template->set_var('lang_news_file',lang('News File'));
	$GLOBALS['phpgw']->template->set_var('lang_minutes',lang('Minutes between refresh'));
	$GLOBALS['phpgw']->template->set_var('lang_listings',lang('Listings Displayed'));
	$GLOBALS['phpgw']->template->set_var('lang_type',lang('News Type'));
	$GLOBALS['phpgw']->template->set_var('lang_button',lang('Add'));

	$GLOBALS['phpgw']->template->set_var('input_display','<input name="n_display" value="' . $n_display . '" size="40">');
	$GLOBALS['phpgw']->template->set_var('input_base_url','<input name="n_base_url" value="' . $n_base_url . '" size="40">');
	$GLOBALS['phpgw']->template->set_var('input_news_file','<input name="n_newsfile" value="' . $n_newsfile . '" size="40">');
	$GLOBALS['phpgw']->template->set_var('input_minutes','<input name="n_cachetime" value="' . $n_cachetime . '" size="4">');
	$GLOBALS['phpgw']->template->set_var('input_listings','<input name="n_listings" value="' . $n_listings . '" size="2">');

	$news_type = array('rdf','fm','lt','sf','rdf-chan');
	while(list(,$item) = each($news_type))
	{
		$_select .= '<option value="' . $item . '"' . ($n_newstype == $item ? ' checked' : '')
			. '>' . $item . '</option>';
	}
	$GLOBALS['phpgw']->template->set_var('input_type','<select name="n_newstype">' . $_select . '</select>');

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/headlines/newheadline.php'));

	$GLOBALS['phpgw']->template->parse('buttons','buttons');
	$GLOBALS['phpgw']->template->pfp('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
