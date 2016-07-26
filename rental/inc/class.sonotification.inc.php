<?php
	phpgw::import_class('rental.socommon');
	phpgw::import_class('rental.soworkbench_notification');

	include_class('rental', 'notification', 'inc/model/');

	class rental_sonotification extends rental_socommon
	{

		protected static $so;
		protected $db2;
		public function __construct()
		{
			parent::__construct();
			$this->db2 = clone($this->db);
		}

		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return rental_sonotification the storage object
		 */
		public static function get_instance()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.sonotification');
			}
			return self::$so;
		}

		protected function get_id_field_name()
		{
			return 'notification_id';
		}

		protected function get_query( string $sort_field, bool $ascending, string $search_for, string $search_type, array $filters, bool $return_count )
		{
			$clauses = array('1=1');

			$dir = $ascending ? 'ASC' : 'DESC';
			$order = $sort_field ? "ORDER BY $sort_field $dir" : '';

			if (isset($filters))
			{
				foreach ($filters as $column => $value)
				{
					$clauses[] = $this->db->db_addslashes($column) . "=" . $this->db->db_addslashes($value);
				}
			}

			if ($return_count) // We should only return a count
			{
				$cols = 'COUNT(DISTINCT(rn.id)) AS count';
			}
			else
			{
				$cols = 'rn.id AS notification_id, rn.location_id, rn.account_id, rn.contract_id, rn.message, rn.date, rn.last_notified, rn.recurrence, rn.deleted, rcr.title, rc.location_id';
			}

			$condition = join(' AND ', $clauses);

			//$order = $sort ? "ORDER BY $sort $dir ": '';

			$sql = "SELECT {$cols} FROM rental_notification rn
		LEFT JOIN rental_contract_responsibility rcr ON (rcr.location_id = rn.location_id)
		LEFT JOIN rental_contract rc ON(rc.id = rn.contract_id)
		WHERE deleted = 'FALSE' AND $condition $order";

			return $sql;
		}

		protected function populate( int $notification_id, &$notification )
		{
			if (!isset($notification_id) || $notification_id < 1)
			{
				$notification_id = $this->unmarshal($this->db->f('notification_id', true), 'int');
			}
			$notification = new rental_notification(
				$notification_id, $this->unmarshal($this->db->f('account_id', true), 'int'), $this->unmarshal($this->db->f('location_id', true), 'int'), $this->unmarshal($this->db->f('contract_id', true), 'int'), $this->unmarshal($this->db->f('date', true), 'int'), $this->unmarshal($this->db->f('message', true), 'text'), $this->unmarshal($this->db->f('recurrence', true), 'int'), $this->unmarshal($this->db->f('last_notified', true), 'int'), $this->unmarshal($this->db->f('title', true), 'string'), $this->unmarshal($this->db->f('originated_from', true), 'int')
			);
			$notification->set_field_of_responsibility_id($this->db->f('location_id', true), 'int');
			return $notification;
		}

		protected function populate2( int $notification_id, &$notification )
		{
			if (!isset($notification_id) || $notification_id < 1)
			{
				$notification_id = $this->unmarshal($this->db2->f('notification_id', true), 'int');
			}
			$notification = new rental_notification(
				$notification_id,
				$this->unmarshal($this->db2->f('account_id'), 'int'),
				$this->unmarshal($this->db2->f('location_id'), 'int'),
				$this->unmarshal($this->db2->f('contract_id'), 'int'),
				$this->unmarshal($this->db2->f('date'), 'int'),
				$this->unmarshal($this->db2->f('message', true), 'text'),
				$this->unmarshal($this->db2->f('recurrence'), 'int'),
				$this->unmarshal($this->db2->f('last_notified'), 'int'),
				$this->unmarshal($this->db2->f('title', true), 'string'),
				$this->unmarshal($this->db2->f('originated_from'), 'int')
			);
			$notification->set_field_of_responsibility_id($this->db2->f('location_id'));
			return $notification;
		}

		function add( &$notification )
		{
			$cols = array('contract_id', 'date', 'message', 'recurrence');
			$values = array(
				(int)$notification->get_contract_id(),
				(int)$notification->get_date(),
				"'{$notification->get_message()}'",
				(int)$notification->get_recurrence(),
			);

			if ($notification->get_account_id() && $notification->get_account_id() > 0)
			{
				$cols[] = 'account_id';
				$values[] = $notification->get_account_id();
			}

			if ($notification->get_location_id() && $notification->get_location_id() > 0)
			{
				$cols[] = 'location_id';
				$values[] = $notification->get_location_id();
			}

			$q = "INSERT INTO rental_notification (" . join(',', $cols) . ") VALUES (" . join(',', $values) . ")";
			$result = $this->db->query($q);
			if ($result)
			{
				$notification->set_id($this->db->get_last_insert_id('rental_notification', 'id'));
				$this->populate_workbench_notifications();
				return true;
			}
			return false;
		}

		function update( $notification )
		{
			// TODO: Not implemented yet
			return false;
		}

		/**
		 * This method is a proxy menthod for the rental_notification::populate_workbench_notifications
		 * method so that it can be called from the Asynchservice in PHPGWAPI
		 *
		 * @param $day the day to populate the workbench
		 * @return unknown_type
		 */
		public static function populate_workbench( $day = null )
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
		public function populate_workbench_notifications( $day = null )
		{
			// Select all notifications not marked as deleted
			$sql = "SELECT * FROM rental_notification WHERE deleted = false";

			$result = $this->db2->query($sql);

			//Iterate through all notifications
			while ($this->db2->next_record())
			{
				$result_id = $this->unmarshal($this->db2->f('id', true), 'int'); // The id of object
				// Create notification object
				$notification = $this->populate2($result_id, $notification);

				// Calculate timestamps the notification date, target date (default: today) and last notified
				$notification_date = date("Y-m-d", $notification->get_date());

				if (!$day || is_array($day))
				{
					$day = date("Y-m-d", strtotime('now'));
				}

				$ts_notification_date = strtotime($notification_date);
				$ts_today = strtotime($day);
				$ts_last_notified = $notification->get_last_notified();


				// Check whether today is a notification date
				$is_today_notification_date = false;
				if ($ts_today == $ts_notification_date) // today equals notification date
				{
					$is_today_notification_date = true;

					// Delete the notification if it should not recur
					if ($notification->get_recurrence() == rental_notification::RECURRENCE_NEVER)
					{
						$this->delete_notification($notification->get_id());
					}
				}
				else
				{ // the original notification date is in the past
					// Find out if today is notification date based on recurrence
					$recurrence_interval = '';
					switch ($notification->get_recurrence())
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
					for ($i = 1;; $i++) //loop intervals into the future
					{
						// next interval
						$ts_next_recurrence = strtotime('+' . $i . ' ' . $recurrence_interval, $ts_notification_date);

						if ($ts_next_recurrence > $ts_last_notified || $ts_last_notified == null) // the date for next recurrence is after last notification
						{
							if ($ts_next_recurrence == $ts_today) // today equals notification date
							{
								$is_today_notification_date = true;
								break;
							}
							break; // not yet reached date for notification
						}
					}
				}

				// If users should be notified today
				if ($is_today_notification_date)
				{

					//notify all users in a group or only the single user if target audience is not a group
					$account_id = $notification->get_account_id();
					$notification_id = $notification->get_id();
					$account_ids = array();


					if ($account_id)
					{
						if ($GLOBALS['phpgw']->accounts->get_type($account_id) == phpgwapi_account::TYPE_GROUP)
						{
							$accounts = $GLOBALS['phpgw']->accounts->get_members($account_id);
							$account_ids = array_merge($account_ids, $accounts);  //users in a group
						}
						else
						{
							$account_ids[] = $account_id; // specific user
						}
					}

					//notify all users with write access on the field of responsibility
					$location_id = $notification->get_location_id();
					if ($location_id)
					{
						$location_names = $GLOBALS['phpgw']->locations->get_name($location_id);
						if ($location_names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
						{
							$responsible_accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_EDIT, $location_names['location']);
							foreach ($responsible_accounts as $ra)
							{
								$account_ids[] = $ra['account_id'];
							}
						}
					}

					//merge the two account arrays and retrieve only unique account ids
					$unique_account_ids = array_unique($account_ids);

					//notify each unique account
					foreach ($unique_account_ids as $unique_account)
					{
						if ($unique_account && $unique_account > 0)
						{

							$notification = new rental_notification(
								0, // No notification identifier
								$unique_account,
								0, // No location identifier
								$this->unmarshal($this->db2->f('contract_id', true), 'int'),
								$ts_today,
								null,
								null,
								null,
								null,
								$notification_id
							);
							rental_soworkbench_notification::get_instance()->store($notification);
						}
					}

					// set today as last notification date for this notification
					$this->set_notification_date($notification_id, $ts_today);
				}
			}
		}

		/**
		 * This method sets the last notification date on a given notification
		 * @param $notification_id	the notification to update
		 * @param $notification_date	the date to update with
		 * @return true on successful query execution / false otherwise
		 */
		private function set_notification_date( $notification_id, $notification_date )
		{
			$sql = "UPDATE rental_notification SET last_notified = $notification_date WHERE id = $notification_id";
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
		 * This method deletes a notification (marks as deleted)
		 * @param $id	the notification id
		 * @return true on successful query execution / false otherwise
		 */
		public function delete_notification( $id )
		{
			$sql = "UPDATE rental_notification SET deleted = true WHERE id = $id";
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