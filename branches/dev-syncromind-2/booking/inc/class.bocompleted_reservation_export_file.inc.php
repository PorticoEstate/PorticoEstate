<?php
	phpgw::import_class('booking.bocommon');

	class booking_bocompleted_reservation_export_file extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.socompleted_reservation_export_file');
		}
	}