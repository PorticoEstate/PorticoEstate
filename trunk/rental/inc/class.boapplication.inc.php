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

	phpgw::import_class('rental.soapplication');

	include_class('rental', 'application', 'inc/model/');

	class  rental_boapplication
	{
		protected static
			$bo,
			$fields;


		public function __construct()
		{
			$this->fields = rental_application::get_fields();
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
			return rental_soapplication::get_instance()->store($object);
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
			$fields = $this->fields;

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$params = array(
				'start' => phpgw::get_var('start', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('length', 'int', 'REQUEST', 0),
				'query' => $search['value'],
				'sort' => $columns[$order[0]['column']]['data'],
				'dir' => $order[0]['dir'],
				'allrows' => phpgw::get_var('length', 'int') == -1,
			);

			foreach ($fields as $field => $_params)
			{
				if (!empty($_REQUEST["filter_$field"]))
				{
					$params['filters'][$field] = phpgw::get_var("filter_$field", $_params['type']);
				}
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