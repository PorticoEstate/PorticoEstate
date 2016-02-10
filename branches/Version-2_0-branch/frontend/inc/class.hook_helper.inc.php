<?php
	/**
	 * rental - Hook helper
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2010 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package rental
	 * @version $Id$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */


	phpgw::import_class('frontend.bofrontend');
	phpgw::import_class('frontend.bofellesdata');

	/**
	 * Hook helper
	 *
	 * @package rental
	 */
	class frontend_hook_helper
	{
		/**
		 * Create useraccount on login for SSO/ntlm
		 *
		 * @return void
		 */
		public function auto_addaccount()
		{
			$account_lid = $GLOBALS['hook_values']['account_lid'];

			if(!$GLOBALS['phpgw']->accounts->exists($account_lid))
			{
				$config = CreateObject('phpgwapi.config', 'frontend');
				$config->read();
				$autocreate_user = isset($config->config_data['autocreate_user']) && $config->config_data['autocreate_user'] ? $config->config_data['autocreate_user'] : 0;

				if($autocreate_user)
				{
					$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($account_lid);
					if($fellesdata_user)
					{
						// Read default assign-to-group from config
						$default_group_id = isset($config->config_data['frontend_default_group']) && $config->config_data['frontend_default_group'] ? $config->config_data['frontend_default_group'] : 0;
						$group_lid = $GLOBALS['phpgw']->accounts->name2id($default_group_id);
						$group_lid = $group_lid ? $group_lid : 'frontend_delegates';

						$password = 'PEre' . mt_rand(100,mt_getrandmax ()) . '&';
						$account_id = frontend_bofrontend::create_delegate_account($account_lid, $fellesdata_user['firstname'], $fellesdata_user['lastname'], $password, $group_lid);
						if($account_id)
						{
							$GLOBALS['phpgw']->redirect_link('/login.php', array());
						}
					}
				}
			}
		}
	}
