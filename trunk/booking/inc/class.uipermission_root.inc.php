<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.account_ui_utils');

	class booking_uipermission_root extends booking_uicommon
	{
				
		public 
			$public_functions = array(
				'index'				=> true,
				'index_accounts'	=> true,
				'add'				=> true,
				'delete'			=> true,
			);
		
		public function __construct()
		{
			parent::__construct();
			
			self::process_booking_unauthorized_exceptions();
			
			$this->set_business_object();
			
			$this->fields = array('subject_id', 'subject_name', 'role');
			
			self::set_active_menu('booking::settings::permissions');
		}
		
		protected function set_business_object(booking_bopermission_root $bo = null)
		{
			$this->bo = is_null($bo) ? $this->create_business_object() : $bo;
		}
		
		protected function create_business_object()
		{
			return CreateObject('booking.bopermission_root');
		}
		
		public function generate_link_params($action, $params = array())
		{
			$action = sprintf('booking.uipermission_root.%s', $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		public function generate_link($action, $params = array())
		{
			return $this->link($this->generate_link_params($action, $params));
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}

			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			
			// if($_SESSION['showall'])
			// {
			// 	$active_botton = lang('Show only active');
			// }else{
			// 	$active_botton = lang('Show all');
			// }
			
						
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'link',
								'value' => lang('New Root Permission'),
								'href' => $this->generate_link('add')
							),
							array(
								'type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
							// array(
							// 	'type' => 'link',
							// 	'value' => $active_botton,
							// 	'href' => self::link(array('menuaction' => $this->generate_link('active')))
							// ),
						)
					),
				),
				'datatable' => array(
					'source' => $this->generate_link('index', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'subject_name',
							'label' => lang('User'),
							'sortable' => true,
						),
						array(
							'key' => 'role',
							'label' => lang('Role'),
							'sortable' => true,
						),
						array(
							'key' => 'actions',
							'label' => lang('Actions'),
							'sortable' => false,
							'formatter' => 'YAHOO.booking.'.sprintf('formatGenericLink(\'%s\')', lang('delete')),
						),
						// array(
						// 	'key' => 'link',
						// 	'hidden' => true
						// )
					)
				)
			);
			
			if (!$this->bo->allow_delete()) {
				unset($data['datatable']['field'][2]); //Delete action
			}
			
			if (!$this->bo->allow_create()) {
				unset($data['form']['toolbar']['item'][0]); //New button
			}
			
			self::render_template('datatable', $data);
		}

		public function index_json()
		{
			$this->db = $GLOBALS['phpgw']->db;

			$permissions = $this->bo->read();
			foreach($permissions['results'] as &$permission)
			{
				$permission['link'] = $this->generate_link('edit', array('id' => $permission['id']));
				#$permission['active'] = $permission['active'] ? lang('Active') : lang('Inactive');
				$permission['actions'] = array(
					$this->generate_link('delete', array('id' => $permission['id'])),
				);

				$sql = "SELECT account_lastname, account_firstname FROM phpgw_accounts WHERE account_lid = '".$permission['subject_name']."'";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet)) {
					$permission['subject_name'] = $record['account_firstname']." ".$record['account_lastname'];
				}
			}
			return $this->yui_results($permissions);
		}
		
		public function index_accounts()
		{
			return booking_account_ui_utils::yui_accounts();
		}
		
		protected function get_available_roles()
		{
			$roles = array();
			foreach($this->bo->get_roles() as $role) { $roles[$role] = self::humanize($role); }
			return $roles;
		}
		
		protected function add_default_display_data(&$permission_data)
		{
			$permission_data['available_roles'] 	= $this->get_available_roles();
			$permission_data['permissions_link'] 	= $this->generate_link('index');
			$permission_data['cancel_link'] 		= $this->generate_link('index');
		}
		
		// public function show()
		// {
		// 	$id = intval(phpgw::get_var('id', 'GET'));
		// 	$permission = $this->bo->read_single($id);
		// 	$this->add_default_display_data($permission);
		// 	self::render_template('permission_root', array('permission' => $permission));
		// }
		
		public function add()
		{	
			$errors = array();
			$permission = array();
			
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$permission = extract_values($_POST, $this->fields);
				$errors = $this->bo->validate($permission);
				if(!$errors)
				{
					$receipt = $this->bo->add($permission);
					$this->redirect($this->generate_link_params('index'));
				}
			}
			
			self::add_javascript('booking', 'booking', 'permission_root.js');
			
			$this->add_default_display_data($permission);
			
			$this->flash_form_errors($errors);

			self::render_template('permission_root_form', array('permission' => $permission));
		}
		
		// public function edit()
		// {
		// 	$id = intval(phpgw::get_var('id', 'GET'));
		// 	$permission = $this->bo->read_single($id);
		// 	
		// 	$errors = array();
		// 	if($_SERVER['REQUEST_METHOD'] == 'POST')
		// 	{
		// 		$permission = array_merge($permission, extract_values($_POST, $this->fields));
		// 		$errors = $this->bo->validate($permission);
		// 		if(!$errors)
		// 		{
		// 			$receipt = $this->bo->update($permission);
		// 			$this->redirect($this->generate_link_params('index'));
		// 		}
		// 	}
		// 	
		// 	self::add_javascript('booking', 'booking', 'permission_root.js');
		// 	
		// 	$this->add_default_display_data($permission);
		// 	
		// 	$this->flash_form_errors($errors);
		// 	
		// 	self::render_template('permission_root_form', array('permission' => $permission));
		// }
		
		public function delete()
		{
			$id = intval(phpgw::get_var('id', 'GET'));
			$this->bo->delete($id);
			$this->redirect($this->generate_link_params('index'));
		}
	}