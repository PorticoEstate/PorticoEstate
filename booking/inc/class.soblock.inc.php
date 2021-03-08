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


		public function find_expired()
		{
			$table_name = $this->table_name;
			$db = $this->db;
			$expired_conditions = $this->find_expired_sql_conditions();
			return $this->read(array('filters' => array('where' => $expired_conditions), 'results' => 1000));
		}

		protected function find_expired_sql_conditions()
		{
			$table_name = $this->table_name;
			$now = date('Y-m-d H:i:s', time() + 10 * 60);
			return "({$table_name}.active != 0 AND {$table_name}.entry_time < '{$now}')";
		}

		public function delete_expired()
		{
			$expired = $this->find_expired();
			$table_name = $this->table_name;
			$db = $this->db;
			$ids = join(', ', array_map(array($this, 'select_id'), $expired));
			if($ids)
			{
				$sql = "UPDATE {$table_name} SET active = 0 WHERE {$table_name}.id IN ($ids);";
				$db->query($sql, __LINE__, __FILE__);
			}
		}
	}