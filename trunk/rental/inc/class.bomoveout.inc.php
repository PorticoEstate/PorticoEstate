<?php
	/**
	 * phpGroupWare - rental: a part of a Facilities Management System.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/ and Nordlandssykehuset
	 * @package rental
	 * @subpackage moveout
	 * @version $Id: $
	 */


	phpgw::import_class('phpgwapi.bocommon');
	phpgw::import_class('rental.somoveout');

	include_class('rental', 'moveout', 'inc/model/');

	class rental_bomoveout extends phpgwapi_bocommon
	{
		protected static
			$bo,
			$fields,
			$acl_location;

		public function __construct()
		{
			$this->fields = rental_moveout::get_fields();
			$this->acl_location = rental_moveout::acl_location;
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
				self::$bo = new rental_bomoveout();
			}
			return self::$bo;
		}

		public function store($object)
		{
			$this->store_pre_commit($object);
			$ret = rental_somoveout::get_instance()->store($object);
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
			$values =  rental_somoveout::get_instance()->read($params);
			$dateformat = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			foreach ($values['results'] as &$entry)
			{
					$entry['created'] = date($dateformat, $entry['created']);//$GLOBALS['phpgw']->common->show_date($entry['created']);
					$entry['modified'] = date($dateformat, $entry['modified']);//$GLOBALS['phpgw']->common->show_date($entry['modified']);
			}
			return $values;
		}

		public function read_single($id, $return_object = true)
		{
			if ($id)
			{
				$values = rental_somoveout::get_instance()->read_single($id, $return_object);
			}
			else
			{
				$values = new rental_moveout();
			}

			$custom_fields = rental_moveout::get_custom_fields();
			if($custom_fields)
			{
				$custom_fields = rental_somoveout::get_instance()->read_custom_field_values($id, $custom_fields);
				$_values = createObject('property.custom_fields')->prepare(array('attributes' => $custom_fields), 'rental', rental_moveout::acl_location, $view = false);

				if($return_object)
				{
					$values->attributes = $_values[attributes];
				}
				else
				{
					$values['attributes'] = $_values[attributes];
				}
			}

			return $values;
		}
	}