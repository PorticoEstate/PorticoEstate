<?php
	/**
	 * Calendar - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package calendar 
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
	 * @package calendar
	 */	
	class calendar_menu
	{
		/**
		 * Get the menus for the calendar
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'calendar'	=> array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Calendar', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'calendar.uicalendar.index') ),
					'image'	=> array('calendar', 'navbar'),
					'order'	=> 4,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.add')),
					'image'	=> array('calendar', 'new')

				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Today', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.day')),
					'image'	=> array('calendar', 'today')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Week', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week')),
					'image'	=> array('calendar', 'week')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Week Detailed', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week_new')),
					'image'	=> array('calendar', 'week_detailed')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Month', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.month')),
					'image'	=> array('calendar', 'month')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Year', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.year')),
					'image'	=> array('calendar', 'year')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Group Planner', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.planner')),
					'image'	=> array('calendar', 'planner')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Busy/Free', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.matrixselect')),
					'image'	=> array('calendar', 'busy_free')
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Import', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiicalendar.import')),
					'image'	=> array('calendar', 'import')
				)
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					'index'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Site Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'admin.uiconfig.index', 'appname'	=> 'calendar') )
					),
					'custom'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Custom fields and sorting', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'calendar.uicustom_fields.index') )
					),
					'holiday'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Calendar Holiday Management', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'calendar.uiholiday.admin') )
					),
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'admin.uicategories.index', 'appname'	=> 'calendar') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					'preferences'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'calendar'))
					),
					'acls'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'preferences.uiaclprefs.index', 'acl_app'	=> 'calendar') )
					),
					'categories'	=> array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Edit Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'preferences.uicategories.index', 'cats_app'	=> 'calendar', 'cats_level'	=> 1, 'global_cats'	=> true))
					)
				);
				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'calendar')),
					'image'	=> array('calendar', 'preferences')
				);
			}

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.add'))
				),
				array
				(
					'text'		=> $GLOBALS['phpgw']->translation->translate('view', array(), true),
					'url'		=> '#',
					'children'	=> array
					(
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Today', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.day'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Week', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Week Detailed', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week_new'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Month', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.month'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Year', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.year'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Group Planner', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.planner'))
						),
						array
						(
							'text'	=> $GLOBALS['phpgw']->translation->translate('Daily Matrix View', array(), true),
							'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.matrixselect'))
						)
					)
				),
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Import', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiicalendar.import'))
				)

			);

			$menus['folders'] = phpgwapi_menu::get_categories('calendar');

			return $menus;
		}
	}
