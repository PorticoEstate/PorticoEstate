<?php
  /**************************************************************************\
  * phpGroupWare - Polls                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id: admin.php 15849 2005-04-18 08:42:42Z powerstat $ */

	$GLOBALS['phpgw_info'] = Array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header'            => True
	);
	include('../header.inc.php');

 	$show  = get_var('show',Array('GET'));
 	$order = get_var('order',Array('GET'));
 	$sort  = get_var('sort',Array('GET'));

	if(!$show)
	{
		$GLOBALS['phpgw']->common->phpgw_exit(True);
	}

	if($order)
	{
		$ordermethod = " order by $order $sort";
	}

	if($show == 'questions')
	{
		$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin_list_questions.tpl'));
	}
	else
	{
		$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin_list_answers.tpl'));
	}
	$GLOBALS['phpgw']->template->set_block('admin','form','form');
	$GLOBALS['phpgw']->template->set_block('admin','row','row');

	$GLOBALS['phpgw']->template->set_unknowns('remove');

	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('sort_title',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'poll_title',$order,'admin.php',lang('Title'),'&show=' . $show));
	if($show == 'answers')
	{
		$GLOBALS['phpgw']->template->set_var('sort_answer',$GLOBALS['phpgw']->nextmatchs->show_sort_order($sort,'option_text',$order,'admin.php',lang('Answer'),'&show=' . $show));
	}

	$GLOBALS['phpgw']->template->set_var('lang_edit',lang('edit'));
	$GLOBALS['phpgw']->template->set_var('lang_delete',lang('delete'));
	if($show == 'questions')
	{
		$GLOBALS['phpgw']->template->set_var('lang_view',lang('view'));
	}

	if($show == 'questions')
	{
		$GLOBALS['phpgw']->db->query("select * from phpgw_polls_desc $ordermethod",__LINE__,__FILE__);
	}
	else
	{
		$GLOBALS['phpgw']->db->query("select phpgw_polls_data.*, phpgw_polls_desc.poll_title from phpgw_polls_data,"
			. "phpgw_polls_desc where phpgw_polls_desc.poll_id = phpgw_polls_data.poll_id $ordermethod",__LINE__,__FILE__);
	}
	$GLOBALS['phpgw']->template->set_var('rows','');
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$tr_color = $GLOBALS['phpgw']->nextmatchs->alternate_row_color($tr_color);
		$GLOBALS['phpgw']->template->set_var('tr_color',$tr_color);

		if($show == 'questions')
		{
			$GLOBALS['phpgw']->template->set_var('row_title',stripslashes($GLOBALS['phpgw']->db->f('poll_title')));
			$GLOBALS['phpgw']->template->set_var('row_edit','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_editquestion.php','poll_id=' . $GLOBALS['phpgw']->db->f('poll_id')) . '">' . lang('Edit') . '</a>');
			$GLOBALS['phpgw']->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_deletequestion.php','poll_id=' . $GLOBALS['phpgw']->db->f('poll_id')) . '">' . lang('Delete') . '</a>');
			$GLOBALS['phpgw']->template->set_var('row_view','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_viewquestion.php','poll_id=' . $GLOBALS['phpgw']->db->f('poll_id')) . '">' . lang('View') . '</a>');
		}
		else
		{
			$GLOBALS['phpgw']->template->set_var('row_answer',stripslashes($GLOBALS['phpgw']->db->f('option_text')));
			$GLOBALS['phpgw']->template->set_var('row_title',stripslashes($GLOBALS['phpgw']->db->f('poll_title')));
			$GLOBALS['phpgw']->template->set_var('row_edit','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_editanswer.php','vote_id=' . $GLOBALS['phpgw']->db->f('vote_id')) . '">' . lang('Edit') . '</a>');
			$GLOBALS['phpgw']->template->set_var('row_delete','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_deleteanswer.php','vote_id=' . $GLOBALS['phpgw']->db->f('vote_id')) . '">' . lang('Delete') . '</a>');
		}
		$GLOBALS['phpgw']->template->parse('rows','row',True);
	}

	$GLOBALS['phpgw']->template->set_var('add_action',$GLOBALS['phpgw']->link('/polls/admin_add' . substr($show,0,(strlen($show)-1)) . '.php'));
	$GLOBALS['phpgw']->template->set_var('lang_add',lang('add'));

	$GLOBALS['phpgw']->template->pparse('out','form');

	$GLOBALS['phpgw']->common->phpgw_footer();
?>
