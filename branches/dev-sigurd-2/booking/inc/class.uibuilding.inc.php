<?php
	phpgw::import_class('booking.uicommon');

	class booking_uibuilding extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'active'		=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'schedule'		=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bobuilding');
			self::set_active_menu('booking::buildings');
			$this->fields = array('name', 'homepage', 'description', 'email', 'phone', 'address');
		}
		
		public function active()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}else{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
		}
		
		
		public function index()
		{
			
			
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			if($_SESSION['showall'])
			{
				$active_botton = lang('Show only active');
			}else{
				$active_botton = lang('Show all');
			}
			
						
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New building'),
								'href' => self::link(array('menuaction' => 'booking.uibuilding.add'))
							),
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
								'value' => $active_botton,
								'href' => self::link(array('menuaction' => 'booking.uibuilding.active'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uibuilding.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'homepage',
							'label' => lang('Homepage'),
						),
						array(
							'key' => 'active',
							'label' => lang('Status'),
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
			
			$buildings = $this->bo->read();
			foreach($buildings['results'] as &$building)
			{
				$building['link'] = $this->link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			$data = array(
				'ResultSet' => array(
					"totalResultsAvailable" => $buildings['total_records'], 
					"Result" => $buildings['results']
				)
			);
			return $data;
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = extract_values($_POST, $this->fields);
				$errors = $this->bo->validate($building);
				if(!$errors)
				{
					$receipt = $this->bo->add($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
				}
			}
			$this->flash_form_errors($errors);
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			self::render_template('building_new', array('building' => $building));
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
					$this->redirect(array('menuaction' => 'booking.uibuilding.index'));
				}
			}
			$this->flash_form_errors($errors);
			self::render_template('building_edit', array('building' => $building));
		}
		
		public function show()
		{

			$this->check_active('booking.uibuilding.show');
			$this->flash_form_errors($errors);
			$building = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['edit_link'] = self::link(array('menuaction' => 'booking.uibuilding.edit', 'id' => $building['id']));
			$building['schedule_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id']));
			self::render_template('building', array('building' => $building));
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
			$building = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show', 'id' => $building['id']));
			$building['date'] = $date->format('Y-m-d');
			$building['week'] = intval($date->format('W'));
			$building['year'] = intval($date->format('Y'));
			$building['prev_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id'], 'date'=> $prev_date->format('Y-m-d')));
			$building['next_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule', 'id' => $building['id'], 'date'=> $next_date->format('Y-m-d')));
			for($i = 0; $i < 7; $i++)
			{
				$building['days'][] = array('label' => $date->format('l').'<br/>'.$date->format('M d'), 'key' => $date->format('D'));
				$date->modify('+1 day');
			}
			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('building_schedule', array('building' => $building));
		}
	}
