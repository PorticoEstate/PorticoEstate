<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_boaudience extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soaudience');
		}

		public function set_active_session()
		{
			$_SESSION['ActiveSession'] = "ShowAll";
		}

		public function unset_active_session()
		{
			unset($_SESSION['ActiveSession']);
		}

		function fetch_target_audience( $top_level_activity = 0 )
		{
			$filters = array('active' => '1');
			if ($top_level_activity)
			{
				$filters['activity_id'] = $top_level_activity;
			}

			return $this->so->read(array('filters' => $filters, 'sort' => 'sort'));
		}
	}