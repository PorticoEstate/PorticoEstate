<?php
	/**
	 * Addressbook - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package addressbook 
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


	/**
	 * Menus
	 *
	 * @package addressbook
	 */	
	class admin_menu
	{
		/**
		 * Get the menus for the addressbook
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'admin'	=> array
				(
					'text'	=> lang('Administration'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'admin.uimainscreen.mainscreen') ),
					'image'	=> array('admin', 'navbar'),
					'order'	=> -5,
					'group'	=> 'systools'
				)
			);

			$menus['admin'] = array();
			if (! $GLOBALS['phpgw']->acl->check('site_config_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['index'] = array
				(
					'text'	=> lang('Site Configuration'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'admin') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['users'] = array
				(
					'text'	=> lang('User Accounts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_users') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['groups'] = array
				(
					'text'	=> lang('User Groups'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.list_groups') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('applications_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['apps'] = array
				(
					'text'	=> lang('Applications'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiapplications.get_list') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('global_categories_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['categories'] = array
				(
					'text'	=> lang('Global Categories'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['addressmasters'] = array
				(
					'text'	=> lang('addressmasters'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaclmanager.list_addressmasters', 'account_id' => $GLOBALS['phpgw_info']['user']['account_id']) )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('custom_fields_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['list_functions'] = array
				(
					'text'	=> lang('custom functions'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' => 'tts') )
				);
			}

			if (!$GLOBALS['phpgw']->acl->check('mainscreen_message_access', PHPGW_ACL_READ, 'admin') || !$GLOBALS['phpgw']->acl->check('mainscreen_message_access',2,'admin'))
			{
				$menus['admin']['mainscreen'] = array
				(
					'text'	=> lang('Change Main Screen Message'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uimainscreen.index') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('current_sessions_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['sessions'] = array
				(
					'text'	=> lang('View Sessions'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicurrentsessions.list_sessions') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('access_log_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['access_log'] = array
				(
					'text'	=> lang('View Access Log'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccess_history.list_history') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('error_log_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['error_log'] = array
				(
					'text'	=> lang('View Error Log'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uilog.list_log') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('error_log_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['log_levels'] = array
				(
					'text'	=> lang('Edit Log Levels'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiloglevels.edit_log_levels') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('applications_access', PHPGW_ACL_PRIVATE, 'admin'))
			{
				$menus['admin']['hooks'] = array
				(
					'text'	=> lang('Find and Register all Application Hooks'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiapplications.register_all_hooks') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('asyncservice_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['async'] = array
				(
					'text'	=> lang('Asynchronous timed services'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiasyncservice.index') )
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('info_access', PHPGW_ACL_READ, 'admin'))
			{
				$menus['admin']['phpinfo'] = array
				(
					'text'	=> lang('PHP Configuration'),
					// degrade gracefully hack
					'url'	=> $GLOBALS['phpgw']->link('/admin/phpinfo.php') . '" onclick="window.open(\'' . $GLOBALS['phpgw']->link('/admin/phpinfo.php', array('noheader' => 1)) . '\'); return false;"',
				);
			}


			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array();
			}

			$menus['toolbar'] = array();
			if ( $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_ADD, 'admin') || $GLOBALS['phpgw']->acl->check('account_access', PHPGW_ACL_PRIVATE, 'admin') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> lang('Add User'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_account', 'account_id' => 0) ),
					'image'	=> array('admin', 'user')
				);
			}
			
			if ( $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_ADD, 'admin') || $GLOBALS['phpgw']->acl->check('group_access', PHPGW_ACL_PRIVATE, 'admin') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> lang('Add Group'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.edit_group', 'account_id' => 0) ),
					'image'	=> array('admin', 'group')
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('info_access', PHPGW_ACL_READ, 'admin') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> lang('phpInfo'),
					// degrade gracefully hack
					'url'	=> $GLOBALS['phpgw']->link('/admin/phpinfo.php') . '" onclick="window.open(\'' . $GLOBALS['phpgw']->link('/admin/phpinfo.php') . '\'); return false;"',
					'image'	=> array('admin', 'php')
				);
			}

			//$menus['navigation'] = $menus['admin'];

			return $menus;
		}
	}
