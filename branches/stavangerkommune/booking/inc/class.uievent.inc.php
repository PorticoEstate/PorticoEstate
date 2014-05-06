<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('phpgwapi.send');

	class booking_uievent extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'edit'			=>	true,
			'delete'			=>	true,
			'info'			=>	true,
			'toggle_show_inactive'	=>	true,
		);
		
		protected $customer_id;

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boevent');
			$this->customer_id = CreateObject('booking.customer_identifier');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			$this->organization_bo = CreateObject('booking.boorganization');
			$this->resource_bo = CreateObject('booking.boresource');
			self::set_active_menu('booking::applications::events');
			$this->fields = array('activity_id', 'description',
										'resources', 'cost', 'application_id',
										'building_id', 'building_name', 
										'contact_name', 'contact_email', 'contact_phone',
										'from_', 'to_', 'active', 'audience', 'reminder',
										'is_public', 'sms_total', 'customer_internal');
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
							array(
								'type' => 'link',
								'value' => lang('New event'),
								'href' => self::link(array('menuaction' => 'booking.uievent.add'))
							),
							array('type' => 'filter', 
								'name' => 'buildings',
                                'text' => lang('Building').':',
                                'list' => $this->bo->so->get_buildings(),
							),
							array('type' => 'filter', 
								'name' => 'activities',
                                'text' => lang('Activity').':',
                                'list' => $this->bo->so->get_activities_main_level(),
							),
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
					'source' => self::link(array('menuaction' => 'booking.uievent.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'description',
							'label' => lang('Event'),
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
						),
						array(
							'key' => 'customer_organization_name',
							'label' => lang('Organization'),
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact'),
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
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
							'key' => 'active',
							'label' => lang('Active')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}

		public function index_json()

		{
			if(isset($_SESSION['showall']))
			{
        		unset($filters['building_name']);
                unset($filters['activity_id']);
			} else {
                $testdata =  phpgw::get_var('buildings', 'int', 'REQUEST', null);
                if ($testdata != 0) {
                    $filters['building_name'] = $this->bo->so->get_building(phpgw::get_var('buildings', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['building_name']);                
                }
                $testdata2 =  phpgw::get_var('activities', 'int', 'REQUEST', null);
                if ($testdata2 != 0) {
                    $filters['activity_id'] = $this->bo->so->get_activities(phpgw::get_var('activities', 'int', 'REQUEST', null));        
                } else {
                    unset($filters['activity_id']);                
                }
            }
            
			$params = array(
				'start' => phpgw::get_var('startIndex', 'int', 'REQUEST', 0),
				'results' => phpgw::get_var('results', 'int', 'REQUEST', null),
				'query'	=> phpgw::get_var('query'),
				'sort'	=> phpgw::get_var('sort'),
				'dir'	=> phpgw::get_var('dir'),
				'filters' => $filters
			);
			$events = $this->bo->so->read($params);

			foreach($events['results'] as &$event)
			{
				$event['from_'] = pretty_timestamp($event['from_']);
				$event['to_'] = pretty_timestamp($event['to_']);
			}

			array_walk($events["results"], array($this, "_add_links"), "booking.uievent.edit");
			return $this->yui_results($events);
		}
		
		private function _combine_dates($from_, $to_)
		{
			return array('from_' => $from_, 'to_' => $to_);
		}

		protected function get_customer_identifier() {
			return $this->customer_id;
		}
		
		protected function extract_customer_identifier(&$data) {
			$this->get_customer_identifier()->extract_form_data($data);
		}
		
		protected function validate_customer_identifier(&$data) {
			return $this->get_customer_identifier()->validate($data);
		}
		
		protected function install_customer_identifier_ui(&$entity) {
			$this->get_customer_identifier()->install($this, $entity);
		}
		
		protected function validate(&$entity) {
			$errors = array_merge($this->validate_customer_identifier($entity), $this->bo->validate($entity));
			return $errors;
		}
		
		protected function extract_form_data($defaults = array()) {
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$this->agegroup_bo->extract_form_data($entity);
			$this->extract_customer_identifier($entity);
			return $entity;
		}
		
		protected function extract_and_validate($defaults = array()) {
			$entity = $this->extract_form_data($defaults);
			$errors = $this->validate($entity);
			return array($entity, $errors);
		}
		
		protected function add_comment(&$event, $comment, $type = 'comment') {
			$event['comments'][] = array(
				'time'=> 'now',
				'author'=>$this->current_account_fullname(),
				'comment'=>$comment,
				'type' => $type
			);
		}

	    protected function create_sendt_mail_notification_comment_text($event,$errors)
	    {
			$data = array();

			foreach($errors['allocation'][0] as $e)
			{ 	
				foreach($event['resources'] as $res)
				{
					$time = $this->bo->so->get_overlap_time_info($res,$e,'allocation');
					
					$from_ = new DateTime($time['from']);
					$to_ = new DateTime($time['to']);
					$date = $from_->format('d-m-Y');
					$start = $from_->format('H:i');
					$end = $to_->format('H:i');

					$resource = $this->bo->so->get_resource_info($res);
					$_mymail = $this->bo->so->get_contact_mail($e,'allocation');
				
					$a = $_mymail[0];
					if(array_key_exists($a,$data))
					{
						$data[$a][] = array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end ); 	
					}
					else
					{
						$data[$a] =  array( array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end )); 
					}
					if ($_mymail[1])
					{
						$b = $_mymail[1];
						if(array_key_exists($a,$data))
						{
							$data[$b][] = array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end ); 	
						}
						else
						{
							$data[$b] =  array( array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end )); 
						}
					}
				}
			}

			foreach($errors['booking'][0] as $e)
			{ 	
				foreach($event['resources'] as $res)
				{
					$time = $this->bo->so->get_overlap_time_info($res,$e,'booking');

					$from_ = new DateTime($time['from']);
					$to_ = new DateTime($time['to']);
					$date = $from_->format('d-m-Y');
					$start = $from_->format('H:i');
					$end = $to_->format('H:i');

					$resource = $this->bo->so->get_resource_info($res);
					$_mymail = $this->bo->so->get_contact_mail($e,'booking');

					$a = $_mymail[0];
					if(array_key_exists($a,$data))
					{
						$data[$a][] = array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end ); 	
					}
					else
					{
						$data[$a] =  array( array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end )); 
					}
					if ($_mymail[1])
					{
						$b = $_mymail[1];
						if(array_key_exists($a,$data))
						{
							$data[$b][] = array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end ); 	
						}
						else
						{
							$data[$b] =  array( array('date' => $date, 'building' => $event['building_name'], 'resource' => $resource['name'], 'start' => $start, 'end' => $end )); 
						}
					}

				}
			}
			return $data;
 	   }

		public function add()
		{
			$errors = array();
			$event = array('customer_internal' => 0);
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{

				array_set_default($_POST, 'from_', array());
				array_set_default($_POST, 'to_', array());
				$event['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);

				array_set_default($_POST, 'resources', array());
				$event['active'] = '1';
				$event['completed'] = '0';
				
				array_set_default($event, 'audience', array());
				array_set_default($event, 'agegroups', array());
				$event['secret'] = $this->generate_secret();
				$event['is_public'] = 1;
				$event['building_name'] = $_POST['building_name'];

				if ($_POST['organization_name'] || $_POST['org_id2']) {
					if ($_POST['organization_name']) {
						$event['customer_organization_name'] = $_POST['organization_name'];
						$event['customer_organization_id'] = $_POST['organization_id'];
						$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'POST')));
					} else {
						$orgid = $this->bo->so->get_org($_POST['org_id2']);
						$event['org_id2'] = $_POST['org_id2'];
						$event['customer_organization_name'] = $orgid['name'];
						$event['customer_organization_id'] = $orgid['id'];
						$organization = $this->organization_bo->read_single(intval($orgid['id']));
					}
										
					if ($organization['customer_internal'] == 0) {
						$_POST['customer_identifier_type'] = $organization['customer_identifier_type'];
						$_POST['customer_internal'] = $organization['customer_internal'];
						if (strlen($organization['customer_organization_number']) == 9) {
							$_POST['customer_organization_number'] = $organization['customer_organization_number'];
						} else {
							$errors['organization_number'] = lang('The organization number is wrong or not present');
						}
					} else {
						$_POST['customer_identifier_type'] = 'organization_number';
						$_POST['customer_internal'] = $organization['customer_internal'];
						if ((strlen($organization['customer_number']) == 6) || (strlen($organization['customer_number']) == 5)) {
 							$_POST['customer_organization_number'] = $organization['customer_number'];
						} else {
							$errors['resource_number'] = lang('The resource number is wrong or not present');
						}
					}
                    if ($organization['contacts'][0]['name'] != '') {
                        $_POST['contact_name'] = $organization['contacts'][0]['name'];
                        $_POST['contact_email'] = $organization['contacts'][0]['email'];
                        $_POST['contact_phone'] = $organization['contacts'][0]['phone'];
                    } else {
                        $_POST['contact_name'] = $organization['contacts'][1]['name'];
                        $_POST['contact_email'] = $organization['contacts'][1]['email'];
                        $_POST['contact_phone'] = $organization['contacts'][1]['phone'];
                    }
				}
				if (!$_POST['application_id'])
				{
                   $temp_errors = array();
					foreach( $event['dates'] as $checkdate)				
					{
						$event['from_'] = $checkdate['from_'];
						$_POST['from_'] = $checkdate['from_'];
						$event['to_'] = $checkdate['to_'];
						$_POST['to_'] = $checkdate['to_'];
						list($event, $errors) = $this->extract_and_validate($event);
						$time_from = split(" ",$_POST['from_']);
						$time_to = split(" ",$_POST['to_']);
						if ($time_from[0] == $time_to[0]) 
						{
							if ($time_from[1] >= $time_to[1])
							{
								$errors['time'] = lang('Time is set wrong');
							}
						}  
                        if ($errors != array()) {
                            $temp_errors = $errors;
                        }
					}						
                    $errors = $temp_errors;
				}
				else
				{
					list($event, $errors) = $this->extract_and_validate($event);
					$time_from = split(" ",$_POST['from_']);
					$time_to = split(" ",$_POST['to_']);
					if ($time_from[0] == $time_to[0]) {
						if ($time_from[1] >= $time_to[1])
						{
							$errors['time'] = lang('Time is set wrong');
						}
					}  
				}
				
				if ($_POST['cost'] != 0 and !$event['customer_organization_number'] and !$event['customer_ssn']) {
					$errors['invoice_data'] = lang('There is set a cost, but no invoice data is filled inn');
				}
				if(($_POST['organization_name'] != '' or $_POST['org_id2'] != '') and isset($errors['contact_name'])) {
					$errors['contact_name'] = lang('Organization is missing booking charge');
				}
				if(!$errors['event'] && !$errors['from_'] && !$errors['time'] && !$errors['invoice_data'] && !$errors['resource_number'] && !$errors['organization_number'] && !$errors['contact_name'] && !$errors['cost'] && !$errors['activity_id'])
				{
					if (!$_POST['application_id'])
					{
						$allids = array();
						foreach( $event['dates'] as $checkdate)				
						{
							$event['from_'] = $checkdate['from_'];
							$event['to_'] = $checkdate['to_'];

							unset($event['comments']);
							if (count($event['dates']) < 2)					
							{
								$this->add_comment($event, lang('Event was created'));
								$receipt = $this->bo->add($event);
							}					
							else
							{
								$this->add_comment($event, lang('Multiple Events was created'));
								$receipt = $this->bo->add($event);
								$allids[] = array($receipt['id']);
							}
						}
						if ($allids) 
						{ 
							$this->bo->so->update_comment($allids);
                            $this->bo->so->update_id_string();
						}
					}
					else
					{
						$this->add_comment($event, lang('Event was created'));
						$receipt = $this->bo->add($event);
                        $this->bo->so->update_id_string();
					}
					$this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$receipt['id'], 'secret'=>$event['secret'], 'warnings'=>$errors));
				}
			}
            if ($errors['event']) {
                $errors['warning'] = lang('NB! No data will be saved, if you navigate away you will loose all.'); 
            }
			$default_dates = array_map(array(self, '_combine_dates'), '','');
			array_set_default($event, 'dates', $default_dates);

			if (!phpgw::get_var('from_report', 'POST'))
			{
					$this->flash_form_errors($errors);
			}

			self::add_javascript('booking', 'booking', 'event.js');
			array_set_default($event, 'resources', array());
			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uievent.index'));
			array_set_default($event, 'cost', '0');
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			
			$this->install_customer_identifier_ui($event);
			
			$this->add_template_helpers();
			self::render_template('event_new', array('event' => $event, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
		}

		private function send_mailnotification($receiver, $subject, $body)
		{
			$send = CreateObject('phpgwapi.send');

			$config	= CreateObject('phpgwapi.config','booking');
			$config->read();
			$from = isset($config->config_data['email_sender']) && $config->config_data['email_sender'] ? $config->config_data['email_sender'] : "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";

			if (strlen(trim($body)) == 0) 
			{
				return false;
			}
			
			if (strlen($receiver) > 0) 
			{
				try
				{
					$send->msg('email', $receiver, $subject, $body, '', '', '', $from, '', 'plain');
				}
				catch (phpmailerException $e)
				{
					// TODO: Inform user if something goes wrong
				}
			}
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$event = $this->bo->read_single($id);
			$building_info = $this->bo->so->get_building_info($id);
			$event['building_id'] = $building_info['id'];
			$event['building_name'] = $building_info['name'];
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
			}
			list($event, $errors) = $this->extract_and_validate($event);

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if (!$_POST['organization_name']) {
					$event['customer_organization_name'] = Null;
					$event['customer_organization_id'] = Null;
				}
				array_set_default($_POST, 'resources', array());
			
				if ($_POST['organization_name']) {
					$event['customer_organization_name'] = $_POST['organization_name'];
					$event['customer_organization_id'] = $_POST['organization_id'];
					$organization = $this->organization_bo->read_single(intval(phpgw::get_var('organization_id', 'POST')));

					if ($organization['customer_internal'] == 0) {					
						$event['customer_identifier_type'] = $organization['customer_identifier_type'];
						$event['customer_internal'] = $organization['customer_internal'];
						if (strlen($organization['customer_organization_number']) == 9) {
							$event['customer_organization_number'] = $organization['customer_organization_number'];
						} else {
							$errors['organization_number'] = lang('The organization number is wrong or not present');
						}
					} else {
						$event['customer_identifier_type'] = 'organization_number';
						$event['customer_internal'] = $organization['customer_internal'];
						if ((strlen($organization['customer_number']) == 6) || (strlen($organization['customer_number']) == 5)) {
 							$event['customer_organization_number'] = $organization['customer_number'];
						} else {
							$errors['resource_number'] = lang('The resource number is wrong or not present');
						}
					}
				} elseif ($_POST['customer_identifier_type'] == 'ssn') {
					$event['customer_identifier_type'] = 'ssn';
					$event['customer_ssn'] = $_POST['customer_ssn'];
					
				} elseif ($_POST['customer_identifier_type'] == 'organization_number') {
					$event['customer_identifier_type'] = 'organization_number';
					$event['customer_organization_number'] = $_POST['customer_organization_number'];
				}

				if ($_POST['cost'] != 0 and !$event['customer_organization_number'] and !$event['customer_ssn']) {
					$errors['invoice_data'] = lang('There is set a cost, but no invoice data is filled inn');
				} 
				if(!$errors['event'] and !$errors['resource_number'] and !$errors['organization_number'] and !$errors['invoice_data']  && !$errors['contact_name'] && !$errors['cost'])
				{
					if (( phpgw::get_var('sendtorbuilding', 'POST') || phpgw::get_var('sendtocontact', 'POST') || phpgw::get_var('sendtocollision', 'POST')) && phpgw::get_var('active', 'POST'))
					{

                        if(phpgw::get_var('sendtocollision', 'POST') || phpgw::get_var('sendtocontact', 'POST') || phpgw::get_var('sendtorbuilding', 'POST'))
						{
							$maildata = $this->create_sendt_mail_notification_comment_text($event,$errors);
                            
                            if(phpgw::get_var('sendtocollision', 'POST'))
                            {
							    $comment_text_log = "<span style='color: green;'>".lang('Message sent about the changes in the reservations').':</span><br />';
                                $res = array();
                                $resname= '';
                                foreach ($event['resources'] as $resid) {
                                    $res = $this->bo->so->get_resource_info($resid);
                                    $resname .= $res['name'].', ';
                                }
                                $comment_text_log .= $event['building_name']." (".substr($resname,0,-2).") ".pretty_timestamp($event['from_'])." - ".pretty_timestamp($event['to_']);
							    $this->add_comment($event, $comment_text_log);
    		                }
							if(phpgw::get_var('sendtocollision', 'POST'))
							{
								
                                $subject = $config->config_data['event_conflict_mail_subject'];
                                $body = "<p>".$config->config_data['event_mail_conflict_contact_active_collision']."\n".phpgw::get_var('mail', 'POST')."\n";
                                $body .= '<br /><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].'</a></p>';
                                $body .= "<p>".$config->config_data['application_mail_signature']."</p>";
								$mail_sendt_to = '';
								foreach (array_keys($maildata) as $mail)
								{
                                    $mailbody = '';    								
                                    $comment_text_log = "Reserverasjoner som har blitt overskrevet: \n";
									$mail_sendt_to = $mail_sendt_to.' '.$mail;
									foreach($maildata[$mail] as $data)
									{
										$comment_text_log .= $data['date'].', '.$data['building'].', '.$data['resource'].', Kl. '.$data['start'].' - '.$data['end']." \n";
									}
                                    $mailbody .= $body."<pre>".$comment_text_log."</pre>";
									$this->send_mailnotification($mail, $subject, $mailbody);
								}
								if (strpos($mail_sendt_to,'@') !== False)				
								{
									$comment = "<p>Melding om konflikt er sendt til".$mail_sendt_to."<br />\n".phpgw::get_var('mail', 'POST')."</p>";
									$this->add_comment($event,$comment);			
								}
							}
							if(phpgw::get_var('sendtocontact', 'POST'))
							{
                                $subject = $config->config_data['event_change_mail_subject'];
                                $body = "<p>".$config->config_data['event_change_mail']."\n".phpgw::get_var('mail', 'POST');
                                $body .= '<br /><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].'</a></p>';
								$this->send_mailnotification($event['contact_email'], $subject, $body);
								$comment = $comment_text_log.'<br />Denne er sendt til '.$event['contact_email'];
								$this->add_comment($event,$comment);			
							}
							if(phpgw::get_var('sendtorbuilding', 'POST'))
							{
								
                                $subject = $config->config_data['event_mail_building_subject'];

                                $body = "<p>".$config->config_data['event_mail_building']."<br />\n".phpgw::get_var('mail', 'POST')."</p>";

								if ($event['customer_organization_name']) {
									$username = $event['customer_organization_name'];
								} else {
									$username = $event['contact_name'];
								}
                                $res = array();
                                $resname= '';
                                foreach ($event['resources'] as $resid) {
                                    $res = $this->bo->so->get_resource_info($resid);
                                    $resname .= $res['name'].', ';
                                }
                                $resources = $event['building_name']." (".substr($resname,0,-2).") ".pretty_timestamp($event['from_'])." - ".pretty_timestamp($event['to_']);

								$body  .= '<p>'.$username.' har fått innvilget et arrangement i '.$resources.".";
                                $body .= '<br /><a href="'.$link.'">Link til '.$config->config_data['application_mail_systemname'].'</a></p>';
                                $body .= "<p>".$config->config_data['application_mail_signature']."</p>";

								$sendt = 0;
								$mail_sendt_to = '';
								if($event['contact_email']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$event['contact_email'];
                                    $this->send_mailnotification($event['contact_email'], $subject, $body);
								} 
								if($building_info['email']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$building_info['email'];
									$this->send_mailnotification($building_info['email'], $subject, $body);
								} 
								if ($building_info['tilsyn_email']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$building_info['tilsyn_email'];
									$this->send_mailnotification($building_info['tilsyn_email'], $subject, $body);
								} 
								if ($building_info['tilsyn_email2']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$building_info['tilsyn_email2'];
									$this->send_mailnotification($building_info['sendtorbuilding_email2'], $subject, $body);
								}
								if ($_POST['sendtorbuilding_email1']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$_POST['sendtorbuilding_email1'];
									$this->send_mailnotification($_POST['sendtorbuilding_email1'], $subject, $body);
								} 
								if ($_POST['sendtorbuilding_email2']) {
									$sendt++;
									$mail_sendt_to = $mail_sendt_to.' '.$_POST['sendtorbuilding_email2'];
									$this->send_mailnotification($_POST['sendtorbuilding_email2'], $subject, $body);
								}
								if ($sendt <= 0) {
									$errors['mailtobuilding'] = lang('Unable to send warning, No mailadresses found');
								} 
								else 
								{
                                    $comment_text_log = phpgw::get_var('mail', 'POST');
									$comment = 'Melding om endring er sendt til ansvarlig for bygg: '.$mail_sendt_to.'<br />'.$comment_text_log;
									$this->add_comment($event,$comment);			
								}
							}
						}
                    }
					$receipt = $this->bo->update($event);
					$this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$event['id']));
				}
                if (!phpgw::get_var('active', 'POST')) {

                    $subject = $config->config_data['event_canceled_mail_subject'];
                    $body = $config->config_data['event_canceled_mail']."\n".phpgw::get_var('mail', 'POST');

                    if ($event['customer_organization_name']) {
                        $comment_text_log = $event['customer_organization_name'];
                    } else {
                        $comment_text_log = $event['contact_name'];
                    }
                    $comment_text_log = $comment_text_log.' sitt arrangement i '.$event['building_name'].' '.date('d-m-Y H:i', strtotime($event['from_']))." har blitt kansellert.";

                    $body .= "<br />\n".$comment_text_log;
                    $body = html_entity_decode($body);

                    $sendt = 0;
                    $mail_sendt_to = '';
                    if($building_info['email']) {
                        $sendt++;
                        $mail_sendt_to = $mail_sendt_to.' '.$building_info['email'];
                        $this->send_mailnotification($building_info['email'], $subject, $body);
                    }
                    if ($building_info['tilsyn_email']) {
                        $sendt++;
                        $mail_sendt_to = $mail_sendt_to.' '.$building_info['tilsyn_email'];
                        $this->send_mailnotification($building_info['tilsyn_email'], $subject, $body);

                    }
                    if ($building_info['tilsyn_email2']) {
                        $sendt++;
                        $mail_sendt_to = $mail_sendt_to.' '.$building_info['tilsyn_email2'];
                        $this->send_mailnotification($building_info['tilsyn_email2'], $subject, $body);
                    }
                    if ($_POST['sendtorbuilding_email1']) {
                        $sendt++;
                        $mail_sendt_to = $mail_sendt_to.' '.$_POST['sendtorbuilding_email1'];
                        $this->send_mailnotification($_POST['sendtorbuilding_email1'], $subject, $body);

                    }
                    if ($_POST['sendtorbuilding_email2']) {
                        $sendt++;
                        $mail_sendt_to = $mail_sendt_to.' '.$_POST['sendtorbuilding_email2'];
                        $this->send_mailnotification($_POST['sendtorbuilding_email2'], $subject, $body);
                    }
                    if ($sendt <= 0) {
                        $errors['mailtobuilding'] = lang('Unable to send warning, No mailadresses found');
                    }
                    else
                    {
                        $comment = '<span style="color:red;">Dette arrangemenet er kanselert</span>. Denne er sendt til '.$mail_sendt_to.'<br />'.phpgw::get_var('mail', 'POST');
                        $this->add_comment($event,$comment);
                    }
                    $receipt = $this->bo->update($event);
                    $this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$event['id']));

                }
			}

			if($errors['allocation'])
			{	
				$errors['allocation'] = lang('Event created, Overlaps with existing allocation, Remember to send a notification');
			}
			elseif($errors['booking'])
			{
				$errors['booking'] = lang('Event created, Overlaps with existing booking, Remember to send a notification');
			}
			$this->flash_form_errors($errors);
			if ($customer['customer_identifier_type'])
			{
				$event['customer_identifier_type'] = $customer['customer_identifier_type'];
				$event['customer_ssn'] = $customer['customer_ssn'];
				$event['customer_organization_number'] = $customer['customer_organization_number'];
				$event['customer_internal'] = $customer['customer_internal'];
			}			

			self::add_javascript('booking', 'booking', 'event.js');
			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id'=> $event['application_id']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uievent.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
