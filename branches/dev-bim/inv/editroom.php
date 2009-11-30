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
		Header('Location: ' . $phpgw->link('/inv/rooms.php','sort=' . $sort . '&order=' . $order . '&query=' . $query
										. '&start=' . $start . '&filter=' . $filter . '&subroom=True'));
	}

	$phpgw_info['flags']['currentapp'] = 'inv';

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('room_edit' => 'room_form.tpl'));
	$t->set_block('room_edit','add','addhandle');
	$t->set_block('room_edit','edit','edithandle');

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
		$room_name = addslashes($room_name);
		$errorcount = 0;
		if (!$room_name)
		{
			$error[$errorcount++] = lang('Please enter a name !');
		}

		$phpgw->db->query("select count(*) from phpgw_inv_stockrooms WHERE room_name='$room_name' AND id != '$id' AND room_owner='"
						. $phpgw_info['user']['account_id'] . "'");
		$phpgw->db->next_record();
		if ($phpgw->db->f(0) != 0)
		{
			$error[$errorcount++] = lang('That name has been used already !');
		}

		if (! $error)
		{
			$room_note = addslashes($room_note);

			if ($access)
			{
				$access = 'private';
			}
			else
			{
				$access = 'public';
			}

			$phpgw->db->query("UPDATE phpgw_inv_stockrooms set room_name='$room_name',room_note='$room_note',"
							. "room_access='$access' WHERE id='$id'");
		}
	}

	if ($errorcount)
	{
		$t->set_var('message',$phpgw->common->error_list($error));
	}

	if (($submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message',lang('Stock room %1 has been updated !',$room_name));
	}

	if ((! $submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message','');
	}

	$t->set_var('actionurl',$phpgw->link('/inv/editroom.php','id=' . $id));
	$t->set_var('done_action',$phpgw->link('/inv/rooms.php','subroom=True'));
	$t->set_var('lang_action',lang('Edit stock room'));
	$t->set_var('lang_done',lang('Done'));
	$t->set_var('hidden_vars',$hidden_vars);
	$t->set_var('lang_room_name',lang('Name'));

	$phpgw->db->query("SELECT * from phpgw_inv_stockrooms WHERE id='$id'");
	$phpgw->db->next_record();

	$owner = $phpgw->db->f('room_owner');
	$t->set_var('room_name',$phpgw->strip_html($phpgw->db->f('room_name')));
	$t->set_var('room_note',$phpgw->strip_html($phpgw->db->f('room_note')));

	$t->set_var('lang_room_note',lang('note'));
	$t->set_var('lang_access',lang('Private'));

	if ($phpgw->db->f('room_access')=='private')
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
		$t->set_var('delete','<form method="POST" action="' . $phpgw->link('/inv/deleteroom.php','id=' . $id . '&start=' . $start . '&query=' . $query
							. '&sort=' . $sort . '&order=' . $order . '&filter=' . $filter . '&subroom=True') . '"><input type="submit" value="' . lang('Delete') .'"></form>');          
	}
	else
	{
		$t->set_var('delete','');
	}

	$t->set_var('edithandle','');
	$t->set_var('addhandle','');
	$t->pparse('out','room_edit');
	$t->pparse('edithandle','edit');

	$phpgw->common->phpgw_footer();
?>
