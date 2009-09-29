<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation extends booking_uicommon
	{
		const SESSION_EXPORT_FILTER_KEY = 'export_filters';
		
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'export'       => true,
			'toggle_show_all_completed_reservations'	=>	true,
		);

		protected
			$module = 'booking',
			$fields = array('cost', 'customer_organization_number', 'customer_ssn', 
								 'customer_type', 'description', 'article_description'),
			$customer_id,
			$export_filters = array();
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation');
			$this->customer_id = CreateObject('booking.customer_identifier');
			self::set_active_menu('booking::completed_reservations');
			$this->url_prefix = 'booking.uicompleted_reservation';
			$this->restore_export_filters();
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
				$ui = 'completed_reservation';
			}
			
			$action = sprintf($this->module.'.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}
		
		protected function restore_export_filters() {
			if ($export_key = phpgw::get_var('export_key', 'string', 'REQUEST', null)) {
				if (is_array($export_filters = $this->ui_session_get(self::SESSION_EXPORT_FILTER_KEY.'_'.$export_key))) {
					$this->export_filters = $export_filters;
				}
			}
		}
		
		protected function store_export_filters($filters) {
			$export_key = md5(print_r($filters, true));
			$this->ui_session_set(self::SESSION_EXPORT_FILTER_KEY.'_'.$export_key, $filters);
			return $export_key;
		}
		
		public function export() {
			//TODO: also filter on exported value
			$filter_values = extract_values($_GET, array('season_id', 'season_name', 'building_id', 'building_name', 'to'), array('prefix' => 'filter_', 'preserve_prefix' => true));
			$export_key = $this->store_export_filters($filter_values);
			
			$forward_values = extract_values($_GET, array('season_id', 'season_name', 'building_id', 'building_name', 'to'), array('prefix' => 'filter_'));
			isset($forward_values['to']) AND $forward_values['to_'] = $forward_values['to'];
			$forward_values['export_key'] = $export_key;
			$forward_values['ui'] = 'completed_reservation_export';
			$this->redirect_to('add', $forward_values);
			return;
		}
		
		public function toggle_show_all_completed_reservations()
		{
			if(isset($_SESSION['show_all_completed_reservations']) && !empty($_SESSION['show_all_completed_reservations']))
			{
				$this->bo->unset_show_all_completed_reservations();
			}else{
				$this->bo->show_all_completed_reservations();
			}
			$this->redirect(array('menuaction' => $this->url_prefix.'.index'));
		}
		
		public function index()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json') {
				return $this->index_json();
			}
			
			if (phpgw::get_var('export')) {
				return $this->export();
			}
			
			self::add_javascript('booking', 'booking', 'completed_reservation.js');
			self::add_javascript('booking', 'booking', 'datatable.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			$data = array(
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'autocomplete', 
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building').':',
								'onItemSelect' => 'updateBuildingFilter',
								'onClearSelection' => 'clearBuildingFilter'
							),
							array('type' => 'autocomplete', 
								'name' => 'season',
								'ui' => 'season',
								'text' => lang('Season').':',
								'requestGenerator' => 'requestWithBuildingFilter',
							),
							array('type' => 'date-picker', 
								'name' => 'to',
								'text' => lang('To').':',
							),
							array(
								'type' => 'submit',
								'name' => 'search',
								'value' => lang('Search'),
							),
							array(
								'type' => 'link',
								'value' => $_SESSION['show_all_completed_reservations'] ? lang('Show only unexported') : lang('Show all'),
								'href' => $this->link_to('toggle_show_all_completed_reservations'),
							),
						)
					),
					'list_actions' => array(
						'item' => array(
							array(
								'type' => 'submit',
								'name' => 'export',
								'value' => lang('Export').'...',
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
							'formatter' => 'YAHOO.booking.formatLink'
						),
						array(
							'key' => 'reservation_type',
							'label' => lang('Res. Type'),
							'formatter' => 'YAHOO.booking.formatGenericLink()',
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building'),
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
							'key' => 'customer_type',
							'label' => lang('Cust. Type'),
						),
						array(
							'key' => 'customer_identifier',
							'label' => lang('Customer ID'),
							'sortable' => false,
						),
						array(
							'key' => 'cost',
							'label' => lang('Cost'),
						),
						array(
							'key' => 'exported',
							'label' => lang('Exported'),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);
			
			$data['filters'] = $this->export_filters;
			self::render_template('datatable', $data);
		}
		
		protected function add_current_customer_identifier_info(&$data) {
			$this->get_customer_identifier()->add_current_identifier_info($data);
		}
		
		public function index_json()
		{
			$reservations = $this->bo->read();
			array_walk($reservations["results"], array($this, "_add_links"), $this->module.".uicompleted_reservation.show");
			foreach($reservations["results"] as &$reservation) {
				$reservation['exported'] = $reservation['exported'] ? 'Yes' : 'No';
				$reservation['reservation_type'] = array(
					'href' => $this->link_to('show', array('ui' => $reservation['reservation_type'], 'id' => $reservation['reservation_id'])),
					'label' => lang($reservation['reservation_type']),
				);
				
				$reservation['from_'] = substr($reservation['from_'], 0, -3);
				$reservation['to_'] = substr($reservation['to_'], 0, -3);
				$reservation['customer_type'] = lang($reservation['customer_type']);

				$this->add_current_customer_identifier_info($reservation);
				
				$reservation['customer_identifier'] = isset($reservation['customer_identifier_label']) ? 
					$reservation['customer_identifier_value'] : lang('None');
			}
			
			$results = $this->yui_results($reservations);
			
			return $results;
		}
		
		protected function add_default_display_data(&$reservation)
		{
			$reservation['reservations_link'] = $this->link_to('index');
			$reservation['edit_link'] = $this->link_to('edit', array('id' => $reservation['id']));
			
			$reservation['customer_types'] = array_combine($this->bo->get_customer_types(), $this->bo->get_customer_types());
			
			if ($reservation['season_id']) {
				$reservation['season_link'] = $this->link_to('show', array('ui' => 'season', 'id' => $reservation['season_id']));
			} else {
				unset($reservation['season_id']);
				unset($reservation['season_name']);
			}
			
			if ($reservation['organization_id']) {
				$reservation['organization_link'] = $this->link_to('show', array('ui' => 'organization', 'id' => $reservation['organization_id']));
			} else {
				unset($reservation['organization_id']);
				unset($reservation['organization_name']);
			}
			
			$reservation['reservation_link'] = $this->link_to('show', array(
				'ui' => $reservation['reservation_type'], 'id' => $reservation['reservation_id']));
			
			$reservation['cancel_link'] = $this->link_to('show', array('id' => $reservation['id']));
			//TODO: Add application_link where necessary
			//$reservation['application_link'] = ?;
		}
		
		public function show()
		{
			$reservation = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			$this->add_default_display_data($reservation);
			$this->install_customer_identifier_ui($reservation);
			self::render_template('completed_reservation', array('reservation' => $reservation));
		}
		
		protected function get_customer_identifier() {
			return $this->customer_id;
		}

		protected function extract_customer_identifier(&$data) {
			$this->get_customer_identifier()->extract_form_data($data);
		}

		protected function validate_customer_identifier(&$data) {
			return $this->get_customer_identifier()->validate($data);
		}

		protected function install_customer_identifier_ui(&$entity) {
			$this->get_customer_identifier()->install($this, $entity);
		}

		protected function validate(&$entity) {
			$errors = array_merge($this->validate_customer_identifier($entity), $this->bo->validate($entity));
			return $errors;
		}

		protected function extract_form_data($defaults = array()) {
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$this->extract_customer_identifier($entity);
			return $entity;
		}

		protected function extract_and_validate($defaults = array()) {
			$entity = $this->extract_form_data($defaults);
			$errors = $this->validate($entity);
			return array($entity, $errors);
		}
		
		public function edit() {
			//TODO: Display hint to user about primary type of customer identifier
			
			$reservation = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			
			if (((int)$reservation['exported']) !== 0) {
				//Cannot edit already exported reservation
				$this->redirect_to('show', array('id' => $reservation['id']));
			}
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($reservation, $errors) = $this->extract_and_validate($reservation);

				if(!$errors)
				{
					try {
						$receipt = $this->bo->update($reservation);	
						$this->redirect_to('show', array('id' => $reservation['id']));
					} catch (booking_unauthorized_exception $e) {
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}
			
			$this->add_default_display_data($reservation);
			$this->flash_form_errors($errors);
			$this->install_customer_identifier_ui($reservation);
			self::render_template('completed_reservation_edit', array('reservation' => $reservation));
		}
	}