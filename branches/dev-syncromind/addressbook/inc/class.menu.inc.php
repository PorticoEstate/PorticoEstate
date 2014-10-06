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
					'text'	=> $GLOBALS['phpgw']->translation->translate('Contacts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction'=> 'addressbook.uiaddressbook.index', 'section' => 'Persons') ),
					'image'	=> array('addressbook', 'navbar'),
					'order'	=> 2,
					'group'	=> 'office'
				)
			);
//			if ( isset($GLOBALS['phpgw_info']['user']['apps']['admin']) )
			if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'addressbook'))
			{
				$menus['admin'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Site Configuration', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index', 'appname' => 'addressbook') )
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Edit custom fields', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uifields.index') )
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Global Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index', 'appname' => 'addressbook') )
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Communication Types Manager', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_type.view') )
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Communication Descriptions Manager', array(), true),
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_comm_descr.view') )
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Location Manager', array(), true),
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_addr_type.view') )
					),

					array
					(
						'text'	=> 'Notes Types Manager',
						'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicatalog_contact_note_type.view') )
					),

					array
					(
						'text'	=> lang('Custom fields on org-person'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute', 'appname' => 'addressbook', 'location' =>'org_person', 'menu_selection' => '') )
					)
				);
			}

			$menus['toolbar'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New Person', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_person'))
				),
				
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New Organisation', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_org'))
				)
			);

			$menus['navigation'] = array
			(
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New Person', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_person'))
				),
				
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('New Organisation', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiaddressbook.add_org'))
				),

				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Import VCard', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uivcard.in'))
				),

				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Categorise Persons', array(), true),
					'url'	=>  $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uicategorize_contacts.index'))
				),
				
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Bulk Import - Contacts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.import'))
				),
				
				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Bulk Import - CSV', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/addressbook/csv_import.php')
				),

				array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Export Contacts', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'addressbook.uiXport.export'))
				)
			);

			if ( isset($GLOBALS['phpgw_info']['user']['apps']['preferences']) )
			{
				$menus['preferences'] = array
				(
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook_prefs.index'))
					),

					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'preferences.uiadmin_acl.aclprefs' , 'acl_app' => 'addressbook'))
					),
					
					array
					(
						'text'	=> $GLOBALS['phpgw']->translation->translate('Edit Categories', array(), true),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uicategories.index' , 'cats_app' => 'addressbook', 'cats_level' => true , 'global_cats' => true))
					)
				);

				$menus['toolbar'][] = array
				(
					'text'	=> $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url'	=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'addressbook.uiaddressbook_prefs.index'))
				);
			}

			$menus['folders'] = phpgwapi_menu::get_categories('addressbook');

			return $menus;
		}
	}
