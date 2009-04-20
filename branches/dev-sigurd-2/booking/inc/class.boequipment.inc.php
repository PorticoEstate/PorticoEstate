<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boequipment extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soequipment');
		}
	}
