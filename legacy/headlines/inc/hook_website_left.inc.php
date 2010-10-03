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

	$headlines = CreateObject('headlines.headlines');

	$tpl = $GLOBALS['phpgw']->template;
	$tpl->set_root($GLOBALS['phpgw']->common->get_tpl_dir('headlines'));

	$tpl->set_file(array(
		'layout_row' => 'layout_row.tpl',
		'form'       => 'basic.tpl'
	));
	$tpl->set_block('form','channel');
	$tpl->set_block('form','row');

	while($preference = @each($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
	{
		if($preference[0] != 'headlines_layout')
		{
			$sites[] = $preference[0];
		}
	}

	$j = 0;
	$i = count($sites);

	while(list(,$site) = @each($sites))
	{
		$j++;
		$headlines->readtable($site);

		$tpl->set_var('channel_url',$headlines->base_url);
		$tpl->set_var('channel_title',$headlines->display);

		$links = $headlines->getLinks($site);
		@reset($links);
		if($links == False)
		{
			$var = Array(
				'item_link'  => '',
				'item_label' => '',
				'error'      => lang('Unable to retrieve links').'.'
			);
			$tpl->set_var($var);
			$s .= $tpl->parse('o_','row');
		}
		else
		{
			while(list($title,$link) = each($links))
			{
				if($link && $title)
				{
					$var = Array(
						'item_link'  => stripslashes($link),
						'item_label' => stripslashes($title),
						'error'      => ''
					);
					$tpl->set_var($var);
					$s .= $tpl->parse('o_','row');
				}
			}
		}
		$tpl->set_var('rows',$s);
		unset($s);

		$tpl->set_var('section_' . $j,$tpl->parse('o','channel'));

		if ($j == 3 || $i == 1)
		{
			$GLOBALS['phpgw_info']['wcm']['left'] .= $tpl->fp('out','layout_row');
			$tpl->set_var('section_1', '');
			$tpl->set_var('section_2', '');
			$tpl->set_var('section_3', '');
			$j = 0;
		}
		$i--;
	}
	echo $GLOBALS['phpgw_info']['wcm']['left'];
?>
