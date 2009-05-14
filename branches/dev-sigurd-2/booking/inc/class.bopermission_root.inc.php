<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_bopermission_root extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sopermission_root');
		}
	}