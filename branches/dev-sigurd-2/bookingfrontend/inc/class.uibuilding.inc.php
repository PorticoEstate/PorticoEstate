<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uibuilding extends booking_uicommon
	{
		public $public_functions = array(
			 'schedule'		=> true,
			 'show'         => true,
		);

		function __construct()
		{
			$this->bo = CreateObject('booking.bobuilding');
			parent::__construct();
		}
		
		public function schedule()
		{
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'bookingfrontend.uibuilding');

			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building));
		}
		
		public function show()
		{
			$this->check_active('booking.uibuilding.show');
			$building                  = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $building['id']));
			$building['start']         = self::link(array('menuaction' => 'bookingfrontend.uisearch.index', 'type' => "building"));
			self::render_template('building', array('search' => array("results" => $building)));
		}
		
	}
