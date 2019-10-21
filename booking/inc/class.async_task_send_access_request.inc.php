<?php
	phpgw::import_class('phpgwapi.datetime');
	phpgw::import_class('booking.async_task');

	class booking_async_task_send_access_request extends booking_async_task
	{

		private $account, $config;

		public function __construct()
		{
			$this->account	 = $GLOBALS['phpgw_info']['user']['account_id'];
			$this->config	 = CreateObject('phpgwapi.config', 'booking')->read();
		}

		public function get_default_times()
		{
			return array('min'	 => '*', 'hour'	 => '*', 'dow'	 => '*', 'day'	 => '*', 'month'	 => '*',
				'year'	 => '*');
		}

		public function run( $options = array() )
		{

			$request_method = !empty($this->config['e_lock_request_method']) ? $this->config['e_lock_request_method'] : 'Stavanger_e_lock.php';

			if (!$request_method)
			{
				throw new LogicException('request_method not chosen');
			}

			$file = PHPGW_SERVER_ROOT . "/booking/inc/custom/default/{$request_method}";

			if (!is_file($file))
			{
				throw new LogicException("request method \"{$request_method}\" not available");
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

			$stages = array(
				0	 => 60 * 60 * 3, // 3 hours : send SMS and email as reminder
				1	 => 60 * 10, // 10 minutes : request access
				2	 => 60 * 5, // 5 minutes : get request status
			);

			$so_resource = CreateObject('booking.soresource');

			//SMS
			$sms_service = CreateObject('sms.sms');

			foreach ($stages as $stage => $time_ahead)
			{
				foreach ($reservation_types as $reservation_type)
				{
					$bo = CreateObject('booking.bo' . $reservation_type);

					$_stage = $stage;

					$request_access = $bo->find_request_access($_stage, $time_ahead);

					$_stage ++;

					if (!is_array($request_access) || !isset($request_access['results']))
					{
						continue;
					}

					$db->transaction_begin();

					if (count($request_access['results']) > 0)
					{
						foreach ($request_access['results'] as $reservation)
						{

							$resources = $so_resource->read(array('filters'	 => array('where' => 'bb_resource.id IN(' . implode(', ', $reservation['resources']) . ')'),
								'results'	 => 100));

							foreach ($resources['results'] as $resource)
							{
								if (!$resource['e_lock_resource_id'])
								{
									continue;
								}

								if ($stage == 0)
								{
									/**
									 * send SMS
									 */
									$sms_text = "Hei {$reservation['contact_name']}\n "
										. "Du har fått tilgang til {$resource['name']} i tidsrommet {$reservation['from_']} - {$reservation['to_']}";

									try
									{
										$sms_res = $sms_service->websend2pv($this->account, $reservation['contact_phone'], $sms_text);
									}
									catch (Exception $ex)
									{
										//implement me
										$this->log('sms_error', $ex->getMessage());
									}

									if (!empty($sms_res[0][0]))
									{
										$comment = 'Melding om tilgang er sendt til ' . $reservation['contact_phone'];
										$bo->add_single_comment($reservation['id'], $comment);
									}

									/**
									 * send email
									 */
									$this->send_mailnotification($reservation['contact_email'], 'Melding om tilgang', nl2br($sms_text));

									$this->log('sms_tekst', $sms_text);
								}
								else if ($stage == 1)
								{
									/**
									 * send request
									 */
									$post_data = array
										(
										'desc'	 => $reservation['contact_name'],
										'email'	 => $reservation['contact_email'],
										'from'	 => date('Y-m-d\TH:i:s.v', phpgwapi_datetime::user_localtime()) . 'Z',
										'mobile' => $reservation['contact_phone'],
										'resid'	 => $resource['e_lock_resource_id'],
										'system' => $resource['e_lock_system_id'],
										'to'	 => date('Y-m-d\TH:i:s.v', strtotime($reservation['to_'])) . 'Z',
									);

									$http_code = $e_lock_integration->resources_create($post_data);

									//							_debug_array($http_code);

									$log_data = _debug_array($post_data, false);
									$this->log('post_data', $log_data);
									$this->log('http_code', $http_code);
								}
								else if ($stage == 2)
								{
									/**
									 * Get status
									 */
									$get_data = array
										(
										'resid'		 => $resource['e_lock_resource_id'],
										'system'	 => $resource['e_lock_system_id'],
										'reserved'	 => 1
									);

									$status_arr = $e_lock_integration->get_status($get_data);

									$log_data = _debug_array($get_data, false);
									$this->log('get_data', $log_data);

									$found_reservation = false;
									foreach ($status_arr as $status)
									{
										if ($status['mobile'] == $reservation['contact_phone'])
										{
											$found_reservation	 = true;
											$request_from		 = strtotime($reservation['from_']);// - phpgwapi_datetime::user_timezone();
											$status_from		 = strtotime($status['from']);
											$status_to			 = strtotime($status['to']);
											if ($request_from > $status_from && $request_from <= $status_to)
											{
												/**
												 * send SMS
												 */
												$sms_text = "Hei {$reservation['contact_name']}\n "
													. "Du har fått tilgang til {$resource['name']} i tidsrommet {$reservation['from_']} - {$reservation['to_']}.\n "
													. "Koden er: {$status['key']}";

												try
												{
													$sms_res = $sms_service->websend2pv($this->account, $reservation['contact_phone'], $sms_text);
												}
												catch (Exception $ex)
												{
													$this->log('sms_error', $ex->getMessage());
												}

												if (!empty($sms_res[0][0]))
												{
													$comment = 'Melding om tilgang og kode er sendt til ' . $reservation['contact_phone'];
													$bo->add_single_comment($reservation['id'], $comment);
												}

												/**
												 * send email
												 */
												$this->send_mailnotification($reservation['contact_email'], 'Melding om tilgang', nl2br($sms_text));

												$this->log('sms_tekst', $sms_text);
											}
										}
										/**
										 * Implement me:
										 * look for contact_phone, and send email/sms with key
										 */
									}

									if(!$found_reservation)
									{
										$error_msg = "Fann ikkje reservasjonen i adgangskontrollen";
										$sms_res = $sms_service->websend2pv($this->account, $reservation['contact_phone'], $error_msg);
										$this->send_mailnotification($reservation['contact_email'], 'Melding om tilgang', nl2br($error_msg));
									}
								}
							}
						}

						$bo->complete_request_access($request_access['results'], $_stage);
					}

					$db->transaction_commit();
				}
			}
		}

		private function send_mailnotification( $receiver, $subject, $body )
		{
			$send = CreateObject('phpgwapi.send');

			$from = isset($this->config['email_sender']) && $this->config['email_sender'] ? $this->config['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0)
			{
				return false;
			}

			if (strlen($receiver) > 0)
			{
				try
				{
					$send->msg('email', $receiver, $subject, $body, '', '', '', $from, 'AktivKommune', 'html');
				}
				catch (Exception $e)
				{
					// TODO: Inform user if something goes wrong
				}
			}
		}

		private function log( $what, $value = '' )
		{
			$GLOBALS['phpgw']->log->message(array(
				'text'	 => "what: %1, <br/>value: %2",
				'p1'	 => $what,
				'p2'	 => $value ? $value : ' ',
				'line'	 => __LINE__,
				'file'	 => __FILE__
			));
			$GLOBALS['phpgw']->log->commit();
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
