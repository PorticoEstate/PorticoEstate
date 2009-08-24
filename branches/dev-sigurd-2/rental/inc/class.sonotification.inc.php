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
			'account_id'	=> array('type' => 'int'),
			'contract_id'	=> array('type' => 'int'),
			'message' => array('type' => 'text'),
			'date' => array('type', 'date'),
 			'recurrence'	=> array('type' => 'int'),
			'dismissed'	=>	array('type' => 'date')
		));
	}
	
	
	
	/**
	 * Get all notifications with regards to result offset, search query and filters (e.g. contract identifier). 
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
			$notification = new rental_notification(
				$this->unmarshal($this->db->f('id', true), 'int'), 
				$this->unmarshal($this->db->f('account_id', true), 'int'), 
				$this->unmarshal($this->db->f('contract_id', true), 'int'), 
				$date, 
				$this->unmarshal($this->db->f('message', true), 'text'), 
				$this->unmarshal($this->db->f('recurrence', true), 'int'));
			
			$results[] = $notification;
		}
		
		return $results;
	}
	
	/**
	 * Get all active (not dismissed) workbench notifications
	 * 
	 * @param $start paginator parameter
	 * @param $limit paginator paramater
	 * @param $account_id the account identifier
	 * @return the workbench notification objects for the user
	 */
	function get_workbench_notifications($start = 0, $limit = 1000, $account_id)
	{	
		$results = array();
		if(isset($account_id)){
			$now = strtotime("now");
			
			$sql = "SELECT rnw.id as workbench_id, rnw.dismissed, rnw.account_id, rn.message, rn.contract_id, rn.recurrence, rnw.date
					FROM rental_notification_workbench rnw 
					LEFT JOIN rental_notification rn ON (rnw.notification_id = rn.id)
					WHERE 
						( rnw.account_id = $account_id 
						OR rnw.account_id IN (SELECT group_id FROM phpgw_group_map WHERE account_id = $account_id) )
						AND dismissed > $now 
					ORDER BY rnw.date ASC";
			
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			
			while ($this->db->next_record()) {
				$notification = new rental_notification(
					$this->unmarshal($this->db->f('workbench_id', true), 'int'), 
					$this->unmarshal($this->db->f('account_id', true), 'int'), 
					$this->unmarshal($this->db->f('contract_id', true), 'int'), 
					$this->unmarshal($this->db->f('date', true), 'int'),
					$this->unmarshal($this->db->f('message', true), 'text'),
					$this->unmarshal($this->db->f('recurrence', true), 'int'),
					$this->unmarshal($this->db->f('dismissed', true), 'int'));
				$results[] = $notification;
			}
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
			(int)$notification->get_recurrence()
		);
		
		$cols = array('user_id', 'contract_id', 'date', 'message', 'recurrence');
		
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$notification->set_id($receipt['id']);
		
		return $receipt;
	}
	
	public function populate_workbench_notifications()
	{
		// Go through all notification data and add/remove notifications from the users workbench

		
	}
}