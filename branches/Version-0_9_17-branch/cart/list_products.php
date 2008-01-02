<?php
	/**************************************************************************\
	* phpGroupWare - Shopping cart                                             *
	* http://www.phpgroupware.org                                              *
	* Written by Bettina Gille [ceb@phpgroupware.org]                          *
	* -----------------------------------------------                          *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id: list_products.php 9701 2002-03-11 11:04:57Z milosch $ */

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'cart',
		'enable_nextmatchs_class' => True
	);

	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array(
		'listproducts_t' => 'list_products.tpl',
		'listproducts'   => 'list_products.tpl'
	));

	$GLOBALS['phpgw']->template->set_block('listproducts_t','listproducts','list');

	$inventory = CreateObject('inv.inventory');
	$grants = $GLOBALS['phpgw']->acl->get_grants('inv');
	$c = CreateObject('phpgwapi.categories');
	$c->app_name = 'inv';
    $GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/cart/list_products.php'));

	$currency = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];

	$archive_id = $inventory->get_status_id('archive');

	if(!$start)
	{
		$start = '0';
	}

	if($query)
	{
		$querymethod = " AND (id like '%$query%' OR serial like '%$query%' OR name like '%$query%' OR descr like '%$query%' "
			. "OR cost like '%$query%' OR price like '%$query%' OR stock like '%$query%' OR mstock like '%$query%' OR url like '%$query%' "
			. "OR ftp like '%$query%' OR pdate like '%$query%' OR sdate like '%$query%' OR product_note like '%$query%')";
	}

	if($cat_id)
	{
		$filter = $cat_id;
	}

	if(!$filter) 
	{
		$GLOBALS['phpgw']->db->query("SELECT category from phpgw_inv_products WHERE status != '$archive_id' $querymethod");    
		$GLOBALS['phpgw']->db->next_record();
		$category = $c->return_single($GLOBALS['phpgw']->db->f('category'));
		if($category)
		{
			if($inventory->check_perms($grants[$category[0]['owner']],PHPGW_ACL_READ) || $category[0]['owner'] == $GLOBALS['phpgw_info']['user']['account_id']) 
			{
				$filter = $GLOBALS['phpgw']->db->f('category');
			}
		}
		else 
		{
			$filter = '999';
		}
	}
	else
	{
		$category = $c->return_single($filter);
	}

	$products = $inventory->read_products($start,True,$query,'category',$filter,$sort,$order,'active');

//--------------------------------- nextmatch --------------------------------------------

	$left = $GLOBALS['phpgw']->nextmatchs->left('/cart/list_products.php',$start,$inventory->total_records);
	$right = $GLOBALS['phpgw']->nextmatchs->right('/cart/list_products.php',$start,$inventory->total_records);
	$GLOBALS['phpgw']->template->set_var('left',$left);
	$GLOBALS['phpgw']->template->set_var('right',$right);

	$GLOBALS['phpgw']->template->set_var('lang_showing',$GLOBALS['phpgw']->nextmatchs->show_hits($inventory->total_records,$start)); 

// ------------------------------ end nextmatch ------------------------------------------

//---------------------------- list variable template-declarations -------------------------

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('sort_id',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'id',$order,'/cart/list_products.php',lang('Product ID')));
	$GLOBALS['phpgw']->template->set_var('sort_name',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'name',$order,'/cart/list_products.php',lang('Name')));
	$GLOBALS['phpgw']->template->set_var('sort_retail',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'retail',$order,'/cart/list_products.php',lang('Price')));
	$GLOBALS['phpgw']->template->set_var('lang_view',lang('View'));
	$GLOBALS['phpgw']->template->set_var('currency',$currency);
	$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/cart/list_products.php'));
	$GLOBALS['phpgw']->template->set_var('lang_search',lang('Search'));
	$GLOBALS['phpgw']->template->set_var('category_list',$c->formated_list('list','all',$filter,False,'/cart/list_products.php'));
	$GLOBALS['phpgw']->template->set_var('lang_submit',lang('Submit'));
	$GLOBALS['phpgw']->template->set_var('lang_choose',lang('Choose'));
	$GLOBALS['phpgw']->template->set_var('lang_piece',lang('Piece'));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add to Shopping cart'));

// -------------------------------- end declaration -----------------------------------------

	$taxpercent = select_tax($filter);
	for($i=0;$i<count($products);$i++) 
	{
		$GLOBALS['phpgw']->templater_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($GLOBALS['phpgw']->templater_color);
		$GLOBALS['phpgw']->template->set_var('tr_color',$GLOBALS['phpgw']->templater_color);
		$choose = '<input type="checkbox" name="choose[' . $products[$i]['con'] . ']" value="True">';
		$piece = '<input type="text" name="piece[]" value="" size="5">';
		$name = $GLOBALS['phpgw']->strip_html($products[$i]['name']);
		if(!$name)
		{
			$name = '&nbsp;';
		}
		$id = $GLOBALS['phpgw']->strip_html($products[$i]['id']);
		$price = $products[$i]['price'];
		$retail = round(($price)*(1+$taxpercent),2);

//---------------------------------- list records -------------------------------------

		$GLOBALS['phpgw']->template->set_var(array(
			'choose' => $choose,
			'piece' => $piece,
			'id' => $id,
			'name' => $name,
			'retail' => sprintf("%01.2f",$retail)
		));

		$GLOBALS['phpgw']->template->set_var('view',$GLOBALS['phpgw']->link('/cart/view_product.php','con=' . $products[$i]['con'] . '&filter=' . $filter));

		$GLOBALS['phpgw']->template->parse('list','listproducts',True);
	}

// ---------------------------- end list records -----------------------------------------

	$GLOBALS['phpgw']->template->parse('out','listproducts_t',True);
	$GLOBALS['phpgw']->template->p('out');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
