<?php
/**
 * Abstract class that represents an unit (area) and its belonging data.
 *
 */
abstract class rental_unit
{

	protected $location_code;
	protected $location_id;
	protected $available_for_renting;
	protected $contract_date_array;
	protected $address;
	protected $area_gros;
	protected $area_net;
	
	public function __construct(string $location_code = null, int $location_id = null, $available_for_renting = true)
	{
		$this->location_code = (string)$location_code;
		$this->location_id = (int)$location_id;
		$this->available_for_renting = (bool)$available_for_renting;
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
	
	public function is_available_for_renting()
	{
		return $this->available_for_renting;
	}
	
	public function set_available_for_renting(bool $available_for_renting)
	{
		$this->available_for_renting = (bool)$available_for_renting;
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
		return $this->contract_date_array = array_merge($this->contract_date_array, (array)$contract_date_array);
	}
	
	public function __toString() {
        return 'unit[location code:'.$this->location_code.']';
    }

}
?>