<?php
phpgw::import_class('controller.socommon');

include_class('controller', 'control_group', 'inc/model/');

class controller_socontrol_group extends controller_socommon
{
	protected static $so;

	/**
	 * Get a static reference to the storage object associated with this model object
	 *
	 * @return controller_socontrol_group the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('controller.socontrol_group');
		}
		return self::$so;
	}

	/**
	 * Function for adding a new control group to the database.
	 *
	 * @param controller_control_group $control_group the control group to be added
	 * @return int id of the new control group object
	 */
	function add(&$control_group)
	{
		$cols = array(
				'group_name',
				'procedure_id',
				'control_area_id',
				'building_part_id'
		);
			
		$values = array(
			$this->marshal($control_group->get_group_name(), 'string'),
			$this->marshal($control_group->get_procedure_id(), 'int'),
			$this->marshal($control_group->get_control_area_id(), 'int'),
			$this->marshal($control_group->get_building_part_id(), 'int'),
		);
		
		$result = $this->db->query('INSERT INTO controller_control_group (' . join(',', $cols) . ') VALUES (' . join(',', $values) . ')', __LINE__,__FILE__);
		
		if(isset($result)) {
			// Get the new control group ID and return it
			return $this->db->get_last_insert_id('controller_control_group', 'id');
		}
		else
		{
			return 0;
		}
			
	}

	/**
	 * Update the database values for an existing activity object.
	 *
	 * @param $activity the activity to be updated
	 * @return boolean true if successful, false otherwise
	 */

	function update($control_group)
	{	
		$id = intval($control_group->get_id());
			
		$values = array(
			'group_name = ' . $this->marshal($control_group->get_group_name(), 'string'),
			'procedure_id = '. $this->marshal($control_group->get_procedure_id(), 'int'),
			'control_area_id = ' . $this->marshal($control_group->get_control_area_id(), 'int'),
			'building_part_id = ' . $this->marshal($control_group->get_building_part_id(), 'int')
		);
		
		//var_dump('UPDATE activity_activity SET ' . join(',', $values) . " WHERE id=$id");
		$result = $this->db->query('UPDATE controller_control_group SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		return isset($result);
	}
	
	/**
	 * Get single procedure
	 * 
	 * @param	$id	id of the procedure to return
	 * @return a controller_procedure
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$joins = "	{$this->left_join} fm_building_part ON (p.building_part_id = CAST(fm_building_part.id AS INT))";
		$joins .= "	{$this->left_join} controller_procedure ON (p.procedure_id = controller_procedure.id)";
		$joins .= "	{$this->left_join} controller_control_area ON (p.control_area_id = controller_control_area.id)";
		
		$sql = "SELECT p.*, fm_building_part.descr AS building_part_descr, controller_procedure.title as procedure_title, controller_control_area.title as control_area_name FROM controller_control_group p {$joins} WHERE p.id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
		$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
		$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
		$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
		$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
		$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
		$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'int'));
		$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));
		
		return $control_group;
	}
	
	/**
	 * Get a list of procedure objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_composite objects
	 */
	function get_control_group_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		//$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		//$sql = "SELECT * FROM controller_procedure WHERE $condition $order";
		$sql = "SELECT * FROM controller_control_group $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
			
			$results[] = $control_group;
		}
		
