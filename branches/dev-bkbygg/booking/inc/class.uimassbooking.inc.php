<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//    phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uimassbooking extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'active' => true,
			'add' => true,
			'show' => true,
			'edit' => true,
			'schedule' => true,
			'properties' => true,
			'toggle_show_inactive' => true,
			'find_buildings_used_by' => true,
		);

		public function __construct()
		{
			parent::__construct();


			$this->bo = CreateObject('booking.bomassbooking');
			self::set_active_menu('booking::applications::massbookings');
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uimassbooking.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Building'),
							'formatter' => 'JqueryPortico.formatLink'
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

			$data['datatable']['actions'][] = array();
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{

			$buildings = $this->bo->read();
			foreach ($buildings['results'] as &$building)
			{
				$building['link'] = $this->link(array('menuaction' => 'booking.uimassbooking.schedule',
					'id' => $building['id']));
//				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->jquery_results($buildings);
		}

		private function item_link( &$item, $key )
		{
			if (in_array($item['type'], array('allocation', 'booking', 'event')))
				$item['info_url'] = $this->link(array('menuaction' => 'booking.ui' . $item['type'] . '.info',
					'id' => $item['id']));
		}

		public function schedule()
		{
			$backend = phpgw::get_var('backend', 'bool');
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'int'), "booking.uimassbooking");
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
			if ($backend)
			{
				$building['date'] = phpgw::get_var('date', 'string');
			}

			$building['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Massbooking Schedule'), 'link' => '#massbooking_schedule');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::add_javascript('booking', 'base', 'schedule.js');
			phpgwapi_jquery::load_widget("datepicker");
			self::render_template_xsl('massbooking_schedule', array('building' => $building,
				'backend' => $backend));
		}
	}