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
			'index'					 => true,
			'query'					 => true,
			'add'					 => true,
			'edit'					 => true,
			'get_custom'			 => true,
			'show'					 => true,
			'schedule'				 => true,
			'toggle_show_inactive'	 => true,
		);

		public function __construct()
		{
			parent::__construct();

//			Analizar esta linea de permiso self::process_booking_unauthorized_exceptions();

			$this->bo			 = CreateObject('booking.boresource');
			$this->activity_bo	 = CreateObject('booking.boactivity');
			$this->fields		 = array(
				'name'				 => 'string',
				'building_id'		 => 'string',
				'building_name'		 => 'string',
				'description'		 => 'html',
				'activity_id'		 => 'int',
				'active'			 => 'int',
				'type'				 => 'string',
				'sort'				 => 'string',
				'organizations_ids'	 => 'string',
			);
			self::set_active_menu('booking::buildings::resources');
		}

		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$data = array(
				'form'		 => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type'	 => 'link',
								'value'	 => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href'	 => self::link(array('menuaction' => $this->url_prefix . '.toggle_show_inactive'))
							),
						)
					),
				),
				'datatable'	 => array(
					'source' => self::link(array('menuaction' => 'booking.uiresource.index', 'phpgw_return_as' => 'json')),
					'field'	 => array(
						array(
							'key'		 => 'name',
							'label'		 => lang('Resource Name'),
							'formatter'	 => 'JqueryPortico.formatLink'
						),
						array(
							'key'	 => 'sort',
							'label'	 => lang('Order')
						),
						array(
							'key'	 => 'link',
							'hidden' => true
						),
						array(
							'key'	 => 'building_name',
							'label'	 => lang('Building name')
						),
						array(
							'key'	 => 'type',
							'label'	 => lang('Resource Type')
						),
						array(
							'key'	 => 'activity_name',
							'label'	 => lang('Activity')
						),
						array(
							'key'	 => 'building_street',
							'label'	 => lang('Street')
						),
						array(
							'key'	 => 'building_city',
							'label'	 => lang('Postal city')
						),
						array(
							'key'	 => 'building_district',
							'label'	 => lang('District')
						),
						array(
							'key'	 => 'active',
							'label'	 => lang('Active'),
						),
					)
				)
			);

			if($this->bo->allow_create())
			{
				array_unshift($data['form']['toolbar']['item'], array(
					'type'	 => 'link',
					'value'	 => lang('New resource'),
					'href'	 => self::link(array('menuaction' => 'booking.uiresource.add'))
				));
			}

//			self::render_template('datatable', $data);
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function query()
		{
			return $this->jquery_results($this->bo->populate_grid_data("booking.uiresource.show"));
		}

