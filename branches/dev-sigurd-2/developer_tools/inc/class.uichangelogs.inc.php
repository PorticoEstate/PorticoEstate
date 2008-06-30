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

	class uichangelogs
	{
		var $bo;
		var $cat;
		var $template;
		var $public_functions = array(
			'list_changelogs' => True,
			'add'             => True,
			'search'          => True,
			'create_sgml'     => True
		);

		function uichangelogs()
		{
			$this->template = $GLOBALS['phpgw']->template;
			$this->bo       = createobject('developer_tools.bochangelogs');
			$this->cat      = createobject('phpgwapi.categories');
		}

		function header()
		{
			$GLOBALS['phpgw']->common->phpgw_header();
			echo parse_navbar();
			include(PHPGW_APP_INC . '/header.inc.php');

			$this->template->set_file('_header','changelog_header.tpl');
			$this->template->set_var('lang_header',lang('Changelogs'));
			$this->template->set_var('lang_list_changelogs','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uichangelogs.list_changelogs')) . '">' . lang('List changelogs') . '</a>');
			$this->template->set_var('lang_add_changelogs','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uichangelogs.add')) . '">' . lang('Add change') . '</a>');
			$this->template->set_var('lang_search_changelogs','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uichangelogs.search')) . '">' . lang('Search changelogs') . '</a>');
			$this->template->set_var('lang_sgml','<a href="' . $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.uichangelogs.create_sgml')) . '">' . lang('Create SGML file') . '</a>');

			$this->template->pfp('out','_header');
		}

		function list_changelogs()
		{
			$this->header();
			echo '<p>&nbsp;</p><p>&nbsp;</p><center><b>Coming soon to a theater near you!</b></center>';
		}

		function add($messages = '',$fields = '')
		{
			$this->header();
			$this->template->set_file('_form','changelog_form.tpl');
			$this->template->set_block('_form','form');

			if ($messages)
			{
				if (is_array($messages))
				{
					$this->template->set_var('messages',$GLOBALS['phpgw']->common->error_list($messages));
				}
				else
				{
					$this->template->set_var('messages',$messages);
				}
			}

			$this->template->set_var('form_action',$GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'developer_tools.bochangelogs.add')));
			$this->template->set_var('lang_project',lang('Project'));
			$this->template->set_var('lang_version',lang('Version'));
			$this->template->set_var('lang_change',lang('Change'));

			$this->template->set_var('value_project','<select name="fields[project]"><option value="">'
					. lang('Select project') . '</option>' . $this->cat->formated_list('select','mains',$fields['project'],True)
					. '</select>');
			$this->template->set_var('value_version','<input name="fields[version]" value="' . $fields['version'] . '">');
			$this->template->set_var('value_change','<input name="fields[change]" value="' . $fields['change'] . '">');
			$this->template->set_var('button_submit','<input type="submit" name="submit" value="' . lang('Add') . '">');

			$this->template->pfp('out','form');
		}

		function search()
		{
			$this->header();
			echo '<p>&nbsp;</p><p>&nbsp;</p><center><b>Coming soon to a theater near you!</b></center>';
		}

		function create_sgml()
		{
			$this->header();
			echo '<p>&nbsp;</p><p>&nbsp;</p><center><b>Coming soon to a theater near you!</b></center>';
		}
	}
