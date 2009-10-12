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

	class bochangelogs
	{
		var $so;
		var $ui;
		var $public_functions = array(
			'list_changelogs' => True,
			'add'             => True,
			'search'          => True,
			'create_sgml'     => True
		);

		function bochangelogs()
		{
			$this->so = createobject('developer_tools.sochangelogs');
		}

		function list_changelogs()
		{
		
		}

		function add()
		{
			$fields = get_var('qfields',Array('POST'));

			$this->ui = createobject('developer_tools.uichangelogs');
			if (! $fields['project'])
			{
				$errors[] = lang('You must select a project');
			}

			if (! $fields['change'])
			{
				$errors[] = lang('You must enter a change');
			}

			if (! $fields['version'])
			{
				$errors[] = lang('You must a version');
			}

			if (is_array($errors))
			{
				$this->ui->add($errors,$fields);
			}
			else
			{
				$this->so->add($fields);

				// This is if they are adding multiable entrys to a project and version
				$_fields['version'] = $fields['version'];
				$_fields['project'] = $fields['project'];
				$this->ui->add(lang('Changelog entry has been added'),$_fields);
			}
		}

		function search()
		{

		}

		function create_sgml()
		{

		}
	}
