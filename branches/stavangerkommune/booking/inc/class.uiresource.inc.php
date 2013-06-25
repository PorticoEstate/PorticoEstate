<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_resource');
	phpgw::import_class('booking.uipermission_resource');

	class booking_uiresource extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'edit'			=>	true,
			'show'			=>	true,
			'schedule'		=>	true,
			'toggle_show_inactive'	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.boresource');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->fields = array('name', 'building_id', 'building_name','description','activity_id', 'active', 'type', 'sort');
			self::set_active_menu('booking::resources');
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
					'source' => self::link(array('menuaction' => 'booking.uiresource.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Resource Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'sort',
							'label' => lang('Order')
						),
						array(
							'key' => 'link',
							'hidden' => true
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building name')
						),
						array(
							'key' => 'type',
							'label' => lang('Resource Type')
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
						),
						array(
							'key' => 'building_street',
							'label' => lang('Street')
						),
						array(
							'key' => 'building_city',
							'label' => lang('Postal city')
						),
						array(
							'key' => 'building_district',
							'label' => lang('District')
						),
						array(
							'key' => 'active',
							'label' => lang('Active'),
						),
					)
				)
			);
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New resource'),
					'href' => self::link(array('menuaction' => 'booking.uiresource.add'))
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			return $this->bo->populate_grid_data("booking.uiresource.show");
		}

		public function add()
		{
			$errors = array();
			$resource = array();
			$resource['sort'] = '0';
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = extract_values($_POST, $this->fields);
				$resource['active'] = '1';
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($resource);
						$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			else
			{
				$resource['type'] = 'Location';
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'resource_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			$activity_data = $this->activity_bo->fetch_activities();
			$resource['types'] = $this->resource_types();
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$this->use_yui_editor();
			self::render_template('resource_form', array('resource' => $resource, 'activitydata' => $activity_data, 'new_form' => true));
		}
		
		protected function resource_types()
		{
			$types = array();
			foreach($this->bo->allowed_types() as $type) { $types[$type] = self::humanize($type); }
			return $types;
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['types'] = $this->resource_types();
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'resource_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			$activity_data = $this->activity_bo->fetch_activities();
			foreach($activity_data['results'] as $acKey => $acValue)
			{
				$activity_data['results'][$acKey]['resource_id'] = $resource['activity_id'];
			}
			$this->use_yui_editor();
			self::render_template('resource_form', array('resource' => $resource, 'activitydata' => $activity_data));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiresource.edit', 'id' => $resource['id']));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['schedule_link'] = self::link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $resource['id']));
			$resource['add_document_link'] = booking_uidocument::generate_inline_link('resource', $resource['id'], 'add');
			$resource['add_permission_link'] = booking_uipermission::generate_inline_link('resource', $resource['id'], 'add');
			$data = array(
				'resource'	=>	$resource
			);
			
			self::render_template('resource', $data);
		}

		public function schedule()
		{
			$resource = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), 'booking.uibuilding', 'booking.uiresource');
			$resource['datasource_url'] = self::link(array(
				'menuaction' => 'booking.uibooking.resource_schedule', 
				'resource_id' => $resource['id'], 
				'phpgw_return_as' => 'json',
			));
			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('resource_schedule', array('resource' => $resource));
		}
	}
