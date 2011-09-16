<?php

abstract class controller_model
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
	
}
?>
