<?php
	phpgw::import_class('booking.socommon');
	
	class booking_somassbooking extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_building', 
				array(
					'id' => array('type' => 'int'),
					'name' => array('type' => 'string', 'query' => true, 'required' => true),
					'homepage' => array('type' => 'string'),
					'description' => array('type' => 'string'),
					'phone' => array('type' => 'string'),
					'email' => array('type' => 'string'),
					'location_code' =>array('type' => 'string', 'required' => false),
					'street' 		=> array('type' => 'string', 'query' => true),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string', 'query' => true),
					'city' 			=> array('type' => 'string', 'query' => true),
					'active' => array('type' => 'int')
				)
			);
		}
		function get_permission($userid,$buildingid)
		{
			$sql = "select role from bb_permission where subject_id=".$userid." and object_id=".$buildingid.";";
			$this->db->query($sql);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('role', false);
			}
			return $results;
		}
		
	}
