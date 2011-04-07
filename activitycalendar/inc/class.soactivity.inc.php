<?php
phpgw::import_class('activitycalendar.socommon');

include_class('activitycalendar', 'activity', 'inc/model/');

class activitycalendar_soactivity extends activitycalendar_socommon
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
			self::$so = CreateObject('activitycalendar.soactivity');
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
			$cols = 'COUNT(DISTINCT(activity.id)) AS count';
		}
		else
		{
			$columns[] = 'activity.id';
			$columns[] = 'activity.organization_id';
			$columns[] = 'activity.group_id';
			$columns[] = 'activity.district';
			$columns[] = 'activity.category';
			$columns[] = 'activity.description';
			$columns[] = 'activity.arena';
			$columns[] = 'activity.date_start';
			$columns[] = 'activity.date_end';
			$columns[] = 'activity.contact_person_1';
			$columns[] = 'activity.contact_person_2';
			
			$cols = implode(',',$columns);
		}

		$tables = "activity_activity activity";

		//$join_contracts = "	{$this->left_join} rental_contract_party c_p ON (c_p.party_id = party.id)
		//{$this->left_join} rental_contract contract ON (contract.id = c_p.contract_id)";
		
		//var_dump("SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} WHERE {$condition} {$order}";
	}



	/**
	 * Function for adding a new activity to the database. Updates the activity object.
	 *
	 * @param activitycalendar_activity $activity the party to be added
	 * @return bool true if successful, false otherwise
	 */
	function add(&$activity)
	{
		// Insert a new activity
		$q ="INSERT INTO activity_activity (organization_id) VALUES (1)";
		$result = $this->db->query($q);

		if(isset($result)) {
			// Set the new party ID
			$activity->set_id($this->db->get_last_insert_id('activity_activity', 'id'));
			// Forward this request to the update method
			return $this->update($activity);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */
	function update($activity)
	{
		$id = intval($activity->get_id());
			
		$values = array(
			'organization_id = '. $this->marshal($activity->get_organization_id(), 'string'),
			'group_id = '     . $this->marshal($activity->get_group_id(), 'string'),
			'district =  '     . $this->marshal($activity->get_district(), 'string'),
			'category = '          . $this->marshal($activity->get_category(), 'string'),
			'target = '   . $this->marshal($activity->get_target(), 'string'),
			'description = '     . $this->marshal($activity->get_description(), 'string'),
			'arena = '      . $this->marshal($activity->get_arena(), 'string'),
			'date_start = '      . $this->marshal($activity->get_date_start(), 'string'),
			'date_end = '    . $this->marshal($activity->get_date_end(), 'string'),
			'contact_person_1 = '          . $this->marshal($activity->get_contact_person_1(), 'string'),
			'contact_person_2 = '          . $this->marshal($activity->get_contact_person_2(), 'string')
		);
		
		$result = $this->db->query('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
			
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
				'table'			=> 'activity', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		return $ret;
	}

	protected function populate(int $activity_id, &$activity)
	{

		if($activity == null) {
			$activity = new activitycalendar_activity((int) $activity_id);

			$activity->set_organization_id($this->unmarshal($this->db->f('organization_id'), 'int'));
			$activity->set_group_id($this->unmarshal($this->db->f('group_id'), 'int'));
			$activity->set_district($this->unmarshal($this->db->f('district'), 'string'));
			$activity->set_category($this->unmarshal($this->db->f('category'), 'string'));
			$activity->set_description($this->unmarshal($this->db->f('description'), 'string'));
			$activity->set_arena($this->unmarshal($this->db->f('arena'), 'string'));
			$activity->set_date_start($this->unmarshal($this->db->f('date_start'), 'int'));
			$activity->set_date_end($this->unmarshal($this->db->f('date_end'), 'int'));
			$activity->set_contact_person_1($this->unmarshal($this->db->f('contact_person_1'), 'int'));
			$activity->set_contact_person_2($this->unmarshal($this->db->f('contact_person_2'), 'int'));
		}
		return $activity;
	}
}