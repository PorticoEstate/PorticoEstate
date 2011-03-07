<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sosystem_message extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_system_message', 
				array(
					'id' => array('type' => 'int'),
					'created' => array('type' => 'string'),
					'title' => array('type' => 'string', 'query' => true, 'required' => true),
					'display_in_dashboard' => array('type' => 'int', 'nullable' => False, 'precision' => '4', 'default' => 1),
					'building_id' => array('type' => 'int', 'precision' => '4'),
					'name' => array('type' => 'string','nullable' => False),
					'phone' => array('type' => 'string','nullable' => False, 'default'=>''),
					'email' => array('type' => 'string','nullable' => False, 'default'=>''),
					'message' => array('type' => 'string', 'required' => true),
					'type' => array('type' => 'string', 'default' => 'message'),
					'status' => array('type' => 'string', 'default' => 'NEW')
				)
			);
		}
		
	}


