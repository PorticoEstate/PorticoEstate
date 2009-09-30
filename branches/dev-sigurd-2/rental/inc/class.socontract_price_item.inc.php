<?php
phpgw::import_class('rental.socommon');

class rental_socontract_price_item extends rental_socommon
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
			self::$so = CreateObject('rental.socontract_price_item');
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
		
		//Add columns to this array to include them in the query
		$columns = array();
		
		$dir = $ascending ? 'ASC' : 'DESC';
		$order = $sort_field ? "ORDER BY $sort_field $dir": '';
		
		$filter_clauses = array();
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "{$this->get_id_field_name()} = {$id}";
		}
		if(isset($filters['contract_id'])){
			$id = $this->marshal($filters['contract_id'],'int');
			$filter_clauses[] = "contract_id = {$id}";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}
		
		$condition =  join(' AND ', $clauses);
		
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
		}
		else
		{
			$cols = '*';
		}
		
		$tables = "rental_contract_price_item";
		$joins = '';
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $price_item_id, &$price_item)
	{
		if($price_item == null)
		{
			$price_item = new rental_contract_price_item($this->unmarshal($this->db->f('id'),'int'));
			$price_item->set_price_item_id($this->unmarshal($this->db->f('price_item_id'),'int'));
			$price_item->set_contract_id($this->unmarshal($this->db->f('contract_id'),'int'));
			$price_item->set_title($this->unmarshal($this->db->f('title'),'string'));
			$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id'),'string'));
			$price_item->set_is_area($this->unmarshal($this->db->f('is_area'),'bool'));
			$price_item->set_price($this->unmarshal($this->db->f('price'),'float'));
			$price_item->set_area($this->unmarshal($this->db->f('area'),'float'));
			$price_item->set_count($this->unmarshal($this->db->f('count'),'int'));
			$price_item->set_total_price($this->unmarshal($this->db->f('total_price'),'float'));
			$price_item->set_date_start($this->unmarshal($this->db->f('date_start'),'date'));
			$price_item->set_date_end($this->unmarshal($this->db->f('date_end'),'date'));
		}
		return $price_item;
	}
	
	protected function add(&$price_item)
	{
		
	}
	
	protected function update($price_item)
	{
		
	}
	
}
?>