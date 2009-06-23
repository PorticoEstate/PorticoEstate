<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boevent extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soevent');
		}

	}
