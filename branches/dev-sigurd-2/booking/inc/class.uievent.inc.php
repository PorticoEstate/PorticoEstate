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
			array_walk($events["results"], array($this, "_add_links"), "booking.uievent.edit");
			return $this->yui_results($events);
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

		public function add()
		{
			$errors = array();
			$event = array('customer_internal' => 1); 
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$event['active'] = '1';
				$event['completed'] = '0';
				
				array_set_default($event, 'audience', array());
				array_set_default($event, 'agegroups', array());
				$event['secret'] = $this->generate_secret();
				$event['is_public'] = 1;
				
				list($event, $errors) = $this->extract_and_validate($event);
				if(!$errors)
				{
					$receipt = $this->bo->add($event);
					$this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$receipt['id'], 'secret'=>$event['secret']));
				}
			}
			$this->flash_form_errors($errors);
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
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				
				list($event, $errors) = $this->extract_and_validate($event);
				
				if(!$errors)
				{
					$receipt = $this->bo->update($event);
					$this->send_mailnotification($event['contact_email'], lang('Event changed'), phpgw::get_var('mail', 'POST'));
					$this->redirect(array('menuaction' => 'booking.uievent.edit', 'id'=>$event['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'event.js');
			$event['resources_json'] = json_encode(array_map('intval', $event['resources']));
			$event['application_link'] = self::link(array('menuaction' => 'booking.uiapplication.show', 'id'=> $event['application_id']));
			$event['cancel_link'] = self::link(array('menuaction' => 'booking.uievent.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$this->install_customer_identifier_ui($event);
			
			$this->add_template_helpers();
			self::render_template('event_edit', array('event' => $event, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
		}

	}
