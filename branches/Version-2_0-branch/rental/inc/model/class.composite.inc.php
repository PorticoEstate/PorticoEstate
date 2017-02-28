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
		const STATUS_ACTIVE = 1;
		const STATUS_INACTIVE = 2;
		const STATUS_EXPIRED = 3;


		protected $name;
		protected $description;
		protected $is_active;
		protected $status_id;
		protected $has_custom_address;
		// These are custom fields that may be set on the composite
		protected $custom_address_1;
		protected $custom_address_2;
		protected $custom_house_number;
		protected $custom_postcode;
		protected $custom_place;
		protected $object_type_id;
		protected $area;
		protected $status;
		protected $furnish_type_id;
		protected $standard_id;
		protected $composite_type_id;
		protected $units;
		protected $contracts;
		protected $part_of_town_id;
		protected $custom_price_factor = '1.00';
		protected $custom_price;
		protected $price_type_id;
		protected static $furnish_types_arr;

		/**
		 * Constructor.  Takes an optional ID.  If a composite is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct( int $id = null)
		{
			parent::__construct((int)$id);
			$this->units = array();
			$this->contracts = array();
		}

		public static function get_status_list()
		{

			return array(
				self::STATUS_ACTIVE => lang('active'),
				self::STATUS_INACTIVE	=> lang('inactive'),
				self::STATUS_EXPIRED => lang('expired'),
			);
		}
		/**
		 * Adds a composite to the composite object. Note that this method is
		 * meant for populating the object and will not fetch anything from
		 * the database.
		 * @param $unit to add to object.
		 */
		public function add_unit( $new_unit )
		{
			$this->units[] = $new_unit;
		}

		/**
		 * Adds a contract to the contracts array sorted by end date. Note that this method is
		 * meant for populating the object and will not fetch/insert anything from
		 * the database.
		 * @param $unit to add to object.
		 */
		public function add_contract( $new_contract )
		{
			$temp_contracts = array();
			$added = false;

			foreach ($this->contracts as $contract)
			{
				if ($added == false & $contract->get_contract_date()->get_end_date() == 0)
				{
					$temp_contracts[] = $new_contract;
					$temp_contracts[] = $contract;
					$added = true;
				}
				else if ($added == false & $new_contract->get_contract_date()->get_end_date() == 0)
				{
					$temp_contracts[] = $contract;
					$temp_contracts[] = $new_contract;
					$added = true;
				}
				else if ($added == false & $contract->get_contract_date()->get_end_date() < $new_contract->get_contract_date()->get_end_date())
				{
					$temp_contracts[] = $contract;
				}
				else if ($added == false & !$contract->get_contract_date()->get_end_date() < $new_contract->get_contract_date()->get_end_date())
				{
					$temp_contracts[] = $new_contract;
					$temp_contracts[] = $contract;
					$added = true;
				}
				else if ($added == true)
				{
					$temp_contracts[] = $contract;
				}
			}

			if ($added == false)
			{
				$temp_contracts[] = $new_contract;
			}

			$this->contracts = &$temp_contracts;
		}

		/**
		 * Checks if a unit is already added to the composite.
		 * 
		 * @param $location_code string with location code.
		 * @return bool true if unit is added, false if not.
		 */
		public function contains_unit( $location_code )
		{
			foreach ($this->units as $unit)
			{
				if ($location_code == $unit->get_location_code())
				{
					return true;
				}
			}
			return false;
		}

		/**
		 * Checks if a contract is already added to the composite.
		 * 
		 * @param $contract_id int with contract id.
		 * @return bool true if contract is added, false if not.
		 */
		public function contains_contract( $contract_id )
		{
			foreach ($this->contracts as $contract)
			{
				if ($contract_id == $contract->get_id())
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
		public function remove_unit( $unit_to_remove )
		{
			$units = $this->get_units();

			foreach ($this->get_units() as $index => $unit)
			{
				if ($unit->get_location_id() == $unit_to_remove->get_location_id())
				{
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
		public function get_units( $sort = null, $dir = 'asc', $start = 0, $results = null )
		{
			return $this->units;
		}

		public function set_description( $description )
		{
			$this->description = $description;
		}

		public function get_description()
		{
			return $this->description;
		}
		public function set_status_id( $status_id )
		{
			$this->status_id = $status_id;
		}

		public function get_status_id()
		{
			return $this->status_id;
		}

		public function set_is_active( $is_active )
		{
			$this->is_active = (bool)$is_active;
		}

		public function is_active()
		{
			return $this->is_active;
		}

		public function set_name( $name )
		{
			$this->name = $name;
		}

		public function get_name()
		{
			return $this->name;
		}

		public function set_has_custom_address( $has_custom_address )
		{
			$this->has_custom_address = $has_custom_address;
		}

		public function has_custom_address()
		{
			return $this->has_custom_address;
		}

		public function set_custom_postcode( $custom_postcode )
		{
			$this->custom_postcode = $custom_postcode;
		}

		public function set_custom_address_1( $custom_address_1 )
		{
			$this->custom_address_1 = $custom_address_1;
		}

		public function get_custom_address_1()
		{
			return $this->custom_address_1;
		}

		public function set_custom_address_2( $custom_address_2 )
		{
			$this->custom_address_2 = $custom_address_2;
		}

		public function get_custom_address_2()
		{
			return $this->custom_address_2;
		}

		public function get_custom_postcode()
		{
			return $this->custom_postcode;
		}

		public function set_custom_place( $custom_place )
		{
			$this->custom_place = $custom_place;
		}

		public function set_custom_house_number( $custom_house_number )
		{
			$this->custom_house_number = $custom_house_number;
		}

		public function get_custom_house_number()
		{
			return $this->custom_house_number;
		}

		public function get_custom_place()
		{
			return $this->custom_place;
		}

		public function get_area_gros()
		{
			$area = 0;
			foreach ($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if ($location != null) // There is an underlying property location
				{
					$area += $location->get_area_gros();
				}
			}
			return $area;
		}

		public function get_area_net()
		{
			$area = 0;
			foreach ($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();
				if ($location != null) // There is an underlying property location
				{
					$area += $location->get_area_net();
				}
			}
			return $area;
		}

		public function set_object_type_id( int $obj_type )
		{
			$this->object_type_id = $obj_type;
		}

		public function get_object_type_id()
		{
			return (int)$this->object_type_id;
		}

		public function set_furnish_type_id( $furnish_type )
		{
			$this->furnish_type_id = (int)$furnish_type;
		}

		public function get_furnish_type_id()
		{
			return (int)$this->furnish_type_id;
		}

		public function set_part_of_town_id(  $part_of_town_id )
		{
			$this->part_of_town_id = $part_of_town_id;
		}

		public function get_part_of_town_id()
		{
			return $this->part_of_town_id;
		}

		public function set_custom_price_factor( $custom_price_factor )
		{
			$this->custom_price_factor = (float)$custom_price_factor;
		}

		public function get_custom_price_factor()
		{
			return (float)$this->custom_price_factor;
		}

		public function get_furnish_type()
		{

			$furnish_types = $this->get_furnish_types();

			return $furnish_types[$this->get_furnish_type_id()];
		}

		public static function get_furnish_types()
		{

			self::$furnish_types_arr = array(
				0 => lang('furnish_type_not_specified'),
				1 => lang('furnish_type_furnished'),
				2 => lang('furnish_type_partly_furnished'),
				3 => lang('furnish_type_not_furnished')
			);

			return self::$furnish_types_arr;
		}

		public function set_standard_id( $standard_id )
		{
			$this->standard_id = (int)$standard_id;
		}

		public function get_standard_id()
		{
			return (int)$this->standard_id;
		}
		public function set_composite_type_id( $composite_type_id )
		{
			$this->composite_type_id = (int)$composite_type_id;
		}

		public function get_composite_type_id()
		{
			return (int)$this->composite_type_id;
		}

		public function set_custom_price( $custom_price )
		{
			$this->custom_price = (float)$custom_price;
		}

		public function get_custom_price()
		{
			return (float)$this->custom_price;
		}

		public function set_price_type_id( $price_type_id )
		{
			$this->price_type_id = (int)$price_type_id;
		}

		public function get_price_type_id()
		{
			return (int)$this->price_type_id;
		}

		/**
		 * Fetch composite standards on the form array(array('id' => 1, 'name' => 'some text', 'selected' => 1|0))
		 * @return array
		 */
		public function get_standards( $selected )
		{
			if ($composite_standards = execMethod('rental.bogeneric.get_list', array('type' => 'composite_standard',
				'selected' => $selected)))
			{
				array_unshift($composite_standards, array('id' => '', 'name' => lang('none')));
			}
			return $composite_standards;
		}

		/**
		 * Fetch composite types on the form array(array('id' => 1, 'name' => 'some text', 'selected' => 1|0))
		 * @return array
		 */
		public function get_types( $selected )
		{
			if ($composite_types = execMethod('rental.bogeneric.get_list', array('type' => 'composite_type',
				'selected' => $selected)))
			{
				array_unshift($composite_types, array('id' => '', 'name' => lang('none')));
			}
			return $composite_types;
		}

		public function set_area( $area )
		{
			$this->area = $area;
		}

		public function get_area()
		{
			return $this->area;
		}

		public function set_status( $status )
		{
			$this->status = $status;
		}

		public function get_status()
		{
			return $this->status;
		}

		public function set_contracts( $contracts )
		{
			$this->contracts = $contracts;
		}

		public function get_contracts()
		{
			return $this->contracts;
		}

		/**
		 * Return a string representation of the composite.
		 * 
		 * @return string
		 */
		function __toString()
		{
			$result = '{';
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
			$contract_dates = '';
			foreach ($this->get_units() as $unit) // Runs through all of the composites units
			{
				$location = $unit->get_location();

				if ($location != null) // There is an underlying property location
				{
					$address = $location->get_address_1();
					if (isset($address) && $address != '')
					{
						$addresses .= $address . "<br>\n";
					}
					else
					{
						$addresses .= $location->get_concat_name() . "<br/>\n";
					}
					$location_codes .= $location->get_location_code() . "<br/>\n";
					$gab_ids .= $location->get_gab_id() . "<br>\n";
				}
			}

			// Adds info about contracts to a string
			foreach ($this->get_contracts() as $contract)
			{
				$start_date = $contract->get_contract_date()->get_start_date();
				$end_date = $contract->get_contract_date()->get_end_date();

				if ($end_date == 0)
					$contract_dates .= date("d-m-Y", $start_date) . " - løpende";
				else
					$contract_dates .= date("d-m-Y", $start_date) . " - " . date("d-m-Y", $end_date);

				$contract_dates .= " (" . $contract->get_old_contract_id() . ")" . "<br/>\n";
			}

			if (count($this->get_contracts()) == 0)
			{
				$contract_dates .= "Ingen<br/>\n";
			}

			if ($this->has_custom_address())
			{
				$addresses = $this->get_custom_address_1() . ' ' . $this->get_custom_house_number();
			}
			return array(
				'id' => $this->get_id(),
				'location_code' => $location_codes,
				'description' => $this->get_description(),
				'status_id' => $this->get_status_id(),
				'is_active' => $this->is_active(),
				'name' => $this->get_name(),
				'address' => $addresses,
				'gab_id' => $gab_ids,
				'area_gros' => $this->get_area_gros(),
				'area_net' => $this->get_area_net(),
				'status' => $this->get_status(),
				'contracts' => $contract_dates,
				'furnished_status' => $this->get_furnish_type(),
				'standard_id' =>  $this->get_standard_id(),
				'composite_type_id' =>  $this->get_composite_type_id(),
				'part_of_town_id' =>  $this->get_part_of_town_id(),
				'custom_price_factor' =>  $this->get_custom_price_factor(),
				'custom_price' =>  $this->get_custom_price(),
				'price_type_id' =>  $this->get_price_type_id(),
			);
		}
	}