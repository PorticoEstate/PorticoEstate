<?php
	phpgw::import_class('booking.uidocument_view');

	class bookingfrontend_uidocument_view extends booking_uidocument_view
	{

		public $public_functions = array
		(
			'regulations' => true,
			'download' => true
		);

		protected $module;

		public function __construct()
		{
			parent::__construct();
			$this->module = "bookingfrontend";
		}
		public function regulations()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			phpgw::no_access();
		}
	}