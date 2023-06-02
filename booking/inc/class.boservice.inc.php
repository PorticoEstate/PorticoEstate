<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package booking
	 * @subpackage article
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
	phpgw::import_class('booking.soservice');

	include_class('booking', 'service', 'inc/model/');

	class booking_boservice extends phpgwapi_bocommon
	{
		protected static
			$bo;

		public function __construct()
		{
			$this->fields = booking_service::get_fields();
			$this->acl_location = booking_service::acl_location;
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
				self::$bo = new booking_boservice();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = booking_soservice::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
		}

		public function read($params)
		{
			$values =  booking_soservice::get_instance()->read($params);
	//		$status_text = booking_service::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
	//				$entry['status'] = $status_text[$entry['status']];
					$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
					$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
			}
			return $values;
		}

		public function read_single($id, $return_object = true, $relaxe_acl = false)
		{
			if ($id)
			{
				$values = booking_soservice::get_instance()->read_single($id, $return_object, $relaxe_acl);
			}
			else
			{
				$values = new booking_service();
			}

			return $values;
		}

		public function get_mapped_services( )
		{
			return booking_soservice::get_instance()->get_mapped_services();
		}

		public function get_pricing( $id )
		{
			return booking_soservice::get_instance()->get_pricing($id);
		}

		public function get_reserved_resources( $building_id )
		{
			return booking_soservice::get_instance()->get_reserved_resources($building_id);
		}

		public function get_mapping($service_id)
		{

			return booking_soservice::get_instance()->get_mapping($service_id);
		}
		public function set_mapping($service_id, $selected_resources)
		{

			return booking_soservice::get_instance()->set_mapping($service_id, $selected_resources);
		}


	}