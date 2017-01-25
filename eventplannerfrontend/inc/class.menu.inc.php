<?php
	/**
	 * phpGroupWare - eventplannerfrontend.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package eventplannerfrontend
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
	 * @package eventplannerfrontend
	 */
	class eventplannerfrontend_menu
	{

		/**
		 * Get the menus for the eventplannerfrontend
		 *
		 * @return array available menus for the current user
		 */
		public function get_menu()
		{
			$incoming_app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eventplannerfrontend';
			$GLOBALS['phpgw']->translation->add_app('eventplanner');
	
			$menus['navbar'] = array(
				'eventplannerfrontend' => array(
					'text' => lang('eventplanner'),
					'url' => $GLOBALS['phpgw']->link('eventplannerfrontend/', array('menuaction' => "eventplannerfrontend.uiapplication.index")),
					'image' => array('eventplannerfrontend', 'navbar'),
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
							'appname' => 'eventplannerfrontend'))
					)
				);
			}
	
			$menus['navigation'] = array(
				'application' => array(
					'text' => lang('application'),
					'url' => phpgwapi_uicommon_jquery::link( array('menuaction' => 'eventplannerfrontend.uiapplication.index'))
				),
				'events' => array(
					'text' => lang('events'),
					'url' => phpgwapi_uicommon_jquery::link( array('menuaction' => "eventplannerfrontend.uievents.index")),
					'image' => array('events', 'navbar'),
				),
				'customer' => array(
					'text' => lang('customer'),
					'url' =>  phpgwapi_uicommon_jquery::link(  array('menuaction' => "eventplannerfrontend.uicustomer.index")),
					'image' => array('customer', 'navbar'),
				),
				'vendor' => array(
					'text' => lang('vendor'),
					'url' =>  phpgwapi_uicommon_jquery::link(  array('menuaction' => "eventplannerfrontend.uivendor.index")),
					'image' => array('vendor', 'navbar'),
				),
				'booking' => array(
					'text' => lang('booking'),
					'url' =>  phpgwapi_uicommon_jquery::link(  array('menuaction' => "eventplannerfrontend.uibooking.index")),
					'image' => array('customer', 'navbar'),
				),
				'vendor_report' => array(
					'text' => lang('vendor report'),
					'url' =>  phpgwapi_uicommon_jquery::link(  array('menuaction' => "eventplannerfrontend.uivendor_report.index")),
					'image' => array('vendor_report', 'navbar'),
				),
				'customer_report' => array(
					'text' => lang('customer report'),
					'url' =>  phpgwapi_uicommon_jquery::link(  array('menuaction' => "eventplannerfrontend.uicustomer_report.index")),
					'image' => array('customer_report', 'navbar'),
				)
			);
			$GLOBALS['phpgw_info']['flags']['currentapp'] = $incoming_app;
			return $menus;
		}
	}