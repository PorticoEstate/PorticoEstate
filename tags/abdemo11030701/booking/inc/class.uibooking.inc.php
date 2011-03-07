<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	class booking_uibooking extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'info'			=>	true,
			'building_schedule' =>  true,
			'resource_schedule' =>  true,
			'toggle_show_inactive'	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.bobooking');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->building_bo = CreateObject('booking.bobuilding');
			$this->resource_bo = CreateObject('booking.boresource');
			$this->season_bo = CreateObject('booking.boseason');
			$this->allocation_bo = CreateObject('booking.boallocation');
			$this->group_bo    = CreateObject('booking.bogroup');
			self::set_active_menu('booking::applications::bookings');
			$this->fields = array('allocation_id', 'activity_id', 'resources',
								  'building_id', 'building_name', 'application_id',
								  'season_id', 'season_name', 
			                      'group_id', 'group_name','group_shortname', 'organization_id', 'organization_name',
			                      'from_', 'to_', 'audience', 'active', 'cost', 'reminder', 'sms_total');
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix.'.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uibooking.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'group_name',
							'label' => lang('Group')
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'season_name',
							'label' => lang('Season')
						),
						array(
							'key' => 'from_',
							'label' => lang('From')
						),
						array(
							'key' => 'to_',
							'label' => lang('To')
						),
						array(
							'key' => 'cost',
							'label' => lang('Cost')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
						'type' => 'link',
						'value' => lang('New booking'),
						'href' => self::link(array('menuaction' => 'booking.uibooking.add'))
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$bookings = $this->bo->read();
			foreach($bookings['results'] as &$booking) {
				$building = $this->building_bo->read_single($booking['building_id']);
				$booking['building_name'] = $building['name'];
				$booking['from_'] = pretty_timestamp($booking['from_']);
				$booking['to_'] = pretty_timestamp($booking['to_']);
			}

			array_walk($bookings["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->yui_results($bookings);
		}

		private function item_link(&$item, $key)
		{
			if(in_array($item['type'], array('allocation', 'booking', 'event')))
				$item['info_url'] = $this->link(array('menuaction' => 'booking.ui'.$item['type'].'.info', 'id' => $item['id']));
		}

		public function building_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_schedule(phpgw::get_var('building_id', 'int'), $date);
			foreach($bookings['results'] as &$booking)
			{
				$booking['resource_link'] = $this->link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $booking['resource_id']));
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
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

		public function resource_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->resource_schedule(phpgw::get_var('resource_id', 'int'), $date);
			foreach($bookings['results'] as &$booking)
			{
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
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
			$booking['cost'] = 0;
			$allocation_id = phpgw::get_var('allocation_id', 'int', 'GET');
			$booking['building_id'] = phpgw::get_var('building_id', 'int', 'GET');
			$booking['resources'] = phpgw::get_var('resources', 'int', 'GET');
			$booking['from_'] = phpgw::get_var('from_', 'str', 'GET');
			$booking['to_'] = phpgw::get_var('to_', 'str', 'GET');
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
				$booking['organization_id'] = $allocation['organization_id'];
				$booking['organization_name'] = $allocation['organization_name'];
			}

            //start Debug code for testing problem on production server. to be removed ASAP!
            if (phpgw::get_var('DEBUG', 'str', 'GET') == 'yes') {
                echo "<pre>\n";
                echo mb_detect_encoding(phpgw::get_var('from_', 'str', 'GET'), "auto");echo "\n";
                print_r($allocation_id);echo "\n";
                print_r($booking['from_']);echo "\n";
                print_r($booking['to_']);echo "\n";
                print_r($time_from);echo "\n";
                print_r($time_to);echo "\n";
                print_r($booking);echo "\n";
                exit;
            }
            //end
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
				$booking['active'] = '1';
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
				if (!$allocation_id &&  $_POST['outseason'] == 'on')
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
					$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id'=>$booking['building_id']));
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
						$this->redirect(array('menuaction' => 'booking.uimassbooking.schedule', 'id'=>$booking['building_id']));
					}
				}
			}

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'booking.js');
			array_set_default($booking, 'resources', array());
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uimassbooking.index'));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$groups = $this->group_bo->so->read(array('filters'=>array('organization_id'=>$allocation['organization_id'], 'active'=>1)));
			$groups = $groups['results'];

			$resouces_full = $this->resource_bo->so->read(array('filters'=>array('id'=>$booking['resources']), 'sort'=>'name'));
			$res_names = array();
			foreach($resouces_full['results'] as $res)
			{
				$res_names[] = array('id' => $res['id'],'name' => $res['name']);
			}

            //start Debug code for testing problem on production server. to be removed ASAP!
            if (phpgw::get_var('DEBUG', 'str', 'GET') == 'end') {
                echo "<pre>\n";
                echo "encoding: ";echo mb_detect_encoding(phpgw::get_var('from_', 'str', 'GET'), "auto");echo "\n";
                echo "encoding array: ";echo mb_detect_encoding($booking['from_'], "auto");echo "\n";
                echo "allocation_id: ";print_r($allocation_id);echo "\n";
                echo "booking from: ";print_r($booking['from_']);echo "\n";
                echo "booking to: ";print_r($booking['to_']);echo "\n";
                echo "time from: ";print_r($time_from);echo "\n";
                echo "time to:";print_r($time_to);echo "\n";
                echo "booking:\n";print_r($booking);echo "\n";
                exit;
            }
            //end

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

		private function send_mailnotification_to_group($group, $subject, $body)
		{
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0) 
			{
				return false;
			}

			foreach($group['contacts'] as $contact) 
			{
				if (strlen($contact['email']) > 0) 
				{
					try
					{
						$send->msg('email', $contact['email'], $subject, $body, '', '', '', $from, '', 'plain');
					}
					catch (phpmailerException $e)
					{
					}
				}
			}
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$booking = $this->bo->read_single($id);
			$booking['group'] = $this->group_bo->so->read_single($booking['group_id']);
			$booking['organization_id'] = $booking['group']['organization_id'];
			$booking['organization_name'] = $booking['group']['organization_name'];
			$booking['building'] = $this->building_bo->so->read_single($booking['building_id']);
			$booking['building_name'] = $booking['building']['name'];
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$booking = array_merge($booking, extract_values($_POST, $this->fields));
				$booking['allocation_id'] = $booking['allocation_id'] ? $booking['allocation_id'] : null;
				$this->agegroup_bo->extract_form_data($booking);
				$group = $this->group_bo->read_single(intval(phpgw::get_var('group_id', 'GET')));
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($booking);
						$this->send_mailnotification_to_group($group, lang('Booking changed'), phpgw::get_var('mail', 'POST'));
						$this->redirect(array('menuaction' => 'booking.uibooking.show', 'id'=>$booking['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'booking.js');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
			$booking['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id' => $booking['application_id']));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			self::render_template('booking_edit', array('booking' => $booking, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
		}
		
		public function show()
		{
			$booking = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$booking['bookings_link'] = self::link(array('menuaction' => 'booking.uibooking.index'));
			$booking['edit_link'] = self::link(array('menuaction' => 'booking.uibooking.edit', 'id' => $booking['id']));
			$resource_ids = '';
			foreach($booking['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$booking['resource_ids'] = $resource_ids;
			self::render_template('booking', array('booking' => $booking));
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
			$booking['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $booking['resources'][0]['building_id']));
			$booking['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $booking['group']['organization_id']));
			$booking['group_link'] = self::link(array('menuaction' => 'booking.uigroup.show', 'id' => $booking['group']['id']));
			
			$booking['edit_link'] = self::link(array('menuaction' => 'booking.uibooking.edit', 'id' => $booking['id']));
				
			$booking['when'] = pretty_timestamp($booking['from_']).' - '.pretty_timestamp($booking['to_']);
			self::render_template('booking_info', array('booking'=>$booking));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}

	}
