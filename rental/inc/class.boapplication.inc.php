<?php
	/**
	 * phpGroupWare
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License v2 or later
	 * @internal
	 * @package rental
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
	phpgw::import_class('rental.soapplication');

	include_class('rental', 'application', 'inc/model/');

	class  rental_boapplication extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields;

		public function __construct()
		{
			$this->fields = rental_application::get_fields();
			$this->acl_location = rental_application::acl_location;
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
				self::$bo = new rental_boapplication();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = rental_soapplication::get_instance()->store($object);
			$this->store_post_commit($object);
			return $ret;
		}

		public function read($params)
		{
			return rental_soapplication::get_instance()->read($params);
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$application = rental_soapplication::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$application = new rental_application();
			}

			return $application;
		}

		public function build_default_read_params()
		{
			$params = parent::build_default_read_params();
			if (phpgw::get_var('composite_id'))
			{
				$params['filters']['composite_id'] = phpgw::get_var('composite_id');
			}

			return $params;
		}

		public function populate($application)
		{
			$fields = $this->fields;

			foreach ($fields as $field	=> $field_info)
			{
				if(($field_info['action'] & PHPGW_ACL_ADD) ||  ($field_info['action'] & PHPGW_ACL_EDIT))
				{
					$application->set_field( $field, phpgw::get_var($field, $field_info['type'] ) );
				}
			}
			return $application;
		}

	}