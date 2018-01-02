<?php
	phpgw::import_class('booking.uicommon');
	phpgw::import_class('booking.sopermission');

	phpgw::import_class('booking.uidocument_building');
	phpgw::import_class('booking.uipermission_building');
	phpgw::import_class('phpgwapi.datetime');

//	phpgw::import_class('phpgwapi.uicommon_jquery');

	class booking_uicompleted_reservation extends booking_uicommon
	{

		const SESSION_EXPORT_FILTER_KEY = 'export_filters';

		public $public_functions = array
			(
			'index' => true,
			'query' => true,
			'show' => true,
			'edit' => true,
			'export' => true,
			'toggle_show_all_completed_reservations' => true,
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
			self::set_active_menu('booking::invoice_center::completed_reservations');
			$this->url_prefix = 'booking.uicompleted_reservation';
			$this->restore_export_filters();
		}

		public function link_to( $action, $params = array() )
		{
			return $this->link($this->link_to_params($action, $params));
		}

		public function redirect_to( $action, $params = array() )
		{
			return $this->redirect($this->link_to_params($action, $params));
		}

		public function link_to_params( $action, $params = array() )
		{
			if (isset($params['ui']))
			{
				$ui = $params['ui'];
				unset($params['ui']);
			}
			else
			{
				$ui = 'completed_reservation';
			}

			$action = sprintf($this->module . '.ui%s.%s', $ui, $action);
			return array_merge(array('menuaction' => $action), $params);
		}

		protected function restore_export_filters()
		{
			if ($export_key = phpgw::get_var('export_key', 'string', 'REQUEST', null))
			{
				if (is_array($export_filters = $this->ui_session_get(self::SESSION_EXPORT_FILTER_KEY . '_' . $export_key)))
				{
					$this->export_filters = $export_filters;
				}
			}
		}

		protected function store_export_filters( $filters )
		{
			$export_key = md5(print_r($filters, true));
			$this->ui_session_set(self::SESSION_EXPORT_FILTER_KEY . '_' . $export_key, $filters);
			return $export_key;
		}

		public function export()
		{
			//TODO: also filter on exported value
			$filter_values = extract_values($_GET, array('season_id', 'season_name', 'building_id',
				'building_name', 'to'), array('prefix' => 'filter_', 'preserve_prefix' => true));
			$export_key = $this->store_export_filters($filter_values);

			$forward_values = extract_values($_GET, array('season_id', 'season_name',
				'building_id', 'building_name', 'to'), array('prefix' => 'filter_'));
			isset($forward_values['to']) AND $forward_values['to_'] = $forward_values['to'];
			$forward_values['export_key'] = $export_key;
			$forward_values['ui'] = 'completed_reservation_export';
			$this->redirect_to('add', $forward_values);
			return;
		}

		public function toggle_show_all_completed_reservations()
		{
			if (isset($_SESSION['show_all_completed_reservations']) && !empty($_SESSION['show_all_completed_reservations']))
			{
				$this->bo->unset_show_all_completed_reservations();
			}
			else
			{
				$this->bo->show_all_completed_reservations();
			}
			$this->redirect(array('menuaction' => $this->url_prefix . '.index'));
		}

		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			if (phpgw::get_var('export'))
			{
				return $this->export();
			}

			$GLOBALS['phpgw']->jqcal2->add_listener('filter_to');
			phpgwapi_jquery::load_widget('datepicker');

			self::add_javascript('booking', 'base', 'completed_reservation.js');

			$data = array(
				'datatable_name' => lang('booking') . ': ' . lang('Completed'),
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array('type' => 'autocomplete',
								'name' => 'building',
								'ui' => 'building',
								'text' => lang('Building') . ':',
								'onItemSelect' => 'updateBuildingFilter',
								'onClearSelection' => 'clearBuildingFilter'
							),
							array('type' => 'autocomplete',
								'name' => 'season',
								'ui' => 'season',
								'text' => lang('Season') . ':',
								'depends' => 'building',
								'requestGenerator' => 'requestWithBuildingFilter',
							),
							array(
								'type' => 'date-picker',
								'id' => 'to',
								'name' => 'to',
								'value' => '',
								'text' => lang('To') . ':',
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
								'type' => 'button',
								'name' => 'export',
								'value' => lang('Export') . '...',
								'onClick' => "export_completed_reservations();"
							),
						)
					),
				),
				'datatable' => array(
					'source' => $this->link_to('index', array('phpgw_return_as' => 'json')),
					'sorted_by' => array('key' => 0, 'dir' => 'desc'),//id
					'field' => array(
						array(
							'key' => 'id',
							'label' => lang('ID'),
							'formatter' => 'JqueryPortico.formatLink'
						),
						array(
							'key' => 'reservation_type',
							'label' => lang('Res. Type'),
							'formatter' => 'JqueryPortico.formatLinkGeneric',
						),
						array(
							'key' => 'event_id',
							'label' => lang('Event id'),
							'sortable' => false
						),
						array(
							'key' => 'description',
							'label' => lang('Description'),
						),
						array(
							'key' => 'building_name',
							'label' => lang('Building'),
						),
						array(
							'key' => 'organization_name',
							'label' => lang('Organization'),
						),
						array(
							'key' => 'contact_name',
							'label' => lang('Contact'),
							'sortable' => false
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
							'key' => 'from_',
							'label' => lang('From'),
						),
						array(
							'key' => 'to_',
							'label' => lang('To'),
						),
						array(
							'key' => 'cost',
							'label' => lang('Cost'),
						),
						array(
							'key' => 'exported',
							'label' => lang('Exported'),
							'formatter' => 'JqueryPortico.formatLinkGeneric',
						),
						array(
							'key' => 'export_file_id',
							'label' => lang('Export File'),
							'formatter' => 'JqueryPortico.formatLinkGeneric',
						),
						array(
							'key' => 'invoice_file_order_id',
							'label' => lang('Order id'),
						),
						array(
							'key' => 'link',
							'hidden' => true
						)
					)
				)
			);

			$data['filters'] = $this->export_filters;
