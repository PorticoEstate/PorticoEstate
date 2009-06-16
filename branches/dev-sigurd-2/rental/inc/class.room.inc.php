<?php
phpgw::import_class('rental.section');

/**
 * A room within a section. Inherits all properties from a building.
 *
 */
class rental_room extends rental_section
{
	protected $room_name;
	
	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		parent::__construct($location_code, $location_id, $available_for_renting);
	}
	
	public function get_room_name()
	{
		return $this->room_name;
	}
	
	public function set_room_name(string $room_name)
	{
		$this->room_name = (string)$room_name;
	}
	
	public function __toString() {
        return 'room[location code:'.$this->location_code.']';
    }
	
}
?>