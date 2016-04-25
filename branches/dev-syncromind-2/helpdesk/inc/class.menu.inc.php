<?php
	/**
	 * helpdesk - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2007,2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package helpdesk
	 * @version $Id: class.menu.inc.php 6711 2010-12-28 15:15:42Z sigurdne $
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
	 * @package helpdesk
	 */
	class helpdesk_menu
	{
		/**
		 * Get the menus for the helpdesk
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu($type='')
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'helpdesk';
			$acl = & $GLOBALS['phpgw']->acl;
			$menus = array();


			$menus['navbar'] = array
				(
					'helpdesk' => array
					(
						'text'	=> lang('helpdesk'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "helpdesk.uitts.index") ),
						'image'	=> array('helpdesk', 'navbar'),
						'order'	=> 35,
						'group'	=> 'facilities management'
					),
				);

			$menus['toolbar'] = array();


			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'helpdesk'))
			{

				$menus['admin'] = array
					(
						'index'	=> array
						(
							'text'	=> lang('Configuration'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'helpdesk') )
						),
						'ticket_cats'	=> array
						(
							'text'	=> lang('Ticket Categories'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'helpdesk', 'location' => '.ticket', 'global_cats' => 'true', 'menu_selection' => 'admin::helpdesk::ticket_cats') )
						),
						'ticket_status'	=> array
						(
							'text'	=> lang('Ticket status'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigeneric.index', 'type' => 'helpdesk_status') )
						),
						'acl'	=> array
						(
							'text'	=> lang('Configure Access Permissions'),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'helpdesk') )
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
							'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'helpdesk', 'type'=> 'user') )
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'helpdesk.uiadmin.aclprefs', 'acl_app'=> 'helpdesk'))
						)
					);

				$menus['toolbar'][] = array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'helpdesk')),
						'image'	=> array('helpdesk', 'preferences')
					);
			}

			$menus['navigation'] = array();


			if ( $acl->check('.ticket',PHPGW_ACL_READ, 'helpdesk') )
			{
				$menus['navigation']['helpdesk'] = array
					(
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'helpdesk.uitts.index')),
						'text'	=> lang('inbox'),
						'image'		=> array('helpdesk', 'helpdesk')
					);
			}

			if ( $acl->check('.ticket.order',PHPGW_ACL_ADD, 'helpdesk') )
			{
				$menus['navigation']['helpdesk']['children'] = array
					(
						'order_template' => array
						(	
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'property.uigeneric.index', 'type' => 'order_template') ),
							'text'	=> lang('order template'),
							'image'		=> array('helpdesk', 'helpdesk')
						)
					);
			}


			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}
