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

		if($sort_field != null && !$return_count) {
			if($sort_field == 'identifier')
			{
				$sort_field = 'org.id';
			}
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY $sort_field $dir";
		}
		else if(!$return_count)
		{
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY org.id $dir";
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
				default:
					$like_clauses[] = "org.name $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		$filter_clauses[] = "show_in_portal=1";
		
		$use_local_org = false;
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "org.id = {$id}";
		}
		if(isset($filters['changed_orgs'])){
			$use_local_org = true;
			//$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			//$filter_clauses[] = "org.id = {$id}";
			unset($filter_clauses);
			if(isset($filters[$this->get_id_field_name()])){
				$id = $this->marshal($filters[$this->get_id_field_name()],'int');
				$filter_clauses[] = "org.id = {$id}";
			}
		}
		if(isset($filters['new_orgs'])){
			$use_local_org = true;
			//$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			//$filter_clauses[] = "org.id = {$id}";
			unset($filter_clauses);
			$filter_clauses[] = "org.change_type = 'new'";
			if(isset($filters[$this->get_id_field_name()])){
				$id = $this->marshal($filters[$this->get_id_field_name()],'int');
				$filter_clauses[] = "org.id = {$id}";
			}
		}

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
		
		if($use_local_org)
		{
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
				$columns[] = 'org.address';
				$columns[] = 'org.district';
				$columns[] = 'org.change_type';
				$columns[] = 'org.transferred';
				$columns[] = 'org.orgno AS organization_number';
				
				$cols = implode(',',$columns);
			}
	
			$tables = "activity_organization org";
		}
		else
		{
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
		}

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
	
	function get_organization_name_local($org_id)
	{
		$result = "Ingen";
    	if(isset($org_id)){
	    	$q1="SELECT name FROM activity_organization WHERE id={$org_id}";
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
	
	function get_contacts_local($organization_id)
	{
		$contacts = array();
    	if(isset($organization_id)){
	    	$q1="SELECT id FROM activity_contact_person WHERE organization_id='{$organization_id}'";
	    	var_dump($q1);
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
	
	function get_description_local($organization_id)
	{
    	if(isset($organization_id)){
	    	$q1="SELECT description FROM activity_organization WHERE id={$organization_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}
	
	
	function get_district_from_name($name)
	{
		$this->db->query("SELECT part_of_town_id FROM fm_part_of_town where name like UPPER('%{$name}%') ", __LINE__, __FILE__);
		while($this->db->next_record()){
			$result = $this->db->f('part_of_town_id');
		}	
		return $result;
	}
	
	function get_office_from_district($district_id)
	{
		if($district_id)
		{
			$district_id = (int)$district_id;
			$q1="SELECT fm_district.descr FROM fm_part_of_town,fm_district WHERE fm_part_of_town.part_of_town_id={$district_id} AND fm_district.id = fm_part_of_town.district_id";
			//var_dump($q1);
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$office_name = $this->db->f('descr');
			}
		}
		return $office_name;
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
	function update_local($organization)
	{
		$name = $organization->get_name();
		$orgnr = $organization->get_organization_number();
		$homepage = $organization->get_homepage();
		$phone = $organization->get_phone();
		$email = $organization->get_email();
		$description = $organization->get_description();
		$street = $organization->get_address();
		$district = $organization->get_district();
		$change_type = $organization->get_change_type();
		$transferred = ($organization->get_transferred() == 1 || $organization->get_transferred() == true)?'true':'false';
		
		$values[] = "NAME='{$name}'";
		$values[] = "HOMEPAGE='{$homepage}'";
		$values[] = "PHONE='{$phone}'";
		$values[] = "EMAIL='{$email}'";
		$values[] = "DESCRIPTION='{$description}'";
		$values[] = "ADDRESS='{$street}'";
		$values[] = "ORGNO='{$orgnr}'";
		$values[] = "DISTRICT='{$district}'";
		$values[] = "CHANGE_TYPE='{$change_type}'";
		$values[] = "TRANSFERRED={$transferred}";
		$vals = implode(',',$values);
		
		$sql = "UPDATE activity_organization SET {$vals} WHERE ID={$organization->get_id()}";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
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
				'table'			=> 'org', // alias
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
			$organization->set_address($this->unmarshal($this->db->f('address'), 'string'));
			$organization->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
			$organization->set_email($this->unmarshal($this->db->f('email'), 'string'));
			$organization->set_homepage($this->unmarshal($this->db->f('homepage'), 'string'));
			$organization->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$organization->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$organization->set_change_type($this->unmarshal($this->db->f('change_type'), 'string'));
			$organization->set_transferred($this->unmarshal($this->db->f('transferred'), 'bool'));
			$organization->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
		}
		return $organization;
	}
	
	function add_organization_local($organization)
	{
		$name = $organization->get_name();
		$orgnr = $organization->get_organization_number();
		$homepage = $organization->get_homepage();
		$phone = $organization->get_phone();
		$email = $organization->get_email();
		$description = $organization->get_description();
		$street = $organization->get_address();
/*		$zip = $organization->get_();
		if($zip && strlen($zip) > 5)
		{
			$zip_code = substr($zip,0,4);
			$city = substr($zip, 5);
		}
		else
		{
			$zip_code = '';
			$city = '';
		}*/
		$district = $organization->get_district();
		
		$values[] = "NAME='{$name}'";
		$values[] = "HOMEPAGE='{$homepage}'";
		$values[] = "PHONE='{$phone}'";
		$values[] = "EMAIL='{$email}'";
		$values[] = "DESCRIPTION='{$description}'";
		$values[] = "STREET='{$street}'";
		//$values[] = "'{$zip_code}'";
		//$values[] = "'{$city}'";
		$values[] = "ORGNO='{$orgnr}'";
		$values[] = "DISTRICT='{$district}'";
		$vals = implode(',',$values);
		
		//var_dump("INSERT INTO activity_organization ({$cols}) VALUES ({$vals})");
		$sql = "UPDATE activity_organization SET {$vals} WHERE ID={$organization->get_id()}";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function transfer_organization($org_info)
	{
		$name = $org_info['name'];
		$orgnr = $org_info['orgnr'];
		$homepage = $org_info['homepage'];
		$phone = $org_info['phone'];
		$email = $org_info['email'];
		$description = $org_info['description'];
		$street = $org_info['street'];
		$zip = $org_info['zip'];
		if($zip && strlen($zip) > 5)
		{
			$zip_code = substr($zip,0,4);
			$city = substr($zip, 5);
		}
		else
		{
			$zip_code = '';
			$city = '';
		}
		$district = $org_info['district'];
		$activity_id = $org_info['activity_id'];
		$show_in_portal = 1; 
		
		$columns[] = 'name';
		$columns[] = 'homepage';
		$columns[] = 'phone';
		$columns[] = 'email';
		$columns[] = 'description';
		$columns[] = 'street';
		$columns[] = 'zip_code';
		$columns[] = 'city';
		$columns[] = 'district';
		$columns[] = 'organization_number';
		$columns[] = 'activity_id';
		$columns[] = 'show_in_portal';
		$cols = implode(',',$columns);
		
		$values[] = "'{$name}'";
		$values[] = "'{$homepage}'";
		$values[] = "'{$phone}'";
		$values[] = "'{$email}'";
		$values[] = "'{$description}'";
		$values[] = "'{$street}'";
		$values[] = "'{$zip_code}'";
		$values[] = "'{$city}'";
		$values[] = "'{$district}'";
		$values[] = "'{$orgnr}'";
		$values[] = $this->marshal($activity_id, 'int');
		$values[] = $show_in_portal;
		$vals = implode(',',$values);
		
		$sql = "INSERT INTO bb_organization ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('bb_organization', 'id');
		}
		else
		{
			return 0;
		}
	}
	
	function update($organization)
	{
		return false;
	}
}
?>
