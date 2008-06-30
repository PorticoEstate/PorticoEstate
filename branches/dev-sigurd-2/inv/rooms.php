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
	$t->set_file(array('room_list_t' => 'listrooms.tpl',
						'room_list' => 'listrooms.tpl'));
	$t->set_block('room_list_t','room_list','list');

	$hidden_vars = '<input type="hidden" name="sort" value="' . $sort . '">' . "\n"
				. '<input type="hidden" name="order" value="' . $order . '">' . "\n"
				. '<input type="hidden" name="query" value="' . $query . '">' . "\n"
				. '<input type="hidden" name="start" value="' . $start . '">' . "\n"
				. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	if (! $start)
	{
		$start = 0;
	}

	$rooms = $inventory->read_rooms($start,True,$query,$filter,$sort,$order);

//--------------------------------- nextmatch --------------------------------------------

	$left = $phpgw->nextmatchs->left('/inv/rooms.php',$start,$inventory->total_records,'&subroom=True');
	$right = $phpgw->nextmatchs->right('/inv/rooms.php',$start,$inventory->total_records,'&subroom=True');
	$t->set_var('left',$left);
	$t->set_var('right',$right);

    $t->set_var('lang_showing',$phpgw->nextmatchs->show_hits($inventory->total_records,$start));

// ------------------------------ end nextmatch ------------------------------------------

	$t->set_var('title_action',lang('Stock room list'));
	$t->set_var('lang_search',lang('Search'));
	$t->set_var('hidden_vars',$hidden_vars);
	$t->set_var('search_action',$phpgw->link('/inv/rooms.php'));

// -------------------------- header declaration ------------------------------------------

	$t->set_var('th_bg',$phpgw_info['theme']['th_bg']);
	$t->set_var('sort_name',$phpgw->nextmatchs->show_sort_order($sort,'room_name',$order,'/inv/rooms.php',lang('Name'),'&subroom=True'));
	$t->set_var('sort_note',$phpgw->nextmatchs->show_sort_order($sort,'room_note',$order,'/inv/rooms.php',lang('Note'),'&subroom=True'));
	$t->set_var('lang_products',lang('Products'));
	$t->set_var('lang_edit',lang('Edit'));
	$t->set_var('lang_delete',lang('Delete'));
	$t->set_var('lang_add',lang('Add'));

// ---------------------------- end header declaration ------------------------------------

	for ($i=0;$i<count($rooms);$i++)
	{
		$id = $rooms[$i]['id'];
		$owner = $rooms[$i]['owner'];
		$tr_color = $phpgw->nextmatchs->alternate_row_color($tr_color);
		$t->set_var('tr_color',$tr_color);
		$room_name = $phpgw->strip_html($rooms[$i]['name']);

		$room_note = $phpgw->strip_html($rooms[$i]['note']);
		if (!$room_note) { $room_note = '&nbsp;'; }

//--------- template declaration for list records--------------------

		$t->set_var(array('room_name' => $room_name,
							'room_note' => $room_note));

		$t->set_var('products',$phpgw->link('/inv/liststockprd.php','filter=' . $id . '&subroom=True'));
		$t->set_var('lang_products_entry',lang('Products'));

		if ($inventory->check_perms($grants[$owner],PHPGW_ACL_EDIT) || $owner == $phpgw_info['user']['account_id'])
		{
			$t->set_var('edit',$phpgw->link('/inv/editroom.php','id=' . $id . '&subroom=True'));
			$t->set_var('lang_edit_entry',lang('Edit'));
		}
		else
		{
			$t->set_var('edit','');
			$t->set_var('lang_edit_entry','&nbsp;');
		}

		if ($inventory->check_perms($grants[$owner],PHPGW_ACL_DELETE) || $owner == $phpgw_info['user']['account_id'])
		{
			$t->set_var('delete',$phpgw->link('/inv/deleteroom.php','id=' . $id . '&subroom=True'));
			$t->set_var('lang_delete_entry',lang('Delete'));
		}
		else
		{
			$t->set_var('delete','');
			$t->set_var('lang_delete_entry','&nbsp;');
		}

		$t->parse('list','room_list',True);
	}

//-------- -------------- end record declaration ------------------------

	$t->set_var('add_action',$phpgw->link('/inv/addroom.php','filter=' . $filter . '&start=' . $start . '&sort=' . $sort
										. '&query=' . $query . '&order=' . $order . '&subroom=True'));

	$t->parse('out','room_list_t',True);
	$t->p('out');

	$phpgw->common->phpgw_footer();
?>
