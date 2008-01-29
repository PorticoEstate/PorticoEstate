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
	/* $Id: deleteorder.php 4985 2001-05-22 01:55:01Z bettina $ */

	if ($confirm)
	{
		$phpgw_info['flags'] = array('noheader' => True,
									'nonavbar' => True);
	}

	$phpgw_info['flags']['currentapp'] = 'inv';
	include('../header.inc.php');

	if (! $id)
	{
		Header('Location: ' . $phpgw->link('/inv/listorders.php'));
	}

	if ($confirm)
	{
		$phpgw->db->query("delete from phpgw_inv_orders where id='$id'");
		$phpgw->db->query("delete from phpgw_inv_orderpos where order_id='$id'");
		$phpgw->db->query("delete from phpgw_inv_delivery where order_id='$id'");
		$phpgw->db->query("delete from phpgw_inv_invoice where order_id='$id'");
		Header('Location: ' . $phpgw->link('/inv/listorders.php'));
	}
	else
	{
		$hidden_vars = '<input type="hidden" name="id" value="' . $id . '">' . "\n";

		$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
		$t->set_file(array('order_delete' => 'delete.tpl'));

		$t->set_var('deleteheader',lang('Are you sure you want to delete this order ?'));
		$t->set_var('lang_subs','');
		$t->set_var('subs', '');
		$t->set_var('nolink',$phpgw->link('/inv/listorders.php','id=' . $id));
		$t->set_var('lang_no',lang('No'));
		$t->set_var('hidden_vars',$hidden_vars);
		$t->set_var('action_url',$phpgw->link('/inv/deleteorder.php','id=' . $id));
		$t->set_var('lang_yes',lang('Yes'));

		$t->pparse('out','order_delete');
	}

	$phpgw->common->phpgw_footer();
?>
