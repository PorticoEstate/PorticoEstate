<?php
	phpgw::import_class('booking.uicommon');

	class booking_uibooking extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
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
			self::set_active_menu('booking::applications::bookings');
			$this->fields = array('allocation_id', 'activity_id', 'resources',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'group_id', 'group_name', 
			                      'from_', 'to_', 'audience', 'active');
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
			array_walk($bookings["results"], array($this, "_add_links"), "booking.uibooking.show");
			return $this->yui_results($bookings);
		}

		public function building_schedule()
		{
		    $date = new DateTime(phpgw::get_var('date'));
			$bookings = $this->bo->building_schedule(phpgw::get_var('building_id', 'int'), $date);
			foreach($bookings['results'] as &$booking)
			{
				$booking['resource_link'] = $this->link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $booking['resource_id']));
				$booking['link'] = $this->link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
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
					try {
						$receipt = $this->bo->add($booking);
						$this->redirect(array('menuaction' => 'booking.uibooking.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'booking.js');
			array_set_default($booking, 'resources', array());
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.index'));
			$agegroups = $this->agegroup_bo->fetch_age_groups();
			$agegroups = $agegroups['results'];
			$audience = $this->audience_bo->fetch_target_audience();
			$audience = $audience['results'];
			$activities = $this->activity_bo->fetch_activities();
			$activities = $activities['results'];
			self::render_template('booking_new', array('booking' => $booking, 'activities' => $activities, 'agegroups' => $agegroups, 'audience' => $audience));
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
				$booking['allocation_id'] = $booking['allocation_id'] ? $booking['allocation_id'] : null;
				$this->agegroup_bo->extract_form_data($booking);
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($booking);
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
	}
