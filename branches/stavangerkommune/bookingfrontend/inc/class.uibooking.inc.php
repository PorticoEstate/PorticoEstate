<?php
	phpgw::import_class('booking.uibooking');

	class bookingfrontend_uibooking extends booking_uibooking
	{
		public $public_functions = array
		(
			'building_schedule' =>  true,
			'building_extraschedule' =>  true,
			'resource_schedule' =>  true,
			'info'				=>	true,
			'add' =>				true,
			'show' =>				true,
			'edit' =>				true,
			'report_numbers' =>		true,
			'massupdate' =>			true,
			'cancel' =>		    	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->group_bo = CreateObject('booking.bogroup');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->season_bo = CreateObject('booking.boseason');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->system_message_bo = CreateObject('booking.bosystem_message');
			$this->organization_bo = CreateObject('booking.boorganization');
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
				$row['resource_link'] = $this->link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $row['resource_id']));
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

		public function building_extraschedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_extraschedule(phpgw::get_var('building_id', 'int'), $date);
			foreach($bookings['results'] as &$row)
			{
				$row['resource_link'] = $this->link(array('menuaction' => 'bookingfrontend.uiresource.schedule', 'id' => $row['resource_id']));
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
				array_walk($booking, array($this, 'item_link'));
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
            #The string replace is a workaround for a problem at Bergen Kommune 
            $booking['from_'] = str_replace('%3A',':',phpgw::get_var('from_', 'str', 'GET'));
            $booking['to_'] = str_replace('%3A',':',phpgw::get_var('to_', 'str', 'GET'));
			$time_from = split(" ",phpgw::get_var('from_', 'str', 'GET'));
			$time_to = 	split(" ",phpgw::get_var('to_', 'str', 'GET'));

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
				array_set_default($booking, 'resources', array(get_var('resource', int, 'GET')));
			} else {
				$season = $this->season_bo->read_single($_POST['season_id']);
            }
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$today = getdate();
				$booking = extract_values($_POST, $this->fields);
				if(strlen($_POST['from_']) < 6) 
				{
					$date_from = array($time_from[0], $_POST['from_']);
					$booking['from_'] = join(" ",$date_from);
					$_POST['from_'] = join(" ",$date_from);
					$date_to = array($time_to[0], $_POST['to_']);
					$booking['to_'] = join(" ",$date_to); 
					$_POST['to_'] = join(" ",$date_to);
				}				
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


