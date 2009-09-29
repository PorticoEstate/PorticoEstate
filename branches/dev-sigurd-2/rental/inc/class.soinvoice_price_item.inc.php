<?php
phpgw::import_class('rental.socommon');

class rental_soinvoice_price_item extends rental_socommon
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
			self::$so = CreateObject('rental.soinvoice_price_item');
		}
		return self::$so;
	}
	
	protected function get_id_field_name()
	{
		return 'id';
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		throw new Exception("Not implemented");
	}
	
	protected function populate(int $object_id, &$object)
	{
		throw new Exception("Not implemented");
	}
	
	public function add(&$invoice_price_item)
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
		$query ="INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (" . join(',', $values) . ")";
		$receipt = null;
		if($this->db->query($query))
		{
			$receipt = array();
			$receipt['id'] = $this->db->get_last_insert_id('rental_invoice_price_item', 'id');
			$invoice_price_item->set_id($receipt['id']);
		}
		return $receipt;
	}
	
	protected function update($object)
	{
		throw new Exception("Not implemented");
	}
	
}
?>