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
			$control_area_id = phpgw::get_var('control_area_id', 'int');

			$control_id		 = phpgw::get_var('control_id', 'int');
			$part_of_town_id = (array)phpgw::get_var('part_of_town_id', 'int');
			$current_year	 = phpgw::get_var('current_year', 'int', 'REQUEST', date(Y));

			if (phpgw::get_var('prev_year', 'bool'))
			{
				$current_year --;
			}
			if (phpgw::get_var('next_year', 'bool'))
			{
				$current_year ++;
			}

			$control_types = createObject('controller.socontrol')->get_controls_by_control_area($control_area_id);

			$control_type_list = array(array('id' => '', 'name' => lang('select')));
			foreach ($control_types as $control_type)
			{
				$control_type_list[] = array(
					'id'		 => $control_type['id'],
					'name'		 => $control_type['title'],
					'selected'	 => $control_id == $control_type['id'] ? 1 : 0
				);
			}


			$first_half_year = array();
			for ($i = 1; $i <= 6; $i++)
			{
				$first_half_year[] = array(
					'id'	 => $i,
					'name'	 => lang(date('F', mktime(0, 0, 0, $i, 1))),
					'url'	 => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
						'year'		 => $current_year,
						'month'		 => $i))
				);
			}

			$second_half_year = array();
			for ($i = 7; $i <= 12; $i++)
			{
				$second_half_year[] = array(
					'id'	 => $i,
					'name'	 => lang(date('F', mktime(0, 0, 0, $i, 1))),
					'url'	 => self::link(array('menuaction' => 'controller.uicalendar_planner.monthly',
						'year'		 => $current_year,
						'month'		 => $i))
				);
			}

			$part_of_towns = createObject('property.sogeneric')->get_list(array('type'		 => 'part_of_town',
				'selected'	 => 0, 'order'		 => 'name', 'sort'		 => 'asc'));

			$part_of_town_list = array();
			$part_of_town_list2 = array();
			foreach ($part_of_towns as &$part_of_town)
			{
				if ($part_of_town['id'] > 0)
				{
					$selected = in_array($part_of_town['id'], $part_of_town_id) ? 1 : 0;
					$part_of_town['name']		 = ucfirst(strtolower($part_of_town['name']));
					$part_of_town['selected']	 = $selected;
					$part_of_town_list[]		 = $part_of_town;

					if($selected)
					{
						$part_of_town_list2[] = $part_of_town;
					}
				}
			}

			$cats				 = CreateObject('phpgwapi.categories', -1, 'controller', '.control');
			$cats->supress_info	 = true;

			$control_area = $cats->formatted_xslt_list(array('format'	 => 'filter', 'globals'	 => true,
				'use_acl'	 => $this->_category_acl));


			$control_area_list = array();
			foreach ($control_area['cat_list'] as $cat_list)
			{
				$control_area_list[] = array
					(
					'id'		 => $cat_list['cat_id'],
					'name'		 => $cat_list['name'],
					'selected'	 => $control_area_id == $cat_list['cat_id'] ? 1 : 0
				);
			}

			array_unshift($control_area_list, array('id' => '', 'name' => lang('select')));

			$data = array
				(
				'control_area_list'	 => array('options' => $control_area_list),
				'prev_year'			 => $current_year - 1,
				'current_year'		 => $current_year,
				'next_year'			 => $current_year + 1,
				'first_half_year'	 => $first_half_year,
				'second_half_year'	 => $second_half_year,
				'part_of_town_list2' => $part_of_town_list2,
				'part_of_town_list'	 => array('options' => $part_of_town_list),
				'form_action'		 => self::link(array('menuaction' => 'controller.uicalendar_planner.index')),
				'control_type_list'	 => array('options' => $control_type_list),
			);

			phpgwapi_jquery::load_widget('bootstrap-multiselect');
			self::add_javascript('controller', 'base', 'calendar_planner.start.js');

			self::render_template_xsl(array('calendar/calendar_planner'), array('start' => $data));
		}

		public function monthly()
		{
			$month	 = phpgw::get_var('month', 'int');
			$data	 = array
				(
				'current_month'	 => lang(date('F', mktime(0, 0, 0, $month, 1))),
				'current_year'	 => phpgw::get_var('year', 'int')
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::render_template_xsl(array('calendar/calendar_planner'), array('monthly' => $data));
		}

		public function send_notification()
		{


			$data = array
				(
				'first_half_year' => $first_half_year,
			);

			phpgwapi_jquery::load_widget('autocomplete');
			self::add_javascript('controller', 'base', 'ajax.js');
			self::render_template_xsl(array('calendar/calendar_planner'), array('notification' => $data));
		}

		public function query()
		{

		}
	}