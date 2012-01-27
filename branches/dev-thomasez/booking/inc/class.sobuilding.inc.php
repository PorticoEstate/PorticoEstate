<?php
	phpgw::import_class('booking.socommon');
	
	class booking_sobuilding extends booking_socommon
	{
		const COST_TYPE_PH	= 'Perhour';
		const COST_TYPE_PD	= 'Perday';
		const COST_TYPE_PW	= 'Perweek';

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
					'deactivate_calendar' => array('type' => 'int'),
					'deactivate_application' => array('type' => 'int'),
					'deactivate_sendmessage' => array('type' => 'int'),
					'location_code' =>array('type' => 'string', 'required' => false),
					'street' 		=> array('type' => 'string', 'query' => true),
					'zip_code' 		=> array('type' => 'string'),
					'district' 		=> array('type' => 'string', 'query' => true),
					'city' 			=> array('type' => 'string', 'query' => true),
					'active' => array('type' => 'int'),
					'internal_cost'		=> array('type' => 'int'),
					'external_cost'		=> array('type' => 'int'),
					'cost_type'		=> array('type' => 'string'),
					'campsites'  => array('type' => 'int'),
					'bedspaces'  => array('type' => 'int'),
					'heating' 		=> array('type' => 'string', 'query' => true),
					'water' 		=> array('type' => 'string', 'query' => true),
					'kitchen' 		=> array('type' => 'string', 'query' => true),
					'location' 		=> array('type' => 'string', 'query' => true),
					'communication' => array('type' => 'string', 'query' => true),
					'usage_time' 	=> array('type' => 'string', 'query' => true),
					'weather_url'		=> array('type' => 'string'),
					'map_url'		=> array('type' => 'string')
				)
			);
		}

		function fylker()
		{
			return array( "akerhus" => "Akershus",
				"oslo" => "Oslo",
				"austagder" => "Aust-Agder",
				"buskerud" => "Buskerud",
				"finnmark" => "Finnmark",
				"hedemark" => "Hedmark",
				"hordaland" => "Hordaland",
				"moreogromsdal" => "Møre og Romsdal",
				"nordland" => "Nordland",
				"nordtrodelag" => "Nord-Trøndelag",
				"oppland" => "Oppland",
				"rogaland" => "Rogaland",
				"songogfjordane" => "Sogn og Fjordane",
				"sortrondelag" => "Sør-Trøndelag",
				"telemark" => "Telemark",
				"troms" => "Troms",
				"vestagder" => "Vest-Agder",
				"vestfold" => "Vestfold",
				"ostfold" => "Østfold");
		}	

		public static function allowed_cost_types()
		{
			return array(self::COST_TYPE_PH, self::COST_TYPE_PD, self::COST_TYPE_PW);
		}
	
		/**
		 * Returns buildings used by the organization with the specified id
		 * within the last 300 days.
		 *
		 * @param int $organization_id
		 * @param array $params Parameters to pass to socommon->read
		 *
		 * @return array (in socommon->read format)
		 */
		function find_buildings_used_by($organization_id, $params = array()) {				
			if (!isset($params['filters'])) { $params['filters'] = array(); }
			if (!isset($params['filters']['where'])) { $params['filters']['where'] = array(); }
			
			$params['filters']['where'][] = '%%table%%.id IN ('.
				'SELECT r.building_id FROM bb_allocation_resource ar '.
				'JOIN bb_resource r ON ar.resource_id = r.id '. 
				'JOIN bb_allocation a ON a.id = ar.allocation_id AND (a.from_ - \'now\'::timestamp < \'300 days\') AND a.organization_id = '.$this->_marshal($organization_id, 'int').' '.
			')';
			
			return $this->read($params);
		}
	}
