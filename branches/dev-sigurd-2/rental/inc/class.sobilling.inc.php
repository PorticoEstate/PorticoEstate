<?php
phpgw::import_class('rental.socommon');

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
			$cols = 'id, total_sum, success, timestamp_start, timestamp_stop, location_id, term_id, year, month';
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} {$dir}": 'ORDER BY timestamp_stop DESC';
		}
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $billing_id, &$billing)
	{
		if($billing == null)
		{
			$billing = new rental_billing($this->db->f('id', true), $this->db->f('location_id', true), $this->db->f('term_id', true), $this->db->f('year', true), $this->db->f('month', true));
			$billing->set_success($this->db->f('success', true));
			$billing->set_total_sum($this->db->f('total_sum', true));
			$billing->set_timestamp_start($this->db->f('timestamp_start', true));
			$billing->set_timestamp_stop($this->db->f('timestamp_stop', true));
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
			$billing->get_success() ? 'true' : 'false',
			$this->marshal($billing->get_timestamp_start(), 'int'),
			$this->marshal($billing->get_timestamp_stop(), 'int'),
			$this->marshal($billing->get_location_id(), 'int'),
			$this->marshal($billing->get_billing_term(), 'int'),
			$this->marshal($billing->get_year(), 'int'),
			$this->marshal($billing->get_month(), 'int'),
		);
		$query ="INSERT INTO rental_billing(total_sum, success, timestamp_start, timestamp_stop, location_id, term_id, year, month) VALUES (" . join(',', $values) . ")";
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
			"success = '" . ($billing->get_success() ? 'true' : 'false') . "'",
			'timestamp_start = ' . $this->marshal($billing->get_timestamp_start(), 'int'),
			'timestamp_stop = ' . $this->marshal($billing->get_timestamp_stop(), 'int'),
			'location_id = ' . $this->marshal($billing->get_location_id(), 'int'),
			'term_id = ' . $this->marshal($billing->get_billing_term(), 'int'),
			'year = ' . $this->marshal($billing->get_year(), 'int'),
			'month = ' . $this->marshal($billing->get_month(), 'int')
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
		
	public function create_billing(int $decimals, int $contract_type, int $billing_term, int $year, int $month, array $contracts_to_bill, array $contract_billing_start_date)
	{
		// We start a transaction before running the billing
		$this->db->transaction_begin();
		$billing = new rental_billing(-1, $contract_type, $billing_term, $year, $month); // The billing job itself
		$billing->set_timestamp_start(time()); // Start of run
		$this->store($billing); // Store job as it is
		$billing_end_timestamp = strtotime('-1 day', strtotime(($month == 12 ? ($year + 1) : $year) . '-' . ($month == 12 ? '01' : ($month + 1)) . '-01')); // Last day of billing period is the last day of the month we're billing
		$counter = 0;
		$total_sum = 0;
		foreach($contracts_to_bill as $contract_id) // Runs through all the contracts that should be billed in this run
		{
			$invoice = rental_invoice::create_invoice($decimals, $billing->get_id(), $contract_id, $contract_billing_start_date[$counter++], $billing_end_timestamp); // Creates an invoice of the contract
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
	
}
?>