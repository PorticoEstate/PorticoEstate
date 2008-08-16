<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* Written by Joseph Engo <jengo@phpgroupware.org>                          *
	*            Bettina Gille [ceb@phpgroupware.org]                          *
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

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('listproducts_t' => 'listproducts_full.tpl',
						'listproducts' => 'listproducts_full.tpl'));

	$t->set_block('listproducts_t','listproducts','list');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
	$c = CreateObject('phpgwapi.categories');

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	if (isset($phpgw_info['user']['preferences']['common']['currency']))
	{
		$currency = $phpgw_info['user']['preferences']['common']['currency'];
		$t->set_var('error','');
	}
	else
	{
		$t->set_var('error',lang('Please set your preferences for this application !'));
	}

	$archive_id = $inventory->get_status_id('archive');

	if (!$start) { $start = 0; }

	if ($query)
	{
		$querymethod = " AND (id like '%$query%' OR serial like '%$query%' OR name like '%$query%' OR descr like '%$query%' "
						. "OR cost like '%$query%' OR price like '%$query%' OR stock like '%$query%' OR mstock like '%$query%' OR url like '%$query%' "
						. "OR ftp like '%$query%' OR pdate like '%$query%' OR sdate like '%$query%' OR product_note like '%$query%')";
	}

	if (! $filter)
	{
		$phpgw->db->query("select category from phpgw_inv_products WHERE status='$archive_id' $querymethod");
		$phpgw->db->next_record();
		$category = $c->return_single($phpgw->db->f('category'));
		if ($category)
		{
			if ($inventory->check_perms($grants[$category[0]['owner']],PHPGW_ACL_READ) || $category[0]['owner'] == $phpgw_info['user']['account_id'])
			{
				$filter = $phpgw->db->f('category');
			}
		}
		else { $filter = '999'; }
	}
	else { $category = $c->return_single($filter); }

	$products = $inventory->read_products($start,True,$query,'category',$filter,$sort,$order,'archive');

//--------------------------------- nextmatch --------------------------------------------

	$left = $phpgw->nextmatchs->left('/inv/archiv.php',$start,$inventory->total_records,'&subarchive=True');
	$right = $phpgw->nextmatchs->right('/inv/archiv.php',$start,$inventory->total_records,'&subarchive=True');
	$t->set_var('left',$left);
	$t->set_var('right',$right);

	$t->set_var('search_message',$phpgw->nextmatchs->show_hits($inventory->total_records,$start));

// ------------------------------ end nextmatch ------------------------------------------ 

//---------------------------- list variable template-declarations -------------------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('sort_id',$phpgw->nextmatchs->show_sort_order($sort,'id',$order,'/inv/archiv.php',lang('Product ID'),'&subarchive=True'));
	$t->set_var('sort_serial',$phpgw->nextmatchs->show_sort_order($sort,'serial',$order,'/inv/archiv.php',lang('serial number'),'&subarchive=True'));
	$t->set_var('sort_name',$phpgw->nextmatchs->show_sort_order($sort,'name',$order,'/inv/archiv.php',lang('Name'),'&subarchive=True'));
	$t->set_var('sort_dist',$phpgw->nextmatchs->show_sort_order($sort,'dist',$order,'/inv/archiv.php',lang('Distributor'),'&subarchive=True'));
	$t->set_var('sort_status',$phpgw->nextmatchs->show_sort_order($sort,'status',$order,'/inv/archiv.php',lang('Status'),'&subarchive=True'));
	$t->set_var('sort_cost',$phpgw->nextmatchs->show_sort_order($sort,'cost',$order,'/inv/archiv.php',lang('Cost'),'&subarchive=True'));
	$t->set_var('sort_price',$phpgw->nextmatchs->show_sort_order($sort,'price',$order,'/inv/archiv.php',lang('Price'),'&subarchive=True'));
	$t->set_var('sort_retail',$phpgw->nextmatchs->show_sort_order($sort,'retail',$order,'/inv/archiv.php',lang('Retail'),'&subarchive=True'));
	$t->set_var('sort_stock',$phpgw->nextmatchs->show_sort_order($sort,'stock',$order,'/inv/archiv.php',lang('Stock'),'&subarchive=True'));
	$t->set_var('lang_edit',lang('Edit'));
	$t->set_var('lang_view',lang('View'));
	$t->set_var('currency',$currency);
	$t->set_var('lang_search',lang('Search'));
	$t->set_var('lang_action',lang('Product archive'));
	$t->set_var('filter_action',$phpgw->link('/inv/archiv.php','subarchive=True'));
	$t->set_var('action','');
	$t->set_var('search_action',$phpgw->link('/inv/archiv.php','subarchive=True'));
	$t->set_var('lang_select_cat',lang('Select category'));
	$t->set_var('category_list',$c->formatted_list('select','all',$filter,'False'));
	$t->set_var('lang_submit',lang('Submit'));

