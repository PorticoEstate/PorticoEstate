<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* (http://www.phpgroupware.org)                                            *
	* Written by Bettina Gille  [ceb@phpgroupware.org]                         *
	* ------------------------------------------------                         *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	if (!$id)
	{
		Header('Location: ' . $phpgw->link('/inv/listorders.php','sort=' . $sort . '&order=' . $order . '&query=' . $query
										. '&start=' . $start . '&filter=' . $filter));
	}

	$phpgw_info['flags']['currentapp'] = 'inv';

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('order_edit' => 'order_form.tpl'));
	$t->set_block('order_edit','add','addhandle');
	$t->set_block('order_edit','edit','edithandle');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="id" value="' . $id . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	if ($submit)
	{
		$num = addslashes($num);
		$errorcount = 0;
		if (!$num)
		{
			$error[$errorcount++] = lang('Please enter an ID !');
		}

		$phpgw->db->query("select count(*) from phpgw_inv_orders WHERE num='$num' AND id != '$id'");
		$phpgw->db->next_record();
		if ($phpgw->db->f(0) != 0)
		{
			$error[$errorcount++] = lang('That ID has been used already !');
		}

		if (checkdate($month,$day,$year))
		{
			$date = mktime(2,0,0,$month,$day,$year);
		}
		else
		{
			if ($month && $day && $year)
			{
				$error[$errorcount++] = lang('You have entered an invalid date !') . '<br>' . $month . '/' . $day . '/' . $year;
			}
		}

		if (! $error)
		{
			$descr = addslashes($descr);
			if ($access)
			{
				$access = 'private';
			}
			else
			{
				$access = 'public';
			}

			$phpgw->db->query("UPDATE phpgw_inv_orders set num='$num',customer='$abid',descr='$descr',date='$date',status='$status',"
							. "access='$access' WHERE id='$id'");
		}
	}

	if ($errorcount)
	{
		$t->set_var('message',$phpgw->common->error_list($error));
	}

	if (($submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message',lang('Order %1 has been updated !',$num));
	}

	if ((! $submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message','');
	}

	$t->set_var('addressbook_link',$phpgw->link('/inv/addressbook.php','query='));
	$t->set_var('actionurl',$phpgw->link('/inv/editorder.php','id=' . $id));
	$t->set_var('done_action',$phpgw->link('/inv/listorders.php'));
	$t->set_var('lang_action',lang('Edit order'));
	$t->set_var('lang_select',lang('Select per button !'));
	$t->set_var('lang_done',lang('Done'));
	$t->set_var('hidden_vars',$hidden_vars);
	$t->set_var('lang_num',lang('Order ID'));
	$t->set_var('lang_choose','');
	$t->set_var('choose','');

	$phpgw->db->query("SELECT * from phpgw_inv_orders WHERE id='$id'");
	$phpgw->db->next_record();

	$owner = $phpgw->db->f('owner');
	$t->set_var('num', $phpgw->strip_html($phpgw->db->f('num')));
	$t->set_var('descr', $phpgw->strip_html($phpgw->db->f('descr')));

// customer 
	$t->set_var('lang_customer',lang('Customer'));

	$d = CreateObject('phpgwapi.contacts');
	$cols = array('n_given' => 'n_given',
				'n_family' => 'n_family',
				'org_name' => 'org_name');

	$customer = $d->read_single_entry($phpgw->db->f('customer'),$cols);

	if ($customer[0]['org_name'] == '')
	{
		$t->set_var('name',$customer[0]['n_given'] . ' ' . $customer[0]['n_family']);
	}
	else
	{
		$t->set_var('name',$customer[0]['org_name'] . ' [ ' . $customer[0]['n_given'] . ' ' . $customer[0]['n_family'] . ' ]');
	}

	$t->set_var('lang_descr',lang('Description'));
	$t->set_var('lang_date',lang('Date'));

	$sm = CreateObject('phpgwapi.sbox');

	$date = $phpgw->db->f('date');
	if ($date != 0)
	{
		$month = date('m',$date);
		$day = date('d',$date);
		$year = date('Y',$date);
	}
	else
	{
		$month = date('m',time());
		$day = date('d',time());
		$year = date('Y',time());
	}

	$t->set_var('date_select',$phpgw->common->dateformatorder($sm->getYears('year',$year),$sm->getMonthText('month',$month),$sm->getDays('day',$day)));

	$t->set_var('lang_status',lang('Status'));
	if ($phpgw->db->f('status')=='open'):
		$stat_sel[0]=' selected';
	elseif ($phpgw->db->f('status')=='closed'):
		$stat_sel[1]=' selected';
	elseif ($phpgw->db->f('status')=='archive'):
		$stat_sel[2]=' selected';
	endif;

	$status_list = '<option value="active"' . $stat_sel[0] . '>' . lang('Open') . '</option>' . "\n"
				. '<option value="nonactive"' . $stat_sel[1] . '>' . lang('Closed') . '</option>' . "\n"
				. '<option value="archive"' . $stat_sel[2] . '>' . lang('Archive') . '</option>' . "\n";

	$t->set_var('status_list',$status_list);

	$t->set_var('lang_access',lang('Private'));
	if ($phpgw->db->f('access')=='private')
	{
		$t->set_var('access', '<input type="checkbox" name="access" value="True" checked>');
	}
	else
	{
		$t->set_var('access', '<input type="checkbox" name="access" value="True">');
	}

	$t->set_var('lang_edit',lang('Edit'));

	if ($inventory->check_perms($grants[$owner],PHPGW_ACL_DELETE) || $owner == $phpgw_info['user']['account_id'])
	{
		$t->set_var('delete','<form method="POST" action="' . $phpgw->link('/inv/deleteorder.php','id=' . $id . '&start=' . $start . '&query=' . $query
							. '&sort=' . $sort . '&order=' . $order . '&filter=' . $filter) . '"><input type="submit" value="' . lang('Delete') .'"></form>');          
	}
	else
	{
		$t->set_var('delete','');
	}

	$t->set_var('edithandle','');
	$t->set_var('addhandle','');
	$t->pparse('out','order_edit');
	$t->pparse('edithandle','edit');

	$phpgw->common->phpgw_footer();
?>
