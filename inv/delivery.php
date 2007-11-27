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
	/* $Id: delivery.php 12919 2003-05-30 22:25:43Z gugux $ */

	$phpgw_info["flags"] = array('currentapp' => 'inv',
					'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('product_list_t' => 'del_listproducts.tpl'));
	$t->set_block('product_list_t','product_list','list');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

	if ($phpgw_info['server']['db_type']=='pgsql')
	{
		$join = " JOIN ";
	}
	else
	{
		$join = " LEFT JOIN ";
	}

	if($Delivery)
	{
		$errorcount = 0;

/*	if(!$delivery_id) {
	    $phpgw->db->query("SELECT max(id) FROM phpgw_inv_delivery");
	    $phpgw->db->next_record(); 
	    $delivery_id = $phpgw->db->f(0)+1; 
	} */

		if ($choose)
		{
			$delivery_num = create_deliveryid($year);
		}
		else
		{
			$delivery_num = addslashes($delivery_num);
		}

		if (!$delivery_num)
		{
			$error[$errorcount++] = lang('Please enter an ID !');
		}

		$phpgw->db->query("SELECT num FROM phpgw_inv_delivery WHERE num='$delivery_num'");
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
			$phpgw->db->query("INSERT INTO phpgw_inv_delivery (num,order_id,date) VALUES ('$delivery_num','$order_id','$date')");
			$phpgw->db->query("SELECT id from phpgw_inv_delivery WHERE num='$delivery_num'");
			$phpgw->db->next_record();
			$delivery_id = $phpgw->db->f('id');

			$db2 = $phpgw->db;
			$db2->query("SELECT phpgw_inv_products.con FROM phpgw_inv_products $join phpgw_inv_orderpos ON "                                                                           
					. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id'");                                                                                 
			while($db2->next_record())
			{
				$product_id = $db2->f('con');      
				$phpgw->db->query("INSERT INTO phpgw_inv_deliverypos (delivery_id,product_id) VALUES ('$delivery_id','$product_id')"); 
				$phpgw->db->query("UPDATE phpgw_inv_orderpos set dstatus='done' where product_id='$product_id' AND order_id='$order_id'");
			}
		}
	}

	if ($errorcount)
	{
		$t->set_var('message',$phpgw->common->error_list($error));
	}

	if (($Delivery) && (! $error) && (! $errorcount))
	{
		$t->set_var('message',lang('Delivery %1 has been created !',$delivery_num));
	}

	if ((! $Delivery) && (! $error) && (! $errorcount))
	{
		$t->set_var('message','');
	}

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n"
				. '<input type="hidden" name="delivery_id" value="' . $delivery_id . '">' . "\n"
				. '<input type="hidden" name="order_id" value="' . $order_id . '">' . "\n";

	$t->set_var('hidden_vars',$hidden_vars);

//------------ list header variable template-declarations----------------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('actionurl',$phpgw->link('/inv/delivery.php'));
	$t->set_var('lang_action',lang('Delivery'));
	$t->set_var('h_lang_id',lang('Product ID'));
	$t->set_var('h_lang_name',lang('Name'));
	$t->set_var('h_lang_piece',lang('Piece'));
	$t->set_var('h_lang_serial',lang('Serial number'));
	$t->set_var('h_lang_pos',lang('Position'));

	if (isset($phpgw_info['user']['preferences']['inv']['print_format']))
	{
		$t->set_var('error','');
		$t->set_var('lang_print_delivery',lang('Print delivery'));
		if ($phpgw_info['user']['preferences']['inv']['print_format']=='html'):
			$t->set_var('print_delivery',$phpgw->link('/inv/delivery_print.php','order_id=' . $order_id));
		elseif ($phpgw_info['user']['preferences']['inv']['print_format']=='pdf'):
			$t->set_var('print_delivery',$phpgw->link('/inv/delivery_pdf.php','order_id=' . $order_id));
		endif;
	}
	else
	{
		$t->set_var('error',lang('Please set your preferences for this application !'));
		$t->set_var('lang_print_delivery','');
	}

	$t->set_var('lang_list_delivery',lang('List deliveries'));
	$t->set_var('list_delivery',$phpgw->link('/inv/delivery_list.php','order_id=' . $order_id));

// -------------- end header declaration -----------------

	$t->set_var('title_descr',lang('Order description'));
	$t->set_var('title_customer',lang('Customer'));
	$t->set_var('title_delivery_num',lang('Delivery ID'));
	$t->set_var('lang_delivery_date',lang('Delivery date'));

	if (!$delivery_id)
	{
		$phpgw->db->query("SELECT descr,owner,phpgw_inv_orders.customer FROM phpgw_inv_orders WHERE phpgw_inv_orders.id='$order_id'");
	}
	else
	{
		$phpgw->db->query("SELECT phpgw_inv_orders.descr,owner,phpgw_inv_orders.customer,phpgw_inv_delivery.id,phpgw_inv_delivery.order_id "
						. "FROM phpgw_inv_orders,phpgw_inv_delivery "
						. "WHERE phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_delivery.order_id=phpgw_inv_orders.id");
	}

	$phpgw->db->next_record();
	$owner = $phpgw->db->f('owner');
	$descr = $phpgw->strip_html($phpgw->db->f('descr'));
	if (! $descr) { $descr  = '&nbsp;'; }
	$t->set_var('descr',$descr);

	$ab_id = $phpgw->db->f('customer');
	if (!$ab_id)
	{ 
		$t->set_var('customer',lang('You have no customer selected !'));
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
		$t->set_var('customer',$customerout);
	}

	$t->set_var('delivery_num',$delivery_num);

	if (!$Delivery)
	{
		$t->set_var('lang_choose',lang('Generate Delivery ID ?'));
		$t->set_var('choose','<input type="checkbox" name="choose" value="True">');
	}
	else
	{
		$t->set_var('choose','');
		$t->set_var('lang_choose','');
	}

	$pos = 0;
	if(!$delivery_id)
	{
		$date=0;
		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.piece FROM phpgw_inv_products $join phpgw_inv_orderpos ON "
						. "phpgw_inv_products.con=phpgw_inv_orderpos.product_id WHERE phpgw_inv_orderpos.order_id='$order_id' AND phpgw_inv_orderpos.dstatus='open'");
	}
	else
	{
		$phpgw->db->query("SELECT date FROM phpgw_inv_delivery WHERE id='$delivery_id'");
		$phpgw->db->next_record();
		$date=$phpgw->db->f('date');

		$phpgw->db->query("SELECT phpgw_inv_products.*,phpgw_inv_orderpos.order_id,phpgw_inv_orderpos.piece FROM phpgw_inv_products "
						. "$join phpgw_inv_orderpos ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id $join phpgw_inv_deliverypos ON "
						. "phpgw_inv_deliverypos.product_id=phpgw_inv_products.con $join phpgw_inv_delivery ON "
						. "phpgw_inv_delivery.order_id=phpgw_inv_orderpos.order_id "
						. "WHERE phpgw_inv_delivery.id='$delivery_id' AND phpgw_inv_deliverypos.delivery_id='$delivery_id'");
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

		$product_id = $phpgw->db->f('id');    
		$piece = $phpgw->db->f('piece');

		$serial = $phpgw->strip_html($phpgw->db->f('serial'));                                                                                                                                               
		if (! $serial)
		{
			$serial = '&nbsp;';
		}

		$product_name = $phpgw->strip_html($phpgw->db->f('name'));                                                                                                                                              
		if (! $product_name)
		{
			$product_name = '&nbsp;';
		}

// ---------------------- template declaration for list records ----------------

		$t->set_var(array('pos' => $pos,
					'product_id' => $product_id,
						'piece' => $piece,
				'product_name' => $product_name,
						'serial' => $serial));

		$t->parse('list','product_list',True);

// ------------------------ end record declaration ------------------------
	}

	if ($inventory->check_perms($grants[$owner],PHPGW_ACL_ADD) || $owner == $phpgw_info['user']['account_id'])
	{
		$t->set_var('create','<input type="submit" name="Delivery" value="' . lang('Create delivery') .'">');
	}
	else
	{
		$t->set_var('create','');
	}

	$t->parse('out','product_list_t',True);
	$t->p('out');

	$phpgw->common->phpgw_footer();
?>
