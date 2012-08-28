<?php
phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'contact_person', 'inc/model/');

class activitycalendar_socontactperson extends activitycalendar_socommon
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
			self::$so = CreateObject('activitycalendar.socontactperson');
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
		$table = "";

		//Add columns to this array to include them in the query
		$columns = array();

/*		if($sort_field != null) {
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY id $dir";
		}
		*/
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
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
				case "identifier":
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					break;
				case "reskontro":
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
				case "result_unit_number":
					$like_clauses[] = "party.result_unit_number $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "party.address_1 $this->like $like_pattern";
					$like_clauses[] = "party.address_2 $this->like $like_pattern";
					$like_clauses[] = "party.postal_code $this->like $like_pattern";
					$like_clauses[] = "party.place $this->like $like_pattern";
					$like_clauses[] = "party.identifier $this->like $like_pattern";
					$like_clauses[] = "party.comment $this->like $like_pattern";
					$like_clauses[] = "party.reskontro $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}
		
		$filter_clauses = array();
		$contact_person_id = $this->marshal($filters['id'],'int');
		if(isset($filters['org_id']))
		{
			$org_id = $this->marshal($filters['org_id'],'int');
			if(isset($org_id) && $org_id > 0)
			{
				$filter_clauses[] = "organization_contact.organization_id = {$org_id}";
				$filter_clauses[] = "organization_contact.id = {$contact_person_id}";
				$table = "bb_organization_contact organization_contact";
				
			}
		}
		if(isset($filters['organization_id']))
		{
			$org_id = $this->marshal($filters['organization_id'],'int');
			if(isset($org_id) && $org_id > 0)
			{
				$filter_clauses[] = "organization_contact.organization_id = {$org_id}";
				$table = "bb_organization_contact organization_contact";
				
			}
		}
		else if(isset($filters['group_id']))
		{
			$group_id = $this->marshal($filters['group_id'],'int');
			if(isset($group_id) && $group_id > 0)
			{
				$filter_clauses[] = "group_contact.group_id = {$group_id}";
				$filter_clauses[] = "group_contact.id = {$contact_person_id}";
				$table = "bb_group_contact group_contact";
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

		if($table == "bb_organization_contact organization_contact")
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(organization_contact.id)) AS count';
			}
			else
			{
				$columns[] = 'organization_contact.id';
				$columns[] = 'organization_contact.name';
				$columns[] = 'organization_contact.ssn';
				$columns[] = 'organization_contact.phone';
				$columns[] = 'organization_contact.email';
				$columns[] = 'organization_contact.organization_id';
				
				$cols = implode(',',$columns);
			}
		}
		else
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(group_contact.id)) AS count';
			}
			else
			{
				$columns[] = 'group_contact.id';
				$columns[] = 'group_contact.name';
				$columns[] = 'group_contact.phone';
				$columns[] = 'group_contact.email';
				$columns[] = 'group_contact.group_id';
				
				$cols = implode(',',$columns);
			}
		}

		$tables = $table;

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";
		
		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}


	function get_group_contact_name($id)
	{
		$result = "Ingen";
    	if(isset($id) && $id != ''){
	    	$q1="SELECT name, phone, email FROM bb_group_contact WHERE id={$id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name') . "<br/>" . $this->db->f('phone') . "<br/>" . $this->db->f('email');
			}
    	}
		return $result;
	}
	
	function get_group_contact_name_local($id)
	{
		$result = "Ingen";
    	if(isset($id) && $id != ''){
	    	$q1="SELECT name, phone, email FROM activity_contact_person WHERE id={$id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name') . "<br/>" . $this->db->f('phone') . "<br/>" . $this->db->f('email');
			}
    	}
		return $result;
	}
	
	function get_org_contact_name($id)
	{
		$result = "Ingen";
    	if(isset($id) && $id != ''){
	    	$q1="SELECT name, phone, email FROM bb_organization_contact WHERE id={$id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name') . "<br/>" . $this->db->f('phone') . "<br/>" . $this->db->f('email');
			}
    	}
		return $result;
	}
	
	function get_org_contact_name_local($id)
	{
		$result = "Ingen";
    	if(isset($id) && $id != ''){
	    	$q1="SELECT name, phone, email FROM activity_contact_person WHERE id={$id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name') . "<br/>" . $this->db->f('phone') . "<br/>" . $this->db->f('email');
			}
    	}
		return $result;
	}
	
	
	function get_mailaddress_for_group_contact($contact_person_id)
	{
		if($contact_person_id){
	    	$q1="SELECT email FROM bb_group_contact WHERE id={$contact_person_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('email');
			}
    	}
		return $result;
	}
	
	function get_mailaddress_for_org_contact($contact_person_id)
	{
		if($contact_person_id){
	    	$q1="SELECT email FROM bb_organization_contact WHERE id={$contact_person_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('email');
			}
    	}
		return $result;
	}
	

	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$contact_person)
	{
		return false;
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($contact_person)
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
				'table'			=> 'activity', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}

	protected function populate(int $contact_person_id, &$contact_person)
	{

		if($contact_person == null) {
			$contact_person = new activitycalendar_contact_person((int) $contact_person_id);

			$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
			$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
			$contact_person->set_ssn($this->unmarshal($this->db->f('ssn'), 'string'));
		}
		return $contact_person;
	}
	
	function get_local_contact_persons($id, $group=false)
	{
		$result = array();
    	if(isset($id)){
    		if($group)
    		{
    			$q1="SELECT id, organization_id, group_id, name, phone, email FROM activity_contact_person WHERE group_id='{$id}'";
    		}
    		else
    		{
	    		$q1="SELECT id, organization_id, group_id, name, phone, email FROM activity_contact_person WHERE organization_id='{$id}' and group_id='0'";
    		}
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person($this->db->f('id'), 'int');
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$result[] = $contact_person;
			}
    	}
		return $result;
	}
	
	function get_booking_contact_persons($id, $group=false)
	{
		$result = array();
    	if(isset($id)){
    		$columns[] = 'group_contact.id';
				$columns[] = 'group_contact.name';
				$columns[] = 'group_contact.phone';
				$columns[] = 'group_contact.email';
				$columns[] = 'group_contact.group_id';
    		if($group)
    		{
    			$q1="SELECT id, group_id, name, phone, email FROM bb_group_contact WHERE group_id='{$id}'";
    		}
    		else
    		{
	    		$q1="SELECT id, organization_id, name, phone, email, ssn FROM bb_organization_contact WHERE organization_id='{$id}'";
    		}
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person($this->db->f('id'), 'int');
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$result[] = $contact_person;
			}
    	}
		return $result;
	}
	
	function update_local_contact_person($contact)
	{
		$id = $contact['id'];
		$name = $contact['name'];
		$phone = $contact['phone'];
		$mail = $contact['mail'];
		$org_id = $contact['org_id'];
		$group_id = $contact['group_id'];
		
		$columns[] = "name='{$name}'";
		$columns[] = "phone='{$phone}'";
		$columns[] = "email='{$mail}'";
		$columns[] = "organization_id={$org_id}";
		$columns[] = "group_id={$group_id}";
		$columns[] = "address=''";
		$columns[] = "zipcode=''"; 
		$columns[] = "city=''";
		$cols = implode(',',$columns);

		$sql = "UPDATE activity_contact_person SET {$cols} WHERE id={$id}";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		return isset($result);
	}
        
        function add_new_group_contact($contact)
	{
            $name = $contact->get_name();
            $phone = $contact->get_phone();
            $mail = $contact->get_email();
            $group_id = $contact->get_group_id();

            $columns[] = 'name';
            $columns[] = 'phone';
            $columns[] = 'email';
            $columns[] = 'group_id';
            $cols = implode(',',$columns);

            $values[] = "'{$name}'";
            $values[] = "'{$phone}'";
            $values[] = "'{$mail}'";
            $values[] = $group_id;
            $vals = implode(',',$values);

            $sql = "INSERT INTO bb_group_contact ({$cols}) VALUES ({$vals})";
            $result = $this->db->query($sql, __LINE__, __FILE__);
            
            if(isset($result))
            {
                return $this->db->get_last_insert_id('bb_group_contact', 'id');
            }
            else
            {
                return 0;
            }
	}
}