<?php
	phpgw::import_class('booking.uicommon');

	class bookingfrontend_uibooking extends booking_uicommon
	{
		public $public_functions = array
		(
			'building_schedule' =>  true,
			'resource_schedule' =>  true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bobooking');
			self::set_active_menu('booking::bookings');
			$this->fields = array('name', 'resources',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'group_id', 'group_name', 
			                      'from_', 'to_');
		}

		public function building_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_schedule(phpgw::get_var('building_id', 'int'), $date);
			foreach($bookings['results'] as &$booking)
			{
				$booking['resource_link'] = $this->link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $booking['resource_id']));
				$booking['link'] = $this->link(array('menuaction' => 'bookingfrontend.uibooking.show', 'id' => $booking['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $bookings['total_records'], 
					"Result" => $bookings['results']
				)
			);
			return $data;
		}

		public function resource_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->resource_schedule(phpgw::get_var('resource_id', 'int'), $date);
			foreach($bookings['results'] as &$booking)
			{
				$booking['link'] = $this->link(array('menuaction' => 'bookingfrontend.uibooking.show', 'id' => $booking['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $bookings['total_records'], 
					"Result" => $bookings['results']
				)
			);
			return $data;
		}

	}
