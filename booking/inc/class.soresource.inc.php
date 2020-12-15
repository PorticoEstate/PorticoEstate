<?php
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('booking.socommon');

	class booking_soresource extends booking_socommon
	{

		const TYPE_LOCATION = 'Location';
		const TYPE_EQUIPMENT = 'Equipment';

		function __construct()
		{
			parent::__construct('bb_resource', array(
				'id' => array('type' => 'int'),
				'active' => array('type' => 'int', 'required' => true),
				'sort' => array('type' => 'int', 'required' => false),
//				'building_id'		 => array('type' => 'int', 'required' => true),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'type' => array('type' => 'string', 'query' => true, 'required' => true),
				'description' => array('type' => 'string', 'query' => true, 'required' => false),
				'opening_hours' => array('type' => 'string'),
				'contact_info' => array('type' => 'string'),
				'activity_id' => array('type' => 'int', 'required' => false),
				'organizations_ids' => array('type' => 'string'),
				'json_representation' => array('type' => 'json'),
				'rescategory_id' => array('type' => 'int', 'required' => false),
				'direct_booking' => array('type' => 'int', 'required' => false),
				'direct_booking_season_id' => array('type' => 'int', 'required' => false),
				'simple_booking' => array('type' => 'int', 'required' => false),
				'simple_booking_start_date' => array('type' => 'int', 'required' => false),
				'simple_booking_end_date' => array('type' => 'int', 'required' => false),
				'booking_day_horizon' => array('type' => 'int', 'required' => false),
				'booking_month_horizon' => array('type' => 'int', 'required' => false),
				'booking_day_default_lenght' => array('type' => 'int', 'required' => false),
				'booking_dow_default_start' => array('type' => 'int', 'required' => false),
//				'booking_dow_default_end' => array('type' => 'int', 'required' => false),
				'booking_time_default_start' => array('type' => 'int', 'required' => false),
				'booking_time_default_end' => array('type' => 'int', 'required' => false),
				'building_id' => array(
					'type' => 'int',
					'query' => true,
					'join_type' => 'manytomany',
					'join' => array(
						'table' => 'bb_building_resource',
						'fkey' => 'id',
						'key' => 'resource_id',
						'column' => 'building_id'
					)),
				/* 				'building_name'		 => array('type'	 => 'string',
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
				  )), */
				'activity_name' => array('type' => 'string', 'query' => true,
					'join' => array(
						'table' => 'bb_activity',
						'fkey' => 'activity_id',
						'key' => 'id',
						'column' => 'name'
					)),
				'rescategory_name' => array('type' => 'string', 'query' => true,
					'join' => array(
						'table' => 'bb_rescategory',
						'fkey' => 'rescategory_id',
						'key' => 'id',
						'column' => 'name',
					)),
				'direct_booking_season_name' => array('type' => 'string', 'query' => false,
					'join' => array(
						'table' => 'bb_season',
						'fkey' => 'direct_booking_season_id',
						'key' => 'id',
						'column' => 'name',
					)),
				'rescategory_active' => array('type' => 'int',
					'join' => array(
						'table' => 'bb_rescategory',
						'fkey' => 'rescategory_id',
						'key' => 'id',
						'column' => 'active',
					)),
				'buildings' => array('type' => 'int', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_building_resource',
						'key' => 'resource_id',
						'column' => 'building_id'
					)),
				'activities' => array('type' => 'int',
					'manytomany' => array(
						'table' => 'bb_resource_activity',
						'key' => 'resource_id',
						'column' => 'activity_id',
					)),
				'facilities' => array('type' => 'int',
					'manytomany' => array(
						'table' => 'bb_resource_facility',
						'key' => 'resource_id',
						'column' => 'facility_id',
					)),
				'e_locks' => array('type' => 'string',
					'manytomany' => array(
						'table' => 'bb_resource_e_lock',
						'key' => 'resource_id',
						'column' => array('e_lock_system_id', 'e_lock_resource_id', 'e_lock_name','access_code_format', 'active', 'modified_on', 'modified_by'),
					)),
				)
			);
			$this->account = $GLOBALS['phpgw_info']['user']['account_id'];
		}

		function get_metainfo( $id )
		{
			$id = (int)$id;
			$sql = "SELECT bb_resource.name, bb_building.name as building, bb_building.city, bb_building.district, bb_resource.description"
				. " FROM bb_resource JOIN bb_building_resource ON  bb_resource.id =bb_building_resource.resource_id"
				. " JOIN bb_building ON bb_building_resource.building_id = bb_building.id"
				. " WHERE bb_resource.id={$id}";

			$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return False;
			}
			return array('name' => $this->db->f('name', true),
				'building' => $this->db->f('building', true),
				'district' => $this->db->f('district', true),
				'city' => $this->db->f('city', true),
				'description' => $this->db->f('description', true));
		}

		public static function allowed_types()
		{
			return array(self::TYPE_LOCATION, self::TYPE_EQUIPMENT);
		}

		protected function preValidate( &$entity )
		{
			if (!empty($entity['direct_booking']))
			{
				$entity['direct_booking'] = phpgwapi_datetime::date_to_timestamp($entity['direct_booking']);
			}
			if (!empty($entity['simple_booking_start_date']))
			{
				$entity['simple_booking_start_date'] = phpgwapi_datetime::date_to_timestamp($entity['simple_booking_start_date']);
			}
			if (!empty($entity['simple_booking_end_date']))
			{
				$entity['simple_booking_end_date'] = phpgwapi_datetime::date_to_timestamp($entity['simple_booking_end_date']);
			}
		}

		function doValidate( $entity, booking_errorstack $errors )
		{
			parent::doValidate($entity, $errors);
			if (!isset($errors['type']) && !in_array($entity['type'], self::allowed_types(), true))
			{
				$errors['type'] = lang('Invalid Resource Type');
			}
		}

		function _get_conditions( $query, $filters )
		{
			static $custom_fields_arr = array();
			$conditions = parent::_get_conditions($query, $filters);

			$soactivity = createObject('booking.soactivity');
			$activity_ids = array();


			$custom_condition_arr = array();
			$custom_fields_criteria = array();
			if (isset($filters['filter_top_level']) && is_array($filters['filter_top_level']))
			{
				foreach ($filters['filter_top_level'] as $activity_top_level)
				{
					if (!isset($activity_ids[$activity_top_level]))
					{
						$activity_ids[$activity_top_level] = array_merge(array($activity_top_level), $soactivity->get_children($activity_top_level, 0, true));
					}
				}
				unset($activity_top_level);
			}
//			_debug_array($activity_ids);
			if (isset($filters['custom_fields_criteria']) && is_array($filters['custom_fields_criteria']))
			{
				//			_debug_array($filters['custom_fields_criteria']);
				foreach ($filters['custom_fields_criteria'] as $activity_top_level => $_custom_fields_criteria)
				{
					if (isset($_custom_fields_criteria['choice']) && isset($activity_ids[$activity_top_level]))
					{
						$custom_fields_criteria = array_merge($custom_fields_criteria, $_custom_fields_criteria['choice']);
					}
				}
				unset($activity_top_level);

				foreach ($custom_fields_criteria as $criteria)
				{
					$sub_condition = array();
					$location_id = $criteria['location_id'];

					if (!isset($custom_fields_arr[$location_id]))
					{
						$custom_fields = $GLOBALS['phpgw']->custom_fields->find2($location_id, 0, '', 'ASC', 'attrib_sort', true, true);
						$custom_fields_arr[$location_id] = $custom_fields;
					}

					$field_name = $custom_fields[$criteria['attribute_id']]['name'];
					$field_value = isset($criteria['choice_id']) ? $criteria['choice_id'] : false;

					if (!$field_value)
					{
						continue;
					}

//					$sub_condition[] = " json_representation @> '{\"schema_location\":$location_id}'";

					if ($custom_fields[$criteria['attribute_id']]['datatype'] == 'CH') // in array
					{
						/**
						 * Note: JSONB operator '?' is troublesome: convert to '~@'
						 * CREATE OPERATOR ~@ (LEFTARG = jsonb, RIGHTARG = text, PROCEDURE = jsonb_exists);
						 * CREATE OPERATOR ~@| (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_any);
						 * CREATE OPERATOR ~@& (LEFTARG = jsonb, RIGHTARG = text[], PROCEDURE = jsonb_exists_all);
						 */
						$sub_condition[] = " json_representation #> '{{$location_id},{$field_name}}' ~@ '$field_value'";
					}
					else // discrete value
					{
						$sub_condition[] = " json_representation #> '{{$location_id},{$field_name}}' = '\"$field_value\"'";
					}


					$custom_condition_arr[$criteria['cat_id']][] = '(' . implode(' AND ', $sub_condition) . ')';
				}
			}

			$_conditions = array();
			if (isset($filters['custom_fields_criteria']) && is_array($filters['custom_fields_criteria']))
			{
				foreach ($filters['custom_fields_criteria'] as $activity_top_level => $_custom_fields_criteria)
				{
					if (!isset($activity_ids[$activity_top_level]))
					{
						continue;
					}

					if (isset($custom_condition_arr[$activity_top_level]))
					{
						$_conditions[] = '(' . $conditions . ' AND (bb_resource.activity_id IN (' . implode(',', $activity_ids[$activity_top_level]) . ') AND' . implode(' OR ', $custom_condition_arr[$activity_top_level]) . '))';
					}
					else
					{
						$_conditions[] = '(' . $conditions . ' AND bb_resource.activity_id IN (' . implode(',', $activity_ids[$activity_top_level]) . '))';
					}
					$activity_ids[$activity_top_level] = array();
				}
				unset($activity_top_level);
			}
			$__activity_ids = array();
			foreach ($activity_ids as $activity_top_level => $_activity_ids)
			{
				$__activity_ids = array_merge($__activity_ids, $_activity_ids);
			}

			if ($__activity_ids)
			{
				$_conditions[] = '(' . $conditions . ' AND bb_resource.activity_id IN (' . implode(',', $__activity_ids) . '))';
			}

			if (!$_conditions)
			{
				$_conditions[] = $conditions;
			}

			$conditions = implode(' OR ', $_conditions);
//			_debug_array($conditions);
			return $conditions;
		}

		function add_building( $resource_id, $building_id )
		{

			if (!$resource_id || !$building_id)
			{
				return false;
			}

			//check for duplicate
			$this->db->query("SELECT resource_id FROM bb_building_resource " .
				"WHERE resource_id = {$resource_id} AND building_id = {$building_id}", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				return false;
			}
			else
			{
				return $this->db->query("INSERT INTO bb_building_resource (resource_id, building_id)"
						. " VALUES ({$resource_id}, {$building_id})", __LINE__, __FILE__);
			}
		}

		function remove_building( $resource_id, $building_id )
		{

			if (!$resource_id || !$building_id)
			{
				return false;
			}

			$this->db->query("SELECT resource_id FROM bb_building_resource " .
				"WHERE resource_id = {$resource_id} AND building_id = {$building_id}", __LINE__, __FILE__);
			if ($this->db->next_record())
			{
				return $this->db->query("DELETE FROM bb_building_resource " .
						"WHERE resource_id = {$resource_id} AND building_id = {$building_id}", __LINE__, __FILE__);
			}
			else
			{
				return false;
			}
		}

		function get_seasons( $resource_id )
		{
			$values = array();
			if (!$resource_id)
			{
				return $values;
			}

			$sql = "SELECT bb_season.*, bb_building.name as building_name FROM bb_season"
				. " JOIN bb_building ON bb_season.building_id = bb_building.id"
				. " JOIN bb_season_resource ON bb_season_resource.season_id = bb_season.id"
				. " WHERE bb_season.active = 1"
				. " AND bb_season_resource.resource_id = " . (int)$resource_id;

			$this->db->query($sql, __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' => $this->db->f('id'),
					'name' => $this->db->f('building_name') . '::' . $this->db->f('name')
					);
			}

			return $values;
		}
		function get_e_locks( $resource_id )
		{
			$values = array();

			$this->db->query("SELECT * FROM bb_resource_e_lock " .
				"WHERE resource_id = {$resource_id}", __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array(
					'resource_id' => $this->db->f('resource_id'),
					'e_lock_system_id' => $this->db->f('e_lock_system_id'),
					'e_lock_resource_id' => $this->db->f('e_lock_resource_id'),
					'e_lock_name' => $this->db->f('e_lock_name',true),
					'access_code_format' => $this->db->f('access_code_format',true),
					'active' => $this->db->f('active'),
					'modified_on' => $this->db->f('modified_on'),
					'modified_by' => $this->db->f('modified_by'),
				);
			}

			return array(
				'results' => $values,
				'total_records' => count($values),
				'start' => 0,
				'sort' => '',
				'dir' => ''
			);
		}

		function add_e_lock( $resource_id, $e_lock_system_id, $e_lock_resource_id, $e_lock_name = '', $access_code_format = '' )
		{
			$ret = 0;
			if (!$resource_id || !$e_lock_system_id || !$e_lock_resource_id)
			{
				return false;
			}

			$insert_update[] = array
			(
				1	=> array
				(
					'value'	=> $this->db->db_addslashes(substr($e_lock_name, 0, 20)),
					'type'	=> PDO::PARAM_STR
				),
				2	=> array
				(
					'value'	=> $this->db->db_addslashes($access_code_format),
					'type'	=> PDO::PARAM_STR
				),
				3	=> array
				(
					'value'	=> date($this->db->datetime_format()),
					'type'	=> PDO::PARAM_STR
				),
				4	=> array
				(
					'value'	=> (int)$GLOBALS['phpgw_info']['user']['account_id'],
					'type'	=> PDO::PARAM_INT
				),
				5	=> array
				(
					'value'	=> $resource_id,
					'type'	=> PDO::PARAM_INT
				),
				6	=> array
				(
					'value'	=> $e_lock_system_id,
					'type'	=> PDO::PARAM_INT
				),
				7	=> array
				(
					'value'	=> $e_lock_resource_id,
					'type'	=> PDO::PARAM_INT
				)
			);
			//check for duplicate

			$sql = "SELECT resource_id FROM bb_resource_e_lock"
				. " WHERE resource_id = ? AND e_lock_system_id = ? AND e_lock_resource_id = ?";
			$condition =  array((int)$resource_id, (int)$e_lock_system_id,  (int)$e_lock_resource_id);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$update_sql = "UPDATE bb_resource_e_lock SET e_lock_name = ?, access_code_format = ?, modified_on = ?, modified_by = ? WHERE resource_id = ? AND e_lock_system_id = ? AND e_lock_resource_id = ?";
				if( $this->db->insert($update_sql, $insert_update, __LINE__, __FILE__))
				{
					$ret = 2;
				}
			}
			else
			{
				$add_sql = "INSERT INTO bb_resource_e_lock (e_lock_name, access_code_format, modified_on, modified_by, resource_id, e_lock_system_id, e_lock_resource_id)"
					. " VALUES (?, ?, ?, ?, ? ,? ,?)";
				if( $this->db->insert($add_sql, $insert_update, __LINE__, __FILE__))
				{
					$ret = 1;
				}
			}

			return $ret;
		}

		function remove_e_lock( $resource_id, $e_lock_system_id, $e_lock_resource_id)
		{
			if (!$resource_id || !$e_lock_system_id || !$e_lock_resource_id)
			{
				return false;
			}

			$delete_sql = "DELETE FROM bb_resource_e_lock WHERE resource_id = ? AND e_lock_system_id = ? AND e_lock_resource_id = ?";
			$delete = array();
			$delete[] = array
			(
				1	=> array
				(
					'value'	=> $resource_id,
					'type'	=> PDO::PARAM_INT
				),
				2	=> array
				(
					'value'	=> $e_lock_system_id,
					'type'	=> PDO::PARAM_INT
				),
				3	=> array
				(
					'value'	=> $e_lock_resource_id,
					'type'	=> PDO::PARAM_INT
				)
			);

			return $this->db->delete($delete_sql, $delete, __LINE__, __FILE__);
		}


		function get_paricipant_limit( $resource, $check_current = false)
		{

			$resource_ids = array(-1); // in case of empty: don't  break the query
			if (is_array($resource))
			{
				foreach ($resource as $entry)
				{
					if(is_array($entry) && !empty($entry['id']))
					{
						$resource_ids[] = $entry['id'];
					}
					else if ($entry)
					{
						$resource_ids[] = $entry;
					}
				}
			}
			else if($resource)
			{
				$resource_ids[] = $resource;
			}

			$order_menthod = 'ORDER BY from_ DESC';
			$filter = 'resource_id IN (' . implode(',', $resource_ids) . ')';

			if($check_current)
			{
				$filter .= " AND from_ < '" . date($this->db->date_format()) . "'";
			}
			$group_menthod = 'GROUP BY id, resource_id, from_, quantity, modified_on, modified_by';

			$values = array();

			$this->db->query("SELECT id, resource_id, from_,  max(quantity) AS quantity, modified_on, modified_by  FROM bb_participant_limit " .
				"WHERE {$filter} {$group_menthod} {$order_menthod}", __LINE__, __FILE__);

			while ($this->db->next_record())
			{
				$values[] = array(
					'id' => $this->db->f('id'),
					'resource_id' => $this->db->f('resource_id'),
					'from_' => $this->db->f('from_'),
					'quantity' => $this->db->f('quantity'),
					'modified_on' => $this->db->f('modified_on'),
					'modified_by' => $this->db->f('modified_by'),
				);
			}

			return array(
				'results' => $values,
				'total_records' => count($values),
				'start' => 0,
				'sort' => '',
				'dir' => ''
			);
		}

		function add_paricipant_limit( $resource_id, $_limit_from, $limit_quantity )
		{
			$ret = 0;
			if (!$resource_id || !$_limit_from)
			{
				return false;
			}

			$limit_from = date($this->db->date_format(),$_limit_from);

			if(!$limit_quantity)
			{
				$delete_sql = "DELETE FROM bb_participant_limit WHERE resource_id = ? AND from_ = ?";
				$delete = array();
				$delete[] = array
				(
					1	=> array
					(
						'value'	=> $resource_id,
						'type'	=> PDO::PARAM_INT
					),
					2	=> array
					(
						'value'	=> $limit_from,
						'type'	=> PDO::PARAM_STR
					)
				);

				$this->db->delete($delete_sql, $delete, __LINE__, __FILE__);
				return 2;
			}

			$insert_update[] = array
			(
				1	=> array
				(
					'value'	=> $limit_quantity,
					'type'	=> PDO::PARAM_INT
				),
				2	=> array
				(
					'value'	=> date($this->db->datetime_format()),
					'type'	=> PDO::PARAM_STR
				),
				3	=> array
				(
					'value'	=> (int)$GLOBALS['phpgw_info']['user']['account_id'],
					'type'	=> PDO::PARAM_INT
				),
				4	=> array
				(
					'value'	=> $resource_id,
					'type'	=> PDO::PARAM_INT
				),
				5	=> array
				(
					'value'	=> $limit_from,
					'type'	=> PDO::PARAM_STR
				),
			);
			//check for duplicate

			$sql = "SELECT resource_id FROM bb_participant_limit"
				. " WHERE resource_id = ? AND from_ = ?";
			$condition =  array((int)$resource_id, $limit_from);

			$this->db->select($sql, $condition, __LINE__, __FILE__);

			if ($this->db->next_record())
			{
				$update_sql = "UPDATE bb_participant_limit SET quantity = ?, modified_on = ?, modified_by = ? WHERE resource_id = ? AND from_ = ?";
				if( $this->db->insert($update_sql, $insert_update, __LINE__, __FILE__))
				{
					$ret = 2;
				}
			}
			else
			{
				$add_sql = "INSERT INTO bb_participant_limit (quantity, modified_on, modified_by, resource_id, from_)"
					. " VALUES (?, ?, ?, ?, ?)";
				if( $this->db->insert($add_sql, $insert_update, __LINE__, __FILE__))
				{
					$ret = 1;
				}
			}

			return $ret;
		}
	}