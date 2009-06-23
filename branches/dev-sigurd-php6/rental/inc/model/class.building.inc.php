<?php
include_class('rental', 'property', 'inc/model/');

/**
 * A building in a property. Inherits all data from a property.
 *
 */
class rental_building extends rental_property
{
	protected $building_name;

	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		parent::__construct($location_code, $location_id, $available_for_renting);
	}
	
	public function get_building_name()
	{
		return $this->building_name;
	}
	
	public function set_building_name(string $building_name)
	{
		$this->building_name = (string)$building_name;
	}
	
	public function __toString() {
        return 'building[location code:'.$this->location_code.']';
    }
	
}
?>