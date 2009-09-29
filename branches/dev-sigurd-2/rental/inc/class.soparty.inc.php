<?php

phpgw::import_class('rental.socommon');

include_class('rental', 'party', 'inc/model/');

class rental_soparty extends rental_socommon
{
	protected static $so;
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.soparty');
		}
		return self::$so;
	}

    /**
     * Generate SQL query
     *
     * @todo Add support for filter "party_type", meaning what type of contracts
     * the party is involved in.
     *
     * @param string $sort_field
     * @param boolean $ascending
     * @param string $search_for
     * @param string $search_type
     * @param array $filters
     * @param boolean $return_count
     * @return string SQL
     */
    protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');

		//Add columns to this array to include them in the query
		$columns = array();

        if($ascending != null && $sort_field != null) {
            $sort = $this->marshal($sort_field,'field');
            $dir = $ascending ? 'ASC' : 'DESC';
            $order = $sort ? "ORDER BY $sort $dir": '';
        }
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "id":
                    $like_clauses[] = "party.id = $search_for";
					break;
                case "name":
                    $like_clauses[] = "party.first_name $this->like $like_pattern";
                    $like_clauses[] = "party.last_name $this->like $like_pattern";
                    $like_clauses[] = "party.company_name $this->like $like_pattern";
                    break;
                case "address":
                    $like_clauses[] = "party.address_1 $this->like $like_pattern";
                    $like_clauses[] = "party.address_2 $this->like $like_pattern";
                    $like_clauses[] = "party.postal_code $this->like $like_pattern";
                    $like_clauses[] = "party.place $this->like $like_pattern";
                    break;
                case "ssn":
                    $like_clauses[] = "party.personal_identification_number = $search_for";
                    break;
                case "organisation_number":
                    $like_clauses[] = "party.organisation_number = $search_for";
                    break;
                case "account":
                    $like_clauses[] = "party.agresso_id = $search_for";
				case "all":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
                    $like_clauses[] = "party.last_name $this->like $like_pattern";
                    $like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
                    $like_clauses[] = "party.address_2 $this->like $like_pattern";
                    $like_clauses[] = "party.postal_code $this->like $like_pattern";
                    $like_clauses[] = "party.place $this->like $like_pattern";
					$like_clauses[] = "party.personal_identification_number = $search_for";
                    $like_clauses[] = "party.organisation_number = $search_for";
                    $like_clauses[] = "party.agresso_id = $search_for";
                    $like_clauses[] = "party.comment $this->like $like_pattern";
					$like_clauses[] = "party.reskontro = $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();


		// All parties with contracts of type X
		if(isset($filters['party_type']))
		{
			$party_type = $this->marshal($filters['party_type'],'int');
            $filter_clauses[] = "SELECT $columns
					FROM rental_party
					LEFT JOIN (
						SELECT party_id, contract_id, location_id FROM rental_contract_party rcp
						LEFT JOIN (
							SELECT id, location_id FROM rental_contract
						) c
						ON (c.id = rcp.contract_id)
					) contracts
					ON (rental_party.id = contracts.party_id OR contracts.contract_id IS NULL)
					WHERE location_id = $party_type";
		}

		if(count($filter_clauses))
        {
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(rental_party.id)) AS count';
		}
		else
		{
			$columns[] = 'party.id AS party_id';
			$columns[] = 'party.agresso_id';
			$columns[] = 'party.personal_identification_number AS pid';
			$columns[] = 'party.first_name';
			$columns[] = 'party.last_name';
			$columns[] = 'party.title';
			$columns[] = 'party.company_name';
			$columns[] = 'party.department';
			$columns[] = 'party.organisation_number AS orgno';
			$columns[] = 'party.address_1';
			$columns[] = 'party.address_2';
			$columns[] = 'party.postal_code';
			$columns[] = 'party.place';
			$columns[] = 'party.phone';
			$columns[] = 'party.fax';
			$columns[] = 'party.email';
			$columns[] = 'party.url';
			$columns[] = 'party.account_number';
			$columns[] = 'party.reskontro';
			$columns[] = 'party.location_id';
			$cols = implode(',',$columns);
		}


		$tables = "rental_party party";
		//$join_contract_type = 	$this->left_join.' rental_contract_responsibility type ON (type.location_id = contract.location_id)';
		//$join_parties = $this->left_join.' rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON (c_t.party_id = party.id)';
		//$join_composites = 		$this->left_join." rental_contract_composite c_c ON (contract.id = c_c.contract_id) {$this->left_join} rental_composite composite ON c_c.composite_id = composite.id";
		//$join_last_edited = $this->left_join.' rental_contract_last_edited last_edited ON (contract.id = last_edited.contract_id)';
		//$joins = $join_contract_type.' '.$join_parties.' '.$join_composites.' '.$join_last_edited;

		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$party)
	{
		// Insert a new party
		$q ="INSERT INTO ".$this->table_name." (is_active) VALUES (true)";
		$result = $this->db->query($q);

		if(isset($result)) {
			// Set the new party ID
			$party->set_id($this->db->get_last_insert_id($this->table_name, 'id'));
			// Forward this request to the update method
			return $this->update($party);
		}
		else
		{
			return false;
		}
	}

	protected function get_search_conditions($query, $search_option,$table_name, $prefix='')
	{
		if($query)
		{
			$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
			switch($search_option){
				case "id":
					$like_clauses[] = "$table_name.id = $query";
					break;
				case "name":
					$like_clauses[] = "$table_name.first_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.last_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.company_name $this->like $like_pattern";
					break;
				case "address":
					$like_clauses[] = "$table_name.address_1 $this->like $like_pattern";
					$like_clauses[] = "$table_name.address_2 $this->like $like_pattern";
					$like_clauses[] = "$table_name.postal_code $this->like $like_pattern";
					$like_clauses[] = "$table_name.place $this->like $like_pattern";
					break;
				case "ssn":
					$like_clauses[] = "$table_name.personal_identification_number $this->like $like_pattern";
					break;
				case "organisation_number":
					$like_clauses[] = "$table_name.organisation_number $this->like $like_pattern";
				case "account":
					$like_clauses[] = "$table_name.reskontro = $like_pattern";
				case "all":
					$like_clauses[] = "$table_name.first_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.last_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.address_1 $this->like $like_pattern";
					$like_clauses[] = "$table_name.comment $this->like $like_pattern";
					$like_clauses[] = "$table_name.address_2 $this->like $like_pattern";
					$like_clauses[] = "$table_name.postal_code $this->like $like_pattern";
					$like_clauses[] = "$table_name.place $this->like $like_pattern";
					$like_clauses[] = "$table_name.personal_identification_number $this->like $like_pattern";
					$like_clauses[] = "$table_name.organisation_number = $like_pattern";
					$like_clauses[] = "$table_name.reskontro = $like_pattern";
					break;
			}
		}
		if(count($like_clauses) > 0)
		{
			return $prefix.' (' . join(' OR ', $like_clauses) . ')';
		}
		else
		{
			return '';
		}
			
	}

	protected function get_filter_conditions($filters,$table_name, $prefix='')
	{
			
		if(isset($filters['contract_id']))
		{
			$filter_clauses[] = "($table_name.contract_id != ".$filters['contract_id']." OR $table_name.contract_id IS NULL)";
		}
			
		if(isset($filters['party_type']) && $filters['party_type'] != 'all')
		{
			$filter_clauses[] = "$table_name.location_id = ".$filters['party_type'];
		}
		if(count($filter_clauses) > 0)
		{
			return $prefix.' '.join(' AND ', $filter_clauses);
		}
		else
		{
			return '';
		}
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($party)
	{
		$id = intval($party->get_id());
		$values = array(
			'personal_identification_number = ' . $this->marshal($party->get_personal_identification_number(), 'string'),
			'first_name = '     . $this->marshal($party->get_first_name(), 'string'),
			'last_name =  '     . $this->marshal($party->get_last_name(), 'string'),
			'title = '          . $this->marshal($party->get_title(), 'string'),
			'company_name = '   . $this->marshal($party->get_company_name(), 'string'),
			'department = '     . $this->marshal($party->get_department(), 'string'),
			'address_1 = '      . $this->marshal($party->get_address_1(), 'string'),
			'address_2 = '      . $this->marshal($party->get_address_2(), 'string'),
			'postal_code = '    . $this->marshal($party->get_postal_code(), 'string'),
			'place = '          . $this->marshal($party->get_place(), 'string'),
			'phone = '          . $this->marshal($party->get_phone(), 'string'),
			'fax = '            . $this->marshal($party->get_fax(), 'string'),
			'email = '          . $this->marshal($party->get_email(), 'string'),
			'url = '            . $this->marshal($party->get_url(), 'string'),
			'account_number = ' . $this->marshal($party->get_account_number(), 'string'),
			'reskontro = '      . $this->marshal($party->get_reskontro(), 'string'),
			'is_active = '      . $this->marshal(($party->is_active() ? 'true' : 'false'), 'bool'),
			'comment = '        . $this->marshal($party->get_comment(), 'string')
			);

        $result = $this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

        if(isset($result))
        {
            return true;
        }
        else
        {
            return false;
        }
	}
	
	
	/**
	 * Get the parties not involved in this contract
	 * 
	 * TODO: make as filter
	 * 
	 * @param $contract_id the contract id
	 * @return  A list of rental_party objects
	 */
	public function get_available_parties_for_contract($contract_id)
	{
		$sql = "SELECT DISTINCT party_id FROM rental_contract_party WHERE contract_id != $contract_id";
		$this->db->query($sql);
		$parties = array();
		$parties_so = rental_party::get_so();
		while($this->db->next_record()) { 
			$party_id = $this->unmarshal($this->db->f('party_id', true), 'int'); 
			$parties[] = $parties_so->get_single($party_id);
		}
		return $parties;
	}

    protected function get_id_field_name()
    {
        return 'party_id';
    }
    protected function populate(int $party_id, &$party)
    {
        if($party == null) {
            $party = new rental_party((int) $party_id);

            $party->set_account_number( $this->unmarshal($this->db->f('account_number'), 'string'));
            $party->set_address_1(      $this->unmarshal($this->db->f('address_1'), 'string'));
            $party->set_address_2(      $this->unmarshal($this->db->f('address_2'), 'string'));
            $party->set_agresso_id(     $this->unmarshal($this->db->f('agresso_id'), 'string'));
            $party->set_comment(        $this->unmarshal($this->db->f('comment'), 'string'));
            $party->set_company_name(   $this->unmarshal($this->db->f('company_name'), 'string'));
            $party->set_department(     $this->unmarshal($this->db->f('department'), 'string'));
            $party->set_email(          $this->unmarshal($this->db->f('email'), 'string'));
            $party->set_fax(            $this->unmarshal($this->db->f('fax'), 'string'));
            $party->set_first_name(     $this->unmarshal($this->db->f('first_name'), 'string'));
            $party->set_is_active(      $this->unmarshal($this->db->f('is_active'), 'bool'));
            $party->set_last_name(      $this->unmarshal($this->db->f('last_name'), 'string'));
            $party->set_location_id(    $this->unmarshal($this->db->f('location_id'), 'int'));
            $party->set_pid(            $this->unmarshal($this->db->f('personal_identification_number'), 'string'));
            $party->set_phone(          $this->unmarshal($this->db->f('phone'), 'string'));
            $party->set_place(          $this->unmarshal($this->db->f('place'), 'string'));
            $party->set_postal_code(    $this->unmarshal($this->db->f('postal_code'), 'int'));
            $party->set_reskontro(      $this->unmarshal($this->db->f('reskontro'), 'string'));
            $party->set_title(          $this->unmarshal($this->db->f('title'), 'string'));
            $party->set_url(            $this->unmarshal($this->db->f('url'), 'string'));
        }

        return $party;
    }
}
?>