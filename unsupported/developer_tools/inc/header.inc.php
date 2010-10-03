<?php
	/**************************************************************************\
	* phpGroupWare - Developer Tools                                           *
	* http://www.phpgroupware.org                                              *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	$GLOBALS['phpgw']->template->set_root(PHPGW_APP_TPL);
	$GLOBALS['phpgw']->template->set_file(array('header' => 'header.tpl'));

	$GLOBALS['phpgw']->template->set_var('lang_developer_tools',lang('Developer tools'));
	$GLOBALS['phpgw']->template->set_var('link_diary',lang('Diary'));
	/*
	$GLOBALS['phpgw']->template->set_var('link_sourceforge_project','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uisf_project_tracker.display_tracker')) . '">' . lang('SF Project tracker') . '</a>');
	*/
	$GLOBALS['phpgw']->template->set_var('link_changelog','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'developer_tools.uichangelogs.list_changelogs')) . '">' . lang('Changelogs') . '</a>');
	$GLOBALS['phpgw']->template->set_var('link_language_management','<a href="' . $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'developer_tools.uilangfile.index')) . '">' . lang('Language file management'));
//	$GLOBALS['phpgw']->template->set_var('link_preferences','<a href="' . $GLOBALS['phpgw']->link('/preferences/index.php#developer_tools') . '">' . lang('Preferences') . '</a>');
	$GLOBALS['phpgw']->template->set_var('link_preferences','<a href="' . $GLOBALS['phpgw']->link('/preferences/preferences.php',array('appname'=> 'developer_tools', 'type'=> 'user')) . '">' . lang('Preferences') . '</a>');

	$GLOBALS['phpgw']->template->pfp('out','header');