#			$comments = array_reverse($event['comments']);
			$comments = $this->bo->so->get_ordered_comments($id);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$this->install_customer_identifier_ui($event);
			$this->add_template_helpers();
			self::render_template('event_edit', array('event' => $event, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience, 'comments' => $comments));
		}
		public function delete()
		{
			$event_id = phpgw::get_var('event_id', 'GET');
			$application_id = phpgw::get_var('application_id', 'GET');

			if ($GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking')) {
				$this->bo->so->delete_event($event_id);
			}
			if (isset($application_id)) {
				$this->redirect(array('menuaction' => 'booking.uiapplication.show', 'id'=>$application_id));
			} else {
				$this->redirect(array('menuaction' => 'booking.uievent.index'));
			}

		}
		public function info()
		{
			$event = $this->bo->read_single(intval(phpgw::get_var('id', 'GET')));
			$resources = $this->resource_bo->so->read(array('filters'=>array('id'=>$event['resources']), 'sort'=>'name'));
			$event['resources'] = $resources['results'];
			$res_names = array();
			foreach($event['resources'] as $res)
			{
				$res_names[] = $res['name'];
			}
			$event['resource'] = phpgw::get_var('resource', 'GET');
			$event['resource_info'] = join(', ', $res_names);
			$event['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $event['resources'][0]['building_id']));
			$event['org_link'] = self::link(array('menuaction' => 'booking.uiorganization.show', 'id' => $event['organization_id']));
			$event['add_link'] = self::link(array('menuaction' => 'booking.uibooking.add', 'allocation_id'=>$event['id'], 'from_'=>$event['from_'], 'to_'=>$event['to_'], 'resource'=>$event['resource']));
			$event['when'] = pretty_timestamp($event['from_']).' - '.pretty_timestamp($event['to_']);

			$event['edit_link'] = self::link(array('menuaction' => 'booking.uievent.edit', 'id' => $event['id']));

			self::render_template('event_info', array('event'=>$event));
			$GLOBALS['phpgw']->xslttpl->set_output('wml'); // Evil hack to disable page chrome
		}

	}
