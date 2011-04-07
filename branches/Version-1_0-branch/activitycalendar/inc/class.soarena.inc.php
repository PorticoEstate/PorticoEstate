<?php

phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'arena', 'inc/model/');

class activitycalendar_soarena extends activitycalendar_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return activitycalendar_soparty the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('activitycalendar.soarena');
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
			if($sort_field == 'arena_id')
			{
				$sort_field='id';
			}
			$order = "ORDER BY {$this->marshal($sort_field,'field')} $dir";
		}
/*		if($search_for)
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
		}*/

		$filter_clauses = array();
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "arena.id = {$id}";
		}
		
		//$filter_clauses[] = "show_in_portal";
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
			$cols = 'COUNT(DISTINCT(arena.id)) AS count';
		}
		else
		{
			$columns[] = 'arena.id';
			$columns[] = 'arena.arena_name';
			$columns[] = 'arena.address';
			$columns[] = 'arena.internal_arena_id';
			
			$cols = implode(',',$columns);
		}

		$tables = "activity_arena arena";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";

		$joins = $join_contracts;
		//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $arena_id, &$arena)
	{

		if($arena == null) {
			$arena = new activitycalendar_arena((int) $arena_id);

			$arena->set_arena_name($this->unmarshal($this->db->f('arena_name'), 'string'));
			$arena->set_address($this->unmarshal($this->db->f('address'), 'string'));
			$arena->set_internal_arena_id($this->unmarshal($this->db->f('internal_arena_id'), 'int'));
		}
		return $arena;
	}
	
	/**
	 * Function for adding a new arena to the database. Updates the arena object.
	 *
	 * @param activitycalendar_arena $arena the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$arena)
	{
		// Insert a new arena
		$q ="INSERT INTO activity_arena (arena_name) VALUES ('test')";
		$result = $this->db->query($q);

		if(isset($result)) {
			// Set the new party ID
			$arena->set_id($this->db->get_last_insert_id('activity_arena', 'id'));
			// Forward this request to the update method
			return $this->update($arena);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update the database values for an existing arena object.
	 *
	 * @param $arena the party to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($arena)
	{
		$id = intval($arena->get_id());
		
		$values = array(
			'arena_name = '		. $this->marshal($arena->get_arena_name(), 'string'),
			'address = '     . $this->marshal($arena->get_address(), 'string'),
			'internal_arena_id =  '     . $this->marshal($arena->get_internal_arena_id(), 'int')
		);
		
		$result = $this->db->query('UPDATE activity_arena SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
			
		return isset($result);
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
				'table'			=> 'arena', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}
}
?>