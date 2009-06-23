<?php
phpgw::import_class('rental.bocommon');
include_class('rental', 'contract_date', 'inc/model/');

/**
 * Abstract class that represents an unit (area) and its belonging data.
 *
 */
abstract class rental_unit
{
	protected static $so;

	protected $location_code;
	protected $location_id;
	protected $contract_date_array;
	protected $address;
	protected $area_gros;
	protected $area_net;
	protected $composite_id_array;
	
	protected static function get_so()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socomposite');
		}
		return self::$so;
	}
	
	public function __construct(string $location_code = null, int $location_id = null)
	{
		$this->location_code = (string)$location_code;
		$this->location_id = (int)$location_id;
		$this->contract_date_array = array();
		$this->composite_id_array = array();
	}
	
	public function get_location_code()
	{
		return $this->location_code;
	}
	
	public function get_location_id()
	{
		return $this->location_id;
	}
	
	public function get_address()
	{
		return $this->address;
	}
	
	public function set_address(string $address)
	{
		$this->address = (string)$address;
	}
	
	public function get_area_net()
	{
		return $this->area_net;
	}
	
	public function set_area_net(int $area_net)
	{
		$this->area_net = (int)$area_net;
	}
	
	public function get_area_gros()
	{
		return $this->area_gros;
	}
	
	public function set_area_gros(int $area_gros)
	{
		$this->area_gros = (int)$area_gros;
	}
	
	public function get_composite_id_array()
	{
		return $this->composite_id_array;
	}
	
	public function add_composite_id(int $composite_id)
	{
		if(!$this->has_composite_id($composite_id))
		{
			$this->composite_id_array[] = $composite_id;
		}
	}
	
	public function has_composite_id(int $composite_id)
	{
		return in_array($composite_id, $this->composite_id_array);
	}
	
	/**
	 * Returns an array of the contract dates registered on the unit.
	 * 
	 * @return array of rental_contract_date objects.
	 */
	public function get_contract_date_array()
	{
		return $this->contract_date_array;
	}
	
	/**
	 * Replaces the old set of rental_contract_date objects with a new one.
	 * 
	 * @param $contract_date_array array of rental_contract_date objects to add.
	 */
	public function set_contract_date_array(array $contract_date_array)
	{
		$this->contract_date_array = (array)$contract_date_array;
	}
	
	/**
	 * Adds an array of contract date to the unit. Will not add any dates already
	 * existing for the unit.
	 * 
	 * @param $contract_date_array array of rental_contract_date to add.
	 */
	public function add_contract_date_array(array $contract_date_array)
	{
		if($contract_date_array == null || count($contract_date_array) == 0) // No contents in array
		{
			return;
		}
		foreach($contract_date_array as $contract_date)
		{
			$this->add_contract_date($contract_date);
		}
	}
	
	/**
	 * Adds an contract date to the unit. Will not add the date if it already exists.
	 * 
	 * @param $new_contract_date rental_contract_date to add.
	 */
	public function add_contract_date(rental_contract_date $new_contract_date)
	{
		foreach($this->contract_date_array as $contract_date)
		{
			if($new_contract_date == $contract_date) // We already have that date
			{
				return; // Return without adding the date
			}
		}
		$this->contract_date_array[] = $new_contract_date;
	}
	
	/**
	 * Returns an array of contract_date objects for when the contract is occupied.
	 * Returns null if the unit is not available at all. Returns an empty array if
	 * it's available from the date specified (or today if no date specified) and
	 * forever. The array is sorted on the start date.
	 * 
	 * @param $date string with date (YYYY-MM-DD). Will use today's date if not specified.
	 * @return array of contract_date objects for when the unit is occupied, an
	 * empty array if it's available at all times from the date used, null if it's
	 * completly booked.
	 */
	public function get_occupied_date_array(string $date = null)
	{
		$occupied_date_array = array();
		if($date == null) // Date not specified..
		{
			$date = date('Y-m-d'); // ..use today
		}
		foreach($this->contract_date_array as $contract_date) // Runs through all contract dates
		{
			if($contract_date->has_start_date() && !$contract_date->has_end_date()) // Start date is set and end date is not set - contract is running
			{
				return null; // Unit is not available
			}
			if(!$contract_date->has_start_date() && !$contract_date->has_end_date()) // No dates set (contract probably not finished)
			{
				continue; // Jump to next date - this one doesn't affect us
			}
			if($contract_date->get_end_date() < $date) // End date was before the date we're looking for
			{
				continue; // Jump to next date - this one doesn't affect us
			}
			// We create a key for sorting the dates
			$array_key = ($contract_date->has_start_date() ? $contract_date->get_start_date() : '1970-01-01').'-'.$contract_date->get_end_date();
			$occupied_date_array[$array_key] = $contract_date;
		}
		ksort($occupied_date_array); // We sort the keys on dates
		return $this->get_spanned_date_array($occupied_date_array);
	}
	
	/**
	 * Takes an array of rental_contract_date objects sorted chronologically, 
	 * finds any overlapping dates, and returns an array where the dates have
	 * been spanned.
	 *  
	 * @param $occupied_date_array array of rental_contract_date objects to span.
	 * @return array of rental_contract_date objects.
	 */
	protected function get_spanned_date_array(array $occupied_date_array)
	{
		$spanned_date_array = array();
		if($occupied_date_array != null && count($occupied_date_array) > 0)
		{
			$array_keys = array_keys($occupied_date_array);
			$count = count($occupied_date_array);
			for($i = 0; $i < $count - 1; $i++)
			{
				$contract_date_1 = $occupied_date_array[$array_keys[$i]];
				$contract_date_2 = $occupied_date_array[$array_keys[$i + 1]];
				if($contract_date_1->get_end_date() > $contract_date_2->get_start_date()) // First end date after second start date 
				{
					// We create a new date for this span and replace the second one so we can compare it in the next iteration
					$occupied_date_array[$array_keys[$i + 1]] = new rental_contract_date($contract_date_1->get_start_date(), max($contract_date_1->get_end_date(), $contract_date_2->get_end_date()));
				}
				else // Can't span date
				{
					$spanned_date_array[] = $contract_date_1;
				}
			}
			// We don't reach the last element in the for loop so we add it here
			$spanned_date_array[] = $occupied_date_array[$array_keys[$count -1]];
		}
		return $spanned_date_array;
	}
	
	/**
	 * Tells if a unit is available for renting. If no date is specified it
	 * tells if it's available  at some time in the future, and if a date is
	 * specified it tells if the unit is available at that exact date. This first
	 * of the two is done by checking if there are any running contracts without
	 * end date.
	 * 
	 * @return bool with true if the unit is available for renting at, false if
	 * not.
	 */
	public function is_available_for_renting($avaiable_at_date = null)
	{
		if($avaiable_at_date == null || $avaiable_at_date == '') // Date not specified
		{
			foreach($this->contract_date_array as $contract_date) // Runs through all contract dates
			{
				if($contract_date->has_start_date() && !$contract_date->has_end_date()) // Start date is set and end date is not set - contract is running
				{
					return false;
				}
			}
		}
		else // Date specified
		{
			foreach($this->contract_date_array as $contract_date) // Runs through all contract dates
			{
				// Start date is set (contract isn't a draft) and start date is before the date we're checking for and either end date isn't set or end date is after the date we're checking for.
				if($contract_date->has_start_date() && $contract_date->get_start_date() <= $avaiable_at_date && (!$contract_date->has_end_date() || ($contract_date->has_end_date() && $contract_date->get_end_date() > $avaiable_at_date)))
				{
					return false;
				}
			}
		}
		return true;
	}
	
	/**
	 * Returns a string representation of this object.
	 * 
	 * @return string with data about the object.
	 */
	public function __toString() {
        return 'unit[location code:'.$this->location_code.']';
	}
	
	public static function get_units_for_composite($id, $sort = null, $dir = 'asc', $start = 0, $results = null)
	{
		$so = self::get_so();
		$units = $so->get_included_rental_units($id, $sort = null, $dir = 'asc', $start = 0, $results = null);
		return $units;
	}
    
	/**
	 * 
	 * @param $type int 1-5 with type of units to return.
	 * @return array with rental_unit objects.
	 */
	public static function get_available_rental_units(int $level, int $composite_id, string $avaiable_at_date = null, $start_row = 0, $num_of_rows = 25, $sort_field = 'location_code', $sort_ascending = true)
	{
		$level = (int)$level;
		// First we get all areas on the level we're currently on
		$unit_array = rental_unit::get_so()->get_unit_array($level, null, 0, 10000, $sort_field, $sort_ascending); // These are the elements the user expects to see
		$available_unit_array = array();
		foreach($unit_array as $unit) // We run through each area
		{
			if(!$unit->has_composite_id($composite_id) && $unit->is_available_for_renting($avaiable_at_date)) // Unit doesn't already belong to specified composite and there are openings on this unit at specified time
			{
				$add_unit = true; // Tells if we should add unit to list of available units
				for($i = 1; $i <= 5; $i++) // Runs through from top (property) to bottom (unit)
				{
					if($i != $level) // Not the level we already have data for
					{
						$related_unit_array = rental_unit::get_so()->get_unit_array($i, $unit->get_location_code(), 0, 10000, null, true);
						foreach($related_unit_array as $related_unit)
						{
							if(!$related_unit->has_composite_id($composite_id) && $related_unit->is_available_for_renting($avaiable_at_date)) // Unit doesn't already belong to specified composite and there are openings on this unit at specified time
							{
								// We add the contract dates from the related units to see at what time it's possible to rent the unit
								$unit->add_contract_date_array($related_unit->get_contract_date_array());
							}
							else // Nothing available
							{
								$add_unit = false;
								break 2; // No reason to continue
							}
						}
					}
				}
				if($add_unit) // We should add unit
				{
					// Unit is available for renting, so we add it to the array
					$available_unit_array[] = $unit;
				}
			}
		}
		if(count($available_unit_array) > $num_of_rows) // We've found more units than asked for
		{
			$available_unit_array = array_slice($available_unit_array, (int)$start_row, $num_of_rows);
		}
		return $available_unit_array;
	}

}
?>