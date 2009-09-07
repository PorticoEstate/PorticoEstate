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
 			'recurrence'	=> array('type' => 'int')
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
		
		$sql = "SELECT * FROM rental_notification WHERE deleted = 'FALSE' AND $condition $order";
		$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);

		while ($this->db->next_record()) {
			$date = $this->unmarshal($this->db->f('date', true), 'date');
			$notification = new rental_notification(
				$this->unmarshal($this->db->f('id', true), 'int'), 
				$this->unmarshal($this->db->f('account_id', true), 'int'), 
				$this->unmarshal($this->db->f('location_id', true), 'int'),
				$this->unmarshal($this->db->f('contract_id', true), 'int'), 
				$date, 
				$this->unmarshal($this->db->f('message', true), 'text'), 
				$this->unmarshal($this->db->f('recurrence', true), 'int')
			);
			
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
			
			$sql = "SELECT rnw.id as id, rnw.account_id, rn.message, rn.contract_id, rn.recurrence, rnw.date
					FROM rental_notification_workbench rnw 
					LEFT JOIN rental_notification rn ON (rnw.notification_id = rn.id)
					WHERE 
						( rnw.account_id = $account_id 
						OR rnw.account_id IN (SELECT group_id FROM phpgw_group_map WHERE account_id = $account_id) )
						AND rnw.dismissed = 'FALSE'
					ORDER BY rnw.date ASC";
			//var_dump($sql);
			
			$this->db->limit_query($sql, $start, __LINE__, __FILE__, $limit);
			
			while ($this->db->next_record()) {
				
				$results[] = $this->read_notification();
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
			(int)$notification->get_account_id(),
			(int)$notification->get_contract_id(),
			(int)$notification->get_date(),
			"'{$notification->get_message()}'",
			(int)$notification->get_recurrence()
		);
		
		$cols = array('account_id', 'contract_id', 'date', 'message', 'recurrence');
		
		$q ="INSERT INTO ".$this->table_name." (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
		$result = $this->db->query($q);
		$receipt['id'] = $this->db->get_last_insert_id($this->table_name, 'id');
		
		$notification->set_id($receipt['id']);
		
		$this->populate_workbench_notifications();
		return $receipt;
	}
	
	/**
	 * This method is a proxy menthod for the rental_notification::populate_workbench_notifications 
	 * mthod so that it can be called from the Asynchservice in PHPGWAPI
	 * 
	 * @param $day the day to populate the workbench
	 * @return unknown_type
	 */
	public static function populate_workbench($day = null)
	{
		rental_notification::populate_workbench_notifications($day);	
	}
	
	/**
	 * CRON
	 * 
	 * Populate workbench notifications. Traverses all notifications and populates PE users workbenches
	 * based on date information and group membership. 
	 * 	 
	 * @param $today a string date, no parameter means today
	 * @return unknown_type
	 */
	public function populate_workbench_notifications($day = null)
	{
		// Select all notifications not marked as deleted
		$sql = "SELECT * FROM rental_notification WHERE deleted = false";
		
		$result = $this->db->query($sql);
		
		//Iterate through all notifications
		while ($this->db->next_record()) 
		{
			// Create notification object
			$notification = $this->read_notification();

			
			// Calculate timestamps the notification date, target date (default: today) and last notified
			$notification_date = date("Y-m-d",$notification->get_date());
			
			if(!$day)
			{
				$day = date("Y-m-d",strtotime('now'));
			}
			
			$ts_notification_date = strtotime($notification_date);
			$ts_today = strtotime($day);
			$ts_last_notified = $notification->get_last_notified();
			
			
			// Check whether today is a notification date
			$is_today_notification_date = false;
			if( $ts_today == $ts_notification_date ) // today equals notification date
			{
				$is_today_notification_date = true;
				
				// Delete the notification if it should not recur
				if($notification->get_recurrence() == rental_notification::RECURRENCE_NEVER)
				{
					$this->delete_notification($notification->get_id());
				}
			} else { // the original notification date is in the past
				
				// Find out if today is notification date based on recurrence
				$recurrence_interval = '';
				switch($notification->get_recurrence())
				{
					case rental_notification::RECURRENCE_ANNUALLY:
						$recurrence_interval = 'year';
						break;
					case rental_notification::RECURRENCE_MONTHLY:
						$recurrence_interval = 'month';
						break;
					case rental_notification::RECURRENCE_WEEKLY:
						$recurrence_interval = 'week';
						break;
				}
				
				$ts_next_recurrence;
				for($i=1;;$i++) //loop intervals into the future
				{
					// next interval
					$ts_next_recurrence = strtotime('+'.$i.' '.$recurrence_interval,$ts_notification_date);
					
					if($ts_next_recurrence > $ts_last_notified || $ts_last_notified == null) // the date for next recurrence is after last notification
					{
						if($ts_next_recurrence == $ts_today ) // today equals notification date
						{
							$is_today_notification_date = true;
							break;
						}
						break; // not yet reached date for notification
					}	
				}
			}

			// If users should be notified today
			if($is_today_notification_date)
			{
				//notify all users in a group or only the single user if target audience is not a group
				$account_id = $notification->get_account_id();
				$notification_id = $notification->get_id();
				if($GLOBALS['phpgw']->accounts->get_type($account_id) == 'g')
				{
					$accounts = $this->accounts->get_members($account_id);
				
					foreach($accounts as $a)
					{
						$a_id = $a->__get['id'];
						$this->add_workbench_notification($a_id,$ts_today,$notification_id); //user in a group
					}
				} 
				else 
				{
					$this->add_workbench_notification($account_id,$ts_today,$notification_id); // specific user
				}		
				
				// set today as last notification date for this notification
				$this->set_notification_date($notification_id,$ts_today);
			}
		}	
	}
	
	/**
	 * A utility method to read a notification from query result and construct an object representation
	 * 
	 * @return unknown_type
	 */
	private function read_notification()
	{
		return new rental_notification(
			$this->unmarshal($this->db->f('id', true), 'int'), 
			$this->unmarshal($this->db->f('account_id', true), 'int'),
			$this->unmarshal($this->db->f('location_id', true), 'int'),
			$this->unmarshal($this->db->f('contract_id', true), 'int'), 
			$this->unmarshal($this->db->f('date', true), 'int'),
			$this->unmarshal($this->db->f('message', true), 'text'),
			$this->unmarshal($this->db->f('recurrence', true), 'int'),
			$this->unmarshal($this->db->f('last_notified', true), 'int')
		);	
	}
	
	/**
	 * This method adds workbench notifications for a user marked with given date
	 * @param $account_id	the end user
	 * @param $date	the notification date (based on original notification date or recurrence)
	 * @param $notification_id	the notification this workbench notification originated from
	 * @return unknown_type
	 */
	private function add_workbench_notification($account_id, $date, $notification_id)
	{
		$sql = "INSERT INTO rental_notification_workbench (account_id,date,notification_id,dismissed) VALUES ($account_id, $date, $notification_id,'FALSE')";
		$result = $this->db->query($sql);
	}
	
	/**
	 * This method sets the last notification date on a given notification
	 * @param $notification_id	the notification to update
	 * @param $notification_date	the date to update with
	 * @return unknown_type
	 */
	private function set_notification_date($notification_id, $notification_date)
	{
		$sql = "UPDATE rental_notification SET last_notified = $notification_date WHERE id = $notification_id";
		$result = $this->db->query($sql);
	}
	
	/**
	 * This method deletes a notification (marks as deleted)
	 * @param $id	the notification id
	 * @return unknown_type
	 */
	public function delete_notification($id)
	{
		$sql = "UPDATE rental_notification SET deleted = true WHERE id = $id";
		$result = $this->db->query($sql);
	}
	
	/**
	 * This method dismisses a workbench notification
	 * @param $id	the workbench notification identifier
	 * @param $ts_dismissed	the timestamp of dismissal
	 * @return unknown_type
	 */
	public function dismiss_notification($id)
	{
		$sql = "UPDATE rental_notification_workbench SET dismissed = 'TRUE' WHERE id = $id";
		$result = $this->db->query($sql);
	}
}