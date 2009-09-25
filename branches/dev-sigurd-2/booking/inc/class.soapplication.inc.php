<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soapplication extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_application', 
				array(
					'id'		=> array('type' => 'int', 'query' => true),
					'active'	=> array('type' => 'int'),
					'status'	=> array('type' => 'string', 'required' => true),
					'secret'	=> array('type' => 'string', 'required' => true),
					'created'	=> array('type' => 'timestamp'),
					'modified'	=> array('type' => 'timestamp'),
					'owner_id'	=> array('type' => 'int', 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'status'	=> array('type' => 'string', 'required' => true),
					'customer_identifier_type' 		=> array('type' => 'string', 'required' => False),
					'customer_ssn' 						=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN'), 'required' => false),
					'customer_organization_number' 	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'owner_name'	=> array('type' => 'string', 'query' => true,
						  'join' 		=> array(
							'table' 	=> 'phpgw_accounts',
							'fkey' 		=> 'owner_id',
							'key' 		=> 'account_id',
							'column' 	=> 'account_lid'
					)),
					'activity_name'	=> array('type' => 'string',
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'description'	=> array('type' => 'string', 'query' => true, 'required' => true),
					'contact_name'	=> array('type' => 'string', 'query' => true, 'required'=> true),
					'contact_email'	=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorEmail', array(), array('invalid' => '%field% is invalid'))),
					'contact_phone'	=> array('type' => 'string'),
					'audience' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_targetaudience',
							'key' => 'application_id',
							'column' => 'targetaudience_id'
					)),
					'agegroups' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_agegroup',
							'key' => 'application_id',
							'column' => array('agegroup_id' => array('type' => 'int', 'required' => true), 'male' => array('type' => 'int', 'required' => true), 'female' => array('type' => 'int', 'required' => true)),
					)),
					'dates' => array('type' => 'timestamp', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_date',
							'key' => 'application_id',
							'column' => array('from_', 'to_', 'id')
					)),
					'comments' => array('type' => 'string',
						  'manytomany' => array(
							'table' => 'bb_application_comment',
							'key' => 'application_id',
							'column' => array('time', 'author', 'comment')
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_resource',
							'key' => 'application_id',
							'column' => 'resource_id'
					))
				)
			);
		}

		function get_building_info($id)
		{
			$this->db->limit_query("SELECT bb_building.id, bb_building.name FROM bb_building, bb_resource, bb_application_resource WHERE bb_building.id=bb_resource.building_id AND bb_resource.id=bb_application_resource.resource_id AND bb_application_resource.application_id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', true),
						 'name' => $this->db->f('name', true));
		}
		
		/**
		 * Check if a given timespan is available for bookings or allocations
		 *
		 * @param resources 
		 * @param timespan start
		 * @param timespan end
		 *
		 * @return boolean
		 */
		function check_timespan_availability($resources, $from_, $to_)
		{
			$rids = join(',', array_map("intval", $resources));
			$nrids = count($resources);
			$this->db->query("SELECT id FROM bb_season 
			                  WHERE id IN (SELECT season_id 
							               FROM bb_season_resource 
							               WHERE resource_id IN ($rids,-1) 
							               GROUP BY season_id 
							               HAVING count(season_id)=$nrids)", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$season_id = $this->_unmarshal($this->db->f('id', true), 'int');
				if (CreateObject('booking.soseason')->timespan_within_season($season_id, new DateTime($from_), new DateTime($to_)))
				{
					return true;
				}
			}
			return false;
		}
	}
