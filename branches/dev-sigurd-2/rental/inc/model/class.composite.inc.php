<?php
	include_class('rental', 'model', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	
	/**
	 * Class that represents a rental composite
	 *
	 */
	class rental_composite extends rental_model
	{
		protected $name;
		protected $description;
		protected $is_active;
		protected $has_custom_address;
		// These are custom fields that may be set on the composite
		protected $custom_address_1;
		protected $custom_address_2;
		protected $custom_house_number;
		protected $custom_postcode;
		protected $custom_place;
		
		protected $units;
	
		/**
		 * Constructor.  Takes an optional ID.  If a composite is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			parent::__construct($id);
			$this->units = array();
		}
		
		/**
		 * Adds a composite to the composite object. Note that this method is
		 * meant for populating the object and will not fetch anything from
		 * the database.
		 * @param $unit to add to object.
		 */
		public function add_unit($new_unit)
		{
			$this->units[] = $new_unit;
		}
		
		/**
		 * Checks if a unit is already added to the composite.
		 * 
		 * @param $location_code string with location code.
		 * @return boolean true if unit is added, false if not.
		 */
		public function contains_unit($location_code)
		{
			foreach($this->units as $unit)
			{
				if($location_code == $unit->get_location_code())
				{
					return true;
				}
			}
			return false;
		}
		
		/**
		 * Remove a given rental unit from this rental_composite. Note that the composite is not updated
		 * in the database until store() is called.
		 * 
		 * @param $unit_to_remove the rental_unit to remove
		 */
		public function remove_unit($unit_to_remove)
		{
			$units = $this->get_units();
			
			foreach ($this->get_units() as $index => $unit) {
				if ($unit->get_location_id() == $unit_to_remove->get_location_id()) {
					unset($this->rental_units[$index]);
				}
			}
		}
		
		/**
		 * Get the rental_unit objects associated with this composite
		 * 
		 * @param $sort the name of the column to sort by
		 * @param $dir the sort direction, 'asc' or 'desc'
		 * @param $start which row number to start returning results from
		 * @param $results how many results to return
		 * @return rental_unit[]
		 */
		public function get_units($sort = null, $dir = 'asc', $start = 0, $results = null)
		{
			return $this->units; 
		}
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_is_active($is_active)
		{
			$this->is_active = (boolean)$is_active;
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
		
		public function set_custom_postcode($custom_postcode)
		{
			$this->custom_postcode = $custom_postcode;
		}
		
		public function set_custom_address_1($custom_address_1)
		{
			$this->custom_address_1 = $custom_address_1;
		}
	
		public function get_custom_address_1(){ return $this->custom_address_1; }
			
		public function set_custom_address_2($custom_address_2)
		{
			$this->custom_address_2 = $custom_address_2;
		}
	
		public function get_custom_address_2(){ return $this->custom_address_2; }
			
		public function get_custom_postcode() { return $this->custom_postcode; }
		
		public function set_custom_place($custom_place)
		{
			$this->custom_place = $custom_place;
		}
		
		public function set_custom_house_number($custom_house_number)
		{
			$this->custom_house_number = $custom_house_number;
		}
	
		public function get_custom_house_number(){ return $this->custom_house_number; }
		
		public function get_custom_place() { return $this->custom_place; }
		
		public function get_area_gros() {
			$area = 0;
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$area += $location->get_area_gros() ;
				}
			}
			return $area;
		}
		
		public function get_area_net() {
			$area = 0;
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$area += $location->get_area_net() ;
				}
			}
			return $area;

		}
		
		/**
		 * Return a string representation of the composite.
		 * 
		 * @return string
		 */
		function __toString()
		{
			$result  = '{';
			$result .= '"id":"' . $this->get_id() . '",';
			$result .= '"name":"' . $this->get_name() . '"';
			$result .= '}';
			return $result;
		}
		
		public function serialize()
		{
			$addresses = '';
			$location_codes = '';
			$gab_ids = '';
			foreach($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if($location != null) // There is an underlying property location
				{
					$addresses .= $location->get_address_1() . "<br>\n";
					$location_codes .= $location->get_location_code() . "<br>\n";
					$gab_ids .= $location->get_gab_id() . "<br>\n";
				}
			}
			if($this->has_custom_address())
			{
				$addresses = $this->get_custom_address_1() . ' ' . $this->get_custom_house_number();
			}
			return array(
				'id' => $this->get_id(),
				'location_code' => $location_codes,
				'description' => $this->get_description(),
				'is_active' => $this->is_active(),
				'name' => $this->get_name(),
				'address' => $addresses,
				'gab_id' => $gab_ids,
				'area_gros' => $this->get_area_gros(),
				'area_net' => $this->get_area_net()
			);
		} 
	}
?>
