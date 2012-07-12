<?php
/**
 * Class that represents a contract party
 *
 */
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'result_unit', 'inc/locations/');
include_class('rental', 'organisational_location', 'inc/locations/');

class rental_party extends rental_model
{
	public static $so;

    protected $id;
    protected $identifier;
    protected $first_name;
    protected $last_name;
    protected $location_id;
    protected $is_inactive;
    protected $comment;

    protected $title;
    protected $company_name;
    protected $department;

    protected $address_1;
    protected $address_2;
    protected $postal_code;
    protected $place;
    protected $postal_country_code;

    protected $phone;
    protected $mobile_phone;
    protected $fax;
    protected $email;
    protected $url;

    protected $account_number;
    protected $reskontro;

	protected $contracts;
	
	protected $sync_data;
	protected $sync_problems = array();
	protected $org_enhet_id;
	protected $unit_leader;

	public function __construct($id = 0)
	{
		$this->id = $id;
		$this->postal_country_code = 'NO'; // TODO: How should we handle this one? The Agresso CS15 format needs to know the country code, but currently the rental module only support Norwegian addresses. And we have no idea which standard Agresso uses for country codes..
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

	public static function get_all($start_index = 0, $num_of_objects = 1000, $sort_field = null, $ascending = true, $search_for = null, $search_type = null, $filters = array())
	{
		$so = self::get_so();
        $result = $so->get($start_index, $num_of_objects, $sort_field, $ascending, $search_for, $search_type, $filters);
		//$result = $so->get_party_array($start, $results, $sort, $dir, $query, $search_option, $filters,$count);
		return $result;
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

	public function set_identifier($identifier)
	{
		$this->identifier = $identifier;
	}
	
	public function get_org_enhet_id() { return $this->org_enhet_id; }

	public function set_org_enhet_id($org_enhet_id)
	{
		$this->org_enhet_id = $org_enhet_id;
	}
	
	public function get_unit_leader() { return $this->unit_leader; }

	public function set_unit_leader($unit_leader)
	{
		$this->unit_leader = $unit_leader;
	}

	public function get_sync_data() { return $this->sync_data; }
	
	public function set_sync_data($sync_data)
	{
		$this->sync_data = $sync_data;
	}
	
	public function get_sync_problems() { return $this->sync_problems; }

	public function add_sync_problem($sync_problem)
	{
		$this->sync_problems[] = $sync_problem;	
	}

	public function get_identifier() { return $this->identifier; }

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

	public function set_location_id(int $location_id)
	{
		$this->location_id = (int)$location_id;
	}

	public function get_location_id() { return $this->location_id; }

	public function set_is_inactive(bool $is_inactive)
	{
		$this->is_inactive = (bool)$is_inactive;
	}

	public function is_inactive() { return $this->is_inactive; }

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
	
	public function set_postal_country_code($postal_country_code)
	{
		$this->postal_country_code = $postal_country_code;
	}

	public function get_postal_country_code(){ return $this->postal_country_code; }

	public function set_phone($phone)
	{
		$this->phone = $phone;
	}

	public function get_phone() { return $this->phone; }
	
	public function set_mobile_phone($mobile_phone)
	{
		$this->mobile_phone = $mobile_phone;
	}

	public function get_mobile_phone() { return $this->mobile_phone; }

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

	public function set_account_number($account_number)
	{
		$this->account_number = $account_number;
	}

	public function get_account_number() { return $this->account_number; }

	public function set_reskontro($reskontro)
	{
		$this->reskontro = $reskontro;
	}

    public function set_comment($comment)
    {
        $this->comment = $comment;
    }

    public function get_comment()
    {
        return $this->comment;
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
			'internal' => lang('internal'),
			'external' => lang('external'),
			'all' => lang('all')
		);
	}

	public function serialize(rental_contract $contract = null)
	{
		$is_payer = '';
		if(isset($contract) && $contract->get_payer_id() == $this->id){
			$is_payer = lang('is_payer');
		}
		$address_elements = array($this->address_1, $this->address_2, "{$this->postal_code} {$this->place}");
		$address = '';
		foreach($address_elements as $element)
		{
			if($element != null && $element != '') // Address set
			{
				if($address != '') // There's alredy some text set
				{
					$address .= ', ';
				}
				$address .= $element;
			}
		}
		
		$location_name = $GLOBALS['phpgw']->locations->get_name($this->location_id);
		$result_unit_number = result_unit::get_identifier_from_name($location_name['location']);
		
		return array(
			'id' => $this->id,
			'name' => $this->get_name(),
			'identifier' => $this->identifier,
			'firstname' => $this->first_name,
			'lastname' => $this->last_name,
			'title' => $this->title,
			'company_name' => $this->company_name,
			'department' => $this->department,
			'address' => $address,
			'address1' => $this->address_1,
			'address2' => $this->address_2,
			'postal_code' => $this->postal_code,
		 	'place' => $this->place,
			'phone' => $this->phone,
			'mobile_phone' => $this->mobile_phone,
			'fax' => $this->fax,
			'email' => $this->email,
			'url' => $this->url,
			'location_id' => $this->location_id,
			'account_number' => $this->account_number,
			'reskontro' => $this->reskontro,
			'is_inactive' => $this->is_inactive,
			'is_payer' => $is_payer,
			'result_unit_number' => $result_unit_number,
			'service_id' => $this->sync_data['service_id'],
			'responsibility_id' => $this->sync_data['responsibility_id'],
			'org_enhet_id' => $this->get_org_enhet_id(),
			'unit_leader' => $this->get_unit_leader(),
			'sync_message' => implode('<br/>',$this->get_sync_problems())
		);
		
		
	}

}
?>
