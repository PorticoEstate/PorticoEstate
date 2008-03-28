<?php
	/**
	* Shared functions for other account repository managers and loader
	* @author coreteam <phpgroupware-developers@gnu.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v3 or later
	* @package admin
	* @subpackage accounts
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	class admin_soaccounts
	{
		function add_user($userData)
		{
			$GLOBALS['phpgw']->db->lock
			(
				array
				(
					'phpgw_accounts',
					'phpgw_nextid',
					'phpgw_preferences',
					'phpgw_sessions',
					'phpgw_acl',
					'phpgw_applications',
					'phpgw_app_sessions',
					'phpgw_hooks'
				)
			);

			$userData['account_id'] = $GLOBALS['phpgw']->accounts->create($userData);

			$apps =& CreateObject('phpgwapi.applications',array($userData['account_id'],'u'));
			$apps->read_installed_apps();

			// Read Group Apps
			if ($userData['account_groups'])
			{
				$apps->account_type = 'g';
				reset($userData['account_groups']);
				while($groups = each($userData['account_groups']))
				{
					$apps->account_id = $groups[0];
					$old_app_groups = $apps->read_account_specific();
					@reset($old_app_groups);
					while($old_group_app = each($old_app_groups))
					{
						if (!$apps_after[$old_group_app[0]])
						{
							$apps_after[$old_group_app[0]] = $old_app_groups[$old_group_app[0]];
						}
					}
				}
			}

			$apps->account_type = 'u';
			$apps->set_account_id($userData['account_id']);
			$apps->account_apps = Array(Array());

/* moved to bo
			if ($userData['account_permissions'])
			{
				@reset($userData['account_permissions']);
				while (list($app,$turned_on) = each($userData['account_permissions']))
				{
					if ($turned_on)
					{
						$apps->add($app);
						if (!$apps_after[$app])
						{
							$apps_after[] = $app;
						}
					}
				}
			}
			$apps->save_repository();
*/

			$GLOBALS['phpgw']->acl->add_repository('preferences','changepassword',$userData['account_id'],1);

			// Assign user to groups
			if ($userData['groups'])
			{
				for ($i=0; $i < count($userData['groups']); $i++)
				{
					$GLOBALS['phpgw']->acl->add_repository('phpgw_group',$userData['groups'][$i],$userData['account_id'],1);
				}
			}

/*			if ($apps_after)
			{
				$GLOBALS['pref'] =& CreateObject('phpgwapi.preferences',$userData['account_id']);
				$GLOBALS['phpgw']->hooks->single('add_def_pref','admin');
				while ($apps = each($apps_after))
				{
					if (strcasecmp ($apps[0], 'admin') != 0)
					{
						$GLOBALS['phpgw']->hooks->single('add_def_pref', $apps[1]);
					}
				}
				$GLOBALS['pref']->save_repository(False);
			} */

			$apps->account_apps = array(array());
			$apps_after = array(array());

			$GLOBALS['phpgw']->db->unlock();

/*
			// start inlcuding other admin tools
			while($app = each($apps_after))
			{
				$GLOBALS['phpgw']->hooks->single('add_user_data', $value);
			}
*/
			return $userData['account_id'];
		}
	}
