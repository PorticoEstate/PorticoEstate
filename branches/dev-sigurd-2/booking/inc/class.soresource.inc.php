<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soresource extends booking_socommon
	{
		function __construct()
		{
			parent::__construct('bb_resource', 
				array(
					'id'			=> array('type' => 'int'),
					'building_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'building_name'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'activity_name'	=> array('type' => 'string',
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'description'			=> array('type' => 'string', 'query' => true, 'required' => false),
					'activity_id'			=> array('type' => 'int', 'query' => true, 'required' => false)
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
	}
