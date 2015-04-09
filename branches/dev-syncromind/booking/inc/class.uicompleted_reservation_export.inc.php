<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation_export extends booking_uicommon
	{	
		public $public_functions = array
		(
			'index'			=> true,
			'add'				=> true,
			'show'			=> true
		);

		protected 
			$module = 'booking',
			$fields = array('season_id', 'season_name', 'building_id', 'building_name', 'from_', 'to_', 'export_configurations');
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation_export');
			$this->generated_files_bo = CreateObject('booking.bocompleted_reservation_export_file');
			self::set_active_menu('booking::invoice_center::exported_files');
			$this->url_prefix = 'booking.uicompleted_reservation_export';
		}
		
		public function link_to($action, $params = array())
		{
			return $this->link($this->link_to_params($action, $params));
		}
		
		public function redirect_to($action, $params = array())
		{
			return $this->redirect($this->link_to_params($action, $params));
		}
		
		public function link_to_params($action, $params = array())
		{
			if (isset($params['ui'])) {
				$ui = $params['ui'];
				unset($params['ui']);
			} else {
				$ui = 'completed_reservation_export';
			}
			
			$action = sprintf($this->module.'.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		protected function generate_files() {
            $filter_to = phpgw::get_var('filter_to', 'string', 'REQUEST', null);
			$filter_params = is_null($filter_to) ? array() : array('filter_to' => $filter_to);

            if (!($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
				|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking')
				|| $this->bo->has_role(booking_sopermission::ROLE_MANAGER)))			
            {
    			$this->flash_form_errors(array('access_denied' => lang("Access denied")));
    			$this->redirect_to('index', $filter_params);
            }
			//This will read all of the list data using the values of the standard search filters in the ui index view
			$exports = $this->bo->read_all();
			
			if (!is_array($exports) || count($exports['results']) <= 0) {
				$this->flash_form_errors(array('empty_list' => lang("Cannot generate files from empty list")));
				$this->redirect_to('index', $filter_params);
			}
			
			if (is_array($this->generated_files_bo->generate_for($exports['results']))) {
				$this->redirect_to('index', array('ui' => 'completed_reservation_export_file'));
			}
			
			$this->flash_form_errors(array('already_generated' => lang("The invoice data in this list already has generated files")));
			$this->redirect_to('index', $filter_params);
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}

			if (phpgw::get_var('generate_files')) {
				$this->generate_files();
			}
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'date-picker', 
								'name' => 'to',
								'text' => lang('To').':',
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
					),
