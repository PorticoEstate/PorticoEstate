<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiactivity extends booking_uicommon
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
			$this->bo = CreateObject('booking.boactivity');
			self::set_active_menu('booking::settings::activity');
		}
		
		function treeitem($children, $parent_id)
		{
			$nodes = array();
			if(is_array($children[$parent_id]))
			{
				foreach($children[$parent_id] as $activity)
				{
					$nodes[] = array("type"=>"text", "href" => self::link(array('menuaction' => 'booking.uiactivity.edit', 'id' => $activity['id'])), "target" => "_self", "label"=>$activity['name'], 'children' => $this->treeitem($children, $activity['id']));
				}
			}
			return $nodes;
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			$resources = $this->bo->read();
			$children = array();
			foreach($resources['results'] as $activity)
			{
				if(!array_key_exists($activity['id'], $children))
				{
					$children[$activity['id']] = array();	
				}
				if(!array_key_exists($activity['parent_id'], $children))
				{
					$children[$activity['parent_id']] = array();	
				}				
				$children[$activity['parent_id']][] = $activity;
			}
			$treedata = json_encode($this->treeitem($children, null));
						
			phpgwapi_yui::load_widget('treeview');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New activity'),
								'href' => self::link(array('menuaction' => 'booking.uiactivity.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uiactivity.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'parent_id',
							'label' => lang('Parent')
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
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
			$navi['add'] = self::link(array('menuaction' => 'booking.uiactivity.add'));
			$lang['add'] = lang('Add Activity');
			self::render_template('activities', array('data' => $data, 'treedata' => $treedata, 'navi' => $navi, 'lang' => $lang));
		}

		public function index_json()
		{
			$resources = $this->bo->read();
			array_walk($resources["results"], array($this, "_add_links"), "booking.uiactivity.show");
			return $this->yui_results($resources);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ( $_POST['parent_id'] == 0 )
				{
					$_POST['parent_id'] = null;
				}
				$activity = extract_values($_POST, array('name', 'description', 'parent_id'));
				$errors = $this->bo->validate($activity);
				if(!$errors)
				{
					$receipt = $this->bo->add($activity);
					$this->redirect(array('menuaction' => 'booking.uiactivity.index'));
				}
			}
			$this->flash_form_errors($errors);
			$lang['title'] = lang('New Activity');
			$lang['activity'] = lang('Activity');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['resource'] = lang('Resource');
			$lang['create'] = lang('Create');
			$lang['buildings'] = lang('Buildings');
			$lang['resources'] = lang('Resources');
			$lang['activities'] = lang('activities');
			$lang['parent'] = lang('Parent Aktivity');
			$lang['novalue'] = lang('No Parent');
	
			$dropdown = $this->bo->read();
			self::render_template('activity_new', array('activity' => $activity, 'lang' => $lang, 'dropdown' => $dropdown));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$activity = $this->bo->read_single($id);
			$parent_activity = $this->bo->read_single($activity['parent_id']);
			$dropdown = $this->bo->read();
			$activity['id'] = $id;
			$activity['resource_link'] = self::link(array('menuaction' => 'booking.uiactivity.show', 'id' => $activity['id']));
			$activity['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$activity['activities_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			$activity['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$activity = array_merge($activity, extract_values($_POST, array('name', 'description', 'parent_id')));
				$errors = $this->bo->validate($activity);
				if(!$errors)
				{
					$receipt = $this->bo->update($activity);
					$this->redirect(array('menuaction' => 'booking.uiactivity.index'));
				}
			}
			$this->flash_form_errors($errors);
			$lang['title'] = lang('New activity');
			$lang['name'] = lang('Name');
			$lang['description'] = lang('Description');
			$lang['resource'] = lang('Resource');
			$lang['create'] = lang('Create');
			$lang['buildings'] = lang('Buildings');
			$lang['resources'] = lang('Resources');
			$lang['activities'] = lang('activities');
			$lang['save'] = lang('Save');
			$lang['activities'] = lang('activities');
			$lang['parent'] = lang('Parent activity');
			$lang['novalue'] = lang('No Parent');
			$lang['cancel'] = lang('Cancel');
			$activity['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			self::render_template('activity_edit', array('activity' => $activity, 'lang' => $lang, 'parent' => $parent_activity, 'dropdown' => $dropdown));
		}
	}
