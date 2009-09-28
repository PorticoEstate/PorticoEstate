<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'contract_date', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');
include_class('rental', 'composite', 'inc/model/');
include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract_price_item', 'inc/model/');

class rental_socontract extends rental_socommon
{

	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.socontract');
		}
		return self::$so;
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{	
		$clauses = array('1=1');
		
		//Add columns to this array to include them in the query
		$columns = array();
		
		$dir = $ascending ? 'ASC' : 'DESC';
		switch($sort_field)
		{
			case 'id':
			default:
				$sort_field = 'contract.id';
				break;
		}
		$order = $sort_field ? "ORDER BY $sort_field $dir": '';
		
		if($query)
		{
			$query = $this->marshal($query,'string');
			$like_pattern = "'%".$query."%'";
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
			$party_id  =   $this->marshal($filters['party_id'],'int');
			$filter_clauses[] = "party.id = $party_id";
		}
		
		if(isset($filters['executive_officer'])){
			$account_id  =   $this->marshal($filters['executive_officer'],'int');
			$filter_clauses[] = "contract.executive_officer = $account_id";
		}
		
		if(isset($filters['last_edited_by'])){
			$account_id  =  $this->marshal($filters['last_edited_by'],'int');
			$filter_clauses[] = "last_edited.account_id = $account_id";
		}
					
		if(isset($filters['contract_type']) && $filters['contract_type'] != 'all'){
			$type = $this->marshal($filters['contract_type'],'string');
			$filter_clauses[] = "contract.location_id IN ($type)";
		}
		
		if(isset($filters['id'])){
			$id = $this->marshal($filters['id'],'int');
			$filter_clauses[] = "contract.id = {$id}";
		}
		
		// All contracts for a given composite id
		if(isset($filters['composite_id']))
		{	
			$composite_id = $this->marshal($filters['composite_id'],'int');
			$filter_clauses[] = "composite.id = {$composite_id}";
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
				$ts_query = strtotime($this->marshal($filters['status_date_hidden'],'int')); // target timestamp specified by user
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
		
		if(isset($filters['contracts_for_billing']))
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
			
			$filter_clauses[] = "contract.location_id = {$contract_type_location_id}";
			$filter_clauses[] = "contract.term_id = {$billing_term_id}";
			$filter_clauses[] = "date_start < $timestamp_end";
			$filter_clauses[] = "(date_end IS NULL OR date_end >= {$timestamp_start})";
			$filter_clauses[] = "billing_start <= {$timestamp_end}";
			
			$specific_ordering = 'contract.billing_start DESC, contract.date_start DESC, contract.date_end DESC';
			$order = $order ? $order.' '.$specific_ordering : "ORDER BY {$specific_ordering}";
		}
			
		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
			
		$condition =  join(' AND ', $clauses);
		
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(rental_contract.id)) AS count';
		}
		else
		{
			$columns[] = 'contract.id AS contract_id';
			$columns[] = 'contract.date_start, contract.date_end, contract.old_contract_id, contract.executive_officer, contract.last_updated, contract.location_id';
			$columns[] = 'party.id AS party_id';
			$columns[] = 'party.first_name, party.last_name, party.company_name';		
			$columns[] = 'composite.id AS composite_id';
			$columns[] = 'composite.name AS composite_name';
			$columns[] = 'type.title, type.notify_before';
			$columns[] = 'last_edited.edited_on';	
			$cols = implode(',',$columns);
		}
		
		
		//TODO: add price item support
		
		$tables = "rental_contract contract";
		$join_contract_type = 	$this->left_join.' rental_contract_responsibility type ON (type.location_id = contract.location_id)';
		$join_parties = $this->left_join.' rental_contract_party c_t ON (contract.id = c_t.contract_id) LEFT JOIN rental_party party ON (c_t.party_id = party.id)';
		$join_composites = 		$this->left_join." rental_contract_composite c_c ON (contract.id = c_c.contract_id) {$this->left_join} rental_composite composite ON c_c.composite_id = composite.id";
		$join_last_edited = $this->left_join.' rental_contract_last_edited last_edited ON (contract.id = last_edited.contract_id)';
		$joins = $join_contract_type.' '.$join_parties.' '.$join_composites.' '.$join_last_edited;

		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	public function get_id_field_name(){
		return 'contract_id';
	}

	
	function populate(int $contract_id, &$contract)
	{ 
		if($contract == null ) // new contract
		{
			$contract_id = (int) $contract_id; 
			$contract = new rental_contract($contract_id);
			$contract->set_contract_date(new rental_contract_date
				(
					$this->unmarshal($this->db->f('date_start'),'int'),
					$this->unmarshal($this->db->f('date_end'),'int')
				)
			);
			
			$contract->set_billing_start_date($this->unmarshal($this->db->f('billing_start'),'int'));
			$contract->set_old_contract_id($this->unmarshal($this->db->f('old_contract_id'),'int'));
			$contract->set_contract_type_title($this->unmarshal($this->db->f('title'),'string'));
			$contract->set_comment($this->unmarshal($this->db->f('comment'),'string'));
			$contract->set_last_edited_by_current_user($this->unmarshal($this->db->f('edited_on'),'int'));
			$contract->set_location_id($this->unmarshal($this->db->f('location_id'),'int'));
			$contract->set_last_updated($this->unmarshal($this->db->f('last_updated'),'int'));
		}
		
		$party_id = $this->unmarshal($this->db->f('party_id', true), 'int');
		if($party_id)
		{
			$party = new rental_party($party_id);
			$party->set_first_name($this->unmarshal($this->db->f('first_name', true), 'string'));
			$party->set_last_name($this->unmarshal($this->db->f('last_name', true), 'string'));
			$party->set_company_name($this->unmarshal($this->db->f('company_name', true), 'string'));
			$contract->add_party($party);
		}
		
		$composite_id = $this->unmarshal($this->db->f('composite_id', true), 'int');
		if($composite_id)
		{
			$composite = new rental_composite($composite_id);
			$composite->set_name($this->unmarshal($this->db->f('composite_name', true), 'string'));
			$contract->add_composite($composite);
		}
		return $contract;
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
	 * Get the parties not involved in this contract
	 * 
	 * TODO: where does this go?
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
	 * 	 * TODO: where does this go?
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
	function update($contract)
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
	function add(&$contract)
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
