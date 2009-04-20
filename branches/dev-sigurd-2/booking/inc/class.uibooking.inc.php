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
			'resource_schedule' =>  true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bobooking');
			self::set_active_menu('booking::bookings');
			$this->fields = array('name', 'resources',
								  'building_id', 'building_name', 
								  'season_id', 'season_name', 
			                      'group_id', 'group_name', 
			                      'from_', 'to_');
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
								'value' => lang('New booking'),
								'href' => self::link(array('menuaction' => 'booking.uibooking.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uibooking.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Booking Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'group_name',
							'label' => lang('Group')
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
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$bookings = $this->bo->read();
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
				array_set_default($_POST, 'resources', array());
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					$receipt = $this->bo->add($booking);
					$this->redirect(array('menuaction' => 'booking.uibooking.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'booking.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			phpgwapi_yui::load_widget('paginator');
			$lang['title'] = lang('Add Booking');
			$lang['buildings'] = lang('Buildings');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['group'] = lang('Group');
			$lang['from'] = lang('From');
			$lang['to'] = lang('To');
			$lang['season'] = lang('Season');
			$lang['date'] = lang('Date');
			$lang['resources'] = lang('Resources');
			$lang['select-building-first'] = lang('Select a building first');
			$lang['telephone'] = lang('Telephone');
			$lang['email'] = lang('Email');
			$lang['homepage'] = lang('Homepage');
			$lang['address'] = lang('Address');
			$lang['save'] = lang('Save');
			$lang['create'] = lang('Create');
			$lang['cancel'] = lang('Cancel');
			$lang['edit'] = lang('Edit');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.index'));
			self::render_template('booking_new', array('booking' => $booking, 'lang' => $lang));
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
				$errors = $this->bo->validate($booking);
				if(!$errors)
				{
					$receipt = $this->bo->update($booking);
					$this->redirect(array('menuaction' => 'booking.uibooking.show', 'id'=>$booking['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'booking.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			$lang['title'] = lang('Edit Booking');
			$lang['buildings'] = lang('Buildings');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['group'] = lang('Group');
			$lang['from'] = lang('From');
			$lang['to'] = lang('To');
			$lang['season'] = lang('Season');
			$lang['date'] = lang('Date');
			$lang['resources'] = lang('Resources');
			$lang['select-building-first'] = lang('Select a building first');
			$lang['telephone'] = lang('Telephone');
			$lang['email'] = lang('Email');
			$lang['homepage'] = lang('Homepage');
			$lang['address'] = lang('Address');
			$lang['save'] = lang('Save');
			$lang['create'] = lang('Create');
			$lang['cancel'] = lang('Cancel');
			$lang['edit'] = lang('Edit');
			$booking['resources_json'] = json_encode(array_map('intval', $booking['resources']));
			$booking['cancel_link'] = self::link(array('menuaction' => 'booking.uibooking.show', 'id' => $booking['id']));
			self::render_template('booking_edit', array('booking' => $booking, 'lang' => $lang));
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
			$lang['title'] = lang('Bookings');
			$lang['buildings'] = lang('Buildings');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['group'] = lang('Group');
			$lang['from'] = lang('From');
			$lang['to'] = lang('To');
			$lang['season'] = lang('Season');
			$lang['date'] = lang('Date');
			$lang['resources'] = lang('Resources');
			$lang['select-building-first'] = lang('Select a building first');
			$lang['telephone'] = lang('Telephone');
			$lang['email'] = lang('Email');
			$lang['homepage'] = lang('Homepage');
			$lang['address'] = lang('Address');
			$lang['save'] = lang('Save');
			$lang['create'] = lang('Create');
			$lang['cancel'] = lang('Cancel');
			$lang['edit'] = lang('Edit');
			self::render_template('booking', array('booking' => $booking, 'lang' => $lang));
		}
	}
