<?php
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
				'activity_id' => array('type' => 'int', 'required' => false),
				'organizations_ids' => array('type' => 'string'),
				'json_representation' => array('type' => 'json'),
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
				'buildings' => array('type' => 'int', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_building_resource',
						'key' => 'resource_id',
						'column' => 'building_id'
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
	}