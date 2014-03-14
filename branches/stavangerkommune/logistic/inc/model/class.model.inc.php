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
 	* @version $Id: class.model.inc.php 10059 2012-09-28 19:08:25Z sigurdne $
	*/

	abstract class logistic_model
	{
		protected $validation_errors = array();
		protected $validation_warnings = array();

		protected $consistency_warnings = array();

		protected $field_of_responsibility_id;
		protected $field_of_responsibility_name;
		protected $permission_array;

		public function __construct(int $id)
		{
			$this->id = (int)$id;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id($id)
		{
			$this->id = $id;
		}

		/**
		 * Gets the value of the class attribute with the given name.  As such this function translates from
		 * string to variable.
		 *
		 * @param $field the name of the class attribute to get
		 * @return mixed the value of the attribute
		 */
		public function get_field($field)
		{
			return $this->{"$field"};
		}

		/**
		 * Sets the value of the class attribute with the given name.  As such this function translates from
		 * string to variable name.
		 *
		 * @param $field the name of the class attribute to set
		 * @param $value the value to set
		 */
		public function set_field($field, $value)
		{
			$this->{"$field"} = $value;
		}

		/**
		 * Validate the object according to the database setup and custom rules.  This function
		 * can be overridden in subclasses.  It is then up to the subclasses to call this parent method
		 * in order to validate against the standard database rules.  The subclasses can in addition
		 * add their own specific validation logic.
		 *
		 * @return boolean true if the object is valid, false otherwise
		 */
		public function validates()
		{
			return true;
		}

		public function toArray()
		{

	// Alternative 1
	//			return get_object_vars($this);

	// Alternative 2
				$exclude = array
				(
					'get_field', // feiler (foreldreklassen)
					'get_so',//unødvendig 
				);

				$class_methods = get_class_methods($this);
				$control_item_arr = array();
				foreach ($class_methods as $class_method)
				{
					if( stripos($class_method , 'get_' ) === 0  && !in_array($class_method, $exclude))
					{
						$_class_method_part = explode('get_', $class_method);
						$control_item_arr[$_class_method_part[1]] = $this->$class_method();
					}
				}
				
				return $control_item_arr;
			}

	}
