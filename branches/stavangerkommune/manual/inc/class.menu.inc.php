<?php
	/**
	 * Admin - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 - 2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package addressbook
	 * @version $Id: class.menu.inc.php 11511 2013-12-08 20:57:07Z sigurdne $
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
	class manual_menu
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
				'manual'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('manual', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', 
								array
								(
									'menuaction'		=> 'manual.uidocuments.index'
								)
							),
					'image'	=> array('hrm', 'navbar'),
					'order'	=> -5,
					'group'	=> 'systools'
				)
			);

			$menus['admin'] = array();

			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin'))
			{
				$menus['admin'] = array
				(
					'index' => array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', 
								array
								(
									'menuaction'		=> 'admin.uicategories.index',
									'appname'			=> 'manual',
									'location'			=> '.documents',
									'global_cats'		=> 'true',
									'menu_selection'	=> 'admin::manual::index'
								)
							)
					),
					'acl'	=> array
					(
						'text'	=> lang('Configure Access Permissions'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'manual') )
					)
				);
			}


			$menus['navigation'] =  array
			(
				'add' => array
				(
					'text'	=> lang('add'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array( 'menuaction' => 'manual.uidocuments.add' )),
					'image'	=> array('property', 'location_1'),
				),
				'view' => array
				(
					'text'	=> lang('view'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array( 'menuaction' => 'manual.uidocuments.view' )),
					'image'	=> array('property', 'location_1'),
				),
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array();
			}

			$menus['toolbar'] = array();


			return $menus;
		}
	}
