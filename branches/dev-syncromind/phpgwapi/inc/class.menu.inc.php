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
	   the Free Software Foundation, either version 2 of the License, or
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
		var $public_functions = array
		(
			'get_local_menu_ajax' => true
		);
		
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
			static $menu = null;

			$account_id = $GLOBALS['phpgw_info']['user']['account_id'];

			if ( !$menu )
			{
				$menu = phpgwapi_cache::user_get('phpgwapi', 'menu', $account_id,true,true);
			}

			if ( !$menu )
			{
				$menu = self::load();
				phpgwapi_cache::user_set('phpgwapi', 'menu', $menu, $account_id,true,true);
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

			foreach ( $GLOBALS['phpgw_info']['user']['apps'] as $app => $app_info )
		//	foreach ( $raw_menus as $app => $raw_menu )
			{
				$raw_menu = $raw_menus[$app];
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

		/**
		 * Render  a menu for an application
		 *
		 * @param array  $item the menu item
		 * @param string $id   identificator for current location
		 * @param string $children rendered sub menu
		 * @param bool   $show_appname show appname as top level
		 */
		public function render_menu($app, $navigation, $navbar, $show_appname = false)
		{
			$treemenu = '';
			$submenu = isset($navigation) ? self::_render_submenu($app, $navigation) : '';
			$treemenu .= self::_render_item($navbar, "navbar::{$app}", $submenu, $show_appname);
			$html = <<<HTML
			<ul id="menu">
				{$treemenu}
			</ul>

HTML;
			return $html;
		}

		/**
		 * Render items from a menu and append the children
		 *
		 * @param array  $item         the menu item
		 * @param string $id           identificator for current location
		 * @param string $children     rendered sub menu
		 * @param bool   $show_appname show appname as top level
		 */
		protected static function _render_item($item, $id='', $children='', $show_appname = false)
		{
			$current_class = '';
			if ( $id == "navbar::{$GLOBALS['phpgw_info']['flags']['menu_selection']}" )
			{
				$current_class = 'current';
			}

			$link_class =" class=\"{$current_class}\"";

			if(isset($item['group']) && $show_appname) // at application
			{
				return <<<HTML
				<li class="parent">
					<span>{$item['text']}</span>
				{$children}
				</li>
HTML;
			}
			if(isset($item['group']) && !$show_appname)
			{
				return <<<HTML
				{$children}
HTML;
			}
			else if (isset($item['url']))
			{
				return <<<HTML
				<li>
					<a href="{$item['url']}"{$link_class} id="{$id}">
						<span>{$item['text']}</span>
					</a>
					{$children}
				</li>
HTML;
			}
		}

		/**
		 * Get sub items from a menu 
		 *
		 * @param string $parent  name of parent item
		 * @param array  $menu the menu items to add to structure
		 * @param bool   $show_appname show appname as top level
		 */
		protected static function _render_submenu($parent, $menu, $show_appname = false)
		{
			$out = '';
			foreach ( $menu as $key => $item )
			{
				$children = isset($item['children']) ? self::_render_submenu("{$parent}::{$key}", $item['children'], $show_appname) : '';
				$out .= self::_render_item($item, "navbar::{$parent}::{$key}", $children, $show_appname);
			}

			$out = <<<HTML
			<ul>
				{$out}
			</ul>

HTML;
			return $out;
		}


		/**
		 * Render a horisontal menu for an application
		 *
		 * @param array  $menu the menu item
		 */
		public static function render_horisontal_menu($menu)
		{
			$html = <<<HTML
			<table id="menu">
				<tr>
					<td>
						<table>
							<tr>
HTML;
			foreach ($menu as &$item)
			{
				$current_class = '';
				if ( $item['this'] )
				{
					$current_class = 'current';
					$item['text'] = "[<b>{$item['text']}</b>]";
				}
				$link_class =" class=\"{$current_class}\"";
				$html .= <<<HTML
					<td>
						<a href="{$item['url']}"{$link_class} id="{$id}">
							<span>{$item['text']}</span>
						</a>
					</td>
HTML;

				if ( $item['children'] )
				{
					$children  = $item['children'];
				}
			}
			$html .= <<<HTML
							</tr>
						</table>
HTML;

			$html .= isset($children) ? self::_render_horisontal_submenu($children) : '';

			$html .= <<<HTML
				</tr>
			</table>
HTML;
			return $html;
		}

		/**
		 * Get sub items from a menu 
		 *
		 * @param array  $menu the menu items to add to structure
		 */
		protected static function _render_horisontal_submenu($menu)
		{
			$html = <<<HTML
				<tr>
					<td>
						<table>
							<tr>				
HTML;

			foreach ($menu as &$item)
			{
				$current_class = '';
				if ( $item['this'] )
				{
					$current_class = 'current';
					$item['text'] = "[<b>{$item['text']}</b>]";
				}
				$link_class =" class=\"{$current_class}\"";
				$html .= <<<HTML
					<td>
						<a href="{$item['url']}"{$link_class} id="{$id}">
							<span>{$item['text']}</span>
						</a>
					</td>
HTML;

				if ( $item['children'] )
				{
					$children  = $item['children'];
				}
			}
			$html .= <<<HTML
							</tr>
						</table>
HTML;

			$html .= isset($children) ? self::_render_horisontal_submenu($children) : '';
			$html .= <<<HTML
				</td>
			</tr>
HTML;
			return $html;
		}


		public function get_local_menu($app = '')
		{
			$app = $app ? $app : $GLOBALS['phpgw_info']['flags']['currentapp'];
			switch ( $app )
			{
				case 'home':
				case 'login':
					return array();
				default:
					// nothing
			}

			if(!$menu = $GLOBALS['phpgw']->session->appsession($GLOBALS['phpgw_info']['flags']['menu_selection'], "menu_{$app}"))
			{
				$menu_gross = execMethod("{$app}.menu.get_menu",'horisontal');
				$selection = explode('::',$GLOBALS['phpgw_info']['flags']['menu_selection']);
				$level=0;
				$menu = self::_get_sub_menu($menu_gross['navigation'],$selection,$level);
				$GLOBALS['phpgw']->session->appsession(isset($GLOBALS['phpgw_info']['flags']['menu_selection']) && $GLOBALS['phpgw_info']['flags']['menu_selection'] ? $GLOBALS['phpgw_info']['flags']['menu_selection'] : 'menu_missing_selection', "menu_{$app}", $menu);
				unset($menu_gross);
			}
			return $menu;
		}

		public function get_local_menu_ajax()
		{
			$node		= phpgw::get_var('node');

			$selection = explode('|',$node);
			$app = $selection[0];

			if(!isset($GLOBALS['phpgw_info']['user']['apps'][$app]))
			{
				return array();
			}
			$menu = array();

			$_section = 'navigation';
			if($app == 'admin')
			{
				if(!isset($selection[1]))
				{

					$navbar		= $this->get('navbar');
					$navigation = $this->get('admin');

					foreach ( $GLOBALS['phpgw_info']['user']['apps'] as $_app => $app_info )
					{
						if(!in_array($_app, array('logout', 'about', 'preferences')) && isset($navbar[$_app]))
						{
							if(isset($navigation[$_app]))
							{
								$menu[] = array
								(
									'key' 		=> $_app,
									'is_leaf'	=> count($navigation[$_app]) > 1 ? false : true,
									'text'		=> $GLOBALS['phpgw']->translation->translate($_app, array(), true),
									'url'		=> $GLOBALS['phpgw']->link('/index.php',
													array('menuaction' => 'admin.uiconfig.index', 'appname' => $_app))

								);
							}
						}
					}

					return $menu;				
				}
				else
				{
					$_section =  'admin';
					$app =  $selection[1];
					array_shift($selection);
				}
			}


			if(!$menu_gross = phpgwapi_cache::session_get('phpgwapi', "menu_{$app}"))
			{
				$menu_gross = execMethod("{$app}.menu.get_menu");
				phpgwapi_cache::session_set('phpgwapi', "menu_{$app}",$menu_gross);
			}

			$menu_gross = $menu_gross[$_section];

			$count_selection = count($selection);
			if($count_selection > 1)
			{
				for ($i=1;$i<count($selection);$i++)
				{
					if(isset($menu_gross[$selection[$i]]))
					{
						$menu_gross = $menu_gross[$selection[$i]];
					}
					else if (isset($menu_gross['children'][$selection[$i]]))
					{
						$menu_gross = $menu_gross['children'][$selection[$i]];
					}
					else
					{
						$menu_gross = array();
					}
				}
				$children = isset($menu_gross['children']) ? $menu_gross['children'] : array();
			}
			else
			{
				$children = $menu_gross;
			}

			$i=0;
			foreach($children as $key => $vals)
			{
				$vals['url'] = str_replace('&amp;','&', $vals['url']);
				$menu[$i] = $vals;
				$menu[$i]['key'] = $key;
				$menu[$i]['is_leaf'] = true;
				if(isset($menu[$i]['children']))
				{
					$menu[$i]['is_leaf'] = false;
					unset($menu[$i]['children']);
				}
				$i++;
			}

			return $menu;
		}

		protected static function _get_sub_menu($children = array(), $selection=array(),$level=0)
		{
			$level++;
			$i=0;
			$menu = array();
			foreach($children as $key => $vals)
			{
				$menu[$i] = $vals;
				$menu[$i]['this'] = false;
				$menu[$i]['key'] = $key;
				$menu[$i]['is_leaf'] = true;
				if($key == $selection[$level])
				{
					$menu[$i]['this'] = true;
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['children'] = self::_get_sub_menu($menu[$i]['children'],$selection,$level);
						$menu[$i]['is_leaf'] = false;
					}
				}
				else
				{
					if(isset($menu[$i]['children']))
					{
						$menu[$i]['is_leaf'] = false;
						unset($menu[$i]['children']);
					}
				}
				$i++;
			}

			return $menu;
		}
	}
