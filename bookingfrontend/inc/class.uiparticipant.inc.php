<?php
	phpgw::import_class('booking.uiparticipant');
	phpgw::import_class('phpgwapi.datetime');

	class bookingfrontend_uiparticipant extends booking_uiparticipant
	{

		public $public_functions = array
			(
			'add' => true,
			'index' => true
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->resource_bo = CreateObject('booking.boresource');
			$this->group_bo = CreateObject('booking.bogroup');
			$this->module = "bookingfrontend";
		}

		public function add()
		{
			$config = CreateObject('phpgwapi.config', 'booking')->read();

			$reservation_type	 = phpgw::get_var('reservation_type');
			$reservation_id		 = phpgw::get_var('reservation_id', 'int');
			$register_type		 = phpgw::get_var('register_type');

			$participant					 = array();
			$participant['email']			 = null;
			$participant['phone']			 = null;
			$participant['quantity']		 = 1;
			$participant['reservation_type'] = $reservation_type;
			$participant['reservation_id']	 = $reservation_id;

			$reservation = createObject("booking.bo{$reservation_type}")->read_single($reservation_id);
			$resource_paricipant_limit_gross = CreateObject('booking.soresource')->get_paricipant_limit($reservation['resources'], true);

			$resources = $this->resource_bo->so->read(array('filters' => array('id' => $reservation['resources']),'sort' => 'name'));
			$res_names = array();
			foreach ($resources['results'] as $res)
			{
				$res_names[] = $res['name'];
			}

			$reservation['resource_info'] = join(', ', $res_names);

			if(!empty($reservation['group_id']))
			{
				$reservation['group'] = $this->group_bo->read_single($reservation['group_id']);
			}

			if(!empty($resource_paricipant_limit_gross['results'][0]['quantity']))
			{
				$resource_paricipant_limit = $resource_paricipant_limit_gross['results'][0]['quantity'];
			}

			if(!$reservation['participant_limit'])
			{
				$reservation['participant_limit'] = $resource_paricipant_limit ? $resource_paricipant_limit : (int)$config['participant_limit'];
			}

			$reservation['participant_limit'] = $reservation['participant_limit'] ? $reservation['participant_limit'] : (int)$config['participant_limit'];

			$interval	 = (new DateTime($reservation['from_']))->diff(new DateTime($reservation['to_']));
			$when		 = "";
			if ($interval->days > 0)
			{
				$when = pretty_timestamp($reservation['from_']) . ' - ' . pretty_timestamp($reservation['to_']);
			}
			else
			{
				$end	 = new DateTime($reservation['to_']);
				$when	 = pretty_timestamp($reservation['from_']) . ' - ' . $end->format('H:i');
			}

			$timezone	 = !empty($GLOBALS['phpgw_info']['user']['preferences']['common']['timezone']) ? $GLOBALS['phpgw_info']['user']['preferences']['common']['timezone'] : 'UTC';

			try
			{
				$DateTimeZone	 = new DateTimeZone($timezone);
			}
			catch (Exception $ex)
			{
				throw $ex;
			}

			$from = new DateTime(date('Y-m-d H:i:s', strtotime($reservation['from_'])),$DateTimeZone);
			$from->modify("-2 hour");
			$to = new DateTime(date('Y-m-d H:i:s', strtotime($reservation['to_'])),$DateTimeZone);
			$to->modify("+2 hour");

			$now =  new DateTime('now', $DateTimeZone);

			$enable_register_pre = $from > $now ? true : false;
			$enable_register_in	 = $from < $now && $to > $now ? true : false;

			if($enable_register_pre || $enable_register_in )
			{
				$enable_register_out = true;
			}

//			_debug_array($from);
//			_debug_array($now);
//			_debug_array($to);

			if($enable_register_pre || $enable_register_in || $enable_register_out)
			{
				$enable_register_form = true;
			}
			else
			{
				$enable_register_form = false;
			}

			$errors							 = array();
			$sms_error_message = '';
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $register_type && $enable_register_form)
			{
				$user_inputs = (array)phpgwapi_cache::system_get('bookingfrontendt', 'add_participant');
				$ip_address = phpgw::get_ip_address();
				$user_inputs[$ip_address][time()] = 1;

				/**
				 * 2 seconds limit
				 */
				$check_timestamp = time() - 2;

				$limit = 1;

				$number_of_submits = 0;

				foreach ($user_inputs as $_ip_address =>  &$timestamps)
				{
					foreach ($timestamps as $timestamp =>  $entry)
					{
						if($timestamp > $check_timestamp)
						{
							$number_of_submits ++;
						}
						else
						{
							unset($timestamps[$timestamp]);
						}
					}
				}

				phpgwapi_cache::system_set('bookingfrontendt', 'add_participant', $user_inputs);

				if($number_of_submits > $limit)
				{
					$errors = array('phone' =>'Number of submit is exceeded within timelimit');
				}
				else
				{
					$phone = phpgw::get_var('phone', 'string');
					$participant = $this->bo->get_previous_registration($reservation_type, $reservation_id, $phone, $register_type);
					$participant['register_type']	 = $register_type;
					$participant['phone']			 = $phone;
					$participant['email']			 = phpgw::get_var('email', 'email');

					if($register_type == 'register_in' && phpgw::get_var('quantity', 'int') < $participant['quantity'])
					{
						$participant['quantity'] = phpgw::get_var('quantity', 'int');
					}
					$participant['quantity']		 = $participant['quantity'] ? $participant['quantity'] : phpgw::get_var('quantity', 'int');
					$participant['reservation_type'] = $reservation_type;
					$participant['reservation_id']	 = $reservation_id;

					$errors							 = $this->bo->validate($participant);
				}

				$number_of_participants = $this->bo->get_number_of_participants($reservation_type, $reservation_id);
				$number_of_participants_registered_in = $this->bo->get_number_of_participants($reservation_type, $reservation_id, true);

				if( !empty($reservation['participant_limit']) && $participant['quantity'])
				{
					if($register_type == 'register_pre')
					{
						if(($number_of_participants  + $participant['quantity']) > (int) $reservation['participant_limit'])
						{
							$sms_error_message = "Det gikk ikke: antall er begrenset til {$reservation['participant_limit']}.";
							$errors = array('quantity' => $sms_error_message);
						}
						else if($participant['id'])
						{
							$sms_error_message = "Det gikk ikke: du er allerede innregistrert med antall {$participant['quantity']}.\n";
							$sms_error_message .= "Du kan eventuelt forsøke å registrere deg ut - og deretter inn igjen med nytt antall.";
							$errors = array('quantity' => $sms_error_message);
						}
					}
					else if($register_type == 'register_in')
					{
						if(($participant['id'] && ($number_of_participants_registered_in  + $participant['quantity']) > (int) $reservation['participant_limit'])
							|| (!$participant['id'] && ($number_of_participants  + $participant['quantity']) > (int) $reservation['participant_limit']))
						{
							$sms_error_message = "Det gikk ikke: antall er begrenset til {$reservation['participant_limit']}.";
							$errors = array('quantity' => $sms_error_message);
						}
					}
				}

				if (!$errors)
				{
					if(!empty($participant['id']))
					{
						$participant['from_'] = $participant['from_'] ? $participant['from_'] : null;
						$participant['to_'] = $participant['to_'] ? $participant['to_'] : null;
						$receipt = $this->bo->update($participant);
					}
					else if(empty($participant['id']) && $register_type == 'register_out')
					{
						$sms_error_message = "Du har forsøkt å avbestille tid - men nummeret ditt er ikke registrert, og kan ikke benyttes for avbestilling."
						. "\n Begrensning: {$reservation['participant_limit']}."
						. "\n Totalt antall påmeldt: {$number_of_participants}";
					}
					else
					{
						$receipt = $this->bo->add($participant);
					}

//					$participant_id = $receipt['id'];
//					$external_site_address = !empty($config['external_site_address'])? $config['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];
//
//					// Hack..
//					if(!preg_match('/^http/', $external_site_address))
//					{
//						$external_site_address = "http:/{$external_site_address}";
//					}
//
//					$participant_registration_link = $external_site_address
//						. "/bookingfrontend/?menuaction=bookingfrontend.uiparticipant.add"
//						. "&phone={$phone}"
//						. "&quantity={$participant['quantity']}"
//						. "&reservation_type={$participant['reservation_type']}"
//						. "&reservation_id={$participant['reservation_id']}";


					switch ($reservation_type)
					{
						case 'event':
							$lang_reservation_type = strtolower(lang('event'));
							break;
						default:
							$lang_reservation_type = 'arrangement/aktivitet';
							break;
					}

					switch ($register_type)
					{
						case 'register_pre':
							$sms_text = "Du er forhåndspåmeldt med {$participant['quantity']} deltaker(e) for {$lang_reservation_type} '{$reservation['name']}' som avholdes i tidsrommet {$when}.\n"
								. "Du må registrere fremmøte når du møter ved arrangementet\n";
							break;
						case 'register_in':
							$sms_text = "Du har registrert fremmøte for {$participant['quantity']} deltaker(e) for {$lang_reservation_type} '{$reservation['name']}' som avholdes i tidsrommet {$when}.\n";
			//				$sms_text .= "Du kan frigjøre plassen(e) ved å melde deg ut når du forlater arrangementet ";
							break;
						case 'register_out':
							if($enable_register_pre)
							{
								$sms_text = "Du har registrert at du avbestiller fra {$lang_reservation_type} '{$reservation['name']}' som avholdes i tidsrommet {$when} med {$participant['quantity']} deltaker(e)";
							}
							else
							{
								$sms_text = "Du har registrert at du forlater et {$lang_reservation_type} '{$reservation['name']}' som avholdes i tidsrommet {$when} med {$participant['quantity']} deltaker(e)";
							}
							break;

						default:
							$sms_text = "Hei.\n "
								. "Du har registrert {$participant['quantity']} deltaker(e) for {$lang_reservation_type} '{$reservation['name']}' som avholdes i tidsrommet {$when}";
							break;
					}

					/**
					 * disable SMS for now
					 */

					if($config['participant_limit_sms'])
					{
						$sms_text = $sms_error_message ? $sms_error_message : $sms_text;
						try
						{
							$sms_service = CreateObject('sms.sms');
							$sms_res = $sms_service->websend2pv($this->account, $participant['phone'], "Hei.\n{$sms_text} \nDenne meldingen kan ikke besvares");
						}
						catch (Exception $ex)
						{
							//implement me
							$this->log('sms_error', $ex->getMessage());
						}
					}

					phpgwapi_cache::message_set($sms_text);

					$this->redirect(array('menuaction'	=> 'bookingfrontend.uiparticipant.add',
					'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id));
				}
			}
			if($enable_register_pre)
			{
				$lang_register_out = 'Avbestill';
			}
			else
			{
				$lang_register_out = lang('Register out') . ' / Avbestill';
			}


			if($sms_error_message && $participant['phone'] && $config['participant_limit_sms'])
			{
				try
				{
					$sms_service = CreateObject('sms.sms');
					$sms_res = $sms_service->websend2pv($this->account, $participant['phone'], "Hei.\n{$sms_error_message} \nDenne meldingen kan ikke besvares");
				}
				catch (Exception $ex)
				{
					//implement me
					$this->log('sms_error', $ex->getMessage());
				}

			}

			$this->flash_form_errors($errors);

			$number_of_participants = $this->bo->get_number_of_participants($reservation_type, $reservation_id);
			$number_of_participants_registered_in = $this->bo->get_number_of_participants($reservation_type, $reservation_id, true);

			$participant_limit		 = !empty($reservation['participant_limit']) ? $reservation['participant_limit'] : 0;

			if($participant_limit && ($number_of_participants >= $participant_limit))
			{
				$enable_register_pre = null;
				$enable_register_in = null;
				if($number_of_participants_registered_in < $number_of_participants)
				{
					$enable_register_in = $enable_register_in ? $enable_register_in : null;
				}
			}

			$name = '';

			if((array_key_exists('is_public', $reservation) && $reservation['is_public']))
			{
				$name = $reservation['name'];
			}
			else if(!array_key_exists('is_public', $reservation) && !empty($reservation['name']))
			{
				$name = $reservation['name'];
			}

			$data = array
			(
				'participanttext'		 => !empty($config['participanttext'])? $config['participanttext'] :'',
				'enable_register_pre'	 => $enable_register_pre,
				'enable_register_in'	 => $enable_register_in,
				'enable_register_out'	 => $enable_register_out,
				'enable_register_form'	 => $enable_register_form,
				'number_of_participants' => $number_of_participants,
				'lang_register_out'		 => $lang_register_out,
				'when'					 => $when,
				'phone'					 => $participant['phone'],
				'email'					 => $participant['email'],
				'quantity'				 => $participant['quantity'],
				'name'					 => $name,
				'reservation'			 => $reservation,
				'participant_limit'		 => $participant_limit,
				'form_action'			 => self::link(array('menuaction'		 => 'bookingfrontend.uiparticipant.add',
					'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id)),
			);

			if($enable_register_form)
			{
				phpgwapi_jquery::init_intl_tel_input('phone');
			}

			self::add_javascript('bookingfrontend', 'base', 'participant_edit.js');
			self::render_template_xsl('participant_edit', $data);
		}


		public function index()
		{
			if(!CreateObject('bookingfrontend.bouser')->is_logged_in())
			{
				return array();
			}


			$_REQUEST['filter_reservation_id'] = phpgw::get_var('filter_reservation_id', 'int', 'REQUEST', -1);
			$participants = $this->bo->read();

			$data = array('results' => array(), 'total_records' => 0, 'start' => 0, 'sort' => $participants['sort'], 'dir' => $participants['dir']);

			foreach ($participants['results'] as $participant)
			{
				if($participant['to_'])
				{
					continue;
				}
				$data['results'][] = $participant;
				$data['total_records'] += 1;
			}

			$results = $this->jquery_results($data);

			return $results;
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