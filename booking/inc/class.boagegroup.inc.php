<?php
	phpgw::import_class('booking.bocommon_global_manager_authorized');

	class booking_boagegroup extends booking_bocommon_global_manager_authorized
	{

		function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('booking.soagegroup');
		}

		function fetch_age_groups( $top_level_activity = 0 )
		{
			$filters = array('active' => '1');
			if ($top_level_activity)
			{
				$filters['activity_id'] = $top_level_activity;
			}

			return $this->so->read(array('filters' => $filters, 'sort' => 'sort'));
		}

		// Extract agegroup info from _POST into $data
		public function extract_form_data( &$data )
		{
			$one_group_value_found = false;
			$groups_by_ref = array();

			$sexes = array('male' => 'female', 'female' => 'male');

			foreach ($sexes as $current_sex => $opposite_sex)
			{
				foreach ($_POST[$current_sex] as $group_id => $num)
				{
					$found = false;
					$num = (int)$num;
					$num = $num > 0 ? $num : null;

					if ($num && $num > 0)
					{
						$one_group_value_found = true;
					}

					foreach ($data['agegroups'] as &$group)
					{
						if ($group['agegroup_id'] == $group_id)
						{
							$found = true;
							$group[$current_sex] = $num;
							$groups_by_ref[$group_id] = &$group;
							break;
						}
					}

					if (!$found)
					{
						$data['agegroups'][] = array('agegroup_id' => $group_id, $current_sex => $num,
							$opposite_sex => null);
						$groups_by_ref[$group_id] = &$data['agegroups'][count($data['agegroups']) - 1];
					}
				}
			}

			if (!$one_group_value_found)
			{
				return $data;
			}

			foreach ($groups_by_ref as &$group)
			{
				foreach ($sexes as $current_sex)
				{
					if (!$group[$current_sex])
					{
						$group[$current_sex] = 0;
					}
				}
			}

			return $data;
		}
	}