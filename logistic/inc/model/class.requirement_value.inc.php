<?php

	/**
	 * phpGroupWare - logistic: a part of a Facilities Management System.
	 *
	 * @author Erik Holm-Larsen <erik.holm-larsen@bouvet.no>
	 * @copyright Copyright (C) 2011,2012 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @subpackage logistic
	 * @version $Id $
	 */
	include_class('logistic', 'model', '/inc/model/');

	class logistic_requirement_value extends logistic_model
	{
		public static $so;

		protected $id;
		protected $requirement_id;
		protected $value;
		protected $operator;
		protected $cust_attribute_id;
		protected $create_user;

		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 *
		 * @param int $id the id of this project
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int) $id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_requirement_id($requirement_id)
		{
			$this->requirement_id = $requirement_id;
		}

		public function get_requirement_id()
		{
			return $this->requirement_id;
		}

		public function set_cust_attribute_id($cust_attribute_id)
		{
			$this->cust_attribute_id = $cust_attribute_id;
		}

		public function get_cust_attribute_id()
		{
			return $this->cust_attribute_id;
		}

		public function set_value($value)
		{
			$this->value = $value;
		}

		public function get_value()
		{
			return $this->value;
		}
		
		public function set_operator($operator)
		{
			$this->operator = $operator;
		}

		public function get_operator()
		{
			return $this->operator;
		}
		
		public function set_create_user($create_user)
		{
			$this->create_user = $create_user;
		}
		
		public function get_create_user() { return $this->create_user; }

		/**
		* Get a static reference to the storage object associated with this model object
		*
		* @return the storage object
		*/
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('logistic.sorequirement');
			}

			return self::$so;
		}
	}
