<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiactivity extends booking_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'add' => true,
			'show' => true,
			'edit' => true
		);

		public function __construct()
		{
			parent::__construct();

			self::process_booking_unauthorized_exceptions();

			$this->bo = CreateObject('booking.boactivity');

			self::set_active_menu('booking::settings::activity');
		}

		function treeitem( $children, $parent_id, $show_all )
		{
			$nodes = array();
			if (is_array($children[$parent_id]))
			{
				foreach ($children[$parent_id] as $activity)
				{
					if ($activity['active'] == false && $show_all == false)
					{
						continue;
					}
					$node = array(
						"type" => "text",
						"href" => self::link(array('menuaction' => 'booking.uiactivity.edit',
							'id' => $activity['id'])), 'target' => '_self',
						'label' => $activity['name'],
						'text' => $activity['name'],
						'children' => $this->treeitem($children, $activity['id'], $show_all)
					);
					if (!$this->bo->allow_write($activity))
					{
						unset($node['href']);
					}

					$nodes[] = $node;
				}
			}
			return $nodes;
		}

		public function query()
		{
			
		}

		public function index()
		{
			$show_all = phpgw::get_var('show_all') || false;
			$activities = $this->bo->so->read(array('sort' => 'name', 'dir' => 'ASC'));
			$children = array();
			foreach ($activities['results'] as $activity)
			{
				if (!array_key_exists($activity['id'], $children))
				{
					$children[$activity['id']] = array();
				}
				if (!array_key_exists($activity['parent_id'], $children))
				{
					$children[$activity['parent_id']] = array();
				}
				$children[$activity['parent_id']][] = $activity;
			}
			$treedata = json_encode($this->treeitem($children, null, $show_all));
			phpgwapi_jquery::load_widget('treeview');
			$links = array(
				'show_inactive' => self::link(array('menuaction' => 'booking.uiactivity.index',
					'show_all' => 'true')),
				'hide_inactive' => self::link(array('menuaction' => 'booking.uiactivity.index',
					'show_all' => ''))
			);

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Booking'), 'link' => '#activities');
			$active_tab = 'generic';
			$tabs = phpgwapi_jquery::tabview_generate($tabs, $active_tab);

			if ($this->bo->allow_create())
			{
				$links['add'] = self::link(array('menuaction' => 'booking.uiactivity.add'));
			}
			self::render_template_xsl('activities', array('treedata' => $treedata, 'links' => $links,
				'show_all' => $show_all, 'tabs' => $tabs));
		}

		public function add()
		{
			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ($_POST['parent_id'] == '0')
				{
					$_POST['parent_id'] = null;
				}
				$activity = extract_values($_POST, array('name', 'description', 'parent_id'));
				$activity['active'] = '1';
				$errors = $this->bo->validate($activity);
				if (!$errors)
				{
					$receipt = $this->bo->add($activity);
					//Add locations for application and resources
					$GLOBALS['phpgw']->hooks->single(array('id' => $receipt['id'], 'name' => $activity['name'],
						'location' => 'activity_add'), 'booking');
					$this->redirect(array('menuaction' => 'booking.uiactivity.index'));
				}
			}
			$this->flash_form_errors($errors);
			$activities = $this->bo->fetch_activities();
			$activities = $activities['results'];
			$activity['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Activity New'), 'link' => '#activity_add');
			$active_tab = 'generic';

			$activity['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$activity['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('activity_new', array('activity' => $activity, 'activities' => $activities));
		}

		public function edit()
		{
			$id = phpgw::get_var('id', 'int');
			$activity = $this->bo->read_single($id);
			$parent_activity = $this->bo->read_single($activity['parent_id']);
			$activities = $this->bo->fetch_activities();
			$activities = $activities['results'];
			$activity['id'] = $id;
			$activity['activities_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));
			$activity['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if ($_POST['parent_id'] == '0')
				{
					$_POST['parent_id'] = null;
				}
				$activity = array_merge($activity, extract_values($_POST, array('name', 'active',
					'description', 'parent_id')));
				$errors = $this->bo->validate($activity);
				if (!$errors)
				{
					$receipt = $this->bo->update($activity);
					//Edit locations for application and resources
					$GLOBALS['phpgw']->hooks->single(array('id' => $id, 'name' => $activity['name'],
						'location' => 'activity_edit'), 'booking');
					$this->redirect(array('menuaction' => 'booking.uiactivity.index'));
				}
			}
			$this->flash_form_errors($errors);
			$activity['cancel_link'] = self::link(array('menuaction' => 'booking.uiactivity.index'));

			$tabs = array();
			$tabs['generic'] = array('label' => lang('Activity Edit'), 'link' => '#activity_edit');
			$active_tab = 'generic';

			$activity['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$activity['validator'] = phpgwapi_jquery::formvalidator_generate(array('location',
					'date', 'security', 'file'));

			self::render_template_xsl('activity_edit', array('activity' => $activity, 'parent' => $parent_activity,
				'activities' => $activities));
		}
	}