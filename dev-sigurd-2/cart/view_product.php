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

	/* $Id$ */

	if(!$Add)
	{
		$referer = $HTTP_REFERER;
	}

	if(!$con)
	{
		$GLOBALS['phpgw_info']['flags'] = array(
			'noheader' => True,
			'nonavbar' => True
		);
		Header('Location: ' . $HTTP_REFERER);
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'cart';
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array('view' => 'view_product.tpl'));

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
		. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
		. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
		. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
		. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n"
		. '<input type="hidden" name="referer" value="' . $referer . '">' . "\n"
		. '<input type="hidden" name="con" value="' . $con . '">' . "\n";

	$c = CreateObject('phpgwapi.categories');

	$currency = $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'];
	$GLOBALS['phpgw']->template->set_var('hidden_vars',$hidden_vars);
	$GLOBALS['phpgw']->template->set_var('lang_id',lang('Product ID'));
	$GLOBALS['phpgw']->template->set_var('lang_short_name',lang('Short Name'));
	$GLOBALS['phpgw']->template->set_var('lang_description',lang('Description'));
	$GLOBALS['phpgw']->template->set_var('lang_category',lang('Category'));
	$GLOBALS['phpgw']->template->set_var('lang_price',lang('Price'));
	$GLOBALS['phpgw']->template->set_var('lang_piece',lang('Piece'));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('Add to Shopping cart'));
	$GLOBALS['phpgw']->template->set_var('currency',$currency);
	$GLOBALS['phpgw']->template->set_var('image',PHPGW_IMAGES . '/logo.jpg');

	$GLOBALS['phpgw']->db->query("select * from phpgw_inv_products where con='$con'");
	$GLOBALS['phpgw']->db->next_record();

	$category = $GLOBALS['phpgw']->db->f('category');
	$GLOBALS['phpgw']->template->set_var('cat_name',$c->return_name($category));
	$GLOBALS['phpgw']->template->set_var('piece',$piece);

	$price = $GLOBALS['phpgw']->db->f('price');
	$GLOBALS['phpgw']->template->set_var('id',$GLOBALS['phpgw']->db->f('id'));
	$GLOBALS['phpgw']->template->set_var('name',$GLOBALS['phpgw']->strip_html($GLOBALS['phpgw']->db->f('name')));
	$GLOBALS['phpgw']->template->set_var('descr',$GLOBALS['phpgw']->strip_html($GLOBALS['phpgw']->db->f('descr')));

	$taxpercent = select_tax($category);
	$retail = round($price*(1+$taxpercent),2);
	$GLOBALS['phpgw']->template->set_var('retail',sprintf("%01.2f",$retail));

	$GLOBALS['phpgw']->template->set_var('action_url',$GLOBALS['phpgw']->link('/cart/view_product.php'));
	$GLOBALS['phpgw']->template->set_var('done_action',$referer);
	$GLOBALS['phpgw']->template->set_var('lang_done',lang('Done'));
	$GLOBALS['phpgw']->template->pparse('out','view');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
