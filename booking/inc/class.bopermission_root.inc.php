<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_bopermission_root extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sopermission_root');
		}

		public function get_roles()
		{
			return $this->so->get_roles();
		}
	}