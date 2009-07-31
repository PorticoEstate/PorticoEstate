<?php
phpgw::import_class('rental.socommon');

include_class('rental', 'notification', 'inc/model/');

class rental_sonotification extends rental_socommon
{
	function __construct()
	{
		parent::__construct('rental_notification',
		array
		(
			'id'	=> array('type' => 'int'),
			'user_id'	=> array('type' => 'int'),
			'contract_id'	=> array('type' => 'int'),
			'message' => array('type' => 'text'),
			'date' => array('type', 'date'),
 			'dismissed'	=> array('type' => 'bool'),
		));
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
		
		$notification = new rental_price_item($this->get_field_value('id'));
		$notification->set_title($this->get_field_value('title'));
		$notification->set_agresso_id($this->get_field_value('agresso_id'));
		$notification->set_is_area($this->get_field_value('is_area'));
		$notification->set_price($this->get_field_value('price'));
		
		return $notification;
	}
	
	function get_single_contract_price_item($id)
	{
		$id = (int)$id;
		
		$sql = "SELECT * FROM rental_contract_price_item WHERE id = " . $id;
		$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
		$this->db->next_record();
		
		$notification = new rental_contract_price_item($this->get_field_value('id'));
		$notification->set_price_item_id($this->get_field_value('price_item_id'));
		$notification->set_contract_id($this->get_field_value('contract_id'));
		$notification->set_title($this->get_field_value('title'));
		$notification->set_agresso_id($this->get_field_value('agresso_id'));
		$notification->set_is_area($this->get_field_value('is_area'));
		$notification->set_price($this->get_field_value('price'));
		$notification->set_area($this->get_field_value('area'));
		$notification->set_count($this->get_field_value('count'));
		$notification->set_total_price($this->get_field_value('total_price'));
		$notification->set_date_start($this->get_field_value('date_start'));
		$notification->set_date_end($this->get_field_value('date_end'));
		
		return $notification;
	}
	
	/**
	 * Get a list of price_item objects matching the specific filters
	 * 
	 * @param $start search result offset
	 * @param $results number of results to return
	 * @param $sort field to sort by
	 * @param $query LIKE-based query string
	 * @param $filters array of custom filters
	 * @return list of notfication objects
	 */
	function get_notification_array($start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array())
	{
		$results = array();
		
		$condition = $this->get_conditions($query, $filters, $search_option);
		$order = $sort ? "ORDER BY $sort $dir ": '';
		
		$sql = "SELECT * FROM rental_notification WHERE $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

		while ($this->db->next_record()) {
			$date = $this->unmarshal($this->db->f('date', true), 'date');
			if(isset($date))
			{
				$date = strtotime($date);
			}
			$notification = new rental_notification($this->unmarshal($this->db->f('id', true), 'int'), $this->unmarshal($this->db->f('user_id', true), 'int'), $this->unmarshal($this->db->f('contract_id', true), 'int'), $date, $this->unmarshal($this->db->f('message', true), 'text'), $this->unmarshal($this->db->f('dismissed', true), 'bool'));
			
			$results[] = $notification;
		}
		
		return $results;
	}
	
	protected function get_conditions($query, $filters,$search_option)
	{	
		$clauses = array('1=1');
		if(isset($filters))
		{
			foreach($filters as $column => $value)
			{
				$clauses[] = $this->db->db_addslashes($column)."=".$this->db->db_addslashes($value);
			}
		}
		
		return join(' AND ', $clauses);
	}
	
	/**
	 * Add a new notification to the database.  Adds the new insert id to the object reference.
	 * 
	 * @param $notification the object to be added
	 * @return result receipt from the db operation
	 */
	function add(&$notification)
	{
		// Build a db-friendly array of the composite object
		$values = array(
			(int)$notification->get_user_id(),
			(int)$notification->get_contract_id(),
			"'".date('Y-m-d', (int)$notification->get_date())."'",
			"'{$notification->get_message()}'",
			$notification->is_dismissed() ? "true" : "false"
		);
		
		$cols = array('user_id', 'contract_id', 'date', 'message', 'dismissed');
		
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$notification->set_id($receipt['id']);
		
		return $receipt;
	}
}