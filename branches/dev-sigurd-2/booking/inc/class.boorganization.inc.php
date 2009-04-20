<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boorganization extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soorganization');
		}
	}