// -------------------------------- end declaration -----------------------------------------

	$d = CreateObject('phpgwapi.contacts');
	$taxpercent = select_tax($filter);

	for ($i=0;$i<count($products);$i++)
	{
		$phpgw->templater_color = $phpgw->nextmatchs->alternate_row_color($phpgw->templater_color);
		$t->set_var('tr_color',$phpgw->templater_color);

		$serial = $phpgw->strip_html($products[$i]['serial']);
		if (! $serial) $serial = '&nbsp;';

		$name = $phpgw->strip_html($products[$i]['name']);
		if (! $name) $name = '&nbsp;';

		$id = $phpgw->strip_html($products[$i]['id']);
		$status = $status_list[$products[$i]['status']];
		$cost = $products[$i]['cost'];
		$price = $products[$i]['price'];
		$retail = round(($price)*(1+$taxpercent),2);

		$ab_id = $products[$i]['dist'];
		if (!$ab_id) { $dist = '&nbsp;'; }
		else
		{
			$cols = array('org_name' => 'org_name');
			$entry = $d->read_single_entry($ab_id,$cols);
			$dist = $entry[0]['org_name'];
		}

		if ($products[$i]['mstock'] == $products[$i]['stock']) { $stock = '<b>' . $products[$i]['stock'] . '</b>'; }
		if ($products[$i]['mstock'] < $products[$i]['stock']) { $stock = $products[$i]['stock']; }
		if ($products[$i]['mstock'] > $products[$i]['stock']) { $stock = '<font color=FF0000><b>' . $products[$i]['stock'] . '</b></font>'; }

//---------------------------------- list records -------------------------------------

		$t->set_var(array('id' => $id,
						'name' => $name,
						'dist' => $dist,
						'status' => $status,
						'cost' => $cost,
						'price' => $price,
						'retail' => sprintf("%01.2f",$retail),
						'stock' => $stock,
						'serial' => $serial));

		if ($inventory->check_perms($grants[$category[0]['owner']],PHPGW_ACL_EDIT) || $category[0]['owner'] == $phpgw_info['user']['account_id'])
		{
			$t->set_var('edit',$phpgw->link('/inv/editproduct.php','con=' . $products[$i]['con'] . '&filter=' . $filter . '&subarchive=True'));
			$t->set_var('lang_edit_entry',lang('Edit'));
		}
		else
		{
			$t->set_var('edit','');
			$t->set_var('lang_edit_entry','&nbsp;');
		}

		$t->set_var('view',$phpgw->link('/inv/viewproduct.php','con=' . $products[$i]['con'] . '&filter=' . $filter . '&subarchive=True'));

		$t->parse('list','listproducts',True);
	}

// ---------------------------- end list records -----------------------------------------

	$t->parse('out','listproducts_t',True);
	$t->p('out');

	$phpgw->common->phpgw_footer();
?>
