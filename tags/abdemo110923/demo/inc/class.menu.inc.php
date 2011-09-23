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
	 * @package demo
	 */

	class demo_menu
	{
		/**
		 * Get the menus for the demo
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$start_page = 'demo';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['demo']['default_start_page'])
					&& $GLOBALS['phpgw_info']['user']['preferences']['demo']['default_start_page'] )
			{
					$start_page = $GLOBALS['phpgw_info']['user']['preferences']['demo']['default_start_page'];
			}

			$menus['navbar'] = array
			(
				'demo' => array
				(
					'text'	=> lang('demo'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "demo.ui{$start_page}.index") ),
					'image'	=> array('demo', 'navbar'),
					'order'	=> 35,
					'group'	=> 'office'
				),
			);

			$menus['toolbar'] = array();
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'demo'))
					),
					'acl'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl', 'acl_app' => 'demo'))
					),
					'list_atrribs'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom fields', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'demo'))
					),
					'list_functions'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('custom functions', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function', 'appname' =>  'demo'))
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
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'demo', 'type'=> 'user') )
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=> 'preferences.uiadmin_acl.aclprefs', 'acl_app'=> 'demo') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'demo')),
					'image'	=> array('demo', 'preferences')
				);
			}

			$menus['navigation'] = array
			(
				'html'	=> array
				(
					'text'	=> 'HTML',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index', 'output' => 'html'))
				),
				'wml'	=> array
				(
					'text'	=> 'WML',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index', 'output' => 'wml'))
				),
				'alternative'	=> array
				(
					'text'	=> 'YAHOO-table',
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'demo.uidemo.index2'))
				)
			);
			return $menus;
		}
	}
