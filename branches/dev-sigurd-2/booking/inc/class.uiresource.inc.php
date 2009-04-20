<?php
	phpgw::import_class('booking.uicommon');

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
							'key' => 'description',
							'label' => lang('Description')
						),
						array(
							'key' => 'activity_id',
							'label' => lang('Activity')
						)
					)
				)
			);
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			return $this->bo->populateGridData("booking.uiresource.show");
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
			$activity_data = $this->bo->fetchActivities();
			$lang['activity'] = lang('Activity');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			self::render_template('resource_new', array('resource' => $resource, 'activitydata' => $activity_data, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
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
			$activity_data = $this->bo->fetchActivities();
			$lang['activity'] = lang('Activity');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['save'] = lang('Save');
			foreach($activity_data['results'] as $acKey => $acValue)
			{
				$activity_data['results'][$acKey]['resource_id'] = $resource['activity_id'];
			}
			self::render_template('resource_edit', array('resource' => $resource, 'activitydata' => $activity_data, 'lang' => $lang));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiresource.edit', 'id' => $resource['id']));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['schedule_link'] = self::link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $resource['id']));
			$resource['activity'] = $this->bo->getActivityName($resource['activity_id']);
			$data = array(
				'resource'	=>	$resource
			);
			
			self::render_template('resource', $data);
		}

		public function schedule()
		{
			$date = new DateTime(phpgw::get_var('date'));
			// Make sure $from is a monday
			if($date->format('w') != 1)
			{
				$date->modify('last monday');
			}
			$prev_date = clone $date;
			$next_date = clone $date;
			$prev_date->modify('-1 week');
			$next_date->modify('+1 week');
			self::add_javascript('booking', 'booking', 'schedule.js');
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $resource['building_id']));
			$resource['resource_link'] = self::link(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
			$resource['date'] = $date->format('Y-m-d');
			$resource['week'] = intval($date->format('W'));
			$resource['year'] = intval($date->format('Y'));
			$resource['prev_link'] = self::link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $resource['id'], 'date'=> $prev_date->format('Y-m-d')));
			$resource['next_link'] = self::link(array('menuaction' => 'booking.uiresource.schedule', 'id' => $resource['id'], 'date'=> $next_date->format('Y-m-d')));
			for($i = 0; $i < 7; $i++)
			{
				$resource['days'][] = array('label' => $date->format('l').'<br/>'.$date->format('M d'), 'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			$lang['resource_schedule'] = lang('Resource schedule');
			$lang['prev_week'] = lang('Previous week');
			$lang['next_week'] = lang('Next week');
			$lang['week'] = lang('Week');
			$lang['buildings'] = lang('Buildings');
			$lang['schedule'] = lang('Schedule');
			$lang['time'] = lang('Time');
			self::render_template('resource_schedule', array('resource' => $resource, 'lang' => $lang));
		}
	}
