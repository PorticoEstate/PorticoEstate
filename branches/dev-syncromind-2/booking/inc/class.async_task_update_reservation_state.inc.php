<?php
	phpgw::import_class('booking.async_task');
	phpgw::import_class('booking.socompleted_reservation');

	class booking_async_task_update_reservation_state extends booking_async_task
	{

		public function run( $options = array() )
		{
			$db = & $GLOBALS['phpgw']->db;

			$reservation_types = array
				(
				'booking',
				'event',
				'allocation'
			);
			$completed_so = CreateObject('booking.socompleted_reservation');

			foreach ($reservation_types as $reservation_type)
			{
				$bo = CreateObject('booking.bo' . $reservation_type);

				$expired = $bo->find_expired();

				if (!is_array($expired) || !isset($expired['results']))
				{
					continue;
				}

				$db->transaction_begin();

				if (count($expired['results']) > 0)
				{
					foreach ($expired['results'] as $reservation)
					{
						$completed_so->create_from($reservation_type, $reservation);
					}

					$bo->complete_expired($expired['results']);
				}

				$db->transaction_commit();
			}
		}
	}
	/*
Begreper:
application  - Søknad
allocation   - tildeling
booking      - Booking
event        - Arrangementer
reservation  - reservasjon / betalingsgrunnlag

En Søknad (application) kan resultere i en tildeling(allocation) eller et Arrangement(event).
En tildeling(allocation) kan deles opp i flere Booking(booking).

Utgangspunktet for 'Klar for fakturering' ligger i tabellen 'bb_completed_reservation'

Denne (reservation) er satt sammen av tre ulike element-typer: tildeling(allocation), Booking(booking) og Arrangement(event)

En Booking referer til en tildeling (allocation_id).

For å produsere innholdet i bb_completed_reservation:

cron-job som starter i booking/inc/class.async_task_update_reservation_state.inc.php

async_task_update_reservation_state::run()

Uttrekket defineres her (pr type):

 booking_sobooking::find_expired();
 booking_soallocation::find_expired();
 booking_soevent::find_expired();

Oppgaven her blir da:

1) Alle kandidater for 'booking' skal faktureres (som før)
2) Alle kandidater for 'event' skal faktureres (som før)
3) kandidater for 'allocation' som ikke er referert til fra 'booking' skal faktureres (omarbeides)

*/
