<?php
phpgw::import_class('rental.building');

/**
 * A floor in a building. Inherits all properties from a building.
 *
 */
class rental_floor extends rental_building
{
	protected $floor_name;
	
	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		parent::__construct($location_code, $location_id, $available_for_renting);
	}
	
	public function get_floor_name()
	{
		return $this->floor_name;
	}
	
	public function set_floor_name(string $floor_name)
	{
		$this->floor_name = (string)$floor_name;
	}
	
	public function __toString() {
        return 'floor[location code:'.$this->location_code.']';
    }
	
}
?>