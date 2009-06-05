<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soapplication extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_application', 
				array(
					'id'		=> array('type' => 'int'),
					'active'	=> array('type' => 'int'),
					'status'	=> array('type' => 'string', 'required' => true),
					'secret'	=> array('type' => 'string', 'required' => true),
					'created'	=> array('type' => 'timestamp'),
					'modified'	=> array('type' => 'timestamp'),
					'owner_id'	=> array('type' => 'int', 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'status'	=> array('type' => 'string', 'required' => true),
					'owner_name'	=> array('type' => 'string',
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
					'contact_name'	=> array('type' => 'string'),
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
							'column' => array('agegroup_id', 'male', 'female')
					)),
					'dates' => array('type' => 'timestamp', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_application_date',
							'key' => 'application_id',
							'column' => array('from_', 'to_')
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
	}
