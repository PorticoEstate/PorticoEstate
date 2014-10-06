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
		$clauses = array('1=1');
		if(isset($filters[$this->get_id_field_name()]))
		{
			$filter_clauses[] = "{$this->marshal($this->get_id_field_name(),'field')} = {$this->marshal($filters[$this->get_id_field_name()],'int')}";
		}
		if(isset($filters['invoice_id']))
		{
			$filter_clauses[] = "invoice_id = {$this->marshal($filters['invoice_id'],'int')}";
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

		$tables = "rental_invoice_price_item";
		$joins = "	{$this->left_join} rental_invoice ON (rental_invoice.id = rental_invoice_price_item.invoice_id)";
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(rental_invoice_price_item.id)) AS count';
		}
		else
		{
			$cols = 'rental_invoice_price_item.id, invoice_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end';
		}
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": ($return_count ? '' : 'ORDER BY rental_invoice_price_item.id ASC');
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $price_item_id, &$price_item)
	{
		if($price_item == null)
		{
			$price_item = new rental_invoice_price_item(0, $this->db->f('id', true), $this->db->f('invoice_id', true), $this->db->f('title', true), $this->db->f('agresso_id', true), $this->db->f('is_area', true), $this->db->f('price', true), $this->db->f('area', true), $this->db->f('count', true), strtotime($this->db->f('date_start', true)), strtotime($this->db->f('date_end', true)));
			$price_item->set_total_price($this->db->f('total_price', true));
		}
		return $price_item;
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