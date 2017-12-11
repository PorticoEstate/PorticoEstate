<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uibuilding extends booking_uicommon
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
		protected $module;

		public function __construct()
		{
			parent::__construct();

			//self::process_booking_unauthorized_exceptions();

			$this->bo = CreateObject('booking.bobuilding');
			$this->bo_booking = CreateObject('booking.bobooking');
			$this->activity_bo = CreateObject('booking.boactivity');
			self::set_active_menu('booking::buildings');
			$this->fields = array
				(
				'name' => 'string',
				'homepage' => 'url',
				'description' => 'html',
				'email' => 'email',
				'tilsyn_name' => 'string',
				'tilsyn_email' => 'email',
				'tilsyn_phone' => 'string',
				'tilsyn_name2' => 'string',
				'tilsyn_email2' => 'email',
				'tilsyn_phone2' => 'string',
				'street' => 'string',
				'zip_code' => 'string',
				'city' => 'string',
				'district' => 'string',
				'phone' => 'string',
				'active' => 'int',
				'location_code' => 'string',
				'deactivate_application' => 'int',
				'deactivate_calendar' => 'int',
				'deactivate_sendmessage' => 'int',
				'extra_kalendar' => 'string',
				'calendar_text' => 'string',
				'activity_id' => 'int',
			);
			$this->module = "booking";
		}

		public function properties()
		{
			$q = phpgw::get_var('query', 'string', 'REQUEST', null);
			$type_id = count(explode('-', $q));
			$so = CreateObject('property.solocation');
			$ret = $so->read(array('type_id' => $type_id, 'location_code' => $q));
			foreach ($ret as &$r)
			{
				$name = array();
				for ($i = 1; $i <= $type_id; $i++)
					$name[] = $r['loc' . $i . '_name'];
				$r['name'] = $r['location_code'] . ' (' . join(', ', $name) . ')';
				$r['id'] = $r['location_code'];
			}
			$locations = array('results' => $ret, 'total_results' => count($ret));
			return $this->yui_results($locations);
		}

		public function find_buildings_used_by()
		{
			if (!phpgw::get_var('phpgw_return_as') == 'json')
			{
				return;
			}

			if (($organization_id = phpgw::get_var('organization_id', 'int', 'REQUEST', null)))
			{
				$buildings = $this->bo->find_buildings_used_by($organization_id);
				array_walk($buildings["results"], array($this, "_add_links"), "bookingfrontend.uibuilding.show");
				return $this->yui_results($buildings);
			}

			return $this->yui_results(null);
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'datatable_name' => lang('building'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'label' => lang('toggle show inactive'),
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => $this->module . '.uibuilding.index',
						'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Building'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Activity')
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

			$data['datatable']['actions'][] = array();
			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = self::link(array('menuaction' => $this->module . '.uibuilding.add'));
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{

			$filter_part_of_town_id = phpgw::get_var(filter_part_of_town_id);
			if (preg_match("/,/", $filter_part_of_town_id))
			{
				$_REQUEST['filter_part_of_town_id'] = explode(',', $filter_part_of_town_id);
			}
			$buildings = $this->bo->read();
			foreach ($buildings['results'] as &$building)
			{
				$building['link'] = $this->link(array('menuaction' => $this->module . '.uibuilding.show',
					'id' => $building['id']));
#				$building['active'] = $building['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->jquery_results($buildings);
		}

		public function add()
		{
			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = extract_values($_POST, $this->fields);
				$building['active'] = '1';
				$errors = $this->bo->validate($building);
				if (!$errors)
				{
					$receipt = $this->bo->add($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.show', 'id' => $receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$activity_data = $this->activity_bo->get_top_level();

			phpgwapi_jquery::load_widget('autocomplete');
			self::rich_text_editor('field_description');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('new building'), 'link' => '#building_form');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$building['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('building_form', array('building' => $building, 'activitydata' => $activity_data,
				'new_form' => true));
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$building = $this->bo->read_single($id);
			$building['id'] = $id;
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $building['id']));
			$building['top-nav-bar-buildings'] = lang('Buildings');
			$config = CreateObject('phpgwapi.config', 'booking');
			$config->read();

			if ($config->config_data['extra_schedule'] == 'yes')
			{
				$building['extra'] = 1;
			}
			else
			{
				$building['extra'] = 0;
			}

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$building = array_merge($building, extract_values($_POST, $this->fields));

				$errors = $this->bo->validate($building);
				if (!$errors)
				{
					$receipt = $this->bo->update($building);
					$this->redirect(array('menuaction' => 'booking.uibuilding.show', 'id' => $receipt['id']));
				}
			}
			$activity_data = $this->activity_bo->get_top_level();
			foreach ($activity_data as $acKey => $acValue)
			{
				$activity_data[$acKey]['activity_id'] = $building['activity_id'];
			}

			$this->flash_form_errors($errors);

			phpgwapi_jquery::load_widget('autocomplete');
			self::rich_text_editor('field_description');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Building Edit'), 'link' => '#building_form');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$building['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('building_form', array('building' => $building, 'activitydata' => $activity_data));
		}

		public function show()
		{
			$building = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$building['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['edit_link'] = self::link(array('menuaction' => 'booking.uibuilding.edit',
					'id' => $building['id']));
			$building['schedule_link'] = self::link(array('menuaction' => 'booking.uibuilding.schedule',
					'id' => $building['id']));
			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$building['message_link'] = self::link(array('menuaction' => 'booking.uisystem_message.edit',
					'building_id' => $building['id']));
			$building['add_document_link'] = booking_uidocument::generate_inline_link('building', $building['id'], 'add');
			$building['add_permission_link'] = booking_uipermission::generate_inline_link('building', $building['id'], 'add');
			$building['location_link'] = self::link(array('menuaction' => 'property.uilocation.view',
					'location_code' => $building['location_code']));
			if (trim($building['homepage']) != '' && !preg_match("/^http|https:\/\//", trim($building['homepage'])))
			{
				$building['homepage'] = 'http://' . $building['homepage'];
			}

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Building Show'), 'link' => '#building_show');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('building', array('building' => $building));
		}

		public function schedule()
		{
			$building = $this->bo->get_schedule(phpgw::get_var('id', 'int'), "booking.uibuilding");

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$building['name']}";

			$building['cancel_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $building['id']));
			$building['datasource_url'] = self::link(array(
					'menuaction' => 'booking.uibooking.building_schedule',
					'building_id' => $building['id'],
					'phpgw_return_as' => 'json',
			));
			self::add_javascript('booking', 'base', 'schedule.js');
			phpgwapi_jquery::load_widget("datepicker");

			$building['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Building Schedule'), 'link' => '#building_schedule');
			$active_tab = 'generic';

			$building['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			self::render_template_xsl('building_schedule', array('building' => $building));
		}
	}