<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package eventplanner
	 * @subpackage booking
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
	phpgw::import_class('eventplanner.sobooking');

	include_class('eventplanner', 'booking', 'inc/model/');

	class eventplanner_bobooking extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = eventplanner_booking::get_fields();
			$this->acl_location = eventplanner_booking::acl_location;
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
				self::$bo = new eventplanner_bobooking();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = eventplanner_sobooking::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
		}

		public function read($params)
		{
			if(empty($params['filters']['active']))
			{
				$params['filters']['active'] = 1;
			}
			else
			{
				unset($params['filters']['active']);
			}
			$values =  eventplanner_sobooking::get_instance()->read($params);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
				$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
				$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
				$entry['from_'] = $GLOBALS['phpgw']->common->show_date($entry['from_']);
				$entry['to_'] = $GLOBALS['phpgw']->common->show_date($entry['to_']);
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = eventplanner_sobooking::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new eventplanner_booking();
			}

			return $values;
		}

		public function update_active_status( $ids, $action )
		{
			return eventplanner_sobooking::get_instance()->update_active_status($ids, $action);
		}

		public function update_schedule( $id, $from_ )
		{
			$booking = eventplanner_sobooking::get_instance()->read_single($id, true);
			$booking->from_ = $from_;
//			$application = createObject('eventplanner.boapplication')->read_single($entity->application_id);
//			$booking->to_ = $booking->from_ + ((int)$application->timespan * 60);
			$booking->customer_id = $booking->customer_id ? $booking->customer_id : '';//foreigns key

			if($booking->validate())
			{
				return $booking->store();
			}
//			return eventplanner_sobooking::get_instance()->update($booking);
		}


	}