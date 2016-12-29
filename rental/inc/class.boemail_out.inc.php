<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package rental
	 * @subpackage email_out
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
	phpgw::import_class('rental.soemail_out');

	include_class('rental', 'email_out', 'inc/model/');

	class rental_boemail_out extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = rental_email_out::get_fields();
			$this->acl_location = rental_email_out::acl_location;
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
				self::$bo = new rental_boemail_out();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = rental_soemail_out::get_instance()->store($object);
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
			$values =  rental_soemail_out::get_instance()->read($params);
	//		$status_text = rental_email_out::get_status_list();
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
	//				$entry['status'] = $status_text[$entry['status']];
					$entry['created'] = $GLOBALS['phpgw']->common->show_date($entry['created']);
					$entry['modified'] = $GLOBALS['phpgw']->common->show_date($entry['modified']);
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = rental_soemail_out::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new rental_email_out();
			}

			return $values;
		}
	}