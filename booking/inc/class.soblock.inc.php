<?php
	phpgw::import_class('booking.socommon');

	class booking_soblock extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_block', array(
				'id' => array('type' => 'int'),
				'active' => array('type' => 'int', 'required' => true, 'default' => 1),
				'from_' => array('type' => 'string', 'required' => true),
				'to_' => array('type' => 'string', 'required' => true),
				'entry_time' => array('type' => 'timestamp', 'required' => true, 'default' => date('Y-m-d H:i:s')),
				'session_id' => array('type' => 'string', 'required' => false),
				'resource_id' => array('type' => 'int', 'required' => true),
				)
			);
		}

		public function delete_block( $id )
		{
			$id = (int) $id;
			if(!$id)
			{
				return false;
			}
			$db = $this->db;
			$db->transaction_begin();

			$table_name = $this->table_name;
//			$sql = "DELETE FROM $table_name WHERE id = ($id)";
			$sql = "UPDATE {$table_name} SET active = 0 WHERE {$table_name}.id IN ($id);";
			$db->query($sql, __LINE__, __FILE__);

			return	$db->transaction_commit();
		}

		public function cancel_block($session_id, $dates, $resources)
		{
			$table_name = $this->table_name;

			if(!$resources)
			{
				return;
			}
			else if(isset($resources[0]['id']))
			{
				$resource_ids = join(', ', array_map(array($this, 'select_id'), $resources));
			}
			else
			{
				$resource_ids = join(', ', $resources);
			}

			foreach ($dates as $checkdate)
			{
				$checkdate['from_'];
				$checkdate['to_'];

				$sql = "UPDATE {$table_name} SET active = 0"
				. " WHERE session_id = '{$session_id}'"
				. " AND from_ = '{$checkdate['from_']}'"
				. " AND to_ = '{$checkdate['to_']}'"
				. " AND resource_id IN ({$resource_ids});";
				$this->db->query($sql, __LINE__, __FILE__);
			}
		}

		public function delete_expired()
		{
			$expired = $this->find_expired();
			$table_name = $this->table_name;
			$this->db->transaction_begin();

			$ids = join(', ', array_map(array($this, 'select_id'), $expired['results']));
			if($ids)
			{
				$sql = "UPDATE {$table_name} SET active = 0 WHERE {$table_name}.id IN ($ids);";
				$this->db->query($sql, __LINE__, __FILE__);
			}

			/**
			 * Delete old partial applications as well
			 */
			$yesterday = date('Y-m-d H:i:s', time() -  4 * 3600);

//			$sql = "SELECT id FROM bb_application WHERE status = 'NEWPARTIAL1' AND created < '$yesterday'";

			$sql = "SELECT bb_application.id FROM bb_application"
				. " LEFT JOIN bb_event ON bb_application.id = bb_event.application_id"
				. " WHERE bb_application.status = 'NEWPARTIAL1'"
				. " AND bb_event.id IS NULL AND created < '$yesterday'"
				. " ORDER by bb_application.id";

			$this->db->query($sql, __LINE__, __FILE__);
			$applications = array(-1,-2,-3);
			while($this->db->next_record())
			{
				$applications[] = $this->db->f('id');
			}

			$soapplication = createObject('booking.soapplication');
			foreach ($applications as $application_id)
			{
				$soapplication->delete_application($application_id);
			}
			$this->db->transaction_commit();

		}

		public function find_expired()
		{
			$table_name = $this->table_name;
			$expired_conditions = $this->find_expired_sql_conditions();
			return $this->read(array('filters' => array('where' => $expired_conditions), 'results' => 1000));
		}

		/**
		 * 10 minutes old
		 * @return string condition
		 */
		protected function find_expired_sql_conditions()
		{
			$table_name = $this->table_name;

			$timezone	 = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

			try
			{
				$DateTimeZone	 = new DateTimeZone($timezone);
			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			$now = new DateTime('now', $DateTimeZone);
			$now->modify('-10 minutes');
			$now_string = $now->format('Y-m-d H:i');

			return "({$table_name}.active != 0 AND {$table_name}.entry_time < '{$now_string}')";
		}

	}