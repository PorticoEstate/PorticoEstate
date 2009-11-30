<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* (http://www.phpgroupware.org)                                            *
	* Written by Bettina Gille  [ceb@phpgroupware.org]                         *
	* --------------------------------------------------------                 *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$phpgw_info['flags'] = array('currentapp' => 'inv',
									'noheader' => True,
									'nonavbar' => True);

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('delivery_print_t' => 'delivery_print.tpl',
						'delivery_print' => 'delivery_print.tpl'));

    $t->set_block('delivery_print_t','delivery_print','print');

	if ($phpgw_info['server']['db_type']=='pgsql') { $join = " JOIN "; }
	else { $join = " LEFT JOIN "; }

	$d = CreateObject('phpgwapi.contacts');

	if (isset($phpgw_info['user']['preferences']['inv']['abid']) && isset($phpgw_info['user']['preferences']['common']['country']))
	{
		$t->set_var('error','');
		$id = $phpgw_info['user']['preferences']['inv']['abid'];

		$t->set_var('myaddress',$d->formatted_address($id,True));	
	}
	else
	{
		$t->set_var('error',lang('Please set your preferences for this application !'));
		$t->set_var('myaddress','');
	}

	$t->set_var('site_title',$phpgw_info['site_descr']);
	$charset = $phpgw->translation->translate('charset');
	$t->set_var('charset',$charset);
    $t->set_var('font',$phpgw_info['theme']['font']);
	$t->set_var('lang_delivery',lang('Delivery ID'));
	$t->set_var('lang_order_descr',lang('Order'));
	$t->set_var('lang_pos',lang('Position'));
    $t->set_var('lang_date',lang('Delivery date'));
    $t->set_var('lang_order_descr',lang('Order description'));
    $t->set_var('lang_piece',lang('Piece'));
    $t->set_var('lang_product_id',lang('Product ID'));
    $t->set_var('lang_product_name',lang('Name'));
    $t->set_var('lang_serial',lang('Serial number'));

	if (!$delivery_id)
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_delivery.num,phpgw_inv_delivery.order_id, "
						. "phpgw_inv_delivery.date FROM phpgw_inv_delivery,phpgw_inv_orders WHERE "
						. "phpgw_inv_delivery.order_id='$order_id' AND phpgw_inv_orders.id='$order_id'");
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_delivery.id,phpgw_inv_delivery.num, "
						. "phpgw_inv_delivery.order_id,phpgw_inv_delivery.date FROM phpgw_inv_delivery,phpgw_inv_orders WHERE "
						. "phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_delivery.order_id=phpgw_inv_orders.id");
	}

	if ($phpgw->db->next_record())
	{
		$custadr= $phpgw->db->f('customer');

		if (isset($phpgw_info['user']['preferences']['common']['country']))
		{
			$t->set_var('customer',$d->formatted_address($custadr,True));
		}
		else
		{
			$t->set_var('error',lang('Please set your preferences for this application !'));
			$t->set_var('customer','');
		}

		$date = $phpgw->db->f('date');
		$month = $phpgw->common->show_date(time(),'n');
		$day = $phpgw->common->show_date(time(),'d');
		$year = $phpgw->common->show_date(time(),'Y');

		$date = $date + (60*60) * $phpgw_info['user']['preferences']['common']['tz_offset'];
		$dateout =  $phpgw->common->show_date($date,$phpgw_info['user']['preferences']['common']['dateformat']);
		$t->set_var('delivery_date',$dateout);

		$t->set_var('delivery_num',$phpgw->strip_html($phpgw->db->f('num')));
		$order_descr = $phpgw->strip_html($phpgw->db->f('descr'));
		if (!$order_descr) $order_descr = '&nbsp;';
		$t->set_var('order_descr',$order_descr);
	}

	$pos = 0;

	if (!$delivery_id)
	{
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece FROM phpgw_inv_products $join phpgw_inv_orderpos "
						. "ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id'");                                                                                         
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece FROM phpgw_inv_products "
						. "$join phpgw_inv_orderpos ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id $join phpgw_inv_deliverypos ON "
						. "phpgw_inv_deliverypos.product_id=phpgw_inv_products.con $join phpgw_inv_delivery ON "
						. "phpgw_inv_delivery.order_id=phpgw_inv_orderpos.order_id "
						. "WHERE phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_deliverypos.delivery_id='$delivery_id'");
	}
	while ($phpgw->db->next_record())
	{
		$pos++;
		$t->set_var('pos',$pos);
		$t->set_var('piece',$phpgw->db->f('piece'));
		$t->set_var('id',$phpgw->db->f('id'));
		$name = $phpgw->strip_html($phpgw->db->f('name'));
		if (!$name) $name = '&nbsp;';
		$t->set_var('name',$name);
		$serial = $phpgw->strip_html($phpgw->db->f('serial'));
		if (!$serial) $serial = '&nbsp;';
		$t->set_var('serial',$serial);
		$t->parse('print','delivery_print',True);
	}

	$t->parse('out','delivery_print_t',True);
	$t->p('out');
?>
