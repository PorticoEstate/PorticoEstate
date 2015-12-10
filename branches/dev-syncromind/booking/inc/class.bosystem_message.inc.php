<?php
	phpgw::import_class('booking.bocommon');

	class booking_bosystem_message extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sosystem_message');
		}
	}