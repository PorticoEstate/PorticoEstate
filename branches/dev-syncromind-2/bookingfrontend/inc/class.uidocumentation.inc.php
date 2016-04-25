<?php
	phpgw::import_class('booking.uidocumentation');

	class bookingfrontend_uidocumentation extends booking_uidocumentation
	{

		public $public_functions = array
			(
			'download' => true,
			'index' => true,
			'index_images' => true,
		);
		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}
	}