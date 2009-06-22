<?php
	/**
	 * Class that represents a rental composite
	 *
	 */
	
	phpgw::import_class('rental.bocommon');
	include_class('rental', 'contract', 'inc/model/');
	
	class rental_tenant
	{
		public static $so;

    protected $id;
    protected $agresso_id;
    protected $personal_identification_number;
    protected $first_name;
    protected $last_name;
    protected $type_id;
    protected $is_active;

    protected $title;
    protected $company_name;
    protected $department;

    protected $address_1;
    protected $address_2;
    protected $postal_code;
    protected $place;

    protected $phone;
    protected $fax;
    protected $email;
    protected $url;

    protected $post_bank_account_number;
    protected $account_number;
    protected $reskontro;

		public function __construct(int $id = 0)
		{
			$this->id = $id;
		}
		
		/**
		 * Get a static reference to the storage object associated with this model object
		 * 
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null) {
				self::$so = CreateObject('rental.sotenant');
			}
			
			return self::$so;
		}
		
		/**
		 * Return a single rental_tenant object based on the provided id
		 * 
		 * @param $id tenant id
		 * @return a rental_tenant
		 */
		public static function get($id)
		{
			$so = self::get_so();
			
			return $so->get_single($id);
		}
		
		/**
		 * Return a list of the contracts this tenant is associated with
		 * 
		 * @return a list of rental_contract objects
		 */
		public function get_contracts()
		{
			$so = self::get_so();
			
			return rental_contract::get_contracts_for_tentant($this->get_id);
		}

		public function set_id($id)
		{
			$this->id = $id;
		}
		
		public function get_id() { return $this->id; }

		public function set_($agresso_id)
		{
			$this->agresso_id = $agresso_id;
		}
		
		public function get_agresso_id() { return $this->agresso_id; }

		public function set_personal_identification_number($personal_identification_number)
		{
			$this->personal_identification_number = $personal_identification_number;
		}
		
		public function get_personal_identification_number() { return $this->personal_identification_number; }

		public function set_first_name($first_name)
		{
			$this->first_name = $first_name;
		}
		
		public function get_first_name() { return $this->first_name; }

		public function set_last_name($last_name)
		{
			$this->last_name = $last_name;
		}
		
		public function get_last_name() { return $this->last_name; }

		public function set_type_id($type_id)
		{
			$this->type_id = $type_id;
		}
		
		public function get_type_id() { return $this->type_id; }

		public function set_is_active($is_active)
		{
			$this->is_active = $is_active;
		}
		
		public function get_is_active() { return $this->is_active; }

		public function set_title($title)
		{
			$this->title = $title;
		}
		
		public function get_title() { return $this->title; }

		public function set_company_name($company_name)
		{
			$this->company_name = $company_name;
		}
		
		public function get_company_name() { return $this->company_name; }

		public function set_department($department)
		{
			$this->department = $department;
		}
		
		public function get_department() { return $this->department; }

		public function set_address_1($address_1)
		{
			$this->address_1 = $address_1;
		}
		
		public function get_address_1() { return $this->address_1; }

		public function set_address_2($address_2)
		{
			$this->address_2 = $address_2;
		}
		
		public function get_address_2() { return $this->address_2; }

		public function set_postal_code($postal_code)
		{
			$this->postal_code = $postal_code;
		}
		
		public function get_postal_code() { return $this->postal_code; }

		public function set_place($place)
		{
			$this->place = $place;
		}
		
		public function get_place() { return $this->place; }

		public function set_phone($phone)
		{
			$this->phone = $phone;
		}
		
		public function get_phone() { return $this->phone; }

		public function set_fax($fax)
		{
			$this->fax = $fax;
		}
		
		public function get_fax() { return $this->fax; }

		public function set_email($email)
		{
			$this->email = $email;
		}
		
		public function get_email() { return $this->email; }

		public function set_url($url)
		{
			$this->url = $url;
		}
		
		public function get_url() { return $this->url; }

		public function set_post_bank_account_number($post_bank_account_number)
		{
			$this->post_bank_account_number = $post_bank_account_number;
		}
		
		public function get_post_bank_account_number() { return $this->post_bank_account_number; }

		public function set_account_number($account_number)
		{
			$this->account_number = $account_number;
		}
		
		public function get_account_number() { return $this->account_number; }

		public function set_reskontro($reskontro)
		{
			$this->reskontro = $reskontro;
		}
		
		public function get_reskontro() { return $this->reskontro; }
	}
?>