//		public function index_json()
//		{
//			return $this->bo->populate_grid_data("booking.uiresource.show");
//		}

		public function add()
		{
			$errors				 = array();
			$resource			 = array();
			$resource['sort']	 = '0';

			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource			 = extract_values($_POST, $this->fields);
				$resource['active']	 = '1';
				$errors				 = $this->bo->validate($resource);
				if(!$errors)
				{
					try
					{
						$receipt = $this->bo->add($resource);
						$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $receipt['id']));
					}
					catch(booking_unauthorized_exception $e)
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
			self::add_javascript('booking', 'booking', 'resource_new.js');
			phpgwapi_jquery::load_widget('autocomplete');

			phpgwapi_jquery::init_ckeditor('field_description');
			$activity_data			 = $this->activity_bo->fetch_activities();
			$resource['types']		 = $this->resource_types();
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$tabs					 = array();
			$tabs['generic']		 = array('label' => lang('Permission Edit'), 'link' => '#resource');
			$active_tab				 = 'generic';

			$resource['tabs']		 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator']	 = phpgwapi_jquery::formvalidator_generate(array('location',
				'date', 'security', 'file'));

			self::render_template_xsl('resource_form', array('resource' => $resource, 'activitydata' => $activity_data,
				'new_form' => true));
		}

		protected function resource_types()
		{
			$types = array();
			foreach($this->bo->allowed_types() as $type)
			{ $types[$type] = self::humanize($type);}
			return $types;
		}

		public function edit()
		{
			$id							 = phpgw::get_var('id', 'int');
			$resource					 = $this->bo->read_single($id);
			$resource['id']				 = $id;
			$resource['building_link']	 = self::link(array('menuaction' => 'booking.uibuilding.show',
				'id' => $resource['id']));
			$resource['buildings_link']	 = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['cancel_link']	 = self::link(array('menuaction' => 'booking.uiresource.show',
				'id' => $resource['id']));
			$resource['types']			 = $this->resource_types();

			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource	 = array_merge($resource, extract_values($_POST, $this->fields));
				$errors		 = $this->bo->validate($resource);
				$location		 = $this->get_location();
				$location_id	= $GLOBALS['phpgw']->locations->get_id('booking', $location);

				$fields		= ExecMethod('booking.custom_fields.get_fields',$location);
				$values_attribute = phpgw::get_var('values_attribute');
				$json_representation = array();
				foreach($fields as $attrib_id => &$attrib)
				{
					$json_representation[$attrib['name']] = isset($values_attribute[$attrib_id]['value']) ? $values_attribute[$attrib_id]['value'] : null;
			//		$attrib['value'] = isset($values_attribute[$attrib_id]['value']) ? $values_attribute[$attrib_id]['value'] : null;
				}
				$resource['json_representation'] = array(
					'schema_location' => $location_id,
					'data' => $json_representation
				);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'resource_new.js');
			phpgwapi_jquery::load_widget('autocomplete');
			phpgwapi_jquery::init_ckeditor('field_description');
			$activity_data = $this->activity_bo->fetch_activities();
			foreach($activity_data['results'] as $acKey => $acValue)
			{
				$activity_data['results'][$acKey]['resource_id'] = $resource['activity_id'];
			}
			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Permission Edit'), 'link' => '#resource');
			$active_tab		 = 'generic';

			$resource['tabs']		 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['validator']	 = phpgwapi_jquery::formvalidator_generate(array('location',
				'date', 'security', 'file'));

			self::render_template_xsl('resource_form', array('resource' => $resource, 'activitydata' => $activity_data));
		}


		private function get_location()
		{
			$activity_id		 = phpgw::get_var('activity_id', 'int');
			$activity_path		 = $this->activity_bo->get_path($activity_id);
			$top_level_activity	 = $activity_path ? $activity_path[0]['id'] : 0;
			return ".resource.{$top_level_activity}";
		}

		public function get_custom()
		{
			$resource_id		= phpgw::get_var('resource_id', 'int');
			$resource			= $this->bo->read_single($resource_id);
			$custom_values		= $resource['json_representation']->data;
//			_debug_array($custom_values);


			$location			 = $this->get_location();
			$custom_fields		= createObject('booking.custom_fields');
			$fields	= $custom_fields->get_fields($location);
			foreach($fields as $attrib_id => &$attrib)
			{
				$attrib['value'] = isset($custom_values->$attrib['name']) ? $custom_values->$attrib['name'] : null;

				if(isset($attrib['choice']) && is_array($attrib['choice']) && $attrib['value'])
				{
					foreach($attrib['choice'] as &$choice)
					{
						if(is_array($attrib['value']))
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
			$organized_fields	 = $custom_fields->organize_fields($location, $fields);

			$data = array(
				'attributes_group'	=> $organized_fields,
			);
			$GLOBALS['phpgw']->xslttpl->add_file(array('attributes_form'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('custom_fields' => $data));
		}

		public function show()
		{
			$resource						 = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$resource['edit_link']			 = self::link(array('menuaction' => 'booking.uiresource.edit',
				'id' => $resource['id']));
			$resource['building_link']		 = self::link(array('menuaction' => 'booking.uibuilding.show',
				'id' => $resource['building_id']));
			$resource['buildings_link']		 = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['schedule_link']		 = self::link(array('menuaction' => 'booking.uiresource.schedule',
				'id' => $resource['id']));
			$resource['cancel_link']		 = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['add_document_link']	 = booking_uidocument::generate_inline_link('resource', $resource['id'], 'add');
			$resource['add_permission_link'] = booking_uipermission::generate_inline_link('resource', $resource['id'], 'add');

			$tabs				 = array();
			$tabs['generic']	 = array(
				'label'	 => lang('Resource'),
				'link'	 => '#resource'
			);
			$active_tab			 = 'generic';
			$resource['tabs']	 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$data				 = array(
				'resource' => $resource
			);
			self::add_javascript('booking', 'booking', 'resource_new.js'); // to render custom fields
			self::render_template_xsl('resource', $data);
		}

		public function schedule()
		{
			$resource						 = $this->bo->get_schedule(phpgw::get_var('id', 'int'), 'booking.uibuilding', 'booking.uiresource');
			$resource['application_link']	 = self::link(array(
				'menuaction'	 => 'booking.uiapplication.add',
				'building_id'	 => $resource['building_id'],
				'building_name'	 => $resource['building_name'],
				'activity_id'	 => $resource['activity_id'],
				'resource'		 => $resource['id']
			));
			$resource['datasource_url']		 = self::link(array(
				'menuaction'		 => 'booking.uibooking.resource_schedule',
				'resource_id'		 => $resource['id'],
				'phpgw_return_as'	 => 'json',
			));

			$resource['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');

			$tabs			 = array();
			$tabs['generic'] = array('label' => lang('Resource Schedule'), 'link' => '#resource_schedule');
			$active_tab		 = 'generic';

			$resource['tabs']		 = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiresource.show',
				'id' => $resource['id']));

			self::add_javascript('booking', 'booking', 'schedule.js');

			phpgwapi_jquery::load_widget("datepicker");

			self::render_template('resource_schedule', array('resource' => $resource));
		}
	}