<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uiequipment extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boequipment');
			self::set_active_menu('booking::equipment');
		}
		
		public function index()
		{
			return $this->bo->populate_json_data("bookingfrontend.uiequipment");
		}

	}

