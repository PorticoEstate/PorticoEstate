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

	/* $Id: header.inc.php 17907 2007-01-24 16:51:08Z Caeies $ */

	if ($GLOBALS['phpgw_info']['flags']['admin_header'])
	{
		$tpl = $GLOBALS['phpgw']->template;
		$tpl->set_file(array('admin_header' => 'admin_header.tpl'));

		$tpl->set_var('th_bg',$GLOBALS['phpgw_info']['theme']['th_bg']);
		$tpl->set_var('info',lang('Voting booth administration'));
		$tpl->set_var('link_list_questions','<a href="' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'questions')) . '">' . lang('Show questions') . '</a>');
		$tpl->set_var('link_list_answers','<a href="' . $GLOBALS['phpgw']->link('/polls/admin.php', array('show' => 'answers')) . '">' . lang('Show answers') . '</a>');
		$tpl->set_var('link_questions','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_addquestion.php') . '">' . lang('Add questions') . '</a>');
		$tpl->set_var('link_answers','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_addanswer.php') . '">' . lang('Add answers') . '</a>');
		$tpl->set_var('link_settings','<a href="' . $GLOBALS['phpgw']->link('/polls/admin_settings.php') . '">' . lang('Poll settings') . '</a>');

		$tpl->pfp('out','admin_header');
	}
?>
