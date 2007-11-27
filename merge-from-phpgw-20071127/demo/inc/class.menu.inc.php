<?php
	/**
	* phpGroupWare - DEMO: a demo aplication.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003-2007 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package demo
	* @subpackage core
 	* @version $Id: class.menu.inc.php 18358 2007-11-27 04:43:37Z skwashd $
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
	 
	 /**
	 * Description
	 * @package demo
	 */

	class demo_menu
	{
		/**
		* @var string $sub the currently selected menu option??
		*/
		private $sub;

		/**
		* Constructor
		*/
		public function __construct($sub = null)
		{
			$this->sub	= $sub;
		}

		/**
		* Generate a menu
		*
		* @param ??? $page ???
		* @param ??? $page2 ???
		*/
		public function links($page = '', $page_2 = '')
		{
			$menu['module'][] = array
			(
				'url'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index', 'output' => 'html')),
				'text' 			=> 'HTML',
				'statustext' 	=> 'HTML',
				'this'			=> $this->sub == 'html'
			);

			$menu['module'][] = array
			(
				'url'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index', 'output' => 'wml')),
				'text' 			=> 'WML',
				'statustext' 	=> 'WML',
				'this'			=> $this->sub == 'wml'
			);

			$menu['module'][] = array
			(
				'url'			=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index2', 'output' => 'html')),
				'text' 			=> 'Alternative',
				'statustext' 	=> 'Alternative list',
				'this'			=> $this->sub == 'alternative'
			);

			$GLOBALS['phpgw']->session->appsession('menu_demo', 'sidebox', $menu);
			return $menu;
		}

		
		/**
		* Set the submenu value
		*
		* @param string $sub the current sub menu selection
		*/
		public function set_sub($sub)
		{
			$this->sub = phpgw::clean_value($sub, 'string');
		}
	}
