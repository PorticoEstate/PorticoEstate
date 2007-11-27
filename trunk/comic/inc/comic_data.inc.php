<?php
  /**************************************************************************\
  * phpGroupWare - Daily Comic Data Functions                                *
  * http://www.phpgroupware.org                                              *
  * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: comic_data.inc.php 15277 2004-07-20 23:51:55Z skwashd $ */

	function get_db_var($field)
	{
		$new_var = $GLOBALS['phpgw']->db->f($field, True);
		if(!$new_var)
		{
			$new_var = '&nbsp;';
		}
		return $new_var;
	}

	function comic_table($order, $sort, $filter, $start, $query, $qfield, &$table_c)
	{
		$edit_label   = lang('Edit');
		$delete_label = lang('Delete');
		$searchobj = Array(
			Array(
				'data_title',
				'Title'
			),
			Array(
				'data_class',
				'Genre'
			),
			Array(
				'data_censorlvl',
				'Rated'
			),
			Array(
				'data_parser',
				'Parser'
			),
			Array(
				'data_resolve',
				'Resolve'
			)
		);
                       
		if(!$sort)
		{
			$sort = 'desc';
		}
    
		if($order)
		{
			$ordermethod = 'ORDER BY ' . $GLOBALS['phpgw']->db->db_addslashes($order) 
					. ' ' .  $GLOBALS['phpgw']->db->db_addslashes($sort);
		}
		else
		{
			$ordermethod = "ORDER by data_title asc ";
		}
    
		if(!$start)
		{
			$start = 0;
		}
    
		if(!$filter)
		{
			$filter = 'none';
		}

		if(!$qfield)
		{
			$qfield = 'data_title';
		}
    
		$likeness = 'LIKE';
		$myquery  = '%' . $GLOBALS['phpgw']->db->db_addslashes($query) . '%';
    
		if ($qfield == 'data_censorlvl')
		{
			while(list($key,$value) = each($GLOBALS['g_censor_level']))
			{
				if(ucwords($query) == $value)
				{
					$myquery = $key;
					$likeness = '=';
					break;
				}
			}
		}
    
		$sql_query = 'SELECT COUNT(*) FROM phpgw_comic_data';

		$GLOBALS['phpgw']->db->query($sql_query,__LINE__,__FILE__);
    
		$GLOBALS['phpgw']->db->next_record();

		$total_records = $GLOBALS['phpgw']->db->f(0);

		if ($total_records > $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'])
		{
			if($start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > $total_records)
			{
				$max_turn = $total_records;
			}
			else
			{
				$max_turn = $start + $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			$match_comment = lang('showing %1 - %2 of %3',($start + 1),$max_turn,$total_records);
		}
		else
		{
			$match_comment = lang('showing %1',$total_records);
		}
    
		if(!$query)
		{
			$sql_query = 'SELECT * FROM phpgw_comic_data '.$ordermethod;
		}
		else
		{
			$sql_query = 'SELECT * FROM phpgw_comic_data WHERE '.$qfield.' '.$likeness." '$myquery' ".$ordermethod;
		}

		$GLOBALS['phpgw']->db->limit_query($sql_query,intval($start),__LINE__,__FILE__);
    
		$table_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
		$table_tpl->set_unknowns('remove');
		$table_tpl->set_file(
			Array(
				'table' => 'table.comics.tpl',
				'row'   => 'row.comics.tpl'
			)
		);
    
		while ($GLOBALS['phpgw']->db->next_record()) 
		{
			$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
        
			$comic_id = $GLOBALS['phpgw']->db->f('data_id');
			$comic_encoded = urlencode($comic_id);
        
			$comic_censor = $GLOBALS['g_censor_level'][$GLOBALS['phpgw']->db->f('data_censorlvl')];
        
			$table_tpl->set_var(
				Array(
					'row_color'    => $tr_color,
					'comic_name'   => get_db_var('data_title'),
					'comic_parser' => get_db_var('data_parser'),
					'comic_resolve'=> get_db_var('data_resolve'),
					'comic_class'  => get_db_var('data_class'),
					'comic_censor' => $comic_censor,
					'edit_url'     => $GLOBALS['phpgw']->link('/comic/admin_comics.php',
							Array(
								'con'    => $comic_encoded,
								'act'    => 'edit',
								'start'  => $start,
								'order'  => $order,
								'filter' => $filter,
								'sort'   => $sort,
								'query'  => urlencode($query),
								'qfield' => $qfield
							)
						),
					'edit_label'   => $edit_label,
					'delete_url'   => $GLOBALS['phpgw']->link('/comic/admin_comics.php',
							Array(
								'con'    => $comic_encoded,
								'act'    => 'delete',
								'start'  => $start,
								'order'  => $order,
								'filter' => $filter,
								'sort'   => $sort,
								'query'  => urlencode($query),
								'qfield' => $qfield
							)
						),
					'delete_label' => $delete_label
				)
			);
			$table_tpl->parse(comic_rows, 'row', True);
		}
    
		$table_tpl->set_var(
			Array(
				'th_bg'                => $GLOBALS['phpgw_info']['theme']['th_bg'],
				'total_matchs'         => $match_comment,
				'next_matchs'          => $GLOBALS['phpgw']->nextmatchs->show_tpl('/comic/admin_comics.php',$start,$total_records, '','85%', $GLOBALS['phpgw_info']['theme']['th_bg'],$searchobj,0),
				'comic_label'          => $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'data_title',$order,'/comic/admin_comics.php',lang('Title')),
				'comic_parser_label'   => $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'data_parser',$order,'/comic/admin_comics.php',lang('Parser')),
				'comic_resolve_label'  => $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'data_resolve',$order,'/comic/admin_comics.php',lang('Resolve')),
				'comic_class_label'    => $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'data_class',$order,'/comic/admin_comics.php',lang('Genre')),
				'comic_censor_label'   => $GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'data_censorlvl',$order,'/comic/admin_comics.php',lang('Rated')),
				'edit_label'           => $edit_label,
				'delete_label'         => $delete_label,
				'action_url'           => $action_url,
				'action_label'         => lang($act),
				'reset_label'          => lang('Reset')
			)
		);

		$table_tpl->parse(table_part, 'table');
		$table_c = $table_tpl->get('table_part');
	}

	function comic_entry($con, $act, $order, $sort, $filter, $start, $query, $qfield, &$form_c)
	{
		$action_url   = $GLOBALS['phpgw']->link('/comic/admin_comics.php',
			Array(
				'act'     => $act,
				'start'   => $start,
				'order'   => $order,
				'filter'  => $filter,
				'sort'    => $sort,
				'query'   => urlencode($query),
				'qfield'  => $qfield
			)
		);
    
		switch($act)
		{
			case 'add':
				$bg_color = $GLOBALS['phpgw_info']['theme']['th_bg'];
				break;
			case 'delete':
				$bg_color = $GLOBALS['phpgw_info']['theme']['bg07'];
				break;
			default:
				$bg_color = $GLOBALS['phpgw_info']['theme']['table_bg'];
				break;
		}

		$comic_name = '';
    
		if( intval($con) )
		{
			$GLOBALS['phpgw']->db->query('SELECT * FROM phpgw_comic_data '
						. ' WHERE data_id=' . intval($con), __LINE__, __FILE__ );

			$GLOBALS['phpgw']->db->next_record();

			$comic_name = $GLOBALS['phpgw']->db->f('data_title', True);
		}
        
		$modify_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
		$modify_tpl->set_unknowns('remove');
		$modify_tpl->set_file(form, 'form.comics.tpl');
    
		$modify_tpl->set_var(
			Array(
				'bg_color'	=> $bg_color,
				'data_id'	=> $con,
				'comic_label'	=> lang('Title'),
				'comic_name'	=> $comic_name,
				'action_url'	=> $action_url,
				'action_label'	=> lang($act),
				'reset_label'	=> lang('Reset')
			)
		);
    
		$modify_tpl->parse(form_part, 'form');
		$form_c = $modify_tpl->get('form_part');
	}
?>
