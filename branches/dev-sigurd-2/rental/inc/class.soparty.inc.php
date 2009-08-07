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
			'type_id'	=> array('type' => 'int'),
			'is_active' => array('type', 'bool'),
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
	 * @return a rental_party
	 */
	function get_single($id)
	{
		$id = (int)$id;

      $sql = "SELECT * FROM " . $this->table_name ." WHERE " . $this->table_name . ".id={$id}";

      $this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);

      $party = new rental_party();

      $this->db->next_record();

      $party->set_id($this->unmarshal($this->db->f('id', true), 'int'));
      $party->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
      $party->set_personal_identification_number($this->unmarshal($this->db->f('personal_identification_number', true), 'string'));
      $party->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
      $party->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
      $party->set_type_id($this->unmarshal($this->db->f('type_id', true), 'int'));
      $party->set_is_active($this->unmarshal($this->db->f('is_active', true), 'bool'));

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

	/**
	 * Get a list of composite objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_party objects
	*/
	function get_party_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{	
		// We have the option to search for party type. A party does not have a type per se
		// but gets one or more types from the contracts it is associated to.
		// So if this filter is set we need to do some joining to check what contracts this
		// party is tied to.
		if ((isset($filters['party_type']) && $filters['party_type'] != 'all') || isset($filters['contract_id'])) {
			
			// Join the contracts (many to many) so we can search for contract types, only
			// include parties that actually have contracts

			$filter_conditions = $this->get_filter_conditions($filters,'contracts');
			$search_conditions = $this->get_search_conditions($query,$search_option,'rental_party','AND');
			$party_not_in = '';
			if(isset($filters['contract_id']) )
			{
				//$party_not_in = "AND party_id NOT IN (SELECT party_id FROM rental_contract_party WHERE contract_id = ".$filters['contract_id'].")";
			}
			if(isset($filters['contract_id']) && $filters['party_type'] != 'all')
			{
				
			}
			
			$sql = "SELECT DISTINCT(rental_party.id), rental_party.* FROM rental_party LEFT JOIN
					(
						SELECT party_id, contract_id, type_id FROM rental_contract_party rcp LEFT JOIN
						(
							SELECT id, type_id FROM rental_contract 
						) 
						c
						ON (c.id = rcp.contract_id) 
					) 
					contracts
					ON (rental_party.id = contracts.party_id OR contracts.contract_id IS NULL)
					WHERE $filter_conditions $search_conditions $party_not_in $order";
			
			//var_dump($sql);
			// Alternative, with subselect.  Test with many rows:
			/*
			$sql = "SELECT *
								FROM rental_party
								WHERE id IN 
								(SELECT id
									FROM rental_contract_party
									WHERE contract_id IN (SELECT id FROM rental_contract WHERE type_id = $type_id)) AND $condition $order";
			*/
		} else {
			// No type filter was set, do a normal select
			$search_conditions = $this->get_search_conditions($query,$search_option,'WHERE');
			$sql = "SELECT * FROM rental_party $search_conditions $order";
		}
		
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
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
			$party->set_type_id($row['type_id']);
			$party->set_is_active($row['is_active']);
			
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

	function add(rental_party &$party)
	{
		// Insert the new party
		$q ="INSERT INTO ".$this->table_name." (is_active) VALUES (true)";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		$party->set_id($receipt['id']);
		return $receipt;
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
				case "result_unit_number":
					$like_clauses[] = "$table_name.result_unit $this->like $like_pattern";
				case "organisation_number":
					$like_clauses[] = "$table_name.organisation_number $this->like $like_pattern";
				case "account":
					$like_clauses[] = "$table_name.reskontro = $like_pattern";
				case "all":
					$like_clauses[] = "$table_name.first_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.last_name $this->like $like_pattern";
					$like_clauses[] = "$table_name.address_1 $this->like $like_pattern";
					$like_clauses[] = "$table_name.address_2 $this->like $like_pattern";
					$like_clauses[] = "$table_name.postal_code $this->like $like_pattern";
					$like_clauses[] = "$table_name.place $this->like $like_pattern";
					$like_clauses[] = "$table_name.personal_identification_number $this->like $like_pattern";
					$like_clauses[] = "$table_name.result_unit $this->like $like_pattern";
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
			$filter_clauses[] = "$table_name.type_id = ".$filters['party_type'];
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
	 * @return result receipt from the db operation
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
			'is_active = \'' . ($party->is_active() ? 'true' : 'false') . '\''
		);
				
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		return $receipt;
	}
	
}
?>