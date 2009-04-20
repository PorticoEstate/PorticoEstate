<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boaudience extends booking_bocommon
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

	}
