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
			
			self::process_booking_unauthorized_exceptions();
			
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
					if($activity['active'] == false)
					{
						continue;
					}
					$node = array(
						"type"=>"text", 
						"href" => self::link(array('menuaction' => 'booking.uiactivity.edit', 
						                           'id' => $activity['id'])), 'target' => '_self', 
						                           'label' => $activity['name'], 
						                           'children' => $this->treeitem($children, $activity['id'])
					);
					if (!$this->bo->allow_write($activity)) {
						unset($node['href']);
					}
					
					$nodes[] = $node;
				}
			}
			return $nodes;
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			$activities = $this->bo->so->read(array('sort'=>'name', 'dir'=>'ASC'));
			$children = array();
			foreach($activities['results'] as $activity)
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
			if ($this->bo->allow_create())
			{
				$navi['add'] = self::link(array('menuaction' => 'booking.uiactivity.add'));
			}
			self::render_template('activities', array('treedata' => $treedata, 'navi' => $navi));
		}

		public function index_json()
		{
			$activities = $this->bo->read();
			array_walk($activities["results"], array($this, "_add_links"), "booking.uiactivity.show");
			return $this->yui_results($activities);
		}

		public function add()
		{
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ( $_POST['parent_id'] == '0' )
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
			$dropdown = $this->bo->read();
			$activity['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			self::render_template('activity_new', array('activity' => $activity, 'dropdown' => $dropdown));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$activity = $this->bo->read_single($id);
			$parent_activity = $this->bo->read_single($activity['parent_id']);
			$dropdown = $this->bo->read();
			$activity['id'] = $id;
			$activity['activities_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			$activity['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ( $_POST['parent_id'] == '0' )
				{
					$_POST['parent_id'] = null;
				}
				$activity = array_merge($activity, extract_values($_POST, array('name', 'active', 'description', 'parent_id')));
				$errors = $this->bo->validate($activity);
				if(!$errors)
				{
					$receipt = $this->bo->update($activity);
					$this->redirect(array('menuaction' => 'booking.uiactivity.index'));
				}
			}
			$this->flash_form_errors($errors);
			$activity['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			self::render_template('activity_edit', array('activity' => $activity, 'parent' => $parent_activity, 'dropdown' => $dropdown));
		}
	}
