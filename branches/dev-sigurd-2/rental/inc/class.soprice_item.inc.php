<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'price_item', 'inc/model/');
include_class('rental', 'contract', 'inc/model/');

class rental_soprice_item extends rental_socommon
{
	protected static $so;
	
	/**
	 * Get a static reference to the storage object associated with this model object
	 * 
	 * @return rental_soprice_item the storage object
	 */
	public static function get_instance()
	{
		if (self::$so == null) {
			self::$so = CreateObject('rental.soprice_item');
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
		
		$sql = "SELECT * FROM rental_price_item WHERE id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
		$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
		$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
		$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
		$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
		
		return $price_item;
	}
	
	/**
	 * Get the first price item matching the given title
	 * 
	 * @param string $title
	 * @return rental_price_item
	 */
	function get_single_with_title($title)
	{
		$title = (string)$title;
		
		$sql = "SELECT * FROM rental_price_item WHERE title LIKE '" . $title . "'";
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		
		if ($this->db->next_record()) {
			$price_item = new rental_price_item($this->unmarshal($this->db->f('id', true), 'int'));
			$price_item->set_title($this->unmarshal($this->db->f('title', true), 'string'));
			$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id', true), 'string'));
			$price_item->set_is_area($this->unmarshal($this->db->f('is_area', true), 'bool'));
			$price_item->set_price($this->unmarshal($this->db->f('price', true), 'float'));
			
			return $price_item;
		}
		
		return null;
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
		
		$q ="INSERT INTO rental_price_item (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id("rental_price_item", 'id');
		
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
				
		$this->db->query('UPDATE rental_price_item SET ' . join(',', $values) . " WHERE id=$id", __LINE__,__FILE__);
		
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
			$values[] = 'date_start = ' . $price_item->get_date_start();
		}
		
		if ($price_item->get_date_end()) {
			$values[] = 'date_end = ' . $price_item->get_date_end();
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
	 * @param $price_item	the price item to remove
	 * @return true if successful, false otherwise
	 */
	function remove_price_item($contract_id, $price_item_id)
	{
		$q = "DELETE FROM rental_contract_price_item WHERE id = {$price_item_id} AND contract_id = {$contract_id}";
		$result = $this->db->query($q);
		if($result)
		{
			rental_socontract::get_instance()->last_updated($contract_id);
			rental_socontract::get_instance()->last_edited_by($contract_id);
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
	function add_price_item($contract_id, $price_item_id)
	{
		$price_item = $this->get_single($price_item_id);
		if($price_item)
		{
			$values = array(
				$price_item_id,
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
				rental_socontract::get_instance()->last_updated($contract_id);
				rental_socontract::get_instance()->last_edited_by($contract_id);
				return true;
			}
		}
		return false;
	}
	
	function reset_contract_price_item($contract_id, $price_item_id)
	{
		//TODO: implement reset function
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
		
		$tables = "rental_price_item";
		$joins = '';
		
		return "SELECT {$cols} FROM {$tables} {$joins} WHERE {$condition} {$order}";
	}
	
	protected function populate(int $price_item_id, &$price_item)
	{
		if($price_item == null)
		{
			$price_item = new rental_price_item($this->unmarshal($this->db->f('id'),'int'));
			$price_item->set_title($this->unmarshal($this->db->f('title'),'string'));
			$price_item->set_agresso_id($this->unmarshal($this->db->f('agresso_id'),'string'));
			$price_item->set_is_area($this->unmarshal($this->db->f('is_area'),'bool'));
			$price_item->set_price($this->unmarshal($this->db->f('price'),'float'));
		}
		return $price_item;
	}
}