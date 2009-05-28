<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_bogroup extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sogroup');
		}
	}

