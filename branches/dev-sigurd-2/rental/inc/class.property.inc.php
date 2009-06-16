<?php
phpgw::import_class('rental.unit');

class rental_property extends rental_unit
{
	protected $property_name;
	
	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		parent::__construct($location_code, $location_id, $available_for_renting);
	}
	
	public function get_property_name()
	{
		return $this->property_name;
	}
	
	public function set_property_name(string $property_name)
	{
		$this->property_name = (string)$property_name;
	}
	
}
?>