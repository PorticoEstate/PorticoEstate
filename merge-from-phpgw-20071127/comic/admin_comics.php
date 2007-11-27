<?php
    /**************************************************************************\
    * phpGroupWare - Daily Comic Admin Link Data                               *
    * http://www.phpgroupware.org                                              *
    * This file written by Sam Wynn <neotexan@wynnsite.com>                    *
    * --------------------------------------------                             *
    *  This program is free software; you can redistribute it and/or modify it *
    *  under the terms of the GNU General Public License as published by the   *
    *  Free Software Foundation; either version 2 of the License, or (at your  *
    *  option) any later version.                                              *
    \**************************************************************************/

    /* $Id: admin_comics.php 15277 2004-07-20 23:51:55Z skwashd $ */

	$phpgw_info['flags'] = Array(
		'currentapp' => 'comic',
		'enable_nextmatchs_class' => True,
		'admin_header' => True
	);

	include('../header.inc.php');
	include('inc/comic_data.inc.php');

	if(!$GLOBALS['phpgw_info']['user']['apps']['comic'])
	{
		Header('Location: '. $GLOBALS['phpgw']->link('/home.php') );
		exit;
	}

	$title             = lang('Daily Comics Data');

	$done_label        = lang('Done');
	$doneurl           = $GLOBALS['phpgw']->link('/admin/index.php') . '#comic';

	$message           = '';

	$act		= get_var('act', array('POST', 'GET'), '');
	$order		= get_var('order', array('POST', 'GET'), 'data_title');
	$sort		= get_var('sort', array('POST', 'GET'), 'ASC');
	$start		= get_var('start', array('POST', 'GET'), 0);
	$query		= get_var('query', array('POST', 'GET'), '');
	$qfield		= get_var('qfield', array('POST', 'GET'), 'data_title');
	$filter		= get_var('filter', array('POST', 'GET'), '');
	$con		= get_var('con', array('POST', 'GET'), 0);
	$submit		= get_var('submit', array('POST'), false);
	$comic_name	= get_var('comic_name', array('POST'), '');
	$data_id	= intval( get_var('data_id', array('POST'), 0) );
    
	if ($submit)
	{
		switch($act)
		{
			case 'edit':
				$message = 'modification';
				break;
			case 'delete':
				$message = 'deletion';
				break;
			case 'add':
				$message = 'addition';
				break;
		}
		$message = lang('Performed %1 of element', lang($message) );
	}

	$other_c           = '';
	
	switch($act)
	{
		case 'edit':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('UPDATE phpgw_comic_data'
						. " SET data_title = '".$GLOBALS['phpgw']->db->db_addslashes($comic_name)."'"
						. " WHERE data_id = $data_id",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();

				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			}
			else
			{
				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				//comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
				comic_entry($con,$act,$order,$sort,$filter,$start,$query,$qfield,$other_c);
			}
			break;
		case 'delete':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('DELETE FROM phpgw_comic_data'
					. " WHERE data_id =  $data_id",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();

				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			}
			else
			{
				comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
				//comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
				comic_entry($con,$act,$order,$sort,$filter,$start,$query,$qfield,$other_c);
			}
			break;
		case 'add':
			if ($submit)
			{
				$GLOBALS['phpgw']->db->lock('phpgw_comic_data');
				$GLOBALS['phpgw']->db->query('INSERT INTO phpgw_comic_data(data_name) '
					. " VALUES ('".$GLOBALS['phpgw']->db->db_addslashes($comic_name)."')",__LINE__,__FILE__);
				$GLOBALS['phpgw']->db->unlock();
			}
			comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
			comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			break;
		default:
			comic_table($order,$sort,$filter,$start,$query,$qfield,$table_c);
			comic_entry('','add',$order,$sort,$filter,$start,$query,$qfield,$add_c);
			break;
	}
    
	$comics_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
	$comics_tpl->set_unknowns('remove');
	$comics_tpl->set_file(
		Array(
			'message'   => 'message.common.tpl',
			'comics'    => 'admin.datalist.tpl'
		)
	);
	$comics_tpl->set_var(
		Array(
			'messagename'      => $message,
			'title'            => $title,
			'done_url'         => $doneurl,
			'done_label'       => $done_label,
			'data_table'       => $table_c,
			'add_form'         => $add_c,
			'other_form'       => $other_c
		)
	);

	$comics_tpl->parse('message_part','message');
	$message_c = $comics_tpl->get('message_part');

	$comics_tpl->parse('body_part','comics');
	$body_c = $comics_tpl->get('body_part');
    
    /**************************************************************************
     * pull it all together
     *************************************************************************/
	$body_tpl = CreateObject('phpgwapi.Template',$GLOBALS['phpgw']->common->get_tpl_dir('comic'));
	$body_tpl->set_unknowns('remove');
	$body_tpl->set_file('body','admin.common.tpl');
	$body_tpl->set_var(
		Array(
			'admin_message' => $message_c,
			'admin_body'    => $body_c
		)
	);
	$body_tpl->parse('BODY','body');
	$body_tpl->p('BODY');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
