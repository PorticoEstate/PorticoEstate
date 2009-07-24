<?php

	include_class('rental', 'validator', 'inc/model/');

	abstract class rental_model
	{
		protected $validation_errors = array();
		
		/**
		* Store the object in the database.  If the object has no ID it is assumed to be new and
		* inserted for the first time.  The object is then updated with the new insert id.
		*/
		public function store()
		{
			if ($this->validates()) {
				$so = $this->get_so();
				
				if ($this->id) {
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
		 * Validate the object according to the database setup and custom rules
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
				if (!$field_def['nullable'] && ($field_def['type'] != 'auto') && !rental_validator::valid_required($value)) {
					// TODO: language string
					$this->validation_errors[$field] = lang('rental_messages_required_field');
					$valid = false;
				}
			
				/*
				 * Field type
				 */
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
		
		public abstract function serialize();
	}
?>