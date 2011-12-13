<?php
	phpgw::import_class('booking.socommon');
	
	class booking_soresource extends booking_socommon
	{
		const TYPE_LOCATION = 'Location';
		const TYPE_HOUSE = 'House';
		const TYPE_EQUIPMENT = 'Equipment';
		const TYPE_BOAT = 'Boat';
		const TYPE_CAMPSITE = 'Campsite';
		const COST_TYPE_PH	= 'Perhour';
		const COST_TYPE_PD	= 'Perday';
		const COST_TYPE_PW	= 'Perweek';
			

		function __construct()
		{
			parent::__construct('bb_resource', 
				array(
					'id'			=> array('type' => 'int'),
					'active'		=> array('type' => 'int', 'required' => true),
					'sort'			=> array('type' => 'int', 'required' => false),
					'building_id'	=> array('type' => 'int', 'required' => true),
					'name'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'type'			=> array('type' => 'string', 'query' => true, 'required' => true),
					'description'			=> array('type' => 'string', 'query' => true, 'required' => false),
					'activity_id'			=> array('type' => 'int', 'required' => false),
					'campsites'  => array('type' => 'int'),
					'bedspaces'  => array('type' => 'int'),
					'heating' 		=> array('type' => 'string', 'query' => true),
					'water' 		=> array('type' => 'string', 'query' => true),
					'kitchen' 		=> array('type' => 'string', 'query' => true),
					'location' 		=> array('type' => 'string', 'query' => true),
					'communication' => array('type' => 'string', 'query' => true),
					'usage_time' 	=> array('type' => 'string', 'query' => true),
					'internal_cost'		=> array('type' => 'int'),
					'external_cost'		=> array('type' => 'int'),
					'cost_type'		=> array('type' => 'string'),
					'building_name'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					)),
					'building_street'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'street'
					)),
					'building_city'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'city'
					)),
					'building_district'	=> array('type' => 'string',
						  'query'		=> true,
						  'join' 		=> array(
							'table' 	=> 'bb_building',
							'fkey' 		=> 'building_id',
							'key' 		=> 'id',
							'column' 	=> 'district'
					)),
					'activity_name'	=> array('type' => 'string', 'query' => true, 
						  'join' 		=> array(
							'table' 	=> 'bb_activity',
							'fkey' 		=> 'activity_id',
							'key' 		=> 'id',
							'column' 	=> 'name'
					))
				)
			);
			$this->account		= $GLOBALS['phpgw_info']['user']['account_id'];
		}
		
		public static function allowed_types()
		{
			return array(self::TYPE_HOUSE, self::TYPE_CAMPSITE, self::TYPE_BOAT, self::TYPE_EQUIPMENT,self::TYPE_LOCATION);
		}

		public static function allowed_cost_types()
		{
			return array(self::COST_TYPE_PH, self::COST_TYPE_PD, self::COST_TYPE_PW);
		}
		
		function doValidate($entity, booking_errorstack $errors)
		{
			parent::doValidate($entity, $errors);
			if (!isset($errors['type']) && !in_array($entity['type'], self::allowed_types(), true)) {
				$errors['type'] = lang('Invalid Resource Type');
			}
		}
	}
