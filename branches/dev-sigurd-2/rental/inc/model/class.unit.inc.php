<?php
phpgw::import_class('rental.bocommon');

/**
 * Respresents one single unit. A unit is one single combination of one
 * composite from the rental module and one location from the property
 * module. 
 *
 */
class rental_unit
{
	protected static $so;

	protected $composite_id;
	// The property location that this unit represents
	protected $location;
	
	public function __construct($composite_id = -1, rental_property_location $location = null)
	{
		$this->composite_id = (int)$composite_id;
		$this->location = $location;
	}

	public function get_composite_id(){ return $this->composite_id; }

	public function set_location($location)
	{
		$this->location = $location;
	}

	public function get_location(){ return $this->location; }

	public function get_location_id()
	{
		if($this->location != null)
		{
			return $this->location->get_location_id();
		}
		return -1;
	}
	protected static function get_so()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socomposite');
		}
		return self::$so;
	}
		
	/**
	 * Returns a string representation of this object.
	 * 
	 * @return string with data about the object.
	 */
	public function __toString() {
        return 'unit[composite id:'.$this->composite_id.',location:'.$this->get_location().']';
	}
	
	public static function get_units_for_composite($id, $sort = null, $dir = 'asc', $start = 0, $results = null)
	{
		$so = self::get_so();
		$units = $so->get_included_rental_units($id, $sort = null, $dir = 'asc', $start = 0, $results = null);
		return $units;
	}
	
	/** 
	 * Get a list of all rental units that are not part of a rental_composite
	 * 
	 * @return 
	 * @param object $start_row[optional]
	 * @param object $num_of_rows[optional]
	 * @param object $sort_field[optional]
	 * @param object $sort_ascending[optional]
	 */
	public static function get_orphan_rental_units($start_row = 0, $num_of_rows = 1000, $sort_field = 'location_code', $sort_ascending = true)
	{
		$so = self::get_so();
		return $so->get_orphan_rental_units($start_row, $num_of_rows, $sort_field, $sort_ascending);
	}
	
	/**
	 * Get a count of all the orphan rental units.
	 * 
	 * @return 
	 */
	public static function get_orphan_rental_unit_count()
	{
		return self::get_so()->get_orphan_rental_unit_count();
	}
    
    public function serialize()
	{
		$result = array();
		$result['composite_id'] = $this->get_composite_id();
		$location = $this->get_location();
		if($location != null)
		{
			$result = array_merge($result, $location->serialize());
		}
		
		return $result;
	}
}
?>