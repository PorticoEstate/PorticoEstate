<?php
	/**
	 * phpGroupWare menu handler class
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage utitlity
	 * @version $Id: class.hooks.inc.php 18013 2007-03-06 14:30:39Z sigurdne $
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

	/*
	 * phpGroupWare menu handler class
	 *
	 * @package phpgwapi
	 * @subpackage utitlity
	 */
	class phpgwapi_menu
	{
		/**
		* Clear the user's menu so it can be regenerated cleanly
		*/
		public function clear()
		{
			$GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu', null);
		}

		/**
		* Get the menu structure and return it
		*
		* @param string $mtype the type of menu sought - default all returned
		* @return array menu structure
		*/
		public function get($mtype = null)
		{
			//$menu = $GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu');
			$menu = null;
			if ( !$menu )
			{
				$menu = self::load();
				$GLOBALS['phpgw']->session->appsession('phpgwapi', 'menu', $menu);
			}
			if ( !is_null($mtype) && isset($menu[$mtype]) )
			{
				return $menu[$mtype];
			}
			return $menu;
		}

		/**
		* Get categories available for the current user
		*
		* @param string $module the module the categories are sought for
		* @return array menu class compatiable array of categories
		*/
		public static function get_categories($module)
		{
			$catobj = createObject('phpgwapi.categories', $GLOBALS['phpgw_info']['user']['account_id'], $module);
			$cats = $catobj->return_sorted_array(0, false, '', 'ASC', 'cat_main, cat_level, cat_name', true);
			//echo "module: $module <pre>" . print_r($cats, true) . '</pre>';
		}
		
		/**
		* Load the menu structure from all available applications
		*
		* @return array the menu structure for the current user
		*/
		private function load()
		{
			$menus = array();
			$raw_menus = $GLOBALS['phpgw']->hooks->process('menu');
			foreach ( $raw_menus as $app => $raw_menu )
			{
				// Ignore invalid entries
				if ( !is_array($raw_menu) )
				{
					continue;
				}
				foreach ( $raw_menu as $mtype => $menu )
				{
					 //no point in adding empty items
					if ( !count($menu) )
					{
						continue;
					}
					$menus[$mtype][$app] = $menu;
				}
			}
			return $menus;
		}
	}
