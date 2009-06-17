<?php
phpgw::import_class('rental.contract_date');

/**
 * Abstract class that represents an unit (area) and its belonging data.
 *
 */
abstract class rental_unit
{

	protected $location_code;
	protected $location_id;
	protected $contract_date_array;
	protected $address;
	protected $area_gros;
	protected $area_net;
	
	public function __construct(string $location_code = null, int $location_id = null)
	{
		$this->location_code = (string)$location_code;
		$this->location_id = (int)$location_id;
		$this->contract_date_array = array();
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
	
	public function get_contract_date_array()
	{
		return $this->contract_date_array;
	}
	
	public function set_contract_date_array(array $contract_date_array)
	{
		$this->contract_date_array = (array)$contract_date_array;
	}
	
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
	
	public function is_available_for_renting()
	{
		foreach($this->contract_date_array as $contract_date) // Runs through all contract dates
		{
			if($contract_date->has_start_date() && !$contract_date->has_end_date()) // Start date is set and end date is not set - contract is running
			{
				return false;
			}
		}
		return true;
	}
	
	public function __toString() {
        return 'unit[location code:'.$this->location_code.']';
    }

}
?>