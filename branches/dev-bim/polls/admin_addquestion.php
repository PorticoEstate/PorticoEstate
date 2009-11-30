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

	if(get_var('submit',Array('POST')))
	{
		$question = get_var('question',Array('POST'));

		$GLOBALS['phpgw']->db->query("insert into phpgw_polls_desc (poll_title,poll_timestamp) values ('"
			. addslashes($question) . "','" . time() . "')",__LINE__,__FILE__);
		$GLOBALS['phpgw']->template->set_var('message',lang('New poll has been added.  You should now add some answers for this poll'));
	}
	else
	{
		$GLOBALS['phpgw']->template->set_var('message','');
	}

	$GLOBALS['phpgw']->template->set_var('header_message',lang('Add new poll question'));
	$GLOBALS['phpgw']->template->set_var('td_message','&nbsp;');
	$GLOBALS['phpgw']->template->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
	$GLOBALS['phpgw']->template->set_var('form_action',$GLOBALS['phpgw']->link('/polls/admin_addquestion.php'));
	$GLOBALS['phpgw']->template->set_var('form_button_1','<input type="submit" name="submit" value="' . lang('Add') . '">');
	$GLOBALS['phpgw']->template->set_var('form_button_2','</form><form method="POST" action="' . $GLOBALS['phpgw']->link('/polls/admin.php') . '"><input type="submit" name="submit" value="' . lang('Cancel') . '">');

	add_template_row($GLOBALS['phpgw']->template,lang('Enter poll question'),'<input name="question">');

	$GLOBALS['phpgw']->template->pparse('out','form');
	$GLOBALS['phpgw']->common->phpgw_footer();
?>
