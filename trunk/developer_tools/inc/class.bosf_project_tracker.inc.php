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

	class bosf_project_tracker
	{
		var $so;
		var $public_functions = array(
			'preferences' => True
		);

		function display_tracker()
		{
			$group_id = $GLOBALS['phpgw_info']['user']['preferences']['developer_tools']['sf_project_id'];

			if (! $group_id)
			{
				return lang('You need to set your preferences for this app');
			}

			$this->so        = createobject('developer_tools.sosf_project_tracker',$group_id);
			$cache_timestamp = $this->so->grab_cache_time();

			if ($cache_timestamp)
			{
				$last_cache = (time() - $cache_timestamp);
			}
			else
			{
				$last_cache = 601;
			}

			// This is hard coded for 10 minutes cache, it will be a config option in the future
			if ($last_cache > 600)
			{
				$data = $this->so->grab_tracker_from_http();
			}
			else
			{
				$data = $this->so->grab_tracker_from_db();
			}

			return $data;
		}

		function preferences()
		{
			$preferences = get_var('preferences',Array('POST'));

			$ui =	createobject('developer_tools.uisf_project_tracker');
			while (is_array($preferences) && list($preference) = each($preferences))
			{
				$GLOBALS['phpgw']->preferences->add('developer_tools',$preference,$preferences[$preference]);
			}
			$GLOBALS['phpgw']->preferences->save_repository();

			$ui->preferences(lang('Preferences have been updated'));
		}
	}
