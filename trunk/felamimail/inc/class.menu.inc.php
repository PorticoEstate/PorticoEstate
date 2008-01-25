<?php
	/**
	 * felamimail - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package felamimail 
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


	/**
	 * Menus
	 *
	 * @package felamimail
	 */	
	class felamimail_menu
	{
		/**
		 * Get the menus for the felamimail
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'felamimail'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('Felamimail', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'felamimail.uifelamimail.index') ),
					'image'	=> array('felamimail', 'navbar'),
					'order'	=> 6,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uicompose.compose'))
				),
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Site Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'admin.uiconfig.index','appname'=> 'felamimail'))
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'felamimail')),
					),
					array
					(
						'text'	=> $GLOBALS['phpgw']->translations->translate('Manage Filters', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uisieve.mainScreen', 'action' => 'updateFilter')),
					),
					array
					(
						'text'	=>'Manage Folders',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uipreferences.listFolder') )
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'felamimail')),
					'image'	=> array('felamimail', 'preferences')
				);
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('Manage Filters', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uisieve.mainScreen', 'action' => 'updateFilter')),
				);
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('Manage Folders', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'felamimail.uipreferences.listFolder') )
				);
			}

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translations->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'felamimail.uicompose.compose'))
				)
			);
			//$menus['folders'] = phpgwapi_menu::get_categories('felamimail');

			return $menus;
		}
	}
