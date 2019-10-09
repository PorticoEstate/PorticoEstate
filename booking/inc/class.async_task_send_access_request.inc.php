<?php
	phpgw::import_class('booking.async_task');

	class booking_async_task_send_access_request extends booking_async_task
	{

		public function get_default_times()
		{
			return array('min'	 => '*', 'hour'	 => '*', 'dow'	 => '*', 'day'	 => '*', 'month'	 => '*',
				'year'	 => '*');
		}

		public function run( $options = array() )
		{

			$config = CreateObject('phpgwapi.config', 'booking')->read();

			$request_method = !empty($config['e_lock_request_method']) ? $config['e_lock_request_method'] : 'Stavanger_e_lock.php';

			if (!$request_method)
			{
				throw new LogicException('authentication_method not chosen');
			}

			$file = PHPGW_SERVER_ROOT . "/booking/inc/custom/default/{$request_method}";

			if (!is_file($file))
			{
				throw new LogicException("authentication method \"{$request_method}\" not available");
			}

			require_once $file;

			$e_lock_integration = new booking_e_lock_integration();

			$db = & $GLOBALS['phpgw']->db;

			$reservation_types = array
				(
//				'booking',
				'event',
//				'allocation'
			);

			$so_resource = CreateObject('booking.soresource');

			foreach ($reservation_types as $reservation_type)
			{
				$bo = CreateObject('booking.bo' . $reservation_type);

				$request_access = $bo->find_request_access();

				if (!is_array($request_access) || !isset($request_access['results']))
				{
					continue;
				}

				$db->transaction_begin();

				if (count($request_access['results']) > 0)
				{
					foreach ($request_access['results'] as $reservation)
					{

						$resources = $so_resource->read(array('filters' => array('where' => 'bb_resource.id IN(' . implode(', ', $reservation['resources']) . ')'),
							'results' => 100));

						foreach ($resources['results'] as $resource)
						{
							if (!$resource['e_lock_resource_id'])
							{
								continue;
							}
							/**
							 * send request
							 */
							$post_data = array
							(
								'desc'	 => $reservation['contact_name'],
								'email'	 => $reservation['contact_email'],
								'from'	 => date('Y-m-d\TH:i:s.v') . 'Z',
								'mobile' => $reservation['contact_phone'],
								'resid'	 => $resource['e_lock_resource_id'],
								'system' => $resource['e_lock_system_id'],
								'to'	 => date('Y-m-d\TH:i:s.v', strtotime($reservation['to_'])) . 'Z',
							);
							$ret = $e_lock_integration->resources_create($post_data);
							_debug_array($ret);
						}
					}

					$bo->complete_request_access($request_access['results']);
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
