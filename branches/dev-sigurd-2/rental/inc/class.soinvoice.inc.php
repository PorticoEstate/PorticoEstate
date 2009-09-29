<?php
phpgw::import_class('rental.socommon');

class rental_soinvoice extends rental_socommon
{
	protected static $so;
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.soinvoice');
		}
		return self::$so;
	}	
	
	protected function get_id_field_name()
	{
		return 'id';
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
			$clauses = array('1=1');
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "{$this->marshal($this->get_id_field_name(),'field')} = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		if(isset($filters['contract_id']))
		{
			$filter_clauses[] = "contract_id = {$this->marshal($filters['contract_id'],'int')}";
		}
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		$condition =  join(' AND ', $clauses);

		$tables = "rental_invoice";
		$joins = "	{$this->left_join} rental_unit ON (rental_composite.id = rental_unit.composite_id)";
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
		}
		else
		{
			$cols = 'id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum';
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $invoice_id, &$invoice)
	{
		if($invoice == null)
		{
			$invoice = new rental_invoice($this->db->f('id', true), $this->db->f('billing_id', true), $contract_id, $this->db->f('timestamp_created', true), $this->db->f('timestamp_start', true), $this->db->f('timestamp_end', true), $this->db->f('total_sum', true));
		}
		return $invoice;
	}
	
	public function add(&$invoice)
	{
		$values = array
		(
			$this->marshal($invoice->get_contract_id(), 'int'),
			$this->marshal($invoice->get_billing_id(), 'int'),
			$this->marshal($invoice->get_party_id(), 'int'),
			$this->marshal($invoice->get_timestamp_created(), 'int'),
			$this->marshal($invoice->get_timestamp_start(), 'int'),
			$this->marshal($invoice->get_timestamp_end(), 'int'),
			$this->marshal($invoice->get_total_sum(), 'float')
		);
		$query ="INSERT INTO ".$this->table_name." (" . join(',', array_keys(array_slice($this->fields, 1))) . ") VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
			$invoice->set_id($receipt['id']);
		}
		return $receipt;
	}
	
	public function update($invoice)
	{
		$values = array(
			'contract_id = '		. $this->marshal($invoice->get_contract_id(), 'int'),
			'billing_id = '			. $this->marshal($invoice->get_billing_id(), 'int'),
			'party_id = '			. $this->marshal($invoice->get_party_id(), 'int'),
			'timestamp_created = '	. $this->marshal($invoice->get_timestamp_created(), 'int'),
			'timestamp_start = '	. $this->marshal($invoice->get_timestamp_start(), 'int'),
			'timestamp_end = '		. $this->marshal($invoice->get_timestamp_end(), 'int'),
			'total_sum = '			. $this->marshal($invoice->get_total_sum(), 'float')
		);
		$result = $this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=" . $invoice->get_id(), __LINE__,__FILE__);
	}
	
}
?>