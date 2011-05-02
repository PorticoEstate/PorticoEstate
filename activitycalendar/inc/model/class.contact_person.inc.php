<?php

	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_contact_person extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $name;
		protected $ssn;
		protected $phone;
		protected $email;
		protected $organization_id;
		protected $group_id;
		
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
		
		public function get_id() { return $this->id; }

		public function set_name($name){
			$this->name = $name;
		}
		
		public function get_name(){
			return $this->name;
		}
		
		public function set_ssn($ssn)
		{
			$this->ssn = $ssn;
		}
	
		public function get_ssn()
		{
			return $this->ssn;
		}
		
		public function set_phone($phone)
		{
			$this->phone = $phone;
		}
	
		public function get_phone()
		{
			return $this->phone;
		}
		
		public function set_email($email)
		{
			$this->email = $email;
		}
	
		public function get_email()
		{
			return $this->email;
		}
		
		public function set_organization_id($organization_id)
		{
			$this->organization_id = $organization_id;
		}
	
		public function get_organization_id()
		{
			return $this->organization_id;
		}
		
		public function set_group_id($group_id)
		{
			$this->group_id = $group_id;
		}
	
		public function get_group_id()
		{
			return $this->group_id;
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.socontactperson');
			}
			
			return self::$so;
		}
		
		public function serialize()
		{
			return array(
				'id' => $this->get_id(),
				'name' => $this->get_name(),
				'ssn' => $this->get_ssn(),
				'phone' => $this->get_phone(),
				'email' => $this->get_email(),
				'organization_id' => $this->get_organization_id(),
				'group_id' => $this->get_group_id()
			);
		}
	}

?>