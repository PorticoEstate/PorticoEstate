<?php
	/**
	 * Addressbook - Menus
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2007 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package addressbook 
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
	 * @package addressbook
	 */	
	class addressbook_menu
	{
		/**
		 * Get the menus for the addressbook
		 *
		 * @return array available menus for the current user
		 */
		function get_menu()
		{
			$menus = array();
			$menus['navbar'] = array
			(
				'addressbook'	=> array
				(
					'text'	=> lang('Contacts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'addressbook.uiaddressbook.index', 'section' => 'Persons') ),
					'image'	=> array('addressbook', 'navbar'),
					'order'	=> 1,
					'group'	=> 'office'
				)
			);
			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> lang('Site Configuration'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'addressbook') )
					),

					array
					(
						'text'	=> lang('Edit custom fields'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index') )
					),

					array
					(
						'text'	=> lang('Global Categories'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'addressbook') )
					),

					array
					(
						'text'	=> lang('Communication Types Manager'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_type.view') )
					),

					array
					(
						'text'	=> lang('Communication Descriptions Manager'),
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_descr.view') )
					),

					array
					(
						'text'	=> lang('Location Manager'),
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_addr_type.view') )
					),

					array
					(
						'text'	=> 'Notes Types Manager',
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_note_type.view') )
					)
				);
			}

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> lang('New Person'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_person'))
				),
				
				array
				(
					'text'	=> lang('New Organisation'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_org'))
				)
			);

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> lang('New Person'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_person'))
				),
				
				array
				(
					'text'	=> lang('New Organisation'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_org'))
				),

				array
				(
					'text'	=> lang('Import VCard'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uivcard.in'))
				),

				array
				(
					'text'	=> lang('Categorise Persons'),
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicategorize_contacts.index'))
				),
				
				array
				(
					'text'	=> lang('Bulk Import - Contacts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.import'))
				),
				
				array
				(
					'text'	=> lang('Bulk Import - CSV'),
					'url'	=> $GLOBALS['phpgw']->link('/addressbook/csv_import.php')
				),

				array
				(
					'text'	=> lang('Export Contacts'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.export'))
				)
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> lang('Preferences'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook_prefs.index'))
					),

					array
					(
						'text'	=> lang('Grant Access'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'preferences.uiadmin_acl.aclprefs' , 'acl_app' => 'addressbook'))
					),
					
					array
					(
						'text'	=> lang('Edit Categories'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uicategories.index' , 'cats_app' => 'addressbook', 'cats_level' => true , 'global_cats' => true))
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> lang('Preferences'),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook_prefs.index'))
				);
			}

			$menus['folders'] = phpgwapi_menu::get_categories('addressbook');

			return $menus;
		}
	}
