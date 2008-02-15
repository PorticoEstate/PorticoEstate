<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* (http://www.phpgroupware.org)                                            *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$phpgw_info['flags']['currentapp'] = 'inv';
	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('room_add' => 'room_form.tpl'));
	$t->set_block('room_add','add', 'addhandle');
	$t->set_block('room_add','edit','edithandle');

	if ($submit)
	{
		$errorcount = 0;

		if (!$room_name)
		{
			$error[$errorcount++] = lang('Please enter a name !');
		}

		$phpgw->db->query("select count(*) from phpgw_inv_stockrooms WHERE room_name='$room_name' AND room_owner='" .$phpgw_info['user']['account_id'] ."'");
		$phpgw->db->next_record();
		if ($phpgw->db->f(0) != 0)
		{
			$error[$errorcount++] = lang('That name has been used already !');
		}

		if (! $error)
		{
			$owner = $phpgw_info['user']['account_id'];
			$room_name = addslashes($room_name);
			$room_note = addslashes($room_note);

			if ($access)
			{
				$access = 'private';
			}
			else
			{
				$access = 'public';
			}

			$phpgw->db->query("INSERT into phpgw_inv_stockrooms (room_owner,room_access,room_name,room_note) "
							. "values ('$owner','$access','$room_name','$room_note')");
		}
	}

	if ($errorcount)
	{
		$t->set_var('message',$phpgw->common->error_list($error));
	}

	if (($submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message',lang('Stock room %1 has been added !',$room_name));
	}

	if ((! $submit) && (! $error) && (! $errorcount))
	{
		$t->set_var('message','');
	}

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	$t->set_var('actionurl',$phpgw->link('/inv/addroom.php'));
	$t->set_var('lang_action',lang('Add stock room'));
	$t->set_var('hidden_vars',$hidden_vars);
	$t->set_var('lang_room_name',lang('Name'));
	$t->set_var('room_name',$room_name);

	$t->set_var('lang_room_note',lang('Note'));
	$t->set_var('room_note',$room_note);

	$t->set_var('lang_access',lang('Private'));

	if ($access)
	{
		$t->set_var('access','<input type="checkbox" name="access" value="True" checked>');
	}
	else
	{
		$t->set_var('access','<input type="checkbox" name="access" value="True">');
	}

	$t->set_var('lang_add',lang('Add'));
	$t->set_var('lang_reset',lang('Clear Form'));
	$t->set_var('lang_done',lang('Done'));
	$t->set_var('done_action',$phpgw->link('/inv/rooms.php','subroom=True'));
	$t->set_var('edithandle','');
	$t->set_var('addhandle','');
	$t->pparse('out','room_add');
	$t->pparse('addhandle','add');
	$phpgw->common->phpgw_footer();
?>
