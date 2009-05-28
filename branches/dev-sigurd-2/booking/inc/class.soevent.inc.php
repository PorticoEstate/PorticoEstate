<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soevent extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_event', 
				array(
					'id'		=> array('type' => 'int'),
					'active'	=> array('type' => 'int', 'required' => true),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'description' => array('type' => 'string', 'required'=> true),
					'from_'		=> array('type' => 'string', 'required'=> true),
					'to_'		=> array('type' => 'string', 'required'=> true),
					'cost'		=> array('type' => 'decimal', 'required' => true),
					'contact_name' => array('type' => 'string', 'required'=> true),
					'contact_email' => array('type' => 'string', 'required'=> true, 'sf_validator' => new sfValidatorEmail(array(), array('invalid' => '%field% is invalid'))),
					'contact_phone' => array('type' => 'string', 'required'=> true),
					'activity_name'	=> array('type' => 'string',
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'resources' => array('type' => 'int', 'required' => true,
						  'manytomany' => array(
							'table' => 'bb_event_resource',
							'key' => 'event_id',
							'column' => 'resource_id'
					)),
				)
			);
		}

		function get_building_info($id)
		{
			$this->db->limit_query("SELECT bb_building.id, bb_building.name FROM bb_building, bb_resource, bb_event_resource WHERE bb_building.id=bb_resource.building_id AND bb_resource.id=bb_event_resource.resource_id AND bb_event_resource.event_id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if(!$this->db->next_record())
			{
				return False;
			}
			return array('id' => $this->db->f('id', true),
						 'name' => $this->db->f('name', true));
		}

	}
