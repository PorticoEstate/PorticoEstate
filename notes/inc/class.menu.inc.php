<?php
	/**
	 * notes - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package notes 
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
	 * @package notes
	 */	
	class notes_menu
	{
		/**
		 * Get the menus for the notes
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();

			$menus['navbar'] = array
			(
				'notes' => array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('notes', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'notes.uinotes.index') ),
					'image'	=> array('notes', 'navbar'),
					'order'	=> 8,
					'group'	=> 'office'
				)
			);

			$menus['toolbar'] = array
			(
				array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'notes.uinotes.edit')),
					'text'  => $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'image' => array('notes', 'new')
				)

			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> 'Global Categories',
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'notes', 'global_cats' => 'true') )
					)
				);
			}

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> 'Grant Access',
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.aclprefs', 'acl_app' => 'notes'))
					),
					array
					(
						'text'	=> 'Edit categories',
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction'=>'preferences.uicategories.index', 'cats_app' => 'notes', 'cats_level' => true, 'global_cats' => true))
					)
				);
			}

			$menus['navigation'] = array
			(
				array
				(
					'url'   => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'notes.uinotes.edit')),
					'text'  => $GLOBALS['phpgw']->translation->translate('New', array(), true),
					'image' => array('notes', 'new')
				)
			);
			$menus['folders'] = phpgwapi_menu::get_categories('notes');

			return $menus;
		}
	}
