<?php
	/**
	 * phpgwapi - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi 
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
	 * @package phpgwapi
	 */	
	class phpgwapi_menu_apps
	{
		/**
		 * Get the menus for the phpgwapi related stuff
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'home' => array
				(
					'text'	=> lang('Home'),
					'url'	=> $GLOBALS['phpgw']->link('/home.php'),
					'image'	=> array('phpgwapi', 'home'),
					'order'	=> -100,
					'group'	=> 'core'
				),
				/*
				'home' => array
				(
					'text'	=> lang('something'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'phpgwapi.uiphpgwapi.index') ),
					'image'	=> array('phpgwapi', 'navbar'),
					'order'	=> 99,
					'group'	=> 'core'
				)
				*/
				'logout' => array
				(
					'text'	=> lang('Logout'),
					'url'	=> $GLOBALS['phpgw']->link('/logout.php'),
					'image'	=> array('phpgwapi', 'logout'),
					'order'	=> 999,
					'group'	=> 'core'
				)
			);
			/*

			$menus['toolbar'] = array();

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array();
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array();
			}

			$menus['navigation'] = array();
			$menus['folders'] = phpgwapi_menu::get_categories('phpgwapi');
			*/
			return $menus;
		}
	}
