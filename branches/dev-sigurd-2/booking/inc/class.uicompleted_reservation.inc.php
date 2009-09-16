<?php
phpgw::import_class('booking.uicommon');

	class booking_uicompleted_reservation extends booking_uicommon
	{
		public $public_functions = array
		(
			'index'			=>	true,
			'show'			=>	true,
			'edit'			=>	true,
			'export'       => true,
			'toggle_show_all_completed_reservations'	=>	true,
		);
		
		protected $fields = array('cost', 'customer_organization_number', 'customer_ssn', 'description');

		protected $module = 'booking';
		
		public function __construct()
		{
			parent::__construct();
			$this->bo = CreateObject('booking.bocompleted_reservation');
			self::set_active_menu('booking::completed_reservations');
			$this->url_prefix = 'booking.uicompleted_reservation';
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
		
		public function export() {
			//TODO: also filter on exported value
			$forward_values = extract_values($_GET, array('season_id', 'season_name', 'building_id', 'building_name', 'to'), array('prefix' => 'filter_'));
			isset($forward_values['to']) AND $forward_values['to_'] = $forward_values['to'];
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
							'label' => lang('Cust. #'),
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
			self::render_template('datatable', $data);
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
				$reservation['customer_identifier'] = $this->bo->get_active_customer_identifier($reservation);
				$string_customer_identifier = (is_null(current($reservation['customer_identifier'])) ? 'N/A' : current($reservation['customer_identifier']));
				$reservation['customer_identifier'] = $string_customer_identifier;
			}
			
			$results = $this->yui_results($reservations);
			
			return $results;
		}
		
		protected function add_default_display_data(&$reservation)
		{
			$reservation['reservations_link'] = $this->link_to('index');
			$reservation['edit_link'] = $this->link_to('edit', array('id' => $reservation['id']));
			
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
			
			if (isset($reservation['customer_identifier_type']) && !empty($reservation['customer_identifier_type'])) {
				$reservation['customer_identifier_type'] = self::humanize($reservation['customer_identifier_type']);
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
			self::render_template('completed_reservation', array('reservation' => $reservation));
		}
		
		public function edit() {
			//TODO: Add editing of reservation type
			//TODO: Display hint to user about primary type of customer identifier
			
			$reservation = $this->bo->read_single(phpgw::get_var('id', 'GET'));
			
			$errors = array();
			if($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				$reservation = array_merge($reservation, extract_values($_POST, $this->fields));
				$errors = $this->bo->validate($reservation);
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
			self::render_template('completed_reservation_edit', array('reservation' => $reservation));
		}
	}