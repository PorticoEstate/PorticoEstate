<?php
	/**
	 * preferences - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package preferences
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
	 * @package preferences
	 */
	class preferences_menu
	{
		/**
		 * Get the menus for the preferences
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'preferences' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php'),
					'image'	=> array('preferences', 'navbar'),
					'order'	=> 0,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array();

			$menus['navigation'] = array();
			$menus['navigation'][] = array
			(
				'text'	=> $GLOBALS['phpgw']->translation->translate('My Preferences', array(), true),
				'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'preferences')),
				'image'	=> array('preferences', 'preferences')
			);

			if ( !$GLOBALS['phpgw']->acl->check('changepassword', phpgwapi_acl::READ, 'preferences') )
			{
				$menus['navigation'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Change your Password', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/changepassword.php')
				);
			}
			if ( (isset($GLOBALS['phpgw_info']['server']['auth_type'])
					&& $GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser')
				|| (isset($GLOBALS['phpgw_info']['server']['half_remote_user'])
					&& $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser') )
			{
				if ( $GLOBALS['phpgw_info']['server']['mapping'] == 'table'
					|| $GLOBALS['phpgw_info']['server']['mapping'] == 'all' )
				{
					$menus['navigation'][] = array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Mapping', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array
									(
										'menuaction' => 'preferences.uimapping.index',
										'appname' => 'preferences'
									))
					);
				}
			}

			if ( isset($navbar['admin']) )
			{
				$menus['navigation'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Default Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php', array('type' => 'default'))
				);
				$menus['navigation'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Forced Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/index.php', array('type' => 'forced'))
				);
			}

			$menus['preferences'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php',
									array('appname'	=> 'preferences')),
					'image'	=> array('preferences', 'preferences')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Change your Password', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/changepassword.php')
				)
			);

			return $menus;
		}
	}
