<?php
	/**
	 * Admin - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 - 2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package addressbook
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


	/**
	 * Menus
	 *
	 * @package admin
	 */
	class admin_menu
	{
		/**
		 * Get the menus for admin
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
					'text'	=> $GLOBALS['phpgw']->translation->translate('Administration', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction'=> 'admin.uimainscreen.mainscreen')),
					'image'	=> array('admin', 'navbar'),
					'order'	=> -5,
					'group'	=> 'systools'
				)
			);

			$menus['admin'] = array();
			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['index'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('global configuration', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiconfig.index', 'appname' => 'admin'))
				);
				$menus['admin']['file_config'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('file configuration', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
						array
						(
							'menuaction' => 'admin.uiconfig2.index',
							'location_id' => $GLOBALS['phpgw']->locations->get_id('admin', 'vfs_filedata')
						)
					)
				);	

				$menus['admin']['file_attribs']	= array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('file attributes', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
						array
						(
							'menuaction'		=> 'admin.ui_custom.list_attribute',
							'appname'			=> 'admin',
							'location'			=> 'vfs_filedata',
							'menu_selection'	=> 'admin::file_attribs'
						)
					)
				);
				$menus['admin']['file_cats']	= array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('file categories', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
						array
						(
							'menuaction'		=> 'admin.uicategories.index',
							'appname'			=> 'admin',
							'location'			=> 'vfs_filedata',
							'menu_selection'	=> 'admin::file_cats'
						)
					)
				);

			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['global_message'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('global message', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccounts.global_message'))
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['users'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('manage users', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccounts.list_users'))
				);
			}

			if (! $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['groups'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('manage groups', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccounts.list_groups'))
				);
			}

/*
			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['clear_user_cache'] = array
				(
					'text'	=> lang('Clear user cache'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.clear_user_cache') )
				);
			}
*/
			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['clear_cache'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('clear cache', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.clear_cache') )
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['sync_account'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Sync Account-Contact', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiaccounts.sync_accounts_contacts') )
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['apps'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Applications', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiapplications.get_list'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['categories'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uicategories.index'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['addressmasters'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('addressmasters', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array
								(
									'menuaction' => 'admin.uiaclmanager.list_addressmasters',
									'account_id' => $GLOBALS['phpgw_info']['user']['account_id']
								))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['mainscreen'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Change Main Screen Message', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uimainscreen.index'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['sessions'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('View Sessions', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uicurrentsessions.list_sessions'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['access_log'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('View Access Log', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccess_history.list_history'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['error_log'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('View Error Log', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uilog.list_log'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['log_levels'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Edit Log Levels', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiloglevels.edit_log_levels'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$text = $GLOBALS['phpgw']->translation->translate('Find and Register all Application Hooks',
						array(), true);

				$menus['admin']['hooks'] = array
				(
					'text'	=> $text,
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiapplications.register_all_hooks'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin']['async'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Asynchronous timed services', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiasyncservice.index'))
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
					&& function_exists('phpinfo') ) // it is possible to disable commands in php.ini
			{
				$menus['admin']['phpinfo'] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('PHP Configuration', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/admin/phpinfo.php')
								. '" onclick="window.open(\''
								. $GLOBALS['phpgw']->link('/admin/phpinfo.php', array('noheader' => 1))
								. '\'); return false;',
				);
			}


			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array();
			}

			$menus['toolbar'] = array();
			if ( $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::ADD, 'admin')
					|| $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::PRIV, 'admin') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Add User', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccounts.edit_account', 'account_id' => 0)),
					'image'	=> array('admin', 'user')
				);
			}

			if ( $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::ADD, 'admin')
				|| $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::PRIV, 'admin') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Add Group', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',
								array('menuaction' => 'admin.uiaccounts.edit_group', 'account_id' => 0)),
					'image'	=> array('admin', 'group')
				);
			}

			if ( !$GLOBALS['phpgw']->acl->check('info_access', phpgwapi_acl::READ, 'admin')
					&& function_exists('phpinfo') )
			{
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('phpInfo', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/admin/phpinfo.php')
								. '" onclick="window.open(\''
								. $GLOBALS['phpgw']->link('/admin/phpinfo.php')
								. '\'); return false;"',
					'image'	=> array('admin', 'php')
				);
			}

			//$menus['navigation'] = $menus['admin'];

			return $menus;
		}
	}
