<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');
	
	class booking_boactivity extends booking_bocommon_global_manager_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soactivity');
		}

		function fetch_activities()
		{
			return $this->so->read(array());
		}

		function get_activity($id)
		{
			return $this->activity_so->read_single($id);
		}
	}
