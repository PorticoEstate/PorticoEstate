<?php
phpgw::import_class('rental.socommon');

class rental_soinvoice extends rental_socommon
{

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
	
	public function add(rental_invoice &$invoice)
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
	
	public function update(rental_invoice &$invoice)
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
	
	/**
	 * Returns all invoices of a specified contract id. The contracts are
	 * ordered so that the invoices with the last end timestamps are first in
	 * the array returned.
	 * 
	 * @param $contract_id int with id of conctract.
	 * @return array of rental_invoice objects, empty array if no invoices
	 * found, never null.
	 */
	public function get_invoices_for_contract(int $contract_id)
	{
		$invoices = array();
		$contract_id = (int)$contract_id;
		if($contract_id > 0) // Id ok
		{
			$query = "SELECT id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum FROM {$this->table_name} wHERE contract_id = {$contract_id} ORDER BY timestamp_end DESC";
			if($this->db->query($query))
			{
				while($this->db->next_record()){
					$invoices[] = new rental_invoice($this->db->f('id', true), $this->db->f('billing_id', true), $contract_id, $this->db->f('timestamp_created', true), $this->db->f('timestamp_start', true), $this->db->f('timestamp_end', true), $this->db->f('total_sum', true));
				}
			}
		}
		return $invoices;
	}
	
		/**
		 * 
		 * TODO: ROY SOLBERG WILL FIX
		 * 
		 * 
		 * Helper method to return the end date of the last invoice. The timestamp
		 * parameter is optional, but when used the date returned will be the
		 * end date of the last invoice before or at that time.
		 *  
		 * @param $timestamp int with UNIX timestamp.
		 * @return int with UNIX timestamp with the end date of the invoice, or
		 * null if no such invoice was found.
		 */
		public function get_last_invoice_timestamp(int $timestamp = null)
		{
			$invoices = $this->get_invoices(); // Should be ordered so that the last invoice is first
			if($invoices != null && count($invoices) > 0) // Found invoices
			{
				if($timestamp == null) // No timestamp specified
				{
					// We can just use the first invoice
					$keys = array_keys($invoices);
					return $invoices[$keys[0]]->get_timestamp_end();
				}
				foreach ($invoices as $invoice) // Runs through all invoices
				{
					if($invoice->get_timestamp_end() <= $timestamp)
					{
						return $invoice->get_timestamp_end();
					}
				}
			}
			return null; // No matching invoices found
		}
	
}
?>