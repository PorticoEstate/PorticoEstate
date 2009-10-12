<?php
	/**************************************************************************\
	* phpGroupWare - Inventory                                                 *
	* http://www.phpgroupware.org                                              *
	* This file written by Joseph Engo <jengo@phpgroupware.org>                *
	* 					   Bettina Gille [ceb@phpgroupware.org]                *
	* --------------------------------------------                             *
	* This program is free software; you can redistribute it and/or modify it  *
	* under the terms of the GNU General Public License as published by the    *
	* Free Software Foundation; either version 2 of the License, or (at your   *
	* option) any later version.                                               *
	\**************************************************************************/
	/* $Id$ */
  
	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('inv_header' => 'header.tpl'));
	$t->set_var('info',lang('Inventory'));
	$t->set_var('bg_color',$phpgw_info['theme']['th_bg']);
	$t->set_var('tr_color1',$phpgw_info['theme']['row_on']);

	$t->set_var('link_products',$phpgw->link('/inv/listproducts.php','subproduct=True'));
	$t->set_var('lang_products',lang('Products'));

	$t->set_var('link_categorys',$phpgw->link('/inv/index.php'));
	$t->set_var('lang_categorys',lang('Categories'));

	$t->set_var('link_status',$phpgw->link('/inv/liststatus.php')); 
	$t->set_var('lang_status',lang('Product status'));

	$t->set_var('link_orders',$phpgw->link('/inv/listorders.php')); 
	$t->set_var('lang_orders',lang('Orders'));

	$t->set_var('link_dist',$phpgw->link('/inv/listdist.php')); 
	$t->set_var('lang_dist',lang('Distributors'));

	$t->set_var('link_archive',$phpgw->link('/inv/archiv.php','subarchive=True'));
    $t->set_var('lang_archive',lang('Archive'));

	$t->set_var('link_rooms',$phpgw->link('/inv/rooms.php','subroom=True')); 
    $t->set_var('lang_rooms',lang('Stock rooms'));

	if ($subarchive == True)
	{
		$t->set_var('sub_productarchive',$phpgw->link('/inv/archiv.php','subarchive=True')); 
		$t->set_var('sub_orderarchive',$phpgw->link('/inv/order_archiv.php','subarchive=True')); 
		$t->set_var('sublang_productarchive',lang('Product archive'));
		$t->set_var('sublang_orderarchive',lang('Order archive'));
		$t->set_var('tr_color2',$phpgw_info['theme']['row_off']);
	}
	else
	{
		$t->set_var('sub_productarchive','');
		$t->set_var('sub_orderarchive','');
        $t->set_var('sublang_productarchive','');
        $t->set_var('sublang_orderarchive','');
	}

	if ($subroom == True)
	{
		$t->set_var('sub_rooms',$phpgw->link('/inv/rooms.php','subroom=True')); 
		$t->set_var('sub_stockprd',$phpgw->link('/inv/liststockprd.php','subroom=True')); 
		$t->set_var('sublang_rooms',lang('Stock rooms'));
		$t->set_var('sublang_stockprd',lang('Product location'));
		$t->set_var('tr_color2',$phpgw_info['theme']['row_off']);
	}
	else
	{
		$t->set_var('sub_rooms','');
		$t->set_var('sub_stockprd','');
        $t->set_var('sublang_rooms','');
        $t->set_var('sublang_stockprd','');
	}

	if ($subproduct == True)
	{
		$t->set_var('sub_products',$phpgw->link('/inv/listproducts.php','subproduct=True')); 
		$t->set_var('sub_minstock',$phpgw->link('/inv/listminstock.php','subproduct=True'));
		$t->set_var('sub_receipt',$phpgw->link('/inv/listreceipt.php','subproduct=True')); 
		$t->set_var('sublang_products',lang('Products'));
		$t->set_var('sublang_minstock',lang('Out of stock products'));
		$t->set_var('sublang_receipt',lang('Stock receipt'));
		$t->set_var('tr_color2',$phpgw_info['theme']['row_off']);
	}
	else
	{
		$t->set_var('sub_products','');
		$t->set_var('sub_minstock','');
		$t->set_var('sub_receipt','');
        $t->set_var('sublang_products','');
        $t->set_var('sublang_minstock','');
        $t->set_var('sublang_receipt','');
	}

	if (!$subarchive && !$subproduct && !$subroom)
	{
		$t->set_var('tr_color2','');
	}

	$t->pparse('out','inv_header');
?>
