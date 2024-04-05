<?php
	phpgw::import_class('booking.socommon');

	class booking_somassbooking extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_building', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'query' => true, 'required' => true),
				'homepage' => array('type' => 'string'),
				'description_json' => array('type' => 'json'),
				'phone' => array('type' => 'string'),
				'email' => array('type' => 'string'),
				'location_code' => array('type' => 'string', 'required' => false),
				'street' => array('type' => 'string', 'query' => true),
				'zip_code' => array('type' => 'string'),
				'district' => array('type' => 'string', 'query' => true),
				'city' => array('type' => 'string', 'query' => true),
				'active' => array('type' => 'int')
				)
			);
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

	}