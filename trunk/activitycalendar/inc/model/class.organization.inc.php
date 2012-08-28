<?php
	include_class('activitycalendar', 'model', 'inc/model/');

	class activitycalendar_organization extends activitycalendar_model
	{
		public static $so;
		
		protected $id;
		protected $name;
		protected $description;
		protected $organization_number;
		protected $show_in_portal;
		protected $district;
		protected $homepage;
		protected $email;
		protected $phone;
		protected $address;
		protected $change_type;
		protected $transferred;
		protected $original_org_id;
                protected $addressnumber;
                protected $zip_code;
                protected $city;
		
		/**
		 * Constructor.  Takes an optional ID.  If a organization is created from outside
		 * the database the ID should be empty so the database can add one according to its logic.
		 * 
		 * @param int $id the id of this organization
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
		
		public function set_name($name)
		{
			$this->name = $name;
		}
		
		public function get_name() { return $this->name; }
		
		public function set_homepage($homepage)
		{
			$this->homepage = $homepage;
		}
		
		public function get_homepage() { return $this->homepage; }
		
		public function set_change_type($change_type)
		{
			$this->change_type = $change_type;
		}
		
		public function get_change_type() { return $this->change_type; }
		
		public function set_email($email)
		{
			$this->email = $email;
		}
		
		public function get_email() { return $this->email; }
		
		public function set_phone($phone)
		{
			$this->phone = $phone;
		}
		
		public function get_phone() { return $this->phone; }
		
		public function set_address($address)
		{
			$this->address = $address;
		}
		
		public function get_address() { return $this->address; }
		
		public function set_description($description)
		{
			$this->description = $description;
		}
		
		public function get_description() { return $this->description; }
		
		public function set_organization_number($organization_number)
		{
			$this->organization_number = $organization_number;
		}
		
		public function get_organization_number() { return $this->organization_number; }
		
		public function set_show_in_portal($show_in_portal)
		{
			$this->show_in_portal = $show_in_portal;
		}
		
		public function get_show_in_portal() { return $this->show_in_portal; }
		
		public function set_district($district)
		{
			$this->district = $district;
		}
		
		public function get_district() { return $this->district; }
		
		public function set_transferred($transferred)
		{
			$this->transferred = $transferred;
		}
		
		public function get_transferred() { return $this->transferred; }
		
		public function set_original_org_id($original_org_id)
		{
			$this->original_org_id = $original_org_id;
		}
		
		public function get_original_org_id() { return $this->original_org_id; }
		
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
		
		public function serialize()
		{
			$so_org = activitycalendar_soorganization::get_instance();
                        $so_act = activitycalendar_soactivity::get_instance();
			return array(
				'id' => $this->get_id(),
				'name' => $this->get_name(),
				'organization_number' => $this->get_organization_number(),
				'district' => (($this->get_change_type() == "new" || $this->get_change_type() == "change") && $this->get_district() && is_numeric($this->get_district()))?$so_act->get_district_from_id($this->get_district()):$this->get_district(),
				'description' => $this->get_description(),
				'homepage'	=>	$this->get_homepage(),
				'email'	=>	$this->get_email(),
				'phone'	=>	$this->get_phone(),
				'address'	=>	$this->get_address(),
			    'addressnumber'	=>	$this->get_addressnumber(),
			    'zip_code'	=>	$this->get_zip_code(),
			    'city'	=>	$this->get_city(),
				'show_in_portal' => $this->get_show_in_portal(),
				'change_type' => lang($this->get_change_type()),
				'transferred' => $this->get_transferred(),
				'office' => $so_org->get_office_from_district($so_org->get_district_from_name($this->get_district()))
			);
		}
		
	}
?>