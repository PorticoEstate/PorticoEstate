<?php
  /**************************************************************************\
  * phpGroupWare application (NNTP)                                          *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id$ */

	$phpgw_flags = Array(
		'currentapp' => 'nntp',
		'noheader'   => True,
		'nonavbar'   => True
	);

	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;

	include('../header.inc.php');

	$news = Array();
	while($pref = @each($GLOBALS['phpgw_info']['user']['preferences']['nntp']))
	{
		$news[]=$pref[0];
	}

	if(count($news) == 0)
	{
		header('Location: '.$GLOBALS['phpgw']->link('/nntp/preferences.php'));
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	function close_routine()
	{
		$GLOBALS['nntp']->close_port();
	}

	@set_time_limit(0);
	$GLOBALS['nntp']->display = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	if ($GLOBALS['nntp']->errorset)
	{
		echo $GLOBALS['nntp']->error['msg'].':'.$GLOBALS['nntp']->error['desc']."<br>\n";
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	else
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
	}

	register_shutdown_function('close_routine');

	$p = CreateObject('phpgwapi.Template',$phpgw->common->get_tpl_dir('nntp'));
	$templates = array(
		'index_form' => 'index.tpl'
	);
	$p->set_file($templates);
	$p->set_block('index_form','index','index');
	$p->set_block('index_form','layout_table','layout_table');
	$p->set_block('index_form','basic_row','basic_row');

	reset($news);
	$j = 0;
	$i = count($news);

	while ($group = each($news))
	{
		$j++;
		$s = '';
		if($GLOBALS['nntp']->read_table(intval($group[1])))
		{
			$channel_url = '<a href="'.$GLOBALS['phpgw']->link('/nntp/viewgroup.php','folder='.$group[1]).'" target="new" style="text-decoration: none; color: #000000; text-align: center">';
			$channel_title = $GLOBALS['nntp']->mailbox.'</a>';
		}
		else
		{
			$channel_url = '';
			$channel_title = $GLOBALS['nntp']->error['msg'].':'.$GLOBALS['nntp']->error['desc'];
		}
		
		$var = Array(
			'channel_url'   => $channel_url,
			'channel_title' => $channel_title
		);

		$p->set_var($var);

		if ($nntp->errorset)
		{
//			echo 'Found an error reading group: '.$group[1]."<br>\n";
			$p->set_var('item_link','');
			$p->set_var('item_label',$GLOBALS['nntp']->error['msg'].':'.$GLOBALS['nntp']->error['desc']);
			$s .= $p->fp('o_','basic_row');
		}
		elseif ($GLOBALS['nntp']->active == 'N')
		{
			$p->set_var('item_link','');
			$p->set_var('item_label',lang('%1 not active',$GLOBALS['nntp']->mailbox));
			$s .= $p->fp('o_','basic_row');
		}
		else
		{
			$links = $GLOBALS['nntp']->get_subjects();
			if (count($links)==0 || $links == 0)
			{
				$p->set_var('channel_url','');
				$p->set_var('channel_title',$GLOBALS['nntp']->mailbox);
				$p->set_var('item_link','');
				$p->set_var('item_label','No articles found!');
				$s .= $p->fp('o_','basic_row');
			}
			else
			{
				krsort($links);
				$clinks = count($links);
				for ($k=0,reset($links); $k<$clinks; $k++,next($links))
				{
					$key = key($links);
					$link_url = $GLOBALS['phpgw']->link('/nntp/read_article.php','folder='.$group[1].'&msgnum='.$key);
					$p->set_var('item_link','<a href="'.$link_url.'">');
					$p->set_var('item_label',$links[$key].'</a>');
					$s .= $p->fp('o_','basic_row');
				}
			}
		}
		$p->set_var('rows',$s);
		$p->set_var('section_' . $j,$p->fp('o','layout_table'));

		if ($j == 3 || $i == 1)
		{
			$p->pfp('out','index');
			$p->set_var('section_1', '');
			$p->set_var('section_2', '');
			$p->set_var('section_3', '');
			$j = 0;
		}
		$i--;
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
