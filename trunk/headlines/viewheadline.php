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
		'currentapp'              => 'admin',
		'enable_nextmatchs_class' => True,
		'nonavbar'                => True,
		'noheader'                => True
	);
	include('../header.inc.php');

	$con = get_var('con',array('POST','GET'));
	if(!$con)
	{
		$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/headlines/admin.php'));
	}

	$GLOBALS['phpgw']->common->phpgw_header();

	$GLOBALS['phpgw']->db->query("SELECT * FROM phpgw_headlines_sites WHERE con='$con'",__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->next_record();

	// This is done for a reason (jengo)
	$GLOBALS['phpgw']->template->set_root($GLOBALS['phpgw']->common->get_tpl_dir('headlines'));

	$GLOBALS['phpgw']->template->set_file(array(
		'admin_form' => 'admin_form.tpl'
	));
	$GLOBALS['phpgw']->template->set_block('admin_form','form');
	$GLOBALS['phpgw']->template->set_block('admin_form','listing_row');
	$GLOBALS['phpgw']->template->set_block('admin_form','listing_rows');

	$GLOBALS['phpgw']->template->set_var('title',lang('Headlines Administration'));
	$GLOBALS['phpgw']->template->set_var('lang_header',lang('View headline'));
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('row_on',$GLOBALS['phpgw_info']['theme']['row_on']);
	$GLOBALS['phpgw']->template->set_var('row_off',$GLOBALS['phpgw_info']['theme']['row_off']);
	$GLOBALS['phpgw']->template->set_var('lang_display',lang('Display'));
	$GLOBALS['phpgw']->template->set_var('lang_base_url',lang('Base URL'));
	$GLOBALS['phpgw']->template->set_var('lang_news_file',lang('News File'));
	$GLOBALS['phpgw']->template->set_var('lang_minutes',lang('Minutes between refresh'));
	$GLOBALS['phpgw']->template->set_var('lang_listings',lang('Listings Displayed'));
	$GLOBALS['phpgw']->template->set_var('lang_type',lang('News Type'));

	$GLOBALS['phpgw']->template->set_var('input_display',$GLOBALS['phpgw']->db->f('display'));
	$GLOBALS['phpgw']->template->set_var('input_base_url',$GLOBALS['phpgw']->db->f('base_url'));
	$GLOBALS['phpgw']->template->set_var('input_news_file',$GLOBALS['phpgw']->db->f('newsfile'));
	$GLOBALS['phpgw']->template->set_var('input_minutes',$GLOBALS['phpgw']->common->show_date($GLOBALS['phpgw']->db->f('lastread')));
	$GLOBALS['phpgw']->template->set_var('input_listings',$GLOBALS['phpgw']->db->f('cachetime'));
	$GLOBALS['phpgw']->template->set_var('input_type',$GLOBALS['phpgw']->db->f('newstype'));

	$GLOBALS['phpgw']->db->query("SELECT title,link FROM phpgw_headlines_cached WHERE site='$con'",__LINE__,__FILE__);

	$GLOBALS['phpgw']->template->set_var('th_bg2',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('lang_current_cache',lang('Current headlines in cache'));

	if($GLOBALS['phpgw']->db->num_rows() == 0)
	{
		$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
		$GLOBALS['phpgw']->template->set_var('value',lang('None'));
		$GLOBALS['phpgw']->template->parse('listing_rows','listing_row',True);
	}

	while($GLOBALS['phpgw']->db->next_record())
	{
		$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);
		$GLOBALS['phpgw']->template->set_var('value','<a href="' . $GLOBALS['phpgw']->db->f('link') . '" target="_new">' . $GLOBALS['phpgw']->db->f('title') . '</a>');
		$GLOBALS['phpgw']->template->parse('listing_rows','listing_row',True);
	}

	$GLOBALS['phpgw']->template->pfp('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
