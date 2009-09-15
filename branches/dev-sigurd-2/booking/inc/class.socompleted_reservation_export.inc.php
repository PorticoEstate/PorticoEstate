<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socompleted_reservation_export extends booking_socommon
	{
		// protected 
		// 	$resource_so,
		// 	$season_so;
		
		function __construct()
		{
			// $this->season_so = CreateObject('booking.soseason');
			// $this->resource_so = CreateObject('booking.soresource');
			
			parent::__construct('bb_completed_reservation_export', 
				array(
					'id' 						=> array('type' => 'int'),
					'season_id' 			=> array('type' => 'int'),
					'building_id'    		=> array('type' => 'int'),
					'from_'					=> array('type' => 'timestamp', 'required' => true),
					'to_'						=> array('type' => 'timestamp', 'required' => true),
					'filename'    			=> array('type' => 'string', 'required' => true),
					key(booking_socommon::$AUTO_CREATED_ON) => current(booking_socommon::$AUTO_CREATED_ON),
					// 'account_codes_id'    => array('type' => 'int', 'precision' => '4'), 
					'season_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' => 'bb_season',
							'fkey' => 'season_id',
							'key' => 'id',
							'column' => 'name'
					)),
					'building_name'	=> array('type' => 'string', 'query' => true, 'join' => array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
				)
			);
		}
	}