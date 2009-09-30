<?php
/**
 * Class that represents a contract party
 *
 */
include_class('rental', 'contract', 'inc/model/');

class rental_party extends rental_model
{
	public static $so;

    protected $id;
    protected $agresso_id;
    protected $personal_identification_number;
    protected $first_name;
    protected $last_name;
    protected $location_id;
    protected $is_active;
    protected $comment;

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

    /**
     * Alias for set_personal_identificiation_number()
     *
     * @param mixed $pid Personal ID number
     */
    public function set_pid($pid) {
        $this->set_personal_identification_number($pid);
    }

    /**
     * Alias for get_personal_identificiation_number()
     *
     * @return string Personal ID number
     */
    public function get_pid() { return $this->get_personal_identification_number(); }

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
		$this->location_id = (int)$ocation_id;
	}

	public function get_ocation_id() { return $this->location_id; }

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
		return array(
			'id' => $this->id,
			'name' => $this->get_name(),
			'personal_identification_number' => $this->personal_identification_number,
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
			'fax' => $this->fax,
			'email' => $this->email,
			'url' => $this->url,
			'location_id' => $this->location_id,
			'account_number' => $this->account_number,
			'reskontro' => $this->reskontro,
			'is_active' => $this->is_active,
			'is_payer' => $is_payer
		);
	}

}
?>
