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
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

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
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'group', // alias
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

	protected function populate(int $group_id, &$group)
	{

		if($group == null) {
			$group = new activitycalendar_group((int) $group_id);

			$group->set_name($this->unmarshal($this->db->f('name'), 'string'));
			$group->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$group->set_shortname($this->unmarshal($this->db->f('shortname'), 'string'));
			$group->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$group->set_show_in_portal($this->unmarshal($this->db->f('show_in_portal'), 'int'));
		}
		return $group;
	}
}
?>
