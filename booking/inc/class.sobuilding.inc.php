<?php
	phpgw::import_class('booking.socommon');

	class booking_sobuilding extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_building', array(
				'id' => array('type' => 'int', 'query' => true),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage' => array('type' => 'string', 'required' => true),
				'calendar_text' => array('type' => 'string'),
				'description' => array('type' => 'string'),
				'description_json' => array('type' => 'json'),
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
				'activity_id' => array('type' => 'int', 'required' => true),
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
			$this->db->limit_query("SELECT to_ FROM bb_season WHERE status = 'PUBLISHED' AND active=1 AND building_id =" . intval($id) . " ORDER BY to_ DESC", 0, __LINE__, __FILE__, 1);
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
			return array('name' => $this->db->f('name', true),
				'district' => $this->db->f('district', true),
				'city' => $this->db->f('city', true),
				'description' => $this->db->f('description', true));
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

		function get_user_list()
		{
			$sql	 = "SELECT DISTINCT account_id, account_lastname,  account_firstname FROM bb_permission "
				. "JOIN phpgw_accounts ON bb_permission.subject_id = phpgw_accounts.account_id WHERE object_type = 'building'";
			$this->db->query($sql);
			$users	 = array();
			while ($this->db->next_record())
			{
				$users[] = array(
					'id' => $this->db->f('account_id'),
					'name' => $this->db->f('account_lastname', true) . ', ' . $this->db->f('account_firstname', true),
				);
			}

			return $users;
		}
		function _get_conditions( $query, $filters )
		{
			$conditions = parent::_get_conditions($query, $filters);

			$filter_user_id = phpgw::get_var('filter_user_id', 'int');

			if($filter_user_id)
			{

				if(is_array($filter_user_id))
				{
					$filter_user_ids = array_map('abs', $filter_user_id);
				}
				else
				{
					$filter_user_ids = array(abs($filter_user_id));
				}

				$sql = "SELECT object_id FROM bb_permission WHERE object_type = 'building' AND subject_id IN (" .implode(',', $filter_user_ids) .")";
				$this->db->query($sql );
				$building_ids = array(-1);
				while ($this->db->next_record())
				{
					$building_ids[] = $this->db->f('object_id');
				}
				$conditions .= ' AND bb_building.id IN (' . implode(',', $building_ids) . ')';
			}
			return $conditions;

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

		public function get_facility_types($query="")
		{
			$query_SQL = " where name ilike '$query%'";
			if (empty($query)) {
				$query_SQL = '';
			}
			$result = array();
			$queryURL = "select id,name from bb_rescategory".$query_SQL;
			$this->db->query($queryURL  );
			while ($this->db->next_record())
			{
				$result[] = array(
					'id' => $this->db->f('id',false),
					'name' => $this->db->f('name',true),
				);
			}
			return $result;
		}

		public function get_building_id_from_resource_id($resource_id)
		{
			$queryURL = "select building_id from bb_building_resource where resource_id = {$resource_id}";
			$this->db->query($queryURL);

			if (!$this->db->next_record())
			{
				return false;
			}
			return $this->db->f('building_id');
		}
	}
