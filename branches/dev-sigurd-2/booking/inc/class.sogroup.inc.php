<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	
	class booking_sogroup extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_group', 
				array(
					'id'			=> array('type' => 'int'),
					'organization_id'	=> array('type' => 'int', 'required' => true),
					'description'    => array('type' => 'description', 'query' => true, 'required' => false,),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'contact_primary' => array('type' => 'int', 'precision' => '4', 'nullable' => false, 'required'=>true),
					'contact_secondary' => array('type' => 'int', 'precision' => '4', 'nullable' => True,),
					'organization_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_organization',
							'fkey' => 'organization_id',
							'key' => 'id',
							'column' => 'name'
						))
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

