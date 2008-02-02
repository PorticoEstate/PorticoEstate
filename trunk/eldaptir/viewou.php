<?php
  /**************************************************************************\
  * phpGroupWare - eLDAPtir - LDAP Administration                            *
  * http://www.phpgroupware.org                                              *
  * Written by Miles Lott <milosch@phpgroupware.org>                         *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'eldaptir',
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array('entries' => 'entries.tpl'));
	$GLOBALS['phpgw']->template->set_block('entries','list','list');
	$GLOBALS['phpgw']->template->set_block('entries','row','row');
	$GLOBALS['phpgw']->template->set_block('entries','empty_row','empty_row');

	$servers = servers();
	$server_type = $servers[$server_id]['type'];
	$ldapobj = CreateObject('eldaptir.ldap',$servers[$server_id]);

	$start  = get_var('start',Array('GET','POST'));
	$query  = get_var('query',Array('POST'));
	$sort   = get_var('sort',Array('GET'));
	$order  = get_var('order',Array('GET'));
	$qfield = get_var('qfield',Array('GET'));

	$ou = get_var('ou',Array('GET'));

	if($ou)
	{
		if($ou == 'Roaming')
		{
			$searchobj = 'ou='.$ou;
			$filterobj = 'nsLIProfileName=*,objectclass=*';
			$andor = 'OR';
			$GLOBALS['phpgw']->template->set_var('lang_user_accounts','nsLIProfileName=*');
		}
		else
		{
			$searchobj = 'ou='.$ou;
			$filterobj = 'cn=*,uid=*';
			$andor = 'OR';
			$GLOBALS['phpgw']->template->set_var('lang_user_accounts','ou='.$ou);
		}
	}
	elseif($nismapname || $nisMapName)
	{
		if($nismapname)
		{
			$nis = $nismapname;
		}
		elseif($nisMapName)
		{
			$nis = $nisMapName;
		}
		$searchobj = 'nismapname='.$nis;
		$filterobj = 'objectclass=*';
		$andor = 'OR';
		$GLOBALS['phpgw']->template->set_var('lang_user_accounts','nisMapName='.$nis);
	}

	$total = $ldapobj->count($searchobj.','.$ldapobj->base,$filterobj,$andor,$query);

	if (! $start)
	{
		$start = 0;
	}

	if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
		$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
	{
		$offset = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else
	{
		$offset = 15;
	}

	$GLOBALS['phpgw']->template->set_var('title','<a href="'.$GLOBALS['phpgw']->link('/eldaptir','server_id='.$server_id).'">'.lang('eldaptir')."</a>\n");
	$GLOBALS['phpgw']->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['bg_color']);
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);

	$GLOBALS['phpgw']->template->set_var('next_matchs', $GLOBALS['phpgw']->nextmatchs->show_tpl('/eldaptir/viewou.php',$start,$total,
		"&order=$order&filter=$filter&sort=$sort&query=$query&".$searchobj.'&server_id='.$server_id,'75%',
		$GLOBALS['phpgw_info']['theme']['th_bg'],0,0,1,0,0,''));

	$GLOBALS['phpgw']->template->set_var('lang_loginid',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'dn',$order,'/eldaptir/viewou.php',lang('dn'),'&'.$searchobj.'&server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('lang_lastname',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'sn',$order,'/eldaptir/viewou.php',lang('sn'),'&'.$searchobj.'&server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('lang_firstname',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'cn',$order,'/eldaptir/viewou.php',lang('cn').'/'.lang('givenname'),'&'.$searchobj.'&server_id='.$server_id));

	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('Edit'));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('Delete'));
	$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));

	if ($total> $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
	{
		if ($start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > $total)
		{
			$end = $total;
		}
		else
		{
			$end = $start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		$lang_showing=lang('showing %1 - %2 of %3',($start + 1),$end,$total);
	}
	else
	{
		$lang_showing=lang('showing %1',$total);
	}
	$GLOBALS['phpgw']->template->set_var('lang_showing',$lang_showing.' '.lang('in').' ');

	$info = $ldapobj->search($start,$offset,$searchobj.','.$ldapobj->base,$filterobj,'',$andor,$sort,$order,$query);

	if (!count($info))
	{
		$GLOBALS['phpgw']->template->set_var('message',lang('No matches found'));
		$GLOBALS['phpgw']->template->parse('rows','empty_row',True);
	}
	else
	{
		while (list($null,$entry) = each($info))
		{
			$dn = $entry['dn'];

			$GLOBALS['phpgw']->nextmatchs->template_alternate_row_color($GLOBALS['phpgw']->template);

			$GLOBALS['phpgw']->template->set_var('row_loginid',$dn);
			if ($entry['givenname'][0])
			{
				$GLOBALS['phpgw']->template->set_var('row_firstname',$entry['givenname'][0]);
			}
			elseif($entry['cn'][0])
			{
				$GLOBALS['phpgw']->template->set_var('row_firstname',$entry['cn'][0]);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('row_firstname','&nbsp');
			}
			if ($entry['sn'][0])
			{
				$GLOBALS['phpgw']->template->set_var('row_lastname',$entry['sn'][0]);
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('row_lastname','&nbsp');
			}

			$GLOBALS['phpgw']->template->set_var('row_edit','<a href="'.$GLOBALS['phpgw']->link('/eldaptir/edit.php','ou='.$ou.'&dn='
				. urlencode($dn).'&nisMapName='.$nisMapName .'&server_id='.$server_id) . '"> ' . lang('Edit') . ' </a>');

			if ($GLOBALS['phpgw_info']['user']['userid'] != $entry['uidnumber'])
			{
				$GLOBALS['phpgw']->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/eldaptir/delete.php','ou='.$ou.'&dn='
					. urlencode($dn)) . '&server_id='.$server_id . '"> '.lang('Delete').' </a>');
			}
			else
			{
				$GLOBALS['phpgw']->template->set_var('row_delete','&nbsp;');
			}

			$GLOBALS['phpgw']->template->set_var('row_view','<a href="' . $GLOBALS['phpgw']->link('/eldaptir/view.php','ou='.$ou.'&dn='
				. urlencode($dn).'&nisMapName='.$nisMapName .'&server_id='.$server_id) . '"> ' . lang('View') . ' </a>');

			$GLOBALS['phpgw']->template->parse('rows','row',True);
		}
	}		// End else

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/eldaptir/add.php','ou='.$ou.'&nisMapName='.$nisMapName.'&server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('cancel_url',$GLOBALS['phpgw']->link('/eldaptir/index.php','server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('accounts_url',$GLOBALS['phpgw']->link('/eldaptir/viewou.php','server_id='.$server_id));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('add'));
	$GLOBALS['phpgw']->template->set_var('lang_cancel',lang('cancel'));
	$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));

	$GLOBALS['phpgw']->template->pparse('out','list');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
