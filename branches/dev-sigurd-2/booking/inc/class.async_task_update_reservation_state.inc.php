<?php
	phpgw::import_class('booking.async_task');
	phpgw::import_class('booking.socompleted_reservation');
	
	class booking_async_task_update_reservation_state extends booking_async_task
	{
		public function run($options = array()) {
			$db = & $GLOBALS['phpgw']->db;
			
			$reservation_types = array('booking', 'event', 'allocation');
			$completed_so = CreateObject('booking.socompleted_reservation');
			
			foreach ($reservation_types as $reservation_type) {
				$bo = CreateObject('booking.bo'.$reservation_type);
				
				$expired = $bo->find_expired();

				if (!is_array($expired) || !isset($expired['total_records']) || $expired['total_records'] <= 0) { continue; }

				$db->transaction_begin();

				$bo->complete_expired();

				foreach ($expired['results'] as $reservation) {
					$completed_so->create_from($reservation_type, $reservation);
				}

				$db->transaction_commit();
			}
		}
	}