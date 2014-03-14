<?php
phpgw::import_class('rental.socommon');
phpgw::import_class('rental.uicommon');

include_class('rental', 'billing_info', 'inc/model/');

class rental_sobilling_info extends rental_socommon
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
			self::$so = CreateObject('rental.sobilling_info');
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
		if(isset($filters['billing_id'])){
			$filter_clauses[] = "billing_id = {$this->marshal($filters['billing_id'], 'int')}";
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
		
		$tables = "rental_billing_info";
		$joins = '';
//		var_dump("SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}");
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $billing_info_id, &$billing_info)
	{
		if($billing_info == null)
		{
			$billing_info = new rental_billing_info($this->unmarshal($this->db->f('id'),'int'));
			$billing_info->set_billing_id($this->unmarshal($this->db->f('billing_id'),'int'));
			$billing_info->set_location_id($this->unmarshal($this->db->f('location_id'),'int'));
			$billing_info->set_term_id($this->unmarshal($this->db->f('term_id'),'int'));
			$billing_info->set_year($this->unmarshal($this->db->f('year'),'int'));
			$billing_info->set_month($this->unmarshal($this->db->f('month'),'int'));
			if($billing_info->get_term_id() == 2){ // yearly
				$billing_info->set_term_label(lang('annually'));
			}
			else if($billing_info->get_term_id() == 3){ // half year
				if($billing_info->get_month() == 6){
					$billing_info->set_term_label(lang('first_half'));
				}
				else{
					$billing_info->set_term_label(lang('second_half'));
				}
				
			}
			else if($billing_info->get_term_id() == 4){ // quarterly
				if($billing_info->get_month() == 3){
					$billing_info->set_term_label(lang('first_quarter'));
				}
				else if($billing_info->get_month() == 6){
					$billing_info->set_term_label(lang('second_quarter'));
				}
				else if($billing_info->get_month() == 9){
					$billing_info->set_term_label(lang('third_quarter'));
				}
				else{
					$billing_info->set_term_label(lang('fourth_quarter'));
				}
			}
		}
		return $billing_info;
	}
	
	/**
	 * Add a new contract_price_item to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $billing_info the billing_info to be added
	 * @return mixed receipt from the db operation
	 */
	protected function add(&$billing_info)
	{
		// Build a db-friendly array of the composite object
		$values = array(
			$billing_info->get_billing_id(),
			$billing_info->get_term_id(),
			$billing_info->get_location_id(),
			$billing_info->get_month(),
			$billing_info->get_year(),
			($billing_info->is_deleted() ? "true" : "false")
		);
		
		$cols = array('billing_id', 'term_id', 'location_id', 'month', 'year', 'deleted');
		
		$q ="INSERT INTO rental_billing_info (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";

		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id("rental_billing_info", 'id');
		
		$billing_info->set_id($receipt['id']);
		
		return $receipt;
	}
	
	/**
	 * Update the database values for an existing contract billing_info.
	 * 
	 * @param $billing_info the billing info to be updated
	 * @return result receipt from the db operation
	 */
	protected function update($billing_info)
	{
		$id = intval($billing_info->get_id());
		
		// Build a db-friendly array of the composite object
		$values = array(
			"billing_id = " . $this->marshal($billing_info->get_billing_id(), 'int'),
			"term_id = " . $this->marshal($billing_info->get_term_id(), 'int'),
			"location_id = " . $this->marshal($billing_info->get_location_id(), 'int'),
			"month = " . $this->marshal($billing_info->get_month(), 'int'),
			"year = " . $this->marshal($billing_info->get_year(), 'int'),
			"deleted = " . ($billing_info->is_deleted() ? "true" : "false")
		);

		$this->db->query('UPDATE rental_billing_info SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));
		return $receipt;
	}
}

?>
