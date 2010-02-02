<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'adjustment', 'inc/model/');

class rental_soadjustment extends rental_socommon
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
			self::$so = CreateObject('rental.soadjustment');
		}
		return self::$so;
	}

	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{		
		$clauses = array('1=1');
		
		$filter_clauses = array();
		
		if(isset($filters[$this->get_id_field_name()])){
			$id = $this->marshal($filters[$this->get_id_field_name()],'int');
			$filter_clauses[] = "{$this->get_id_field_name()} = {$id}";
		}
		
		if(isset($filters['manual_adjustment']))
		{
			$clauses[] = "is_manual";
		}
		else
		{
			$clauses[] = "NOT is_manual";
		}
		
		if(count($filter_clauses))
		{
			$clauses[] = join(' AND ', $filter_clauses);
		}

		$condition =  join(' AND ', $clauses);

		$tables = "rental_adjustment";
		$joins = "";
		$dir = $ascending ? 'ASC' : 'DESC';
		if($return_count) // We should only return a count
		{
			$cols = 'COUNT(DISTINCT(id)) AS count';
			$order = "";
		}
		else
		{
			$cols = 'id, price_item_id, responsibility_id, new_price, percent, adjustment_date';
			$order = $sort_field ? "ORDER BY {$this->marshal($sort_field, 'field')} $dir ": ' ORDER BY adjustment_date DESC';
		}
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	function populate(int $adjustment_id, &$adjustment)
	{ 
		if($adjustment == null ) // new object
		{
			$adjustment = new rental_adjustment($adjustment_id);
			$adjustment->set_price_item_id($this->unmarshal($this->db->f('price_item_id', true), 'int'));
			$adjustment->set_responsibility_id($this->unmarshal($this->db->f('responsibility_id', true), 'int'));
			$adjustment->set_new_price($this->unmarshal($this->db->f('new_price', true), 'float'));
			$adjustment->set_percent($this->unmarshal($this->db->f('percent', true), 'int'));
			$adjustment->set_adjustment_date($this->unmarshal($this->db->f('adjustment_date', true), 'int'));
			$adjustment->set_is_manual($this->unmarshal($this->db->f('is_manual'),'bool'));
		}
		
		return $adjustment;
	}
	
	public function get_id_field_name(){
		return 'id';
	}

	/**
	 * Update the database values for an existing composite object. Also updates associated rental units.
	 *
	 * @param $composite the composite to be updated
	 * @return result receipt from the db operation
	 */
	public function update($adjustment)
	{
		$id = intval($adjustment->get_id());

		$values = array(
			'price_item_id = ' . $adjustment->get_price_item_id() ,
			'responsibility_id = ' . $adjustment->get_responsibility_id(),
			'new_price= ' . $adjustment->get_new_price(),
            'percent = '.$adjustment->get_percent(),
            'adjustment_date = ' . $adjustment->get_adjustment_date(),
			'is_manual = ' . ($adjustment->is_manual() ? "true" : "false")
		);

		$result = $this->db->query('UPDATE rental_adjustment SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);

		return $result != null;
	}
	
	/**
	 * Add a new adjustment to the database.  Adds the new insert id to the object reference.
	 *
	 * @param $adjustment the adjustment to be added
	 * @return int with id of the adjustment
	 */
	public function add(&$adjustment)
	{
		// Build a db-friendly array of the adjustment object
		$cols = array('price_item_id', 'responsibility_id', 'new_price', 'percent', 'adjustment_date', 'is_manual');
		$values = array(
			$adjustment->get_price_item_id(),
			$adjustment->get_responsibility_id(),
			$adjustment->get_new_price(),
			$adjustment->get_percent(),
            $adjustment->get_adjustment_date(),
            ($adjustment->is_manual() ? "true" : "false")
		);

		$query ="INSERT INTO rental_adjustment (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($query);

		$adjustment_id = $this->db->get_last_insert_id('rental_adjustment', 'id');
		$adjustment->set_id($adjustment_id);
		return $adjustment_id;
	}
}
?>