#				if (strtotime($_POST['from_']) < $today[0])
#				{
#					if($_POST['recurring'] == 'on' || $_POST['outseason'] == 'on')
#					{					
#						$errors['booking'] = lang('Can not repeat from a date in the past');
#					}
#					else
#					{
#						$errors['booking'] = lang('Can not create a booking in the past');
#					}
#				} 
				if (!$season['id'] &&  $_POST['outseason'] == 'on')
				{
					$errors['booking'] = lang('This booking is not connected to a season');
				}	

				if (!$errors)
				{
					$step++;
				}

				if (!$errors && $_POST['recurring'] != 'on' && $_POST['outseason'] != 'on' )
				{
					$receipt = $this->bo->add($booking);
					$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
				}
				else if ( ($_POST['recurring'] == 'on' || $_POST['outseason'] == 'on')  && !$errors && $step > 1)
				{
					if ($_POST['recurring'] == 'on') {
						$repeat_until = strtotime($_POST['repeat_until'])+60*60*24; 
					} 
					else
					{
						$repeat_until = strtotime($season['to_'])+60*60*24; 
						$_POST['repeat_until'] = $season['to_'];
					} 

					$max_dato = strtotime($_POST['to_']); // highest date from input
					$interval = $_POST['field_interval']*60*60*24*7; // weeks in seconds
					$i = 0;
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
			$resources_full = $this->resource_bo->so->read(array('filters'=>array('id'=>$booking['resources']), 'sort'=>'name'));
			$res_names = array();
			foreach($resources_full['results'] as $res)
			{
				$res_names[] = array('id' => $res['id'],'name' => $res['name']);
			}
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
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'date_from' => $time_from[0],
					'date_to' => $time_to[0],
					'res_names' => $res_names)
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
					'outseason' => $_POST['outseason'],
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
			$group = $this->group_bo->read_single($booking['group_id']);
			$errors = array();
			$update_count = 0;
			$today = getdate();
			$step = intval(phpgw::get_var('step', 'POST'));

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{

				if (!($_POST['recurring'] == 'on' || $_POST['outseason'] == 'on')){

					array_set_default($_POST, 'resources', array());
					$booking = array_merge($booking, extract_values($_POST, $this->fields));
					$this->agegroup_bo->extract_form_data($booking);
					$errors = $this->bo->validate($booking);
				
#					if (strtotime($_POST['from_']) < ($today[0]-60*60*24*7*2))
#					{
#						$errors['booking'] = lang('You cant edit a booking that is older than 2 weeks');
#					}										
					if (!$errors) {
						$receipt = $this->bo->update($booking);
						$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
					}
				} 
				else 
				{
					$step++;

					if (strtotime($_POST['from_']) < ($today[0]-60*60*24*7*2) && $step != 3)
					{					
						$errors['booking'] = lang('You cant update bookings that is older than 2 weeks');
					}

					if (!$booking['allocation_id'] &&  $_POST['outseason'] == 'on')
					{
						$errors['booking'] = lang('This booking is not connected to a season');
					}	

					if (!$errors)
					{

                        $max_dato = strtotime($_POST['to_']); // highest date from input

                        $season = $this->season_bo->read_single($booking['season_id']);

						if ($_POST['recurring'] == 'on') {
							$repeat_until = strtotime($_POST['repeat_until'])+60*60*24; 
						} 
						else
						{
							$repeat_until = strtotime($season['to_'])+60*60*24; 
							$_POST['repeat_until'] = $season['to_'];
						} 

						$where_clauses[] = sprintf("bb_booking.from_ >= '%s 00:00:00'", date('Y-m-d', strtotime($_POST['from_'])));
						if ($_POST['recurring'] == 'on') {
							$where_clauses[] = sprintf("bb_booking.to_ < '%s 00:00:00'", date('Y-m-d', $repeat_until));
						}
                        $where_clauses[] = sprintf("EXTRACT(DOW FROM bb_booking.from_) in (%s)",date('w', strtotime($_POST['from_'])));
                        $where_clauses[] = sprintf("EXTRACT(HOUR FROM bb_booking.from_) = %s",date('H', strtotime($_POST['from_'])));
                        $where_clauses[] = sprintf("EXTRACT(MINUTE FROM bb_booking.from_) = %s",date('i', strtotime($_POST['from_'])));
                        $where_clauses[] = sprintf("EXTRACT(HOUR FROM bb_booking.to_) = %s",date('H', strtotime($_POST['to_'])));
                        $where_clauses[] = sprintf("EXTRACT(MINUTE FROM bb_booking.to_) = %s",date('i', strtotime($_POST['to_'])));
                        $params['sort'] = 'from_';
						$params['filters']['where'] = $where_clauses;
						$params['filters']['season_id'] = $booking['season_id'];
						$params['filters']['group_id'] = $booking['group_id'];
						$bookings = $this->bo->so->read($params);

						if ($step == 2)
						{
							$_SESSION['audience'] = $_POST['audience'];
							$_SESSION['male'] = $_POST['male'];
							$_SESSION['female'] = $_POST['female'];
						}
                        if ($step == 3)
						{
							foreach($bookings['results'] as $b)
							{
								//reformatting the post variable to fit the booking object
								$temp_agegroup = array();
								$sexes = array('male', 'female');
								foreach($sexes as $sex)
								{
									$i = 0;
									foreach($_SESSION[$sex] as $agegroup_id => $value)
									{
										$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
										$temp_agegroup[$i][$sex] = $value;
										$i++;
									}
								}
								$b['agegroups'] = $temp_agegroup;
								$b['audience'] = $_SESSION['audience'];
								$b['group_id'] =$_POST['group_id'];
								$b['activity_id'] = $_POST['activity_id'];
								$errors = $this->bo->validate($b);

								if(!$errors)
								{
									$receipt = $this->bo->update($b);
									$update_count++;
								}
							}
                            unset($_SESSION['female']);
							unset($_SESSION['male']);
							unset($_SESSION['audience']);

						}
                    }
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('bookingfrontend', 'bookingfrontend', 'booking.js');
			if ($step < 2) {
				$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
				$booking['organization_name'] = $group['organization_name'];
			}
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
			if ($step < 2) 
			{
				self::render_template('booking_edit', array('booking' => $booking, 
					'activities' => $activities, 
					'agegroups' => $agegroups, 
					'audience' => $audience, 
					'groups' => $groups, 
					'step' => $step, 
					'repeat_until' => $_POST['repeat_until'],
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					)
				);
			} 
			else if ($step >= 2) 
			{
				self::render_template('booking_edit_preview', array('booking' => $booking, 
					'bookings' => $bookings,
					'agegroups' => $agegroups,
					'audience' => $audience,
					'groups' => $groups,
					'activities' => $activities,
					'step' => $step,
					'repeat_until' => $_POST['repeat_until'],
					'recurring' => $_POST['recurring'],
					'outseason' => $_POST['outseason'],
					'group_id' => $_POST['group_id'],
					'activity_id' => $_POST['activity_id'],
					'update_count' => $update_count)
				);
			}
		}

		public function massupdate()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$booking = $this->bo->read_single($id);
			$booking['building'] = $this->building_bo->so->read_single($booking['building_id']);
			$booking['building_name'] = $booking['building']['name'];
			$allocation = $this->allocation_bo->read_single($booking['allocation_id']);
			$errors = array();
			$update_count = 0;
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$step = intval(phpgw::get_var('step', 'POST'));
				$step++;

				$season = $this->season_bo->read_single($booking['season_id']);

                $where_clauses[] = sprintf("EXTRACT(DOW FROM bb_booking.from_) in (%s)",date('w', strtotime($booking['from_'])));
                $where_clauses[] = sprintf("EXTRACT(HOUR FROM bb_booking.from_) = %s",date('H', strtotime($booking['from_'])));
                $where_clauses[] = sprintf("EXTRACT(MINUTE FROM bb_booking.from_) = %s",date('i', strtotime($booking['from_'])));
                $where_clauses[] = sprintf("EXTRACT(HOUR FROM bb_booking.to_) = %s",date('H', strtotime($booking['to_'])));
                $where_clauses[] = sprintf("EXTRACT(MINUTE FROM bb_booking.to_) = %s",date('i', strtotime($booking['to_'])));
                $where_clauses[] = sprintf("bb_booking.from_ >= '%s 00:00:00'", date('Y-m-d'));
                $params['sort'] = 'from_';
				$params['filters']['where'] = $where_clauses;
				$params['filters']['season_id'] = $booking['season_id'];
				$params['filters']['group_id'] = $booking['group_id'];
				$booking = $this->bo->so->read($params);

				if ($step == 2)
				{
					$_SESSION['audience'] = $_POST['audience'];
					$_SESSION['male'] = $_POST['male'];
					$_SESSION['female'] = $_POST['female'];
				}

				if ($step == 3)
				{
					foreach($booking['results'] as $b)
					{
						//reformatting the post variable to fit the booking object
						$temp_agegroup = array();
						$sexes = array('male', 'female');
						foreach($sexes as $sex)
						{
							$i = 0;
							foreach($_SESSION[$sex] as $agegroup_id => $value)
							{
								$temp_agegroup[$i]['agegroup_id'] = $agegroup_id;
								$temp_agegroup[$i][$sex] = $value;
								$i++;
							}
						}

						$b['agegroups'] = $temp_agegroup;
						$b['audience'] = $_SESSION['audience'];
						$b['group_id'] =$_POST['group_id'];
						$b['activity_id'] = $_POST['activity_id'];
						$errors = $this->bo->validate($b);
						if(!$errors)
						{
							$receipt = $this->bo->update($b);
							$update_count++;
						}
					}
					unset($_SESSION['female']);
					unset($_SESSION['male']);
					unset($_SESSION['audience']);
				}
			}

			$this->flash_form_errors($errors);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];

			$group = $this->group_bo->so->read_single($booking['group_id']);
			$groups = $this->group_bo->so->read(array('filters'=>array('organization_id'=>$group['organization_id'], 'active'=>1)));
			$groups =  $groups['results'];

			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			
			self::render_template('booking_massupdate',
					array('booking' => $booking,
						  'agegroups' => $agegroups,
						  'audience' => $audience,
						  'groups' => $groups,
						  'activities' => $activities,
						  'step' => $step,
						  'group_id' => $_POST['group_id'],
						  'activity_id' => $_POST['activity_id'],
						  'update_count' => $update_count,
						)
					);
		}

		public function building_users($building_id, $group_id, $delete_allocation) {

            $contacts = array();
#            $building = $this->building_bo->so->read_single($building_id);
            
			$organizations = $this->organization_bo->find_building_users($building_id);
            foreach($organizations['results'] as $org)
            {
                if ($org['email'] != '' && strstr($org['email'], '@')) {
                    if (!in_array($org['email'], $contacts)) {
                        $contacts[] = $org['email'];
                    }
                }
                if ($org['contacts'][0]['email'] != '' && strstr($org['contacts'][0]['email'], '@')) {
                    if (!in_array($org['contacts'][0]['email'], $contacts)) {
                        $contacts[] = $org['contacts'][0]['email']; 
                    }
                }
                if ($org['contacts'][1]['email'] != '' && strstr($org['contacts'][1]['email'], '@')) {
                    if (!in_array($org['contacts'][1]['email'], $contacts)) {
                        $contacts[] = $org['contacts'][1]['email']; 
                    }
                }
                $grp_con = $this->bo->so->get_group_contacts_of_organization($org['id']);
                foreach ($grp_con as $grp) {
                    if ($grp['email'] != '' && strstr($grp['email'], '@') && $grp['group_id'] != $group_id ) {
                        if (!in_array($grp['email'], $contacts)) {
                            $contacts[] = $grp['email'];
                        }
                    }
                }
            } 
			return $contacts;
		}

		public function organization_users($group_id) {

            $contacts = array();
            $groups = $this->bo->so->get_all_group_of_organization_from_groupid($group_id);
            foreach($groups as $grp) {
                if ($grp['email'] != '' && strstr($grp['email'], '@') && $grp['group_id'] != $group_id ) {
                    if (!in_array($grp['email'], $contacts)) {
                        $contacts[] = $grp['email'];
                    }
                }
            }
			return $contacts;
        }
		public function cancel()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();

			if ($config->config_data['user_can_delete_bookings'] != 'yes') {

	        	$booking = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
	   			$errors = array();
				if($_SERVER['REQUEST_METHOD'] == 'POST')
	            {
	            
	                $from = $_POST['from_'];
	                $to =  $_POST['to_'];
	                $organization_id = $_POST['organization_id'];
	                $outseason = $_POST['outseason'];
	                $recurring = $_POST['recurring'];
	                $repeat_until = $_POST['repeat_until'];
	                $field_interval = $_POST['field_interval'];
	                $delete_allocation = $_POST['delete_allocation'];
                    $allocation = $this->allocation_bo->read_single($booking['allocation_id']);

                    $maildata = array();
                    $maildata['outseason'] = $outseason;
                    $maildata['recurring'] = $recurring;
                    $maildata['repeat_until'] = $repeat_until;
                    $maildata['delete_allocation'] = $delete_allocation;


                    date_default_timezone_set("Europe/Oslo");
					$date = new DateTime(phpgw::get_var('date'));
					$system_message = array();
					$system_message['building_id'] = intval($booking['building_id']);
					$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
					$system_message['created'] =  $date->format('Y-m-d  H:m');
					$system_message = array_merge($system_message, extract_values($_POST, array('message')));
	                $system_message['type'] = 'cancelation';
					$system_message['status'] = 'NEW';
					$system_message['name'] = $booking['group_name'];
					$system_message['phone'] = ' ';
					$system_message['email'] = ' ';
					$system_message['title'] = lang('Cancelation of booking from')." ".$booking['group_name'];

	                $link = self::link(array('menuaction' => 'booking.uibooking.delete','id' => $booking['id'], 'outseason' => $outseason, 'recurring' => $recurring, 'repeat_until' => $repeat_until, 'field_interval' => $field_interval, 'delete_allocation' => $delete_allocation));
	                $link = mb_strcut($link,16,strlen($link));
	                $system_message['message'] = $system_message['message']."\n\n".lang('To cancel booking use this link')." - <a href='".$link."'>".lang('Delete')."</a>";
                    $this->bo->send_admin_notification($booking, $maildata, $system_message, $allocation);
					$receipt = $this->system_message_bo->add($system_message);
					$this->redirect(array('menuaction' =>  'bookingfrontend.uibuilding.schedule', 'id' => $system_message['building_id']));

	            }
	            $this->flash_form_errors($errors);
				$allocation['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $allocation['building_id']));
	
				$this->use_yui_editor();
				self::render_template('booking_cancel', array('booking'=>$booking));

			} else {
	
				$id = intval(phpgw::get_var('id', 'GET'));
				$outseason = phpgw::get_var('outseason', 'GET');
				$recurring = phpgw::get_var('recurring', 'GET');
				$repeat_until = phpgw::get_var('repeat_until', 'GET');
				$field_interval = intval(phpgw::get_var('field_interval', 'GET'));
				$delete_allocation = phpgw::get_var('delete_allocation', 'GET');
				$booking = $this->bo->read_single($id);
	            $allocation = $this->allocation_bo->read_single($booking['allocation_id']);
	            $season = $this->season_bo->read_single($booking['season_id']);
				$step = phpgw::get_var('step', 'str', 'POST');
	        	if (! isset($step)) $step = 1;
	            $errors = array();
				$invalid_dates = array();
				$valid_dates = array();
	            $allocation_delete = array();
	            $allocation_keep = array();

                $mailadresses = $this->building_users($booking['building_id'],$booking['group_id'], $delete_allocation);

                $maildata = array();
                $maildata['outseason'] = $outseason;		
                $maildata['recurring'] = $recurring;		
                $maildata['repeat_until'] = $repeat_until;
                $maildata['delete_allocation'] = $delete_allocation;

				if($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$from_date = $_POST['from_'];
					$to_date = $_POST['to_'];
					$info_deleted = '';
					$inf_del = '';

	  				if ($_POST['recurring'] != 'on' && $_POST['outseason'] != 'on' )
        	        {

        	            if ($_POST['delete_allocation'] != 'on') {

		            	    $inf_del = "Booking";
                            $maildata['allocation'] = 0;                            
						    $this->bo->so->delete_booking($id);
	                    }
	                    else
	                    {
	                        $allocation_id = $booking['allocation_id'];
	                        $this->bo->so->delete_booking($id);
	                        $err  = $this->allocation_bo->so->check_for_booking($allocation_id);
	                        if ($err)
	                        {
			            	    $inf_del = "Booking";
                                $maildata['allocation'] = 0;                            
	                    	    $errors['booking'] = lang('Could not delete allocation due to a booking still use it');
	                        }
	                        else
							{
		            	    	$inf_del = "Booking and allocation";
                                $maildata['allocation'] = 1;                            
	                            $err = $this->allocation_bo->so->delete_allocation($allocation_id);
	                        }

	                    }

						$res_names = '';
						date_default_timezone_set("Europe/Oslo");
						$date = new DateTime(phpgw::get_var('date'));
						$system_message = array();
						$system_message['building_id'] = intval($booking['building_id']);
						$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
						$system_message['created'] =  $date->format('Y-m-d  H:m');
						$system_message = array_merge($system_message, extract_values($_POST, array('message')));
		                $system_message['type'] = 'cancelation';
						$system_message['status'] = 'NEW';
						$system_message['name'] = $booking['group_name'];
						$system_message['phone'] = ' ';
						$system_message['email'] = ' ';
						$system_message['title'] = lang("Cancelation of ".$inf_del." from")." ".$this->bo->so->get_organization($booking['group_id'])." - ".$booking['group_name'];
						foreach ($booking['resources'] as $res) {
							$res_names = $res_names.$this->bo->so->get_resource($res)." ";
						}
						$info_deleted = lang($inf_del." deleted on")." ".$system_message['building_name'].":<br />".$res_names." - ".pretty_timestamp($booking['from_'])." - ".pretty_timestamp($booking['to_']);
                        $this->bo->send_admin_notification($booking, $maildata, $system_message, $allocation);
                        $this->bo->send_notification($booking, $allocation, $maildata, $mailadresses);
			            $system_message['message'] = $system_message['message']."<br />".$info_deleted;
						$receipt = $this->system_message_bo->add($system_message);

                        $this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$booking['building_id']));
	                } 
	                else
	                { 
	                    $step++;
						if ($_POST['recurring'] == 'on') {
								$repeat_until = strtotime($_POST['repeat_until'])+60*60*24; 
						} 
						else
						{
							$repeat_until = strtotime($season['to_'])+60*60*24; 
							$_POST['repeat_until'] = $season['to_'];
						} 
	
						$max_dato = strtotime($_POST['to_']); // highest date from input
						$interval = $_POST['field_interval']*60*60*24*7; // weeks in seconds
						$i = 0;
						// calculating valid and invalid dates from the first booking's to-date to the repeat_until date is reached
						// the form from step 1 should validate and if we encounter any errors they are caused by double bookings.
	
						while (($max_dato+($interval*$i)) <= $repeat_until)
						{
							$fromdate = date('Y-m-d H:i', strtotime($_POST['from_']) + ($interval*$i));
							$todate = date('Y-m-d H:i', strtotime($_POST['to_']) + ($interval*$i));
							$booking['from_'] = $fromdate;
							$booking['to_'] = $todate;
							$info_deleted = '';
							$inf_del = '';
	
	                        $id = $this->bo->so->get_booking_id($booking);                
	                        if($id) {
	                            $aid = $this->bo->so->check_allocation($id);
	                        } else {
	                            $aid = $this->bo->so->check_for_booking($booking);    
	                        }
	                            
	                		if ($id) 
							{
								$valid_dates[$i]['from_'] = $fromdate;
								$valid_dates[$i]['to_'] = $todate;
								if ($step == 3)
								{
				            	    $inf_del = "Bookings";
	                                $stat = $this->bo->so->delete_booking($id);
	                            }                            
	                        }
	                        if ($_POST['delete_allocation'] == 'on')                         {
	    						if (!$aid) 
	    						{
	    							$allocation_keep[$i]['from_'] = $fromdate;
	    							$allocation_keep[$i]['to_'] = $todate;
	    						} 
	    						else 
	    						{
	    							$allocation_delete[$i]['from_'] = $fromdate;
	    							$allocation_delete[$i]['to_'] = $todate;
	    							if ($step == 3)
	    							{
				            	       $inf_del = "Bookings and allocations";
	                                   $stat = $this->bo->so->delete_allocation($aid);
	                                }                            
	                            }
	                        }
							$i++;
	                    }
						if ($step == 3) 
						{
                            $maildata = array();
                            $maildata['outseason'] = phpgw::get_var('outseason','GET');
                            $maildata['recurring'] = phpgw::get_var('recurring', 'GET');		
                            $maildata['repeat_until'] = phpgw::get_var('repeat_until', 'GET');	
                            $maildata['delete_allocation'] = phpgw::get_var('delete_allocation', 'GET');
							$maildata['keep'] = $allocation_keep;
							$maildata['delete'] = $allocation_delete;

                            $res_names = '';
							date_default_timezone_set("Europe/Oslo");
							$date = new DateTime(phpgw::get_var('date'));
							$system_message = array();
							$system_message['building_id'] = intval($booking['building_id']);
							$system_message['building_name'] = $this->bo->so->get_building($system_message['building_id']);
							$system_message['created'] =  $date->format('Y-m-d  H:m');
							$system_message = array_merge($system_message, extract_values($_POST, array('message')));
			                $system_message['type'] = 'cancelation';
							$system_message['status'] = 'NEW';
							$system_message['name'] = $booking['group_name'];
							$system_message['phone'] = ' ';
							$system_message['email'] = ' ';
							$system_message['title'] = lang("Cancelation of ".$inf_del." from")." ".$this->bo->so->get_organization($booking['group_id'])." - ".$booking['group_name'];
							foreach ($booking['resources'] as $res) {
								$res_names = $res_names.$this->bo->so->get_resource($res)." ";
							}
							$info_deleted = lang($inf_del." deleted on")." ".$system_message['building_name'].":<br />";
							foreach ($valid_dates as $valid_date) {
								$info_deleted = $info_deleted."<br />".$res_names." - ".pretty_timestamp($valid_date['from_'])." - ".pretty_timestamp($valid_date['to_']);
							}
				            $system_message['message'] = $system_message['message']."<br />".$info_deleted;
                            $this->bo->send_admin_notification($booking, $maildata, $system_message, $allocation, $valid_dates);
                            $this->bo->send_notification($booking, $allocation, $maildata, $mailadresses, $valid_dates);
							$receipt = $this->system_message_bo->add($system_message);
							$this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=>$allocation['building_id']));
						}
	                }
				}
	
				$this->flash_form_errors($errors);
				if ($config->config_data['user_can_delete_allocations'] != 'yes')
				{
					$user_can_delete_allocations = 0;
				
				} else {
					$user_can_delete_allocations = 1;
				}
				self::add_javascript('booking', 'booking', 'booking.js');
				$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
