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
					'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('product_list_t' => 'inv_listproducts.tpl'));
	$t->set_block('product_list_t','product_list','list');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

	if (isset($phpgw_info['user']['preferences']['common']['currency']))
	{
		$currency = $phpgw_info['user']['preferences']['common']['currency'];
		$t->set_var('error','');
	}
	else
	{
		$t->set_var('error',lang('Please set your preferences for this application !'));
	}

	if ($phpgw_info['server']['db_type'] == 'pgsql')
	{
		$join = " JOIN ";
	}
	else
	{
		$join = " LEFT JOIN ";
	}

	if($Invoice)
	{
		$errorcount = 0;
/*	if(!$invoice_id) {
            $phpgw->db->query("SELECT max(id) FROM phpgw_inv_invoice");
            if ($phpgw->db->next_record()) { $invoice_id = $phpgw->db->f(0)+1; }
	    else { $invoice_id = 1; }
	} */

		if ($choose)
		{
			$invoice_num = create_invoiceid($year);
		}
		else
		{
			$invoice_num = addslashes($invoice_num);
		}

		if (!$invoice_num)
		{
			$error[$errorcount++] = lang('Please enter an ID !');
		}
		$phpgw->db->query("SELECT num FROM phpgw_inv_invoice WHERE num='$invoice_num'");
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
				$error[$errorcount++] = lang('You have entered an invalid date !') . '<br> ' . $month . '/' . $day . '/' . $year;
			}
		}

		if (! $error)
		{
			$phpgw->db->query("INSERT INTO phpgw_inv_invoice (num,sum,order_id,date) VALUES ('$invoice_num',0,'$order_id','$date')");
			$phpgw->db->query("SELECT id from phpgw_inv_invoice WHERE num='$invoice_num'");
			$phpgw->db->next_record();
			$invoice_id = $phpgw->db->f('id');

			$db2 = $phpgw->db;

			$sql = "SELECT phpgw_inv_products.con FROM phpgw_inv_products $join phpgw_inv_orderpos ON "                                      
				. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id'";

			$db2->query($sql,__LINE__,__FILE__);

			while($db2->next_record())
			{
				$product_id = $db2->f('con');
				$phpgw->db->query("INSERT INTO phpgw_inv_invoicepos (invoice_id,product_id) VALUES ('$invoice_id','$product_id')");
				$phpgw->db->query("UPDATE phpgw_inv_orderpos set istatus='sold' where product_id='$product_id' AND order_id='$order_id'");
			}

			$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece from phpgw_inv_products $join phpgw_inv_orderpos ON "                                                                   
							. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id $join phpgw_inv_invoicepos ON "                                                                                                        
							. "phpgw_inv_invoicepos.product_id=phpgw_inv_products.con $join phpgw_inv_invoice ON phpgw_inv_invoice.order_id=phpgw_inv_orderpos.order_id "                                                               
							. "WHERE phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoicepos.invoice_id='$invoice_id'");                                                                                           
			while ($phpgw->db->next_record())
			{
				$sum_price = (($phpgw->db->f('price'))*($phpgw->db->f('piece')));
				$sum += ((float)$sum_price);
			}
			$phpgw->db->query("UPDATE phpgw_inv_invoice SET sum='$sum' WHERE id='$invoice_id'");
		}
	}

	if ($errorcount)
	{
		$t->set_var('message',$phpgw->common->error_list($error));
	}
	if (($Invoice) && (! $error) && (! $errorcount))
	{
		$t->set_var('message',lang('Invoice %1 has been created !',$invoice_num));
	}
	if ((! $Invoice) && (! $error) && (! $errorcount))
	{
		$t->set_var('message','');
	}

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n"
				. '<input type="hidden" name="invoice_id" value="' . $invoice_id . '">' . "\n"
				. '<input type="hidden" name="order_id" value="' . $order_id . '">' . "\n";

	$t->set_var('hidden_vars',$hidden_vars);

//------------ list header variable template-declarations----------------------

	$t->set_var('lang_action',lang('Invoice'));
	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('currency',$currency);
	$t->set_var('lang_procent',lang('%'));
	$t->set_var('lang_id',lang('Product ID'));
	$t->set_var('lang_name',lang('Name'));
	$t->set_var('lang_piece',lang('Piece'));
	$t->set_var('lang_serial',lang('Serial number'));
	$t->set_var('lang_sum_net',lang('Sum net'));
	$t->set_var('lang_tax',lang('tax'));
	$t->set_var('lang_pos',lang('Position'));
	$t->set_var('lang_price',lang('a piece'));
	$t->set_var('lang_sum',lang('Sum'));
	$t->set_var('lang_createinvoice',lang('Create invoice'));
	$t->set_var('actionurl',$phpgw->link('/inv/invoice.php'));

	if (isset($phpgw_info['user']['preferences']['inv']['print_format']))
	{
		$t->set_var('error','');
		$t->set_var('lang_print_invoice',lang('Print invoice'));

		if ($phpgw_info['user']['preferences']['inv']['print_format']=='html'):
			$t->set_var('print_invoice',$phpgw->link('/inv/invoice_print.php','order_id=' . $order_id));  
		elseif ($phpgw_info['user']['preferences']['inv']['print_format']=='pdf'):
			$t->set_var('print_invoice',$phpgw->link('/inv/invoice_pdf.php','order_id=' . $order_id));
		endif;
	}
	else
	{
		$t->set_var('error',lang('Please set your preferences for this application'));
		$t->set_var('print_invoice','');
		$t->set_var('lang_print_invoice','');
	}

	$t->set_var('lang_list_invoice',lang('List invoices'));
	$t->set_var('list_invoice',$phpgw->link('/inv/invoice_list.php','order_id=' . $order_id));

