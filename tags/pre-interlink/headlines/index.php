<?php
	/**************************************************************************\
	* phpGroupWare - news headlines                                            *
	* http://www.phpgroupware.org                                              *
	* Written by Mark Peters <mpeters@satx.rr.com>                             *
	* Based on pheadlines 0.1 19991104 by Dan Steinman <dan@dansteinman.com>   *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp'           => 'headlines',
		'enable_network_class' => True,
		'noheader'             => True,
		'nonavbar'             => True
	);
	include('../header.inc.php');

	if(!count($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
	{
		Header('Location: ' . $GLOBALS['phpgw']->link('/headlines/preferences.php'));
	}
	else
	{
		unset($GLOBALS['phpgw_info']['flags']['noheader']);
		unset($GLOBALS['phpgw_info']['flags']['nonavbar']);
		$GLOBALS['phpgw']->common->phpgw_header();
	}

	if(!$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'])
	{
		$GLOBALS['phpgw']->preferences->change('headlines','headlines_layout','basic');
		$GLOBALS['phpgw']->preferences->commit(True);
		$GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'] = 'basic';
	}

	while($preference = each($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
	{
		if($preference[0] != 'headlines_layout' &&
			$preference[0] != 'mainscreen_showheadlines')
		{
			$sites[] = $preference[0];
		}
	}

	$headlines = CreateObject('headlines.headlines');

	$t = &$GLOBALS['phpgw']->template;

	$t->set_file(array(
		'layout_row' => 'layout_row.tpl',
		'form'       => $GLOBALS['phpgw_info']['user']['preferences']['headlines']['headlines_layout'] . '.tpl'
	));
	$t->set_block('form','channel');
	$t->set_block('form','row');

	$l = '';

	$j = 0;
	$i = count($sites);
	if(!$i)
	{
		$l = '<center>' . lang('please set your preferences for this application') . '.</center>';
	}
	reset($sites);
	while(list(,$site) = @each($sites))
	{
		$j++;
		$headlines->readtable($site);

		$t->set_var('channel_url',$headlines->base_url);
		$t->set_var('channel_title',$headlines->display);

		$links = $headlines->getLinks($site);
		if($links == False)
		{
			$var = Array(
				'item_link'  => '',
				'item_label' => '',
				'error'      => lang('Unable to retrieve links').'.'
			);
			$t->set_var($var);
			$s .= $t->parse('o_','row');
		}
		else
		{
			while(list($title,$link) = @each($links))
			{
				if($link && $title)
				{
					$var = Array(
						'item_link'  => stripslashes($link),
						'item_label' => stripslashes($title),
						'error'      => ''
					);
					$t->set_var($var);
					$s .= $t->parse('o_','row');
				}
			}
		}
		$t->set_var('rows',$s);
		unset($s);

		$t->parse('section_' . $j,'channel');
		$t->set_var('rows','');

		if($j == 3 || $i == 1)
		{
			$l .= $t->fp('output','layout_row');
			$t->set_var('section_1', '');
			$t->set_var('section_2', '');
			$t->set_var('section_3', '');
			$j = 0;
		}
		$i--;
	}

	$t->set_var('phpgw_body',$l);
?>
