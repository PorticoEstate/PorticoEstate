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
			'schedule'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boresource');
			$this->activity_bo = CreateObject('booking.boactivity');
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
							array(
								'type' => 'link',
								'value' => lang('New resource'),
								'href' => self::link(array('menuaction' => 'booking.uiresource.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uiresource.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Resource Name'),
							'formatter' => 'YAHOO.booking.formatLink'
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
							'key' => 'activity_name',
							'label' => lang('Activity')
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			return $this->bo->populate_grid_data("booking.uiresource.show");
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = extract_values($_POST, array('name', 'building_id', 'building_name','description','activity_id'));

				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->add($resource);
					$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'resource_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			$activity_data = $this->activity_bo->fetch_activities();
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			self::render_template('resource_new', array('resource' => $resource, 'activitydata' => $activity_data));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, array('name', 'building_id', 'building_name','description','activity_id')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			$activity_data = $this->activity_bo->fetch_activities();
			foreach($activity_data['results'] as $acKey => $acValue)
			{
				$activity_data['results'][$acKey]['resource_id'] = $resource['activity_id'];
			}
			self::render_template('resource_edit', array('resource' => $resource, 'activitydata' => $activity_data));
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

			$lang['resource_schedule'] = lang('Resource schedule');
			$lang['prev_week'] = lang('Previous week');
			$lang['next_week'] = lang('Next week');
			$lang['week'] = lang('Week');
			$lang['buildings'] = lang('Buildings');
			$lang['schedule'] = lang('Schedule');
			$lang['time'] = lang('Time');

			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('resource_schedule', array('resource' => $resource, 'lang' => $lang));
		}
	}
