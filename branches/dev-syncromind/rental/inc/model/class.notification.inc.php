<?php
	include_class('rental', 'model', 'inc/model/');

	/**
	 * Class representing notifications, e.g. reminders for ending contracts, time for dismissals and coming
	 * regulations. The class is designed so that it can be used to represent general notification data, such
	 * as notification audience, notification date data, and a user message. It can also represent specific users
	 * workbench notification, holding data such as date of dismissal.
	 */
	class rental_notification extends rental_model
	{

		// Contants for recurrence
		const RECURRENCE_NEVER = 0;
		const RECURRENCE_ANNUALLY = 1;
		const RECURRENCE_MONTHLY = 2;
		const RECURRENCE_WEEKLY = 3;

		public static $so;  // Storage object
		protected $id; // Notification id
		protected $contract_id;  // Contract identifier
		protected $location_id;  // Location identifier (audience)
		protected $account_id;  // Specific user or group (audience)
		protected $date;   // Notification date data
		protected $last_notified; // Date lst notified
		protected $message;   // User message
		protected $field_of_responsibility; //The title of the field of responsibility (location)
		protected $originated_from;

		/**
		 * Constructor for creating a notification data object
		 *
		 * @param int $id	Notification identifier
		 * @param int $account_id	Account identifier
		 * @param int $contract_id	Contract identifier
		 * @param string $date	Notification date
		 * @param string $message	User message
		 * @param $recurrence	Recurrence constant
		 * @param $dismissed date	The date for dismissal (workbench notification), optional
		 */
		public function __construct(
		int $id = null, int $account_id = null, int $location_id = null, int $contract_id = null, int $date = null, string $message = null, $recurrence = RECURRENCE_NEVER, int $last_notified = null, string $title = null, int $originated_from = null )
		{
			$this->id = (int)$id;
			if ($account_id > 0)
			{
				$this->account_id = (int)$account_id;
			}
			if ($location_id > 0)
			{
				$this->location_id = (int)$location_id;
			}
			$this->contract_id = (int)$contract_id;
			$this->date = $date;
			$this->last_notified = $last_notified;
			$this->message = $message;
			$this->recurrence = (int)$recurrence;
			$this->field_of_responsibility = $title;
			$this->originated_from = $originated_from;
		}

		// Get methods
		public function get_id()
		{
			return $this->id;
		}

		public function get_contract_id()
		{
			return $this->contract_id;
		}

		public function get_account_id()
		{
			if ($this->account_id && $this->account_id > 0)
				return $this->account_id;
			return null;
		}

		public function get_date()
		{
			return $this->date;
		}

		public function get_last_notified()
		{
			return $this->last_notified;
		}

		public function get_message()
		{
			return $this->message;
		}

		public function get_recurrence()
		{
			return $this->recurrence;
		}

		public function get_location_id()
		{
			return $this->location_id;
		}

		public function get_field_of_responsibility()
		{
			return $this->field_of_responsibility;
		}

		public function get_originated_from()
		{
			return $this->originated_from;
		}

		// Set methods
		public function set_id( int $id )
		{
			$this->id = (int)$id;
		}

		/**
		 * Convert this object to a hash representation
		 *
		 * @see rental/inc/model/rental_model#serialize()
		 */
		public function serialize()
		{
			$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
			$recurrence = lang('never'); // Default
			switch ($this->get_recurrence())
			{
				case rental_notification::RECURRENCE_ANNUALLY:
					$recurrence = lang('annually');
					break;
				case rental_notification::RECURRENCE_MONTHLY:
					$recurrence = lang('monthly');
					break;
				case rental_notification::RECURRENCE_WEEKLY:
					$recurrence = lang('weekly');
					break;
			}

			$account = $GLOBALS['phpgw']->accounts->get($this->get_account_id());
			if ($account)
			{
				$name = $account->__toString();
			}


			if ($this->get_field_of_responsibility())
			{
				$responsibility = lang($this->get_field_of_responsibility());
			}

			return array(
				'id' => $this->get_id(),
				'account_id' => $this->get_account_id(),
				'name' => $name,
				'contract_id' => $this->get_contract_id(),
				'message' => $this->get_message(),
				'date' => date($date_format, $this->get_date()),
				'recurrence' => $recurrence,
				'field_of_responsibility' => $responsibility,
				'originated_from' => $this->get_originated_from(),
				'permissions' => $this->get_permission_array()
			);
		}

		// Static functions
		/**
		 * Get a static reference to the storage object associated with this model object
		 *
		 * @return the storage object
		 */
		public static function get_so()
		{
			if (self::$so == null)
			{
				self::$so = CreateObject('rental.sonotification');
			}

			return self::$so;
		}

		// Get all notifications
		public static function get_all( $start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array() )
		{
			$so = self::get_so();
			return $so->get_notification_array($start, $results, $sort, $dir, $query, $search_option, $filters);
		}

		// Get workbench notifications
		public static function get_workbench_notifications( $start = 0, $results = 1000, $sort = null, $dir = '', $query = null, $search_option = null, $filters = array() )
		{
			$so = self::get_so();
			return $so->get_workbench_notifications($start, $results, $sort, $dir, $query, $search_option, $filters);
		}

		// Delete a notification
		public static function delete_notification( $id )
		{
			$so = self::get_so();
			return $so->delete_notification($id);
		}

		// Dismiss a workbench notification
		public static function dismiss_notification( $id, $ts_dismissed )
		{
			$so = self::get_so();
			return $so->dismiss_notification($id, $ts_dismissed);
		}

		public static function dismiss_notification_for_all( $notification_id )
		{
			$so = self::get_so();
			return $so->dismiss_notification_for_all($notification_id);
		}

		// Populates the workbench on a given day
		public static function populate_workbench_notifications( $day = null )
		{
			$so = self::get_so();
			return $so->populate_workbench_notifications();
		}
	}