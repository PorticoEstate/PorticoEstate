<?php

phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'organization', 'inc/model/');

class activitycalendar_soorganization extends activitycalendar_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return rental_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('activitycalendar.soorganization');
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

		if($sort_field != null) {
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY id $dir";
		}
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "name":
					$like_clauses[] = "org.name $this->like $like_pattern";
					$like_clauses[] = "org.shortname $this->like $like_pattern";
					break;
				case "org_id":
					$like_clauses[] = "org.organization_number $this->like $like_pattern";
					break;
				case "district":
					$like_clauses[] = "org.district $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		$filter_clauses[] = "show_in_portal=1";

/*
		// All parties with contracts of type X
		if(isset($filters['party_type']))
		{
			$party_type = $this->marshal($filters['party_type'],'int');
			if(isset($party_type) && $party_type > 0)
			{
				$filter_clauses[] = "contract.location_id = {$party_type}";
			}
		}
*/		
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(org.id)) AS count';
		}
		else
		{
			$columns[] = 'org.id';
			$columns[] = 'org.name';
			$columns[] = 'org.homepage';
			$columns[] = 'org.phone';
			$columns[] = 'org.email';
			$columns[] = 'org.description';
			$columns[] = 'org.active';
			$columns[] = 'org.street';
			$columns[] = 'org.zip_code';
			$columns[] = 'org.city';
			$columns[] = 'org.district';
			$columns[] = 'org.organization_number';
			$columns[] = 'org.activity_id';
			$columns[] = 'org.customer_number';
			$columns[] = 'org.customer_identifier_type';
			$columns[] = 'org.customer_organization_number';
			$columns[] = 'org.customer_ssn';
			$columns[] = 'org.customer_internal';
			$columns[] = 'org.shortname';
			$columns[] = 'org.show_in_portal';
			
			$cols = implode(',',$columns);
		}

		$tables = "bb_organization org";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";
		
		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}

	function get_organization_name($org_id)
	{
		$result = "Ingen";
    	if(isset($org_id)){
	    	$q1="SELECT name FROM bb_organization WHERE id={$org_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}
		
		return $result;
	}
	
	function get_contacts($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT id FROM bb_organization_contact WHERE organization_id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$cont_id = $this->db->f('id');
				$contacts[] = $cont_id;
			}
			//$result=$contacts;
    	}
		return $contacts;
	}
	
	function get_description($organization_id)
	{
    	if(isset($organization_id)){
	    	$q1="SELECT description FROM bb_organization WHERE id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}

	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$organization)
	{
		return false;
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($party)
	{
		return false;
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'organization', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}

	protected function populate(int $org_id, &$organization)
	{

		if($organization == null) {
			$organization = new activitycalendar_organization((int) $org_id);

			$organization->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$organization->set_organization_number($this->unmarshal($this->db->f('organization_number'), 'int'));
			$organization->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$organization->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$organization->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
		}
		return $organization;
	}
}
?>
