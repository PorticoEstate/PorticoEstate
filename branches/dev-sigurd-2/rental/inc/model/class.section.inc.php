<?php
include_class('rental', 'floor', 'inc/model/');

/**
 * A section within a floor. Inherits all properties from a floor. Typically
 * it represents one or more rooms.
 *
 */
class rental_section extends rental_floor
{
	protected $section_name;

	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		parent::__construct($location_code, $location_id, $available_for_renting);
	}
	
	public function get_section_name()
	{
		return $this->section_name;
	}
	
	public function set_section_name(string $section_name)
	{
		$this->section_name = (string)$section_name;
	}
	
	public function __toString() {
        return 'section[location code:'.$this->location_code.']';
    }
	
}
?>