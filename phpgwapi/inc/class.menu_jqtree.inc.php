<?php
	/**
	 * phpgwapi - Menus
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2022 Free Software Foundation, Inc. http://www.fsf.org/
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
	class phpgwapi_menu_jqtree
	{
		var $public_functions = array(
			'get_menu' => true,
		);

		private $current_node_id;
		private $navbar = array();
		private $menu, $bookmarks ;

		function __construct( $navbar = array() )
		{
			$this->menu = createObject('phpgwapi.menu');
			if (!$navbar)
			{
				$navbar = $this->get_navbar();
			}
			$this->set_navbar($navbar);
			$this->set_current_node_id(0);
			$this->bookmarks = phpgwapi_cache::user_get('phpgwapi', "bookmark_menu", $GLOBALS['phpgw_info']['user']['id']);

		}

		private function get_navbar()
		{
			$navbar = $this->menu->get('navbar');
			if (empty($GLOBALS['phpgw_info']['user']['preferences']['property']['nonavbar']))
			{
				$this->prepare_navbar($navbar);
			}
			else
			{
				foreach ($navbar as & $app_tmp)
				{
					$app_tmp['text'] = ' ...';
				}
			}

			return $navbar;
		}

		/**
		 * Callback for usort($navbar)
		 *
		 * @param array $item1 the first item to compare
		 * @param array $item2 the second item to compare
		 * @return int result of comparision
		 */
		function sort_navbar( $item1, $item2 )
		{
			$a	 = & $item1['order'];
			$b	 = & $item2['order'];

			if ($a == $b)
			{
				return strcmp($item1['text'], $item2['text']);
			}
			return ($a < $b) ? -1 : 1;
		}

		/**
		 * Organise the navbar properly
		 *
		 * @param array $navbar the navbar items
		 * @return array the organised navbar
		 */
		function prepare_navbar( &$navbar )
		{
			if (isset($navbar['admin']) && is_array($navbar['admin']))
			{
				$navbar['admin']['children'] = $this->menu->get('admin');
			}
			uasort($navbar, array($this, 'sort_navbar'));

		}

		private function set_navbar( $navbar )
		{
			$this->navbar = $navbar;
		}

		private function set_current_node_id( $current_node_id )
		{
			$this->current_node_id = $current_node_id;
		}

		public function get_current_node_id()
		{
			return $this->current_node_id;
		}

		public function get_menu()
		{
			$treemenu	 = array();
			$navbar		 = $this->navbar;
			$navigation	 = $this->menu->get('navigation');

			foreach ($navbar as $app => $app_data)
			{
				if (!in_array($app, array('logout', 'about', 'preferences')))
				{
					$submenu	 = isset($navigation[$app]) ? $this->render_submenu($app, $navigation[$app]) : '';
					$treemenu[]	 = $this->render_item($app_data, "navbar::{$app}", $submenu);
				}
			}
			return $treemenu;
		}

		private function render_item( $item, $id = '', $children = array())
		{
			
			static $node_id = 0;
			$node_id++;

			$current_class	 = 'context-menu-nav';
			$parent_class	 = '';
			$link_class		 = '';
			$expand_class	 = '';
			$icon_style		 = '';
			$selected		 = false;

//			static $blank_image;
//			static $images	 = array(); // cache

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				$menu_selection = phpgwapi_cache::session_get('navbar', 'menu_selection');
			}
			else
			{
				$menu_selection = $GLOBALS['phpgw_info']['flags']['menu_selection'];

			}

			/*
			 * navbar#{$location_id}
			 */
			if ($id == "navbar::{$menu_selection}" || ($item['location_id'] && $item['location_id'] == $menu_selection))
			{
				$selected = true;
			}

			if(!$children && preg_match("/(^navbar::)/i", $id)) // bookmarks
			{
				if(is_array($this->bookmarks) && isset($this->bookmarks[$id]))
				{
					$current_class .= ' bookmark_checked';
					$item['bookmark_id'] =$id;
				}
			}

			$link_class	 = " class=\"{$current_class}\"";

			$target = '';
			if (isset($item['target']))
			{
				$target = "target = '{$item['target']}'";
			}
			if (isset($item['local_files']) && $item['local_files'])
			{
				$item['url'] = 'file:///' . str_replace(':', '|', $item['url']);
			}

			$icon = !empty($entry['icon']) ? "<i class='{$entry['icon']} mr-2 text-gray-400'></i>": '<i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>';

			$ret = array(
				'name'		 => "<a id='{$id}' href='{$item['url']}' {$link_class} icon='{$item['icon']}' location_id='{$item['location_id']}' style='white-space:nowrap; color:inherit;'{$target}>{$item['text']}</a>",
				'id'		 => $node_id,
				'text'		 => $item['text'],
				'selected'	 => $selected ? 1 : 0,
				'icon'		 => isset($item['icon']) ? $item['icon'] : null

			);

			if ($children)
			{
				$ret['children'] = $children;
			}

			return $ret;
		}

		private function render_submenu( $parent, $menu )
		{
			$out = array();
			foreach ($menu as $key => $item)
			{
				$children	 = isset($item['children']) ? $this->render_submenu("{$parent}::{$key}", $item['children']) : array();
				$out[]		 = $this->render_item($item, "navbar::{$parent}::{$key}", $children);
			}
			return $out;
		}

		/**
		 * Cheat function to collect bookmarks
		 * @staticvar array $bookmarks
		 * @param array $item
		 * @return array bookmarks
		 */
		function set_get_bookmarks($item = array())
		{
			static $bookmarks = array();
			if($item)
			{
				$bookmarks[] = $item;
			}
			return $bookmarks;
		}
	}