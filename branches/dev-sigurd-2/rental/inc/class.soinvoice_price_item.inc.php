<?php
phpgw::import_class('rental.socommon');

class rental_soinvoice_price_item extends rental_socommon
{
	public function __construct()
	{
		parent::__construct('rental_invoice_price_item',
			array
			(
				'id'			=> array('type' => 'int'),
				'invoice_id'	=> array('type' => 'int'),
				'title'			=> array('type' => 'string'),
				'agresso_id'	=> array('type' => 'string'),
				'is_area'		=> array('type' => 'bool'),
				'price'			=> array('type' => 'float'),
				'area'			=> array('type' => 'float'),
				'count'			=> array('type' => 'int'),
				'total_price'	=> array('type' => 'float'),
				'date_start'	=> array('type' => 'date'),
				'date_end'		=> array('type' => 'date')
			));
	}
	
	public function add(rental_invoice_price_item &$invoice_price_item)
	{
		$values = array
		(
			$this->marshal($invoice_price_item->get_invoice_id(), 'int'),
			$this->marshal($invoice_price_item->get_title(), 'string'),
			$this->marshal($invoice_price_item->get_agresso_id(), 'string'),
			$invoice_price_item->is_area() ? 'true' : 'false',
			$this->marshal($invoice_price_item->get_price(), 'float'),
			$this->marshal($invoice_price_item->get_area(), 'float'),
			$this->marshal($invoice_price_item->get_count(), 'int'),
			$this->marshal($invoice_price_item->get_total_price(), 'float'),
			$this->marshal(date('Y-m-d', $invoice_price_item->get_timestamp_start()), 'date'),
			$this->marshal(date('Y-m-d', $invoice_price_item->get_timestamp_end()), 'date')
		);
		$query ="INSERT INTO ".$this->table_name." (" . join(',', array_keys(array_slice($this->fields, 1))) . ") VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
			$invoice_price_item->set_id($receipt['id']);
		}
		return $receipt;
	}
	
}
?>