		return $results;
	}
	
	function get_control_group_select_array()
	{
            $results = array();
			$results[] = array('id' =>  0,'name' => lang('Not selected'));
			$this->db->query("SELECT id, group_name as name FROM controller_control_group ORDER BY name ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = array('id' => $this->db->f('id', false),
						           'name' => $this->db->f('name', false));
			}
			return $results;
	}
	
	function get_building_part_select_array($selected_building_part_id)
	{
            $results = array();
			$results[] = array('id' =>  0,'name' => lang('Not selected'));
			$this->db->query("SELECT id, descr as name FROM fm_building_part ORDER BY id ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$curr_id = $this->db->f('id', false);
				if($selected_building_part_id && $selected_building_part_id > 0 && $selected_building_part_id == $curr_id)
				{
					$results[] = array('id' => $this->db->f('id', false),
							           'name' => $this->db->f('name', false),
									   'selected' => 'yes');
				}
				else
				{
					$results[] = array('id' => $this->db->f('id', false),
							           'name' => $this->db->f('name', false));
				}
			}
			return $results;
	}
	
	function get_control_groups($control_area_id)
	{
		$results = array();
		
		$sql = "SELECT * FROM controller_control_group WHERE control_area_id=$control_area_id";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$control_group = new controller_control_group($this->unmarshal($this->db->f('id', true), 'int'));
			$control_group->set_group_name($this->unmarshal($this->db->f('group_name', true), 'string'));
			$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id', true), 'string'));
			$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id', true), 'string'));
			
			$results[] = $control_group;
		}
		
		return $results;
	}
	
	function get_id_field_name($extended_info = false)
	{
		
		if(!$extended_info)
		{
			$ret = 'id';
		}
		else
		{
			$ret = array
			(
				'table'			=> 'controller_control_group', // alias
				'field'			=> 'id',
				'translated'	=> 'id'
			);
		}
		
		return $ret;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');
		if($search_for)
		{
			$like_pattern = "'%" . $this->db->db_addslashes($search_for) . "%'";
			$like_clauses = array();
			switch($search_type){
				default:
					$like_clauses[] = "controller_control_group.group_name $this->like $like_pattern";
					break;
			}
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
		}

		$filter_clauses = array();
		/*switch($filters['is_active']){
			case "active":
				$filter_clauses[] = "rental_composite.is_active = TRUE";
				break;
			case "non_active":
				$filter_clauses[] = "rental_composite.is_active = FALSE";
				break;
			case "both":
				break;
		}*/
		/*
		$special_query = false;	//specify if the query should use distinct on rental_composite.id (used for selecting composites that has an active or inactive contract)
		$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
		$availability_date_from = $ts_query;
		$availability_date_to = $ts_query;
		
		if(isset($filters['availability_date_from']) && $filters['availability_date_from'] != ''){
			$availability_date_from = strtotime($filters['availability_date_from']); 
		}
		
		if(isset($filters['availability_date_to']) && $filters['availability_date_to'] != ''){
			$availability_date_to = strtotime($filters['availability_date_to']); 
		}
		*/
		/*switch($filters['has_contract']){
			case "has_contract":
				$filter_clauses[] = "NOT rental_contract_composite.contract_id IS NULL"; // Composite must have a contract
				$filter_clauses[] = "NOT rental_contract.date_start IS NULL"; // The contract must have start date
			*/	
				/* The contract's start date not after the end of the period if there is no end date */
/*				$filter_clauses[] = "
					((NOT rental_contract.date_start > $availability_date_to AND rental_contract.date_end IS NULL)
					 OR
					(NOT rental_contract.date_start > $availability_date_to AND NOT rental_contract.date_end IS NULL AND NOT rental_contract.date_end < $availability_date_from))";
				$special_query=true;
				break;
			case "has_no_contract":
				$filter_clauses[] = "
				(
					rental_contract_composite.contract_id IS NULL OR 
					NOT rental_composite.id IN 
					(
						SELECT rental_composite.id FROM rental_composite 
						LEFT JOIN  rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id) 
						LEFT JOIN  rental_contract ON (rental_contract.id = rental_contract_composite.contract_id) 
						WHERE  
						(
							NOT rental_contract_composite.contract_id IS NULL AND
							NOT rental_contract.date_start IS NULL AND
							((NOT rental_contract.date_start > $availability_date_to AND rental_contract.date_end IS NULL)
					 		OR
							(NOT rental_contract.date_start > $availability_date_to AND NOT rental_contract.date_end IS NULL AND NOT rental_contract.date_end < $availability_date_from))
						)
					)
				)
				";
				$special_query=true;
				break;
			case "both":
				break;
		}
		
		// Furnished, partly furnished, not furnished, not specified
		if(isset($filters['furnished_status']) & $filters['furnished_status'] < 4){
			// Not specified
			if($filters['furnished_status'] == 0)
				$filter_clauses[] = "rental_composite.furnish_type_id IS NULL";
			else 
				$filter_clauses[] = "rental_composite.furnish_type_id=".$filters['furnished_status'];
		}

		if(isset($filters['not_in_contract'])){
			$filter_clauses[] = "(rental_contract_composite.contract_id != ".$filters['not_in_contract']." OR rental_contract_composite.contract_id IS NULL)";
		}
		
		if(isset($filters['location_code'])){
			$filter_clauses[] = "rental_unit.location_code = '". $filters['location_code'] . "'";
		}
		
		if(isset($filters['contract_id']))
		{
			$filter_clauses[] = "contract_id = {$this->marshal($filters['contract_id'],'int')}";
		}
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "rental_composite.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}*/
		
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "controller_control_group.id = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}

		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		$tables = "controller_control_group";
		$joins = "	{$this->left_join} fm_building_part ON (building_part_id = CAST(fm_building_part.id AS INT))";
		$joins .= "	{$this->left_join} controller_procedure ON (procedure_id = controller_procedure.id)";
		$joins .= "	{$this->left_join} controller_control_area ON (control_area_id = controller_control_area.id)";
		//$joins .= "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
		//$joins .= "	{$this->left_join} rental_contract ON (rental_contract.id = rental_contract_composite.contract_id)";
		
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(controller_control_group.id)) AS count';
		}
		else
		{
			$cols .= "controller_control_group.id, group_name, procedure_id, control_area_id, building_part_id, fm_building_part.descr AS building_part_descr, controller_procedure.title as procedure_title, controller_control_area.title as control_area_name ";
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';

	    //var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");    
	    
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	function populate(int $control_group_id, &$control_group)
	{
		if($control_group == null) {
			$control_group = new controller_control_group((int) $control_group_id);

			$control_group->set_group_name($this->unmarshal($this->db->f('group_name'), 'string'));
			$control_group->set_procedure_id($this->unmarshal($this->db->f('procedure_id'), 'int'));
			$control_group->set_procedure_name($this->unmarshal($this->db->f('procedure_title'), 'string'));
			$control_group->set_control_area_id($this->unmarshal($this->db->f('control_area_id'), 'int'));
			$control_group->set_control_area_name($this->unmarshal($this->db->f('control_area_name'), 'string'));
			$control_group->set_building_part_id($this->unmarshal($this->db->f('building_part_id'), 'int'));
			$control_group->set_building_part_descr($this->unmarshal($this->db->f('building_part_descr'), 'string'));
		}
		//var_dump($control_group);
		return $control_group;
	}
	
}
