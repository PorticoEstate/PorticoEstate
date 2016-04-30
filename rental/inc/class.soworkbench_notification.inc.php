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
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.soworkbench_notification');
			}
			return self::$so;
		}

		protected function get_id_field_name()
		{
			return 'id';
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$clauses = array('1=1');

			//Add columns to this array to include them in the query
			$columns = array();

			// Default is sorting on date ascending
			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY $sort_field $dir" : 'ORDER BY rnw.date ASC';

			if (isset($filters['account_id']))
			{
				$account_id = $this->marshal($filters['account_id'], 'int');
			}

			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rnw.id)) AS count';
				$order = '';
			}
			else
			{
				// ID and date from workbench table
				$columns[] = 'rnw.id as id, rnw.date, rnw.workbench_message';
				// The location (responsibility) and contract id from contract
				$columns[] = 'rc.location_id, rc.id as contract_id';
				// The id for the notification origin, the account id, the contract id, the message, and the recurrence
				$columns[] = 'rn.id as originated_from, rn.account_id, rn.message, rn.contract_id, rn.recurrence';
				// The title of the field of responsibility for this notification (through contract)
				$columns[] = 'rcr.title';
				$cols = implode(',', $columns);
			}

			$sql = "SELECT {$cols}
				FROM rental_notification_workbench rnw 
				{$this->left_join} rental_notification rn ON (rnw.notification_id = rn.id)
				{$this->left_join} rental_contract_responsibility rcr ON (rcr.location_id = rn.location_id)
				{$this->left_join} rental_contract rc ON(rc.id = rn.contract_id)
				WHERE 
					( rnw.account_id = $account_id 
					OR rnw.account_id IN (SELECT group_id FROM phpgw_group_map WHERE account_id = $account_id) )
					AND rnw.dismissed = 'FALSE'
				{$order}";
			//var_dump($sql);
			return $sql;
		}

		protected function populate( int $notification_id, &$notification )
		{
			$message = $this->unmarshal($this->db->f('message', true), 'text');
			if (!isset($message) || $message == '')
			{
				$message = lang($this->unmarshal($this->db->f('workbench_message', true), 'text'));
			}
			$notification = new rental_notification(
				$this->unmarshal($this->db->f('id', true), 'int'), $this->unmarshal($this->db->f('account_id', true), 'int'), $this->unmarshal($this->db->f('location_id', true), 'int'), $this->unmarshal($this->db->f('contract_id', true), 'int'), $this->unmarshal($this->db->f('date', true), 'int'), $message, $this->unmarshal($this->db->f('recurrence', true), 'int'), $this->unmarshal($this->db->f('last_notified', true), 'int'), $this->unmarshal($this->db->f('title', true), 'string'), $this->unmarshal($this->db->f('originated_from', true), 'int')
			);
			$notification->set_field_of_responsibility_id($this->db->f('location_id', true), 'int');
			return $notification;
		}

		function add( &$notification )
		{
			$account_id = $this->marshal($notification->get_account_id(), 'int');
			$date = $this->marshal($notification->get_date(), 'int');
			$notification_id = $this->marshal($notification->get_originated_from(), 'int');
			$workbench_message = $this->marshal($notification->get_message(), 'string');

			$sql = "INSERT INTO rental_notification_workbench (account_id,date,notification_id,workbench_message,dismissed) VALUES ({$account_id},{$date},{$notification_id},{$workbench_message},'FALSE')";
			$result = $this->db->query($sql);

			if ($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		function update( $notification )
		{

		}

		/**
		 * This method dismisses a workbench notification
		 * @param $id	the workbench notification identifier
		 * @param $ts_dismissed	the timestamp of dismissal
		 * @return true on successful query execution / false otherwise
		 */
		public function dismiss_notification( $id )
		{
			$sql = "UPDATE rental_notification_workbench SET dismissed = 'TRUE' WHERE id = {$id}";
			$result = $this->db->query($sql);

			if ($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}

		/**
		 * This method dismisses all workbench notifications generated from a given notification
		 * @param $id the notification id all workbench notifications originated from
		 * @return true on successful query execution / false otherwise
		 */
		public function dismiss_notification_for_all( $id )
		{
			$sql = "UPDATE rental_notification_workbench SET dismissed = 'TRUE' WHERE notification_id = {$id}";
			$result = $this->db->query($sql);

			if ($result)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}