#				$booking['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.show', 'id' => $booking['id']));
                $booking['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' => $booking['building_id'], 'date' => $booking['from_']));
				$booking['booking_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.show', 'id' => $booking['id']));
				if ($step < 2) 
	            {
	    			self::render_template('booking_delete', array('booking' => $booking,
						'recurring' => $recurring,
						'outseason' => $outseason,
						'interval' => $field_interval,
						'repeat_until' => $repeat_until,
	                    'delete_allocation' => $delete_allocation,
						'user_can_delete_allocations' => $user_can_delete_allocations
	                ));
	            }
				elseif ($step == 2) 
	            {
					self::render_template('booking_delete_preview', array('booking' => $booking,
						'step' => $step,
						'recurring' => $_POST['recurring'],
						'outseason' => $_POST['outseason'],
						'interval' => $_POST['field_interval'],
						'repeat_until' => $_POST['repeat_until'],
						'from_date' => $from_date,
						'to_date' => $to_date,
	                    'delete_allocation' => $_POST['delete_allocation'],
						'user_can_delete_allocations' => $user_can_delete_allocations,
						'allocation_keep' => $allocation_keep,
						'allocation_delete' => $allocation_delete,
						'message' => $_POST['message'],
						'valid_dates' => $valid_dates,
						'invalid_dates' => $invalid_dates
					));
	            }                


			}
        }		

		public function info()
		{
			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			if ($config->config_data['user_can_delete_bookings'] != 'yes')
			{
				$user_can_delete_bookings = 0;
			
			} else {
				$user_can_delete_bookings = 1;
			}
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
            {
				$booking['edit_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.edit', 'id' => $booking['id']));
				$booking['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibooking.cancel', 'id' => $booking['id']));
            }
			$booking['when'] = pretty_timestamp($booking['from_']).' - '.pretty_timestamp($booking['to_']);
			self::render_template('booking_info', array('booking'=>$booking, 'user_can_delete_bookings' => $user_can_delete_bookings));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}
	}
