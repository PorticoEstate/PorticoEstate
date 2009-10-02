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
		if(isset($filters['billing_id']))
		{
			$filter_clauses[] = "billing_id = {$this->marshal($filters['billing_id'],'int')}";
		}
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		$condition =  join(' AND ', $clauses);

		$tables = "rental_invoice";
		$joins = "	{$this->left_join} rental_contract_composite ON (rental_contract_composite.contract_id = rental_invoice.contract_id)";
		$joins .= "	{$this->left_join} rental_composite ON (rental_contract_composite.composite_id = rental_composite.id)";
		$joins .= "	{$this->left_join} rental_contract_party ON (rental_contract_party.contract_id = rental_invoice.contract_id)";
		$joins .= "	{$this->left_join} rental_party ON (rental_contract_party.party_id = rental_party.id)";
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(rental_invoice.id)) AS count';
		}
		else
		{
			$cols = 'rental_invoice.id, rental_invoice.contract_id, billing_id, rental_invoice.party_id, timestamp_created, timestamp_start, timestamp_end, total_sum, total_area, header, account_in, account_out, rental_composite.name AS composite_name, rental_party.first_name AS party_first_name, rental_party.last_name AS party_last_name, rental_party.company_name AS party_company_name';
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": '';
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $invoice_id, &$invoice)
	{
		if($invoice == null)
		{
			$invoice = new rental_invoice($this->db->f('id', true), $this->db->f('billing_id', true), $this->db->f('contract_id', true), $this->db->f('timestamp_created', true), $this->db->f('timestamp_start', true), $this->db->f('timestamp_end', true), $this->db->f('total_sum', true), $this->db->f('total_area', true), $this->db->f('header', true), $this->db->f('account_in', true), $this->db->f('account_out', true));
			$invoice->set_party_id($this->unmarshal($this->db->f('party_id'),'int'));
		}
		$invoice->add_composite_name($this->unmarshal($this->db->f('composite_name'),'string'));
		$party_company_name = $this->unmarshal($this->db->f('party_company_name'),'string');
		$party_first_name = $this->unmarshal($this->db->f('party_first_name'),'string');
		$name = $this->unmarshal($this->db->f('party_last_name'),'string');
		if($party_first_name != '') // Firstname is set
		{
			if($name != '') // There's a lastname
			{
				$name .= ', '; // Append comma
			}
			$name .= $party_first_name; // Append firstname
		}
		if($party_company_name != '') // There's a company name
		{
			if($name != '') // We've already got a name
			{
				$name .= " ({$party_company_name})"; // Append company name in parenthesis
			}
			else // No name
			{
				$name = $party_company_name; // Set name to company
			}
		}
		$invoice->add_party_name($name);
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
			$this->marshal($invoice->get_total_sum(), 'float'),
			$this->marshal($invoice->get_total_area(), 'float'),
			$this->marshal($invoice->get_header(), 'string'),
			$this->marshal($invoice->get_account_in(), 'string'),
			$this->marshal($invoice->get_account_out(), 'string')
		);
		$query ="INSERT INTO rental_invoice(contract_id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum, total_area, header) VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id('rental_invoice', 'id');
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
			'total_sum = '			. $this->marshal($invoice->get_total_sum(), 'float'),
			'total_area = '			. $this->marshal($invoice->get_total_area(), 'float'),
			'header = '				. $this->marshal($invoice->get_header(), 'string'),
			'account_in = '			. $this->marshal($invoice->get_account_in(), 'string'),
			'account_out = '		. $this->marshal($invoice->get_account_out(), 'string')
		);
		$result = $this->db->query('UPDATE rental_invoice SET ' . join(',', $values) . " WHERE id=" . $invoice->get_id(), __LINE__,__FILE__);
	}
	
}
?>