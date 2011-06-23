<?php
	phpgw::import_class('booking.uicommon');

	class booking_uiaudience extends booking_uicommon
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
			
			$this->bo = CreateObject('booking.boaudience');
			
			self::set_active_menu('booking::settings::audience');
		}
		
		public function active()
		{
			if(isset($_SESSION['showall']) && !empty($_SESSION['showall']))
			{
				$this->bo->unset_show_all_objects();
			}else{
				$this->bo->show_all_objects();
			}
			$this->redirect(array('menuaction' => 'booking.uiaudience.index'));
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
								'type' => 'link',
								'value' => $_SESSION['showall'] ? lang('Show only active') : lang('Show all'),
								'href' => self::link(array('menuaction' => 'booking.uiaudience.active'))
							),
						)
					),
				),
				'datatable' => array(
					'source' => self::link(array('menuaction' => 'booking.uiaudience.index', 'phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'sort',
							'label' => lang('Order')
						),
						array(
							'key' => 'name',
							'label' => lang('Target Audience'),
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
			
			foreach($groups['results'] as &$audience)
			{
				$audience['link'] = $this->link(array('menuaction' => 'booking.uiaudience.edit', 'id' => $audience['id']));
				$audience['active'] = $audience['active'] ? lang('Active') : lang('Inactive');
			}
			return $this->yui_results($groups);
		}

		public function add()
		{
			$errors = array();
			$audience = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$audience = extract_values($_POST, array('name', 'sort', 'description'));
				$audience['active'] = 1;
				$errors = $this->bo->validate($audience);
				if(!$errors)
				{
					$receipt = $this->bo->add($audience);
					$this->redirect(array('menuaction' => 'booking.uiaudience.index'));
				}
			}
			array_set_default($audience, 'sort', '0');
			$this->flash_form_errors($errors);
			$audience['cancel_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
			self::render_template('audience_new', array('audience' => $audience));
		}

		public function edit()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$audience = $this->bo->read_single($id);
			$audience['id'] = $id;
			$audience['resource_link'] = self::link(array('menuaction' => 'booking.uiaudience.show', 'id' => $audience['id']));
			$audience['resources_link'] = self::link(array('menuaction' => 'booking.uiresource.index'));
			$audience['audience_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
			$audience['building_link'] = self::link(array('menuaction' => 'booking.uibuilding.index'));
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$audience = array_merge($audience, extract_values($_POST, array('name', 'sort', 'description', 'active')));
				$errors = $this->bo->validate($audience);
				if(!$errors)
				{
					$audience = $this->bo->update($audience);
					$this->redirect(array('menuaction' => 'booking.uiaudience.index', 'id'=>$audience['id']));
				}
			}
			$this->flash_form_errors($errors);
			$audience['cancel_link'] = self::link(array('menuaction' => 'booking.uiaudience.index'));
			self::render_template('audience_edit', array('audience' => $audience));
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
