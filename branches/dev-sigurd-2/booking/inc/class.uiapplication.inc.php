<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiapplication extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boapplication');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->agegroup_bo = CreateObject('booking.boagegroup');
			$this->audience_bo = CreateObject('booking.boaudience');
			self::set_active_menu('booking::applications');
			$this->fields = array('description', 'resources', 'activity_id', 'building_id', 'building_name', 'contact_name', 'contact_email', 'contact_phone', 'audience');
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
								'value' => lang('New application'),
								'href' => self::link(array('menuaction' => 'booking.uiapplication.add'))
							),
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiapplication.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
						),
						array(
							'key' => 'created',
							'label' => lang('Created')
						),
						array(
							'key' => 'modified',
							'label' => lang('last modified')
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact')
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
			$applications = $this->bo->read();
			foreach($applications['results'] as &$application)
			{
				$application['link'] = $this->link(array('menuaction' => 'booking.uiapplication.edit', 'id' => $application['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $applications['total_records'], 
					"Result" => $applications['results']
				)
			);
			return $data;
		}

		private function _combine_dates($from_, $to_)
		{
			return array('from_' => $from_, 'to_' => $to_);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$application = extract_values($_POST, $this->fields);
				$application['agegroups'] = array();
				$this->agegroup_bo->extract_form_data(&$application);
				$application['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);
				$application['status'] = 'NEW';
				$application['created'] = 'now';
				$application['modified'] = 'now';
				$errors = $this->bo->validate($application);
				if(!$errors)
				{
					$receipt = $this->bo->add($application);
					$this->redirect(array('menuaction' => 'booking.uiapplication.edit', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'application.js');
			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			$application['cancel_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application_new', array('application' => $application, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
		}


		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$application = $this->bo->read_single($id);
			$building_info = $this->bo->so->get_building_info($id);
			$application['building_id'] = $building_info['id'];
			$application['building_name'] = $building_info['name'];
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$application = array_merge($application, extract_values($_POST, $this->fields));
				$this->agegroup_bo->extract_form_data(&$application);
				$errors = $this->bo->validate($application);
				$application['dates'] = array_map(array(self, '_combine_dates'), $_POST['from_'], $_POST['to_']);
				if(!$errors)
				{
					$receipt = $this->bo->update($application);
					$this->redirect(array('menuaction' => 'booking.uiapplication.edit', 'id'=>$application['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'application.js');
			$application['resources_json'] = json_encode(array_map('intval', $application['resources']));
			$application['cancel_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			self::render_template('application_edit', array('application' => $application, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
		}
		
		public function show()
		{
			$application = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$application['applications_link'] = self::link(array('menuaction' => 'booking.uiapplication.index'));
			$application['edit_link'] = self::link(array('menuaction' => 'booking.uiapplication.edit', 'id' => $application['id']));
			$resource_ids = '';
			foreach($application['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$application['resource_ids'] = $resource_ids;
			self::render_template('application', array('application' => $application));
		}
	}
