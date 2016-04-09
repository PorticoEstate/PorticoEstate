<?php
	phpgw::import_class('booking.bocommon');

	class booking_bocontactperson extends booking_bocommon
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.socontactperson');
		}

		function get_contact_info( $person_id )
		{
			return $this->so->get_contact_info($person_id);
		}
	}