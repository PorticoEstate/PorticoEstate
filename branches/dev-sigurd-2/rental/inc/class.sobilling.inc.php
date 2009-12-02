<?php
phpgw::import_class('rental.socommon');
include_class('rental', 'agresso_gl07', 'inc/model/');
include_class('rental', 'agresso_lg04', 'inc/model/');

class rental_sobilling extends rental_socommon
{
	protected static $so;
	protected $billing_terms; // Used for caching the billing terms
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.sobilling');
		}
		return self::$so;
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		$clauses = array('1=1');
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "{$this->marshal($this->get_id_field_name(),'field')} = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		$filter_clauses[] = "deleted = false";
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		$condition =  join(' AND ', $clauses);

		$tables = "rental_billing";
		$joins = "";
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
		}
		else
		{
			$cols = 'id, total_sum, success, created_by, timestamp_start, timestamp_stop, timestamp_commit, location_id, term_id, year, month, export_format, export_data';
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} {$dir}": 'ORDER BY timestamp_stop DESC';
		}
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $billing_id, &$billing)
	{
		if($billing == null)
		{
			$billing = new rental_billing($this->db->f('id', true), $this->db->f('location_id', true), $this->db->f('term_id', true), $this->db->f('year', true), $this->db->f('month', true), $this->db->f('created_by', true));
			$billing->set_success($this->db->f('success', true));
			$billing->set_total_sum($this->db->f('total_sum', true));
			$billing->set_timestamp_start($this->db->f('timestamp_start', true));
			$billing->set_timestamp_stop($this->db->f('timestamp_stop', true));
			$billing->set_timestamp_commit($this->db->f('timestamp_commit', true));
			$billing->set_export_format($this->db->f('export_format', true));
			if($this->db->f('export_data', true) != null)
			{
				$billing->set_generated_export(true);
			}
		}
		return $billing;
	}
	
	protected function get_id_field_name()
	{
		return 'id';
	}
	
	public function add(&$billing)
	{
		$values = array
		(
			$this->marshal($billing->get_total_sum(), 'float'),
			$billing->is_success() ? 'true' : 'false',
			$this->marshal($billing->get_created_by(), 'int'),
			$this->marshal($billing->get_timestamp_start(), 'int'),
			$this->marshal($billing->get_timestamp_stop(), 'int'),
			$this->marshal($billing->get_timestamp_commit(), 'int'),
			$this->marshal($billing->get_location_id(), 'int'),
			$this->marshal($billing->get_billing_term(), 'int'),
			$this->marshal($billing->get_year(), 'int'),
			$this->marshal($billing->get_month(), 'int'),
			$billing->is_deleted() ? 'true' : 'false',
			$this->marshal($billing->get_export_format(), 'string'),
		);
		$query ="INSERT INTO rental_billing(total_sum, success, created_by, timestamp_start, timestamp_stop, timestamp_commit, location_id, term_id, year, month, deleted, export_format) VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id('rental_billing', 'id');
			$billing->set_id($receipt['id']);
		}
		return $receipt;
	}
	
	public function update($billing)
	{
		$values = array(
			'total_sum = ' . $this->marshal($billing->get_total_sum(), 'float'),
			"success = '" . ($billing->is_success() ? 'true' : 'false') . "'",
			'timestamp_start = ' . $this->marshal($billing->get_timestamp_start(), 'int'),
			'timestamp_stop = ' . $this->marshal($billing->get_timestamp_stop(), 'int'),
			'timestamp_commit = ' . $this->marshal($billing->get_timestamp_commit(), 'int'),
			'location_id = ' . $this->marshal($billing->get_location_id(), 'int'),
			'term_id = ' . $this->marshal($billing->get_billing_term(), 'int'),
			'year = ' . $this->marshal($billing->get_year(), 'int'),
			'month = ' . $this->marshal($billing->get_month(), 'int'),
			"deleted = '" . ($billing->is_deleted() ? 'true' : 'false') . "'",
			'export_format = ' . $this->marshal($billing->get_export_format(), 'string'),
		);
		$result = $this->db->query("UPDATE rental_billing SET " . join(',', $values) . " WHERE id={$billing->get_id()}", __LINE__,__FILE__);
	}
	
	/**
	 * Get a key/value array of titles of billing term types keyed by their id
	 * 
	 * @return array
	 */
	function get_billing_terms()
	{
		if($this->billing_terms == null)
		{
			$sql = "SELECT id, title FROM rental_billing_term";
			$this->db->query($sql, __LINE__, __FILE__);
			$results = array();
			while($this->db->next_record()){
				$results[$this->db->f('id', true)] = $this->db->f('title', true);
			}
			$this->billing_terms = $results;
		}
		
		return $this->billing_terms;
	}
	
	public function get_missing_billing_info(int $billing_term, int $year, int $mont, array $contracts_to_bill, array $contracts_overriding_billing_start, string $export_format)
	{
		$exportable = null;
		$missing_billing_info = array();
		switch($export_format)
		{
			case 'agresso_gl07':
				$exportable = $export_format;
				break;
			case 'agresso_lg04':
				$exportable = $export_format;
				break;
			default:
				$missing_billing_info[] = 'Unknown export format.';
				break;
		}
		foreach($contracts_to_bill as $contract_id) // Runs through all the contracts that should be billed in this run
		{
			$contract = rental_socontract::get_instance()->get_single($contract_id);
			$info = null;
			switch($export_format)
			{
				case 'agresso_gl07':
					$info = rental_agresso_gl07::get_missing_billing_info($contract);
					break;
				case 'agresso_lg04':
					$info = rental_agresso_lg04::get_missing_billing_info($contract);
					break;
			}
			if($info != null && count($info) > 0)
			{
				$missing_billing_info[$contract_id] = $info;
			}
		}
		return $missing_billing_info;
	}
		
	public function create_billing(int $decimals, int $contract_type, int $billing_term, int $year, int $month, int $created_by, array $contracts_to_bill, array $contracts_overriding_billing_start, string $export_format)
	{
		// We start a transaction before running the billing
		$this->db->transaction_begin();
		$billing = new rental_billing(-1, $contract_type, $billing_term, $year, $month, $created_by); // The billing job itself
		$billing->set_timestamp_start(time()); // Start of run
		$billing->set_export_format($export_format);
		$this->store($billing); // Store job as it is
		$billing_end_timestamp = strtotime('-1 day', strtotime(($month == 12 ? ($year + 1) : $year) . '-' . ($month == 12 ? '01' : ($month + 1)) . '-01')); // Last day of billing period is the last day of the month we're billing
		$counter = 0;
		$total_sum = 0;
		
		// Get the number of months in selected term for contract
		$months = rental_socontract::get_instance()->get_months_in_term($billing_term);
		
		// The billing should start from the first date of the periode (term) we're billing for
		$first_day_of_selected_month = strtotime($year . '-' . $month . '-01');
		$bill_from_timestamp = strtotime('-'.($months-1).' month', $first_day_of_selected_month); 
		
		foreach($contracts_to_bill as $contract_id) // Runs through all the contracts that should be billed in this run
		{
			$invoice = rental_invoice::create_invoice($decimals, $billing->get_id(), $contract_id, in_array($contracts_overriding_billing_start,$contract_id) ? true : false,$bill_from_timestamp, $billing_end_timestamp); // Creates an invoice of the contract
			if($invoice != null)
			{
				$total_sum += $invoice->get_total_sum();
			}
		}
		$billing->set_total_sum(round($total_sum, $decimals));
		$billing->set_timestamp_stop(time()); //  End of run
		$billing->set_success(true); // Billing job is a success
		$this->store($billing); // Store job now that we're done
		// End of transaction!
		if ($this->db->transaction_commit()) { 
			return $billing;
		}
		throw new UnexpectedValueException('Transaction failed.');
	}
	
	/**
	 * Helper method to check if a period has been billed before.
	 * 
	 * @param $contract_type
	 * @param $billing_term
	 * @param $year
	 * @param $month
	 * @return boolean true if the period has been billed before, false if not.
	 */
	public function has_been_billed($contract_type, $billing_term, $year, $month)
	{
		$sql = "SELECT COUNT(id) AS count FROM rental_billing WHERE location_id = {$this->marshal($contract_type,'int')} AND term_id = {$this->marshal($billing_term,'int')} AND year = {$this->marshal($year,'int')} AND month = {$this->marshal($month,'int')} AND deleted = false";
		$result = $this->db->query($sql, __LINE__, __FILE__);
		if($result && $this->db->next_record())
		{
			return ($this->unmarshal($this->db->f('count', true), 'int') > 0);
		} 
		return false;
	}
	
	/**
	 * Generates export data and stores in database.
	 * 
	 * @param $billing_job
	 */
	public function generate_export(&$billing_job)
	{
		$exportable = null;
		switch($billing_job->get_export_format())
		{
			case 'agresso_gl07':
				$exportable = new rental_agresso_gl07($billing_job);
				break;
			case 'agresso_lg04':
				$exportable = new rental_agresso_lg04($billing_job);
				break;
		}
		if($exportable != null)
		{
			$sql = "UPDATE rental_billing SET export_data = {$this->marshal($exportable->get_contents(),'string')} WHERE id = {$this->marshal($billing_job->get_id(),'int')}";
			$result = $this->db->query($sql, __LINE__, __FILE__);
			return true;
		}
		return false;
	}
	
	public function get_export_data(int $billing_job_id)
	{
		$sql = "SELECT export_data FROM rental_billing WHERE id = {$this->marshal($billing_job_id,'int')}";
		$result = $this->db->query($sql, __LINE__, __FILE__);
		if($result && $this->db->next_record())
		{
			return $this->unmarshal($this->db->f('export_data', true), 'string');
		}
		return '';
	}
	
}
?>