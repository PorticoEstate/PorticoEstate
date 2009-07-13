<?php
	/**
	 * Class that represents a rental composite
	 *
	 */
	
	phpgw::import_class('rental.bocommon');
	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	
	class rental_composite extends rental_model
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
		protected $postcode;
		protected $place;
		// XXX: What are all these custom fields? They are not used when updated from db..
		protected $custom_address_1;
		protected $custom_address_2;
		protected $custom_house_number;
		protected $custom_postcode;
		protected $custom_place;
		
		protected $gab_id;
		
		protected $area_gros;
		protected $area_net;
		
		protected $units;
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.socomposite');
			}
			
			return self::$so;
		}
	
		public function __construct(int $id = null)
		{
			$this->id = $id;
		}
		
		/**
		 * Return a single rental_composite object based on the provided id
		 * 
		 * @param $id rental composite id
		 * @return a rental_composite
		 */
		public static function get($id)
		{
			$so = self::get_so();
			
			return $so->get_single($id);
		}
		
		/**
		 * Return a list all of rental_composite objects that fits the provided arguments
		 * 
		 * @param $start		which index to start the list at
		 * @param $results	how many results to return
		 * @param $sort			sort column
		 * @param $dir			sort direction
		 * @param $query
		 * @param $search_option
		 * @param $filters
		 * @return a list of rental_composite objects
		 */
		public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
		{
			$so = self::get_so();
			
			$composites = $so->get_composite_array($start, $results, $sort, $dir, $query, $search_option, $filters);
			
			return $composites;
		}
		
		/**
		 * Add a new rental composite object to the store
		 * 
		 * @param $name		the name of the new composite
		 * @return the status of the operation
		 */
		public static function add($name)
		{
			$so = self::get_so();
			
			return $so->add($name);
		}
		
		public function add_unit($new_unit)
		{
			$units = $this->get_included_rental_units();
			
			$already_has_unit = false;
			
			foreach ($this->get_included_rental_units() as $unit) {
				if ($unit->get_location_id() == $new_unit->get_location_id()) {
					$already_has_unit == true;
				}
			}
			
			if (!$already_has_unit) {
				$this->units[] = $new_unit;
			}
		}
		
		public function remove_unit($unit_to_remove)
		{
			$units = $this->get_included_rental_units();
			
			foreach ($this->get_included_rental_units() as $index => $unit) {
				if ($unit->get_location_id() == $unit_to_remove->get_location_id()) {
					unset($this->rental_units[$index]);
				}
			}
		}
		
		/**
		 * Store the composite in the database.  If the composite has no ID it is assumed to be new and
		 * inserted for the first time.  The composite is then updated with the new insert id.
		 */
		public function store()
		{
			$so = self::get_so();
			
			if ($this->id) {
				// We can assume this composite came from the database since it has an ID. Update the existing row
				$so->update($this);
			} 
			else
			{
				// This object does not have an ID, so will be saved as a new DB row
				$so->add($this);
			}
		}
	
		public function get_included_rental_units($sort = null, $dir = 'asc', $start = 0, $results = null)
		{
			if (!$this->units) {
				$this->units = rental_unit::get_units_for_composite($this->get_id(), $sort, $dir, $start, $results);
			}
			return $this->units; 
		}
	
		/**
		 * Get the contracts associated with this composite
		 * 
		 * @return an array of rental_contract objects
		 * @param object $sort[optional]
		 * @param object $dir[optional]
		 * @param object $start[optional]
		 * @param object $results[optional]
		 * @param object $status[optional]
		 * @param object $date[optional]
		 */
		public function get_contracts($sort = null, $dir = 'asc', $start = 0, $results = null, $status = null, $date = null)
		{
			if (!$this->contracts) {
				$this->contracts = rental_contract::get_contracts_for_composite($this->get_id(), $sort, $dir, $start, $results, $status, $date);
			}
			return $this->contracts;
		}
		
		/**
		 * Check if the composite is vacant at the given date.  If no date is given, today is used as the date.
		 * 
		 * @return true if the composite is vacant, false otherwise
		 * @param object $date[optional]
		 */
		public function is_vacant($date = null)
		{
			if (!$date) {
				// No date to check was specified, so check for right now
				$date = mktime(00,00,00);
			}
			
			foreach ($this->get_contracts() as $contract) {
				$start_date = strtotime($contract->get_contract_date()->get_start_date());
				$end_date = strtotime($contract->get_contract_date()->get_end_date());
				
				if (($date > $start_date) && ($date < $end_date)) {
					return false;
				}
			}
			
			return true;
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
		
		public function set_postcode($postcode)
		{
			$this->postcode = $postcode;
		}
		
		public function get_postcode() { return $this->postcode; }
		
		public function set_place($place)
		{
			$this->place = $place;
		}
		
		public function get_place() { return $this->place; }
		
		public function set_custom_address_1($custom_address)
		{
			$this->custom_address_1 = $custom_address;
		}
		
		public function get_custom_address_1() { return $this->custom_address_1; }
		
		public function set_custom_address_2($custom_address)
		{
			$this->custom_address_2 = $custom_address;
		}
		
		public function get_custom_address_2() { return $this->custom_address_2; }
		
		public function set_custom_house_number($custom_house_number)
		{
			$this->custom_house_number = $custom_house_number;
		}
		
		public function get_custom_house_number() { return $this->custom_house_number; }
		
		public function set_custom_postcode($custom_postcode)
		{
			$this->custom_postcode = $custom_postcode;
		}
		
		public function get_custom_postcode() { return $this->custom_postcode; }
		
		public function set_custom_place($custom_place)
		{
			$this->custom_place = $custom_place;
		}
		
		public function get_custom_place() { return $this->custom_place; }
		
		public function set_gab_id($gab_id)
		{
			$this->gab_id = $gab_id;
		}
		
		public function get_gab_id() { return $this->gab_id; }
	
		public function set_area_gros($area)
		{
			$this->area_gros = $area;
		}
		
		public function get_area_gros() { return $this->area_gros; }
		
		public function set_area_net($area)
		{
			$this->area_net = $area;
		}
		
		public function get_area_net() { return $this->area_net; }
		
		function __toString()
		{
			$result  = '{';
			
			$result .= '"id":"' . $this->get_id() . '",';
			$result .= '"name":"' . $this->get_name() . '"';
			
			$result .= '}';
			
			return $result;
		}
	}
?>