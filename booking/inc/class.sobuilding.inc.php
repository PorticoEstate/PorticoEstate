<?php
	phpgw::import_class('booking.socommon');

	class booking_sobuilding extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_building', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage' => array('type' => 'string', 'required' => true),
				'calendar_text' => array('type' => 'string'),
				'description' => array('type' => 'string'),
				'opening_hours' => array('type' => 'string'),
				'phone' => array('type' => 'string'),
				'email' => array('type' => 'string'),
				'tilsyn_name' => array('type' => 'string'),
				'tilsyn_phone' => array('type' => 'string'),
				'tilsyn_email' => array('type' => 'string'),
				'tilsyn_name2' => array('type' => 'string'),
				'tilsyn_phone2' => array('type' => 'string'),
				'tilsyn_email2' => array('type' => 'string'),
				'deactivate_calendar' => array('type' => 'int'),
				'deactivate_application' => array('type' => 'int'),
				'deactivate_sendmessage' => array('type' => 'int'),
				'extra_kalendar' => array('type' => 'int'),
				'location_code' => array('type' => 'string', 'required' => false),
				'activity_id' => array('type' => 'int', 'required' => false),
				'part_of_town_id' => array('type' => 'string',
					'required' => false,
//					'join' => array(
//						'table' => 'fm_location1',
//						'fkey' => 'location_code',
//						'key' => 'location_code',
//						'column' => 'location_code'
//					),
					'multiple_join' => array(
						'statement' => ' LEFT JOIN fm_locations ON fm_locations.location_code = bb_building.location_code'
						. ' LEFT JOIN fm_location1 ON fm_location1.loc1 = fm_locations.loc1',
						'column' => 'fm_location1.part_of_town_id'
					)),
				'street' => array('type' => 'string', 'query' => true),
				'zip_code' => array('type' => 'string'),
				'district' => array('type' => 'string', 'query' => true),
				'city' => array('type' => 'string', 'query' => true),
				'active' => array('type' => 'int'),
				'activity_name' => array('type' => 'string', 'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					))
				)
			);
		}

		function get_endofseason( $id )
		{
			$this->db->limit_query("SELECT to_ FROM bb_season WHERE status = 'PUBLISHED' AND active=1 AND building_id =" . intval($id) . "ORDER BY to_ DESC", 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return false;
			}
			return $this->db->f('to_', false);
		}

		function get_metainfo( $id )
		{
			$this->db->limit_query("SELECT name, district, city, description FROM bb_building where id=" . intval($id), 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', false),
				'district' => $this->db->f('district', false),
				'city' => $this->db->f('city', false),
				'description' => $this->db->f('description', false));
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
		function find_buildings_used_by( $organization_id, $params = array() )
		{
			if (!isset($params['filters']))
			{
				$params['filters'] = array();
			}
			if (!isset($params['filters']['where']))
			{
				$params['filters']['where'] = array();
			}

			$params['filters']['where'][] = '%%table%%.id IN (' .
				'SELECT br.building_id FROM bb_allocation_resource ar ' .
				'JOIN bb_resource r ON ar.resource_id = r.id ' .
				'JOIN bb_building_resource br ON (br.resource_id  = r.id)  ' .
				'JOIN bb_allocation a ON a.id = ar.allocation_id AND (a.from_ - \'now\'::timestamp < \'300 days\') AND a.organization_id = ' . $this->_marshal($organization_id, 'int') . ' ' .
				')';

			$params['length'] = -1;
			return $this->read($params);
		}

		/**
		 * Returns buildins with resources within top level activity
		 * @param type $activity_id
		 * @return array building ids
		 */
		function get_buildings_from_activity( $activities = array() )
		{
			$soactivity = createObject('booking.soactivity');

			$activity_ids = array();
			foreach ($activities as $activity_id)
			{
				$children = array_merge(array($activity_id), $soactivity->get_children($activity_id));
				$activity_ids = array_merge($activity_ids, $children);
			}
			$buildings = array();
			if (!$activity_ids)
			{
				return $buildings;
			}
			$sql = 'SELECT id FROM bb_building WHERE activity_id IN (' . implode(',', $activity_ids) . ')';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$buildings[] = $this->db->f('id');
			}
			return $buildings;
		}

		/**
		 * Returns buildingnames associated with the id
		 * @param array $ids
		 * @return array buildingnames
		 */
		function get_building_names( $ids = array() )
		{
			$buildings = array();
			if (!$ids)
			{
				return $buildings;
			}
			$sql = 'SELECT bb_building.id, bb_building.name, bb_building.street, bb_building.zip_code, bb_building.district, bb_activity.name as activity'
				. ' FROM bb_building JOIN bb_activity ON bb_building.activity_id = bb_activity.id'
				. ' WHERE bb_building.id IN (' . implode(',', $ids) . ')';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$buildings[$this->db->f('id')] = array
					(
					'name' => $this->db->f('name', true),
					'street' => $this->db->f('street', true),
					'zip_code' => $this->db->f('zip_code'),
					'district' => $this->db->f('district', true),
					'activity' => $this->db->f('activity', true),
				);
			}
			return $buildings;
		}

		protected function doValidate( $entity, booking_errorstack $errors )
		{

			if (count($errors) > 0)
			{
				return; /* Basic validation failed */
			}

			$building_id = $entity['id'] ? (int)$entity['id'] : 0;
			$name = $this->db->db_addslashes($entity['name']);

			$this->db->query("SELECT count(*) as cnt FROM bb_building
								WHERE name = '{$name}' AND id != {$building_id}", __LINE__, __FILE__);
			$this->db->next_record();
			$count = $this->db->f('cnt');

			if($count > 0)
			{
				$errors['building'] = lang('duplicate in name');
			}
		}

		/**
		 * Fetches ids from all booked buildings within a given time-range
		 * @param type $from_date
		 * @param type $to_date
		 */
		function get_all_booked_ids($from_date, $to_date)
		{
			$results = array();
			$db = & $GLOBALS['phpgw']->db;
			$db->query(
				"SELECT DISTINCT bb_building.id FROM (SELECT 'booking'::text AS type,
                                    bb_booking.application_id,
                                    bb_booking.id,
                                    bb_booking.from_,
                                    bb_booking.to_,
                                    bb_booking.cost,
                                    bb_booking.active
                                   FROM bb_booking
                                  WHERE bb_booking.application_id IS NOT NULL
                                UNION
                                SELECT 'allocation'::text AS type,
                                    bb_allocation.application_id,
                                    bb_allocation.id,
                                    bb_allocation.from_,
                                    bb_allocation.to_,
                                    bb_allocation.cost,
                                    bb_allocation.active
                                   FROM bb_allocation
                                  WHERE bb_allocation.application_id IS NOT NULL
                                UNION
                                SELECT 'event'::text AS type,
                                    bb_event.application_id,
                                    bb_event.id,
                                    bb_event.from_,
                                    bb_event.to_,
                                    bb_event.cost,
                                    bb_event.active
                                   FROM bb_event
                                  WHERE bb_event.application_id IS NOT NULL) as BOOKINGS  JOIN bb_application ON application_id = bb_application.id JOIN bb_building ON bb_application.building_name = bb_building.name WHERE  from_ >= TO_DATE('".$from_date."', 'yyyy/mm/dd') AND to_ <= TO_DATE('".$to_date."', 'yyyy/mm/dd')"  , __LINE__, __FILE__);
			$i = 0;
			while ($db->next_record())
			{
				$results[] = $db->f('id', true);
				$i++;
			}
			return $results;
		}

		public function get_facilityTypes()
		{
			$result = array();
			$queryURL = "select id,name from bb_rescategory";
			$this->db->query($queryURL);
			while ($this->db->next_record()) {
				$result[] = array(
//					'resource_id' => $this->db->f('resource_id',false),
//					'resource_name' => $this->db->f('resource_name',false),
					'id' => $this->db->f('id',false),
					'name' => $this->db->f('name',false),


				);
			}
			return $result;
		}
	}