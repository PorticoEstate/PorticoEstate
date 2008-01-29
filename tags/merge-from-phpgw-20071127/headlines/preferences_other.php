<?php
	/**************************************************************************\
	* phpGroupWare - headlines                                                 *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id: preferences_other.php 13408 2003-09-07 01:53:18Z skeeter $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'headlines',
		'noheader'   => True,
		'nonavbar'   => True
	);
	include('../header.inc.php');

	if ($done)
	{
		$GLOBALS['phpgw']->redirect($GLOBALS['phpgw']->link('/headlines/index.php'));
	}
	else
	{
		unset($GLOBALS['phpgw_info']['flags']['noheader']);
		unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
		$GLOBALS['phpgw']->common->phpgw_header();
	}
	$submit                              = get_var('submit',Array('POST'));
	$GLOBALS['headlines_layout']         = get_var('headlines_layout',Array('POST'));
	$GLOBALS['mainscreen_showheadlines'] = get_var('mainscreen_showheadlines',Array('POST'));

	if($submit)
	{
		$GLOBALS['phpgw']->preferences->change('headlines','headlines_layout',$GLOBALS['headlines_layout'] );
		$GLOBALS['phpgw']->preferences->change('headlines','mainscreen_showheadlines',$GLOBALS['mainscreen_showheadlines']);
		$GLOBALS['phpgw_info']['user']['preferences'] = $GLOBALS['phpgw']->preferences->commit(True);
	}

	$GLOBALS['phpgw']->template->set_file(array(
		'layout1' => 'basic_sample.tpl',
		'layout2' => 'color_sample.tpl',
		'body'    => 'preferences_other.tpl'
	));

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/headlines/preferences_other.php'));
	$GLOBALS['phpgw']->template->set_var('title',lang('Headlines layout'));
	$GLOBALS['phpgw']->template->set_var('action_label',lang('Submit'));
	$GLOBALS['phpgw']->template->set_var('done_label',lang('Done'));
	$GLOBALS['phpgw']->template->set_var('reset_label',lang('Reset'));

	$GLOBALS['phpgw']->template->set_var('template_label',lang('Choose layout'));

	if($submit)
	{
		$selected[$GLOBALS['headlines_layout']] = ' selected';
	}
	else
	{
		$selected[$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout']] = ' selected';
	}

	$mainscreen = $GLOBALS['phpgw_info']['user']['preferences']['headlines']['mainscreen_showheadlines'] ? ' checked' : '';
	$GLOBALS['phpgw']->template->set_var('selected_mainscreen',$mainscreen);
	$GLOBALS['phpgw']->template->set_var('lang_mainscreen', lang('Show headlines on main screen'));

	$s  = '<option value="basic"' . $selected['basic'] . '>' . lang('Basic') . '</option>';
	$s .= '<option value="color"' . $selected['color'] . '>' . lang('Color') . '</option>';
	$GLOBALS['phpgw']->template->set_var('template_options',$s);

	$GLOBALS['phpgw']->template->parse('layout_1','layout1');
	$GLOBALS['phpgw']->template->parse('layout_2','layout2');

	$GLOBALS['phpgw']->template->parse('phpgw_body','body');
//	$GLOBALS['phpgw']->common->phpgw_footer();
?>
