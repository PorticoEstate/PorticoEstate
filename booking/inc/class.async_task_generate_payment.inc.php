<?php
	phpgw::import_class('booking.async_task');

	class booking_async_task_generate_payment extends booking_async_task
	{
		private $soapplication;

		public function __construct()
		{
			parent::__construct();
			$this->soapplication	 = CreateObject('booking.soapplication');

		}

		public function get_default_times()
		{
			return array( 'hour' => '*/1');
		}

		/**
		 * 
		 * disabled...
		 */
		public function run( $options = array() )
		{
			return;

			$db = & $GLOBALS['phpgw']->db;

			$reservation_types = array(
				'event',
				'allocation'
			);

			foreach ($reservation_types as $reservation_type)
			{
				$so = CreateObject('booking.so' . $reservation_type);

				$orders = $so->find_expired_orders();

				if (empty($orders))
				{
					continue;
				}

				$db->transaction_begin();

				foreach ($orders as $order_id)
				{
					$this->add_payment($order_id);
				}

				$db->transaction_commit();
			}
		}

		private function add_payment( int $order_id )
		{
			$this->soapplication->add_payment($order_id, 'local_invoice', 'live', 2);
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
