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
	/* $Id: liststockprd.php 6496 2001-07-03 14:44:51Z bettina $ */

	$phpgw_info['flags'] = array('currentapp' => 'inv',
					'enable_nextmatchs_class' => True);

	include('../header.inc.php');

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('listproducts_t' => 'liststockprd.tpl',
						'listproducts' => 'liststockprd.tpl'));

    $t->set_block('listproducts_t','listproducts','list');

	$inventory = CreateObject('inv.inventory');
	$grants = $phpgw->acl->get_grants('inv');
	$grants[$phpgw_info['user']['account_id']] = PHPGW_ACL_READ + PHPGW_ACL_ADD + PHPGW_ACL_EDIT + PHPGW_ACL_DELETE;
	$c = CreateObject('phpgwapi.categories');

	$archive_id = $inventory->get_status_id('archive');

	if (!$start) { $start = '0'; }

	if ($query)
	{
		$querymethod = " AND (id like '%$query%' OR serial like '%$query%' OR name like '%$query%' OR descr like '%$query%' "
					. "OR cost like '%$query%' OR price like '%$query%' OR stock like '%$query%' OR mstock like '%$query%' OR url like '%$query%' "
					. "OR ftp like '%$query%' OR pdate like '%$query%' OR sdate like '%$query%' OR product_note like '%$query%')";
	}

	if (! $filter)
	{
		$phpgw->db->query("SELECT bin from phpgw_inv_products WHERE status != '$archive_id' $querymethod");
		$phpgw->db->next_record();
		$room = $inventory->one_room($phpgw->db->f('bin'));
		if ($room)
		{
			if ($inventory->check_perms($grants[$room[0]['owner']],PHPGW_ACL_READ) || $room[0]['owner'] == $phpgw_info['user']['account_id'])
			{
				$filter = $phpgw->db->f('bin');
			}
		}
		else { $filter = '999'; }
	}
	else { $room = $inventory->one_room($filter); }

	$products = $inventory->read_products($start,True,$query,'bin',$filter,$sort,$order,'active');

//--------------------------------- nextmatch --------------------------------------------

	$left = $phpgw->nextmatchs->left('/inv/liststockprd.php',$start,$inventory->total_records,'&subroom=True');
	$right = $phpgw->nextmatchs->right('/inv/liststockprd.php',$start,$inventory->total_records,'&subroom=True');
	$t->set_var('left',$left);
	$t->set_var('right',$right);

	$t->set_var('search_message',$phpgw->nextmatchs->show_hits($inventory->total_records,$start));

// ------------------------------ end nextmatch ------------------------------------------

//---------------------------- list variable template-declarations -------------------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('sort_id',$phpgw->nextmatchs->show_sort_order($sort,'id',$order,'/inv/liststockprd.php',lang('Product ID'),'&subroom=True'));
	$t->set_var('sort_serial',$phpgw->nextmatchs->show_sort_order($sort,'serial',$order,'/inv/liststockprd.php',lang('Serial number'),'&subroom=True'));
	$t->set_var('sort_name',$phpgw->nextmatchs->show_sort_order($sort,'name',$order,'/inv/liststockprd.php',lang('Name'),'&subroom=True'));
	$t->set_var('sort_category',$phpgw->nextmatchs->show_sort_order($sort,'category',$order,'/inv/liststockprd.php',lang('Category'),'&subroom=True'));
	$t->set_var('sort_status',$phpgw->nextmatchs->show_sort_order($sort,'status',$order,'/inv/liststockprd.php',lang('Status'),'&subroom=True'));
	$t->set_var('sort_note',$phpgw->nextmatchs->show_sort_order($sort,'product_note',$order,'/inv/liststockprd.php',lang('Note'),'&subroom=True'));
	$t->set_var('sort_status',$phpgw->nextmatchs->show_sort_order($sort,'status',$order,'/inv/liststockprd.php',lang('Status'),'&subroom=True'));
	$t->set_var('sort_stock',$phpgw->nextmatchs->show_sort_order($sort,'stock',$order,'/inv/liststockprd.php',lang('Stock'),'&subroom=True'));
	$t->set_var('sort_mstock',$phpgw->nextmatchs->show_sort_order($sort,'mstock',$order,'/inv/liststockprd.php',lang('min Stock'),'&subroom=True'));
	$t->set_var('lang_edit',lang('Edit'));
	$t->set_var('lang_view',lang('View'));
	$t->set_var('lang_action',lang('Product location'));
	$t->set_var('filter_action',$phpgw->link('/inv/liststockprd.php','subroom=True'));
	$t->set_var('search_action',$phpgw->link('/inv/liststockprd.php','subroom=True'));
	$t->set_var('lang_select_room',lang('Select stock room'));
	$t->set_var('lang_search',lang('Search'));
	$t->set_var('room_list',$inventory->select_room_list($filter));
	$t->set_var('lang_submit',lang('Submit'));

// -------------------------------- end declaration -----------------------------------------

	$d = CreateObject('phpgwapi.contacts');

	for ($i=0;$i<count($products);$i++)
	{
		$phpgw->templater_color = $phpgw->nextmatchs->alternate_row_color($phpgw->templater_color);
		$t->set_var('tr_color',$phpgw->templater_color);

		$cat_name = $c->id2name($products[$i]['category'],'name');

		$serial = $phpgw->strip_html($products[$i]['serial']);
		if (! $serial) $serial = '&nbsp;';

		$name = $phpgw->strip_html($products[$i]['name']);
		if (! $name) $name = '&nbsp;';

		$id = $phpgw->strip_html($products[$i]['id']);
		$status = $status_list[$products[$i]['status']];
		$statusout = lang($status);

		$note = $products[$i]['product_note'];
        if (! $note) $note = '&nbsp;';

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
		if ($products[$i]['mstock'] > $products[$i]['stock']) { $stock = '<font color="FF0000"><b>' . $products[$i]['stock'] . '</b></font>'; }

		$mstock = $products[$i]['mstock'];

//---------------------------------- list records -------------------------------------

		$t->set_var(array('id' => $id,
						'name' => $name,
						'category' => $cat_name,
						'note' => $note,
						'status' => $statusout,
						'stock' => $stock,
						'mstock' => $mstock,
						'serial' => $serial));

		if ($inventory->check_perms($grants[$room[0]['owner']],PHPGW_ACL_EDIT) || $room[0]['owner'] == $phpgw_info['user']['account_id'])
		{
			$t->set_var('edit',$phpgw->link('/inv/editproduct.php','con=' . $products[$i]['con'] . '&filter=' . $filter . '&subroom=True'));
			$t->set_var('lang_edit_entry',lang('Edit'));
		}
		else
		{
			$t->set_var('edit','');
			$t->set_var('lang_edit_entry','&nbsp;');
		}

		$t->set_var('view',$phpgw->link('/inv/viewproduct.php','con=' . $products[$i]['con'] . '&filter=' . $filter . '&subroom=True'));

		$t->parse('list','listproducts',True);
	}

	if ($inventory->check_perms($grants[$room[0]['owner']],PHPGW_ACL_ADD) || $room[0]['owner'] == $phpgw_info['user']['account_id'])
	{
		$t->set_var('action','<form method="POST" action="' . $phpgw->link('/inv/addproduct.php','bin=' . $filter . '&filter=' . $filter . '&subroom=True')
							. '"><input type="submit" value="' . lang('Add') .'"></form>');
	}
	else { $t->set_var('action',''); }

// ---------------------------- end list records -----------------------------------------

	$t->parse('out','listproducts_t',True);
	$t->p('out');

	$phpgw->common->phpgw_footer();
?>
