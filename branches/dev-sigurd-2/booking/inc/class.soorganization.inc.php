<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	
	class booking_soorganization extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_organization', 
				array(
					'id'			=> array('type' => 'int'),
					'name'			=> array('type' => 'string', 'required' => True, 'query' => True),
					'homepage'		=> array('type' => 'string', 'required' => True, 'query' => True),
					'phone'			=> array('type' => 'string'),
					'email'			=> array('type' => 'string'),
					'description'	=> array('type' => 'string'),
                    'admin_primary' => array('type' => 'int',),
                    'admin_secondary' => array('type' => 'int',),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
        function get_contact_info($person_id)
        {
            static $person = null;
            if ($person === null) {
                $person = new booking_socontactperson();
            }
            return $person->read_single($person_id);
        }
	}
