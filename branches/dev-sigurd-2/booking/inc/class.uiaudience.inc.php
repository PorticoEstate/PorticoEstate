<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiaudience extends booking_uicommon
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
			$this->bo = CreateObject('booking.boaudience');
			self::set_active_menu('booking::audience');
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
			
			if(	extract_values($_GET, array('sessionShowAll')) &&
				!$_SESSION['ActiveSession'])
			{
				$this->bo->set_active_session();
			}
			
			if( extract_values($_GET, array('unsetShowAll')) &&
				$_SESSION['ActiveSession'])
			{
				$this->bo->actUnSet();
			}
			
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			phpgwapi_yui::load_widget('treeview');
			$sessionLink = $this->link(array('menuaction' => 'booking.uiaudience.index', 'sessionShowAll' => 'activate'));
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New Audience group'),
								'href' => self::link(array('menuaction' => 'booking.uiaudience.add'))
							),
							array('type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							array(
								'type' => 'checkbox',
								'name' => 'showall',
								'value' => lang('Show all'),
								'text' => (" [ ".lang('Show all')." ] "),
								'onClick' => ("window.location='".$sessionLink."'")
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiaudience.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'name',
							'label' => lang('Audience Title'),
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
					$navi['add'] = self::link(array('menuaction' => 'booking.uiaudience.add'));
					$lang['add'] = lang('Add Audience');
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			
			$groups = $this->bo->read(array('filters' => "active=1"));
			
			foreach($groups['results'] as &$audience)
			{
				$audience['link'] = $this->link(array('menuaction' => 'booking.uiaudience.edit', 'id' => $audience['id']));
				$audience['active'] = $audience['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($audience);
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
					$this->redirect(array('menuaction' => 'booking.uiaudience.index', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('New Audience');
					$lang['activity'] = lang('Audience');
					$lang['name'] = lang('Audience Title');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['active'] = lang('Active');
					$lang['inactive'] = lang('Inactive');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['audience'] = lang('audience');
					$lang['parent'] = lang('Parent Audience');
					$lang['novalue'] = lang('No Parent');
			self::render_template('audience_new', array('resource' => $resource, 'lang' => $lang));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$resource['id'] = $id;
			$resource['resource_link'] = self::link(array('menuaction' => 'booking.uiaudience.show', 'id' => $resource['id']));
			$resource['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['audience_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, array('name', 'description', 'active')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiaudience.index', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('Edit Audience group');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['audience'] = lang('audience');
					$lang['active'] = lang('Active');
					$lang['inactive'] = lang('Inactive');
					$lang['save'] = lang('Save');
					$lang['audience'] = lang('audience');
					$lang['parent'] = lang('Set new parent');
					$lang['novalue'] = lang('No Parent');
					$lang['current_parent'] = lang('Current Parent');
					$lang['cancel'] = lang('Cancel');
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
			self::render_template('audience_edit', array('resource' => $resource, 'lang' => $lang));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
					$lang['title'] = lang('New audience');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiaudience.edit', 'id' => $resource['id']));
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
					$lang['audience'] = lang('audience');
					$lang['save'] = lang('Save');
					$lang['edit'] = lang('Edit');
					$lang['cancel'] = lang('Cancel');
			$data = array(
				'resource'	=>	$resource
			);
			self::render_template('audience', array('audience' => $data, 'lang' => $lang));
		}
	}
