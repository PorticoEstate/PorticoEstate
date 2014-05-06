
<?php
	phpgw::import_class('booking.uievent');

	class bookingfrontend_uievent extends booking_uievent
	{
		public $public_functions = array
		(
			'info'				=>	true,
			'report_numbers' 	=>	true,
            'cancel'			=>	true,
            'edit' 	            =>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->resource_bo = CreateObject('booking.boresource');
			$this->building_bo = CreateObject('booking.bobuilding');
            $this->organization_bo = CreateObject('booking.boorganization');
            $this->booking_bo = CreateObject('booking.bobooking');
        }
		
        public function edit()
        {
            $id = intval(phpgw::get_var('id', 'GET'));
            $event = $this->bo->read_single($id);
            $building_info = $this->bo->so->get_building_info($id);
            $event['building_id'] = $building_info['id'];
            $event['building_name'] = $building_info['name'];
            $bouser = CreateObject('bookingfrontend.bouser');
            $config	= CreateObject('phpgwapi.config','booking');
            $config->read();

            $external_site_address = isset($config->config_data['external_site_address']) && $config->config_data['external_site_address'] ? $config->config_data['external_site_address'] : $GLOBALS['phpgw_info']['server']['webserver_url'];
            $link = $external_site_address.'/bookingfrontend/?menuaction=bookingfrontend.uibuilding.schedule&id='.$event['building_id']."&date=".substr($event['from_'],0,-9);
            $errors = array();
            $customer = array();

            if ($event['customer_identifier_type'])
            {
                $customer['customer_identifier_type'] = $event['customer_identifier_type'];
                $customer['customer_ssn'] = $event['customer_ssn'];
                $customer['customer_organization_number'] = $event['customer_organization_number'];
                $customer['customer_internal'] = $event['customer_internal'];
                $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
                $customer['customer_organization_id'] = $orginfo['id'];
                $customer['customer_organization_name'] = $orginfo['name'];
            } else {
                $customer['customer_organization_name'] = $event['customer_organization_name'];
                $customer['customer_organization_id'] = $event['customer_organization_id'];
                $organization = $this->organization_bo->read_single($event['customer_organization_id']);
                $customer['customer_identifier_type'] = 'organization_number';
                $customer['customer_ssn'] = $organization['customer_internal'];
                $customer['customer_organization_number'] = $organization['organization_number'];
                $customer['customer_internal'] = $organization['customer_internal'];

            }
            $mailadresses = $this->building_users($event['building_id'],$event['organization_id']);

            if(!$bouser->is_organization_admin($customer['customer_organization_id'])) {
                $date = substr($event['from_'], 0, 10);
                $this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=> $event['building_id'], 'date' => $date));
            }

            $currres = $event['resources'];

            list($event, $errors) = $this->extract_and_validate($event);

            if ($event['customer_organization_number']) {
                $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
                $event['customer_organization_id'] = $orginfo['id'];
                $event['customer_organization_name'] = $orginfo['name'];
            }

            $orgdate = array();
            foreach ($event['dates'] as $odate) {
                if(substr($odate['from_'],0,10) == substr($event['from_'],0,10)) {
                    $orgdate['from'] = $odate['from_'];
                    $orgdate['to'] = $odate['to_'];
                }
            }

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                $event['from_'] = substr($_POST['org_from'],0,11).$_POST['from_'].":00";
                $event['to_'] = substr($_POST['org_to'],0,11).$_POST['to_'].":00";
                array_set_default($_POST, 'resources', array());

                if ($event['from_'] < $_POST['org_from'] || $event['to_'] > $_POST['org_to']) {
                    $errors['out_of_range'] = lang("You can't extend the event, for that contact administrator");
                }

                if (sizeof($currres) != sizeof($_POST['resources'])) {
                    $errors['resource_number'] = lang("You can't change resources to the event, for that contact administrator");
                }

                if(!$errors['event'] and !$errors['resource_number'] and !$errors['organization_number'] and !$errors['invoice_data']  && !$errors['contact_name'] && !$errors['out_of_range'])
                {
                    if ($event['from_'] > $orgdate['from'] || $event['to_'] < $orgdate['to']) {
                        $this->bo->send_notification(true, $event, $mailadresses, $orgdate);
                    }
                    $message = '';
                    $this->bo->send_admin_notification(true, $event, $message, $orgdate);
                    exit;
                    $this->bo->update($event);
                    $date = substr($event['from_'], 0, 10);
                    $this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=> $event['building_id'], 'date' => $date));
                }
            }

            $this->flash_form_errors($errors);
            if ($customer['customer_identifier_type'])
            {
                $event['customer_identifier_type'] = $customer['customer_identifier_type'];
                $event['customer_ssn'] = $customer['customer_ssn'];
                $event['customer_organization_number'] = $customer['customer_organization_number'];
                $event['customer_internal'] = $customer['customer_internal'];
            }

            $date = substr($event['from_'], 0, 10);
            self::add_javascript('booking', 'booking', 'event.js');
            $event['resources_json'] = json_encode(array_map('intval', $event['resources']));
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=> $event['building_id'], 'date' => $date));
            $activities = $this->activity_bo->fetch_activities();
            $activities = $activities['results'];
            $comments = $this->bo->so->get_ordered_comments($id);
            $agegroups = $this->agegroup_bo->fetch_age_groups();
            $agegroups = $agegroups['results'];
            $audience = $this->audience_bo->fetch_target_audience();
            $audience = $audience['results'];
            $this->install_customer_identifier_ui($event);
            $this->add_template_helpers();

            self::render_template('event_edit', array('event' => $event, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience, 'comments' => $comments));
        }

        public function cancel()
        {
            $config	= CreateObject('phpgwapi.config','booking');
            $config->read();


            $event = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
            $bouser = CreateObject('bookingfrontend.bouser');
            $errors = array();

            date_default_timezone_set("Europe/Oslo");
            $currdate = new DateTime(phpgw::get_var('date'));
            $cdate = $currdate->format('Y-m-d H:m:s');
            if ($config->config_data['user_can_delete_events'] != 'yes')
            {
                $can_delete_events = 0;

            } else {
                $can_delete_events = 1;
            }
            if ($event['customer_organization_number']) {
                $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
                $event['customer_organization_id'] = $orginfo['id'];
                $event['customer_organization_name'] = $orginfo['name'];
            }

            $mailadresses = $this->building_users($event['building_id'],$event['organization_id']);
            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if($cdate > $event['to_']) {
                    if($bouser->is_organization_admin($event['customer_organization_id'])) {
                        $this->bo->send_notification(false, $event, $mailadresses);
                        $this->bo->send_admin_notification(false, $event, $_POST['message']);
                        exit;
                        if ($can_delete_events) {
                            $this->bo->so->delete_event($event['id']);
                        } else {
                            $event['active'] = 0;
                            $this->bo->update($event);
                        }
                        $date = substr($event['from_'], 0, 10);
                        $this->redirect(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id' =>$event['building_id'], 'date' => $date));

                    } else {
                        $errors['not_admin'] = lang("You can't cancel events");
                    }
                } else {
                    $errors['started'] = lang("You can't cancel event that has started, for help contacts site admin");
                }
            }
            $this->flash_form_errors($errors);
            $date = substr($event['from_'], 0, 10);
            self::add_javascript('booking', 'booking', 'event.js');
            $event['resources_json'] = json_encode(array_map('intval', $event['resources']));
            $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.schedule', 'id'=> $event['building_id'], 'date' => $date));
            $activities = $this->activity_bo->fetch_activities();
            $activities = $activities['results'];
            $this->install_customer_identifier_ui($event);
            $this->add_template_helpers();

            self::render_template('event_delete', array('event' => $event, 'activities' => $activities, 'can_delete_events' => $can_delete_events));
        }

        public function building_users($building_id) {
            $contacts = array();
            $organizations = $this->organization_bo->find_building_users($building_id);
            foreach($organizations['results'] as $key => $org)
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
                $grp_con = $this->booking_bo->so->get_group_contacts_of_organization($org['id']);
                foreach ($grp_con as $grp) {
                    if (!in_array($grp['email'], $contacts) && strstr($grp['email'], '@')) {
                        $contacts[] = $grp['email'];
                    }
                }
            }
            return $contacts;
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
            $event = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
            unset($event['comments']);
            $resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$event['resources']), 'sort'=>'name'));
            if ($event['customer_organization_number'] != '')
            {
                $orginfo = $this->bo->so->get_org($event['customer_organization_number']);
                if ($orginfo != array()) {
                    $event['customer_organization_id'] = $orginfo['id'];
                    $event['customer_organization_name'] = $orginfo['name'];
                    $orginfo['link'] = self::link(array('menuaction' => 'bookingfrontend.uiorganization.show', 'id' => $orginfo['id']));
                }
            } else {
                $orginfo = array();
            }

            $event['resources'] = $resources['results'];
            $res_names = array();
            foreach($event['resources'] as $res)
            {
                $res_names[] = $res['name'];
            }
            $event['resource_info'] = join(', ', $res_names);
            $event['building_link'] = self::link(array('menuaction' => 'bookingfrontend.uibuilding.show', 'id' => $event['resources'][0]['building_id']));
            $event['when'] = pretty_timestamp($event['from_']).' - '.pretty_timestamp($event['to_']);
            $bouser = CreateObject('bookingfrontend.bouser');
            if($bouser->is_organization_admin($event['customer_organization_id']))
            {
                $event['edit_link'] = self::link(array('menuaction' => 'bookingfrontend.uievent.edit', 'id' => $event['id']));
                $event['cancel_link'] = self::link(array('menuaction' => 'bookingfrontend.uievent.cancel', 'id' => $event['id']));
            }

            self::render_template('event_info', array('event'=>$event,'orginfo' => $orginfo, 'user_can_delete_bookings' => $user_can_delete_bookings));
            $GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
        }

        public function report_numbers()
		{
			$step = 1;
			$id = intval(phpgw::get_var('id', 'GET'));
			$event = $this->bo->read_single($id);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];

			$building_info = $this->bo->so->get_building_info($id);
			$building = $this->building_bo->read_single($building_info['id']);

			if ($event['secret'] != phpgw::get_var('secret', 'GET'))
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

				$event['agegroups'] = $temp_agegroup;
				$event['reminder'] = 2; // status set to delivered
				$errors = $this->bo->validate($event);
				if(!$errors)
				{
					$receipt = $this->bo->update($event);
					$step++;
				}
			}
			self::render_template('report_numbers', array('event_object' => $event, 'agegroups' => $agegroups, 'building' => $building, 'step' => $step));
		}
	}
