<?php
	phpgw::import_class('booking.async_task');

	/**
	 * Delete the Anonymous frontend-user from access-log
	 */
	class booking_async_task_delete_access_log extends booking_async_task
	{

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
			$config = createobject('phpgwapi.config', 'bookingfrontend')->read();

			$login = !empty($config['anonymous_user']) ? $config['anonymous_user'] : '';

			$domain = $GLOBALS['phpgw_info']['server']['default_domain'];

			/**
			 * Bail out if not defined
			 */
			if(!$login)
			{
				return;
			}

			$sql = "DELETE FROM phpgw_access_log WHERE (loginid = '{$login}' OR loginid = '{$login}#{$domain}') AND li < (extract(epoch from now()) - 4000);";

			$this->db->query($sql);
		}
	}