<?php
	phpgw::import_class('booking.uidocument_application');

	class bookingfrontend_uidocument_application extends booking_uidocument_application
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

		public function index()
		{
//			if (phpgw::get_var('phpgw_return_as') == 'json')
//			{
//				return $this->query();
//			}

			phpgw::no_access();
		}
	}