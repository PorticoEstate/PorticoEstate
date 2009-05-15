<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uipermission_season');

	class booking_uiseason extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'boundaries'	=>	true,
			'delete_boundary'	=>	true,
			'wtemplate'		=>	true,
			'wtemplate_json'		=>	true,
			'wtemplate_alloc_json'		=>	true,
			'generate'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.boseason');
			self::set_active_menu('booking::buildings::seasons');
			$this->fields = array('name', 'building_id', 'building_name', 'status', 'from_', 'to_', 'resources');
			$this->boundary_fields = array('wday', 'from_', 'to_');
			$this->wtemplate_alloc_fields = array('id', 'organization_id', 'wday', 'cost', 'from_', 'to_', 'resources');
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
								'value' => lang('New season'),
								'href' => self::link(array('menuaction' => 'booking.uiseason.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uiseason.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Season Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building name')
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
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
			$seasons = $this->bo->read();
			array_walk($seasons["results"], array($this, "_add_links"), "booking.uiseason.show");
			return $this->yui_results($seasons);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$season = extract_values($_POST, $this->fields);
				array_set_default($_POST, 'resources', array());
				$errors = $this->bo->validate($season);
				if(!$errors)
				{
					$receipt = $this->bo->add($season);
					$this->redirect(array('menuaction' => 'booking.uiseason.show', 'id'=>$receipt['id']));
				}
			} else {
				// Initialize the array with empty data
				$season = array("resources" => array());
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'season.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			$season['resources_json'] = json_encode(array_map('intval', $season['resources']));
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.index'));
			$lang['title'] = lang('New Season');
			$lang['buildings'] = lang('Buildings');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['building'] = lang('Building');
			$lang['organization'] = lang('Organization');
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
			$lang['status'] = lang('Status');
			$lang['planning'] = lang('Planning');
			$lang['published'] = lang('Published');
			$lang['archived'] = lang('Archived');
			self::render_template('season_new', array('season' => $season, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($id);
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['id'] = $id;
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$season = array_merge($season, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($season);
				if(!$errors)
				{
					$receipt = $this->bo->update($season);
					$this->redirect(array('menuaction' => 'booking.uiseason.show', 'id'=>$season['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'season.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			$season['resources_json'] = json_encode(array_map('intval', $season['resources']));
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season['id']));
			self::render_template('season_edit', array('season' => $season, 'lang' => $lang));
		}
		
		public function show()
		{
			$season = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['edit_link'] = self::link(array('menuaction' => 'booking.uiseason.edit', 'id' => $season['id']));
			$season['boundaries_link'] = self::link(array('menuaction' => 'booking.uiseason.boundaries', 'id' => $season['id']));
			$season['wtemplate_link'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate', 'id' => $season['id']));
			$resource['add_permission_link'] = booking_uipermission::generate_inline_link('season', $season['id'], 'add');
			$resource_ids = '';
			foreach($season['resources'] as $res)
			{
				$resource_ids = $resource_ids . '&filter_id[]=' . $res;
			}
			$season['resource_ids'] = $resource_ids;
			self::render_template('season', array('season' => $season, 'lang' => $lang));
		}

		public function boundaries()
		{
			$season_id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($season_id);
			$boundaries = $this->bo->get_boundaries($season_id);
			$boundaries = $boundaries['results'];
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['season_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season['id']));
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season['id']));
			$weekdays = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			foreach($boundaries as &$boundary)
			{
				$boundary['wday_name'] = lang($weekdays[$boundary['wday'] - 1]);
				$boundary['delete_link'] = self::link(array('menuaction' => 'booking.uiseason.delete_boundary', 'id' => $boundary['id']));
			}
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$boundary = extract_values($_POST, $this->boundary_fields);
				$boundary['season_id'] = $season_id;
				$errors = $this->bo->validate_boundary($boundary);
				if(!$errors)
				{
					$receipt = $this->bo->add_boundary($boundary);
					$this->redirect(array('menuaction' => 'booking.uiseason.boundaries', 'id'=>$season_id));
				}
			}
			$this->flash_form_errors($errors);
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season_id));
			self::render_template('season_boundaries', array('boundary' => $boundary, 'boundaries' => $boundaries, 'season' => $season));
		}

		public function delete_boundary()
		{
			$boundary_id = intval(phpgw::get_var('id', 'GET'));
			$boundary = $this->bo->so_boundary->read_single($boundary_id);
			$season_id = $boundary['season_id'];
			$this->bo->so_boundary->delete($boundary_id);
			$this->redirect(array('menuaction' => 'booking.uiseason.boundaries', 'id'=>$season_id));
		}

		public function wtemplate()
		{
			$season_id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($season_id);
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['resources_json'] = json_encode(array_map('intval', $season['resources']));
			$season['get_url'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate_alloc_json', 'season_id' => $season['id'], 'phpgw_return_as'=>'json'));
			$season['post_url'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate_alloc_json', 'season_id' => $season['id'], 'phpgw_return_as'=>'json'));
			$season['generate_url'] = self::link(array('menuaction' => 'booking.uiseason.generate', 'id' => $season['id']));

			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('season_wtemplate', array('season' => $season));
		}

		public function wtemplate_json()
		{
			$season_id = intval(phpgw::get_var('id', 'GET'));
			$allocations = $this->bo->wtemplate_schedule($season_id);
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $allocations['total_records'], 
					"Result" => $allocations['results']
				)
			);
			return $data;
		}
		
		/* Return a single wtemplate allocations as JSON */
		public function wtemplate_alloc_json()
		{
			$season_id = intval(phpgw::get_var('season_id', 'GET'));
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$alloc = extract_values($_POST, $this->wtemplate_alloc_fields);
				$alloc['season_id'] = $season_id;
				$errors = $this->bo->validate_wtemplate_alloc($alloc);
				if(!$errors && $alloc['id'])
					$receipt = $this->bo->update_wtemplate_alloc($alloc);
				else if(!$errors && !$alloc['id'])
					$receipt = $this->bo->add_wtemplate_alloc($alloc);
				return $errors;
			}
			$id = intval(phpgw::get_var('id', 'GET'));
			$alloc = $this->bo->wtemplate_alloc_read_single($id);
			return $alloc;
		}

		public function generate()
		{
			$season_id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($season_id);
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['wtemplate_link'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate', 'id' => $season['id']));
			$result = array();
			$step = 1;
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$step = phpgw::get_var('create', 'POST') ? 3 : 2;
				$from_ = phpgw::get_var('from_', 'POST');
				$to_ = phpgw::get_var('to_', 'POST');
				$result = $this->bo->generate_allocation($season_id, 
														 new DateTime($from_),
														 new DateTime($to_),
														 $step == 3);
			}
			self::render_template('season_generate', array('season' => $season, 'result'=>$result, 'step' => $step, 'from_' => $from_, 'to_' => $to_));
		}

	}
