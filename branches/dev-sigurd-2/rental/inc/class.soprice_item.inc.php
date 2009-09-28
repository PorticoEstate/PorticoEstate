<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');

class rental_soprice_item extends rental_socommon
{
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.sobilling');
		}
		return self::$so;
	}
		
	/**
	 * Get single price item
	 * 
	 * @param	$id	id of the price item to return
	 * @return a rental_price_item
	 */
	function get_single($id)
	{
		$id = (int)$id;
		
		$sql = "SELECT * FROM " . $this->table_name . " WHERE id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$price_item = new rental_price_item($this->get_field_value('id'));
		$price_item->set_title($this->get_field_value('title'));
		$price_item->set_agresso_id($this->get_field_value('agresso_id'));
		$price_item->set_is_area($this->get_field_value('is_area'));
		$price_item->set_price($this->get_field_value('price'));
		
		return $price_item;
	}
	
	function get_single_contract_price_item($id)
	{
		$id = (int)$id;
		
		$sql = "SELECT * FROM rental_contract_price_item WHERE id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$price_item = new rental_contract_price_item($this->get_field_value('id'));
		$price_item->set_price_item_id($this->get_field_value('price_item_id'));
		$price_item->set_contract_id($this->get_field_value('contract_id'));
		$price_item->set_title($this->get_field_value('title'));
		$price_item->set_agresso_id($this->get_field_value('agresso_id'));
		$price_item->set_is_area($this->get_field_value('is_area'));
		$price_item->set_price($this->get_field_value('price'));
		$price_item->set_area($this->get_field_value('area'));
		$price_item->set_count($this->get_field_value('count'));
		$price_item->set_total_price($this->get_field_value('total_price'));
		$price_item->set_date_start($this->get_field_value('date_start'));
		$price_item->set_date_end($this->get_field_value('date_end'));
		
		return $price_item;
	}
	
	/**
	 * Get a list of price_item objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of rental_composite objects
	 */
	function get_price_item_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		$condition = $this->get_conditions($query, $filters,$search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		$sql = "SELECT * FROM rental_price_item WHERE $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
		
		while ($this->db->next_record()) {
			$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
			$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
			$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
			$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
			
			$results[] = $price_item;
		}
		
		return $results;
	}
	
	protected function get_conditions($query, $filters,$search_option)
	{	
		$clauses = array('1=1');
		if($query)
		{
			
			$like_pattern = "'%" . $this->db->db_addslashes($query) . "%'";
			$like_clauses = array();
			switch($search_option){
				case "id":
					$like_clauses[] = "rental_price_item.id = $query";
					break;
				case "title":
					$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
					break;
				case "agresso_id":
					$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
					break;
				case "all":
					$like_clauses[] = "rental_price_item.title $this->like $like_pattern";
					$like_clauses[] = "rental_price_item.agresso_id $this->like $like_pattern";
					break;
			}
			
			
			if(count($like_clauses))
			{
				$clauses[] = '(' . join(' OR ', $like_clauses) . ')';
			}
			
			
		}
		
		$filter_clauses = array();
		switch($filters['is_area']){
			case "true":
				$filter_clauses[] = "rental_price_item.is_area = TRUE";
				break;
			case "false":
				$filter_clauses[] = "rental_price_item.is_area = FALSE";
				break;
			case "both":
				break;
		}
			
		if(count($filter_clauses))
			{
				$clauses[] = join(' AND ', $filter_clauses);
			}
		
		return join(' AND ', $clauses);
	}
	
	/**
	 * Add a new price_item to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $price_item the price_item to be added
	 * @return result receipt from the db operation
	 */
	function add(&$price_item)
	{
		$price = $price_item->get_price() ? $price_item->get_price() : 0;
		// Build a db-friendly array of the composite object
		$values = array(
			'\'' . $price_item->get_title() . '\'',
			'\'' . $price_item->get_agresso_id() . '\'',
			($price_item->is_area() ? "true" : "false"),
			$price
		);
		
		$cols = array('title', 'agresso_id', 'is_area', 'price');
		
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$price_item->set_id($receipt['id']);
		
		return $receipt;
	}
	
	/**
	 * Update the database values for an existing price item.
	 * 
	 * @param $price_item the price item to be updated
	 * @return result receipt from the db operation
	 */
	function update($price_item)
	{
		$id = intval($price_item->get_id());
		
		$values = array(
			'title = \'' . $price_item->get_title() . '\'',
			'agresso_id = \'' . $price_item->get_agresso_id() . '\'',
			'is_area = ' . ($price_item->is_area() ? "true" : "false"),
			'price = ' . $price_item->get_price()
		);
				
		$this->db->query('UPDATE ' . $this->table_name . ' SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));

		return $receipt;
	}
	
/**
	 * Update the database values for an existing contract price item.
	 * 
	 * @param $price_item the contract price item to be updated
	 * @return result receipt from the db operation
	 */
	function update_contract_price_item(rental_contract_price_item $price_item)
	{
		$id = intval($price_item->get_id());
		
		$values = array(
			'price_item_id = ' . $price_item->get_price_item_id(),
			'contract_id = ' . $price_item->get_contract_id(),
			'area = ' . $price_item->get_area(),
			'count = ' . $price_item->get_count(),
			'title = \'' . $price_item->get_title() . '\'',
			'agresso_id = \'' . $price_item->get_agresso_id() . '\'',
			'is_area = ' . ($price_item->is_area() ? "true" : "false"),
			'price = ' . $price_item->get_price()
		);
		
		if ($price_item->is_area()) {
			$values[] = 'total_price = ' . ($price_item->get_area() * $price_item->get_price());
		} else {
			$values[] = 'total_price = ' . ($price_item->get_count() * $price_item->get_price());
		}
		
		if ($price_item->get_date_start()) {
			$values[] = 'date_start = ' . $this->marshal(date('Y-m-d', $price_item->get_date_start()), 'date');
		}
		
		if ($price_item->get_date_end()) {
			$values[] = 'date_end = ' . $this->marshal(date('Y-m-d', $price_item->get_date_end()), 'date');
		}
				
		$this->db->query('UPDATE rental_contract_price_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
		$receipt['id'] = $id;
		$receipt['message'][] = array('msg'=>lang('Entity %1 has been updated', $entry['id']));

		return $receipt;
	}
	
/**
	 * This method removes a price item to a contract. Updates last edited hisory.
	 * 
	 * @param $contract_id	the given contract
	 * @param $price_item	the prce item to remove
	 * @return true if successful, false otherwise
	 */
	function remove_price_item($contract_id, $price_item)
	{
		$q = "DELETE FROM rental_contract_price_item WHERE id = {$price_item->get_id()}";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
	
/**
	 * This method adds a price item to a contract. Updates last edited history.
	 * 
	 * @param $contract_id	the given contract
	 * @param $price_item	the price item to add
	 * @return true if successful, false otherwise
	 */
	function add_price_item($contract_id, $price_item)
	{
		$values = array(
			$price_item->get_id(),
			$contract_id,
			"'" . $price_item->get_title() . "'",
			"'" . $price_item->get_agresso_id() . "'",
			$price_item->is_area() ? 'true' : 'false',
			$price_item->get_price()
		);
		$q = "INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, agresso_id, is_area, price) VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		if($result)
		{
			$this->last_updated($contract_id);
			$this->last_edited_by($contract_id);
			return true;
		}
		return false;
	}
}