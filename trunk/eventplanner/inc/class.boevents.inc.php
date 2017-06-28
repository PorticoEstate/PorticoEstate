<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage application
	 * @version $Id:$
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU Lesser General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	phpgw::import_class('phpgwapi.bocommon');
	phpgw::import_class('eventplanner.soevents');

	include_class('eventplanner', 'application', 'inc/model/');

	class eventplanner_boevents extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public $cats;

		public function __construct()
		{
			$this->fields = eventplanner_application::get_fields();
			$this->acl_location = eventplanner_application::acl_location;
			$this->cats = CreateObject('phpgwapi.categories', -1, 'eventplanner', $this->acl_location);
			$this->cats->supress_info = true;
		}

		/**
		 * Implementing classes must return an instance of itself.
		 *
		 * @return the class instance.
		 */
		public static function get_instance()
		{
			if (self::$bo == null)
			{
				self::$bo = new eventplanner_boevents();
			}
			return self::$bo;
		}

		/**
		 * Nothing to store - just reporting
		 * @param type $object
		 */
		public function store($object)
		{
		}

		public function read($params)
		{
			if (isset($params['filters']['category_id']) && $params['filters']['category_id'] > 0)
			{
				$category_id = $params['filters']['category_id'];
				$cat_list = $this->cats->return_sorted_array(0, false, '', '', '', false, $category_id, false);
				$cat_filter = array($category_id);
				foreach ($cat_list as $_category)
				{
					$cat_filter[] = $_category['id'];
				}
				$params['filters']['category_id'] = $cat_filter;
			}

			$values =  eventplanner_soevents::get_instance()->read($params);
			$status_text = eventplanner_application::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
				$dates =  eventplanner_soevents::get_instance()->get_dates($entry['id']);

				$entry['status'] = $status_text[$entry['status']];
				$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
				$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
				$entry['date_start'] = $GLOBALS['phpgw']->common->show_date($dates['date_start'], $dateformat);
				$entry['date_end'] = $GLOBALS['phpgw']->common->show_date($dates['date_end'], $dateformat);
				$entry['case_officer_id'] = $entry['case_officer_id'] ? $GLOBALS['phpgw']->accounts->get($entry['case_officer_id'])->__toString() : '';
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = eventplanner_soevents::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new eventplanner_application();
			}

			return $values;
		}
	}