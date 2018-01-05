<?php
	/**
	* Generic parent for objects
	* @author Sigurd Nes <sigurdne@online.no> and others
	* @copyright Copyright (C) 2016 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage model
	* @version $Id:$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	abstract class phpgwapi_model
	{

		protected $validation_errors = array();
		protected $validation_warnings = array();
		protected $consistency_warnings = array();
		protected $field_of_responsibility_id;
		protected $field_of_responsibility_name;
		protected $permission_array;
		protected $appname;

		public function __construct( int $id )
		{
			$this->id = (int)$id;
			$this->appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
		}

		public function __set($name, $value)
		{	
			$this->$name = $value;
		}

		/**
		 * Magic get method
		 *
		 * @param string $name the variable to fetch
		 *
		 * @return mixed the value of the variable sought - null if not found
		 */
		public function __get($name)
		{
			$called_class = get_called_class();

			$datatype = 'string';
			if( method_exists($called_class, 'get_fields'))
			{
				$fields = $called_class::get_fields();
				$datatype = $fields[$name]['type'];
			}

			if ( isset($this->$name))
			{
				if(is_array($this->$name))
				{
					$value = $this->$name;
				}
				else
				{
					switch ($datatype)
					{
						case 'int':
						case 'integert':
							$value = empty($this->$name) && $this->$name === '' ? null : (int)$this->$name;
							break;
						case 'float':
							$value = (float)$this->$name;
							break;
						case 'bool':
						case 'boolean':
							$value = (bool)$this->$name;
							break;
						default:
							$value = $this->$name;
							break;
					}
				}

				return $value;
			}

			return null;
		}

		public function get_id()
		{
			return $this->id;
		}

		public function set_id( $id )
		{
			$this->id = $id;
		}

		/**
		 * Retrieve the name of the 'field of responsibility' this object belongs to.
		 * The default name is the root location (.)
		 *
		 * @return the field name
		 */
		public function get_field_of_responsibility_name()
		{
			if (!isset($this->field_of_responsibility_name))
			{
				if (isset($this->field_of_responsibility_id))
				{
					$array = $GLOBALS['phpgw']->locations->get_name($this->field_of_responsibility_id);
					if ($array['appname'] = $GLOBALS['phpgw_info']['flags']['currentapp'])
					{
						$this->field_of_responsibility_name = $array['location'];
					}
				}
				else
				{
					$this->field_of_responsibility_name = '.';
				}
				return $this->field_of_responsibility_name;
			}
			else
			{
				return $this->field_of_responsibility_name;
			}
		}

		/**
		 * Check if the current user has been given permission for a given action
		 *
		 * @param $permission
		 * @return true if current user has permission, false otherwise
		 */
		public function has_permission( $permission = PHPGW_ACL_PRIVATE )
		{
			return $GLOBALS['phpgw']->acl->check($this->get_field_of_responsibility_name(), $permission, $this->appname);
		}

		/**
		 * Set the identifier for the field of responsibility this object belongs to
		 *
		 * @param $id the ocation identifier
		 */
		public function set_field_of_responsibility_id( $id )
		{
			$this->field_of_responsibility_id = $id;
		}

		/**
		 * Retrieve an array with the different permission levels the current user has for this object
		 *
		 * @return an array with permissions [PERMISSION_BITMASK => true/false]
		 */
		public function get_permission_array()
		{
			$location_name = $this->get_field_of_responsibility_name();
			return array(
				PHPGW_ACL_READ => $GLOBALS['phpgw']->acl->check($location_name, PHPGW_ACL_READ, $this->appname),
				PHPGW_ACL_ADD => $GLOBALS['phpgw']->acl->check($location_name, PHPGW_ACL_ADD, $this->appname),
				PHPGW_ACL_EDIT => $GLOBALS['phpgw']->acl->check($location_name, PHPGW_ACL_EDIT, $this->appname),
				PHPGW_ACL_DELETE => $GLOBALS['phpgw']->acl->check($location_name, PHPGW_ACL_DELETE, $this->appname),
				PHPGW_ACL_PRIVATE => $GLOBALS['phpgw']->acl->check($location_name, PHPGW_ACL_PRIVATE, $this->appname)
			);
		}

		/**
		 * Validate the object according to the database setup and custom rules.  This function
		 * can be overridden in subclasses.  It is then up to the subclasses to call this parent method
		 * in order to validate against the standard database rules.  The subclasses can in addition
		 * add their own specific validation logic.
		 *
		 * @return bool true if the object is valid, false otherwise
		 */
		public function validates()
		{
			return true;
		}

		public function check_consistency()
		{
			return true;
		}

		public function validate_numeric()
		{
			return true;
		}

		public function set_validation_error( string $rule_name, string $error_language_key )
		{
			$this->validation_errors[$rule_name] = $error_language_key;
		}

		public function get_validation_errors()
		{
			return $this->validation_errors;
		}

		public function set_validation_warning( string $warning_language_key )
		{
			$this->validation_warnings[] = $warning_language_key;
		}

		public function set_consistency_warning( string $warning_language_key )
		{
			$this->consistency_warnings[] = array('warning' => $warning_language_key);
		}

		public function get_consistency_warnings()
		{
			return $this->consistency_warnings;
		}

		public function get_validation_warnings()
		{
			return $this->validation_warnings;
		}

		/**
		 * Gets the value of the class attribute with the given name.  As such this function translates from
		 * string to variable.
		 *
		 * @param $field the name of the class attribute to get
		 * @return mixed the value of the attribute
		 */
		public function get_field( $field )
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
		public function set_field( $field, $value )
		{
			$this->{"$field"} = $value;
		}

		public abstract function serialize();

		public function toArray()
		{
			$rental_item_arr = array();

			$fields =  get_object_vars($this);
			foreach ($fields as $field => $value)
			{
				$rental_item_arr[$field] = $this->get_field($field);
			}
			return $rental_item_arr;
		}

		function validate( )
		{
			$errors = array();
			$this->preValidate( $this );
			$this->_validate( $this, array(), $errors);
			$this->doValidate( $this, $errors);
			foreach ($errors as $key => $message)
			{
				phpgwapi_cache::message_set($message, 'error');
			}
			return $errors ? false : true;
		}


		/**
		 * Implement in subclasses
		 */
		public static function get_custom_fields()
		{
			return array();
		}
		/**
		 * Implement in subclasses to perform actions on entity before validation
		 */
		protected function preValidate( &$entity )
		{

		}

		/**
		 * Implement in subclasses to perform custom validation.
		 */
		protected function doValidate( $entity,  &$errors )
		{

		}

		private function _validate( $entity, array $fields, array &$errors )
		{
			if(!$fields)
			{
				$fields = $this->get_fields();
			}
			foreach ($fields as $field => $params)
			{
				if (!is_array($params))
				{
					continue;
				}

				$value = $entity->get_field($field);
				if(!is_array($value))
				{
					$value = trim($value);
				}
				$empty = false;

				if (isset($params['manytomany']) && isset($params['manytomany']['column']))
				{
					$sub_entity_count = 0;

					if (!empty($value) && is_array($value))
					{
						foreach ($value as $key => $sub_entity)
						{
							$this->_validate(
//								(array)$sub_entity, (array)$params['manytomany']['column'], $errors, sprintf('%s%s[%s]', $field_prefix, empty($field_prefix) ? $field : "[{$field}]", (is_string($key) ? $key : $sub_entity_count))
								$entity, array($field => $params['manytomany']['column']), $errors
							);
							$sub_entity_count++;
						}
					}

					if ($params['required'] && $sub_entity_count == 0)
					{
						$errors[$field] = lang("Field %1 is required", lang($field));
					}
					continue;
				}

				if(!empty($params['alternative']) && is_array($params['alternative']))
				{
					$alternatives_ok = false;
					$found_alternatives = 0;
					foreach ($params['alternative'] as $alternative)
					{
						if($entity->get_field($alternative))
						{
							$found_alternatives ++;
						}
					}
					if($found_alternatives == count($params['alternative']))
					{
						$alternatives_ok = true;
					}
				}
				$error_key = empty($params['label']) ? $field : $params['label'];
				if ($params['required'] && (($value !== '0' && empty($value)) || empty($value) ) && !$alternatives_ok)
				{

					$errors[$error_key] = lang("Field %1 is required", lang($error_key));
					$empty = true;
				}
				if ($params['type'] == 'date' && !empty($value))
				{
					/**
					 * Already converted to integer
					 */
					//$date = date_parse($value);
					//if (!$date || count($date['errors']) > 0)
					if (!ctype_digit($value))
					{
						$errors[$error_key] = lang("Field %1: Invalid format", lang($error_key));
					}
				}

				if (!$empty && $params['sf_validator'])
				{
					try
					{
						$params['sf_validator']->setOption('required', false);
						$params['sf_validator']->clean($value);
					}
					catch (sfValidatorError $e)
					{
						$errors[$error_key] = lang(strtr($e->getMessage(), array('%field%' => $error_key)));
					}
				}
			}
		}
	}
