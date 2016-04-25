<?php
	phpgw::import_class('booking.uidocument_building');

	class bookingfrontend_uidocument_building extends booking_uidocument_building
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