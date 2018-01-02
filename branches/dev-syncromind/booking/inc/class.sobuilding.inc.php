<?php
	phpgw::import_class('booking.socommon');

	class booking_sobuilding extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_building', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage' => array('type' => 'string'),
				'calendar_text' => array('type' => 'string'),
				'description' => array('type' => 'string'),
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
					'join' => array(
						'table' => 'fm_location1',
						'fkey' => 'location_code',
						'key' => 'location_code',
						'column' => 'location_code'
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
	}