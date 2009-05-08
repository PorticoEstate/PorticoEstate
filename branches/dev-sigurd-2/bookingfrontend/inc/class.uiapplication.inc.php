<?php
	phpgw::import_class('booking.uiapplication');

	class bookingfrontend_uiapplication extends booking_uiapplication
	{
		public $public_functions = array
		(
			'add'			=>	true,
			'edit'			=>	true,
		);

	}
