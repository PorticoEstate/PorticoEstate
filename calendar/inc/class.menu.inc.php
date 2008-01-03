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
					'text'	=> lang('Calendar'),
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
					'text'	=> lang('New'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.add')),
					'image'	=> array('calendar', 'new')

				),
				array
				(
					'text'	=> lang('Today'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.day')),
					'image'	=> array('calendar', 'today')
				),
				array
				(
					'text'	=> lang('Week'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week')),
					'image'	=> array('calendar', 'week')
				),
				array
				(
					'text'	=> lang('Week Detailed'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week_new')),
					'image'	=> array('calendar', 'week_detailed')
				),
				array
				(
					'text'	=> lang('Month'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.month')),
					'image'	=> array('calendar', 'month')
				),
				array
				(
					'text'	=> lang('Year'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.year')),
					'image'	=> array('calendar', 'year')
				),
				array
				(
					'text'	=> lang('Group Planner'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.planner')),
					'image'	=> array('calendar', 'planner')
				),
				array
				(
					'text'	=> lang('Busy/Free'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.matrixselect')),
					'image'	=> array('calendar', 'busy_free')
				),
				array
				(
					'text'	=> lang('Import'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiicalendar.import')),
					'image'	=> array('calendar', 'import')
				)
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> lang('Site Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'admin.uiconfig.index', 'appname'	=> 'calendar') )
					),
					array
					(
						'text'	=> lang('Custom fields and sorting'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'calendar.uicustom_fields.index') )
					),
					array
					(
						'text'	=> lang('Calendar Holiday Management'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'calendar.uiholiday.admin') )
					),
					array
					(
						'text'	=> lang('Global Categories'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'admin.uicategories.index', 'appname'	=> 'calendar') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> lang('Preferences'),
						'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'calendar'))
					),
					array
					(
						'text'	=> lang('Grant Access'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'	=> 'preferences.uiaclprefs.index', 'acl_app'	=> 'calendar') )
					),
					array
					(
						'text'	=> lang('Edit Categories'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'	=> 'preferences.uicategories.index', 'cats_app'	=> 'calendar', 'cats_level'	=> 1, 'global_cats'	=> true))
					)
				);
				$menus['toolbar'][] = array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname'	=> 'calendar')),
					'image'	=> array('calendar', 'preferences')
				);
			}

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> lang('New'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.add'))
				),
				array
				(
					'text'	=> lang('Today'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.day'))
				),
				array
				(
					'text'	=> lang('Week'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week'))
				),
				array
				(
					'text'	=> lang('Week Detailed'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.week_new'))
				),
				array
				(
					'text'	=> lang('Month'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.month'))
				),
				array
				(
					'text'	=> lang('Year'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.year'))
				),
				array
				(
					'text'	=> lang('Group Planner'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.planner'))
				),
				array
				(
					'text'	=> lang('Daily Matrix View'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uicalendar.matrixselect'))
				),
				array
				(
					'text'	=> lang('Import'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'calendar.uiicalendar.import'))
				)

			);

			$menus['folders'] = phpgwapi_menu::get_categories('calendar');

			return $menus;
		}
	}
