<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: deleteroom.php 5564 2001-06-07 02:17:59Z bettina $ */

	if ($confirm)
	{
		$phpgw_info['flags'] = array('noheader' => True,
									'nonavbar' => True);
	}

	$phpgw_info['flags']['currentapp'] = 'inv';
	include('../header.inc.php');

	if (! $id)
	{
		Header('Location: ' . $phpgw->link('/inv/rooms.php','subroom=True'));
	}

	if ($confirm)
	{
		$phpgw->db->query("delete from phpgw_inv_stockrooms where id='$id'");
		Header('Location: ' . $phpgw->link('/inv/rooms.php','subroom=True'));
	}
	else
	{
		$hidden_vars = '<input type="hidden" name="id" value="' . $id . '">' . "\n";

		$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
		$t->set_file(array('room_delete' => 'delete.tpl'));

		$t->set_var('deleteheader',lang('Are you sure you want to delete this stock room ?'));
		$t->set_var('lang_subs','');
		$t->set_var('subs', '');
		$t->set_var('nolink',$HTTP_REFERER);
		$t->set_var('lang_no',lang('No'));
		$t->set_var('hidden_vars',$hidden_vars);
		$t->set_var('action_url',$phpgw->link('/inv/deleteroom.php','id=' . $id));
		$t->set_var('lang_yes',lang('Yes'));

		$t->pparse('out','room_delete');
	}

	$phpgw->common->phpgw_footer();
?>
