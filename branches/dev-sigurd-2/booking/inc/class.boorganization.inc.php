<?php
	phpgw::import_class('booking.bocommon');
	
	class booking_boorganization extends booking_bocommon
	{
		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soorganization');
		}
        function get_contact_info($person_id)
        {
            return $this->so->get_contact_info($person_id);
        }
        function get_groups($organization_id) {
            return $this->so->get_groups($organization_id);
        }
	}
