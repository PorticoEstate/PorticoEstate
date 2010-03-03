<?php
	/**
	* phpGroupWare - messenger
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package messenger
	* @subpackage ???
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
	 * Description
	 * @package messenger
	 */

	class messenger_menu
	{
		/**
		 * Get the menus for the messenger
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'messenger';
			$acl = & $GLOBALS['phpgw']->acl;

			$menus = array();

			$menus['navbar'] = array
			(
				'messenger' => array
				(
					'text'	=> lang('messenger'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "messenger.uimessenger.inbox") ),
					'image'	=> array('messenger', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> lang('Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'messenger') )
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'messenger') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'messenger', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'messenger') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'messenger')),
					'image'	=> array('messenger', 'preferences')
				);
			}

			$menus['navigation'] = array
			(
				'inbox' => array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.inbox')),
					'text'  => $GLOBALS['phpgw']->translation->translate('inbox', array(), true),
					'image' => array('messenger', 'navbar')
				)
			);
			if ($GLOBALS['phpgw']->acl->check('.compose', PHPGW_ACL_ADD, 'messenger'))
			{
				$menus['navigation']['compose'] = array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.compose')),
					'text'  => $GLOBALS['phpgw']->translation->translate('compose', array(), true),
				);
			}
			if ($GLOBALS['phpgw']->acl->check('.compose_groups', PHPGW_ACL_ADD, 'messenger'))
			{
				$menus['navigation']['compose_groups'] = array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.compose_groups')),
					'text'  => $GLOBALS['phpgw']->translation->translate('compose groups', array(), true),
				);
			}
			if ($GLOBALS['phpgw']->acl->check('.compose_global', PHPGW_ACL_ADD, 'messenger'))
			{	
				$menus['navigation']['compose_global'] = array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'messenger.uimessenger.compose_global')),
					'text'  => $GLOBALS['phpgw']->translation->translate('compose global', array(), true),
				);
			}
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
