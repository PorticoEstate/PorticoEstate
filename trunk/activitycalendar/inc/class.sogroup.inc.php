<?php

phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'group', 'inc/model/');

class activitycalendar_sogroup extends activitycalendar_socommon
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
			self::$so = CreateObject('activitycalendar.sogroup');
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
			$order = "ORDER BY bb_group.id $dir";
		}
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "name":
					$like_clauses[] = "group.name $this->like $like_pattern";
					$like_clauses[] = "group.shortname $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$use_local_group = false;
		$filter_clauses = array();
		$filter_clauses[] = "bb_group.show_in_portal=1"; 
		if(isset($filters['org_id']))
		{
			$group_org_id = $this->marshal($filters['org_id'],'int');
			if(isset($group_org_id) && $group_org_id > 0)
			{
				$filter_clauses[] = "bb_group.organization_id = {$group_org_id}";
			}
		}
		if(isset($filters['changed_groups'])){
			$use_local_group = true;
			unset($filter_clauses);
			if(isset($filters[$this->get_id_field_name()])){
				$id = $this->marshal($filters[$this->get_id_field_name()],'int');
				$filter_clauses[] = "activity_group.id = {$id}";
			}
		}
		if(isset($filters['new_groups'])){
			$use_local_group = true;
			unset($filter_clauses);
			$filter_clauses[] = "(activity_group.change_type = 'new' OR activity_group.change_type = 'change') ";
			if(isset($filters['group_id'])){
				$id = $this->marshal($filters['group_id'],'int');
				$filter_clauses[] = "activity_group.id = {$id}";
			}
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		if($use_local_group)
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(activity_group.id)) AS count';
			}
			else
			{
				$columns[] = 'activity_group.id';
				$columns[] = 'activity_group.name';
				$columns[] = 'activity_group.description';
				$columns[] = 'activity_group.organization_id';
				$columns[] = 'activity_group.change_type';
				$columns[] = 'activity_group.transferred';
				$columns[] = 'activity_group.original_group_id';
				
				$dir = $ascending ? 'ASC' : 'DESC';
				$order = "ORDER BY activity_group.id $dir";
				
				$cols = implode(',',$columns);
			}
	
			$tables = "activity_group";
		}
		else
		{
			if($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(bb_group.id)) AS count';
			}
			else
			{
				$columns[] = 'bb_group.id';
				$columns[] = 'bb_group.name';
				$columns[] = 'bb_group.description';
				$columns[] = 'bb_group.organization_id';
				$columns[] = 'bb_group.activity_id';
				$columns[] = 'bb_group.active';
				$columns[] = 'bb_group.shortname';
				$columns[] = 'bb_group.show_in_portal';
				
				$cols = implode(',',$columns);
			}
	
			$tables = "bb_group";
		}
		
		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";
		
		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new party to the database. Updates the party object.
	 *
	 * @param rental_party $party the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$group)
	{
		return false;
	}

	/**
	 * Update the database values for an existing party object.
	 *
	 * @param $party the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($group)
	{
		return false;
	}

	public function get_id_field_name($extended_info = false)
	{
		if(!$extended_info)
		{
			$ret = array
			(
				'table'			=> 'activity_group', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		else
		{
			$ret = array
			(
				'table'			=> 'activity_group', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}
	
	function get_group_name($group_id)
	{
		$result = "Ingen";
    	if(isset($group_id)){
	    	$q1="SELECT name FROM bb_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}
		
		return $result;
	}
	
	function get_group_name_local($group_id)
	{
		$result = "Ingen";
    	if(isset($group_id)){
	    	$q1="SELECT name FROM activity_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
    	}
		
		return $result;
	}
	
	function get_orgid_from_group($group_id)
	{
		$result = 0;
    	if(isset($group_id)){
	    	$q1="SELECT organization_id FROM bb_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('organization_id');
			}
    	}
		
		return $result;
	}
	
	function get_orgid_from_group_local($group_id)
	{
		$result = 0;
    	if(isset($group_id)){
	    	$q1="SELECT organization_id FROM activity_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('organization_id');
			}
    	}
		
		return $result;
	}
	
	function get_contacts($group_id)
	{
		$contacts = array();
    	if(isset($group_id)){
	    	$q1="SELECT id FROM bb_group_contact WHERE group_id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contacts[] = $this->db->f('id');
			}
			//$result = $contacts;
    	}
		
		return $contacts;
	}
	
	function get_contacts_as_objects($group_id)
	{
		$contacts = array();
    	if(isset($group_id)){
	    	$q1="SELECT * FROM bb_group_contact WHERE group_id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person((int) $this->db->f('id'));
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$contacts[] = $contact_person;
			}
			//$result = $contacts;
    	}
		
		return $contacts;
	}
	
	function get_contacts_local($group_id)
	{
		$contacts = array();
    	if(isset($group_id)){
	    	$q1="SELECT id FROM activity_contact_person WHERE group_id='{$group_id}'";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contacts[] = $this->db->f('id');
			}
			//$result = $contacts;
    	}
		
		return $contacts;
	}
	
	function get_contacts_local_as_objects($group_id)
	{
		$contacts = array();
    	if(isset($group_id)){
	    	$q1="SELECT * FROM activity_contact_person WHERE group_id='{$group_id}'";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$contact_person = new activitycalendar_contact_person((int) $this->db->f('id'));
				$contact_person->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
				$contact_person->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
				$contact_person->set_name($this->unmarshal($this->db->f('name'), 'string'));
				$contact_person->set_phone($this->unmarshal($this->db->f('phone'), 'string'));
				$contact_person->set_email($this->unmarshal($this->db->f('email'), 'string'));
				$contacts[] = $contact_person;
			}
			//$result = $contacts;
    	}
		
		return $contacts;
	}
	
	function get_description($group_id)
	{
    	if(isset($group_id)){
	    	$q1="SELECT description FROM bb_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}
	
	function get_description_local($group_id)
	{
    	if(isset($group_id)){
	    	$q1="SELECT description FROM activity_group WHERE id={$group_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$desc = $this->db->f('description');
			}
    	}
		return $desc;
	}

	protected function populate(int $group_id, &$group)
	{

		if($group == null) {
			$group = new activitycalendar_group((int) $group_id);

			$group->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$group->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$group->set_shortname($this->unmarshal($this->db->f('shortname'), 'string'));
			$group->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$group->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
			$group->set_change_type($this->unmarshal($this->db->f('change_type'), 'string'));
			$group->set_transferred($this->unmarshal($this->db->f('transferred'), 'bool'));
			$group->set_original_group_id($this->unmarshal($this->db->f('original_group_id'), 'int'));
		}
		return $group;
	}
	
	function update_local($group)
	{
		$name = $group->get_name();
		$orgid = $group->get_organization_id();
		$description = $group->get_description();
		$change_type = $group->get_change_type();
		$transferred = ($group->get_transferred() == 1 || $group->get_transferred() == true)?'true':'false';
		
		$values[] = "NAME='{$name}'";
		$values[] = "DESCRIPTION='{$description}'";
		$values[] = "ORGANIZATION_ID='{$orgid}'";
		$values[] = "CHANGE_TYPE='{$change_type}'";
		$values[] = "TRANSFERRED={$transferred}";
		$vals = implode(',',$values);
		
		$sql = "UPDATE activity_group SET {$vals} WHERE ID={$group->get_id()}";
		var_dump($sql);
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
	
	function transfer_group($group_info)
	{
		$name = $group_info['name'];
		$orgid = $group_info['organization_id'];
		$description = $group_info['description'];
		$activity_id = 1;
		$show_in_portal = 1; 
		
		$columns[] = 'name';
		$columns[] = 'description';
		$columns[] = 'organization_id';
		$columns[] = 'activity_id';
		$columns[] = 'show_in_portal';
		$cols = implode(',',$columns);
		
		$values[] = "'{$name}'";
		$values[] = "'{$description}'";
		$values[] = "'{$orgid}'";
		$values[] = $activity_id;
		$values[] = $show_in_portal;
		$vals = implode(',',$values);
		
		$sql = "INSERT INTO bb_group ({$cols}) VALUES ({$vals})";
    	$result = $this->db->query($sql, __LINE__, __FILE__);
		if(isset($result))
		{
			return $this->db->get_last_insert_id('bb_group', 'id');
		}
		else
		{
			return 0;
		}
	}
	
	function get_group_local($g_id)
	{
		$columns[] = 'activity_group.id';
		$columns[] = 'activity_group.name';
		$columns[] = 'activity_group.description';
		$columns[] = 'activity_group.organization_id';
		$columns[] = 'activity_group.change_type';
		$columns[] = 'activity_group.transferred';
		$columns[] = 'activity_group.original_group_id';
		
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = "ORDER BY activity_group.id $dir";
		
		$cols = implode(',',$columns);
		$table = "activity_group";
		
		$sql = "SELECT {$cols} FROM {$table} WHERE activity_group.id={$g_id}";
		$result = $this->db->query($sql, __LINE__, __FILE__);
		while($this->db->next_record())
		{
			$group = new activitycalendar_group((int) $g_id);

			$group->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$group->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$group->set_shortname($this->unmarshal($this->db->f('shortname'), 'string'));
			$group->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$group->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
			$group->set_change_type($this->unmarshal($this->db->f('change_type'), 'string'));
			$group->set_transferred($this->unmarshal($this->db->f('transferred'), 'bool'));
			$group->set_original_group_id($this->unmarshal($this->db->f('original_group_id'), 'int'));
			
			return $group;
		}
		
	}
}
?>
