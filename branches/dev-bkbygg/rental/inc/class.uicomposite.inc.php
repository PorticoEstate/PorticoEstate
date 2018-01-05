<?php
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.soapplication');
	phpgw::import_class('rental.sounit');
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'application', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');

	class rental_uicomposite extends rental_uicommon
	{

		var $config;
		public $public_functions = array
			(
			'index' => true,
			'view' => true,
			'edit' => true,
			'save' => true,
			'columns' => true,
			'add' => true,
			'add_unit' => true,
			'remove_unit' => true,
			'query' => true,
			'download' => true,
			'schedule' => true,
			'get_schedule' => true,
			'handle_multi_upload_file' => true,
			'build_multi_upload_file' => true,
			'view_file' => true,
			'get_files' => true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::composites');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('rc');

			$this->config = CreateObject('phpgwapi.config', 'rental');
			$this->config->read();
			$this->acl = & $GLOBALS['phpgw']->acl;
			$this->acl_location = '.contract'; //for now
			$this->acl_read = $this->acl->check($this->acl_location, PHPGW_ACL_READ, 'rental');
			$this->acl_add = $this->acl->check($this->acl_location, PHPGW_ACL_ADD, 'rental');
			$this->acl_edit = $this->acl->check($this->acl_location, PHPGW_ACL_EDIT, 'rental');
			$this->acl_delete = $this->acl->check($this->acl_location, PHPGW_ACL_DELETE, 'rental');
		}

		private function get_status_options( $selected = 0 )
		{
			$status_options = array();
			$status_list = rental_composite::get_status_list();

			foreach ($status_list as $_key => $_value)
			{
				$status_options[] = array(
					'id' => $_key,
					'name' => $_value,
					'selected' => $_key == $selected ? 1 : 0
				);
			}
			return $status_options;
		}

		public function get_filters()
		{
			$filters = array();

			$search_option = array
				(
				array('id' => 'all', 'name' => lang('all')),
				array('id' => 'name', 'name' => lang('name')),
				array('id' => 'address', 'name' => lang('address')),
				array('id' => 'location_code', 'name' => lang('object_number'))
			);
			$search_type = phpgw::get_var('search_type');
			foreach ($search_option as &$entry)
			{
				$entry['selected'] = $entry['id'] == $search_type ? 1 : 0;
			}
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'search_option',
				'text' => lang('search option'),
				'list' => $search_option
			);

			if (isset($this->config->config_data['contract_furnished_status']) && $this->config->config_data['contract_furnished_status'])
			{
				$furnish_types_arr = rental_composite::get_furnish_types();
				$furnish_types = array();
				array_unshift($furnish_types, array('id' => '4', 'name' => lang('Alle')));
				foreach ($furnish_types_arr as $id => $title)
				{
					$furnish_types[] = array('id' => $id, 'name' => $title);
				}
				$filters[] = array
					(
					'type' => 'filter',
					'name' => 'furnished_status',
					'text' => lang('furnish_type'),
					'list' => $furnish_types
				);
			}

			$status_options = $this->get_status_options();

			array_unshift($status_options, array('id'=> 'all', 'name' => lang('all')));


			$filters[] = array
					(
					'type' => 'filter',
					'name' => 'status_id',
					'text' => lang('status'),
					'list' => $status_options
				);

			$districts_arr = execMethod('property.sogeneric.get_list', array('type' => 'district'));
			$default_district = (isset($GLOBALS['phpgw_info']['user']['preferences']['property']['default_district']) ? $GLOBALS['phpgw_info']['user']['preferences']['property']['default_district'] : '');
			$districts = array();
			array_unshift($districts, array('id' => '', 'name' => lang('select')));
			foreach ($districts_arr as $district)
			{
				$districts[] = array(
					'id' => $district['id'],
					'name' => $district['name'],
					'selected'	=> $default_district == $district['id'] ? 1 : 0
					);
			}
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'district_id',
				'text' => lang('district'),
				'list' => $districts
			);
