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

		public 	$acl_location = '.application';

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
			$criteria = array(
				'appname' => 'rental',
				'location' => $this->bo->acl_location,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/rental/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'])
				{
					require $file;
				}
			}

			$ret = rental_soapplication::get_instance()->store($object);

			reset($custom_functions);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/rental/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'] && !$entry['pre_commit'])
				{
					require $file;
				}
			}
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