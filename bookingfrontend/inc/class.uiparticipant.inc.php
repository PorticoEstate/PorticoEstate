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
			$reservation_type = phpgw::get_var('reservation_type');
			$reservation_id = phpgw::get_var('reservation_id', 'int');

			$reservation = createObject("booking.bo{$reservation_type}")->read_single($reservation_id);

			$data = array
			(
				'description' => $reservation['description'],
				'form_action' => self::link(array('menyaction' => 'bookingfrontend.uiparticipant.add',
					'reservation_type' => $reservation_type, 'reservation_id' => $reservation_id)),
			);

			self::render_template_xsl('participant_edit', $data);
		}

		public function index()
		{
			phpgw::no_access();
		}
	}