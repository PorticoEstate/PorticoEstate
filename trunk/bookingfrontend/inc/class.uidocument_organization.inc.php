<?php
	phpgw::import_class('booking.uidocument_organization');

	class bookingfrontend_uidocument_organization extends booking_uidocument_organization
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