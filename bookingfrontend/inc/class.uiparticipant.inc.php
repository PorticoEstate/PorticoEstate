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

			$participant					 = array();
			$participant['email']			 = null;
			$participant['phone']			 = null;
			$participant['reservation_type'] = $reservation_type;
			$participant['reservation_id']	 = $reservation_id;
			$errors							 = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$participant['phone']	 = phpgw::get_var('phone', 'int');
				$participant['email']	 = phpgw::get_var('email', 'email');
				$errors					 = $this->bo->validate($participant);
				if (!$errors)
				{
					$receipt = $this->bo->add($participant);
					$this->redirect(array('menuaction'		 => 'bookingfrontend.uiparticipant.add',
						'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id));
				}
			}
			$this->flash_form_errors($errors);

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

			$number_of_participants = $this->bo->get_number_of_participants($reservation_type, $reservation_id);

			$data = array
				(
				'number_of_participants' => $number_of_participants,
				'when'					 => $when,
				'phone'					 => $participant['phone'],
				'email'					 => $participant['email'],
				'name'					 => $reservation['name'],
				'form_action'			 => self::link(array('menuaction'		 => 'bookingfrontend.uiparticipant.add',
					'reservation_type'	 => $reservation_type, 'reservation_id'	 => $reservation_id)),
			);

			self::render_template_xsl('participant_edit', $data);
		}

		public function index()
		{
			phpgw::no_access();
		}
	}