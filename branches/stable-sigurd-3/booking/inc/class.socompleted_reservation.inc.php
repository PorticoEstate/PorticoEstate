<?php
	phpgw::import_class('booking.socommon');
	
	class booking_socompleted_reservation extends booking_socommon
	{
		protected 
			$resource_so,
			$season_so;
		
		function __construct()
		{
			$this->season_so = CreateObject('booking.soseason');
			$this->resource_so = CreateObject('booking.soresource');
			
			parent::__construct('bb_completed_reservation', 
				array(
					'id' 						=> array('type' => 'int'),
					'reservation_type' 	=> array('type' => 'string', 'required' => True, 'nullable' => False),
					'reservation_id' 		=> array('type' => 'int', 'required' => True, 'nullable' => False),
					'season_id' 			=> array('type' => 'int'),
					'cost'					=> array('type' => 'decimal',   'required' => true),
					'from_'					=> array('type' => 'timestamp', 'required'=> true),
					'to_'						=> array('type' => 'timestamp', 'required'=> true),
					'organization_id'    => array('type' => 'int'),
					'payee_type' 			=> array('type' => 'string', 'nullable' => False),
					'payee_organization_number' => array('type' => 'string', 'precision' => '9', 'sf_validator' => createObject('booking.sfValidatorNorwegianOrganizationNumber', array(), array('invalid' => '%field% is invalid'))),
					'payee_ssn' 			=> array('type' => 'string', 'sf_validator' => createObject('booking.sfValidatorNorwegianSSN')), 
					'exported' 				=> array('type' => 'int', 'required' => True, 'nullable' => False, 'default' => 0),
					'description'			=> array('type' => 'string'),
					'building_name'		=> array('type' => 'string', 'required' => True),
					'resources' => array('type' => 'int', 'required' => True,
						  'manytomany' => array(
							'table' => 'bb_completed_reservation_resource',
							'key' => 'completed_reservation_id',
							'column' => 'resource_id'
					)),
				)
			);
		}
		
		// final public static function getTypeColumnValue() {
		// 	return self::typeColumnValue();
		// }
		// 
		// public static function typeColumnValue() {
		// 	return null;
		// }
		
		public function create_from($type, $reservation) {
			$entity = array(
				'reservation_type' 	=> $type, 
				'reservation_id' 		=> $reservation['id'],
				'cost' 					=> $reservation['cost'],
				'from_' 					=> $reservation['from_'],
				'to_' 					=> $reservation['to_'],
				'payee_type' 			=> 'organization',
				'exported' 				=> 0,
				'resources' 			=> $reservation['resources'],
				'season_id'				=> isset($reservation['season_id']) ? $reservation['season_id'] : null,
			);
			
			//echo "$type\n";
			//print_r($reservation);
			
			$method = "initialize_completed_{$type}";
			$this->$method($reservation, $entity);
			$this->set_description($type, $reservation, $entity);
			$this->add($entity);
		}
		
		protected function set_description($type, &$reservation, &$entity) {
			$building_name = $this->get_building_name($type, $reservation);
			$entity['description'] = substr($entity['from_'], 0, -3) .' - '. substr($entity['to_'], 0, -3) .': '. $building_name . ' - ' . implode(', ', $this->get_resource_names($reservation['resources']));
			$entity['building_name'] = $building_name;
		}
		
		public function get_building_name($type, &$reservation) {
			switch ($type) {
				case 'booking':
				case 'allocation':
					return $this->get_building_for_season($reservation['season_id']);
				case 'event':
					return count($reservation['resources']) > 0 ? $this->get_building_for_resource($reservation['resources'][0]) : '';
			}
			
			return '';
		}
		
		protected function get_building_for_season($season_id) {
			static $cache = array();
			if (!isset($cache[$season_id])) {
				$season = $this->season_so->read_single($season_id);
				$cache[$season_id] = $season['building_name'];
			}
			
			return $cache[$season_id];
		}
		
		protected function get_building_for_resource($resource_id) {
			static $cache = array();
			if (!isset($cache[$resource_id])) {
				$resource = $this->resource_so->read_single($resource_id);
				$cache[$resource_id] = $resource['building_name'];
			}
			
			return $cache[$resource_id];
		}
		
		public function get_resource_names($resources) {
			static $cache = array();
			
			$names = array();
			$uncached = array();
			
			foreach ($resources as $id) {
				if ($name = $this->get_cached_resource_name($id, $cache)) {
					$names[$id] = $name;
				} else {
					$uncached[] = $id;
				}
			}
			
			if (count($uncached) > 0) {
				$found_resources = $this->resource_so->read(array(
					'filters' => array('id' => $uncached),
					'results' => count($uncached),
				));
				
				if (is_array($found_resources) && isset($found_resources['results']) && is_array($found_resources['results'])) {
					//Add to returned names and insert into name cache
					foreach ($found_resources['results'] as $resource) {
						$names[$resource['id']] = $cache[$resource['id']] = $resource['name'];
					}
				}
			}
			
			return $names;
		}
		
		protected function get_cached_resource_name($resource_id, &$cache) {
			return isset($cache[$resource_id]) ? $cache[$resource_id] : null;
		}
		
		protected function set_organization(&$entity, &$organization) {
			$entity['payee_type']      = 'organization';
			$entity['organization_id'] = $organization['id'];
			$entity['payee_organization_number'] = $organization['organization_number'];
		}
		
		protected function initialize_completed_booking(&$booking, &$entity) {
			static $sogroup, $soorg;
			static $cache = array();
			
			!$sogroup AND $sogroup = CreateObject('booking.sogroup');
			!$soorg AND $soorg = CreateObject('booking.soorganization');
			
			if (isset($cache[$booking['group_id']])) {
				$org = $cache[$booking['group_id']];
			} else {
				$group = $sogroup->read_single($booking['group_id']);
				$org = $soorg->read_single($group['organization_id']);
				$cache[$booking['group_id']] = $org;
			}

			$this->set_organization($entity, $org);
		}
		
		protected function initialize_completed_allocation(&$allocation, &$entity) {
			static $soorg;
			static $cache = array();
			
			!$soorg AND $soorg = CreateObject('booking.soorganization');
			if (isset($cache[$allocation['organization_id']])) {
				$org = $cache[$allocation['organization_id']];
			} else {
				$org = $soorg->read_single($allocation['organization_id']);
				$cache[$allocation['organization_id']] = $org;
			}
			
			$this->set_organization($entity, $org);
		}
		
		protected function initialize_completed_event(&$event, &$entity) {
			$entity['payee_type']      = 'public';
		}
	}