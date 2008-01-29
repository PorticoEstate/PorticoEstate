<?php
  /**************************************************************************\
  * phpGroupWare - NNTP administration                                       *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: preferences.php 8699 2001-12-21 03:59:13Z milosch $ */

	$phpgw_flags = Array(
		'currentapp' => 'nntp',
		'enable_nextmatchs_class' => True,
		'noheader'   => True,
		'nonavbar'   => True
	);
	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;

	include('../header.inc.php');

	function get_tg()
	{
		$db = $GLOBALS['phpgw']->db;
		$db->query("SELECT count(con) FROM newsgroups WHERE active='Y'");
		$db->next_record();
		$con = $db->f(0);
		unset($db);
		return $con;
	}

	if((@$submit) && (@$nntplist))
	{
		$GLOBALS['phpgw']->preferences->read_repository();

		$minarray = unserialize(urldecode($nntparray));
		reset($minarray);

		while(list($key,$value) = each($minarray))
		{
//echo "Deleting Preference $value<br>\n";
			if($GLOBALS['phpgw_info']['user']['preferences']['nntp'][$value])
			{
				$GLOBALS['phpgw']->preferences->delete('nntp',$value);
			}
		}

		for ($i=0;$i<count($nntplist);$i++)
		{
			$GLOBALS['phpgw']->preferences->add('nntp',$nntplist[$i],True);
		}
		$GLOBALS['phpgw']->preferences->save_repository(True);

		Header('Location: ' . $GLOBALS['phpgw']->link('/nntp/preferences.php','start='.$start.'&tg='.$tg));
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	
	if (!isset($start) || !$start)
	{
		$start = 0;
	}
	if (!isset($query_result) || !$query_result)
	{
		$query_result = 0;
	}

	$orderby = '';
	if (isset($order) && $order)
	{
		switch ($order)
		{
		case 1:
			$orderby = ' ORDER BY CON '.$sort;
			break;
		case 2:
			$orderby = ' ORDER BY NAME '.$sort;
			break;
		case 3:
			$orderby = ' ORDER BY ACTIVE '.$sort;
			break;
		}
	}
	else
	{
		$orderby = '';
	}
	$db2 = $GLOBALS['phpgw']->db;
	if ((isset($search) && $search) || (isset($next) && $next))
	{
		if (isset($search) && $search)
		{
			$query_result = 0;
		}
		else
		{
			$query_result++;
		}
		$db2->query("SELECT name FROM newsgroups WHERE active='Y'$orderby");
		$j = 0;
		$i = 0;
		while($db2->next_record())
		{
			if (stristr($db2->f('name'),$query))
			{
				if($i==$query_result)
				{
					$start = $j;
					break;
				}
				else
				{
					$i++;
				}
			}
			$j++;
		}
	}

	$querystr = "SELECT con, name FROM newsgroups WHERE active='Y'$orderby";
	$db2->limit_query($querystr,$start,__LINE__,__FILE__);

	if(!$db2->num_rows())
	{
		header('Location: '.$GLOBALS['phpgw']->link('/nntp/admin.php'));
		$GLOBALS['phpgw']->common->phpgw_exit();
	}
	else
	{
		$GLOBALS['phpgw']->common->phpgw_header();
		echo parse_navbar();
		$minarray = Array();
		$nntpavail = Array();
		while($db2->next_record())
		{
			$nntpavail[] = Array(
				'con'  => $db2->f('con'),
				'name' => $db2->f('name')
			);
			$minarray[] = $db2->f('con');
		}
		unset($db2);
	}

	$p = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('nntp'));
	$templates = Array(
		'nntp_form' => 'nntp.tpl'
	);

	$p->set_file($templates);

	$p->set_block('nntp_form','nntp','nntp');
	$p->set_block('nntp_form','nntp_list','nntp_list');

	if (!isset($tg) || !$tg)
	{
		$tg = get_tg();
	}
	$first = ($minarray)?min($minarray):0;

	@reset($minarray);

	$common_hidden_vars = '<input type="hidden" name="start" value="'.$start.'">'."\n"
		. '<input type="hidden" name="first" value="'.$first.'">'."\n"
		. '<input type="hidden" name="nntparray" value="'.urlencode(serialize($minarray)).'">'."\n"
		. '<input type="hidden" name="tg" value="'.$tg.'">'."\n";
	if(isset($order) && $order)
	{
		$common_hidden_vars .= '<input type="hidden" name="order" value="'.$order.'">'."\n";
	}
	if(isset($sort) && $sort)
	{
		$common_hidden_vars .= '<input type="hidden" name="sort" value="'.$sort.'">'."\n";
	}
	if(isset($query_result) && $query_result)
	{
		$common_hidden_vars .= '<input type="hidden" name="query_result" value="'.$query_result.'">'."\n";
	}

	$extra_parms = '&tg='.$tg;
	if(isset($sort) && $sort)
	{
		$extra_parms .= '&sort='.$sort;
	}
	if(isset($order) && $order)
	{
		$extra_parms .= '&order='.$order;
	}

	$urlname = '/nntp/preferences.php';

	$var = Array(
		'search_value'			=> (isset($query) && $query?$query:''),
		'search'				=> lang('search'),
		'next'					=> lang('next'),
		'nml'					=> $GLOBALS['phpgw']->nextmatchs->left($urlname,$start,$tg,$extra_parms),
		'nmr'					=> $GLOBALS['phpgw']->nextmatchs->right($urlname,$start,$tg,$extra_parms),
		'title'					=> lang('Newsgroups'),
		'action_url'			=> $GLOBALS['phpgw']->link('/nntp/preferences.php'),
		'common_hidden_vars'	=> $common_hidden_vars,
		'th_bg'					=> $GLOBALS['phpgw_info']['theme']['th_bg'],
		'th_font'				=> $GLOBALS['phpgw_info']['theme']['font'],
		'sort_con'				=> $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'1',$order,$urlname,' # ','&tg='.$tg),
		'sort_group'			=> $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'2',$order,$urlname,lang('Name'),'&tg='.$tg),
		'sort_active'			=> $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'3',$order,$urlname,' '.lang('Active').' ','&tg='.$tg)
	);

	$p->set_var($var);

	if (isset($GLOBALS['phpgw_info']['user']['preferences']['nntp']))
	{
		reset($GLOBALS['phpgw_info']['user']['preferences']['nntp']);
	}

	if(count($GLOBALS['phpgw_info']['user']['preferences']['nntp']))
	{
		while($pref = each($GLOBALS['phpgw_info']['user']['preferences']['nntp']))
		{
			$found[$pref[0]] = ' checked';
		}
	}

	reset($nntpavail);
	while(list($key,$value) = each($nntpavail))
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);

		if (!$value['name'])
		{
			$value['name']  = '&nbsp;';
		}
		$var = Array(
			'tr_color' => $tr_color,
			'con'      => $value['con'],
			'group'    => $value['name'],
			'active'   => '<input type="checkbox" name="nntplist[]" value="'.$value['con'].'"'.$found[$value['con']].'>'
		);

		$p->set_var($var);
		$p->parse('rows','nntp_list',True);
	}

	$var = Array(
		'lang_update' => lang('update'),
		'checkmark'   => $GLOBALS['phpgw']->common->get_image_path('email').'/check.gif'
	);

	$p->set_var($var);

	$p->pparse('out','nntp');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
