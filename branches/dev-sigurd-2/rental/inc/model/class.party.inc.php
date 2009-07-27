<?php
/**
 * Class that represents a rental composite
 *
 */

phpgw::import_class('rental.bocommon');
include_class('rental', 'contract', 'inc/model/');

class rental_party
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
	
	protected $contracts;

	public function __construct($id = 0)
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
			self::$so = CreateObject('rental.soparty');
		}
		
		return self::$so;
	}
	
	/**
	 * Return a single rental_party object based on the provided id
	 * 
	 * @param $id party id
	 * @return a rental_party
	 */
	public static function get($id)
	{
		$so = self::get_so();
		
		return $so->get_single($id);
	}
	
	public static function get_all($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{	
		$so = self::get_so();
		$partys = $so->get_party_array($start, $results, $sort, $dir, $query, $search_option, $filters);
		return $partys;
	}
	
	/**
	 * Return a list of the contracts this party is associated with
	 * 
	 * @return a list of rental_contract objects
	 */
	public function get_contracts()
	{
		$so = self::get_so();
		
		if (!$this->contracts) {
			$this->contracts = rental_contract::get_contracts_for_tentant($this->get_id);
		}
		
		return $this->contracts;
	}

	public function set_id($id)
	{
		$this->id = $id;
	}
	
	public function get_id() { return $this->id; }

	public function set_agresso_id($agresso_id)
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

	public function set_is_active(bool $is_active)
	{
		$this->is_active = (bool)$is_active;
	}
	
	public function is_active() { return $this->is_active; }

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
	
	public function get_name()
	{
		$name = $this->last_name;
		if($this->first_name != '') // Firstname is set
		{
			if($name != '') // There's a lastname
			{
				$name .= ', '; // Append comma
			}
			$name .= $this->first_name; // Append firstname
		}
		if($this->company_name != '') // There's a company name
		{
			if($name != '') // We've already got a name
			{
				$name .= ' (' . $this->company_name . ')'; // Append company name in parenthesis
			}
			else // No name
			{
				$name = $this->company_name; // Set name to company
			}
		}
		return $name;	
	}
	
	public function get_reskontro() { return $this->reskontro; }

	/**
	 * Store the object in the database. If the object has no id it is assumed to be new and
	 * inserted for the first time. The object is then updated with the new insert id.
	 */
	public function store()
	{
		$so = self::get_so();
		if ($this->id) {
			// We can assume this object came from the database since it has an ID. Update the existing row.
			$so->update($this);
		} 
		else
		{
			// This object does not have an ID, so will be saved as a new DB row
			$so->add($this);
		}
	}
	
	/**
	 * Creates a new party.
	 * 
	 * @return array(
	 * 	'id' => int with id of new party
	 *	'msg' => string with any msgs that could be displayed to the user
	 * )
	 */
	public static function add()
	{
		$receipt = array
		(
			'id' => -1,
			'msg' => null
		);
		$receipt['id'] = self::get_so()->add();
		return $receipt;
	}
	
	/**
	 * Get a list of all available party types
	 * 
	 * @return key/value array of party types linked to IDs
	 */
	public static function get_party_types()
	{
		return array(
			'internal' => lang('rental_party_internal'),
			'external' => lang('rental_party_external'),
			'all' => lang('rental_party_all')
		);
	}
	
	public function serialize(rental_contract $contract)
	{	
		$is_payer = '';
		if(isset($contract) && $contract->get_payer_id() == $this->id){
			
			$is_payer = lang('rental_contract_is_payer');
		}
		
		return array(
			'id' => $this->id,
			'name' => $this->get_name(),
			'personal_identification_number' => $this->personal_identification_number,
			'firstname' => $this->first_name,
			'lastname' => $this->last_name,
			'title' => $this->title,
			'company_name' => $this->company_name,
			'department' => $this->department,
			'address' => $this->address_1. ', ' . $this->address_2 . ', ' . $this->postal_code . ', ' . $this->place,
			'address1' => $this->address_1,
			'address2' => $this->address_2,
			'postal_code' => $this->postal_code,
		 	'place' => $this->place,
			'phone' => $this->phone,
			'fax' => $this->fax,
			'email' => $this->email,
			'url' => $this->url,
			'type_id' => $this->type_id,
			'post_bank_account_number' => $this->post_bank_account_number,
			'account_number' => $this->account_number,
			'reskontro' => $this->reskontro,
			'is_active' => $this->is_active,
			'is_payer' => $is_payer
		);
	}
	
}
?>
