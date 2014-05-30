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
					'id'				=> array('type' => 'int'),
					'organization_number' => array('type' => 'string', 'query'=> true, 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'name'			=> array('type' => 'string', 'required' => True, 'query' => True),
					'shortname'		=> array('type' => 'string', 'required' => False, 'query' => True),
					'homepage'		=> array('type' => 'string', 'required' => False, 'query' => True),
					'phone'			=> array('type' => 'string'),
					'email'			=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'description'	=> array('type' => 'string'),
					'street' 		=> array('type' => 'string'),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string'),
					'city' 			=> array('type' => 'string'),
					'active'		=> array('type' => 'int', 'required'=>true),
					'show_in_portal'		=> array('type' => 'int', 'required'=>true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_number'				 		=> array('type' => 'string', 'required' => False),
					'customer_ssn' 						=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'customer_internal'					=> array('type' => 'int', 'required'=>true),
					'activity_name'	=> array('type' => 'string',
						  'query' => true,
						  'join' => array(
							'table' => 'bb_activity',
							'fkey' => 'activity_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'contacts'		=> array('type' => 'string',
						'manytomany' => array(
							'table' => 'bb_organization_contact',
							'key' => 'organization_id',
							'column' => array('name',
							                  'ssn' => array('sf_validator' => createObject('booking.sfValidatorNorwegianSSN')), 
							                  'email' => array('sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% contains an invalid email'))),
							                  'phone')
						)
					),
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_metainfo($id)
		{
			$this->db->limit_query("SELECT name, shortname, district, city, description FROM bb_organization where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', false),
						  'shortname' => $this->db->f('shortname', false),
						  'district' => $this->db->f('district', false),
						  'city' => $this->db->f('city', false),
						  'description' => $this->db->f('description', false));
		}
        function get_orgid($orgnr)
        {
            $this->db->limit_query("SELECT id FROM bb_organization where organization_number ='" . $orgnr."'", 0, __LINE__, __FILE__, 1);
            if(!$this->db->next_record())
            {
                return False;
            }
            return $this->db->f('id', false);
        }
        function get_groups($organization_id)
        {
            static $groups = null;
            if ($groups === null) {
                $groups = new booking_sogroup();
            }
            $results = $groups->read(array("filters" => array("organization_id" => $organization_id)));
            return $results;
        }
		
		/**
		 * Returns the organizations who've used the building with the specified id
		 * within the last 300 days.
		 *
		 * @param int $building_id
		 * @param array $params Parameters to pass to socommon->read
		 *
		 * @return array (in socommon->read format)
		 */
		function find_building_users($building_id, $params = array()) {				
			if (!isset($params['filters'])) { $params['filters'] = array(); }
			if (!isset($params['filters']['where'])) { $params['filters']['where'] = array(); }
			
			$params['filters']['where'][] = '%%table%%.id IN ('.
				'SELECT o.id FROM bb_resource r '.
				'JOIN bb_allocation_resource ar ON ar.resource_id = r.id AND r.building_id = '.$this->_marshal($building_id, 'int').' '.
				'JOIN bb_allocation a ON a.id = ar.allocation_id AND (a.from_ - \'now\'::timestamp < \'300 days\')'.
				'JOIN bb_organization o ON o.id = a.organization_id '.
			')';
			
			return $this->read($params);
		}
	}

