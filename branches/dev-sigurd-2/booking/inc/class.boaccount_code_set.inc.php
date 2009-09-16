<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boaccount_code_set extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soaccount_code_set');
		}
	}