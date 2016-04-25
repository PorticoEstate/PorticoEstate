<?php
	phpgw::import_class('booking.uidocument_resource');

	class bookingfrontend_uidocument_resource extends booking_uidocument_resource
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