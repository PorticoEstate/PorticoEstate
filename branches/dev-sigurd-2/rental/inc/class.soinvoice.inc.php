<?php
phpgw::import_class('rental.socommon');

class rental_soinvoice extends rental_socommon
{
	public function __construct()
	{
		parent::__construct('rental_invoice',
			array
			(
				'id'				=> array('type' => 'int'),
				'contract_id'		=> array('type' => 'int'),
				'billing_id'		=> array('type' => 'int'),
				'party_id'			=> array('type' => 'int'),
				'timestamp_created'	=> array('type' => 'int'),
				'timestamp_start'	=> array('type' => 'int'),
				'timestamp_end'		=> array('type' => 'int')
			));
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
			$this->marshal($invoice->get_timestamp_end(), 'int')
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
			$query = "SELECT id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end FROM {$this->table_name} wHERE contract_id = {$contract_id} ORDER BY timestamp_end DESC";
			if($this->db->query($query))
			{
				while($this->db->next_record()){
					$invoices[] = new rental_invoice($this->db->f('id', true), $this->db->f('billing_id', true), $contract_id, $this->db->f('timestamp_created', true), $this->db->f('timestamp_start', true), $this->db->f('timestamp_end', true));
				}
			}
		}
		return $invoices;
	}
	
}
?>