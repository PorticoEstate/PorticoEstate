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

	$phpgw_info = array();
	$GLOBALS['phpgw_info']['flags'] = array(
		'admin_only'              => True,
		'currentapp'              => 'polls',
		'enable_nextmatchs_class' => True,
		'admin_header'            => True
	);
	include('../header.inc.php');

	$poll_id = $HTTP_GET_VARS['poll_id'];

	$GLOBALS['phpgw']->template->set_file(array('admin' => 'admin_form.tpl'));
	$GLOBALS['phpgw']->template->set_block('admin','form','form');
	$GLOBALS['phpgw']->template->set_block('admin','row','row');

	$GLOBALS['phpgw']->db->query("select poll_title from phpgw_polls_desc where poll_id='$poll_id'");
	$GLOBALS['phpgw']->db->next_record();
	$poll_title = stripslashes($GLOBALS['phpgw']->db->f('poll_title'));

	$GLOBALS['phpgw']->template->set_var('message','');
	$GLOBALS['phpgw']->template->set_var('header_message',lang('View poll'));
	$GLOBALS['phpgw']->template->set_var('td_message','&nbsp;');
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/polls/admin_editquestion.php'));
	$GLOBALS['phpgw']->template->set_var('poll_id',$poll_id);
	$GLOBALS['phpgw']->template->set_var('form_button_1','<input type="submit" name="submit" value="' . lang('Edit') . '">');
	$GLOBALS['phpgw']->template->set_var('form_button_2','</form><form method="POST" action="'
		. $GLOBALS['phpgw']->link("/polls/admin_deletequestion.php","poll_id=$poll_id") . '"><input type="submit" name="submit" value="' . lang('Delete') . '">');

	add_template_row($GLOBALS['phpgw']->template,lang('Poll question'),$GLOBALS['phpgw']->strip_html($poll_title));

	$GLOBALS['phpgw']->db->query("select * from phpgw_polls_data where poll_id='$poll_id'");
	while ($GLOBALS['phpgw']->db->next_record())
	{
		if (! $title_shown)
		{
			$title = lang('Answers');
			$title_shown = True;
		}
		add_template_row($GLOBALS['phpgw']->template,$title,$GLOBALS['phpgw']->strip_html($GLOBALS['phpgw']->db->f('option_text')));
		$title = '&nbsp;';
	}

	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
