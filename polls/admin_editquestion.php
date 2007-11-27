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

  /* $Id: admin_editquestion.php 17907 2007-01-24 16:51:08Z Caeies $ */

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header'            => True
	);
	include('../header.inc.php');

	$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin_form.tpl'));
	$GLOBALS['phpgw']->template->set_block('admin','form','form');
	$GLOBALS['phpgw']->template->set_block('admin','row','row');

	$poll_id = $HTTP_GET_VARS['poll_id'] ? $HTTP_GET_VARS['poll_id'] : $HTTP_POST_VARS['poll_id'];

	if ($HTTP_POST_VARS['edit'])
	{
		$question = $HTTP_POST_VARS['question'];
		$GLOBALS['phpgw']->db->query("update phpgw_polls_desc set poll_title='" . addslashes($question)
			. "' where poll_id='$poll_id'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->template->set_var('message',lang('Question has been updated'));
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('message','');
	}

	$GLOBALS['phpgw']->db->query("select * from phpgw_polls_desc where poll_id='$poll_id'",__LINE__,__FILE__);
	$GLOBALS['phpgw']->db->next_record();

	$GLOBALS['phpgw']->template->set_var('header_message',lang('Edit poll question'));
	$GLOBALS['phpgw']->template->set_var('td_message','&nbsp;');
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('poll_id',$poll_id);
	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/polls/admin_editquestion.php','poll_id=' . $poll_id));
	$GLOBALS['phpgw']->template->set_var('form_button_1','<input type="submit" name="edit" value="' . lang('Edit') . '">');
	$GLOBALS['phpgw']->template->set_var('form_button_2','</form><form method="POST" action="' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'questions'))
		. '"><input type="submit" name="submit" value="' . lang('Cancel') . '">'
	);

	add_template_row($GLOBALS['phpgw']->template,lang('Poll question'),'<input name="question" value="' . stripslashes($GLOBALS['phpgw']->db->f('poll_title')) . '">');

	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
