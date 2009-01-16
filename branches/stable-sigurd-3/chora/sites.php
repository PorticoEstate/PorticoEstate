<?php
  /**************************************************************************\
  * phpGroupWare - Chora CVS Repositories                                    *
  * http://www.phpgroupware.org                                              *
  * Written by Bettina Gille [ceb@phpgroupware.org]                          *
  * -----------------------------------------------                          *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
  /* $Id$ */

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'chora',
		'enable_config_class' => True,
		'enable_nextmatchs_class' => True
	);
	include('../header.inc.php');

	if(!$GLOBALS['phpgw']->acl->check('run',1,'admin'))
	{
		echo lang('access not permitted');
		$GLOBALS['phpgw']->common->phpgw_footer();
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$t = CreateObject('phpgwapi.Template',PHPGW_APP_TPL);
	$t->set_file(array('site_list_t' => 'listsites.tpl'));
	$t->set_block('site_list_t','site_list','list');

	$common_hidden_vars =
		  '<input type="hidden" name="sort"   value="' . $sort   . '">' . "\n"
		. '<input type="hidden" name="order"  value="' . $order  . '">' . "\n"
		. '<input type="hidden" name="query"  value="' . $query  . '">' . "\n"
		. '<input type="hidden" name="start"  value="' . $start  . '">' . "\n"
		. '<input type="hidden" name="filter" value="' . $filter . '">' . "\n";

	$t->set_var('lang_action',lang('Repository List'));
	$t->set_var('add_action',$GLOBALS['phpgw']->link('/chora/addsite.php'));
	$t->set_var('lang_add',lang('Add'));
	$t->set_var('title_sites',lang('CVS Repositories'));
	$t->set_var('lang_search',lang('Search'));
	$t->set_var('actionurl',$GLOBALS['phpgw']->link('/chora/sites.php'));
	$t->set_var('lang_done',lang('Done'));
	$t->set_var('doneurl',$GLOBALS['phpgw']->link('/admin/index.php'));

	if(!$start)
	{
		$start = 0;
	}

	if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] &&
		$GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
	{
		$limit = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
	}
	else
	{
		$limit = 15;
	}

	if(!$sort)
	{
		$sort = 'ASC';
	}
	if($order)
	{
		$ordermethod = "ORDER BY $order $sort ";
	}
	else
	{
		$ordermethod = ' ORDER BY name ASC ';
	}

	if($query)
	{
		$querymethod = " WHERE name LIKE '%$query%' OR title LIKE '%$query%'";
	}

	$db2 = $GLOBALS['phpgw']->db;

	$sql = "SELECT * FROM phpgw_chora_sites $querymethod $ordermethod";
	$db2->query($sql,__LINE__,__FILE__);
	$total_records = $db2->num_rows();
	$GLOBALS['phpgw']->db->limit_query($sql,$start,__LINE__,__FILE__);
	while($GLOBALS['phpgw']->db->next_record())
	{
		$sites[] = array(
			'id'          => $GLOBALS['phpgw']->db->f('id'),
			'name'        => $GLOBALS['phpgw']->db->f('name'),
			'location'    => $GLOBALS['phpgw']->db->f('location'),
			'title'       => $GLOBALS['phpgw']->db->f('title'),
			'is_default'  => $GLOBALS['phpgw']->db->f('is_default')
		);
	}

	//--------------------------------- nextmatch --------------------------------------------
	$left = $GLOBALS['phpgw']->nextmatchs->left('/chora/sites.php',$start,$total_records);
	$right = $GLOBALS['phpgw']->nextmatchs->right('/chora/sites.php',$start,$total_records);
	$t->set_var('left',$left);
	$t->set_var('right',$right);

	if($total_records > $limit)
	{
		$t->set_var('lang_showing',lang('showing %1 - %2 of %3',($start + 1),($start + $limit),$total_records));
	}
	else
	{
		$t->set_var('lang_showing',lang('showing %1',$total_records));
	}
	// ------------------------------ end nextmatch ------------------------------------------

	//------------------- list header variable template-declarations -------------------------
	$t->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$t->set_var('sort_name',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'name',$order,'/chora/sites.php',lang('Name')));
	$t->set_var('sort_title',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'title',$order,'/chora/sites.php',lang('Title')));
	$t->set_var('lang_default',lang('Default'));
	$t->set_var('lang_edit',lang('Edit'));
	$t->set_var('lang_delete',lang('Delete'));
	// -------------------------- end header declaration --------------------------------------

	for($i=0;$i<count($sites);$i++)
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		$t->set_var(tr_color,$tr_color);
		$site_id = $sites[$i]['id'];
		$is_default = $sites[$i]['is_default'];
		$site_name  = $GLOBALS['phpgw']->strip_html($sites[$i]['name']);
		$site_title = $GLOBALS['phpgw']->strip_html($sites[$i]['title']);
		if(!$site_title)
		{
			$site_title= '&nbsp;';
		}

		//-------------------------- template declaration for list records ---------------------------
		$t->set_var(array(
			'site_name'  => $site_name,
			'site_title' => $site_title
		));
		if($is_default)
		{
			$t->set_var('is_default',lang('Yes'));
		}
		else
		{
			$t->set_var('is_default',lang('No'));
		}

		$t->set_var('edit',$GLOBALS['phpgw']->link('/chora/editsite.php',"site_id=$site_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
		$t->set_var('lang_edit_entry',lang('Edit'));

		$t->set_var('delete',$GLOBALS['phpgw']->link('/chora/deletesite.php',"site_id=$site_id&start=$start&query=$query&sort=$sort&order=$order&filter=$filter"));
		$t->set_var('lang_delete_entry',lang('Delete'));
		$t->parse('list','site_list',True);
	}
// ---------------------------- end record declaration -----------------------------------------

	$t->parse('out','site_list_t',True);
	$t->p('out');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