/*
			$active_option = array
				(
				array('id' => 'both', 'name' => lang('all')),
				array('id' => 'active', 'name' => lang('in_operation'),	'selected' => 1	),
				array('id' => 'non_active', 'name' => lang('out_of_operation')),
			);
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'is_active',
				'text' => lang('availability'),
				'list' => $active_option
			);
*/
			$has_contract_option = array
				(
				array('id' => 'both', 'name' => lang('all')),
				array('id' => 'has_contract', 'name' => lang('composite_has_contract')),
				array('id' => 'has_no_contract', 'name' => lang('composite_has_no_contract')),
			);
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'has_contract',
				'text' => lang('contracts'),
				'list' => $has_contract_option
			);

			return $filters;
		}

		public function query()
		{
			$length = phpgw::get_var('length', 'int');

			$user_rows_per_page = $length > 0 ? $length : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];

			$search = phpgw::get_var('search');
			$query = phpgw::get_var('query');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int', 'REQUEST', 1);
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = $length == -1 ? 0 : $user_rows_per_page;
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'id';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			$search_for = (is_array($search)) ? $search['value'] : $search;
			$search_for = $search_for ? $search_for : '';
			$search_for = $query ? $query : $search_for; //from autocomplete

			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');

			$export = phpgw::get_var('export', 'bool');
			$editable = phpgw::get_var('editable', 'bool');

			// Create an empty result set
			$result_objects = array();
			$object_count = 0;
			$district_id = phpgw::get_var('district_id', 'int');
			$status_id = phpgw::get_var('status_id', 'int');

			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			
			$application_id = (phpgw::get_var('application_id')) ? phpgw::get_var('application_id') : 0;

			if ($export)
			{
				$num_of_objects = 0;
			}

			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');

			switch ($query_type)
			{
				case 'available_composites': // ... get all vacant composites
					$filters = array('is_vacant' => 'vacant');
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'included_composites': // ... get all composites in contract
					$filters = array('contract_id' => $contract_id);
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'not_included_composites': // ... get all vacant and active composites not in contract
					$filters = array('is_active' => phpgw::get_var('is_active'),
						'district_id' => $status_id,
						'is_vacant' => phpgw::get_var('occupancy'),//is_vacant seems to be unused
						'not_in_contract' => phpgw::get_var('contract_id'),
						'has_contract' => phpgw::get_var('has_contract'),
						'furnished_status' => phpgw::get_var('furnished_status'));
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'all_composites': // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('rental', 'composite_query', $search_for);
					phpgwapi_cache::session_set('rental', 'composite_search_type', $search_type);
					phpgwapi_cache::session_set('rental', 'composite_status', phpgw::get_var('is_active'));
					phpgwapi_cache::session_set('rental', 'composite_status_id', phpgw::get_var('status_id'));
					phpgwapi_cache::session_set('rental', 'composite_status_contract', phpgw::get_var('has_contract'));
					phpgwapi_cache::session_set('rental', 'composite_furnished_status', phpgw::get_var('furnished_status'));
					$filters = array(
						'furnished_status' => phpgw::get_var('furnished_status'),
						'is_active' => phpgw::get_var('is_active'),
						'is_vacant' => phpgw::get_var('occupancy'),
						'has_contract' => phpgw::get_var('has_contract'),
						'availability_date_from' => phpgw::get_var('availability_date_from'),
						'availability_date_to' => phpgw::get_var('availability_date_to'),
						'district_id' => $district_id,
						'status_id' => $status_id,
						'composite_type_id'	=> phpgw::get_var('composite_type_id', 'int'),
						);
					if ($application_id > 0)
					{
						$filters['application_id'] = $application_id;
					}
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'included_areas': // Returns areas/units added to a specified composite
					$filters = array('included_areas' => phpgw::get_var('composite_id')); // Composite id
					$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'available_areas': // Returns areas/units available for a specified composite
					$filters = array('available_areas' => phpgw::get_var('id')); // Composite id
					$result_objects = rental_sounit::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_sounit::get_instance()->get_count($search_for, $search_type, $filters);
					break;
			}

			$price_item = new rental_price_item();
			$price_types = $price_item->get_price_types();
			foreach ($price_types as $price_type_id => &$price_type_title)
			{
				$price_type_title = lang($price_type_title);

			}

			//Create an empty row set

			$status_list = rental_composite::get_status_list();
			$rows = array();
			foreach ($result_objects as $result)
			{
				if (isset($result))
				{
//					if (!$result->is_active())
//					{
//						$result->set_status('Ikke i drift');
//					}

					$result->set_status($status_list[$result->get_status_id()]);
					$row = $result->serialize();
					$row['price_type'] = $price_types[$row['price_type_id']];
					$rows[] = $row;
				}
			}

			// ... add result data
			//$result_data = array('results' => $rows, 'total_records' => $object_count);
			//$editable = phpgw::get_var('editable') == 'true' ? true : false;

			/* $contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();

			  $config	= CreateObject('phpgwapi.config','rental');
			  $config->read();
			  $valid_contract_types = array();
			  if(isset($config->config_data['contract_types']) && is_array($config->config_data['contract_types']))
			  {
			  foreach ($config->config_data['contract_types'] as $_key => $_value)
			  {
			  if($_value)
			  {
			  $valid_contract_types[] = $_value;
			  }
			  }
			  }

			  $create_types = array();
			  foreach($contract_types as $id => $label)
			  {
			  if($valid_contract_types && !in_array($id,$valid_contract_types))
			  {
			  continue;
			  }

			  $names = $this->locations->get_name($id);
			  if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
			  {
			  if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
			  {
			  // adding allowed contract_types for context menu creation
			  $create_types[] = array($id, $label);
			  }
			  }
			  } */

			/* if(!$export){
			  //Add action column to each row in result table
			  array_walk(
			  $rows,
			  array($this, 'add_actions'),
			  array(													// Parameters (non-object pointers)
			  $contract_id,										// [1] The contract id
			  $query_type,										// [2] The type of query
			  $editable,											// [3] Editable flag
			  $create_types										// [4] Types of contract to create
			  )
			  );
			  } */
			if ($export)
			{
				/*
				 * reverse of nl2br()
				 */
				foreach ($rows as &$row)
				{
					foreach ($row as $key => &$value)
					{
						$value = preg_replace('#<br\s*?/?>#i', "\n", $value);
					}
				}
				return $rows;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $object_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions( &$value, $key, $params )
		{
			//Defining new columns
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			// Get parameters
			$contract_id = $params[0];
			$type = $params[1];
			$editable = $params[2];
			$create_types = $params[3];

			// Depending on the type of query: set an ajax flag and define the action and label for each row
			switch ($type)
			{
				case 'included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view',
							'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if ($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_composite',
								'composite_id' => $value['id'], 'contract_id' => $contract_id)));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'not_included_composites': //does not show unless editable
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view',
							'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_composite',
							'composite_id' => $value['id'], 'contract_id' => $contract_id)));
					$value['labels'][] = lang('add');
					break;
				case 'included_areas':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view',
							'location_code' => $value['location_code'])));
					$value['labels'][] = lang('show');
					if ($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit',
								'id' => $contract_id, 'location_id' => $value['location_id'])));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'available_areas':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view',
							'location_code' => $value['location_code'])));
					$value['labels'][] = lang('show');
					if ($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit',
								'id' => $contract_id, 'location_id' => $value['location_id'], 'loc1' => $value['loc1'])));
						$value['labels'][] = lang('add');
					}
					break;
				case 'contracts':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view',
							'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if ($editable == true)
					{
						$value['ajax'][] = false;
						$value['actions']['edit_contract'] = html_entity_decode(self::link(array(
								'menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
					break;
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view',
							'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit',
							'id' => $value['id'])));
					$value['labels'][] = lang('edit');
					foreach ($create_types as $create_type)
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_from_composite',
								'id' => $value['id'], 'responsibility_id' => $create_type[0])));
						$value['labels'][] = lang('create_contract_' . $create_type[1]);
					}
			}
		}

		/**
		 * Shows a list of composites
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$editable = phpgw::get_var('editable', 'bool');
			$user_is = $this->type_of_user;

			$appname = lang('rc');
			$type = 'all_composites';

			$function_msg = lang('list %1', $appname);

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'date-picker',
								'id' => 'availability_date_from',
								'name' => 'availability_date_from',
								'value' => '',
								'text' => lang('from')
							),
							array
								(
								'type' => 'date-picker',
								'id' => 'availability_date_to',
								'name' => 'availability_date_to',
								'value' => '',
								'text' => lang('to')
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uicomposite.index',
						'editable' => ($editable) ? 1 : 0,
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'rental.uicomposite.download',
						'type' => $type,
						'export' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'rental.uicomposite.add')),
					'editor_action' => '',
					'field' => array(),
					'query' => phpgw::get_var('search_for')
				)
			);

			$filters = $this->get_filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			$uicols = rental_socomposite::get_instance()->get_uicols();

			for ($k = 0; $k < count($uicols['name']); $k++)
			{
				$params = array(
					'key' => $uicols['name'][$k],
					'label' => $uicols['descr'][$k],
					'sortable' => ($uicols['sortable'][$k]) ? true : false,
					'hidden' => ($uicols['input_type'][$k] == 'hidden') ? true : false
				);

				array_push($data['datatable']['field'], $params);
			}
			if (!empty($this->config->config_data['contract_future_info']))
			{
				array_push($data['datatable']['field'], array("key" => "contracts", "label" => lang('contract_future_info'),
					"sortable" => false, "hidden" => false));
			}
			if (!empty($this->config->config_data['contract_furnished_status']))
			{
				array_push($data['datatable']['field'], array("key" => "furnished_status",
					"label" => lang('furnish_type'),
					"sortable" => false, "hidden" => false));
			}
			//array_push($data['datatable']['field'], array("key"=>"actions", "label"=>lang('actions'), "sortable"=>false, "hidden"=>false, "className"=>'dt-center all'));

			$GLOBALS['phpgw']->jqcal->add_listener('filter_availability_date_from');
			$GLOBALS['phpgw']->jqcal->add_listener('filter_availability_date_to');
			phpgwapi_jquery::load_widget('datepicker');

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.view'
				)),
				'parameters' => json_encode($parameters)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.edit'
				)),
				'parameters' => json_encode($parameters)
			);

			$contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();
			/* $config	= CreateObject('phpgwapi.config','rental');
			  $config->read(); */
			$valid_contract_types = array();
			if (isset($this->config->config_data['contract_types']) && is_array($this->config->config_data['contract_types']))
			{
				foreach ($this->config->config_data['contract_types'] as $_key => $_value)
				{
					if ($_value)
					{
						$valid_contract_types[] = $_value;
					}
				}
			}

			$create_types = array();
			foreach ($contract_types as $id => $label)
			{
				if ($valid_contract_types && !in_array($id, $valid_contract_types))
				{
					continue;
				}

				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						// adding allowed contract_types for context menu creation
						$create_types[] = array($id, $label);
					}
				}
			}

			foreach ($create_types as $create_type)
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => $create_type[1],
					'text' => lang('create_contract_' . $create_type[1]),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uicontract.add_from_composite',
						'responsibility_id' => $create_type[0]
					)),
					'parameters' => json_encode($parameters)
				);
			}

			//self::add_javascript('rental', 'rental', 'party.sync.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit'));
		}

		/**
		 * Public method. Called when a user wants to view information about a composite.
		 * @param HTTP::id	the composite ID
		 */
		public function view()
		{
			$composite_id = (int)phpgw::get_var('id');

			if (isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('invalid_request'));
			}

			if (isset($composite) && $composite->has_permission(PHPGW_ACL_READ))
			{
				$this->edit(array(), $mode = 'view');
			}
			else
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_view_composite'));
			}
		}

		/**
		 * Public method. Called when user wants to edit a composite.
		 * @param HTTP::id	the composite ID
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang($mode);

			$composite_id = (int)phpgw::get_var('id');

			if ($mode == 'edit')
			{
				if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit'));
				}
			}

			if (isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$composite = new rental_composite();
			}

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			if ($composite_id)
			{
				$tabs['units'] = array('label' => lang('Units'), 'link' => '#units');
				$tabs['contracts'] = array('label' => lang('Contracts'), 'link' => '#contracts');

				$tabletools1[] = array
					(
					'my_name' => 'view',
					'text' => lang('show'),
					'action' => self::link(array(
						'menuaction' => 'property.uilocation.view'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'location_code',
								'source' => 'location_code'))))
				);

				if ($mode == 'edit')
				{
					$tabletools1[] = array
						(
						'my_name' => 'delete',
						'text' => lang('remove_location'),
						'type' => 'custom',
						'custom_code' => "
								var oArgs = " . json_encode(array(
							'menuaction' => 'rental.uicomposite.remove_unit',
							'phpgw_return_as' => 'json'
						)) . ";
								var parameters = " . json_encode(array('parameter' => array(array('name' => 'ids',
									'source' => 'id')))) . ";
								removeUnit(oArgs, parameters);
							"
					);
				}

				$datatable_def[] = array
					(
					'container' => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uiunit.query',
							'composite_id' => $composite_id, 'editable' => 1, 'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'location_code', 'label' => lang('object_number'), 'sortable' => true),
						array('key' => 'loc1_name', 'label' => lang('property'), 'sortable' => false),
						array('key' => 'loc2_name', 'label' => lang('building'), 'sortable' => false),
						array('key' => 'loc3_name', 'label' => lang('floor'), 'sortable' => false),
						array('key' => 'loc4_name', 'label' => lang('section'), 'sortable' => false),
						array('key' => 'loc5_name', 'label' => lang('room'), 'sortable' => false),
						array('key' => 'address', 'label' => lang('address'), 'sortable' => false),
						array('key' => 'area_gros', 'label' => lang('area_gros'), 'sortable' => false,
							'formatter' => 'formatterArea', 'className' => 'right'),
						array('key' => 'area_net', 'label' => lang('area_net'), 'sortable' => false,
							'formatter' => 'formatterArea', 'className' => 'right')
					),
					'tabletools' => $tabletools1,
					'config' => array(
						array('disableFilter' => true)
					)
				);

				if ($mode == 'edit')
				{
					$tabletools2[] = array
						(
						'my_name' => 'view',
						'text' => lang('show'),
						'action' => self::link(array(
							'menuaction' => 'property.uilocation.view'
						)),
						'parameters' => json_encode(array('parameter' => array(array('name' => 'location_code',
									'source' => 'location_code'))))
					);

					$tabletools2[] = array
						(
						'my_name' => 'add',
						'text' => lang('add_location'),
						'type' => 'custom',
						'custom_code' => "
								var oArgs = " . json_encode(array(
							'menuaction' => 'rental.uicomposite.add_unit',
							'composite_id' => $composite_id,
							'phpgw_return_as' => 'json'
						)) . ";
								var parameters = " . json_encode(array('parameter' => array(array('name' => 'location_code',
									'source' => 'location_code')))) . ";
								addUnit(oArgs, parameters);
							"
					);

					$datatable_def[] = array
						(
						'container' => 'datatable-container_1',
						'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uiproperty_location.query',
							'composite_id' => $composite_id,
							'phpgw_return_as' => 'json',
							'part_of_town_id' => $composite->get_part_of_town_id()))),
						'ColumnDefs' => array(
							array('key' => 'location_code', 'label' => lang('location_code'), 'sortable' => true),
							array('key' => 'loc1_name', 'label' => lang('name'), 'sortable' => false),
							array('key' => 'loc2_name', 'label' => lang('building'), 'sortable' => false),
							array('key' => 'loc3_name', 'label' => lang('floor'), 'sortable' => false),
							array('key' => 'loc4_name', 'label' => lang('section'), 'sortable' => false),
							array('key' => 'loc5_name', 'label' => lang('room'), 'sortable' => false),
							array('key' => 'adresse1', 'label' => lang('address'), 'sortable' => false),
							array('key' => 'postnummer', 'label' => lang('post_code'), 'sortable' => false),
							array('key' => 'poststed', 'label' => lang('post_place'), 'sortable' => false),
							array('key' => 'gab', 'label' => lang('gab'), 'sortable' => false)
						),
						'tabletools' => $tabletools2,
						'config' => array(
							array('disableFilter' => true)
						)
					);
				}

				$tabletools3[] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.edit',
						'initial_load' => 'no'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools3[] = array
					(
					'my_name' => 'copy',
					'text' => lang('copy'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.copy_contract'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools3[] = array
					(
					'my_name' => 'show',
					'text' => lang('show'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.view',
						'initial_load' => 'no'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools3[] = array
					(
					'my_name' => 'download_contracts',
					'text' => lang('download'),
					'type' => 'custom',
					'custom_code' => "
							var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.download',
						'composite_id' => $composite_id,
						'type' => 'contracts_for_composite',
						'export' => true
					)) . ";
							downloadContracts(oArgs);
						"
				);

				$datatable_container_name = ($mode == 'edit') ? 'datatable-container_2' : 'datatable-container_1';

				$datatable_def[] = array
					(
					'container' => $datatable_container_name,
					'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uicontract.query',
							'type' => 'contracts_for_composite', 'composite_id' => $composite_id,
							'editable' => 0,
							'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
						array('key' => 'date_start', 'label' => lang('date_start'), 'sortable' => true),
						array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true),
						array('key' => 'type', 'label' => lang('title'), 'sortable' => false),
						array('key' => 'party', 'label' => lang('party'), 'sortable' => false),
						array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true),
						array('key' => 'total_price', 'label' => lang('total_price'), 'sortable' => false,
							'formatter' => 'formatterPrice', 'className' => 'right'),
						array('key' => 'rented_area', 'label' => lang('area'), 'sortable' => false,
							'formatter' => 'formatterArea', 'className' => 'right'),
						array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false,
							'className' => 'center'),
						array('key' => 'contract_notification_status', 'label' => lang('notification_status'),
							'sortable' => false)
					),
					'tabletools' => $tabletools3,
					'config' => array(
						array('disableFilter' => true)
					)
				);

				if ($mode == 'edit')
				{
					$search_options[] = array('id' => 'objno_name_address', 'name' => lang('objno_name_address'),
						'selected' => 1);
					$search_options[] = array('id' => 'gab', 'name' => lang('gab'), 'selected' => 0);

					$level_options[] = array('id' => '1', 'name' => lang('property'), 'selected' => 0);
					$level_options[] = array('id' => '2', 'name' => lang('building'), 'selected' => 1);
					$level_options[] = array('id' => '3', 'name' => lang('floor'), 'selected' => 0);
					$level_options[] = array('id' => '4', 'name' => lang('section'), 'selected' => 0);
					$level_options[] = array('id' => '5', 'name' => lang('room'), 'selected' => 0);
				}

				$contracts_search_options[] = array('id' => 'all', 'name' => lang('all'), 'selected' => 1);
				$contracts_search_options[] = array('id' => 'id', 'name' => lang('contract_id'),
					'selected' => 0);
				$contracts_search_options[] = array('id' => 'party_name', 'name' => lang('party_name'),
					'selected' => 0);
				$contracts_search_options[] = array('id' => 'composite', 'name' => lang('composite_name'),
					'selected' => 0);
				$contracts_search_options[] = array('id' => 'composite_address', 'name' => lang('composite_address'),
					'selected' => 0);
				$contracts_search_options[] = array('id' => 'location_code', 'name' => lang('object_number'),
					'selected' => 0);

				$status_options[] = array('id' => 'all', 'name' => lang('all'), 'selected' => 1);
				$status_options[] = array('id' => 'under_planning', 'name' => lang('under_planning'),
					'selected' => 0);
				$status_options[] = array('id' => 'active', 'name' => lang('active_plural'),
					'selected' => 0);
				$status_options[] = array('id' => 'under_dismissal', 'name' => lang('under_dismissal'),
					'selected' => 0);
				$status_options[] = array('id' => 'ended', 'name' => lang('ended'), 'selected' => 0);

				$fields_of_responsibility_options[] = array('id' => 'all', 'name' => lang('all'),
					'selected' => 0);
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach ($types as $id => $label)
				{
					$fields_of_responsibility_options[] = array('id' => $id, 'name' => lang($label),
						'selected' => 0);
				}

				$units = $composite->get_units();
				$address_1 = $units[0]->get_location()->get_address_1();

//				application
				$tabletools4 = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uiapplication.edit'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id','source' => 'id'))))
				);
				
				$datatable_container_application_name = ($mode == 'edit') ? 'datatable-container_3' : 'datatable-container_2';
				
				$datatable_def[] = array(
					'container' => $datatable_container_application_name,
					'requestUrl' => json_encode(self::link(array(
						'menuaction' => 'rental.uiapplication.query',
						'composite_id' => $composite_id,
						'editable' => 0,
						'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'id', 'label' => 'id', 'formatter' => 'JqueryPortico.formatLink', 'sortable' => true),
						array('key' => 'ecodimb_name', 'label' => 'dimb', 'sortable' => false),
						array('key' => 'email', 'label' => 'email', 'sortable' => false),
						array('key' => 'assign_date_start', 'label' => 'assign_start', 'sortable' => false),
						array('key' => 'assign_date_end', 'label' => 'assign_end', 'sortable' => false),
						array('key' => 'status', 'label' => 'status', 'sortable' => false),
						array('key' => 'entry_date', 'label' => 'entry_date', 'sortable' => true),
						array('key' => 'executive_officer', 'label' => 'executive_officer', 'sortable' => true),
					),
					'tabletools' => $tabletools4,
					'config' => array(
						array('disableFilter' => true)
					)
				);
				
				$applications_search_options[] = array('id' => 'all', 'name' => lang('all'), 'selected' => 1);
				$applications_search_options[] = array('id' => 'ecodimb_name', 'name' => 'dimb', 'selected' => 0);
				
				
				$status_application_options[] = array('id' => '', 'name' => lang('all'), 'selected' => 1);
				$status_application_options[] = array('id' => 1, 'name' => lang('registered'), 'selected' => 0);
				$status_application_options[] = array('id' => 2, 'name' => lang('pending'), 'selected' => 0);
				$status_application_options[] = array('id' => 3, 'name' => lang('rejected'), 'selected' => 0);
				$status_application_options[] = array('id' => 4, 'name' => lang('approved'), 'selected' => 0);
	
				$file_def = array
					(
					array('key' => 'file_name', 'label' => lang('Filename'), 'sortable' => false,
						'resizeable' => true),
					array('key' => 'delete_file', 'label' => lang('Delete file'), 'sortable' => false,
						'resizeable' => true, 'formatter' => 'JqueryPortico.FormatterCenter'),
				);


				$datatable_def[] = array
					(
					'container' => 'datatable-container_4',
					'requestUrl' => json_encode(self::link(array(
						'menuaction' => 'rental.uicomposite.get_files',
						'id' => $composite_id,
						'phpgw_return_as' => 'json'))),
					'ColumnDefs' => $file_def,
					'data' => array(),
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);
			}

			$link_index = array
				(
				'menuaction' => 'rental.uicomposite.index'
			);

			$GLOBALS['phpgw']->jqcal->add_listener('status_date');

			$composite_standard_name = '';
			$cur_standard_id = $composite->get_standard_id();
			$composite_standard_arr = $composite->get_standards($cur_standard_id);
			$composite_standard_options = array();
			foreach ($composite_standard_arr as $composite_standard)
			{
				$selected = ($composite_standard['selected']) ? 1 : 0;
				if ($selected)
				{
					$composite_standard_name = $composite_standard['name'];
				}
				$composite_standard_options[] = array('id' => $composite_standard['id'], 'name' => $composite_standard['name'],
					'selected' => $selected);
			}
			$composite_type_name = '';
			$cur_type_id = $composite->get_composite_type_id();
			$composite_type_arr = $composite->get_types($cur_type_id);
			$composite_type_options = array();
			foreach ($composite_type_arr as $composite_type)
			{
				$selected = ($composite_type['selected']) ? 1 : 0;
				if ($selected)
				{
					$composite_type_name = $composite_type['name'];
				}
				$composite_type_options[] = array('id' => $composite_type['id'], 'name' => $composite_type['name'],
					'selected' => $selected);
			}

			$furnish_type_name = '';
			$furnish_types_arr = $composite->get_furnish_types();
			$cur_furnish_type_id = $composite->get_furnish_type_id();
			$furnish_types_options = array();
			foreach ($furnish_types_arr as $id => $title)
			{
				$selected = ($cur_furnish_type_id == $id) ? 1 : 0;
				if ($selected)
				{
					$furnish_type_name = $title;
				}
				$furnish_types_options[] = array('id' => $id, 'name' => $title, 'selected' => $selected);
			}

			$code = <<<JS
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
				var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $code);

			$current_price_type_id = $composite->get_price_type_id();
			$price_type_options = array();
			$price_item = new rental_price_item();
			foreach ($price_item->get_price_types() as $price_type_id => $price_type_title)
			{
				$selected = ($current_price_type_id == $price_type_id) ? 1 : 0;
				$price_type_options[] = array('id' => $price_type_id, 'name' => lang($price_type_title),
					'selected' => $selected);
			}

			$data = array
				(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uicomposite.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_index),
				'lang_save' => lang('save'),
				'lang_cancel' => lang('cancel'),
				'value_name' => $composite->get_name(),
				'value_composite_standard_name' => $composite_standard_name,
				'list_composite_standard' => array('options' => $composite_standard_options),
				'value_custom_price' => $composite->get_custom_price(),
				'list_price_type' => array('options' => $price_type_options),
				'value_composite_type_name' => $composite_type_name,
				'list_composite_type' => array('options' => $composite_type_options),
				'list_status_id' => array('options' => $this->get_status_options($composite->get_status_id())),
				'value_furnish_type_name' => $furnish_type_name,
				'list_furnish_type' => array('options' => $furnish_types_options),
				'contract_furnished_status'	=>	!empty($this->config->config_data['contract_furnished_status']),
				'value_part_of_town_id'=>$composite->get_part_of_town_id(),
				'list_part_of_town' => array('options' => execMethod('property.bogeneric.get_list', array(
						'type' => 'part_of_town', 'selected' => $composite->get_part_of_town_id(), 'add_empty' => true))),
				'value_custom_price_factor' => $composite->get_custom_price_factor(),
				'value_unit_count' => rental_sounit::get_instance()->get_count('', 'all', array('composite_id' => $composite_id)),
				'value_address_1' => $address_1,
				'has_custom_address' => ($composite->has_custom_address()) ? 1 : 0,
				'value_custom_address_1' => $composite->get_custom_address_1(),
				'value_custom_house_number' => $composite->get_custom_house_number(),
				'value_custom_address_2' => $composite->get_custom_address_2(),
				'value_custom_postcode' => $composite->get_custom_postcode(),
				'value_custom_place' => $composite->get_custom_place(),
				'value_area_gros' => $composite->get_area_gros() . ' ' . $this->area_suffix,
				'value_area_net' => $composite->get_area_net() . ' ' . $this->area_suffix,
				'is_active' => ($composite->is_active()) ? 1 : 0,
				'value_description' => $composite->get_description(),
				'list_search_option' => array('options' => $search_options),
				'list_type_id' => array('options' => $level_options),
				'list_contracts_search_options' => array('options' => $contracts_search_options),
				'list_applications_search_options' => array('options' => $applications_search_options),
				'list_status_options' => array('options' => $status_options),
				'list_status_application_options' => array('options' => $status_application_options),
				'list_fields_of_responsibility_options' => array('options' => $fields_of_responsibility_options),
				'composite_id' => $composite_id,
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file')),
				'multiple_uploader' => true,
				'fileupload'	=> !!$composite_id,
				'multi_upload_parans' => "{menuaction:'rental.uicomposite.build_multi_upload_file', id:'{$composite_id}'}",
			);

			self::add_javascript('rental', 'rental', 'composite.' . $mode . '.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('composite', 'datatable_inline', 'files'), array($mode => $data));
		}

		public function save()
		{
			if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit'));
			}

			$composite_id = (int)phpgw::get_var('id');

			if ($composite_id)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$composite = new rental_composite();
			}

			if (isset($composite))
			{
				$composite->set_name(phpgw::get_var('name'));
				$composite->set_custom_address_1(phpgw::get_var('address_1'));
				$composite->set_has_custom_address(phpgw::get_var('has_custom_address') == 'on' ? true : false);
				$composite->set_custom_house_number(phpgw::get_var('house_number'));
				$composite->set_custom_address_2(phpgw::get_var('address_2'));
				$composite->set_custom_postcode(phpgw::get_var('postcode'));
				$composite->set_custom_place(phpgw::get_var('place'));
				$composite->set_is_active(phpgw::get_var('is_active') == 'on' ? true : false);
				$composite->set_description(phpgw::get_var('description'));
				$composite->set_furnish_type_id(phpgw::get_var('furnish_type_id'));
				$composite->set_standard_id(phpgw::get_var('composite_standard_id', 'int'));
				$composite->set_composite_type_id(phpgw::get_var('composite_type_id', 'int'));
				$composite->set_part_of_town_id(phpgw::get_var('part_of_town_id', 'int'));
				$composite->set_custom_price_factor(phpgw::get_var('custom_price_factor', 'float'));
				$composite->set_custom_price(phpgw::get_var('custom_price', 'float'));
				$composite->set_price_type_id(phpgw::get_var('price_type_id', 'int'));
				$composite->set_status_id(phpgw::get_var('status_id', 'int'));

				if (rental_socomposite::get_instance()->store($composite))
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					$composite_id = $composite->get_id();
					$this->_handle_files($composite_id);
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
			}

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicomposite.edit',
				'id' => $composite_id));
		}

		private function _handle_files( $composite_id )
		{
			$file_name = @str_replace(' ', '_', $_FILES['file']['name']);

			if ($file_name && $composite_id)
			{
				$bofiles = CreateObject('property.bofiles', '/rental');
				$to_file = "{$bofiles->fakebase}/composite/{$composite_id}/{$file_name}";

				if ($bofiles->vfs->file_exists(array(
						'string' => $to_file,
						'relatives' => array(RELATIVE_NONE)
					)))
				{
					phpgwapi_cache::message_set(lang('This file already exists !'), 'error');
				}
				else
				{
					$bofiles->create_document_dir("composite/{$composite_id}");
					$bofiles->vfs->override_acl = 1;

					if (!$bofiles->vfs->cp(array(
							'from' => $_FILES['file']['tmp_name'],
							'to' => $to_file,
							'relatives' => array(RELATIVE_NONE | VFS_REAL, RELATIVE_ALL))))
					{
						phpgwapi_cache::message_set(lang('Failed to upload file !'), 'error');
					}
					$bofiles->vfs->override_acl = 0;
				}
			}

		}

		/**
		 * Public method. Called when user wants to add a unit to a composite
		 * @param HTTP::id	the composite ID
		 * @param HTTP::location_id
		 * @param HTTP::loc1
		 */
		function add_unit()
		{
			if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied'));
			}

			$composite_id = (int)phpgw::get_var('composite_id');
			$location_code = phpgw::get_var('location_code');
			$level = (int)phpgw::get_var('level');

			$result = array();
			if (isset($composite_id) && $composite_id > 0)
			{
				foreach ($location_code as $code)
				{
					$property_location = new rental_property_location($code, '', $level);
					$unit = new rental_unit(0, $composite_id, $property_location);
					$resp = rental_sounit::get_instance()->store($unit);
					if ($resp)
					{
						$result['message'][] = array('msg' => $code . ' ' . lang('has been added'));
					}
					else
					{
						$result['error'][] = array('msg' => $code . ' ' . lang('not added'));
					}
				}
			}
			return $result;
		}

		/**
		 * Public method. Called when user wants to remove a unit to a composite
		 * @param HTTP::id	the composite ID
		 * @param HTTP::location_id
		 */
		function remove_unit()
		{
			if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied'));
			}
			$unit_ids = phpgw::get_var('ids');

			$result = array();
			if (count($unit_ids) > 0)
			{
				foreach ($unit_ids as $id)
				{
					$resp = rental_sounit::get_instance()->delete($id);
					if ($resp)
					{
						$result['message'][] = array('msg' => 'id ' . $id . ' ' . lang('has been deleted'));
					}
					else
					{
						$result['error'][] = array('msg' => 'id ' . $id . ' ' . lang('not deleted'));
					}
				}
			}

			return $result;
		}

		/**
		 * Stores which columns that should be displayed in index(). The data
		 * is stored per user.
		 *
		 */
		function columns()
		{
			$values = phpgw::get_var('values');
			if (isset($values['save']) && $values['save'])
			{
				$GLOBALS['phpgw']->preferences->account_id = $GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('rental', 'rental_columns_composite', $values['columns'], 'user');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
		}

		public function schedule ()
		{
			$composite_id = (int)phpgw::get_var('id');
			$date = new DateTime(phpgw::get_var('date'));
			if ($date->format('w') != 1) {
				$date->modify('last monday');
			}

			$editable = phpgw::get_var('editable', 'bool');
			$type = 'all_composites';

			$filters = $this->get_filters();

			$schedule['filters'] = $filters;

			$schedule['datasource_url'] = self::link(array(
				'menuaction' => 'rental.uicomposite.get_schedule',
				'editable' => ($editable) ? 1 : 0,
				'type' => $type,
				'phpgw_return_as' => 'json'
			));

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
						)
					)
				);

			$toolbar = array();

			$toolbar[] = array(
				'name' => 'new',
				'text' => lang('new'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicomposite.add'
				))
			);

			$toolbar[] = array(
				'name' => 'download',
				'text' => lang('download'),
				'action' => self::link(array(
					'menuaction' => 'rental.uicomposite.download',
					'type' => $type,
					'export' => true,
					'allrows' => true
				))
			);

			$toolbar[] = array(
				'name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.edit'
				)),
				'parameters' => $parameters
			);

			$toolbar[] = array(
				'name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uicomposite.view'
				)),
				'parameters' => $parameters
			);

			$contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();

			$valid_contract_types = array();
			if (isset($this->config->config_data['contract_types']) && is_array($this->config->config_data['contract_types']))
			{
				foreach ($this->config->config_data['contract_types'] as $_key => $_value)
				{
					if ($_value)
					{
						$valid_contract_types[] = $_value;
					}
				}
			}

			$create_types = array();
			foreach ($contract_types as $id => $label)
			{
				if ($valid_contract_types && !in_array($id, $valid_contract_types))
				{
					continue;
				}

				$names = $this->locations->get_name($id);
				if ($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
				{
					if ($this->hasPermissionOn($names['location'], PHPGW_ACL_ADD))
					{
						$create_types[] = array($id, $label);
					}
				}
			}

			foreach ($create_types as $create_type)
			{
				$toolbar[] = array
					(
					'name' => $create_type[1],
					'text' => lang('create_contract_' . $create_type[1]),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uicontract.add_from_composite',
						'responsibility_id' => $create_type[0]
					)),
					'attributes' => array(
						'class' => 'need-free'
					),
					'parameters' => $parameters
				);
			}

			$schedule['composite_id'] = $composite_id;
			$schedule['date'] = $date;
			$schedule['picker_img'] = $GLOBALS['phpgw']->common->image('phpgwapi', 'cal');
			$schedule['toolbar'] = json_encode($toolbar);
			$data['schedule'] = $schedule;
			self::add_javascript('rental','rental','schedule.js');

			phpgwapi_jquery::load_widget("datepicker");

			self::render_template_xsl(array('schedule', 'rental_schedule'), array('schedule' => $data));
		}

		public function get_schedule ()
		{
			$composite_id = (int)phpgw::get_var('id');
			$date = new DateTime(phpgw::get_var('date'));

			if ($date->format('w') != 1)
			{
				$date->modify('last monday');
			}

			$days = array();
			$date_to_array = clone $date;
			for ($i = 0; $i < 7; $i++)
			{
				$days[] = clone $date_to_array;
				$date_to_array->modify("+1 day");
			}

			$composites_obj = $this->query();
			$composites_data = $composites_obj['data'];

			$composites = array();
			$n = 0;

			foreach ($composites_data as $composite)
			{
				$composites[$n]['id'] = $composite['id'];
				$composites[$n]['name'] = $composite['name'];
				$composites[$n]['object_number'] = $composite['location_code'];

				foreach ($days as $day)
				{
					$composites[$n][date_format($day, 'D')]['status'] = $composite['status'];
				}
				$n++;
			}

			$data = array(
				'ResultSet' => array(
					"totalResultsAvailable" => $composites_obj['recordsTotal'],
					"Result" => $composites
				)
			);

			return $data;
		}

		public function handle_multi_upload_file()
		{
			$id = phpgw::get_var('id');

			phpgw::import_class('property.multiuploader');

			$options['base_dir'] = 'composite/'.$id;
			$options['upload_dir'] = $GLOBALS['phpgw_info']['server']['files_dir'].'/rental/'.$options['base_dir'].'/';
			$options['script_url'] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.handle_multi_upload_file', 'id' => $id)));
			$options['fakebase'] = '/rental';
			$upload_handler = new property_multiuploader($options, false);

			switch ($_SERVER['REQUEST_METHOD']) {
				case 'OPTIONS':
				case 'HEAD':
					$upload_handler->head();
					break;
				case 'GET':
					$upload_handler->get();
					break;
				case 'PATCH':
				case 'PUT':
				case 'POST':
					$upload_handler->add_file();
					break;
				case 'DELETE':
					$upload_handler->delete_file();
					break;
				default:
					$upload_handler->header('HTTP/1.1 405 Method Not Allowed');
			}

			$GLOBALS['phpgw']->common->phpgw_exit();
		}

		public function build_multi_upload_file()
		{
			phpgwapi_jquery::init_multi_upload_file();
			$id = phpgw::get_var('id', 'int');

			$GLOBALS['phpgw_info']['flags']['noframework'] = true;
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw_info']['flags']['xslt_app'] = true;

			$multi_upload_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uicomposite.handle_multi_upload_file', 'id' => $id));

			$data = array
				(
				'multi_upload_action' => $multi_upload_action
			);

			$GLOBALS['phpgw']->xslttpl->add_file(array('files', 'multi_upload_file'));
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw', array('multi_upload' => $data));
		}

		function get_files()
		{
			$id = phpgw::get_var('id', 'int');

			if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				return array(
					'data' => array(),
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => 0,
					'recordsFiltered' => 0
				);
			}

			$link_file_data = array
				(
				'menuaction' => 'rental.uicomposite.view_file',
			);


			$link_view_file = $GLOBALS['phpgw']->link('/index.php', $link_file_data);

			$vfs = CreateObject('phpgwapi.vfs');
			$vfs->override_acl = 1;

			$values = $vfs->ls(array(
				'string' => "/rental/composite/{$id}",
				'relatives' => array(RELATIVE_NONE)));

			$vfs->override_acl = 0;

			$content_files = array();

			foreach ($values as $_entry)
			{

				$content_files[] = array(
					'file_name' => '<a href="' . $link_view_file . '&amp;file_id=' . $_entry['file_id'] . '" target="_blank" title="' . lang('click to view file') . '">' . $_entry['name'] . '</a>',
					'delete_file' => '<input type="checkbox" name="values[file_action][]" value="' . $_entry['file_id'] . '" title="' . lang('Check to delete file') . '">',
				);
			}

			if (phpgw::get_var('phpgw_return_as') == 'json')
			{

				$total_records = count($content_files);

				return array
					(
					'data' => $content_files,
					'draw' => phpgw::get_var('draw', 'int'),
					'recordsTotal' => $total_records,
					'recordsFiltered' => $total_records
				);
			}
			return $content_files;
		}

		function view_file()
		{
			if (!$this->acl_read)
			{
				phpgw::no_access();
			}

			ExecMethod('property.bofiles.get_file', phpgw::get_var('file_id', 'int'));
		}

	}
