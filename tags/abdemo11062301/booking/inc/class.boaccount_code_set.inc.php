<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');
	
	class booking_boaccount_code_set extends booking_bocommon_global_manager_authorized
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soaccount_code_set');
		}
	}