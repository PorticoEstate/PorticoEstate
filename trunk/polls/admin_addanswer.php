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

	/* $Id$ */

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

	if ($HTTP_POST_VARS['submit'])
	{
 		$poll_id = $HTTP_POST_VARS['poll_id'];
 		$answer  = $HTTP_POST_VARS['answer'];
 		$vote_id = $HTTP_POST_VARS['vote_id'];

		$GLOBALS['phpgw']->db->query("select max(vote_id)+1 from phpgw_polls_data where poll_id='$poll_id'",__LINE__,__FILE__);
		$GLOBALS['phpgw']->db->next_record();
		$vote_id = $GLOBALS['phpgw']->db->f(0);

		$GLOBALS['phpgw']->db->query("insert into phpgw_polls_data (poll_id,option_text,option_count,vote_id) values ('$poll_id','"
			. addslashes($answer) . "',0,'$vote_id')",__LINE__,__FILE__);
		$GLOBALS['phpgw']->template->set_var('message',lang('Answer has been added to poll'));
	}

	$GLOBALS['phpgw']->template->set_var('header_message',lang('Add answer to poll'));
	$GLOBALS['phpgw']->template->set_var('td_message','&nbsp;');
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/polls/admin_addanswer.php'));
	$GLOBALS['phpgw']->template->set_var('form_button_1','<input type="submit" name="submit" value="' . lang('Add') . '">');
	$GLOBALS['phpgw']->template->set_var('form_button_2','</form><form method="POST" action="' . $GLOBALS['phpgw']->link('/polls/admin.php') . '"><input type="submit" name="submit" value="' . lang('Cancel') . '">');

	$poll_select = '<select name="poll_id">';
	$GLOBALS['phpgw']->db->query("select * from phpgw_polls_desc",__LINE__,__FILE__);
	while ($GLOBALS['phpgw']->db->next_record())
	{
		$poll_select .= '<option value="' . $GLOBALS['phpgw']->db->f('poll_id') . '"';
		if ($poll_id == $GLOBALS['phpgw']->db->f('poll_id'))
		{
			$poll_select .= ' selected';
		}
		$poll_select .= '>' . stripslashes($GLOBALS['phpgw']->db->f('poll_title')) . '</option>';
	}
	$poll_select .= '</select>';

	add_template_row($GLOBALS['phpgw']->template,lang('Which poll'),$poll_select);
	add_template_row($GLOBALS['phpgw']->template,lang('Answer'),'<input name="answer">');

	$GLOBALS['phpgw']->template->pfp('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
