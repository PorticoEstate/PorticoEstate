<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiagegroup extends booking_uicommon
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
			$this->bo = CreateObject('booking.boagegroup');
			self::set_active_menu('booking::agegroup');
		}
		
		function treeitem($children, $parent_id)
		{
			$nodes = array();
			foreach($children[$parent_id] as $activity)
			{
				$nodes[] = array("type"=>"text", "label"=>$activity['name'], 'children' => $this->treeitem($children, $activity['id']));
			}
			return $nodes;
			
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('treeview');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New agegroup group'),
								'href' => self::link(array('menuaction' => 'booking.uiagegroup.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uiagegroup.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('agegroup Title'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'description',
							'label' => lang('Description')
						),
						array(
							'key' => 'active',
							'label' => lang('Active')
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
					$navi['add'] = self::link(array('menuaction' => 'booking.uiagegroup.add'));
					$lang['add'] = lang('Add agegroup');
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$groups = $this->bo->read();
			foreach($groups['results'] as &$agegroup)
			{
				$agegroup['link'] = $this->link(array('menuaction' => 'booking.uiagegroup.edit', 'id' => $agegroup['id']));
				$agegroup['active'] = $agegroup['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($groups);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if(!$_POST['active'])
				{
					$_POST['active'] = 0;
				}
				$resource = extract_values($_POST, array('name', 'description', 'active'));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->add($resource);
					$this->redirect(array('menuaction' => 'booking.uiagegroup.index', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('New agegroup');
					$lang['activity'] = lang('agegroup');
					$lang['name'] = lang('agegroup Title');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['active'] = lang('Active');
					$lang['inactive'] = lang('Inactive');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['agegroup'] = lang('agegroup');
					$lang['parent'] = lang('Parent agegroup');
					$lang['novalue'] = lang('No Parent');
			self::render_template('agegroup_new', array('resource' => $resource, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['resource_link'] = self::link(array('menuaction' => 'booking.uiagegroup.show', 'id' => $resource['id']));
			$resource['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['agegroup_link'] = self::link(array('menuaction' => 'booking.uiagegroup.index'));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, array('name', 'description', 'active')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiagegroup.index', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('Edit agegroup group');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['agegroup'] = lang('agegroup');
					$lang['active'] = lang('Active');
					$lang['inactive'] = lang('Inactive');
					$lang['save'] = lang('Save');
					$lang['agegroup'] = lang('agegroup');
					$lang['parent'] = lang('Set new parent');
					$lang['novalue'] = lang('No Parent');
					$lang['current_parent'] = lang('Current Parent');
					$lang['cancel'] = lang('Cancel');
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiagegroup.index'));
			self::render_template('agegroup_edit', array('resource' => $resource, 'lang' => $lang));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
					$lang['title'] = lang('New agegroup');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiagegroup.edit', 'id' => $resource['id']));
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiagegroup.index'));
					$lang['agegroup'] = lang('agegroup');
					$lang['save'] = lang('Save');
					$lang['edit'] = lang('Edit');
					$lang['cancel'] = lang('Cancel');
			$data = array(
				'resource'	=>	$resource
			);
			self::render_template('agegroup', array('agegroup' => $data, 'lang' => $lang));
		}
	}
