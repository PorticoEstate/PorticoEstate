<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiequipment extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'edit'			=>	true
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.boequipment');
			self::set_active_menu('booking::equipment');
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
					'source' => self::link(array('menuaction' => 'booking.uiequipment.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'resource_name',
							'label' => lang('Resource name')
						),
						array(
							'key' => 'name',
							'label' => lang('Equipment Name'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'description',
							'label' => lang('Description')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			
			if ($this->bo->allow_create()) {
				array_unshift($data['form']['toolbar']['item'], array(
					'type' => 'link',
					'value' => lang('New equipment'),
					'href' => self::link(array('menuaction' => 'booking.uiequipment.add'))
				));
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			return $this->bo->populate_json_data("booking.uiequipment");
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = extract_values($_POST, array('name', 'description', 'resource_id', 'resource_name'));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($resource);
						$this->redirect(array('menuaction' => 'booking.uiequipment.show', 'id'=>$receipt['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'equipment_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('New Equipment');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['equipment'] = lang('Equipment');
			
			
			
			
			
			
			
			
			self::render_template('equipment_new', array('resource' => $resource, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			
			$this->add_breadcrumbs($resource);
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, array('name', 'description', 'resource_id', 'resource_name')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($resource);
						$this->redirect(array('menuaction' => 'booking.uiequipment.show', 'id'=>$resource['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			$this->flash_form_errors($errors);
			self::add_javascript('booking', 'booking', 'equipment_new.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('autocomplete');
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('New Equipment');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['equipment'] = lang('Equipment');
					$lang['save'] = lang('Save');
			self::render_template('equipment_edit', array('resource' => $resource, 'lang' => $lang));
		}
		
		private function add_breadcrumbs(array &$resource)
		{
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$resource['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['resource_link'] = self::link(array('menuaction' => 'booking.uiresource.show', 'id' => $resource['resource_id']));
			$resource['equipment_link'] = self::link(array('menuaction' => 'booking.uiequipment.index'));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiequipment.edit', 'id' => $resource['id']));

			$this->add_breadcrumbs($resource);
			
			$resource['name-field'] = lang('Name');
			$resource['description-field'] = lang('Description');
			$resource['resource-field'] = lang('Resource');
			$resource['edit-link'] = lang('Edit');
			$resource['top-nav-bar-buildings'] = lang('Buildings');
			$resource['top-nav-bar-resources'] = lang('Resources');
			$resource['top-nav-bar-equipment'] = lang('Equipment');
			$data = array(
				'resource'	=>	$resource
			);
			self::render_template('equipment', $data);
		}
	}
