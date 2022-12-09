<?php
	phpgw::import_class('booking.async_task');

	class booking_async_task_anonyminizer extends booking_async_task
	{

		private $db;

		public function __construct()
		{
			parent::__construct();
			$this->db = & $GLOBALS['phpgw']->db;
		}

		public function get_default_times()
		{
			return array('day' => '*/1');
		}

		public function run( $options = array() )
		{
			/**
			 * Get users
			 */
			$users = $this->get_users();

			/**
			 * anonyminize
			 */
			$souser = CreateObject('booking.souser');
			foreach ($users as $user_id)
			{
				$souser->delete($user_id);
			}
		}


		/**
		 * Work in progress...
		 * @return array
		 */
		private function get_users()
		{
			$users = array();

			$time_limit = date('Y-m-d', time() - 2 * 3600 * 24 * 365);

			$sql = "SELECT DISTINCT id FROM ("
				. "SELECT bb_application.customer_ssn, bb_user.id "
				. " FROM bb_application JOIN bb_user ON bb_user.customer_ssn = bb_application.customer_ssn "
				. " WHERE modified < '$time_limit'"
				. " AND substring(bb_user.customer_ssn, 1, 4) != '0000'"
				. " AND bb_application.customer_ssn NOT IN ("
				. "		SELECT customer_ssn FROM bb_application "
				. "		WHERE modified > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_event "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_completed_reservation "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "	)"
				. " UNION"
				. " SELECT DISTINCT bb_event.customer_ssn, bb_user.id "
				. " FROM bb_event JOIN bb_user ON bb_user.customer_ssn = bb_event.customer_ssn"
				. " WHERE to_ < '$time_limit'"
				. " AND substring(bb_user.customer_ssn, 1, 4) != '0000'"
				. " AND bb_event.customer_ssn NOT IN ("
				. "		SELECT customer_ssn FROM bb_application "
				. "		WHERE modified > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_event "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_completed_reservation "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "	)"
				. " UNION"
				. " SELECT DISTINCT bb_completed_reservation.customer_ssn, bb_user.id "
				. " FROM bb_completed_reservation JOIN bb_user ON bb_user.customer_ssn = bb_completed_reservation.customer_ssn"
				. " WHERE to_ < '$time_limit'"
				. " AND substring(bb_user.customer_ssn, 1, 4) != '0000'"
				. " AND bb_completed_reservation.customer_ssn NOT IN ("
				. "		SELECT customer_ssn FROM bb_application "
				. "		WHERE modified > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_event "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "		UNION"
				. "		SELECT customer_ssn FROM bb_completed_reservation "
				. "		WHERE to_ > '$time_limit' AND ( customer_ssn IS NOT NULL AND customer_ssn != '')"
				. "	)"
				. ") as t";

//			_debug_array($sql);die();
			$this->db->query($sql,__LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$users[] = $this->db->f('id');
			}

			return $users;
		}
	}