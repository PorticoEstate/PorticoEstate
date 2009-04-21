<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soapplication extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_application', 
				array(
					'id'			=> array('type' => 'int'),
					'activity_id'	=> array('type' => 'int', 'required' => true),
					'activity_name'	=> array('type' => 'string',
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'description'	=> array('type' => 'string', 'query' => true, 'required' => true),
					'contact_name'	=> array('type' => 'string'),
					'contact_email'	=> array('type' => 'string'),
					'contact_phone'	=> array('type' => 'string'),
					'comments' => array('type' => 'int',
						  'manytomany' => array(
							'table' => 'bb_application_comment',
							'key' => 'id',
							'column' => 'application_id'
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

	}
