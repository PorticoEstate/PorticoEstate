<?php
	/**
	 * todo - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package todo 
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
	 * @package todo
	 */	
	class todo_menu
	{
		/**
		 * Get the menus for the todo
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'todo' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('todo', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'todo.uitodo.show_list') ),
					'image'	=> array('todo', 'navbar'),
					'order'	=> 10,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'todo.uitodo.add') ),
				)
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'todo', 'global_cats' => 1) )
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'todo')),
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.aclprefs', 'acl_app' => 'todo'))
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Edit categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uicategories.index', 'cats_app' => 'todo', 'cats_level' => 1, 'global_cats'=> 1))
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'todo')),
					'image'	=> array('todo', 'preferences')
				);
			}

			$menus['navigation'] =  array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'todo.uitodo.add') ),
				)
			);

			$menus['folders'] = phpgwapi_menu::get_categories('todo');

			return $menus;
		}
	}
