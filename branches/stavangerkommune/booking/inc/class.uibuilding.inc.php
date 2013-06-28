<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	class booking_uibuilding extends booking_uicommon
	{	
		public $public_functions = array
		(
			'index'			=>	true,
			'active'		=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'schedule'		=>	true,
			'properties'	=>	true,
			'toggle_show_inactive'	=>	true,
			'find_buildings_used_by' => true,
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.bobuilding');
			self::set_active_menu('booking::buildings');
			$this->fields = array('name', 'homepage', 'description', 'email', 'tilsyn_name', 'tilsyn_email', 'tilsyn_phone', 'street', 'zip_code', 'city', 'district', 'phone', 'active', 'location_code','deactivate_application','deactivate_calendar','deactivate_sendmessage','calendar_text');
		}
		
		public function properties()
		{
			$q = phpgw::get_var('query', 'str', 'REQUEST', null);
			$type_id = count(split('-', $q));
			$so = CreateObject('property.solocation');
			$ret = $so->read(array('type_id' => $type_id, 'location_code'=>$q));
			foreach($ret as &$r)
			{
				$name = array();
				for($i=1; $i<=$type_id; $i++)
					$name[] = $r['loc'.$i.'_name'];
				$r['name'] = $r['location_code']. ' ('. join(', ', $name).')';
				$r['id'] = $r['location_code'];
			}
			$locations = array('results'=>$ret, 'total_results'=>count($ret));
			return $this->yui_results($locations);
		}
		
		public function find_buildings_used_by()
		{
			if(!phpgw::get_var('phpgw_return_as') == 'json') { return; }
			
			if (($organization_id = phpgw::get_var('organization_id', 'int', 'REQUEST', null))) {
				$buildings = $this->bo->find_buildings_used_by($organization_id);
				array_walk($buildings["results"], array($this, "_add_links"), "bookingfrontend.uibuilding.show");
				return $this->yui_results($buildings);
			}
			
			return $this->yui_results(null);
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
								'type' => 'text', 
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
					'source' => self::link(array('menuaction' => 'booking.uibuilding.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Building'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'street',
							'label' => lang('Street'),
						),
						array(
							'key' => 'zip_code',
							'label' => lang('Zip code'),
						),
						array(
							'key' => 'city',
							'label' => lang('Postal City'),
						),
						array(
							'key' => 'district',
							'label' => lang('District'),
						),
						array(
							'key' => 'active',
							'label' => lang('Active'),
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
					'value' => lang('New building'),
					'href' => self::link(array('menuaction' => 'booking.uibuilding.add'))
				));
			}

			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			
			$buildings = $this->bo->read();
			foreach($buildings['results'] as &$building)
			{
				$building['link'] = $this->link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
#				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($buildings);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = extract_values($_POST, $this->fields);
				$building['active'] = '1';
				$errors = $this->bo->validate($building);
				if(!$errors)
				{
					$receipt = $this->bo->add($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$this->use_yui_editor();
			self::render_template('building_form', array('building' => $building, 'new_form' => true));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$building = $this->bo->read_single($id);
			$building['id'] = $id;
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
			$building['top-nav-bar-buildings'] = lang('Buildings');
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = array_merge($building, extract_values($_POST, $this->fields));
			
				$errors = $this->bo->validate($building);
				if(!$errors)
				{
					$receipt = $this->bo->update($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			$this->use_yui_editor();
			self::render_template('building_form', array('building' => $building));
		}
		
		public function show()
		{
			$building = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['edit_link'] = self::link(array('menuaction' => 'booking.uibuilding.edit', 'id' => $building['id']));
			$building['schedule_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id']));
			$building['message_link'] = self::link(array('menuaction' => 'booking.uisystem_message.edit', 'building_id' => $building['id']));
			$building['add_document_link'] = booking_uidocument::generate_inline_link('building', $building['id'], 'add');
			$building['add_permission_link'] = booking_uipermission::generate_inline_link('building', $building['id'], 'add');
			$building['location_link'] = self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $building['location_code']));
			if ( trim($building['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($building['homepage'])) )
			{
				$building['homepage'] = 'http://'.$building['homepage'];
			}
			self::render_template('building', array('building' => $building));
		}

		public function schedule()
		{
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), "booking.uibuilding");
			$building['datasource_url'] = self::link(array(
				'menuaction' => 'booking.uibooking.building_schedule', 
				'building_id' => $building['id'], 
				'phpgw_return_as' => 'json',
			));
			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building));
		}
	}
