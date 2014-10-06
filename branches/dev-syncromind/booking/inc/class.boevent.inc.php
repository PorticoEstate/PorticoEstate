<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boevent extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soevent');
		}
		
		public function complete_expired(&$events) {
			$this->so->complete_expired($events);
		}
		
		public function find_expired() {
			return $this->so->find_expired();
		}
	}
