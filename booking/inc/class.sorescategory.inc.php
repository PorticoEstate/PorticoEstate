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

	}
