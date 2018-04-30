<?php
	phpgw::import_class('booking.socommon');

	class booking_sorescategory extends booking_socommon
	{

		function __construct()
		{
			parent::__construct('bb_rescategory', array(
				'id' => array('type' => 'int'),
				'name' => array('type' => 'string', 'required' => true, 'query' => true),
				'active' => array('type' => 'int', 'required' => true),
				'activities' => array('type' => 'string', 'required' => true,
					'manytomany' => array(
						'table' => 'bb_rescategory_activity',
						'key' => 'rescategory_id',
						'column' => 'activity_id'
					)),
				)
			);
			$this->activity_so = CreateObject('booking.soactivity');
		}


		protected function doValidate( $entity, booking_errorstack $errors )
		{
			set_time_limit(300);

			if (count($errors) > 0)
			{
				// Basic validation failed
				return;
			}
			
			// Check that selected activities are on the top level, and that there is at least one activity
			$count_activities = 0;
			$top_level_activities = $this->activity_so->get_top_level();
			foreach ($entity['activities'] as $activity_id)
			{
				$count_activities++;
				$verified = 0;
				foreach ($top_level_activities as $tlactivity)
				{
					if ($tlactivity['id'] == $activity_id)
					{
						$verified = 1;
						continue;
					}
				}
				if (!$verified)
				{
					$errors['activities'] = lang('Not a top level activity');
				}
			}
			if ($count_activities == 0)
			{
				$errors['activities'] = lang('At least one activity must be selected');
			}
		}


		/**
		 * Gets resource categories which belong to the given top level activities
		 *
		 * @param array activity_ids
		 * @return array resource categories
		 */
		function get_rescategories_by_activities($activity_ids = array())
		{
			$rescategories = array();
			if (count($activity_ids) == 0)
			{
				return $rescategories;
			}
			$sql = 'SELECT DISTINCT br.* FROM bb_rescategory br ' .
					'JOIN bb_rescategory_activity bra on bra.rescategory_id=br.id ' .
					'JOIN bb_activity ba on bra.activity_id=ba.id ' .
					'WHERE br.active=1 and ba.parent_id is NULL and bra.activity_id in (' . implode(',', $activity_ids) . ')' .
					'ORDER BY br.name';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$rescategories[] = array('id' => $this->db->f('id'), 'name' => $this->db->f('name'));
			}
			return $rescategories;
		}

	}
