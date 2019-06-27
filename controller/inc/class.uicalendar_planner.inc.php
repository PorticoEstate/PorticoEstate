<?php
	/**
	 * phpGroupWare - controller: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @author Torstein Vadla <torstein.vadla@bouvet.no>
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2011,2012,2013,2014,2015 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/
	 * @package property
	 * @subpackage controller
	 * @version $Id$
	 */
	/**
	 * Import the jQuery class
	 */
	phpgw::import_class('phpgwapi.jquery');
	phpgw::import_class('phpgwapi.uicommon_jquery');

	class controller_uicalendar_planner extends phpgwapi_uicommon_jquery
	{

		public $public_functions = array
			(
			'index'				 => true,
			'monthly'			 => true,
			'send_notification'	 => true,
			'query'				 => true
		);

		public function __construct()
		{
			parent::__construct();

			$read	 = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_READ, 'controller'); //1
			$add	 = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_ADD, 'controller'); //2
			$edit	 = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_EDIT, 'controller'); //4
			$delete	 = $GLOBALS['phpgw']->acl->check('.control', PHPGW_ACL_DELETE, 'controller'); //8

			$manage = $GLOBALS['phpgw']->acl->check('.control', 16, 'controller'); //16
			//		$this->bo = CreateObject('property.bolocation', true);

			self::set_active_menu('controller::calendar_planner');
			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('calendar planner');
		}

		public function index()
		{
			$data = array
				(
				'buildings_on_property'		 => $buildings_on_property,
				'my_locations'				 => $my_locations,
				'property_array'			 => $property_array,
				'current_location'			 => $location_array,
				'heading_array'				 => $heading_array,
				'controls_calendar_array'	 => $controls_calendar_array,
				'components_calendar_array'	 => $components_calendar_array,
				'location_level'			 => $level,
				'roles_array'				 => $roles_array,
				'repeat_type_array'			 => $repeat_type_array,
				'current_year'				 => $year,
				'current_month_nr'			 => $month,
				'current_role'				 => $role,
				'current_repeat_type'		 => $repeat_type
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::render_template_xsl(array('calendar/calendar_planner'), array('start' => $data));
		}

		public function monthly()
		{
			$data = array
				(
				'buildings_on_property'		 => $buildings_on_property,
				'my_locations'				 => $my_locations,
				'property_array'			 => $property_array,
				'current_location'			 => $location_array,
				'heading_array'				 => $heading_array,
				'controls_calendar_array'	 => $controls_calendar_array,
				'components_calendar_array'	 => $components_calendar_array,
				'location_level'			 => $level,
				'roles_array'				 => $roles_array,
				'repeat_type_array'			 => $repeat_type_array,
				'current_year'				 => $year,
				'current_month_nr'			 => $month,
				'current_role'				 => $role,
				'current_repeat_type'		 => $repeat_type
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::render_template_xsl(array('calendar/calendar_planner'), array('monthly' => $data));
		}

		public function send_notification()
		{
			$data = array
				(
				'buildings_on_property'		 => $buildings_on_property,
				'my_locations'				 => $my_locations,
				'property_array'			 => $property_array,
				'current_location'			 => $location_array,
				'heading_array'				 => $heading_array,
				'controls_calendar_array'	 => $controls_calendar_array,
				'components_calendar_array'	 => $components_calendar_array,
				'location_level'			 => $level,
				'roles_array'				 => $roles_array,
				'repeat_type_array'			 => $repeat_type_array,
				'current_year'				 => $year,
				'current_month_nr'			 => $month,
				'current_role'				 => $role,
				'current_repeat_type'		 => $repeat_type
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::render_template_xsl(array('calendar/calendar_planner'), array('notification' => $data));
		}

		public function query()
		{

		}
	}