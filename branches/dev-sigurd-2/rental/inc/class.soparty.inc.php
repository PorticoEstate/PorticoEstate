<?php

phpgw::import_class('rental.socommon');

include_class('rental', 'party', 'inc/model/');

class rental_soparty extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_party',
		array
		(
			'id'	=> array('type' => 'int'),
			'agresso_id' => array('type' => 'string'),
			'personal_identification_number' => array('type' => 'string'),
			'first_name' => array('type' => 'string'),
			'last_name' => array('type' => 'string'),
			'location_id'	=> array('type' => 'int'),
			'is_active' => array('type', 'bool'),
			'comment' => array('type', 'string'),
			'title' => array('type' => 'string'),
			'company_name' => array('type' => 'string'),
			'department' => array('type' => 'string'),
			'address_1' => array('type' => 'string'),
			'address_2' => array('type' => 'string'),
			'postal_code' => array('type' => 'string'),
			'place' => array('type' => 'string'),
			'phone' => array('type' => 'string'),
			'fax' => array('type' => 'string'),
			'email' => array('type' => 'string'),
			'url' => array('type' => 'string'),
			'account_number' => array('type' => 'string'),
			'reskontro' => array('type' => 'string')
		));
	}

	/**
	 * Get single party
	 *
	 * @param	$id	id of the party to return
	 * @return a rental_party object, null if unsuccessful loading
	 */
	function get_single($id)
	{
		$id = (int)$id;

		$sql = "SELECT * FROM " . $this->table_name ." WHERE " . $this->table_name . ".id={$id}";

		$result = $this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

		if(isset($result)){
			$this->db->next_record();
			$party = new rental_party($this->unmarshal($this->db->f('id', true), 'int'));
			
			if(isset($party))
			{
				$party->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
				$party->set_personal_identification_number($this->unmarshal($this->db->f('personal_identification_number', true), 'string'));
				$party->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
				$party->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
				$party->set_location_id($this->unmarshal($this->db->f('location_id', true), 'int'));
				$party->set_is_active($this->unmarshal($this->db->f('is_active', true), 'bool'));
				$party->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));
	
				$party->set_title($this->unmarshal($this->db->f('title', true), 'string'));
				$party->set_company_name($this->unmarshal($this->db->f('company_name', true), 'string'));
				$party->set_department($this->unmarshal($this->db->f('department', true), 'string'));
	
				$party->set_address_1($this->unmarshal($this->db->f('address_1', true), 'string'));
				$party->set_address_2($this->unmarshal($this->db->f('address_2', true), 'string'));
				$party->set_postal_code($this->unmarshal($this->db->f('postal_code', true), 'string'));
				$party->set_place($this->unmarshal($this->db->f('place', true), 'string'));
	
				$party->set_phone($this->unmarshal($this->db->f('phone', true), 'string'));
				$party->set_fax($this->unmarshal($this->db->f('fax', true), 'string'));
				$party->set_email($this->unmarshal($this->db->f('email', true), 'string'));
				$party->set_url($this->unmarshal($this->db->f('url', true), 'string'));
				$party->set_account_number($this->unmarshal($this->db->f('account_number', true), 'string'));
				$party->set_reskontro($this->unmarshal($this->db->f('reskontro', true), 'string'));
				return $party;
			}
		}
		return null;
	}

	/**
	 * Get a list of composite objects matching the specific filters
	 *
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @param $count boolean value, true if only count, false if regular query
	 * @return list of rental_party objects
	 */
	function get_party_array($start = 0, $results = 1000, $sort = 'id', $dir = 'asc', $query = null, $search_option = null, $filters = array(), $count = false)
	{
		// If no count query, create ORDER BY condition
		if(!$count)
		{
			if($sort == 'name')
			{
				$order = "ORDER BY last_name $dir, first_name $dir";
			} 
			else if($sort = 'address')
			{
				$order = "ORDER BY address_1 $dir, address_2 $dir";
			}
			else
			{
				$order = $sort ? "ORDER BY $sort $dir ": '';
			}
		}
		
		// We have the option to search for party type. A party does not have a type per se
		// but gets one or more types from the contracts it is associated to.
		// So if this filter is set we need to do some joining to check what contracts this
		// party is tied to.
		if ((isset($filters['party_type']) && $filters['party_type'] != 'all') || isset($filters['contract_id'])) {
				
			// Join the contracts (many to many) so we can search for contract types, only
			// include parties that actually have contracts
			if($count)
			{
				$columns = "COUNT(DISTINCT(rental_party.id)) as count";
			}
			else
			{
				$columns = 	"DISTINCT(rental_party.id), rental_party.*";
			}
			
			$filter_conditions = $this->get_filter_conditions($filters,'contracts','');
			$search_conditions = $this->get_search_conditions($query,$search_option,'rental_party','AND');
			$sql = "SELECT $columns
					FROM rental_party
					LEFT JOIN (
						SELECT party_id, contract_id, location_id FROM rental_contract_party rcp 
						LEFT JOIN (
							SELECT id, location_id FROM rental_contract 
						) c
						ON (c.id = rcp.contract_id) 
					) contracts
					ON (rental_party.id = contracts.party_id OR contracts.contract_id IS NULL)
					WHERE $filter_conditions $search_conditions $order";
		} 
		else
		{
			// No type filter was set, do a normal select
			if($count)
			{
				$columns = "COUNT(DISTINCT(rental_party.id)) as count";
			}
			else
			{
				$columns = "*";
			}
			
			$search_conditions = $this->get_search_conditions($query,$search_option,'rental_party','WHERE');
			$sql = "SELECT $columns FROM rental_party $search_conditions $order";
		}
		
		if($count)
		{
			return $this->get_count($sql);	
		}
		
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $results);

		$parties = array();

		while ($this->db->next_record()) {
			$row = array();
			foreach($this->fields as $field => $fparams)
			{
				$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
			}
				
			$party = new rental_party($row['id']);
				
			$party->set_agresso_id($row['agresso_id']);
			$party->set_personal_identification_number($row['personal_identification_number']);
			$party->set_first_name($row['first_name']);
			$party->set_last_name($row['last_name']);
			$party->set_location_id($row['location_id']);
			$party->set_is_active($row['is_active']);
			$party->set_comment($row['comment']);
				
			$party->set_title($row['title']);
			$party->set_company_name($row['company_name']);
			$party->set_department($row['department']);
				
			$party->set_address_1($row['address_1']);
			$party->set_address_2($row['address_2']);
			$party->set_postal_code($row['postal_code']);
			$party->set_place($row['place']);
				
			$party->set_phone($row['phone']);
			$party->set_fax($row['fax']);
			$party->set_email($row['email']);
			$party->set_url($row['url']);
			$party->set_account_number($row['account_number']);
			$party->set_reskontro($row['reskontro']);
				
			$parties[] = $party;
		}
		return $parties;
	}

	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(rental_party &$party)
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
	 * @return boolean	true if successful, false otherwise
	 */
	function update($party)
	{
		$id = intval($party->get_id());
		$values = array(
			'personal_identification_number = \'' . $party->get_personal_identification_number() . '\'',
			'first_name = \'' . $party->get_first_name() . '\'',
			'last_name = \'' . $party->get_last_name() . '\'',
			'title = \'' . $party->get_title() . '\'',
			'company_name = \'' . $party->get_company_name() . '\'',
			'department = \'' . $party->get_department() . '\'',
			'address_1 = \'' . $party->get_address_1() . '\'',
			'address_2 = \'' . $party->get_address_2() . '\'',
			'postal_code = \'' . $party->get_postal_code() . '\'',
			'place = \'' . $party->get_place() . '\'',
			'phone = \'' . $party->get_phone() . '\'',
			'fax = \'' . $party->get_fax() . '\'',
			'email = \'' . $party->get_email() . '\'',
			'url = \'' . $party->get_url() . '\'',
			'account_number = \'' . $party->get_account_number() . '\'',
			'reskontro = \'' . $party->get_reskontro() . '\'',
			'is_active = \'' . ($party->is_active() ? 'true' : 'false') . '\'',
			'comment = \'' . $party->get_comment() . '\''
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

}
?>