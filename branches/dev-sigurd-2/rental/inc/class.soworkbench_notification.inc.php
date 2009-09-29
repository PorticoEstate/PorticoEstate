<?php 
class rental_soworkbench_notification extends rental_socommon
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
			self::$so = CreateObject('rental.soworkbench_notification');
		}
		return self::$so;
	}
	
	protected function get_id_field_name()
	{
		return 'notification_id';
	}
	
	protected function get_query(string $sort_field, boolean $ascending, string $search_for, string $search_type, array $filters, boolean $return_count)
	{
		//TODO filter account id
		
	$sql = "SELECT rnw.id as id, rc.location_id, rn.account_id, rn.message, rn.contract_id, rn.recurrence, rnw.date, rcr.title, rn.id as originated_from, rc.id as contract_id
					FROM rental_notification_workbench rnw 
					LEFT JOIN rental_notification rn ON (rnw.notification_id = rn.id)
					LEFT JOIN rental_contract_responsibility rcr ON (rcr.location_id = rn.location_id)
					LEFT JOIN rental_contract rc ON(rc.id = rn.contract_id)
					WHERE 
						( rnw.account_id = $account_id 
						OR rnw.account_id IN (SELECT group_id FROM phpgw_group_map WHERE account_id = $account_id) )
						AND rnw.dismissed = 'FALSE'
					ORDER BY rnw.date ASC";
	return $sql;
	}
	
	protected function populate(int $notification_id, &$notification)
	{
		$notification =  new rental_notification(
			$this->unmarshal($this->db->f('id', true), 'int'), 
			$this->unmarshal($this->db->f('account_id', true), 'int'),
			$this->unmarshal($this->db->f('location_id', true), 'int'),
			$this->unmarshal($this->db->f('contract_id', true), 'int'), 
			$this->unmarshal($this->db->f('date', true), 'int'),
			$this->unmarshal($this->db->f('message', true), 'text'),
			$this->unmarshal($this->db->f('recurrence', true), 'int'),
			$this->unmarshal($this->db->f('last_notified', true), 'int'),
			$this->unmarshal($this->db->f('title', true), 'string'),
			$this->unmarshal($this->db->f('originated_from', true), 'int')
		);	
		$notification->set_field_of_responsibility_id($this->db->f('location_id',true),'int');
		return $notification;
	}
	
	function add(&$notification)
	{
		$sql = "INSERT INTO rental_notification_workbench (account_id,date,notification_id,dismissed) VALUES ($account_id, $date, $notification_id,'FALSE')";
		$result = $this->db->query($sql);
		
		if($result) { return true; }
		else { return false; }
	}
	
	function update($notification)
	{
		
	}
	
/**
	 * This method dismisses a workbench notification
	 * @param $id	the workbench notification identifier
	 * @param $ts_dismissed	the timestamp of dismissal
	 * @return true on successful query execution / false otherwise
	 */
	public function dismiss_notification($id)
	{
		$sql = "UPDATE rental_notification_workbench SET dismissed = 'TRUE' WHERE id = $id";
		$result = $this->db->query($sql);
		
		if($result) { return true; }
		else { return false; }
	}
	
	/**
	 * This method dismisses all workbench notifications generated from a given notification
	 * @param $id the notification id all workbench notifications originated from
	 * @return true on successful query execution / false otherwise
	 */
	public function dismiss_notification_for_all($id)
	{
		$sql = "UPDATE rental_notification_workbench SET dismissed = 'TRUE' WHERE notification_id = $id";
		$result = $this->db->query($sql);
		
		if($result) { return true; }
		else { return false; }
	}
}
?>