//			self::render_template('datatable', $data);
			self::render_template_xsl('datatable_jquery', $data);
		}

		protected function add_current_customer_identifier_info( &$data )
		{
			$this->get_customer_identifier()->add_current_identifier_info($data);
		}

		public function query()
		{
			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$columns = phpgw::get_var('columns');

			$start = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$results = phpgw::get_var('length', 'int', 'REQUEST', null);
			$query = $search['value'];
			$sort = $columns[$order[0]['column']]['data'];
			$dir = $order[0]['dir'];
			/*
			 * Due to problem on order with offset - we need to set an additional parameter in some cases
			 * http://stackoverflow.com/questions/13580826/postgresql-repeating-rows-from-limit-offset
			 */

			switch ($sort)
			{
				case 'cost':
					$_sort = array('cost', 'id');
					break;
				case 'reservation_type':
					$_sort = array('reservation_type', 'id');
					break;
				case 'building_name':
					$_sort = array('building_name', 'id');
					break;
				case 'organization_name':
					$_sort = array('organization_name', 'id');
					break;
				case 'contact_name':
					$_sort = array('contact_name', 'id');
					break;
				case 'customer_type':
					$_sort = array('customer_type', 'id');
					break;
				case 'from_':
					$_sort = array('from_', 'id');
					break;
				case 'to_':
					$_sort = array('to_', 'id');
					break;
				case 'exported':
					$_sort = array('exported', 'id');
					break;
				case 'export_file_id':
					$_sort = array('export_file_id', 'id');
					break;
				default:
					$_sort = $sort;
					break;
			}

			$filters = array();
			foreach ($this->bo->so->get_field_defs() as $field => $params)
			{
				if (phpgw::get_var("filter_$field"))
				{
					$filters[$field] = phpgw::get_var("filter_$field");
				}
			}

			$filter_to = phpgw::get_var('to', 'string', 'REQUEST', null);
			$to_date = $filter_to ? $filter_to : phpgw::get_var('filter_to', 'string', 'REQUEST', null);

			if ($to_date)
			{
				$filter_to2 = date('Y-m-d', phpgwapi_datetime::date_to_timestamp($to_date));
				$filters['where'][] = "%%table%%" . sprintf(".to_ <= '%s 23:59:59'", $GLOBALS['phpgw']->db->db_addslashes($filter_to2));
			}

			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && // admin users should have access to all buildings
				!$this->bo->has_role(booking_sopermission::ROLE_MANAGER))
			{ // users with the booking role admin should have access to all buildings
				$accessable_buildings = $this->bo->accessable_buildings($GLOBALS['phpgw_info']['user']['id']);

				// if no buildings are searched for, show all accessable buildings
				if (!isset($filters['building_id']))
				{
					$filters['building_id'] = $accessable_buildings;
				}
				else
				{ // before displaying search result, check if the building search for is accessable
					if (!in_array($filters['building_id'], $accessable_buildings))
					{
						$filters['building_id'] = -1;
						unset($filters['building_name']);
					}
				}
			}

			if (!isset($_SESSION['showall']))
			{
				$filters['active'] = "1";
			}

			if (!isset($_SESSION['show_all_completed_reservations']))
			{
				$filters['exported'] = '';
			}

			$params = array(
				'start' => $start,
				'results' => $results,
				'query' => $query,
				'sort' => $_sort,
				'dir' => $dir,
				'filters' => $filters
			);

			$reservations = $this->bo->so->read($params);

			array_walk($reservations["results"], array($this, "_add_links"), $this->module . ".uicompleted_reservation.show");
			foreach ($reservations["results"] as &$reservation)
			{

				if (!empty($reservation['exported']))
				{
					$reservation['exported'] = array(
						'href' => $this->link_to('show', array('ui' => 'completed_reservation_export',
							'id' => $reservation['exported'])),
						'label' => (string)$reservation['exported'],
					);
				}
				else
				{
					$reservation['exported'] = array('label' => lang('No'));
				}

				$reservation['reservation_type'] = array(
					'href' => $this->link_to($reservation['reservation_type'] == 'event' ? 'edit' : 'show', array(
						'ui' => $reservation['reservation_type'], 'id' => $reservation['reservation_id'])),
					'label' => lang($reservation['reservation_type']),
				);

				if (isset($reservation['export_file_id']) && !empty($reservation['export_file_id']))
				{
					$reservation['export_file_id'] = array(
						'label' => (string)$reservation['export_file_id'],
						'href' => $this->link_to('show', array('ui' => 'completed_reservation_export_file',
							'id' => $reservation['export_file_id']))
					);
				}
				else
				{
					$reservation['export_file_id'] = array('label' => lang("Not Generated"));
				}

				if (empty($reservation['invoice_file_order_id']))
				{
					$reservation['invoice_file_order_id'] = lang("Not Generated");
				}

				$this->db = & $GLOBALS['phpgw']->db;

				if ($reservation['reservation_type']['label'] == 'Arrangement')
				{
					$sql = "select description,contact_name from bb_event where id=" . $reservation['reservation_id'];
					$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
					$this->db->next_record();
					$reservation['event_id'] = $reservation['reservation_id'];
					$reservation['description'] = $this->db->f('description', false);
					$reservation['contact_name'] = $this->db->f('contact_name', false);
				}
				elseif ($reservation['reservation_type']['label'] == 'Booking')
				{
					$sql = "select  application_id from bb_booking where id=" . $reservation['reservation_id'];
					$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
					$this->db->next_record();
					if (!$this->db->f('application_id', false))
					{
						$reservation['contact_name'] = '';
					}
					else
					{
						$sql = "select  contact_name from bb_application where id=" . $this->db->f('application_id', false);
						$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
						$this->db->next_record();
						$reservation['contact_name'] = $this->db->f('contact_name', false);
					}
					$reservation['event_id'] = '';
					$reservation['description'] = '';
				}
				else
				{
					$sql = "select  application_id from bb_allocation where id=" . $reservation['reservation_id'];
					$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
					$this->db->next_record();
					if (!$this->db->f('application_id', false))
					{
						$reservation['contact_name'] = '';
					}
					else
					{
						$sql = "select  contact_name from bb_application where id=" . $this->db->f('application_id', false);
						$this->db->limit_query($sql, 0, __LINE__, __FILE__, 1);
						$this->db->next_record();
						$reservation['contact_name'] = $this->db->f('contact_name', false);
					}
					$reservation['event_id'] = '';
					$reservation['description'] = '';
				}
				$reservation['from_'] = substr($reservation['from_'], 0, -3);
				$reservation['to_'] = substr($reservation['to_'], 0, -3);
				$reservation['from_'] = pretty_timestamp($reservation['from_']);
				$reservation['to_'] = pretty_timestamp($reservation['to_']);
				$reservation['customer_type'] = lang($reservation['customer_type']);

				$this->add_current_customer_identifier_info($reservation);

				$reservation['customer_identifier'] = isset($reservation['customer_identifier_label']) ?
					$reservation['customer_identifier_value'] : lang('None');
			}

			$results = $this->jquery_results($reservations);

			return $results;
		}

		protected function add_default_display_data( &$reservation )
		{
			$reservation['reservations_link'] = $this->link_to('index');
			$reservation['edit_link'] = $this->link_to('edit', array('id' => $reservation['id']));

			$reservation['customer_types'] = array_combine($this->bo->get_customer_types(), $this->bo->get_customer_types());

			if ($reservation['season_id'])
			{
				$reservation['season_link'] = $this->link_to('show', array('ui' => 'season',
					'id' => $reservation['season_id']));
			}
			else
			{
				unset($reservation['season_id']);
				unset($reservation['season_name']);
			}

			if ($reservation['organization_id'])
			{
				$reservation['organization_link'] = $this->link_to('show', array('ui' => 'organization',
					'id' => $reservation['organization_id']));
			}
			else
			{
				unset($reservation['organization_id']);
				unset($reservation['organization_name']);
			}

			if (!empty($reservation['exported']))
			{
				$reservation['exported_link'] = $this->link_to('show', array('ui' => 'completed_reservation_export',
					'id' => $reservation['exported']));
			}
			else
			{
				$reservation['exported'] = lang('No');
			}

			if (!empty($reservation['export_file_id']))
			{
				$reservation['export_file_id'] = array(
					'label' => (string)$reservation['export_file_id'],
					'href' => $this->link_to('show', array('ui' => 'completed_reservation_export_file',
						'id' => $reservation['export_file_id']))
				);
			}
			else
			{
				$reservation['export_file_id'] = array('label' => lang("Not Generated"));
			}

			if (empty($reservation['invoice_file_order_id']))
			{
				$reservation['invoice_file_order_id'] = lang("Not Generated");
			}

			$reservation['reservation_link'] = $this->link_to($reservation['reservation_type'] == 'event' ? 'edit' : 'show', array(
				'ui' => $reservation['reservation_type'], 'id' => $reservation['reservation_id']));

			$reservation['cancel_link'] = $this->link_to('show', array('id' => $reservation['id']));
			//TODO: Add application_link where necessary
			//$reservation['application_link'] = ?;
		}

		public function show()
		{
			$reservation = $this->bo->read_single(phpgw::get_var('id', 'int'));
			$this->add_default_display_data($reservation);
			$this->install_customer_identifier_ui($reservation);
			$show_edit_button = false;
			$building_role = $this->bo->accessable_buildings($GLOBALS['phpgw_info']['user']['id']);

			if (isset($GLOBALS['phpgw_info']['user']['apps']['admin']) || in_array($reservation['building_id'], $building_role))
			{
				$show_edit_button = true;
			}

			$reservation['from_'] = pretty_timestamp($reservation['from_']);
			$reservation['to_'] = pretty_timestamp($reservation['to_']);
			$reservation['cancel_link'] = self::link(array('menuaction' => 'booking.uicompleted_reservation.index'));

			$tabs = array();
			$tabs['completed_reservation'] = array('label' => lang('Reservation show'), 'link' => '#completed_reservation');
			$active_tab = 'completed_reservation';

			$reservation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			self::render_template('completed_reservation', array('reservation' => $reservation,
				'show_edit_button' => $show_edit_button));
		}

		protected function get_customer_identifier()
		{
			return $this->customer_id;
		}

		protected function extract_customer_identifier( &$data )
		{
			$this->get_customer_identifier()->extract_form_data($data);
		}

		protected function validate_customer_identifier( &$data )
		{
			return $this->get_customer_identifier()->validate($data);
		}

		protected function install_customer_identifier_ui( &$entity )
		{
			$this->get_customer_identifier()->install($this, $entity);
		}

		protected function validate( &$entity )
		{
			$errors = array_merge($this->validate_customer_identifier($entity), $this->bo->validate($entity));
			return $errors;
		}

		protected function extract_form_data( $defaults = array() )
		{
			$entity = array_merge($defaults, extract_values($_POST, $this->fields));
			$this->extract_customer_identifier($entity);
			return $entity;
		}

		protected function extract_and_validate( $defaults = array() )
		{
			$entity = $this->extract_form_data($defaults);
			$errors = $this->validate($entity);
			return array($entity, $errors);
		}

		public function edit()
		{
			//TODO: Display hint to user about primary type of customer identifier

			$building_role = $this->bo->accessable_buildings($GLOBALS['phpgw_info']['user']['id']);
			$reservation = $this->bo->read_single(phpgw::get_var('id', 'int'));

			if (!isset($GLOBALS['phpgw_info']['user']['apps']['admin']) && !in_array($reservation['building_id'], $building_role))
			{
				$this->redirect_to('show', array('id' => phpgw::get_var('id', 'int')));
			}

			if (((int)$reservation['exported']) !== 0)
			{
				//Cannot edit already exported reservation
				$this->redirect_to('show', array('id' => $reservation['id']));
			}

			$errors = array();
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				list($reservation, $errors) = $this->extract_and_validate($reservation);

				if (!$errors)
				{
					try
					{
						$receipt = $this->bo->update($reservation);
						$this->redirect_to('show', array('id' => $reservation['id']));
					}
					catch (booking_unauthorized_exception $e)
					{
						$errors['global'] = lang('Could not update object due to insufficient permissions');
					}
				}
			}

			$tabs = array();
			$tabs['completed_reservation_edit'] = array('label' => lang('Reservation edit'),
				'link' => '#completed_reservation_edit');
			$active_tab = 'completed_reservation_edit';
			$reservation['tabs'] = phpgwapi_jquery::tabview_generate($tabs, $active_tab);
			$reservation['validator'] = phpgwapi_jquery::formvalidator_generate(array(
					'location', 'date', 'security', 'file'));

			$this->add_default_display_data($reservation);
			$this->flash_form_errors($errors);
			$this->install_customer_identifier_ui($reservation);
			self::render_template('completed_reservation_edit', array('reservation' => $reservation));
		}
	}