<?php
	phpgw::import_class('booking.uibooking');

	class bookingfrontend_uibooking extends booking_uibooking
	{
		public $public_functions = array
		(
			'building_schedule' =>  true,
			'resource_schedule' =>  true,
			'add' =>				true,
			'show' =>				true,
			'edit' =>				true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->group_bo = CreateObject('booking.bogroup');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->season_bo = CreateObject('booking.boseason');
			$this->building_bo = CreateObject('booking.bobuilding');
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

		public function add()
		{
			$errors = array();
			$booking = array();
			$booking['building_id'] = phpgw::get_var('building_id', 'int', 'GET');
			$booking['allocation_id'] = phpgw::get_var('allocation_id', 'int', 'GET');
			$booking['resources'] = phpgw::get_var('resources', 'int', 'GET');
			$booking['from_'] = phpgw::get_var('from_', 'str', 'GET');
			$booking['to_'] = phpgw::get_var('to_', 'str', 'GET');
			if($booking['allocation_id'])
			{
				$allocation = $this->allocation_bo->read_single($booking['allocation_id']);
				$season = $this->season_bo->read_single($allocation['season_id']);
				$building = $this->building_bo->read_single($season['building_id']);
				$booking['season_id'] = $season['id'];
				$booking['building_id'] = $building['id'];
				$booking['building_name'] = $building['name'];
			}
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$booking = extract_values($_POST, $this->fields);
				$booking['active'] = '1';
				array_set_default($booking, 'audience', array());
				array_set_default($booking, 'agegroups', array());
				array_set_default($_POST, 'resources', array());
				$this->agegroup_bo->extract_form_data($booking);
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					$receipt = $this->bo->add($booking);
					$this->redirect(array('menuaction' => 'bookingfrontend.uibooking.edit', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'booking.js');
			array_set_default($booking, 'resources', array());
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.index'));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$groups = $this->group_bo->read();
			$groups = $groups['results'];
			self::render_template('booking_new', array('booking' => $booking, 'activities' => $activities,  'agegroups' => $agegroups, 'audience' => $audience, 'groups' => $groups));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$booking = $this->bo->read_single($id);
			$booking['id'] = $id;
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$booking = array_merge($booking, extract_values($_POST, $this->fields));
				$this->agegroup_bo->extract_form_data($booking);
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					$receipt = $this->bo->update($booking);
					$this->redirect(array('menuaction' => 'bookingfrontend.uibooking.show', 'id'=>$booking['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'booking.js');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$groups = $this->group_bo->read();
			$groups = $groups['results'];
			self::render_template('booking_edit', array('booking' => $booking, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience, 'groups' => $groups));
		}

	}
