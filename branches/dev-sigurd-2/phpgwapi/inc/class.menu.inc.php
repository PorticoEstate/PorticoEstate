<?php
	/**
	 * phpGroupWare menu handler class
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage phpgwapi
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

	/*
	 * phpGroupWare menu handler class
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class phpgwapi_menu
	{
		/**
		* Clear the user's menu so it can be regenerated cleanly
		*
		* @return void
		*/
		public function clear()
		{
			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			phpgwapi_cache::user_clear('phpgwapi', 'menu', $account_id);
		}

		/**
		* Get the menu structure and return it
		*
		* @param string $mtype the type of menu sought - default all returned
		*
		* @return array menu structure
		*/
		public function get($mtype = null)
		{
		//	static $menu = null;

			//$menu = null; 
			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			$menu = phpgwapi_cache::user_get('phpgwapi', 'menu', $account_id);

			if ( !$menu )
			{
				$menu = self::load();
				phpgwapi_cache::user_set('phpgwapi', 'menu', $menu, $account_id);
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
		*
		* @return array menu class compatiable array of categories
		*/
		public static function get_categories($module)
		{
			$catobj = createObject('phpgwapi.categories', $GLOBALS['phpgw_info']['user']['account_id'], $module);
			$cats = $catobj->return_sorted_array(0, false, '', 'ASC', 'cat_main, cat_level, cat_name', true);

			return $cats;
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

					if ( !isset($menus[$mtype]) )
					{
						$menus[$mtype] = array();
					}

					switch ( $mtype )
					{
						case 'navbar':
							$menus[$mtype] = array_merge($menus[$mtype], $menu);
							break;
						case 'admin':
							$app_text = $app == 'admin' ? lang('General') : lang($app);
							$menus['navigation']['admin'][$app] = array
							(
								'text'	=> $GLOBALS['phpgw']->translation->translate($app, array(), true),
								'url'	=> $GLOBALS['phpgw']->link('/index.php',
											array('menuaction' => 'admin.uiconfig.index', 'appname' => $app)),
								'image'	=> $raw_menu['navbar'][$app]['image'],
								'children'	=> $menu
							);
							// no break here - fall thru
						default:
							$menus[$mtype][$app] = $menu;
					}
				}
			}
			return $menus;
		}
	}