#					'list_actions' => array(
#						'item' => array(
#							array(
#								'type' => 'submit',
#								'name' => 'generate_files',
#								'value' => lang('Generate files').'...',
#							),
#						)
#					),
				),
				'datatable' => array(
					'source' => $this->link_to('index', array('phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 'id', 'dir' => 'desc'),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'building_id',
							'label' => lang('Building'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
						),
						array(
							'key' => 'season_id',
							'label' => lang('Season'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
						),
						array(
							'key' => 'from_',
							'label' => lang('From'),
						),
						array(
							'key' => 'to_',
							'label' => lang('To'),
						),
						array(
							'key' => 'created_on',
							'label' => lang('Created'),
						),
						array(
							'key' => 'created_by_name',
							'label' => lang('Created by'),
						),
						array(
							'key' => 'total_items',
							'label' => lang('Total Items'),
						),
						array(
							'key' => 'total_cost',
							'label' => lang('Total Cost'),
						),
						array(
							'key' => 'internal',
							'label' => lang('Int. invoice file'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
							'sortable' => false,
						),
						array(
							'key' => 'external',
							'label' => lang('Ext. invoice file'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
							'sortable' => false,
						),
						array(
							'key' => 'link',
							'hidden' => true
						),
					)
				)
			);
            if ( $GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin')
			|| $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking')
            || $this->bo->has_role(booking_sopermission::ROLE_MANAGER))			
            {
            $data['form']['list_actions']= array(
				'item' => array(
					array(
						'type' => 'submit',
						'name' => 'generate_files',
						'value' => lang('Generate files').'...',
					),
				)
            );
            }

			$filters_to = strtotime(extract_values($_GET, array("filter_to")));
			$data['filters'] = date("Y-m-d",$filters_to);


			$this->render_template('datatable', $data);
		}
		
		public function create_link_data($entity, $id_key, $label_key, $null_label, $ui, $action = 'show') {
			$link_data = array(); 
			
			if (isset($entity[$id_key]) && !empty($entity[$id_key])) {
				$link_data['label'] = $entity[$label_key];
				$link_data['href'] = $this->link_to($action, array('ui' => $ui, 'id' => $entity[$id_key]));
			} else {
				$link_data['label'] = $null_label;
			}
			
			return $link_data;
		}
		
		public function create_link_data_by_ref(&$entity, $id_key, $label_key, $null_label, $ui, $action = 'show') { 
			$entity[$id_key] = $this->create_link_data($entity, $id_key, $label_key, $null_label, $ui, $action);
		}
		
		public function add_default_display_data(&$export) {
			$this->create_link_data_by_ref($export, 'season_id', 'season_name', lang('All'), 'season');
			$this->create_link_data_by_ref($export, 'building_id', 'building_name', lang('All'), 'building');
			
			$export['created_on'] = pretty_timestamp($export['created_on']);
			$export['from_'] = pretty_timestamp($export['from_']);
			$export['to_'] = pretty_timestamp($export['to_']);
			$export['index_link'] = $this->link_to('index');
			$this->add_export_configurations_display_data($export);
		}
		
		public function add_export_configurations_display_data(&$export) {
			if (is_array($export['export_configurations'])) {
				foreach($export['export_configurations'] as $type => $conf) {
					if (!is_string($type)) {
						throw new LogicException("Invalid export configuration type");
					}
					
					$export[$type] = $this->create_link_data($conf, 'export_file_id', 'export_file_id', lang('Not generated'), 'completed_reservation_export_file');
				}
			}
		}

		public function index_json()
		{
			$this->db = $GLOBALS['phpgw']->db;
			$exports = $this->bo->read();
			array_walk($exports["results"], array($this, "_add_links"), $this->module.".uicompleted_reservation_export.show");

			foreach($exports["results"] as &$export) {
				$export = $this->bo->so->initialize_entity($export);
				$this->add_default_display_data($export);

				$sql = "SELECT account_lastname, account_firstname FROM phpgw_accounts WHERE account_lid = '".$export['created_by_name']."'";
				$this->db->query($sql);
				while ($record = array_shift($this->db->resultSet)) {
					$export['created_by_name'] = $record['account_firstname']." ".$record['account_lastname'];
				}
			}
			$results = $this->yui_results($exports);
			return $results;
		}
		
		public function show()
		{
			$export = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$this->add_default_display_data($export);
			$this->add_template_file('helpers');
			self::render_template('completed_reservation_export', array('export' => $export));
		}
		
		protected function get_export_key() {
			return phpgw::get_var('export_key', 'string', 'REQUEST', null);
		}
		
		public function pre_validate($export) {
			if (!is_array($errors = $this->bo->validate($export))) { return; }
			
			$export_errors = array_intersect_key(
				$errors, 
				array('nothing_to_export' => true, 'invalid_customer_ids' => true)
			);
			
			if (!count($export_errors) > 0) { return; }
			
			$redirect_params = array('ui' => 'completed_reservation');
			
			if ($export_key = $this->get_export_key()) {
				$redirect_params['export_key'] = $export_key;
			}
			
			$this->flash_form_errors($export_errors);
			$this->redirect_to('index', $redirect_params);
		}
		
		public function add() {
			//Values passed in from the "Export"-action in uicompleted_reservation.index
			$export = extract_values($_GET, $this->fields);
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$export = array();
				$export = extract_values($_POST, $this->fields);
				
				//Fill in a dummy value (so as to temporarily pass validation), this will then be 
				//automatically filled in by bo->add process later on.
				$export['from_'] = date('Y-m-d H:i:s');
				
				$errors = $this->bo->validate($export);
				if(!$errors)
				{
					try {
						$receipt = $this->bo->add($export);
						$this->redirect_to('index');
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not add object due to insufficient permissions');
					}
				}
			}
			
			if (!isset($export['to_']) || empty($export['to_'])) {
				$export['to_'] = date('Y-m-d');
			}
			
			$this->pre_validate($export);
			
			$this->flash_form_errors($errors);
			
			$cancel_params = array('ui' => 'completed_reservation');
			if ($export_key = $this->get_export_key()) {
				$cancel_params['export_key'] = $export_key;
			}
			
			$export['cancel_link'] = $this->link_to('index', $cancel_params);
			
			$this->render_template('completed_reservation_export_form', array('new_form' => true, 'export' => $export));
		}
	}
