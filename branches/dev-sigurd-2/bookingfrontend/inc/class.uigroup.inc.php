<?php
	phpgw::import_class('booking.uigroup');

	class bookingfrontend_uigroup extends booking_uigroup
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'edit'			=>	true,
            'show'          =>  true,
		);

        protected $module;
		public function __construct()
		{
			parent::__construct();
            $this->module = "bookingfrontend";
		}
	}

