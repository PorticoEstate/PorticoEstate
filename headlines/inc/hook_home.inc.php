<?php
  /**************************************************************************\
  * phpGroupWare - Headlines                                                  *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <skeeter@phpgroupware.org>                        *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: hook_home.inc.php 13408 2003-09-07 01:53:18Z skeeter $ */

	$d1 = strtolower(substr(PHPGW_APP_INC,0,3));
	if($d1 == 'htt' || $d1 == 'ftp' )
	{
		echo 'Failed attempt to break in via an old Security Hole!<br>'."\n";
		$GLOBALS['phpgw_info']['flags']['nodisplay'] = True;
		exit;
	}
	unset($d1);

	if ($GLOBALS['phpgw_info']['user']['preferences']['calendar']['mainscreen_showevents'])
	{
		while($preference = each($GLOBALS['phpgw_info']['user']['preferences']['headlines']))
		{
			if($preference[0] != 'headlines_layout' &&
				$preference[0] != 'mainscreen_showheadlines')
			{
				$sites[] = $preference[0];
			}
		}

		$headlines = CreateObject('headlines.headlines');

		$t = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('headlines'));

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

		$app_id = $GLOBALS['phpgw']->applications->name2id('headlines');
		$GLOBALS['portal_order'][] = $app_id;

		$GLOBALS['phpgw']->portalbox->set_params(
			array(
				'app_id'	=> $app_id,
				'title'	        => lang('headlines')
			)
		);
		$GLOBALS['phpgw']->portalbox->draw($l);
	}
?>
