<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sobuilding extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_building', 
				array(
					'id' => array('type' => 'int'),
					'name' => array('type' => 'string', 'query' => true, 'required' => true),
					'homepage' => array('type' => 'string', 'query' => true),
					'description' => array('type' => 'string'),
					'phone' => array('type' => 'string'),
					'email' => array('type' => 'string'),
					'address' => array('type' => 'string'),
					'active' => array('type' => 'int')
				)
			);
		}
	}
