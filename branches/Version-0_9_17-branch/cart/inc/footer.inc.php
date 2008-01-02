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

	/* $Id: footer.inc.php 9701 2002-03-11 11:04:57Z milosch $ */

	$GLOBALS['phpgw']->template->set_file(array('cart_footer' => 'footer.tpl'));
	$GLOBALS['phpgw']->template->set_var('info',lang('Shopping cart'));
	$GLOBALS['phpgw']->template->set_var('bg_color',$GLOBALS['phpgw_info']['theme']['th_bg']);

	$GLOBALS['phpgw']->template->set_var('link_catalog',$GLOBALS['phpgw']->link('/cart/list_products.php'));
	$GLOBALS['phpgw']->template->set_var('lang_catalog',lang('Our catalog'));
	$GLOBALS['phpgw']->template->set_var('link_cart',$GLOBALS['phpgw']->link('/cart/cart.php'));
	$GLOBALS['phpgw']->template->set_var('lang_cart',lang('Shopping cart'));
	$GLOBALS['phpgw']->template->set_var('link_service',$GLOBALS['phpgw']->link('/cart/service.php')); 
	$GLOBALS['phpgw']->template->set_var('lang_service',lang('Customer service'));
	$GLOBALS['phpgw']->template->set_var('link_order',$GLOBALS['phpgw']->link('/cart/order.php'));
	$GLOBALS['phpgw']->template->set_var('lang_order',lang('Order'));

	$GLOBALS['phpgw']->template->pparse('out','cart_footer');
?>
