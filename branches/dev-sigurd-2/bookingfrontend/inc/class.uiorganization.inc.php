<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uiorganization extends booking_uicommon
	{
		public $public_functions = array
		(
			'show'			=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boorganization');
		}
		public function show()
		{
			$organization = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			self::render_template('organization', array('organization' => $organization));
		}
	}
