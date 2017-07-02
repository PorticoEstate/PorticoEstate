<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage vendor_report
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
	phpgw::import_class('eventplanner.sovendor_report');

	include_class('eventplanner', 'vendor_report', 'inc/model/');

	class eventplanner_bovendor_report extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public $cats;

		public function __construct()
		{
			$this->fields = eventplanner_vendor_report::get_fields();
			$this->acl_location = eventplanner_vendor_report::acl_location;
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
				self::$bo = new eventplanner_bovendor_report();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = eventplanner_sovendor_report::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
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

			$values =  eventplanner_sovendor_report::get_instance()->read($params);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];

			$custom = createObject('property.custom_fields');
			$custom_fields = (array)$custom->find('eventplanner', $this->acl_location, 0, '', '', '', true, false);
			$selected = (array)$GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['vendor_report_columns'];
			$location_id	= $GLOBALS['phpgw']->locations->get_id('eventplanner', $this->acl_location);

			foreach ($values['results'] as &$entry)
			{
				$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
				$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
				$entry['date_start'] = $GLOBALS['phpgw']->common->show_date($entry['date_start'], $dateformat);
				$entry['date_end'] = $GLOBALS['phpgw']->common->show_date($entry['date_end'], $dateformat);

				foreach ($custom_fields as $custom_field)
				{
					if( in_array( $custom_field['id'], $selected)  ||  $custom_field['list'])
					{
						$entry[$custom_field['name']] = $custom->get_translated_value(
							array(
								'value' => $entry['json_representation'][$custom_field['name']],
								'datatype' => $custom_field['datatype'],
								'attrib_id' => $custom_field['id']
							),
							$location_id
						);
					}
				}
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = eventplanner_sovendor_report::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new eventplanner_vendor_report();
			}

			return $values;
		}

		function column_list( $selected = '', $allrows = '' )
		{
			if (!$selected)
			{
				$selected = (array)$GLOBALS['phpgw_info']['user']['preferences']['eventplanner']['vendor_report_columns'];
			}
			$filter = array('list' => ''); // translates to "list IS NULL"
			$columns = createObject('phpgwapi.custom_fields')->find('eventplanner', $this->acl_location, 0, '', '', '', true, false, $filter);
			foreach ($columns as &$column)
			{
				$column['name'] = $column['descr'] ? $column['descr'] : $column['input_text'];
			}
			$column_list = $this->select_multi_list($selected, $columns);
			return $column_list;
		}

	}