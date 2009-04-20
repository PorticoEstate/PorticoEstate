<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiactivities extends booking_uicommon
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
			$this->bo = CreateObject('booking.boactivities');
			self::set_active_menu('booking::activities');
		}
		
		function treeitem($children, $parent_id)
		{
			$nodes = array();
			foreach($children[$parent_id] as $activity)
			{
				$nodes[] = array("type"=>"text", "href" => self::link(array('menuaction' => 'booking.uiactivities.edit', 'id' => $activity['id'])), "target" => "_self", "label"=>$activity['name'], 'children' => $this->treeitem($children, $activity['id']));
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
			
			#echo("<pre>");
			$treedata = json_encode($this->treeitem($children, null));
			#echo("</pre>");
			#die;
			
			
			
			//$resources = $this->bo->read(array("query" => "parent_id", "filters"=>NULL));
			//foreach($resources['results'] as $reKey => $reValue)
			//{
			//$resources['results'][$reKey]['sub'] = $this->bo->smart_read(array("filters"=>array('parent_id' => $reValue['id'])));
			//}

						
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
								'value' => lang('New activities'),
								'href' => self::link(array('menuaction' => 'booking.uiactivities.add'))
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
					'source' => self::link(array('menuaction' => 'booking.uiactivities.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'parent_id',
							'label' => lang('Parent')
						),
						array(
							'key' => 'name',
							'label' => lang('activities Name'),
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
					$navi['add'] = self::link(array('menuaction' => 'booking.uiactivities.add'));
					$lang['add'] = lang('Add Activity');
			self::render_template('activities_index', array('data' => $data, 'treedata' => $treedata, 'navi' => $navi, 'lang' => $lang));
		}

		public function index_json()
		{
			$resources = $this->bo->read();
			foreach($resources['results'] as &$resource)
			{
				$resource['link'] = $this->link(array('menuaction' => 'booking.uiactivities.show', 'id' => $resource['id']));
			}
			$data = array
			(
				'ResultSet' => array(
					"totalResultsAvailable" => $resources['total_records'], 
					"Result" => $resources['results']
				)
			);
			return $data;
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
			//$_POST['description'] = "blablablabla";
			//$_POST['parent_id'] = 1;
			//echo("<pre>");
			//print_r($_POST);
			//echo("</pre>");
			//die;
				if ( $_POST['parent_id'] == 0 )
				{
					$_POST['parent_id'] = null;
				}
				$resource = extract_values($_POST, array('name', 'description', 'parent_id'));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->add($resource);
					$this->redirect(array('menuaction' => 'booking.uiactivities.show', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			//self::add_javascript('booking', 'booking', 'activities_new.js');
			//phpgwapi_yui::load_widget('datatable');
			//phpgwapi_yui::load_widget('autocomplete');
			
				/**
				 * Translation
				 **/
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
			//echo("<pre>");
			//print_r($dropdown_list);
			//echo("</pre>");
			//die;
			self::render_template('activities_new', array('resource' => $resource, 'lang' => $lang, 'dropdown' => $dropdown));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$resource = $this->bo->read_single($id);
			$parent_resource = $this->bo->read_single($resource['parent_id']);
			$dropdown = $this->bo->read();
			
			//echo("<pre>");
			//print_r($resource);
			//echo("</pre>");
			//echo("<pre>");
			//print_r($parent_resource);
			//echo("</pre>");
			//die;
			$resource['id'] = $id;
			$resource['resource_link'] = self::link(array('menuaction' => 'booking.uiactivities.show', 'id' => $resource['id']));
			$resource['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$resource['activities_link'] = self::link(array('menuaction' => 'booking.uiactivities.index'));
			$resource['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$resource = array_merge($resource, extract_values($_POST, array('name', 'description', 'parent_id')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiactivities.show', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			
				/**
				 * Translation
				 **/
					$lang['title'] = lang('New activities');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$lang['activities'] = lang('activities');
					$lang['save'] = lang('Save');
					$lang['activities'] = lang('activities');
					$lang['parent'] = lang('Set new parent');
					$lang['novalue'] = lang('No Parent');
					$lang['current_parent'] = lang('Current Parent');
					$lang['cancel'] = lang('Cancel');
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivities.index'));
			self::render_template('activities_edit', array('resource' => $resource, 'lang' => $lang, 'parent' => $parent_resource, 'dropdown' => $dropdown));
		}
		
		public function show()
		{
			$resource = $this->bo->read_single(phpgw::get_var('id', 'GET'));
					$lang['title'] = lang('New activities');
					$lang['name'] = lang('Name');
					$lang['description'] = lang('Description');
					$lang['resource'] = lang('Resource');
					$lang['create'] = lang('Create');
					$lang['buildings'] = lang('Buildings');
					$lang['resources'] = lang('Resources');
					$resource['edit_link'] = self::link(array('menuaction' => 'booking.uiactivities.edit', 'id' => $resource['id']));
					$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivities.index'));
					$lang['activities'] = lang('activities');
					$lang['save'] = lang('Save');
					$lang['edit'] = lang('Edit');
					$lang['cancel'] = lang('Cancel');
			$data = array(
				'resource'	=>	$resource
			);
			self::render_template('activities', array('activities' => $data, 'lang' => $lang));
		}
	}
