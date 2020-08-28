<?php
	phpgw::import_class('booking.uiparticipant');

	class bookingfrontend_uiparticipant extends booking_uiparticipant
	{

		public $public_functions = array
			(
			'add' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}

		public function add()
		{
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

			$errors							 = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && $register_type)
			{
				$user_inputs = (array)phpgwapi_cache::system_get('bookingfrontendt', 'add_participant');
				$ip_address = phpgw::get_ip_address();
				$user_inputs[$ip_address][time()] = 1;

				/**
				 * 10 seconds limit
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

					$phone = phpgw::get_var('phone', 'int');
					$participant = $this->bo->get_previous_registration($reservation_type, $reservation_id, $phone, $register_type);
					$participant['register_type']	 = $register_type;
					$participant['phone']			 = $phone;
					$participant['email']			 = phpgw::get_var('email', 'email');
					$participant['quantity']		 = phpgw::get_var('quantity', 'int');
					$participant['reservation_type'] = $reservation_type;
					$participant['reservation_id']	 = $reservation_id;

					$errors							 = $this->bo->validate($participant);
				}

				if (!$errors)
				{
					if(!empty($participant['id']))
					{
						$participant['from_'] = $participant['from_'] ? $participant['from_'] : null;
						$participant['to_'] = $participant['to_'] ? $participant['to_'] : null;
						$receipt = $this->bo->update($participant);
					}
					else
					{
						$receipt = $this->bo->add($participant);
					}

					/**
					 * send SMS
					 */
					$sms_text = "Hei\n "
						. "Du har registrert {$participant['quantity']} deltaker(e) for {$reservation['name']} som avholdes i tidsrommet {$when}";

					try
					{
						$sms_service = CreateObject('sms.sms');
						$sms_res = $sms_service->websend2pv($this->account, $participant['phone'], $sms_text);
					}
					catch (Exception $ex)
					{
						//implement me
						$this->log('sms_error', $ex->getMessage());
					}

					phpgwapi_cache::message_set(lang('added') . ": {$participant['phone']}");

					$this->redirect(array('menuaction'	=> 'bookingfrontend.uiparticipant.add',
					'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id));
				}
			}
			$this->flash_form_errors($errors);


			$number_of_participants = $this->bo->get_number_of_participants($reservation_type, $reservation_id);

			$data = array
				(
				'number_of_participants' => $number_of_participants,
				'when'					 => $when,
				'phone'					 => $participant['phone'],
				'email'					 => $participant['email'],
				'quantity'				 => $participant['quantity'],
				'name'					 => $reservation['name'],
				'form_action'			 => self::link(array('menuaction'		 => 'bookingfrontend.uiparticipant.add',
					'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id)),
			);
			self::add_javascript('bookingfrontend', 'base', 'participant_edit.js');
			self::render_template_xsl('participant_edit', $data);
		}

		public function index()
		{
			phpgw::no_access();
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