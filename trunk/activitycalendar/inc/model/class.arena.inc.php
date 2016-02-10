<?php
	phpgw::import_class('activitycalendar.soarena');
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_arena extends activitycalendar_model
	{
		
		public static $so;
		protected $id;
		protected $arena_name;
		protected $internal_arena_id;
		protected $address;
		protected $addressnumber;
		protected $zip_code;
		protected $city;
		protected $reference;
		protected $active;
		
		/**
		 * Constructor.  Takes an optional ID.  If a contract is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this composite
		 */
		public function __construct(int $id = null)
		{
			$this->id = (int)$id;
		}
		
		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id()
		{
			return $this->id;
		}

		public function set_internal_arena_id($id)
		{
			$this->internal_arena_id = $id;
		}
		
		public function get_internal_arena_id()
		{
			return $this->internal_arena_id;
		}
		
		public function set_arena_name($arena_name)
		{
			$this->arena_name = $arena_name;
		}
	
		public function get_arena_name()
		{
			return $this->arena_name;
		}
		
		public function set_active(bool $active)
		{
			$this->active = (bool)$active;
		}
	
		public function is_active()
		{
			return $this->active;
		}
		
		public function set_address($address)
		{
			$this->address = $address;
		}
	
		public function get_address()
		{
			return $this->address;
		}
		
	    public function set_addressnumber($addressnumber)
		{
			$this->addressnumber = $addressnumber;
		}
	
		public function get_addressnumber()
		{
			return $this->addressnumber;
		}
		
	    public function set_zip_code($zip_code)
		{
			$this->zip_code = $zip_code;
		}
	
		public function get_zip_code()
		{
			return $this->zip_code;
		}
		
	    public function set_city($city)
		{
			$this->city = $city;
		}
	
		public function get_city()
		{
			return $this->city;
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if(self::$so == null)
			{
				self::$so = CreateObject('rental.socontract');
			}
			
			return self::$so;
		}
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'arena_name' => $this->get_arena_name(),
				'address' => $this->get_address(),
			    'addressnumber' => $this->get_addressnumber(),
			    'zip_code' => $this->get_zip_code(),
			    'city' => $this->get_city(),
				'active'		 => ($this->is_active()) ? 'Aktiv' : 'Inaktiv'
			);
		}
	}
