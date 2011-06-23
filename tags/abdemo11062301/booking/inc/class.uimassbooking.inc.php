<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

	class booking_uimassbooking extends booking_uicommon
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
			
			
			$this->bo = CreateObject('booking.bomassbooking');
			self::set_active_menu('booking::applications::massboooking');
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
					'source' => self::link(array('menuaction' => 'booking.uimassbooking.index', 'phpgw_return_as' => 'json')),
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
				$building['link'] = $this->link(array('menuaction' => 'booking.uimassbooking.schedule', 'id' => $building['id']));
				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($buildings);
		}

		private function item_link(&$item, $key)
		{
			if(in_array($item['type'], array('allocation', 'booking', 'event')))
				$item['info_url'] = $this->link(array('menuaction' => 'booking.ui'.$item['type'].'.info', 'id' => $item['id']));
		}

		public function schedule()
		{
			$backend = phpgw::get_var('backend', 'GET');
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'GET'), "booking.uimassbooking");
			$building['application_link'] = self::link(array(
				'menuaction' => 'booking.uiallocation.add', 
				'building_id' => $building['id'], 
				'building_name' => $building['name'],
			));
			$building['datasource_url'] = self::link(array(
				'menuaction' => 'booking.uibooking.building_schedule', 
				'building_id' => $building['id'], 
				'phpgw_return_as' => 'json',
			));
			if ($backend == 'true')
			{
				$building['date'] = phpgw::get_var('date', 'GET');
			}
			self::add_javascript('booking', 'booking', 'schedule.js');
			self::render_template('massbooking_schedule', array('building' => $building, 'backend' => $backend));
		}


	}
