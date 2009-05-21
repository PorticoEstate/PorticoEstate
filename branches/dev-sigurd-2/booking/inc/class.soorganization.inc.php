<?php
	phpgw::import_class('booking.socommon');
	phpgw::import_class('booking.socontactperson');
	phpgw::import_class('booking.sogroup');
	
	class booking_soorganization extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_organization', 
				array(
					'id'			=> array('type' => 'int'),
					'name'			=> array('type' => 'string', 'required' => True, 'query' => True),
					'homepage'		=> array('type' => 'string', 'required' => False, 'query' => True),
					'phone'			=> array('type' => 'string'),
					'email'			=> array('type' => 'string'),
					'description'	=> array('type' => 'string'),
                    'active'		=> array('type' => 'int', 'required'=>true),
                    'admin_primary' => array('type' => 'int', 'required'=>true),
                    'admin_secondary' => array('type' => 'int',),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
        function get_contact_info($person_id)
        {
            static $person = null;
            static $cache = array();

            if ($person === null) {
                $person = new booking_socontactperson();
            }
            if (!isset($cache[$person_id])) {
                $cache[$person_id] = $person->read_single($person_id);
            }

            return $cache[$person_id];
        }
        function get_groups($organization_id)
        {
            static $groups = null;
            if ($groups === null) {
                $groups = new booking_sogroup();
            }
            $results = $groups->read(array("filters" => array("organization_id" => $organization_id)));
            foreach($results["results"] as &$result) {
                $result["cp"] = $this->get_contact_info($result["contact_primary"]);
                $result["cs"] = $this->get_contact_info($result["contact_secondary"]);
            }
            return $results;
        }
	}

