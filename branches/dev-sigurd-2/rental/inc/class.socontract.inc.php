<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'property', 'inc/model/');
include_class('rental', 'building', 'inc/model/');
include_class('rental', 'floor', 'inc/model/');
include_class('rental', 'section', 'inc/model/');
include_class('rental', 'room', 'inc/model/');
include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');

class rental_socontract extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_contract',
		array
		(
					'id'	=> array('type' => 'int'),
					'date_start' => array('type' => 'date'),
					'date_end' => array('type' => 'date'),
					'title'	=> array('type' => 'string')
		));
	}
	
	protected function get_conditions($query, $filters,$search_option)
	{	
		$clauses = array('1=1');
		if($query)
		{
			
			$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
			$like_clauses = array();
			switch($search_option){
				case "id":
					$like_clauses[] = "rental_contract.id LIKE '$query'";
					break;
				case "tenant_name":
					$like_clauses[] = "rental_tenant.name $this->like $like_pattern";
					break;
				case "composite":
					$like_clauses[] = "rental_composite.name $this->like $like_pattern";
					$like_clauses[] = "fm_location1.adresse1 $this->like $like_pattern";
					$like_clauses[] = "rental_composite.address_1 $this->like $like_pattern";
					$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
					$like_pattern = str_replace('/','',$like_pattern);
					$like_clauses[] = "substring(fm_gab_location.gab_id from 5 for 9) $this->like $like_pattern";
					break;
				case "gab":
					$like_pattern = str_replace('/','',$like_pattern);
					$like_clauses[] = "fm_gab_location.gab_id $this->like $like_pattern";
					break;
				
			}
			
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
			
			
		}
		
		$filter_clauses = array();
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $filters['contract_type'];
			$filter_clauses[] = "rental_contract.type_id = $type";
		}
		
		if(isset($filters['contract_status']) && $filters['contract_status'] != 'all'){
			
			$current_date = date('Y-m-d');
			$timestamp = mktime(0,0,0,date("m")+3,date("d"),date("y"));
			
			$start_date = $current_date;
			$end_date = $current_date;
			$dismissal_date = date('Y-m-d',$timestamp);
			
			if(isset($filters['from_date_hidden']) && $filters['from_date_hidden'] != "")
			{
				$start_date = $filters['from_date_hidden'];
			}
			
			if(isset($filters['to_date_hidden']) && $filters['to_date_hidden'] != "")
			{
				$end_date = $filters['to_date_hidden'];
				$dismissal_timestamp = strtotime(date("Y-m-d", strtotime($end_date)) . " +1 month");;
				$dismissal_date = date('Y-m-d',$dismissal_timestamp);
			}
//			var_dump($start_date);
//			var_dump($end_date);
//			var_dump($dismissal_date);
			
			switch($filters['contract_status']){
				case 'under_planning':
					$filter_clauses[] = "rental_contract.date_start > '{$start_date}'";
					break;
				case 'running':
					$filter_clauses[] = "rental_contract.date_start < '{$start_date}' AND rental_contract.date_start = null";
					break;
				case 'under_dismissal':
					
					$filter_clauses[] = "rental_contract.date_start < '{$start_date}' AND rental_contract.date_end > '{$dismissal_date}' AND rental_contract.date_end < '{$end_date}'";
					break;
				case 'fixed':
					$filter_clauses[] = "rental_contract.date_start < '{$start_date}' AND rental_contract.date_end > '{$end_date}'";
					break;
				case 'ended':
					$filter_clauses[] = "rental_contract.date_end < '{$end_date}'" ;
					break;
			}
		}
		
		//var_dump($filter_clauses);
		
			
		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
		
		return join(' AND ', $clauses);
	}

	function get_contract_types(){
		$sql = "SELECT id,title FROM rental_contract_type";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$row = array();
			$row['id'] = $this->db->f('id', true);
			$row['title'] = $this->db->f('title', true);
			$results[] = $row;
		}
		return $results;
	}
	
	/**
	 * Get a list of contract objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_cotract objects
	 */
	function get_contract_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$condition = $this->get_conditions($query, $filters,$search_option);
		
		$tables = "rental_contract";
		//$joins = 'LEFT JOIN rental_unit ON (rental_composite.id = rental_unit.composite_id) LEFT JOIN fm_location1 ON (rental_unit.loc1 = fm_location1.loc1) LEFT JOIN fm_gab_location ON (rental_unit.loc1 = fm_gab_location.loc1) LEFT JOIN fm_locations ON (rental_unit.location_id = fm_locations.id)';
		$joins = 'LEFT JOIN rental_contract_type ON (rental_contract_type.id = rental_contract.type_id)';
		$cols = 'rental_contract.id, rental_contract.date_start, rental_contract.date_end, rental_contract_type.title';
		
		// Calculate total number of records
		/*$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');*/
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $cols FROM $tables $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $cols FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		}
		
		
		
		$results = array();
		
		while ($this->db->next_record())
		{
			$row = array();
			foreach($this->fields as $field => $fparams)
			{
      			$row[$field] = $this->unmarshal($this->db->f($field, true), $params['type']);
			}
			$results[] = $row;
		}
		
		
		$contracts = array();
		
		// Go through each returned row and create contract objects
		foreach ($results as $row) {
			$contract = new rental_contract($row['id']);
			$contract->set_contract_date(new rental_contract_date($row['date_start'],$row['date_end']));
			$contract->set_contract_type_title($row['title']);
			$contracts[] = $contract;
		}
		return $contracts;
	}
	
	/**
	 * Returns all contracts for a specified composite.
	 * 
	 * @param $params array with parameters for the query
	 * @return array with 'total_records' and 'results'.
	 */
	public function get_contracts($id, $sort = null, $dir = null, $start = 0, $limit = 1000, $contract_status = null, $date = null)
	{
		// Params
		$id = (int)$id;
				
		// Default return data:
		$total_records = 0;
		$results = array();
		
		$contracts = array();
		
		if($id > 0) // Valid id
		{
			$tables = 'rental_contract';
			$joins = 'JOIN rental_contract_composite ON (rental_contract.id = rental_contract_composite.contract_id)';
			$condition = 'rental_contract_composite.composite_id = '.$id;
			$current_date = date('Y-m-d');
			switch($contract_date)
			{
				case 'all':
					/* no-op */
					break;
				case 'not_started':
					$condition .= " AND rental_contract.date_start > '{$current_date}'";  
					break;
				case 'ended':
					$condition .= " AND rental_contract.date_end < '{$current_date}'";  
					break;
				case 'active':
				default:
					$condition .= " AND (rental_contract.date_start <= '{$current_date}' AND rental_contract.date_end >= '{$current_date}')";  
					break;
			}
			
			$order = '';
			
			if($sort != null) // We should sort results
			{
				$order = 'ORDER BY '.$sort.' '.($dir == 'desc' ? 'desc' : 'asc');
			}
			
			$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
			$this->db->next_record();
			$total_records = (int)$this->db->f('count');
			
			$sql = "SELECT rental_contract.id, date_start, date_end FROM {$tables} {$joins} WHERE {$condition} {$order}";
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			while($this->db->next_record())
			{
				$contract = new rental_contract($this->unmarshal($this->db->f('id', true), 'string'));
				
				$date_start =  date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_start', true), 'date')));
	     	$date_end = date($GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'], strtotime($this->unmarshal($this->db->f('date_end', true), 'date')));
	     	
				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));
				
				// TODO: include tenant here whenever that db table is ready
				//$contract->set_tenant($tenant)
				
				$contracts[] = $contract;
			}
		}
		
		return $contracts;
		
		return array(
			'total_records' => $total_records,
			'results'		=> $results
		);
	}
	
	/**
	 * Update the database values for an existing contract object.
	 * 
	 * @param $contract the contract to be updated
	 * @return result receipt from the db operation
	 */
	function update($contract)
	{
		$id = intval($contract->get_id());
		$cols = array();
		$values = array();
		
		$values = array(
			'name = \'' . $contract->get_name() . '\'',
			'description = \'' . $contract->get_description() . '\'',
			'has_custom_address = ' . ($contract->has_custom_address() ? "true" : "false"),
			'address_1 = \'' . $contract->get_address_1() . '\'',
			'address_2 = \'' . $contract->get_address_2() . '\'',
			'house_number = \'' . $contract->get_house_number() . '\'',
			'postcode = \'' . $contract->get_postcode() . '\'',
			'place = \'' . $contract->get_place() . '\''
		);
				
		$cols = join(',', $cols);
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		$current_units = $this->get_included_rental_units($contract->get_id());
		
		// Add rental units from the composite object that aren't in the database
		foreach ($contract->get_included_rental_units() as $unit) {
			$has_unit = false;
			foreach ($current_units as $current_unit) {
				if ($unit->get_location_id() == $current_unit->get_location_id()) {
					// This unit from the composite was found in the db
					$has_unit = true;
				}
			}
			if (!$has_unit) {
				$this->add_unit($contract->get_id(), $unit->get_location_id(), $unit->get_location_code());
			}
		}
		
		$current_units = $this->get_included_rental_units($contract->get_id());
		
		// Remove rental units that are in the database but have been removed from the composite object
		foreach ($current_units as $current_unit) {
			$unit_is_removed = true;
			foreach ($contract->get_included_rental_units() as $unit) {
				if ($current_unit->get_location_id() == $unit->get_location_id()) {
					// This unit from the db was not found on the current composite
					$unit_is_removed = false;
				}
			}
			
			if ($unit_is_removed) {
				$this->remove_unit($contract->get_id(), $unit->get_location_id());
			}
		}
		
		return $receipt;
	}
	
	/**
	 * Add a new contract to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $contract the composite to be added
	 * @return result receipt from the db operation
	 */
	function add(&$contract)
	{
		// Build a db-friendly array of the composite object
		$values = array(
			'name = \'' . $contract->get_name() . '\'',
			'description = \'' . $contract->get_description() . '\'',
			'has_custom_address = ' . ($contract->has_custom_address() ? "true" : "false"),
			'address_1 = \'' . $contract->get_address_1() . '\'',
			'address_2 = \'' . $contract->get_address_2() . '\'',
			'house_number = \'' . $contract->get_house_number() . '\'',
			'postcode = \'' . $contract->get_postcode() . '\'',
			'place = \'' . $contract->get_place() . '\''
		);
		
		$q ="INSERT INTO ".$this->table_name." (name) VALUES ('$values')";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$contract->set_id($receipt['id']);
		
		// Add rental units from the composite object
		foreach ($contract->get_included_rental_units() as $unit) {
			$this->add_unit($contract->get_id(), $unit->get_location_id(), $unit->get_location_code());
		}
		
		return $receipt;
	}
}
?>
