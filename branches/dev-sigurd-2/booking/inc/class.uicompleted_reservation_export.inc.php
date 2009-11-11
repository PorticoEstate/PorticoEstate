<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation_export extends booking_uicommon
	{	
		public $public_functions = array
		(
			'index'			=>	true,
			'add'				=> true,
		);

		protected 
			$module = 'booking',
			$fields = array('season_id', 'season_name', 'building_id', 'building_name', 'from_', 'to_', 'export_configurations');
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation_export');
			$this->generated_files_bo = CreateObject('booking.bocompleted_reservation_export_file');
			self::set_active_menu('booking::invoice_exports');
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
			//This will read the data using the values of the standard search filters in the ui index view
			$exports = $this->bo->read_all();
			
			if (is_array($exports) && count($exports['results']) > 0) {
				if ($this->generated_files_bo->generate_for($exports['results'])) {
					$this->redirect_to('index', array('ui' => 'completed_reservation_export_file'));
				}
			}
			
			$this->flash_form_errors(array('nothing_to_export' => lang("Nothing to export")));
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
					'list_actions' => array(
						'item' => array(
							array(
								'type' => 'submit',
								'name' => 'generate_files',
								'value' => lang('Generate files').'...',
							),
						)
					),
				),
				'datatable' => array(
					'source' => $this->link_to('index', array('phpgw_return_as' => 'json')),
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							// 'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building'),
						),
						array(
							'key' => 'season_name',
							'label' => lang('Season'),
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
			
			$this->render_template('datatable', $data);
		}

		public function index_json()
		{
			$exports = $this->bo->read();
			array_walk($exports["results"], array($this, "_add_links"), $this->module.".uicompleted_reservation_export.show");
			foreach($exports["results"] as &$export) {
				$export = $this->bo->initialize_entity($export);
				
				$export['from_'] = pretty_timestamp($export['from_']);
				$export['to_'] = pretty_timestamp($export['to_']);
				$export['created_on'] = pretty_timestamp($export['created_on']);
				
				foreach($export['export_configurations'] as $type => $conf) {
					if (!is_string($type)) {
						throw new LogicException("Invalid export configuration type");
					}
					
					if (isset($conf['export_file_id']) && !empty($conf['export_file_id'])) {
						$export[$type] = array(
							'label' => (string)$conf['export_file_id'],
							'href' => $this->link_to('show', array('ui' => 'completed_reservation_export_file', 'id' => $conf['export_file_id']))
						);
					} else {
						$export[$type] = array('label' => "Not generated");
					}
				}
				
				$export['created_on'] = substr($export['created_on'], 0, 19);
			}
			
			$results = $this->yui_results($exports);
			return $results;
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