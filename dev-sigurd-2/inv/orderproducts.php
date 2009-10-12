<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */

	$phpgw_info['flags'] = array('currentapp' => 'inv',
					'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('product_list_t' => 'orderproducts.tpl'));
	$t->set_block('product_list_t','product_list','list');

	$c = CreateObject('phpgwapi.categories');

	$hidden_vars = '<input type="hidden" name="order_id" value="' . $order_id . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	$t->set_var('hidden_vars',$hidden_vars);

	if ($phpgw_info['server']['db_type']=='pgsql') { $join = " JOIN "; }
	else { $join = " LEFT JOIN "; }

	$phpgw->db->query("SELECT owner from phpgw_inv_orders WHERE id='$order_id'");
	$phpgw->db->next_record();
	$owner = $phpgw->db->f('owner');

	if ($Order)
	{
		$errorcount = 0; 

		$db2 = $phpgw->db;
		$db2->query("select phpgw_categories.cat_data from phpgw_categories where cat_id = '$filter'");
		$db2->next_record();
		$data = unserialize($db2->f('cat_data'));
		$tax = $data['tax'];

		while($choose && $entry=each($choose))
		{
			$stock = $inventory->check_stock($entry[0],$piece[$entry[0]]);
			if ($stock == True)
			{
				$error[$errorcount++] = lang('Product %1 has only %2 pieces in stock !',$sentry[0],$piece[$entry[0]]);
			}
			else
			{
				$phpgw->db->query("INSERT INTO phpgw_inv_orderpos(order_id,product_id,piece,tax) VALUES('$order_id','$entry[0]','" . $piece[$entry[0]] . "','$tax')");
				$inventory->update_stock('delete',$entry[0],$piece[$entry[0]]);
			}
		}
	}

	if ($Delete)
	{
		while($choose && $entry=each($choose))
		{
			$inventory->update_stock('add',$entry[0],$piece[$entry[0]]);
			$phpgw->db->query("DELETE from phpgw_inv_orderpos WHERE order_id='$order_id' AND product_id='$entry[0]'");
		}
	}

	if ($errorcount) { $t->set_var('message',$phpgw->common->error_list($error)); }
	if ((! $error) && (! $errorcount)) { $t->set_var('message',''); }

	if (isset($phpgw_info['user']['preferences']['common']['currency']))
	{
		$currency = $phpgw_info['user']['preferences']['common']['currency'];
		$t->set_var('error','');
	}
	else { $t->set_var('error',lang('Please set your preferences for this application !')); }

	if (!$start) { $start = 0; }

	if ($order) { $ordermethod = " order by $order $sort"; }
	else { $ordermethod = " order by id asc"; }

	if ($query)
	{
		$querymethod = " AND (id like '%$query%' OR serial like '%$query%' OR name like '%$query%' OR descr like '%$query%' "
					. "OR cost like '%$query%' OR price like '%$query%' OR stock like '%$query%' OR mstock like '%$query%' OR url like '%$query%' "
					. "OR ftp like '%$query%' OR pdate like '%$query%' OR sdate like '%$query%' OR product_note like '%$query%')";
	}

	$archive_id = $inventory->get_status_id('archive');

	if (! $filter)
	{
		$filter = 0;
	}

	$db2 = $phpgw->db;

	if (($Order) or ($View) or ($Delete))
	{
		$sql = "select phpgw_inv_products.*,phpgw_inv_orderpos.piece,phpgw_inv_orderpos.tax from phpgw_inv_products $join phpgw_inv_orderpos "
						. "ON phpgw_inv_products.con=phpgw_inv_orderpos.product_id where phpgw_inv_orderpos.order_id='$order_id'";
	}

	if ((!$Order) && (!$View) && (!$Delete))
	{
		$sql = "select phpgw_inv_products.* from phpgw_inv_products where category='$filter' AND status != '$archive_id' $querymethod";
	}

	$db2->query($sql,__LINE__,__FILE__);
	$total_records = $db2->num_rows();

    $t->set_var('search_message',$phpgw->nextmatchs->show_hits($total_records,$start));    

//--------------------------------- nextmatch --------------------------------------------

	$left = $phpgw->nextmatchs->left('orderproducts.php',$start,$total_records);
	$right = $phpgw->nextmatchs->right('orderproducts.php',$start,$total_records);
	$t->set_var('left',$left);
	$t->set_var('right',$right);

// ------------------------------ end nextmatch ------------------------------------------

//------------------ list variable template-declarations----------------------------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('sort_id',$phpgw->nextmatchs->show_sort_order($sort,'id',$order,'orderproducts.php',lang('Product ID')));
	$t->set_var('sort_serial',$phpgw->nextmatchs->show_sort_order($sort,'serial',$order,'orderproducts.php',lang('Serial number')));
	$t->set_var('sort_name',$phpgw->nextmatchs->show_sort_order($sort,'name',$order,'orderproducts.php',lang('Name')));
	$t->set_var('lang_piece',lang('Piece'));
	$t->set_var('sort_dist',$phpgw->nextmatchs->show_sort_order($sort,'dist',$order,'orderproducts.php',lang('Distributor')));
	$t->set_var('sort_status',$phpgw->nextmatchs->show_sort_order($sort,'status',$order,'orderproducts.php',lang('Status')));
	$t->set_var('sort_cost',$phpgw->nextmatchs->show_sort_order($sort,'cost',$order,'orderproducts.php',lang('Cost')));
	$t->set_var('sort_price',$phpgw->nextmatchs->show_sort_order($sort,'price',$order,'orderproducts.php',lang('Price')));
	$t->set_var('sort_retail',$phpgw->nextmatchs->show_sort_order($sort,'retail',$order,'orderproducts.php',lang('Retail')));
	$t->set_var('sort_stock',$phpgw->nextmatchs->show_sort_order($sort,'stock',$order,'orderproducts.php',lang('Stock')));
	$t->set_var('h_lang_choose',lang('Select'));
	$t->set_var('filter_action',$phpgw->link('/inv/orderproducts.php','order_id=' . $order_id));
	$t->set_var('search_action',$phpgw->link('/inv/orderproducts.php','order_id=' . $order_id . '&filter=' . $filter));
	$t->set_var('actionurl',$phpgw->link('/inv/orderproducts.php','order_id=' . $order_id . '&filter=' . $filter));
	$t->set_var('lang_vieworder',lang('View order'));
	$t->set_var('lang_action',lang('Product list'));
	$t->set_var('lang_select_cats',lang('Select Category'));
	$t->set_var('category_list',$c->formatted_list('select','all',$filter,False));
	$t->set_var('lang_search',lang('Search'));
	$t->set_var('lang_submit',lang('Submit'));
	$t->set_var('currency',$currency);

	$d = CreateObject('phpgwapi.contacts');

// ----------------------- end declaration ---------------------------------

	if ((!$Order) && (!$View) && (!$Delete))
	{
		$taxpercent = select_tax($filter);
		$phpgw->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
		while ($phpgw->db->next_record())
		{
			$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
			$t->set_var('tr_color',$tr_color);

			$choose = '<input type="checkbox" name="choose[' . $phpgw->db->f('con') . ']" value="True">';

			$serial = $phpgw->strip_html($phpgw->db->f('serial'));
			if (! $serial) $serial = '&nbsp;';

			$name = $phpgw->strip_html($phpgw->db->f('name'));
			if (! $name) $name = '&nbsp;';

			$ab_id = $phpgw->db->f('dist');
			if (!$ab_id) { $dist = '&nbsp;'; }
			else
			{
				$cols = array('org_name' => 'org_name');
				$entry = $d->read_single_entry($ab_id,$cols);
				$dist = $entry[0]['org_name'];
			}

			$id = $phpgw->db->f('id');

			$piece = '<input type="text" name="piece[' . $phpgw->db->f('con') . ']" value="" size="5">';

			$status = $status_list[$phpgw->db->f('status')];
			$cost = $phpgw->db->f('cost');
			$price = $phpgw->db->f('price');
			$retail = round(($phpgw->db->f('price'))*(1+$taxpercent),2);
//    $retail = $phpgw->db->f('retail');
			$stock = $phpgw->db->f('stock');

			if ($phpgw->db->f('mstock') == $phpgw->db->f('stock')) { $stock = '<b>' . $phpgw->db->f('stock') . '</b>'; }
			if ($phpgw->db->f('mstock') > $phpgw->db->f('stock')) { $stock = '<font color="FF0000">' . $phpgw->db->f('stock') . '</font>'; }

//--------------- template declaration for list records -------------------------

			$t->set_var(array('choose' => $choose,
								'id' => $id,
							'serial' => $serial,
								'name' => $name,
							'piece' => $piece,
							'dist' => $dist,
							'status' => $status,
							'cost' => $cost,
							'price' => $price,
						'retail' => sprintf("%01.2f",$retail),
						'stock' => $stock));

			$t->parse('list','product_list',True);

// ------------------------- end record declaration ----------------------------

		}
	}

	if (($Order) or ($View) or ($Delete))
	{
		$phpgw->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);
		while ($phpgw->db->next_record())
		{
			$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
			$t->set_var('tr_color',$tr_color);
			$choose = '<input type="checkbox" name="choose[' . $phpgw->db->f('con') . ']" value="True" checked>';

			$serial = $phpgw->strip_html($phpgw->db->f('serial'));
			if (! $serial) { $serial = '&nbsp;'; }

			$name = $phpgw->strip_html($phpgw->db->f('name'));
			if (! $name) { $name = '&nbsp;'; }

			$tax = $phpgw->db->f('tax');

			$ab_id = $phpgw->db->f('dist');
			if (!$ab_id) { $dist ='&nbsp;'; }
			else
			{
				$cols = array('org_name' => 'org_name');
				$entry = $d->read_single_entry($ab_id,$cols);
				$dist = $entry[0]['org_name'];
			}

			$id = $phpgw->db->f('id');

			$piece = '<input type="text" name="piece[' . $phpgw->db->f('con') . ']" value="' . $phpgw->db->f('piece') . '" size="5">';

			$status = $status_list[$phpgw->db->f('status')];
			$cost = $phpgw->db->f('cost');
			$price = $phpgw->db->f('price');
			$taxpercent = ($tax/100);
			$retail = round(($phpgw->db->f('price'))*(1+$taxpercent),2);
//  $retail = $phpgw->db->f('retail');
			$stock = $phpgw->db->f('stock');
			if ($phpgw->db->f('mstock') == $phpgw->db->f('stock')) { $stock = '<b>' . $phpgw->db->f('stock') . '</b>'; }
			if ($phpgw->db->f('mstock') > $phpgw->db->f('stock')) { $stock = '<font color="FF0000">' . $phpgw->db->f('stock') . '</font>'; }

//-------------------- template declaration for list records --------------------

			$t->set_var(array('choose' => $choose,
									'id' => $id,
								'piece' => $piece,
								'serial' => $serial,
								'name' => $name,
								'dist' => $dist,
								'status' => $status,
								'cost' => $cost,
								'price' => $price,
							'retail' => sprintf("%01.2f",$retail),
							'stock' => $stock));

			$t->parse('list','product_list',True);

// -------------------------- end record declaration ------------------------

		}
	}

	if ($inventory->check_perms($grants[$owner],PHPGW_ACL_ADD) || $owner == $phpgw_info['user']['account_id'])
	{
		$t->set_var('addtoorder','<input type="submit" name="Order" value="' . lang('Add to order') .'">');
	}
	else { $t->set_var('addtoorder',''); }

	if ($inventory->check_perms($grants[$owner],PHPGW_ACL_EDIT) || $inventory->check_perms($grants[$owner],PHPGW_ACL_DELETE) || $owner == $phpgw_info['user']['account_id'])
	{
		$t->set_var('updateorder','<input type="submit" name="Delete" value="' . lang('Delete from order') .'">');
	}
	else { $t->set_var('updateorder',''); }

	$t->parse('out','product_list_t',True);
	$t->p('out');
	$phpgw->common->phpgw_footer();
?>
