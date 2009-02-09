<?php
	phpgw::import_class('booking.uicommon');

	class booking_uidashboard extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true,
		);

		public function __construct()
		{
            parent::__construct();
			self::set_active_menu('booking::dashboard');
		}

		public function index()
		{
			$data = array
			(
			);
			self::render_template('dashboard', $data);
		}
	}
