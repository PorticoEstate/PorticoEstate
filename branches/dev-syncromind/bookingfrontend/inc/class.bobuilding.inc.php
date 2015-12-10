<?php
	phpgw::import_class('booking.bocommon');

	class bookingfrontend_bobuilding extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.sobuilding');
		}
	}