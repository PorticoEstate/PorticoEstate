<?php
  /**************************************************************************\
  * phpGroupWare module (NNTP)                                               *
  * http://www.phpgroupware.org                                              *
  * Written by Mark Peters <mpeters@satx.rr.com>                             *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

	/* $Id: admin.php 10901 2002-09-15 23:23:02Z skeeter $ */

	if ((isset($submit) && $submit) && (isset($nntplist) && $nntplist))
	{
		$phpgw_flags = Array(
			'currentapp'					=>	'nntp',
			'admin_header'					=>	True,
			'enable_nextmatchs_class'	=>	True,
			'noheader'						=>	True,
			'nonavbar'						=>	True
		);
	}
	else
	{
		$phpgw_flags = Array(
			'currentapp'					=>	'nntp',
			'admin_header'					=>	True,
			'enable_nextmatchs_class'	=>	True
		);
	}
  
	$GLOBALS['phpgw_info']['flags'] = $phpgw_flags;
	include('../header.inc.php');

	function get_tg()
	{
		$GLOBALS['phpgw']->db->query('SELECT count(con) FROM newsgroups');
		$GLOBALS['phpgw']->db->next_record();
		$tg = $GLOBALS['phpgw']->db->f(0);
		if($tg == 0)
		{
			@set_time_limit(0);
			$GLOBALS['nntp']->load_table();
			return get_tg();
		}
		else
		{
			return $tg;
		}
	}

	if((!isset($submit) || !$submit) && (!isset($nntplist) || !$nntplist))
	{
		if (!isset($tg) || !$tg)
		{
			$tg = intval(get_tg());
		}
		else
		{
			settype($tg,'integer');
		}

		//$phpgw->common->phpgw_header();
		//echo parse_navbar();

		$max = intval($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']);

		$urlname = '/nntp/admin.php';

		$p = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('nntp'));
		$templates = Array(
			'nntp_form'	=>	'nntp.tpl'
		);
		
		$p->set_file($templates);

		$p->set_block('nntp_form','nntp','nntp');
		$p->set_block('nntp_form','nntp_list','nntp_list');

		if (!isset($start) || !$start)
		{
			$start = 0;
		}
     
		if (!isset($query_result) || !$query_result)
		{
			$query_result = 0;
		}

		$orderby = '';

		if (!isset($order) || !$order)
		{
			$order = 1;
		}

		if (isset($order) && $order)
		{
			switch ($order)
			{
				case 1:
					$orderby = ' ORDER BY CON '.$sortorder;
					break;
				case 2:
					$orderby = ' ORDER BY NAME '.$sortorder;
					break;
				case 3:
					$orderby = ' ORDER BY ACTIVE '.$sortorder;
					break;
			}
		}

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
			
			$GLOBALS['phpgw']->db->limit_query("SELECT con FROM newsgroups WHERE name LIKE '%$query%'$orderby ",$start,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$start = (int)$GLOBALS['phpgw']->db->f('con') - 1;
			if($start < 0)
			{
				$start=0;
			}
		}
		
		$common_hidden_vars = '<input type="hidden" name="start" value="'.$start.'">'
			. '<input type="hidden" name="stop" value="'.($start + $max).'">'
			. '<input type="hidden" name="tg" value="'.$tg.'">';
			
		if (isset($query_result) && $query_result)
		{
			$common_hidden_vars .= '<input type="hidden" name="query_result" value="'.$query_result.'">';
		}

		
		$extra_parms = '&tg='.$tg;

		if(isset($sortorder) && $sortorder)
		{
			$extra_parms .= '&sortorder='.$sortorder;
		}
		if(isset($order) && $order)
		{
			$extra_parms .= '&order='.$order;
		}

		$var = Array(
			'search_value'			=>	(isset($query) && $query?$query:''),
			'search'				=>	lang('search'),
			'next'					=>	lang('next'),
			'nml'					=>	$GLOBALS['phpgw']->nextmatchs->left($urlname,intval($start),intval($tg),$extra_parms),
			'nmr'					=>	$GLOBALS['phpgw']->nextmatchs->right($urlname,intval($start),intval($tg),$extra_parms),
			'title'					=>	lang('Newsgroups'),
			'action_url'			=>	$GLOBALS['phpgw']->link('/nntp/admin.php'),
			'common_hidden_vars'	=>	$common_hidden_vars,
			'th_bg'					=>	$GLOBALS['phpgw_info']['theme']['th_bg'],
			'th_font'				=>	$GLOBALS['phpgw_info']['theme']['font'],
			'sort_con'				=>	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'1',$order,$urlname,' # ','&tg='.$tg),
			'sort_group'			=>	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'2',$order,$urlname,lang('Group'),'&tg='.$tg),
			'sort_active'			=>	$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'3',$order,$urlname,lang('Active'),'&tg='.$tg)
		);

		$p->set_var($var);

		if ($max <= $tg - $start)
		{
			$totaltodisplay = $max;
		}
		else
		{
			$totaltodisplay = ($tg - $start) - 1;
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

		$GLOBALS['phpgw']->db->limit_query('SELECT con, name, active FROM newsgroups'.$orderby,$start);

		for ($i=0;$i<$totaltodisplay;$i++)
		{
			$GLOBALS['phpgw']->db->next_record();
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
			$con = $GLOBALS['phpgw']->db->f('con');
			
			$name = $GLOBALS['phpgw']->db->f('name');
			if (!$name)
			{
				$name  = '&nbsp;';
			}
			$group_name = $name;

			$active = $GLOBALS['phpgw']->db->f('active');
			if ($active == 'Y')
			{
				$checked = ' checked';
			}
			else
			{
				$checked = '';
			}
			$active_var = '<input type="checkbox" name="nntplist[]" value="'.$con.'"'.$checked.'>';
			
			$var = Array(
				'tr_color'	=>	$tr_color,
				'con'			=>	$con,
				'group'		=>	$group_name,
				'active'		=>	$active_var
			);

			$p->set_var($var);

				$p->parse('rows','nntp_list',True);
		}
		$var = Array(
			'lang_update'	=>	lang('update'),
			'checkmark'		=>	$GLOBALS['phpgw']->common->get_image_path('email').'/check'
		);

		$p->set_var($var);

		$p->pparse('out','nntp');

		$GLOBALS['phpgw']->common->phpgw_footer();
	}
	else
	{
		$GLOBALS['phpgw']->db->lock('newsgroups');

		$GLOBALS['phpgw']->db->query("UPDATE newsgroups SET active='N' WHERE con>=$start AND con<=$stop");

		for ($i=0;$i<count($nntplist);$i++)
		{
			$GLOBALS['phpgw']->db->query("UPDATE newsgroups SET active='Y' WHERE con=".$nntplist[$i]);
		}
		$GLOBALS['phpgw']->db->unlock();

		Header('Location: ' . $GLOBALS['phpgw']->link('/nntp/admin.php','start='.$start.'&tg='.$tg));
	}
?>