// -------------- end header declaration -----------------

	$t->set_var('title_descr',lang('Order description'));
	$t->set_var('title_customer',lang('Customer'));
	$t->set_var('title_invoice_num',lang('Invoice ID'));
	$t->set_var('lang_invoice_date',lang('Invoice date'));

	if (!$invoice_id)
	{
		$phpgw->db->query("SELECT descr,customer,owner FROM phpgw_inv_orders WHERE phpgw_inv_orders.id='$order_id'");
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.descr,phpgw_inv_orders.customer,phpgw_inv_invoice.id,phpgw_inv_invoice.order_id "
						. "FROM phpgw_inv_orders,phpgw_inv_invoice "
						. "WHERE phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoice.order_id=phpgw_inv_orders.id");
	}

	$phpgw->db->next_record();
	$owner = $phpgw->db->f('owner');
	$t->set_var('descr',$phpgw->db->f('descr'));

	$ab_id = $phpgw->db->f('customer');
	if (!$ab_id)
	{
		$customerout = lang('You have no customer selected !');
	}
	else
	{
		$cols = array('n_given' => 'n_given',
					'n_family' => 'n_family',
					'org_name' => 'org_name');

		$d = CreateObject('phpgwapi.contacts');
		$entry = $d->read_single_entry($ab_id,$cols);
		if ($entry[0]['org_name'] = '')
		{
			$customerout = $entry[0]['n_given'] . ' ' . $entry[0]['n_family'];
		}
		else
		{
			$customerout = $entry[0]['org_name'] . ' [ ' . $entry[0]['n_given'] . ' ' . $entry[0]['n_family'] . ' ]';
		}
	}
	$t->set_var('customer',$customerout);

	$t->set_var('invoice_num',$invoice_num);

	if (!$Invoice)
	{
		$t->set_var('lang_choose',lang('Generate Invoice ID ?'));
		$t->set_var('choose','<input type="checkbox" name="choose" value="True">');
	}
	else
	{
		$t->set_var('lang_choose','');
		$t->set_var('choose','');
	}

	$sum_price = 0;
	$sum_piece = 0;
	$sum_retail = 0;
	$pos = 0;

	if(!$invoice_id)
	{
		$date=0;
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax FROM phpgw_inv_products $join phpgw_inv_orderpos ON "
						. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id' AND phpgw_inv_orderpos.istatus='open'");
	}
	else
	{
		$phpgw->db->query("SELECT date FROM phpgw_inv_invoice WHERE id='$invoice_id'");
		$phpgw->db->next_record();
		$date = $phpgw->db->f('date');
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax "
						. "FROM phpgw_inv_products $join phpgw_inv_orderpos ON "
						. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id $join phpgw_inv_invoicepos ON "
						. "phpgw_inv_invoicepos.product_id=phpgw_inv_products.con $join phpgw_inv_invoice ON "
						. "phpgw_inv_invoice.order_id=phpgw_inv_orderpos.order_id "
						. "WHERE phpgw_inv_invoice.id='$invoice_id' AND phpgw_inv_invoicepos.invoice_id='$invoice_id'");
	}

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

	$sm = CreateObject('phpgwapi.sbox');
	$t->set_var('date_select',$phpgw->common->dateformatorder($sm->getYears('year',$year),$sm->getMonthText('month',$month),$sm->getDays('day',$day)));

	while ($phpgw->db->next_record())
	{
		$pos++;
		$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
		$t->set_var('tr_color',$tr_color);    
		$t->set_var('pos',$pos);

		$id = $phpgw->db->f('id'); 
		$tax = $phpgw->db->f('tax');   
		$taxpercent = ($tax/100);

		$price = $phpgw->db->f('price');
		$piece = $phpgw->db->f('piece');
		$sum_piece = ($price*$piece);
		$retail = round(($phpgw->db->f('price'))*(1+$taxpercent),2);
//    $retail = $phpgw->db->f('retail');
		$sum_retail = ($retail*($phpgw->db->f('piece')));

		$tax_percent = ($sum_retail - $sum_piece);

		$serial = $phpgw->strip_html($phpgw->db->f('serial'));                                                                                                                                               
		if (! $serial) { $serial = '&nbsp;'; }                                                                                                                                                            

		$name = $phpgw->strip_html($phpgw->db->f('name'));                                                                                                                                              
		if (! $name) { $name = '&nbsp;'; }

		$sum_price += ((float)$sum_piece);
		$sum_tax += ((float)$tax_percent);
		$sum_sum += ((float)$sum_retail);

// ---------------------- template declaration for list records ----------------

		$t->set_var(array('pos' => $pos,
							'id' => $id,
							'tax' => $tax,
						'piece' => $piece,
					'sum_piece' => sprintf("%1.2f",$sum_piece),
						'price' => sprintf("%1.2f",$price),
					'sum_retail' => sprintf("%1.2f",$sum_retail),
						'name' => $name,
					'serial' => $serial));

		$t->parse('list','product_list',True);

// ------------------------ end record declaration ------------------------
	}

	$t->set_var('sum_price',sprintf("%01.2f",$sum_price));
	$t->set_var('sum_tax',sprintf("%01.2f",$sum_tax));
	$t->set_var('sum_sum',sprintf("%01.2f",$sum_sum));

	if ($inventory->check_perms($grants[$owner],PHPGW_ACL_ADD) || $owner == $phpgw_info['user']['account_id'])
	{
		$t->set_var('create','<input type="submit" name="Invoice" value="' . lang('Create invoice') .'">');
	}
	else { $t->set_var('create',''); }

	$t->parse('out','product_list_t',True);
	$t->p('out');
	$phpgw->common->phpgw_footer();
?>
