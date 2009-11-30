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
	$t->set_file(array('invoice_print_t' => 'invoice_print.tpl',
						'invoice_print' => 'invoice_print.tpl'));
	$t->set_block('invoice_print_t','invoice_print','print');

	if ($phpgw_info['server']['db_type']=='pgsql') { $join = " JOIN "; }
	else { $join = " LEFT JOIN "; }

	$d = CreateObject('phpgwapi.contacts');

	if (isset($phpgw_info['user']['preferences']['inv']['abid']) && (isset($phpgw_info['user']['preferences']['common']['currency']) && (isset($phpgw_info['user']['preferences']['common']['country']))))
	{
		$currency = $phpgw_info['user']['preferences']['common']['currency'];
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
	$t->set_var('lang_invoice',lang('Invoice ID'));
	$t->set_var('lang_order_descr',lang('Order'));
	$t->set_var('lang_pos',lang('Position'));
	$t->set_var('lang_date',lang('Date'));
	$t->set_var('lang_order_descr',lang('Order description'));
	$t->set_var('lang_piece',lang('Piece'));
	$t->set_var('lang_price',lang('a piece'));
	$t->set_var('lang_sum_net',lang('Sum net'));
	$t->set_var('lang_tax',lang('tax'));
	$t->set_var('lang_procent',lang('%'));
	$t->set_var('lang_product_id',lang('Product ID'));
	$t->set_var('lang_product_name',lang('Name'));
	$t->set_var('lang_serial',lang('Serial number'));
	$t->set_var('lang_sum',lang('Sum'));
	$t->set_var('currency',$currency);

	if (!$invoice_id)
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_invoice.num,phpgw_inv_invoice.order_id,"
						. "phpgw_inv_invoice.date FROM phpgw_inv_invoice,phpgw_inv_orders WHERE "
						. "phpgw_inv_invoice.order_id='$order_id' AND phpgw_inv_orders.id='$order_id'");
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.customer,phpgw_inv_orders.descr,phpgw_inv_invoice.id,phpgw_inv_invoice.num,"
						. "phpgw_inv_invoice.order_id,phpgw_inv_invoice.date FROM phpgw_inv_invoice,phpgw_inv_orders WHERE "
						. "phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoice.order_id=phpgw_inv_orders.id");
	}

	if ($phpgw->db->next_record())
	{
		$custadr = $phpgw->db->f('customer');

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
		$dateout = $phpgw->common->show_date($date,$phpgw_info['user']['preferences']['common']['dateformat']);
		$t->set_var('delivery_date',$dateout);

		$t->set_var('invoice_num',$phpgw->strip_html($phpgw->db->f('num')));
		$order_descr = $phpgw->strip_html($phpgw->db->f('descr'));
		if (!$order_descr) $order_descr = '&nbsp;';
		$t->set_var('order_descr',$order_descr);
	}

	$pos = 0;
	$sum_netto = 0;
	$sum_retail = 0;
	$sum_piece = 0;

	if (!$invoice_id)
	{
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax FROM phpgw_inv_products $join phpgw_inv_orderpos "
						. "ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id'");
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax "
						. "FROM phpgw_inv_products $join phpgw_inv_orderpos ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id"
						. "$join phpgw_inv_invoicepos ON phpgw_inv_invoicepos.product_id=phpgw_inv_products.con $join phpgw_inv_invoice ON "
						. "phpgw_inv_invoice.order_id=phpgw_inv_orderpos.order_id "
						. "WHERE phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoicepos.invoice_id='$invoice_id'");
	}

	while ($phpgw->db->next_record())
	{
		$pos++;
		$t->set_var('pos',$pos);
		$t->set_var('piece',$phpgw->db->f('piece'));
		$t->set_var('id',$phpgw->db->f('id'));
		$t->set_var('price',$phpgw->db->f('price'));
		$sum_piece = (float)($phpgw->db->f('price'))*($phpgw->db->f('piece'));
		$t->set_var('sum_piece',sprintf("%1.2f",$sum_piece));
		$t->set_var('tax',$phpgw->db->f('tax'));
		$retail = $phpgw->db->f('retail');
		$sum_retail = (float)($retail*($phpgw->db->f('piece')));
		$t->set_var('sum_retail',sprintf("%1.2f",$sum_retail));

		$tax_percent = ($sum_retail - $sum_piece);

		$name = $phpgw->strip_html($phpgw->db->f('name'));
		if (!$name) $name = '&nbsp;';
		$t->set_var('name',$name);

		$serial = $phpgw->strip_html($phpgw->db->f('serial'));
		if (!$serial) $serial = '&nbsp;';
		$t->set_var('serial',$serial);

		$sum_price += ((float)$sum_piece);
		$sum_sum += ((float)$sum_retail);
		$sum_tax += ((float)$tax_percent);
//		$t->set_var('sum_price',sprintf("%01.2f",$sum_price));
//		$t->set_var('sum_sum',sprintf("%01.2f",$sum_sum));

		$t->parse('print','invoice_print',True);
	}

	$t->set_var('sum_price',sprintf("%01.2f",$sum_price));
	$t->set_var('sum_sum',sprintf("%01.2f",$sum_sum));
	$t->set_var('sum_tax',sprintf("%01.2f",$sum_tax));

	$t->parse('out','invoice_print_t',True);
	$t->p('out');
?>
