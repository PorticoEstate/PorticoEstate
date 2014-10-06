<?php

phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'arena', 'inc/model/');
include_class('activitycalendar', 'building', 'inc/model/');

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
		else if(!$return_count)
		{
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = "ORDER BY arena.arena_name $dir";
		}
		
		if($search_for)
		{
			$query = $this->marshal($search_for,'string');
			$like_pattern = "'%".$search_for."%'";
			$like_clauses = array();
			switch($search_type){
				case "all":
				default:
					$like_clauses[] = "arena.arena_name $this->like $like_pattern";
					$like_clauses[] = "arena.address $this->like $like_pattern";
					break;
			}


			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "arena.id = {$id}";
		}
		
		//filter on active/non-active
		if(isset($filters['active']))
		{
			if($filters['active'] == 'active')
			{
				$filter_clauses[] = "arena.active = TRUE";
			} 
			else if($filters['active'] == 'inactive')
			{
				$filter_clauses[] = "NOT arena.active";
			} 
		}
		
		//filter on internal/not internal arena
		if(isset($filters['arena_type']))
		{
			if($filters['arena_type'] == 'internal')
			{
				$filter_clauses[] = "NOT arena.internal_arena_id IS NULL";
			}
			else if($filters['arena_type'] == 'not_internal')
			{
				$filter_clauses[] = "arena.internal_arena_id IS NULL";
			}
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);
		//var_dump($filters);
		//var_dump($filter_clauses);
		//var_dump($condition);

		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(arena.id)) AS count';
		}
		else
		{
			$columns[] = 'arena.id';
			$columns[] = 'arena.arena_name';
			$columns[] = 'arena.address';
			$columns[] = 'arena.addressnumber';
			$columns[] = 'arena.zip_code';
			$columns[] = 'arena.city';
			$columns[] = 'arena.internal_arena_id';
			$columns[] = 'arena.active';
			
			$cols = implode(',',$columns);
		}

		$tables = "activity_arena arena";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";

		$joins = $join_contracts;
		//var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	/**
	 * Populates an arena-object
	 * 
	 *  @param int $arena_id
	 *  @param activitycalendar_arena $arena
	 *  @return activitycalendar_arena $arena
	 */
	protected function populate(int $arena_id, &$arena)
	{

		if($arena == null) {
			$arena = new activitycalendar_arena((int) $arena_id);

			$arena->set_arena_name($this->unmarshal($this->db->f('arena_name'), 'string'));
			$arena->set_address($this->unmarshal($this->db->f('address'), 'string'));
			$arena->set_addressnumber($this->unmarshal($this->db->f('addressnumber'), 'string'));
			$arena->set_zip_code($this->unmarshal($this->db->f('zip_code'), 'string'));
			$arena->set_city($this->unmarshal($this->db->f('city'), 'string'));
			$arena->set_internal_arena_id($this->unmarshal($this->db->f('internal_arena_id'), 'int'));
			$arena->set_active($this->unmarshal($this->db->f('active'), 'bool'));
		}
		return $arena;
	}
	
	/**
	 * Get arena name
	 *
	 * @param int $arena_id
	 * @return string arena name
	 */
	function get_arena_name($arena_id)
	{
		$result = "Ingen";
    	if(isset($arena_id) && $arena_id != ''){
	    	$q1="SELECT arena_name FROM activity_arena WHERE id={$arena_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('arena_name');
			}
    	}
		
		return $result;
	}
	
	/**
	 * Get registered buildings from property
	 *
	 * @return array buildings, [id => name]
	 */
	function get_buildings()
	{
		$buildings = array();
    	$q_buildings="SELECT id, name FROM bb_building WHERE active=1 ORDER BY name ASC";
    	//var_dump($q_buildings);
		$this->db->query($q_buildings, __LINE__, __FILE__);
		while($this->db->next_record()){
			$id = $this->db->f('id');
			$buildings[$id] = $this->db->f('name');
		}
		return $buildings;
	}
	
	/**
	 * Get building name from property
	 *
	 * @param int $building_id
	 * @return string building name
	 */
	function get_building_name($building_id){
		if(isset($building_id))
		{
			$building_id = (int)$building_id;
			$q1="SELECT name FROM bb_building WHERE id={$building_id}";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('name');
			}
		}
		return $result;
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
	 * @param $arena the arena to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($arena)
	{
		$id = intval($arena->get_id());
		
		$values = array(
			'arena_name = '		. $this->marshal($arena->get_arena_name(), 'string'),
			'address = '     . $this->marshal($arena->get_address(), 'string'),
    		'addressnumber = '     . $this->marshal($arena->get_addressnumber(), 'string'),
    		'zip_code = '     . $this->marshal($arena->get_zip_code(), 'string'),
    		'city = '     . $this->marshal($arena->get_city(), 'string'),
			'internal_arena_id =  '     . $this->marshal($arena->get_internal_arena_id(), 'int'),
			'active = '     . $this->marshal(($arena->is_active() ? 'true' : 'false'), 'bool'),
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
	
	public function get_address($search)
	{
		$result_arr = array();
	    $curr_index=0;
		if($search)
		{
			$sql = "select * from fm_streetaddress where UPPER(descr) like UPPER('{$search}%')";
			$this->db->query($sql, __LINE__, __FILE__);
			while($this->db->next_record()){
				//$result_arr = $this->db->f('name');
				if($curr_index == 0){
                                    $result_arr[] = "<option value='0'>Velg gateadresse</option>";
				}
				$result_arr[] = "<option value='" . $this->db->f('descr') . "'>" . $this->db->f('descr') . "</option>";
				$curr_index++;
			}
		}
		$result = implode(' ' , $result_arr);
		return $result;
	}
	
	public function get_arena_id_by_name($arena_name)
	{
		$result = 0;
		if(isset($arena_name) && $arena_name != ''){
	    	$q1="SELECT id FROM activity_arena WHERE UPPER(arena_name) = UPPER('{$arena_name}')";
			$this->db->query($q1, __LINE__, __FILE__);
			while($this->db->next_record()){
				$result = $this->db->f('id');
			}
    	}
    	return $result;
	}
}
?>