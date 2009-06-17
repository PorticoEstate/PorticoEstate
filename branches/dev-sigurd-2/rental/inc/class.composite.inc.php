<?php
/**
 * Class that represents a rental composite
 *
 */

phpgw::import_class('rental.bocommon');
phpgw::import_class('rental.unit');

class rental_composite
{
	public static $so;
	
	protected $id;
	protected $description;
	protected $is_active;
	protected $name;
	protected $has_custom_address;
	protected $address_1;
	protected $address_2;
	protected $house_number;
	protected $place;
	protected $gab_id;
	protected $date_from;
	protected $date_to;
	
	protected $units;
	
	public static function get_so()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socomposite');
		}
		
		return self::$so;
	}
	
	public static function get($id)
	{
		$so = self::get_so();
		
		$composite_data = $so->read_single(array('id' => $id));
		
		$composite = new self();
		$composite->set_id($composite_data['id']);
		$composite->set_description($composite_data['description']);
		$composite->set_is_active($composite_dataa['is_active']);
		$composite->set_name($composite_data['name']);
		$composite->set_has_custom_address($composite_data['has_custom_address']);
		
		$composite->set_address_1($composite_data['address_1']);
		$composite->set_address_2($composite_data['address_2']);
		$composite->set_house_number($composite_data['house_number']);
		$composite->set_postcode($composite_data['postcode']);
		$composite->set_place($composite_data['place']);
		
		$composite->set_adresse1($composite_data['addresse1']);
		
		
		$composite->set_gab_id($composite_data['gab_id']);
		$composite->set_date_from($composite_data['date_from']);
		$composite->set_date_to($composite_data['date_to']);

		/* TODO
		'adresse1' => array('type' => 'string'),
					'adresse2' => array('type' => 'string'),
					'postnummer' => array('type' => 'int'),
					'poststed' => array('type' => 'string'),
					*/
		
		return $composite;
	}
	
	public function get_units()
	{
		if (!$this->units) {
			$this->units = rental_unit::get_units_for_composite($this->get_id());
		}
		
		return $this->units; 
	}
	
	public function set_id($id)
	{
		$this->id = $id;
	}
	
	public function get_id() { return $this->id; }
	
	public function set_description($description)
	{
		$this->description = $description;
	}
	
	public function get_description() { return $this->description; }
	
	public function set_is_active($is_active)
	{
		$this->is_active = $is_active;
	}
	
	public function is_active() { return $this->is_active;	}
	
	public function set_name($name)
	{
		$this->name = $name;
	}
	
	public function get_name() { return $this->name; }
	
	public function set_has_custom_address($has_custom_address)
	{
		$this->has_custom_address = $has_custom_address;
	}
	
	public function has_custom_address() { return $this->has_custom_address; }
	
	public function set_address_1($address)
	{
		$this->address_1 = $address;
	}
	
	public function get_address_1() { return $this->address_1; }
	
	public function set_address_2($address)
	{
		$this->address_2 = $address;
	}
	
	public function get_address_2() { return $this->address_2; }
	
	public function set_house_number($house_number)
	{
		$this->house_number = $house_number;
	}
	
	public function get_house_number() { return $this->house_number; }
	
public function set_place($place)
	{
		$this->place = $place;
	}
	
	public function get_place() { return $this->place; }
	
	public function set_gab_id($gab_id)
	{
		$this->gab_id = $gab_id;
	}
	
	public function get_gab_id() { return $this->gab_id; }
	
	public function set_date_from($date_from)
	{
		$this->date_from = $date_from;
	}
	
	public function get_date_from() { return $this->date_from; }
	
	public function set_date_to($date_to)
	{
		$this->date_to = $date_to;
	}
	
	public function get_date_to() { return $this->date_to; }
	
	function __toString()
	{
		$result  = '{';
		
		$result .= '"id":"' . $this->get_id() . '",';
		$result .= '"name":"' . $this->get_name() . '"';
		
		$result .= '}';
	}
}
?>