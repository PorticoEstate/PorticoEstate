<?php
	/**
	 * phpGroupWare - eventplanner.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplanner
	 * @subpackage core
	 * @version $Id: class.menu.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
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
	 * @package eventplanner
	 */
	class eventplanner_menu
	{

		/**
		 * Get the menus for the eventplanner
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eventplanner';
			$start_page = 'application';
			if (isset($GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['default_start_page']) && $GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['default_start_page'])
			{
				$start_page = $GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['default_start_page'];
			}

			$menus['navbar'] = array(
				'eventplanner' => array(
					'text' => lang('eventplanner'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.ui{$start_page}.index")),
					'image' => array('eventplanner', 'navbar'),
					'order' => 35,
					'group' => 'office'
				),
			);

			$menus['toolbar'] = array();
			if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']))
			{
				$menus['admin'] = array
					(
					'index' => array
						(
						'text' => lang('Configuration'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uiconfig.index',
							'appname' => 'eventplanner'))
					),
					'acl' => array(
						'text' => $GLOBALS['phpgw']->translation->translate('Configure Access Permissions', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.list_acl',
							'acl_app' => 'eventplanner'))
					),
					'permission'	=> array
					(
						'text'	=> lang('permission'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uipermission.index') )
					),
					'list_functions' => array(
						'text' => $GLOBALS['phpgw']->translation->translate('custom functions', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_custom_function',
							'appname' => 'eventplanner'))
					),
					'application_cats' => array
						(
						'text' => lang('Application category'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.uicategories.index',
							'appname' => 'eventplanner', 'location' => '.application', 'global_cats' => 'true',
							'menu_selection' => 'admin::eventplanner::application_cats'))
					),
					'application_type'	=> array
					(
						'text'	=> lang('application type'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uigeneric.index', 'type' => 'application_type') )
					),
					'customer_category'	=> array
					(
						'text'	=> lang('customer category'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uigeneric.index', 'type' => 'customer_category') )
					),
					'vendor_category'	=> array
					(
						'text'	=> lang('vendor category'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uigeneric.index', 'type' => 'vendor_category') )
					),
					'resource_category'	=> array
					(
						'text'	=> lang('resource category'),
						'url'	=> $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uigeneric.index', 'type' => 'resource_category') )
					),
					'custom_field_groups' => array
						(
						'text' => lang('custom field groups'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute_group',
							'appname' => 'eventplanner', 'menu_selection' => 'admin::eventplanner::custom_field_groups'))
					),
					'custom_fields' => array
						(
						'text' => lang('custom fields'),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'admin.ui_custom.list_attribute',
							'appname' => 'eventplanner', 'menu_selection' => 'admin::eventplanner::custom_fields'))
					),

				);
			}

			if (isset($GLOBALS['phpgw_info']['user']['apps']['preferences']))
			{
				$menus['preferences'] = array
					(
					array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
						'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'eventplanner',
							'type' => 'user'))
					),
					array
						(
						'text' => $GLOBALS['phpgw']->translation->translate('Grant Access', array(), true),
						'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'preferences.uiadmin_acl.aclprefs',
							'acl_app' => 'eventplanner'))
					)
				);

				$menus['toolbar'][] = array
					(
					'text' => $GLOBALS['phpgw']->translation->translate('Preferences', array(), true),
					'url' => $GLOBALS['phpgw']->link('/preferences/preferences.php', array('appname' => 'eventplanner')),
					'image' => array('eventplanner', 'preferences')
				);
			}

			$menus['navigation'] = array(
				'application' => array(
					'text' => lang('application'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'eventplanner.uiapplication.index'))
				),
				'events' => array(
					'text' => lang('events'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uievents.index")),
					'image' => array('events', 'navbar'),
				),
				'customer' => array(
					'text' => lang('customer'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uicustomer.index")),
					'image' => array('customer', 'navbar'),
				),
				'vendor' => array(
					'text' => lang('vendor'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uivendor.index")),
					'image' => array('vendor', 'navbar'),
				),
				'booking' => array(
					'text' => lang('booking'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uibooking.index")),
					'image' => array('customer', 'navbar'),
				),
				'vendor_report' => array(
					'text' => lang('vendor report'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uivendor_report.index")),
					'image' => array('vendor_report', 'navbar'),
				),
				'customer_report' => array(
					'text' => lang('customer report'),
					'url' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => "eventplanner.uicustomer_report.index")),
					'image' => array('customer_report', 'navbar'),
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}