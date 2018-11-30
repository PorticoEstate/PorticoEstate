<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.uidocument_resource');
	phpgw::import_class('booking.uipermission_resource');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uiresource extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'add' => true,
			'edit' => true,
			'edit_activities' => true,
			'edit_facilities' => true,
			'get_custom' => true,
			'show' => true,
			'schedule' => true,
			'toggle_show_inactive' => true,
			'get_rescategories' => true,
			'get_buildings' => true,
			'add_building' => true,
			'remove_building' => true
		);

		public function __construct()
		{
			parent::__construct();
			$this->sobuilding = CreateObject('booking.sobuilding');

//			Analizar esta linea de permiso self::process_booking_unauthorized_exceptions();

			$this->bo = CreateObject('booking.boresource');
			$this->activity_bo = CreateObject('booking.boactivity');
			$this->facility_bo = CreateObject('booking.bofacility');
			$this->rescategory_bo = CreateObject('booking.borescategory');
			$this->fields = array(
				'name' => 'string',
				'description' => 'html',
				'opening_hours' => 'html',
				'contact_info' => 'html',
				'activity_id' => 'int',
				'active' => 'int',
				'type' => 'string',
				'sort' => 'string',
				'organizations_ids' => 'string',
				'rescategory_id' => 'int',
				'activities' => 'int',
				'facilities' => 'int',
			);
			self::set_active_menu('booking::buildings::resources');
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
					'source' => self::link(array('menuaction' => 'booking.uiresource.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Resource Name'),
							'formatter' => 'JqueryPortico.formatLink'
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
							'label' => lang('Building name'),
							'sortable' => false
						),
						array(
							'key' => 'type',
							'label' => lang('Resource Type')
						),
						array(
							'key' => 'activity_name',
							'label' => lang('Main activity')
						),
						array(
							'key' => 'rescategory_name',
							'label' => lang('Resource category'),
						),
						array(
							'key' => 'building_street',
							'label' => lang('Street'),
							'sortable' => false
						),
						array(
							'key' => 'building_city',
							'label' => lang('Postal city'),
							'sortable' => false
						),
						array(
							'key' => 'building_district',
							'label' => lang('District'),
							'sortable' => false
						),
						array(
							'key' => 'active',
							'label' => lang('Active'),
						),
					)
				)
			);

			$data['datatable']['actions'][] = array();

			if ($this->bo->allow_create())
			{
				$data['datatable']['new_item'] = self::link(array('menuaction' => 'booking.uiresource.add'));
			}

			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			return $this->jquery_results($this->bo->populate_grid_data("booking.uiresource.show"));
		}

		public function add()
		{
			$errors = array();
			$resource = array();
			$resource['sort'] = '0';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = extract_values($_POST, $this->fields);
				$resource['active'] = '1';
				$building_id = phpgw::get_var('building_id', 'int');
				$resource['buildings'][] = $building_id;
				$building = $this->sobuilding->read_single($building_id);
				$resource['activity_id'] = $building['activity_id'];

				$errors = $this->bo->validate($resource);
				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->add($resource);
						$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $receipt['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			else
			{
				$resource['type'] = 'Location';
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'resource_new.js');
			phpgwapi_jquery::load_widget('autocomplete');

			self::rich_text_editor(array('field_description', 'field_opening_hours', 'field_contact_info'));
			$activity_data = $this->activity_bo->fetch_activities();
			$resource['types'] = $this->resource_types();
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit permission'), 'link' => '#resource');
			$active_tab = 'generic';

			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('resource_form', array('resource' => $resource, 'activitydata' => $activity_data,
				'new_form' => true));
		}

		protected function resource_types()
		{
			$types = array();
			foreach ($this->bo->allowed_types() as $type)
			{
				$types[$type] = self::humanize($type);
			}
			return $types;
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $resource['id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.show',
					'id' => $resource['id']));
			$resource['types'] = $this->resource_types();

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($resource);
				$location = $this->get_location();
				$location_id = $GLOBALS['phpgw']->locations->get_id('booking', $location);

				$fields = ExecMethod('booking.custom_fields.get_fields', $location);
				$values_attribute = phpgw::get_var('values_attribute');
				$json_representation = array();
				foreach ($fields as $attrib_id => &$attrib)
				{
					$json_representation[$attrib['name']] = isset($values_attribute[$attrib_id]['value']) ? $values_attribute[$attrib_id]['value'] : null;
				}

				$resource['json_representation'][$location_id] = $json_representation;

				if (!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
				}
			}

			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'base', 'resource_new.js');
			phpgwapi_jquery::load_widget('autocomplete');
			self::rich_text_editor(array('field_description', 'field_opening_hours', 'field_contact_info'));
			$activity_data = $this->activity_bo->fetch_activities();
			foreach ($activity_data['results'] as $acKey => $acValue)
			{
				$activity_data['results'][$acKey]['resource_id'] = $resource['activity_id'];
			}
			$activity_path = $this->activity_bo->get_path($resource['activity_id']);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			$rescategory_data = $this->rescategory_bo->get_rescategories_by_activities($top_level_activity);
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit permission'), 'link' => '#resource');
			$active_tab = 'generic';

			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl(array('resource_form', 'datatable_inline'), array('datatable_def' => self::get_building_datatable_def($id),
				'resource' => $resource, 'activitydata' => $activity_data, 'rescategorydata' => $rescategory_data));
		}

		private function get_location()
		{
			$activity_id = phpgw::get_var('schema_activity_id', 'int');
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			return ".resource.{$top_level_activity}";
		}


		public function edit_activities()
		{
			$id = phpgw::get_var('id', 'int');
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;

			$errors = array();
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit resource activities'), 'link' => '#resource_edit_activities');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'activities', array());
				$resource = array_merge($resource, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($resource);
				if (!$errors)
				{
					// Exclude any activities that don't belong to the main activity. Such activities can't normally be
					// added in UI, but check for this nonetheless. Also check that the activities are active
					$activitylist = $this->activity_bo->fetch_activities_hierarchy();
					$childactivities = array();
					if (array_key_exists($resource['activity_id'], $activitylist))
					{
						$childactivities = $activitylist[$resource['activity_id']]['children'];
					}
					$resactivities = array();
					foreach ($resource['activities'] as $activity_id)
					{
						if (array_key_exists($activity_id, $childactivities))
						{
							$childactivity = $childactivities[$activity_id];
							if ($childactivity['active'])
							{
								$resactivities[] = $activity_id;
							}
						}
					}
					$resource['activities'] = $resactivities;

					try
					{
						$receipt = $this->bo->update($resource);
						$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$this->flash_form_errors($errors);
			$resource['activities_json'] = json_encode(array_map('intval', $resource['activities']));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator'] = phpgwapi_jquery::formvalidator_generate(array());

			self::render_template_xsl('resource_edit_activities', array('resource' => $resource));
		}


		public function edit_facilities()
		{
			$id = phpgw::get_var('id', 'int');
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;

			$errors = array();
			$tabs = array();
			$tabs['generic'] = array('label' => lang('edit resource facilities'), 'link' => '#resource_edit_facilities');
			$active_tab = 'generic';

			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				array_set_default($_POST, 'facilities', array());
				$resource = array_merge($resource, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($resource);
				if (!$errors)
				{
					// Unlike the editing of activities, a check for active facilities is not done, as adding an
					// inactive facility in UI is unlikely and the consequences are not grave (an inactive facility
					// will be excluded when the resource is used)
					try
					{
						$receipt = $this->bo->update($resource);
						$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$this->flash_form_errors($errors);
			$resource['facilities_json'] = json_encode(array_map('intval', $resource['facilities']));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator'] = phpgwapi_jquery::formvalidator_generate(array());

			self::render_template_xsl('resource_edit_facilities', array('resource' => $resource));
		}


		public function get_custom()
		{
			$type = phpgw::get_var('type', 'string', 'REQUEST', 'form');
			$resource_id = phpgw::get_var('resource_id', 'int');
			$resource = $this->bo->read_single($resource_id);
			$location = $this->get_location();
			$location_id = $GLOBALS['phpgw']->locations->get_id('booking', $location);
			$custom_values = $resource['json_representation'][$location_id];
			$custom_fields = createObject('booking.custom_fields');
			$fields = $custom_fields->get_fields($location);
			foreach ($fields as $attrib_id => &$attrib)
			{
				$attrib['value'] = isset($custom_values[$attrib['name']]) ? $custom_values[$attrib['name']] : null;

				if (isset($attrib['choice']) && is_array($attrib['choice']) && $attrib['value'])
				{
					foreach ($attrib['choice'] as &$choice)
					{
						if (is_array($attrib['value']))
						{
							$choice['selected'] = in_array($choice['id'], $attrib['value']) ? 1 : 0;
						}
						else
						{
							$choice['selected'] = $choice['id'] == $attrib['value'] ? 1 : 0;
						}
					}
				}
			}
//			_debug_array($fields);
			$organized_fields = $custom_fields->organize_fields($location, $fields);

			$data = array(
				'attributes_group' => $organized_fields,
			);
			$GLOBALS['phpgw']->xslttpl->add_file(array("attributes_{$type}"));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('custom_fields' => $data));
		}


		function get_rescategories()
		{
			$activity_id = phpgw::get_var('activity_id', 'int');
			$activity_path = $this->activity_bo->get_path($activity_id);
			$top_level_activity = $activity_path ? $activity_path[0]['id'] : 0;
			$rescategory_data = $this->rescategory_bo->get_rescategories_by_activities($top_level_activity);
			return $rescategory_data;
		}


		private static function get_building_columns()
		{
			$columns = array
				(
				array('key' => 'id', 'label' => '#', 'sortable' => true, 'resizeable' => true),
				array('key' => 'name', 'label' => lang('name'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'street', 'label' => lang('street'), 'sortable' => true, 'resizeable' => true),
				array('key' => 'activity_name', 'label' => lang('activity'), 'sortable' => true,
					'resizeable' => true, 'formatter' => 'ChangeSchema'),
				array('key' => 'active', 'label' => lang('active'), 'sortable' => true, 'resizeable' => true)
			);
			return $columns;
		}

		private static function get_building_datatable_def( $id )
		{
			return array
				(
				array
					(
					'container' => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'booking.uiresource.get_buildings',
							'resource_id' => $id, 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => self::get_building_columns(),
					'data' => json_encode(array()),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				)
			);
		}

		public function get_buildings()
		{
			$resource = $this->bo->read_single(phpgw::get_var('resource_id', 'int'));

			$_filter_building['id'] = array_merge(array(-1), $resource['buildings']);

			$bui_result = $this->sobuilding->read(array("sort" => "name", "dir" => "asc",
				"filters" => $_filter_building));

			return $this->jquery_results($bui_result);
		}

		public function add_building()
		{
			$resource_id = phpgw::get_var('resource_id', 'int');
			if (!$building_id = phpgw::get_var('building_id', 'int'))
			{
				return array(
					'ok' => false,
					'msg' => lang('select')
				);
			}

			try
			{
				$resource = $this->bo->read_single($resource_id);
				$receipt = $this->bo->add_building($resource, $resource_id, $building_id);
				$msg = $receipt ? '' : lang('duplicate');
			}
			catch (booking_unauthorized_exception $e)
			{
				return false;
				$msg = lang('Could not add object due to insufficient permissions');
			}

			return array(
				'ok' => $receipt,
				'msg' => $msg
			);
		}

		public function remove_building()
		{
			$resource_id = phpgw::get_var('resource_id', 'int');
			if (!$building_id = phpgw::get_var('building_id', 'int'))
			{
				return array(
					'ok' => false,
					'msg' => lang('select')
				);
			}
			try
			{
				$resource = $this->bo->read_single($resource_id);
				$receipt = $this->bo->remove_building($resource, $resource_id, $building_id);
				$msg = '';
			}
			catch (booking_unauthorized_exception $e)
			{
				return false;
				$msg = lang('Could not update object due to insufficient permissions');
			}

			return array(
				'ok' => $receipt,
				'msg' => $msg
			);
		}

		public function show()
		{
			$id = phpgw::get_var('id', 'int');
			$resource = $this->bo->read_single($id);
			$array_resource = array(&$resource);
			$this->bo->add_activity_facility_data($array_resource);

			$_filter_building['id'] = array_merge(array(-1), $resource['buildings']);

			$bui_result = $this->sobuilding->read(array("sort" => "name", "dir" => "asc",
				"filters" => $_filter_building));

			// Create text strings for the activity and facility lists
			$activitynames = array();
			foreach ($resource['activities_list'] as $activity)
			{
				$activitynames[] = $activity['name'];
			}
			$resource['activities_names'] = implode(', ', $activitynames);
			$facilitynames = array();
			foreach ($resource['facilities_list'] as $facility)
			{
				$facilitynames[] = $facility['name'];
			}
			$resource['facilities_names'] = implode(', ', $facilitynames);

			$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiresource.edit',
					'id' => $resource['id']));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.show',
					'id' => $resource['building_id']));
			$resource['buildings_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['schedule_link'] = self::link(array('menuaction' => 'booking.uiresource.schedule',
					'id' => $resource['id']));
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['add_document_link'] = booking_uidocument::generate_inline_link('resource', $resource['id'], 'add');
			$resource['add_permission_link'] = booking_uipermission::generate_inline_link('resource', $resource['id'], 'add');
			$resource['edit_activities_link'] = self::link(array('menuaction' => 'booking.uiresource.edit_activities',
					'id' => $resource['id']));
			$resource['edit_facilities_link'] = self::link(array('menuaction' => 'booking.uiresource.edit_facilities',
					'id' => $resource['id']));

			$tabs = array();
			$tabs['generic'] = array(
				'label' => lang('Resource'),
				'link' => '#resource'
			);
			$active_tab = 'generic';
			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			$data = array(
				'datatable_def' => self::get_building_datatable_def($id),
				'resource' => $resource
			);
			self::add_javascript('booking', 'base', 'resource_new.js'); // to render custom fields
			self::render_template_xsl(array('resource', 'datatable_inline'), $data);
		}

		public function schedule()
		{
			$resource = $this->bo->get_schedule(phpgw::get_var('id', 'int'), 'booking.uibuilding', 'booking.uiresource');

			$GLOBALS['phpgw_info']['flags']['app_header'] = lang('booking') . "::{$resource['name']}";
			$building_names = array();
			if(is_array($resource['buildings']))
			{
				foreach ($resource['buildings'] as $building_id)
				{
					$building = $this->sobuilding->read_single($building_id);
					$building_names[] = $building['name'];
				}
				$GLOBALS['phpgw_info']['flags']['app_header'] .= ' (' . implode('',$building_names) . ')';
			}

			$resource['application_link'] = self::link(array(
					'menuaction' => 'booking.uiapplication.add',
					'building_id' => $resource['building_id'],
					'building_name' => $resource['building_name'],
					'activity_id' => $resource['activity_id'],
					'resource' => $resource['id']
			));
			$resource['datasource_url'] = self::link(array(
					'menuaction' => 'booking.uibooking.resource_schedule',
					'resource_id' => $resource['id'],
					'phpgw_return_as' => 'json',
			));

			$resource['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Resource Schedule'), 'link' => '#resource_schedule');
			$active_tab = 'generic';

			$resource['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.show',
					'id' => $resource['id']));

			self::add_javascript('booking', 'base', 'schedule.js');

			phpgwapi_jquery::load_widget("datepicker");

			self::render_template('resource_schedule', array('resource' => $resource));
		}
	}