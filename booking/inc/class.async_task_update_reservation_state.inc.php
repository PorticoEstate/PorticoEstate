<?php
	phpgw::import_class('booking.async_task');
	phpgw::import_class('booking.socompleted_reservation');
	phpgw::import_class('phpgwapi.datetime');


	class booking_async_task_update_reservation_state extends booking_async_task
	{

		private $soapplication, $sopurchase_order, $update_reservation_time, $activate_application_articles;

		public function __construct()
		{
			parent::__construct();
			$this->soapplication	 = CreateObject('booking.soapplication');
			$this->sopurchase_order	 = createObject('booking.sopurchase_order');
			$config					 = CreateObject('phpgwapi.config', 'booking')->read();

			$this->activate_application_articles = !empty($config['activate_application_articles']) ? true : false;

			$billing_delay = !empty($config['billing_delay']) ? (int) $config['billing_delay']  : 0;
			$this->update_reservation_time = date('Y-m-d');

			if($billing_delay)
			{
				$_finnish_datestamp = time();
				for ($i = 1; $i < 16; $i++)
				{
					$finnish_datestamp	 = $_finnish_datestamp - (86400 * $i);
					$working_days	 = phpgwapi_datetime::get_working_days( $finnish_datestamp, $_finnish_datestamp);
					if ($working_days == $billing_delay)
					{
						$this->update_reservation_time = date('Y-m-d', $finnish_datestamp) . ' 10:00:00';
						break;
					}
				}
			}
		}

		public function get_default_times()
		{
			return array( 'hour' => '*/1');
		}

		public function run( $options = array() )
		{
			$db = & $GLOBALS['phpgw']->db;

			$reservation_types = array(
//				'booking',
				'event',
				'allocation'
			);

			$completed_so = CreateObject('booking.socompleted_reservation');

			foreach ($reservation_types as $reservation_type)
			{
				$bo = CreateObject('booking.bo' . $reservation_type);

				$expired = $bo->find_expired($this->update_reservation_time);

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
						$orders = $completed_so->find_expired_orders($reservation_type, $reservation['id'], $this->update_reservation_time);

						/**
						 * For vipps kan det være flere krav, for etterfakturering vil det være ett
						 */
						foreach ($orders as $order_id)
						{
							$this->add_payment($order_id);
							$order = $this->sopurchase_order->get_single_purchase_order($order_id);
							$_reservation = $bo->read_single($reservation['id']);

							if($this->activate_application_articles && (float)$_reservation['cost'] != (float)$order['sum'])
							{
								$_reservation['cost'] = $order['sum'];
								$this->add_cost_history($_reservation, 'update from order', $order['sum']);
								$bo->update($_reservation);
							}
						}
					}

					$bo->complete_expired($expired['results']);
				}

				$db->transaction_commit();
			}
		}

		private function add_payment( int $order_id )
		{
			$this->soapplication->add_payment($order_id, 'local_invoice', 'live', 2);
		}

		private function add_cost_history( &$reservation, $comment = '', $cost = '0.00' )
		{
			if (!$comment)
			{
				$comment = lang('cost is set');
			}

			$reservation['costs'][] = array(
				'time' => 'now',
				'author' => 'Cron-job',
				'comment' => $comment,
				'cost' => $cost
			);
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
