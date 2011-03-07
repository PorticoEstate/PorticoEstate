<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiagegroup extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'add'			=>	true,
			'show'			=>	true,
			'active'		=>	true,
			'edit'			=>	true
		);

		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->bo = CreateObject('booking.boagegroup');
			
			self::set_active_menu('booking::settings::agegroup');
		}

		public function active()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}else{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => 'booking.uiagegroup.index'));
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
								'value' => lang('New Age Group'),
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
							array(
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'booking.uiagegroup.active'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiagegroup.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'sort',
							'label' => lang('Order')
						),
						array(
							'key' => 'name',
							'label' => lang('Name'),
							'formatter' => 'YAHOO.booking.formatLink'
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
	
			if (!$this->bo->allow_create()) {
				//Remove new button
				unset($data['form']['toolbar']['item'][0]);
			}
			
			if (!$this->bo->allow_write())
			{
				//Remove link to edit
				unset($data['datatable']['field'][0]['formatter']);
				unset($data['datatable']['field'][2]); 
			}
	
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
			$agegroup = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$agegroup = extract_values($_POST, array('name', 'sort', 'description'));
				$agegroup['active'] = true;
				$errors = $this->bo->validate($agegroup);
				if(!$errors)
				{
					$receipt = $this->bo->add($agegroup);
					$this->redirect(array('menuaction' => 'booking.uiagegroup.index', 'id'=>$receipt['id']));
				}
			}
			$this->flash_form_errors($errors);
			$agegroup['cancel_link'] = self::link(array('menuaction' => 'booking.uiagegroup.index'));
			array_set_default($agegroup, 'sort', '0');
			self::render_template('agegroup_new', array('agegroup' => $agegroup));
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
				$resource = array_merge($resource, extract_values($_POST, array('name', 'sort', 'description', 'active')));
				$errors = $this->bo->validate($resource);
				if(!$errors)
				{
					$receipt = $this->bo->update($resource);
					$this->redirect(array('menuaction' => 'booking.uiagegroup.index', 'id'=>$resource['id']));
				}
			}
			$this->flash_form_errors($errors);
			
			$resource['cancel_link'] = self::link(array('menuaction' => 'booking.uiagegroup.index'));
			self::render_template('agegroup_edit', array('resource' => $resource, 'lang' => $lang));
		}
	}
