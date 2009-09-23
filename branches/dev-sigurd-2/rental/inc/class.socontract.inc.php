<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');

class rental_socontract extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_contract',
		array
		(
					'id'	=> array('type' => 'int'),
					'date_start' => array('type' => 'int'),
					'date_end' => array('type' => 'int'),
					'billing_start' => array('type' => 'int'),
					'title'	=> array('type' => 'string'),
					'composite_name' => array('type' => 'string'),
					'first_name' => array('type' => 'string'),
					'last_name' => array('type' => 'string'),
					'company_name' => array('type' => 'string'),
					'comment' => array('type' => 'string'),
					'old_contract_id' => array('type' => 'string'),
					'edited_on' => array('type' => 'date'),
					'location_id' => array('type' => 'int'),
					'executive_officer' => array('type' => 'int'),
					'last_updated' => array('type' => 'int'),
					'last_edited_by_current_user' => array('type' => 'int')
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
					$like_clauses[] = "contract.id = $query";
					$like_clauses[] = "contract.old_contract_id = $query";
					break;
				case "party_name":
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					break;
				
				case "composite":
					$like_clauses[] = "composite.name $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "contract.id = $query";
					$like_clauses[] = "contract.old_contract_id = $query";
					$like_clauses[] = "contract.comment = $this->like $like_pattern";
					$like_clauses[] = "party.first_name $this->like $like_pattern";
					$like_clauses[] = "party.last_name $this->like $like_pattern";
					$like_clauses[] = "party.company_name $this->like $like_pattern";
					$like_clauses[] = "composite.name $this->like $like_pattern";
					break;
			}
			
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
			
			
		}
		
		$filter_clauses = array();
		
		if(isset($filters['party_id'])){
			$party_id  =   $filters['party_id'];
			$filter_clauses[] = "party.id = $party_id";
		}
		
		if(isset($filters['executive_officer'])){
			$account_id  =   $filters['executive_officer'];
			$filter_clauses[] = "contract.executive_officer = $account_id";
		}
		
		if(isset($filters['last_edited_by'])){
			$account_id  =   $filters['last_edited_by'];
			$filter_clauses[] = "last_edited.account_id = $account_id";
		}
					
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $filters['contract_type'];
			$filter_clauses[] = "contract.location_id IN ($type)";
		}
		
		if(isset($filters['composite_id'])){
			
		}
		
		/* 
		 * Contract status is defined by the dates in each contract compared to the target date (default today):
		 * - contracts under planning: 
		 * the start date is larger (in the future) than the target date, or start date is undefined
		 * - active contracts: 
		 * the start date is smaller (in the past) than the target date, and the end date is undefined (running) or 
		 * larger (fixed) than the target date
		 * - under dismissal: 
		 * the start date is smaller than the target date, 
		 * the end date is larger than the target date, and 
		 * the end date substracted the contract type notification period is smaller than the target date
		 * - ended:
		 * the end date is smaller than the target date
		 */
		if(isset($filters['contract_status']) && $filters['contract_status'] != 'all'){	
			if(isset($filters['status_date_hidden']) && $filters['status_date_hidden'] != "")
			{
				$ts_query = strtotime($filters['status_date_hidden']); // target timestamp specified by user
			} 
			else
			{
				$ts_query = strtotime(date('Y-m-d')); // timestamp for query (today)
			}
			switch($filters['contract_status']){
				case 'under_planning':
					$filter_clauses[] = "contract.date_start > {$ts_query} OR contract.date_start IS NULL";
					break;
				case 'active':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND ( contract.date_end >= {$ts_query} OR contract.date_end IS NULL)";
					break;
				case 'under_dismissal':
					$filter_clauses[] = "contract.date_start <= {$ts_query} AND contract.date_end >= {$ts_query} AND (contract.date_end - (type.notify_before * (24 * 60 * 60)))  <= {$ts_query}";
					break;
				case 'ended':
					$filter_clauses[] = "contract.date_end < {$ts_query}'" ;
					break;
			}
		}
			
		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
		
		return join(' AND ', $clauses);
	}
	

	/**
	 * Get a key/value array of contract type titles keyed by their id
	 * 
	 * @return array
	 */
	function get_fields_of_responsibility(){
		$sql = "SELECT location_id,title FROM rental_contract_responsibility";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$location_id = $this->db->f('location_id', true);
			$results[$location_id] = $this->db->f('title', true);
		}
		return $results;
	}
	
	/**
	 * Get a key/value array of titles of billing term types keyed by their id
	 * 
	 * @return array
	 */
	function get_billing_terms()
	{
		$sql = "SELECT id, title FROM rental_billing_term";
		$this->db->query($sql, __LINE__, __FILE__);
		$results = array();
		while($this->db->next_record()){
			$results[$this->db->f('id', true)] = $this->db->f('title', true);
		}
		
		return $results;
	}
	
	/**
	 * Get single contract
	 * 
	 * @param	$id	id of the contract to return
	 * @return a rental_contract
	 */
	function get_single($id)
	{
		$id = (int)$id;
		$sql_payer_id = " {$this->left_join} (SELECT contract_id, party_id FROM rental_contract_party WHERE is_payer = true) rcp ON (rental_contract.id = rcp.contract_id)";
		
		$sql = "SELECT rental_contract.id AS contract_id, date_start, date_end, billing_start, rental_contract.location_id, rental_contract.comment, term_id, rental_billing_term.title as term_id_title, security_type, security_amount, executive_officer, old_contract_id, rental_contract_responsibility.title, party_id
				FROM " . $this->table_name . $sql_payer_id . " 
				{$this->left_join} rental_contract_responsibility ON (rental_contract_responsibility.location_id = rental_contract.location_id) 
				{$this->left_join} rental_billing_term ON (rental_contract.term_id = rental_billing_term.id) 
				WHERE {$this->table_name}.id={$id}";

		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
	
		$contract = new rental_contract();
	
		$this->db->next_record();

		$contract->set_id($this->unmarshal($this->db->f('contract_id', true), 'int'));

		$date_start =  $this->unmarshal($this->db->f('date_start', true), 'date');
		$date_end = $this->unmarshal($this->db->f('date_end', true), 'date');
	
		$date = new rental_contract_date($date_start, $date_end);
		$contract->set_contract_date($date);
		
		$contract->set_billing_start_date($this->unmarshal($this->db->f('billing_start', true), 'int'));
		$contract->set_location_id($this->unmarshal($this->db->f('location_id', true), 'int'));
		$contract->set_contract_type_title($this->unmarshal($this->db->f('title', true), 'string'));
		$contract->set_term_id($this->unmarshal($this->db->f('term_id', true), 'int'));
		$contract->set_term_id_title($this->unmarshal($this->db->f('term_id_title', true), 'string'));
		$contract->set_security_type($this->unmarshal($this->db->f('security_type', true), 'int'));
		$contract->set_security_amount($this->unmarshal($this->db->f('security_amount', true), 'string'));
		$contract->set_old_contract_id($this->unmarshal($this->db->f('old_contract_id', true), 'string'));
		$contract->set_payer_id($this->unmarshal($this->db->f('party_id', true), 'int'));
		$contract->set_executive_officer_id($this->unmarshal($this->db->f('executive_officer', true), 'int'));
		$contract->set_comment($this->unmarshal($this->db->f('comment', true), 'string'));

			
		return $contract;
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
	function get_contract_array($start = 0, $limit = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{ 
		$distinct = "DISTINCT contract.id, ";
		$columns_for_list = 'contract.date_start, contract.location_id, contract.date_end, contract.old_contract_id, contract.executive_officer, type.title, type.notify_before, composite.name as composite_name, party.first_name, party.last_name, party.company_name, last_edited.edited_on, contract.last_updated';
		$tables = "rental_contract contract";
		$join_contract_type = 	' LEFT JOIN rental_contract_responsibility type ON (type.location_id = contract.location_id)';
		$join_parties = 'LEFT JOIN rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON (c_t.party_id = party.id)';
		$join_composites = 		' LEFT JOIN rental_contract_composite c_c ON (contract.id = c_c.contract_id) LEFT JOIN rental_composite composite ON c_c.composite_id = composite.id';
		$join_last_edited = ' LEFT JOIN rental_contract_last_edited last_edited ON (contract.id = last_edited.contract_id)';
		$joins = $join_contract_type.$join_parties.$join_composites.$join_last_edited;
		$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		// Calculate total number of records
		/*$this->db->query("SELECT COUNT(distinct rental_contract.id) AS count FROM $tables $joins WHERE $condition", __LINE__, __FILE__);
		$this->db->next_record();
		$total_records = (int)$this->db->f('count');*/
		
		if($order != '') // ORDER should be used
		{
			// We get a 'ERROR: SELECT DISTINCT ON expressions must match initial ORDER BY expressions' if we don't wrap the ORDER query.
			$this->db->limit_query("SELECT * FROM (SELECT $distinct $columns_for_list FROM $tables $joins WHERE $condition) AS result $order", $start, __LINE__, __FILE__, $limit);
		}
		else
		{
			$this->db->limit_query("SELECT $distinct $columns_for_list FROM $tables $joins WHERE $condition", $start, __LINE__, __FILE__, $limit);
		}
		return $this->get_contracts_from_result();
	}
	
	
	protected function get_contracts_from_result(){
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
			$new_contract = true;
			$party_name = $row['first_name']." ".$row['last_name'];
			if($row['company_name'] != ''){
				if(trim($party_name) != ''){
					$party_name.= " (".$row['company_name'].")";
				} else {
					$party_name = $row['company_name'];
				}
			}
			
			foreach($contracts as $c) {
				if($row[id] == $c->get_id()){
					$new_contract = false;
					if($row['composite_name'] != ''){
						$c->set_composite_name($row['composite_name']);
					}
					if($row['company_name'] != '' || $row['first_name'] != '' || $row['last_name'] != ''){
						$c->set_party_name($party_name);
					}
					break;
				}		
			}
			if($new_contract) {
                $contract = new rental_contract($row['id']);
                $contract->set_contract_date(new rental_contract_date($row['date_start'],$row['date_end']));
                $contract->set_billing_start_date($row['billing_start']);
                $contract->set_party_name($party_name);
                $contract->set_composite_name($row['composite_name']);
                $contract->set_old_contract_id($row['old_contract_id']);
                $contract->set_contract_type_title($row['title']);
                $contract->set_comment($row['comment']);
                $contract->set_last_edited_by_current_user($row['edited_on']);
                $contract->set_location_id($row['location_id']);
                $contract->set_last_updated($row['last_updated']);
                $contracts[] = $contract;
			}
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
			if(isset($date)){
				$current_date = $date;
			}
			else
			{
				$current_date = strtotime('now');
			}
			switch($contract_date)
			{
				case 'all':
					/* no-op */
					break;
				case 'not_started':
					$condition .= " AND rental_contract.date_start > $current_date";  
					break;
				case 'ended':
					$condition .= " AND rental_contract.date_end < $current_date";  
					break;
				case 'active':
				default:
					$condition .= " AND (rental_contract.date_start <= $current_date AND (rental_contract.date_end >= $current_date OR rental_contract.date_end IS NULL))";  
					break;
			}
			
			$order = '';
			
			if($sort != null) // We should sort results
			{
				$order = 'ORDER BY '.$sort.' '.($dir == 'desc' ? 'desc' : 'asc');
			}
			$sql = "SELECT id FROM $tables $joins WHERE $condition";
			//var_dump($sql);
			$this->db->query($sql, __LINE__, __FILE__);
			$contracts = $this->get_contracts_from_result();
			//var_dump($contracts);
			return $contracts;
		}
		return $results;	
	}
	
	/**
	 * Returns contracts that should be billed for a given period.
	 * 
	 * @param $contract_type_location_id int with location id of contract.
	 * @param $billing_term_id int with billing term id of contract.
	 * @param $year int with year of billing.
	 * @param $month int 1-12 with month of billing.
	 * @return return array of contract objects
	 */
	public function get_contracts_for_billing($contract_type_location_id, $billing_term_id, $year, $month)
	{
		$sql = "SELECT months FROM rental_billing_term WHERE id = {$billing_term_id}";
		$result = $this->db->query($sql);
		if(!$result)
		{
			return;
		}
		if(!$this->db->next_record())
		{
			return;
		}
		$months = $this->unmarshal($this->db->f('months', true), 'int');
		$timestamp_end = strtotime("{$year}-{$month}-01"); // The first day in the month to bill for
		$timestamp_start = strtotime("-{$$months} months", $timestamp_end); // The first day of the period to bill for
		$timestamp_end = strtotime('+1 month', $timestamp_end); // The first day in the month after the one to bill for
		
		$timestamp_start = strtotime("{$year}-{$month}-01");
		$sql = "SELECT contract.id, contract.date_start, contract.date_end, contract.term_id, contract.location_id, contract.billing_start, party.first_name, party.last_name, party.company_name, composite.name as composite_name 
			FROM rental_contract AS contract
			LEFT JOIN rental_contract_party 	con_par 	ON (contract.id = con_par.contract_id) 
			LEFT JOIN rental_party 				party 		ON (party.id = con_par.party_id)
			LEFT JOIN rental_contract_composite	con_con		ON (con_con.contract_id = contract.id)
			LEFT JOIN rental_composite			composite	ON (composite.id = con_con.composite_id)
			WHERE contract.location_id = {$contract_type_location_id} AND contract.term_id = {$billing_term_id}
			AND date_start < $timestamp_end
			AND (date_end IS NULL OR date_end >= {$timestamp_start})
			AND billing_start <= {$timestamp_end}
			ORDER BY contract.billing_start DESC, contract.date_start DESC, contract.date_end DESC
			";
		$this->db->query($sql);
		return $this->get_contracts_from_result();
	}
	
	/**
	 * This methods retrieves all relevant information for contracts edited by current user
	 * @return unknown_type
	 */
	public function get_last_edited_by(){
		$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
		$sql = "
			SELECT edited.edited_on, contract.id, contract.location_id, contract.date_start, contract.date_end, party.first_name, party.last_name, party.company_name, composite.name as composite_name 
			FROM rental_contract_last_edited edited 
			LEFT JOIN rental_contract 			contract	ON (contract.id = edited.contract_id)
			LEFT JOIN rental_contract_party 	con_par 	ON (con_par.contract_id = edited.contract_id) 
			LEFT JOIN rental_party 				party 		ON (party.id = con_par.party_id)
			LEFT JOIN rental_contract_composite	con_con		ON (con_con.contract_id = edited.contract_id)
			LEFT JOIN rental_composite			composite	ON (composite.id = con_con.composite_id)
			WHERE edited.account_id = $account_id";
		//var_dump($sql);
		$this->db->query($sql);						
		return $this->get_contracts_from_result();
	}
	
	/**
	 * This method retrieves contracts belonging to the same area of responsibility as the current user.
	 * The contracts are sorted ascending on the date for the last update.
	 * 
	 * @return array of contract objects
	 */
	public function get_last_edited(){
		$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
		$sql = "
			SELECT contract.last_updated, contract.id, contract.location_id, contract.date_start, contract.date_end, party.first_name, party.last_name, party.company_name, composite.name as composite_name 
			FROM rental_contract contract
			LEFT JOIN rental_contract_party 	con_par 	ON (con_par.contract_id = edited.contract_id) 
			LEFT JOIN rental_party 				party 		ON (party.id = con_par.party_id)
			LEFT JOIN rental_contract_composite	con_con		ON (con_con.contract_id = edited.contract_id)
			LEFT JOIN rental_composite			composite	ON (composite.id = con_con.composite_id)
			WHERE contract.location_id 
			IN (SELECT location_id )
		";
		$this->db->query($sql);
		return $this->get_contracts_form_results();
	}
	
	/**
	 * Get the composites belonging to a certain contract
	 * 
	 * @return A list of rental_composite objects
	 * @param string $contract_id
	 */
	public function get_composites_for_contract($contract_id)
	{
		$sql = "SELECT rental_composite.id FROM rental_composite
			LEFT JOIN rental_contract_composite ON (rental_composite.id = rental_contract_composite.composite_id)
			LEFT JOIN rental_contract ON (rental_contract_composite.contract_id = rental_contract.id)
			WHERE rental_contract.id = $contract_id";
		$this->db->query($sql);						
		$composites = array();
		$composite_so = rental_composite::get_so();
		while($this->db->next_record()) { 
			$composite_id = $this->unmarshal($this->db->f('id', true), 'int');
			$composites[] = $composite_so->get_single($composite_id);
		 }
		return $composites;
	}
	
	/**
	 * Get the composites belonging to a certain contract
	 * 
	 * @return A list of rental_composite objects
	 * @param string $contract_id
	 */
	public function get_available_composites_for_contract($contract_id)
	{
		$sql = "SELECT rental_composite.id FROM rental_composite
			LEFT JOIN rental_contract_composite ON (rental_composite.id = rental_contract_composite.composite_id)
			LEFT JOIN rental_contract ON (rental_contract_composite.contract_id = rental_contract.id)
			WHERE rental_contract.id != $contract_id";
		$this->db->query($sql);						
		$composites = array();
		$composite_so = rental_composite::get_so();
		while($this->db->next_record()) { 
			$composite_id = $this->unmarshal($this->db->f('id', true), 'int');
			$composites[] = $composite_so->get_single($composite_id);
		 }
		return $composites;
	}
	
	
	/**
	 * Get the parties involved in this contract
	 * 
	 * @param $contract_id the contract id
	 * @return A list of rental_party objects
	 */
	public function get_parties_for_contract($contract_id)
	{
		$sql = "SELECT party_id FROM rental_contract_party WHERE contract_id = $contract_id";
		$this->db->query($sql);
		$parties = array();
		$parties_so = rental_party::get_so();
		while($this->db->next_record()) {
			$party_id = $this->unmarshal($this->db->f('party_id', true), 'int');
			$parties[] = $parties_so->get_single($party_id);
		}
		return $parties;
	}
	
	/**
	 * Get the price items involved in this contract
	 * 
	 * @param $contract_id the contract id
	 * @return A list of rental_price_item objects
	 */
	public function get_price_items_for_contract($contract_id)
	{
		$sql = "SELECT * FROM rental_contract_price_item WHERE contract_id = $contract_id";
		$this->db->query($sql);
		$price_items = array();
		$price_item_so = rental_contract_price_item::get_so();
		while($this->db->next_record()) {
			$id = $this->unmarshal($this->db->f('id', true), 'int');
			
			$price_items[] = $price_item_so->get_single_contract_price_item($id);
		}
		return $price_items;
	}
	
	/**
	 * Get the parties not involved in this contract
	 * 
	 * @param $contract_id the contract id
	 * @return  A list of rental_party objects
	 */
	public function get_available_parties_for_contract($contract_id)
	{
		$sql = "SELECT DISTINCT party_id FROM rental_contract_party WHERE contract_id != $contract_id";
		$this->db->query($sql);
		$parties = array();
		$parties_so = rental_party::get_so();
		while($this->db->next_record()) { 
			$party_id = $this->unmarshal($this->db->f('party_id', true), 'int'); 
			$parties[] = $parties_so->get_single($party_id);
		}
		return $parties;
	}
	
	/**
	 * Returns the range of year there are contracts. That is, the array
	 * returned contains reversed chronologically all the years from the earliest start
	 * year of the contracts to next year. 
	 * 
	 * @return array of string values, never null.
	 */
	public function get_year_range()
	{
		$year_range = array();
		$sql = "SELECT date_start FROM rental_contract ORDER BY date_start ASC";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$first_year = (int)date('Y'); // First year in the array returned - we first set it to default this year
		if($this->db->next_record()){
			$date = $this->unmarshal($this->db->f('date_start', true), 'int');
			if($date != null && $date != '')
			{
				$first_contract_year = (int)date('Y', $date);
				if($first_contract_year < $first_year) // First contract year is before this year
				{
					$first_year = $first_contract_year;
				}
			}
		}
		$next_year = (int)date('Y', strtotime('+1 year'));
		for($year = $next_year; $year >= $first_year; $year--) // Runs through all years from next year to the first year we want
		{
			$year_range[] = $year;
		}
		
		return $year_range;
	}
	
	/**
	 * Update the database values for an existing contract object.
	 * 
	 * @param $contract the contract to be updated
	 * @return result receipt from the db operation
	 */
	function update(rental_contract $contract)
	{
		$id = intval($contract->get_id());
		
		$values = array();
		
		if($contract->get_term_id()) {
			$values[] = "term_id = " . $this->marshal($contract->get_term_id(), 'int');
		}
		
		if($contract->get_billing_start_date()) {
			$values[] = "billing_start = " . $this->marshal($contract->get_billing_start_date(), 'int');
		}
		
		if ($contract->get_contract_date()) {
			$values[] = "date_start = " . $this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = "date_end = " . $this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}
		
		$values[] = "security_type = '" . $this->marshal($contract->get_security_type(), 'int') . "'";
		$values[] = "security_amount = " . $this->marshal($contract->get_security_amount(), 'string');
		$values[] = "executive_officer = ". $this->marshal($contract->get_executive_officer_id(), 'int');
		$values[] = "comment = ". $this->marshal($contract->get_comment(), 'string');
		$values[] = "last_updated = ".strtotime('now');

		$result = $this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		if(isset($result))
		{
			$this->last_edited_by($id);
		}
			
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		
		return $receipt;
	}
	
	/**
	 * This method marks the combination contract/user account with the current timestamp. It updates the record if the user has updated
	 * this contract before; inserts a new record if the user has never updated this contract. 
	 * 
	 * @param $contract_id
	 * @return true if the contract was marker, false otherwise
	 */
	private function last_edited_by($contract_id){
		$account_id = $GLOBALS['phpgw_info']['user']['account_id']; // current user
		$ts_now = strtotime('now');
		
		$sql_has_edited_before = "SELECT account_id FROM rental_contract_last_edited WHERE contract_id = $contract_id AND account_id = $account_id";
		$result = $this->db->query($sql_has_edited_before);
		
		if(isset($result))
		{
			if($this->db->next_record())
			{
				$sql = "UPDATE rental_contract_last_edited SET edited_on=$ts_now WHERE contract_id = $contract_id AND account_id = $account_id";
				var_dump($sql);
			} 
			else
			{
				$sql = "INSERT INTO rental_contract_last_edited VALUES ($contract_id,$account_id,$ts_now)";
			}
			$result = $this->db->query($sql);
			if(isset($result))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * This method markw the given contract with the current timestamp
	 * 
	 * @param $contract_id
	 * @return true if the contract was marked, false otherwise
	 */
	private function last_updated($contract_id){
		$ts_now = strtotime('now');
		$sql = "UPDATE rental_contract SET last_updated=$ts_now";
		$result = $this->db->query($sql);
		if(isset($result))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Add a new contract to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $contract the contract to be added
	 * @return array result receipt from the db operation
	 */
	function add(rental_contract &$contract)
	{
		// These are the columns we know we have or that are nullable
		$cols = array('location_id', 'term_id');
		
		// Start making a db-formatted list of values of the columns we have to have
		$values = array(
			$this->marshal($contract->get_location_id(), 'int'),
			$this->marshal($contract->get_term_id(), 'int')
		);
		
		// Check values that can be null before trying to add them to the db-pretty list
		if ($contract->get_billing_start_date()) {
			$cols[] = 'billing_start';
			$values[] = $this->marshal($contract->get_billing_start_date(), 'int');
		}
		
		if ($contract->get_contract_date()) {
			$cols[] = 'date_start';
			$cols[] = 'date_end';
			$values[] = $this->marshal($contract->get_contract_date()->get_start_date(), 'int');
			$values[] = $this->marshal($contract->get_contract_date()->get_end_date(), 'int');
		}
		
		if($contract->get_executive_officer_id()) {
			$cols[] = 'executive_officer';
			$values[] = $this->marshal($contract->get_executive_officer_id(), 'int');
		}
		
		$cols[] = 'created';
		$cols[] = 'created_by';
		$values[] = strtotime('now');
		$values[] = $GLOBALS['phpgw_info']['user']['account_id'];
		
		
		// Insert the new contract
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		
		if($result)
		{
			$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
			$contract->set_id($receipt['id']);
			//var_dump();
			return $receipt;
		}
		else
		{
			var_dump($q);
			var_dump($result);
		}
		
	}
	
	/**
	 * This method adds a party to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to add
	 * @return true if successful, false otherwise
	 */
	function add_party($contract_id, $party_id)
	{
		$q = "INSERT INTO rental_contract_party (contract_id, party_id) VALUES ($contract_id, $party_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method removes a party from a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to remove
	 * @return true if successful, false otherwise
	 */
	function remove_party($contract_id, $party_id)
	{
		$q = "DELETE FROM rental_contract_party WHERE contract_id = $contract_id AND party_id = $party_id";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method adds a composite to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $composite_id	the composite to add
	 * @return true if successful, false otherwise
	 */
	function add_composite($contract_id, $composite_id)
	{
		$q = "INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES ($contract_id, $composite_id)";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method removes a composite from a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the composite to remove
	 * @return true if successful, false otherwise
	 */
	function remove_composite($contract_id, $composite_id)
	{
		$q = "DELETE FROM rental_contract_composite WHERE contract_id = $contract_id AND composite_id = $composite_id";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method adds a price item to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $price_item	the price item to add
	 * @return true if successful, false otherwise
	 */
	function add_price_item($contract_id, $price_item)
	{
		$values = array(
			$price_item->get_id(),
			$contract_id,
			"'" . $price_item->get_title() . "'",
			"'" . $price_item->get_agresso_id() . "'",
			$price_item->is_area() ? 'true' : 'false',
			$price_item->get_price()
		);
		$q = "INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, agresso_id, is_area, price) VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method removes a price item to a contract. Updates last edited hisory.
	 * 
	 * @param $contract_id	the given contract
	 * @param $price_item	the prce item to remove
	 * @return true if successful, false otherwise
	 */
	function remove_price_item($contract_id, $price_item)
	{
		$q = "DELETE FROM rental_contract_price_item WHERE id = {$price_item->get_id()}";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
	/**
	 * This method sets a payer on a contract
	 * 
	 * @param $contract_id	the given contract
	 * @param $party_id	the party to be the payer
	 * @return true if successful, false otherwise
	 */
	function set_payer($contract_id, $party_id)
	{
		$pid =$this->marshal($party_id, 'int');
		$cid = $this->marshal($contract_id, 'int'); 
		$q = "UPDATE rental_contract_party SET is_payer = true WHERE party_id = ".$pid." AND contract_id = ".$cid;
		$result = $this->db->query($q);
		$q1 = "UPDATE rental_contract_party SET is_payer = false WHERE party_id != ".$pid." AND contract_id = ".$cid;
		$result1 = $this->db->query($q1);
		if($result && $result1)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
}
?>
