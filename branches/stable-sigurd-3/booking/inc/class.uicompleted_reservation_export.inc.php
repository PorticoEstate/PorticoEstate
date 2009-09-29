<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation_export extends booking_uicommon
	{	
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'add'				=> true,
			'download'  	=> true,
		);

		protected 
			$module = 'booking',
			$fields = array('season_id', 'season_name', 'building_id', 'building_name', 'from_', 'to_', 'export_files');
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation_export');
			self::set_active_menu('booking::completed_reservations::exports');
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
		
		public function download() {
			$export = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			
			if (!is_array($export)) {
				$this->redirect_to('index');
			}
			
			$file = $this->bo->get_export_file($export, phpgw::get_var('type', 'GET'));
			
			$this->send_file($file->get_system_identifier(), array('filename' => $file->get_identifier()));
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array(
								'type' => 'text', 
								'name' => 'query'
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search')
							),
						),
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
							'key' => 'internal',
							'label' => lang('Internal'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
							'sortable' => false,
						),
						array(
							'key' => 'external',
							'label' => lang('External'),
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
				$export['from_'] = substr($export['from_'], 0, -3);
				$export['to_'] = substr($export['to_'], 0, -3);
				$export_actions = array();
				$export['external'] = array(
					'label' => lang('Download'), 
					'href' => $this->link_to('download', array('id' => $export['id'], 'type' => 'external'))
				);
				$export['internal'] = array(
					'label' => lang('Download'), 
					'href' => $this->link_to('download', array('id' => $export['id'], 'type' => 'internal'))
				);
				$export['created_on'] = substr($export['created_on'], 0, 19);
			}
			
			$results = $this->yui_results($exports);
			return $results;
		}
		
		protected function add_default_display_data(&$export)
		{
			$export['exports_link'] = $this->link_to('index');
			// $export['edit_link'] = $this->link_to('edit', array('id' => $export['id']));
			
			if ($export['season_id']) {
				$export['season_link'] = $this->link_to('show', array('ui' => 'season', 'id' => $export['season_id']));
			} else {
				unset($export['season_id']);
				unset($export['season_name']);
			}
			
			if ($export['building_id']) {
				$export['building_link'] = $this->link_to('show', array('ui' => 'building', 'id' => $export['building_id']));
			} else {
				unset($export['building_id']);
				unset($export['building_name']);
			}
			
			$export['cancel_link'] = $this->link_to('show', array('id' => $export['id']));
		}
		
		public function show()
		{
			$export = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$this->add_default_display_data($export);
			$this->render_template('completed_reservation_export', array('reservation' => $export));
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
				
				//Fill in a dummy value (because of validation), will be automatically filled in by so->add.
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