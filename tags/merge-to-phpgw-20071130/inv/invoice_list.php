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
	/* $Id: invoice_list.php 6491 2001-07-03 12:11:40Z bettina $ */

	$phpgw_info['flags'] = array('currentapp' => 'inv',
					'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	$db2 = $phpgw->db;

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);  
	$t->set_file(array('invoice_list_t' => 'listdelivery.tpl',                                                                                                                
					'invoice_list' => 'listdelivery.tpl'));
	$t->set_block('invoice_list_t','invoice_list','list'); 

	if ($order) { $ordermethod = "order by $order $sort"; } 
	else { $ordermethod = "order by num asc"; }

	$t->set_var('lang_action',lang('Invoice list'));
	$t->set_var('searchurl',$phpgw->link('/inv/invoice_list.php'));
	$t->set_var('lang_search',lang('search'));

	if (! $start)
	{
		$start = 0;
	}

	if ($query)
	{
		$querymethod = " AND (num like '%$query%' or date like '%$query%')";
	}

	$db2 = $phpgw->db;

	$sql = "select * from phpgw_inv_invoice WHERE order_id='$order_id' $querymethod";
	$db2->query($sql,__LINE__,__FILE__);
	$total_records = $db2->num_rows();

	$t->set_var('lang_showing',$phpgw->nextmatchs->show_hits($total_records,$start));

// ---------------- nextmatch variable template-declarations ------------------------------

	$left = $phpgw->nextmatchs->left('invoice_list.php',$start,$total_records);
	$right = $phpgw->nextmatchs->right('invoice_list.php',$start,$total_records);
	$t->set_var('left',$left);
	$t->set_var('right',$right);

// ------------------------- end nextmatch template ---------------------------------------

// -------------- header declaration -----------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('sort_num',$phpgw->nextmatchs->show_sort_order($sort,'num',$order,'/inv/invoice_list.php',lang('Invoice ID')));
	$t->set_var('sort_date',$phpgw->nextmatchs->show_sort_order($sort,'date',$order,'/inv/invoice_list.php',lang('Date')));
	$t->set_var('head_delivery',lang('Invoice'));
	$t->set_var('lang_delivery','');

// -------------- end header declaration -----------------

	$phpgw->db->limit_query($sql . $ordermethod,$start,__LINE__,__FILE__);

	while ($phpgw->db->next_record())
	{
		$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
		$t->set_var('tr_color',$tr_color);
		$num = $phpgw->strip_html($phpgw->db->f('num'));

		$date = $phpgw->db->f('date');
		$month = $phpgw->common->show_date(time(),'n');
		$day = $phpgw->common->show_date(time(),'d');
		$year = $phpgw->common->show_date(time(),'Y');

		$date = $date + (60*60) * $phpgw_info['user']['preferences']['common']['tz_offset'];
		$dateout =  $phpgw->common->show_date($date,$phpgw_info['user']['preferences']['common']['dateformat']);

//--------- template declaration for list records--------------------

		$t->set_var(array('num' => $num,
						'date' => $dateout));

		$t->set_var('delivery',$phpgw->link('/inv/invoice_update.php','invoice_id=' . $phpgw->db->f('id') . '&order_id=' . $order_id)); 
		$t->set_var('lang_delivery',lang('Invoice'));
		$t->parse('list','invoice_list',True);
	}

// -------------------- end record declaration ------------------------

	$t->parse('out','invoice_list_t',True);
	$t->p('out');

	$phpgw->common->phpgw_footer();
?>
