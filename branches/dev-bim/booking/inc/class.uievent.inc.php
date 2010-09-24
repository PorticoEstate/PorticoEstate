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
							'key' => 'description',
							'label' => lang('Event'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity'),
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
			$events = $this->bo->read();

			foreach($events['results'] as &$event)
			{
				$building_info = $this->bo->so->get_building_info($event['id']);
				$event['building_name'] = $building_info['name'];
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
			$event = array('customer_internal' => 1); 
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
				
				if (!$_POST['application_id'])
				{
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
					}						
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

				if(!$errors['event'] && !$errors['time'])
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
						}
					}
					else
					{
						$this->add_comment($event, lang('Event was created'));
					$receipt = $this->bo->add($event);
				}
					$this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$receipt['id'], 'secret'=>$event['secret'], 'warnings'=>$errors));
			}
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
			$errors = array();
			$customer = array();
			if ($event['customer_organization_number'])
			{
				$customer['customer_identifier_type'] = $event['customer_identifier_type'];
				$customer['customer_ssn'] = $event['customer_ssn'];
				$customer['customer_organization_number'] = $event['customer_organization_number'];
			}	
			list($event, $errors) = $this->extract_and_validate($event);
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				
				if(!$errors['event'])
				{
					if (phpgw::get_var('mail', 'POST'))
					{
						if(phpgw::get_var('sendtocollision', 'POST') || phpgw::get_var('sendtocontact', 'POST'))
						{
							$maildata = $this->create_sendt_mail_notification_comment_text($event,$errors);
							if ($maildata)
							{	
								$comment_text_log = lang('Message sent about the changes in the reservations').': ';
								foreach ($maildata as $data)
								{
									foreach ($data as $item)
									{
									$comment_text_log .= $item['date'].', '.$item['building'].', '.$item['resource'].', Kl. '.$item['start'].' - '.$item['end']." <br />";
									}
								}
								$comment_text_log .= phpgw::get_var('mail', 'POST');
								$this->add_comment($event, $comment_text_log);
							}
							if(phpgw::get_var('sendtocollision', 'POST'))
							{
								foreach (array_keys($maildata) as $mail)
								{
									$comment_text_log = lang('There are changes to your reservations').": \n";
									foreach($maildata[$mail] as $data)
									{
										$comment_text_log .= $data['date'].', '.$data['building'].', '.$data['resource'].', Kl. '.$data['start'].' - '.$data['end']." \n";
									}
									$comment_text_log .= phpgw::get_var('mail', 'POST');
									$this->send_mailnotification($mail, lang('Event changed'), $comment_text_log);
								}
							}
							if(phpgw::get_var('sendtocontact', 'POST'))
							{
								$comment_text_log = phpgw::get_var('mail', 'POST');
								$this->send_mailnotification($event['contact_email'], lang('Event changed'), $comment_text_log);
							}
						}				
						else 
						{
						$this->add_comment($event, phpgw::get_var('mail', 'POST'));
					$this->send_mailnotification($event['contact_email'], lang('Event changed'), phpgw::get_var('mail', 'POST'));
						}
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
			if ($customer['customer_organization_number'])
			{
				$event['customer_identifier_type'] = $customer['customer_identifier_type'];
				$event['customer_ssn'] = $customer['customer_ssn'];
				$event['customer_organization_number'] = $customer['customer_organization_number'];
			}			
			self::add_javascript('booking', 'booking', 'event.js');
			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id'=> $event['application_id']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uievent.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$comments = array_reverse($event['comments']);
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$this->install_customer_identifier_ui($event);
			$this->add_template_helpers();
			self::render_template('event_edit', array('event' => $event, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience, 'comments' => $comments));
		}

	}
