<?php

	/**
	 * Represents a location from the property module
	 *
	 */
	class rental_property_location
	{

		protected $location_code; // The '1101-01' code
		protected $gab_id;
		protected $level;
		protected $names; // Names from the different levels
		protected $address_1;
		protected $attributes; // Lots of 'exciting' attributes that differs from all the levels and databases and what not..
		protected $area_gros;
		protected $area_net;

		public function __construct( string $location_code, string $gab_id, $level = -1, array $names = null )
		{
			$this->location_code = $location_code;
			$this->gab_id = $gab_id;
			$this->level = (int)$level;
			$this->names = $names;
			$this->area_gros = 0.0;
			$this->area_net = 0.0;
		}

		public function set_address_1( $address_1 )
		{
			$this->address_1 = $address_1;
		}

		public function get_address_1()
		{
			return $this->address_1;
		}

		public function set_location_code( $location_code )
		{
			$this->location_code = $location_code;
		}

		public function get_location_code()
		{
			return $this->location_code;
		}

		public function set_gab_id( $gab_id )
		{
			$this->gab_id = $gab_id;
		}

		public function get_gab_id()
		{
			return $this->gab_id;
		}

		public function set_area_gros( $area_gros )
		{
			$this->area_gros = (double)$area_gros;
		}

		public function get_area_gros()
		{
			return $this->area_gros;
		}

		public function set_area_net( $area_net )
		{
			$this->area_net = (double)$area_net;
		}

		public function get_area_net()
		{
			return $this->area_net;
		}

		public function get_concat_name()
		{
			if (count($this->names) > 0)
			{
				return implode(', ', $this->names);
			}
			else
			{
				return '';
			}
		}

		public function serialize()
		{
			$result = array();
			if ($this->names != null && is_array($this->names))
			{
				$counter = 0;
				foreach ($this->names as $name)
				{
					$result['loc' . ( ++$counter) . '_name'] = $name;
				}
			}
			$result['location_code'] = $this->get_location_code();
			$result['address'] = $this->get_address_1();
			$result['area_net'] = $this->get_area_net();
			$result['area_gros'] = $this->get_area_gros();
			return $result;
		}
	}