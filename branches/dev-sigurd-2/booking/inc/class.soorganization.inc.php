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
					'email'			=> array('type' => 'string', 'sf_validator' => new sfValidatorEmail(array(), array('invalid' => '%field% is invalid'))),
					'description'	=> array('type' => 'string'),
					'street' 		=> array('type' => 'string'),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string'),
					'city' 			=> array('type' => 'string'),
                    'active'		=> array('type' => 'int', 'required'=>true),
					'contacts'		=> array('type' => 'string',
						'manytomany' => array(
							'table' => 'bb_organization_contact',
							'key' => 'organization_id',
							'column' => array('name',
							                  'ssn' => array('sf_validator' => new sfValidatorNorwegianSSN()), 
							                  'email' => array('sf_validator' => new sfValidatorEmail(array(), array('invalid' => '%field% contains an invalid email'))),
							                  'phone')
						)
					),
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

