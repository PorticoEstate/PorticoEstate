<?php
	phpgw::import_class('booking.async_task');

	class booking_async_task_delete_participants extends booking_async_task
	{

		public function get_default_times()
		{
			return array( 'day' => '*/1');
		}

		public function run( $options = array() )
		{
			$reservation_types = array
				(
				'booking',
				'event',
				'allocation'
			);
			
			$age_days = 28;

			$participant_so = CreateObject('booking.soparticipant');

			foreach ($reservation_types as $reservation_type)
			{
				$participant_so->delete_from_completed($reservation_type, $age_days);

			}
		}
	}
