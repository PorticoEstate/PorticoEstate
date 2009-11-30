<?php
	phpgw::import_class('booking.uibooking');

	class bookingfrontend_uibooking extends booking_uibooking
	{
		public $public_functions = array
		(
			'building_schedule' =>  true,
			'resource_schedule' =>  true,
			'info'				=>	true,
			'add' =>				true,
			'show' =>				true,
			'edit' =>				true,
			'report_numbers' =>		true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->group_bo = CreateObject('booking.bogroup');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->season_bo = CreateObject('booking.boseason');
			$this->building_bo = CreateObject('booking.bobuilding');
		}

		private function item_link(&$item, $key)
		{
			if(in_array($item['type'], array('allocation', 'booking', 'event')))
				$item['info_url'] = $this->link(array('menuaction' => 'bookingfrontend.ui'.$item['type'].'.info', 'id' => $item['id']));
		}

		public function building_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_schedule(phpgw::get_var('building_id', 'int'), $date);
			foreach($bookings['results'] as &$row)
			{
				$row['resource_link'] = $this->link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $booking['resource_id']));
				array_walk($row, array($this, 'item_link'));
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
			$allocation_id = phpgw::get_var('allocation_id', 'int', 'GET');
			$booking['resources'] = phpgw::get_var('resources', 'int', 'GET');
			$booking['from_'] = phpgw::get_var('from_', 'str', 'GET');
			$booking['to_'] = phpgw::get_var('to_', 'str', 'GET');
			$step = phpgw::get_var('step', 'str', 'POST');
			if (! isset($step)) $step = 1;
			$invalid_dates = array();
			$valid_dates = array();

			if($allocation_id)
			{
				$allocation = $this->allocation_bo->read_single($allocation_id);
				$season = $this->season_bo->read_single($allocation['season_id']);
				$building = $this->building_bo->read_single($season['building_id']);
				$booking['season_id'] = $season['id'];
				$booking['building_id'] = $building['id'];
				$booking['building_name'] = $building['name'];
			}
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$booking = extract_values($_POST, $this->fields);
				$booking['building_name'] = $building['name'];
				$booking['building_id'] = $building['id'];
				$booking['active'] = '1';
				$booking['cost'] = 0;
				$booking['completed'] = '0';
				$booking['reminder'] = '1';
				$booking['secret'] = $this->generate_secret();
				array_set_default($booking, 'audience', array());
				array_set_default($booking, 'agegroups', array());
				array_set_default($_POST, 'resources', array());
				$this->agegroup_bo->extract_form_data($booking);

				$errors = $this->bo->validate($booking);

				if (!$errors)
				{
					$step++;
				}

				if (!$errors && $_POST['recurring'] != 'on')
				{
					$receipt = $this->bo->add($booking);
					$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
				}
				else if ( $_POST['recurring'] == 'on' && !$errors && $step > 1)
				{
					$repeat_until = strtotime($_POST['repeat_until']);
					$max_dato = strtotime($_POST['to_']); // highest date from input
					$interval = $_POST['field_interval']*60*60*24*7; // weeks in seconds
					$i = 1;

					// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
					// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.
					while (($max_dato+($interval*$i)) <= $repeat_until)
					{
						$fromdate = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval*$i));
						$todate = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval*$i));
						$booking['from_'] = $fromdate;
						$booking['to_'] = $todate;
						$err = $this->bo->validate($booking);
						if ($err) 
						{
							$invalid_dates[$i]['from_'] = $fromdate;
							$invalid_dates[$i]['to_'] = $todate;
						} 
						else 
						{
							$valid_dates[$i]['from_'] = $fromdate;
							$valid_dates[$i]['to_'] = $todate;
							if ($step == 3)
							{
								$booking['secret'] = $this->generate_secret();
								$receipt = $this->bo->add($booking);
							}
						}
						$i++;
					}
					if ($step == 3) 
					{
						$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
					}
				} 
			}
			$this->flash_form_errors($errors);
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'booking.js');
			array_set_default($booking, 'resources', array());
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=> $booking['building_id']));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$groups = $this->group_bo->so->read(array('filters'=>array('organization_id'=>$allocation['organization_id'], 'active'=>1)));
			$groups = $groups['results'];
			$booking['organization_name'] = $allocation['organization_name'];



			if ($step < 2) 
			{
				self::render_template('booking_new', array('booking' => $booking, 
					'activities' => $activities, 
					'agegroups' => $agegroups, 
					'audience' => $audience, 
					'groups' => $groups, 
					'step' => $step, 
					'interval' => $_POST['field_interval'],
					'repeat_until' => $_POST['repeat_until'],
					'recurring' => $_POST['recurring'])
				);
			} 
			else if ($step == 2) 
			{
				self::render_template('booking_new_preview', array('booking' => $booking, 
					'activities' => $activities,
					'agegroups' => $agegroups,
					'audience' => $audience,
					'step' => $step,
					'recurring' => $_POST['recurring'],
					'interval' => $_POST['field_interval'],
					'repeat_until' => $_POST['repeat_until'],
					'from_date' => $_POST['from_'],
					'to_date' => $_POST['to_'],
					'valid_dates' => $valid_dates,
					'invalid_dates' => $invalid_dates,
					'groups' => $groups)
				);
			}
		}

		public function report_numbers()
		{
			$step = 1;
			$id = intval(phpgw::get_var('id', 'GET'));
			$booking = $this->bo->read_single($id);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$building = $this->building_bo->read_single($booking['building_id']);

			if ($booking['secret'] != phpgw::get_var('secret', 'GET'))
			{
				$step = -1; // indicates that an error message should be displayed in the template
				self::render_template('report_numbers', array('event_object' => $booking, 'agegroups' => $agegroups, 'building' => $building, 'step' => $step));
				return false;
			}

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				//reformatting the post variable to fit the booking object
				$temp_agegroup = array();
				$sexes = array('male', 'female');
				foreach($sexes as $sex)
				{
					$i = 0;
					foreach(phpgw::get_var($sex, 'POST') as $agegroup_id => $value)
					{
						$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
						$temp_agegroup[$i][$sex] = $value;
						$i++;
					}
				}

				$booking['agegroups'] = $temp_agegroup;
				$booking['reminder'] = 2; // status set to delivered
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					$receipt = $this->bo->update($booking);
					$step++;
				}
			} 
			self::render_template('report_numbers', array('event_object' => $booking, 'agegroups' => $agegroups, 'building' => $building, 'step' => $step));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$booking = $this->bo->read_single($id);
			$booking['building'] = $this->building_bo->so->read_single($booking['building_id']);
			$booking['building_name'] = $booking['building']['name'];
			$allocation = $this->allocation_bo->read_single($booking['allocation_id']);
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
					$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'booking.js');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $booking['building_id']));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$group = $this->group_bo->so->read_single($booking['group_id']);
			$groups = $this->group_bo->so->read(array('filters'=>array('organization_id'=>$group['organization_id'], 'active'=>1)));
			$groups =  $groups['results'];
			$booking['organization_name'] = $group['organization_name'];
			self::render_template('booking_edit', array('booking' => $booking, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience, 'groups' => $groups));
		}
		
		public function info()
		{
			$booking = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
			$booking['group'] = $this->group_bo->read_single($booking['group_id']);
			$resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$booking['resources']), 'sort'=>'name'));
			$booking['resources'] = $resources['results'];
			$res_names = array();
			foreach($booking['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$booking['resource_info'] = join(', ', $res_names);
			$booking['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $booking['resources'][0]['building_id']));
			$booking['org_link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $booking['group']['organization_id']));
			$booking['group_link'] = self::link(array('menuaction' => 'bookingfrontend.uigroup.show', 'id' => $booking['group']['id']));
			
			$bouser = CreateObject('bookingfrontend.bouser');
			if($bouser->is_group_admin($booking['group_id']))
				$booking['edit_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.edit', 'id' => $booking['id']));
				
			$booking['when'] = pretty_timestamp($booking['from_']).' - '.pretty_timestamp($booking['to_']);
			self::render_template('booking_info', array('booking'=>$booking));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}
