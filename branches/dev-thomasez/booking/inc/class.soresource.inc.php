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
		public static function getresources($searchterm,$wclause)
		{

            $results = array();

			if($searchterm != '') {
				$wclause .= " AND (br.name like lower('%".$searchterm."%') OR br.description like lower('%".$searchterm."%'))";
			}
			
			$sql = "SELECT br.id,br.active,br.sort,br.building_id,br.name,br.type,
br.description,br.activity_id,br.campsites,br.bedspaces,br.heating,br.water,
br.kitchen,br.location,br.communication,br.usage_time,br.internal_cost,
br.external_cost,br.cost_type,bb.name AS building_name,bb.street AS building_street,
bb.city AS building_city,bb.district AS building_district,ba.name AS activity_name 
FROM bb_resource AS br,bb_building AS bb,bb_activity AS ba 
WHERE br.building_id = bb.id AND br.activity_id = ba.id AND br.active = 1 ".$wclause." ORDER BY br.name ASC";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			while ($GLOBALS['phpgw']->db->next_record())
			{
				$results[] = array('id'  => $GLOBALS['phpgw']->db->f('id', false),
									'active'  => $GLOBALS['phpgw']->db->f('active', false),
									'sort'  => $GLOBALS['phpgw']->db->f('sort', false),
									'building_id'  => $GLOBALS['phpgw']->db->f('building_id', false),
									'name'  => $GLOBALS['phpgw']->db->f('name', false),
									'type'  => $GLOBALS['phpgw']->db->f('type', false),
									'description'  => $GLOBALS['phpgw']->db->f('description', false),
									'activity_id'  => $GLOBALS['phpgw']->db->f('activity_id', false),
									'campsites'  => $GLOBALS['phpgw']->db->f('campsites', false),
									'bedspaces'  => $GLOBALS['phpgw']->db->f('bedspaces', false),
									'heating'  => $GLOBALS['phpgw']->db->f('heating', false),
									'water'  => $GLOBALS['phpgw']->db->f('water', false),
									'kitchen'  => $GLOBALS['phpgw']->db->f('kitchen', false),
									'location'  => $GLOBALS['phpgw']->db->f('location', false),
									'communication'  => $GLOBALS['phpgw']->db->f('communication', false),
									'usage_time'  => $GLOBALS['phpgw']->db->f('usage_time', false),
									'internal_cost'  => $GLOBALS['phpgw']->db->f('internal_cost', false),
									'external_cost'  => $GLOBALS['phpgw']->db->f('external_cost', false),
									'cost_type'  => $GLOBALS['phpgw']->db->f('cost_type', false),
									'building_name'  => $GLOBALS['phpgw']->db->f('building_name', false),
									'building_street'  => $GLOBALS['phpgw']->db->f('building_street', false),
									'building_city '  => $GLOBALS['phpgw']->db->f('building_city', false),
									'building_district'  => $GLOBALS['phpgw']->db->f('building_district', false),
									'activity_name'  => $GLOBALS['phpgw']->db->f('activity_name', false));
			}
			$GLOBALS['phpgw']->db->query("SELECT count(1) AS count FROM bb_resource AS br,bb_building AS bb,bb_activity AS ba 
WHERE br.building_id = bb.id AND br.activity_id = ba.id AND br.active = 1 ".$wclause, __LINE__, __FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$total_records = (int)$GLOBALS['phpgw']->db->f('count');

			return array(
				'total_records' => $total_records,
				'results'		=> $results,
				'start'			=> 0,
				'sort'			=> null,
				'dir'			=> 'asc'
			);


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
