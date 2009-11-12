<?php
	phpgw::import_class('booking.uibuilding');

	class bookingfrontend_uibuilding extends booking_uibuilding
	{
		public $public_functions = array(
			 'index'		=> true,
			 'schedule'		=> true,
			 'show'         => true,
			'find_buildings_used_by' => true,
		);

		public function schedule()
		{
			$backend = phpgw::get_var('backend', 'GET');
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'bookingfrontend.uibuilding');
			$building['application_link'] = self::link(array(
				'menuaction' => 'bookingfrontend.uiapplication.add', 
				'building_id' => $building['id'], 
				'building_name' => $building['name'],
			));
			$building['datasource_url'] = self::link(array(
				'menuaction' => 'bookingfrontend.uibooking.building_schedule', 
				'building_id' => $building['id'], 
				'phpgw_return_as' => 'json',
			));

			// the schedule can also be used from backend
			// if so we want to change default date shown in the calendar
			if ($backend == 'true')
			{
				$building['date'] = phpgw::get_var('date', 'GET');
			}

			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building, 'backend' => $backend));
		}
		
		public function show()
		{
			$this->check_active('booking.uibuilding.show');
			$building                  = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['schedule_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $building['id']));
			$building['start']         = self::link(array('menuaction' => 'bookingfrontend.uisearch.index', 'type' => "building"));
			self::render_template('building', array("building" => $building));
		}
		
	}
