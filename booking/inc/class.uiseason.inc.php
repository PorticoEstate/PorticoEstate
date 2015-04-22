<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.boresource');
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
			'delete_wtemplate_alloc'	=>	true,
			'wtemplate'		=>	true,
			'wtemplate_json'		=>	true,
			'wtemplate_alloc_json'		=>	true,
			'generate'		=>	true,
			'toggle_show_inactive'	=>	true,
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.boseason');
			$this->resource_bo = CreateObject('booking.boresource');
			self::set_active_menu('booking::buildings::seasons');
			$this->fields = array('name', 'building_id', 'building_name', 'status', 'from_', 'to_', 'resources', 'active', 'officer_id', 'officer_name');
			$this->boundary_fields = array('wday', 'from_', 'to_');
			$this->wtemplate_alloc_fields = array('id', 'organization_id', 'wday', 'cost', 'from_', 'to_', 'resources');
		}
		
		public function index()
		{
			$this->db = $GLOBALS['phpgw']->db;
			
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
					'source' => self::link(array('menuaction' => 'booking.uiseason.index', 'phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 'to_', 'dir' => 'desc'),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Season Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building')
						),
						array(
							'key' => 'resource_list',
							'label' => lang('Resources')
						),
						array(
							'key' => 'officer_name',
							'label' => lang('Officer')
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
						),
						array(
							'key' => 'status',
							'label' => lang('Status')
						),
					)
				)
			);
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New season'),
					'href' => self::link(array('menuaction' => 'booking.uiseason.add'))
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$seasons = $this->bo->read();
			array_walk($seasons["results"], array($this, "_add_links"), "booking.uiseason.show");
			
			foreach($seasons['results'] as &$season) {
				$season['status'] = lang($season['status']);
				$season['from_'] = pretty_timestamp($season['from_']);
				$season['to_'] = pretty_timestamp($season['to_']);

				$resources = $this->resource_bo->read_single($season['id']);
				if (isset($season['resources'])) {
					$filters['filters']['id'] = $season['resources'];
					$resources = $this->resource_bo->so->read($filters);
					$temparray = array();
					foreach($resources['results'] as $resource) {
						$temparray[] = $resource['name'];
					}
					$season['resource_list'] = implode(', ', $temparray);
				}

				$sql = "SELECT account_lastname, account_firstname FROM phpgw_accounts WHERE account_lid = '".$season['officer_name']."'";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet)) {
					$season['officer_name'] = $record['account_firstname']." ".$record['account_lastname'];
				}
			}
			return $this->yui_results($seasons);
		}

		public function add()
		{
			$errors = array();
			$season = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$season = extract_values($_POST, $this->fields);
				$season['active'] = '1';
				array_set_default($_POST, 'resources', array());
				$from =  strtotime($season['from_']);
				$to =  strtotime($season['to_']);
				$season['from_'] = date("Y-m-d",$from);
				$season['to_'] = date("Y-m-d",$to);
				$errors = $this->bo->validate($season);
				
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($season);
						$this->redirect(array('menuaction' => 'booking.uiseason.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			} else {
				// Initialize the array with empty data
				$season = array("resources" => array());
				$season['officer_id'] = $GLOBALS['phpgw_info']['user']['account_id'];
				$season['officer_name'] = $GLOBALS['phpgw_info']['user']['account_lid'];
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'season.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('calendar');
			phpgwapi_yui::load_widget('autocomplete');
			array_set_default($season, 'resources', array());
			$season['resources_json'] = json_encode(array_map('intval', $season['resources']));
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.index'));
			self::render_template('season_new', array('season' => $season, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($id);
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$from =  strtotime($season['from_']);
			$to =  strtotime($season['to_']);
			$season['from_'] = date("Y-m-d",$from);
			$season['to_'] = date("Y-m-d",$to);
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'resources', array());
				$season = array_merge($season, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($season);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($season);
						$this->redirect(array('menuaction' => 'booking.uiseason.show', 'id'=>$season['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
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
			$season['add_permission_link'] = booking_uipermission::generate_inline_link('season', $season['id'], 'add');
			$resource_ids = '';
			
			if (count($season['resources']) == 0) {
				$resource_ids = 'filter_id=-1'; //No resources to display, so set filter that returns nothing
			} else {
				foreach($season['resources'] as $res)
				{
					$resource_ids = $resource_ids . '&filter_id[]=' . $res;
				}
			}
			$season['resource_ids'] = $resource_ids;
			$season['status'] = $season['status'] ? lang($season['status']) : $season['status'];
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
			$boundary = $this->bo->read_boundary($boundary_id);
			$season_id = $boundary['season_id'];
			$this->bo->delete_boundary($boundary);
			$this->redirect(array('menuaction' => 'booking.uiseason.boundaries', 'id'=>$season_id));
		}

		public function delete_wtemplate_alloc()
		{
			$allocation_id = intval(phpgw::get_var('id', 'POST'));
			$alloc = $this->bo->so_wtemplate_alloc->read_single($allocation_id);
			$this->bo->delete_wtemplate_alloc($alloc);
			return 1;
		}

		public function wtemplate()
		{
			$season_id = intval(phpgw::get_var('id', 'GET'));
			$season = $this->bo->read_single($season_id);
			$season['season_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season_id));
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['resources_json'] = json_encode(array_map('intval', $season['resources']));
			$season['get_url'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate_alloc_json', 'season_id' => $season['id'], 'phpgw_return_as'=>'json'));
			$season['post_url'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate_alloc_json', 'season_id' => $season['id'], 'phpgw_return_as'=>'json'));
			$season['generate_url'] = self::link(array('menuaction' => 'booking.uiseason.generate', 'id' => $season['id']));
			$season['delete_wtemplate_alloc_url'] = self::link(array('menuaction' => 'booking.uiseason.delete_wtemplate_alloc', 'phpgw_return_as'=>'json'));
			$season['cancel_link'] = self::link(array('menuaction' => 'booking.uiseason.show', 'id' => $season['id']));
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
			
			$this->bo->authorize_write($season);
			
			$season['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$season['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $season['building_id']));
			$season['wtemplate_link'] = self::link(array('menuaction' => 'booking.uiseason.wtemplate', 'id' => $season['id']));
			$result = array();
			$step = 1;
			$errors = array();
			$from_ = $season['from_'];
			$to_ = $season['to_'];
			$interval = 1;
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$step = phpgw::get_var('create', 'POST') ? 3 : 2;
				$from =  strtotime(phpgw::get_var('from_', 'POST'));
				$to =  strtotime(phpgw::get_var('to_', 'POST'));
				$from_ = date("Y-m-d",$from);
				$to_ = date("Y-m-d",$to);

				$interval = phpgw::get_var('field_interval', 'POST');
				if($from_ < $season['from_'])
				{
					$errors['from_'] = lang('Start date must be after %1', pretty_timestamp($season['from_']));
				}
				if($to_ > $season['to_'])
				{
					$errors['to_'] = lang('To date must be before %1', pretty_timestamp($season['to_']));
				}
				if($errors)
				{
					$step = 1;
				}
				else
				{
					$result = $this->bo->generate_allocation($season_id, 
															 new DateTime($from_),
															 new DateTime($to_),
															 $interval,
															 $step == 3);
				}
                $this->bo->so->update_id_string();
			}
			$this->flash_form_errors($errors);
			self::render_template('season_generate', array('season' => $season, 
								  'result' => $result, 'step' => $step,
								  'interval' => $interval,
								  'from_' => $from_, 'to_' => $to_));
		}

	}
