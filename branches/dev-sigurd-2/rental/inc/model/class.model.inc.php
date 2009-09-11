<?php

	include_class('rental', 'validator', 'inc/model/');

	abstract class rental_model
	{
		protected $validation_errors = array();
		protected $field_of_responsibility_id;
		protected $field_of_responsibility_name;
		protected $permission_array;

		/**
		 * Retrieve the name of the 'field of responsibility' this object belongs to.
		 * The default name is the root location (.)
		 * 
		 * @return the field name
		 */
		public function get_field_of_responsibility_name()
		{
			if(!isset($this->field_of_responsibility_name))
			{
				if(isset($this->field_of_responsibility_id))
				{
					$array = $GLOBALS['phpgw']->locations->get_name($this->field_of_responsibility_id);
					if($array['appname'] = $GLOBALS['phpgw_info']['flags']['currentapp']){
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
		public function has_permission($permission = PHPGW_ACL_PRIVATE)
		{
			return $GLOBALS['phpgw']->acl->check_rights($this->get_field_of_responsibility_name(),$permission);
		}
		
		/**
		 * Set the identifier for the field of responsibility this object belongs to
		 * 
		 * @param $id the ocation identifier
		 */
		public function set_field_of_responsibility_id($id)
		{
			$this->field_of_responsibility_id = $id;
		}
		
		/**
		 * Retrieve an array with the different permission levels the current user has for this object
		 * 
		 * @return an array with permissions [PERMISSION_BITMASK => true/false]
		 */
		protected function get_permission_array(){
			$location_name = $this->get_field_of_responsibility_name();
			return array (
				PHPGW_ACL_READ => $GLOBALS['phpgw']->acl->check_rights($location_name, PHPGW_ACL_READ),
				PHPGW_ACL_ADD => $GLOBALS['phpgw']->acl->check_rights($location_name, PHPGW_ACL_ADD),
				PHPGW_ACL_EDIT => $GLOBALS['phpgw']->acl->check_rights($location_name, PHPGW_ACL_EDIT),
				PHPGW_ACL_DELETE => $GLOBALS['phpgw']->acl->check_rights($location_name, PHPGW_ACL_DELETE),
				PHPGW_ACL_PRIVATE => $GLOBALS['phpgw']->acl->check_rights($location_name, PHPGW_ACL_PRIVATE)
			);	
		}
		
		/**
		* Store the object in the database.  If the object has no ID it is assumed to be new and
		* inserted for the first time.  The object is then updated with the new insert id.
		*/
		public function store()
		{
			if ($this->validates()) {
				$so = $this->get_so();
				if ($this->id > 0) {
					// We can assume this composite came from the database since it has an ID. Update the existing row
					return $so->update($this);
				}
				else
				{
					// This object does not have an ID, so will be saved as a new DB row
					return $so->add($this);
				}
			}

			// The object did not validate
			return false;
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
			// Read the database setup
			include('rental/setup/tables_current.inc.php');

			// Get the definition of the table of this object
			$table_def = $phpgw_baseline[$this->get_so()->table_name];

			$valid = true;

			$validator = new rental_validator($this);

			// Do the basic db-format checks for each attribute of this object
			foreach ($table_def['fd'] as $field => $field_def) {
				$value = $this->get_field($field);

				/*
				 * Required field
				 */
				if ((isset($field_def['nullable']) && $field_def['nullable'] == false ) && ($field_def['type'] != 'auto') && !rental_validator::valid_required($value)) {
					// TODO: language string
					$this->validation_errors[$field] = lang('messages_required_field');
					$valid = false;
				}

				/*
				 * Field type
				 */
//				var_dump($field_def['type']);
//				var_dump($field);
//				var_dump($value);
				if ($value && !rental_validator::valid_type($field_def['type'], $value, $this->validation_errors[$field])) {
					$valid = false;
				}

				/*
				 * Field length
				 */
				if ($value && !rental_validator::valid_length($type, $field_def['precision'], $value, $this->validation_errors[$field])) {
					$valid = false;
				}

			}

			return $valid;
		}

		public function set_validation_error_for_field($field, $error)
		{
			$this->validation_errors[$field] = $error;
		}

		public function get_validation_errors()
		{
			return $this->validation_errors;
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

		public abstract function serialize();
		
	}
?>