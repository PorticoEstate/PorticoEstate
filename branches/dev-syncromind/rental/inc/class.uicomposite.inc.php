<?php
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sounit');
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'property_location', 'inc/model/');

	class rental_uicomposite extends rental_uicommon
	{
		var $config;
		
		public $public_functions = array
		(
			'index'		=> true,
			'view'		=> true,
			'edit'		=> true,
			'save'		=> true,
			'columns'	=> true,
			'add'		=> true,
			'add_unit' => true,
			'remove_unit' => true,
			'orphan_units' => true,
			'query'		=> true,
			'download'	=> true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::composites');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('rc');
			
			$this->config = CreateObject('phpgwapi.config','rental');
			$this->config->read();
		}
		
	private function _get_filters()
	{
		$filters = array();

		$search_option = array
		(
			array('id' => 'all', 'name' => lang('all')),
			array('id' => 'name', 'name' => lang('name')),
			array('id' => 'address', 'name' => lang('address')),
			array('id' => 'property_id', 'name' => lang('object_number'))
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'search_option',
						'text'   => lang('search option'),
						'list'   => $search_option
					);
		
		if(isset($this->config->config_data['contract_furnished_status']) && $this->config->config_data['contract_furnished_status'])
		{
			$furnish_types_arr = rental_composite::get_furnish_types();
			$furnish_types = array();
			array_unshift ($furnish_types, array('id'=>'4', 'name'=>lang('Alle')));
			foreach($furnish_types_arr as $id => $title){
					$furnish_types[] = array('id'=>$id, 'name'=>$title); 
			}
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'furnished_status',
							'text'   => lang('furnish_type'),
							'list'   => $furnish_types
						);									
		}
				 
		$districts_arr = execMethod('property.sogeneric.get_list',array('type' => 'district'));
		$districts = array();
		array_unshift ($districts, array('id'=>'', 'name'=>lang('select')));
		foreach($districts_arr as $district)
		{
			$districts[] = array('id'=>$district['id'], 'name'=>$district['name']); 
		}
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'district_id',
						'text'   => lang('district'),
						'list'   => $districts
					);
		
		$active_option = array
		(
			array('id' => 'both', 'name' =>lang('all')),
			array('id' => 'active', 'name' =>lang('in_operation')),
			array('id' => 'non_active', 'name' =>lang('out_of_operation')),
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'is_active',
						'text'   => lang('availability'),
						'list'   => $active_option
					);
		
		$has_contract_option = array
		(
			array('id' => 'both', 'name' =>lang('all')),
			array('id' => 'has_contract', 'name' =>lang('composite_has_contract')),
			array('id' => 'has_no_contract', 'name' =>lang('composite_has_no_contract')),
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'has_contract',
						'text'   => '',
						'list'   => $has_contract_option
					);
		
		return $filters;
	}
	
		public function query()
		{ 
			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}
			
			$search			= phpgw::get_var('search');
			$order			= phpgw::get_var('order');
			$draw			= phpgw::get_var('draw', 'int');
			$columns		= phpgw::get_var('columns');

			$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'identifier'; 
			$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
			$search_for 	= $search['value'];
			$search_type	= phpgw::get_var('search_option', 'string', 'REQUEST', 'all');
			
			$export			= phpgw::get_var('export','bool');
			$editable		= phpgw::get_var('editable', 'bool');
		
			// Create an empty result set
			$result_objects = array();
			$object_count = 0;
			$district_id	= phpgw::get_var('district_id', 'int');
			
			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			
			if ($export)
			{
				$num_of_objects = null;
			}
			
			//Retrieve the type of query and perform type specific logic
			$query_type = phpgw::get_var('type');
			
			switch($query_type)
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
					$filters = array('is_active' => phpgw::get_var('is_active'), 'is_vacant' => phpgw::get_var('occupancy'), 'not_in_contract' => phpgw::get_var('contract_id'));
					$result_objects = rental_socomposite::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
					$object_count = rental_socomposite::get_instance()->get_count($search_for, $search_type, $filters);
					break;
				case 'all_composites': // ... all composites, filters (active and vacant)
					phpgwapi_cache::session_set('rental', 'composite_query', $search_for);
					phpgwapi_cache::session_set('rental', 'composite_search_type', $search_type);
					phpgwapi_cache::session_set('rental', 'composite_status', phpgw::get_var('is_active'));
					phpgwapi_cache::session_set('rental', 'composite_status_contract', phpgw::get_var('has_contract'));
					phpgwapi_cache::session_set('rental', 'composite_furnished_status', phpgw::get_var('furnished_status'));
					$filters = array('furnished_status' => phpgw::get_var('furnished_status'),'is_active' => phpgw::get_var('is_active'), 'is_vacant' => phpgw::get_var('occupancy'), 
									 'has_contract' => phpgw::get_var('has_contract'), 'availability_date_from' => phpgw::get_var('availability_date_from'), 
									 'availability_date_to' => phpgw::get_var('availability_date_to'), 'district_id' => $district_id);
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

			//Create an empty row set
			$rows = array();
			foreach($result_objects as $result) {
				if(isset($result))
				{
					if(!$result->is_active())
					{
						$result->set_status('Ikke i drift');
					}
					$rows[] = $result->serialize();
				}
			}
			
			// ... add result data
			//$result_data = array('results' => $rows, 'total_records' => $object_count);
			
			//$editable = phpgw::get_var('editable') == 'true' ? true : false;
			
			/*$contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();

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
			}*/
			
			/*if(!$export){
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
			}*/

			$result_data    =   array('results' =>  $rows);
			$result_data['total_records']	= $object_count;
			$result_data['draw']    = $draw;

			return $this->jquery_results($result_data);
		
			//return $this->yui_results($result_data, 'total_records', 'results');
		}
		
		/**
		 * Add action links and labels for the context menu of the list items
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, editable]
		 */
		public function add_actions(&$value, $key, $params)
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
			switch($type)
			{
				case 'included_composites':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_composite', 'composite_id' => $value['id'], 'contract_id' => $contract_id)));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'not_included_composites':	//does not show unless editable
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_composite', 'composite_id' => $value['id'], 'contract_id' => $contract_id)));
					$value['labels'][] = lang('add');
					break;
				case 'included_areas':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
					$value['labels'][] = lang('show');
					if($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.remove_unit', 'id' => $contract_id, 'location_id' => $value['location_id'])));
						$value['labels'][] = lang('remove');
					}
					break;
				case 'available_areas':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uilocation.view', 'location_code' => $value['location_code'])));
					$value['labels'][] = lang('show');
					if($editable == true)
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.add_unit', 'id' => $contract_id, 'location_id' => $value['location_id'], 'loc1' => $value['loc1'])));
						$value['labels'][] = lang('add');
					}
					break;
				case 'contracts':
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					if($editable == true)
					{
						$value['ajax'][] = false;
						$value['actions']['edit_contract'] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
					break;
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.view', 'id' => $value['id'])));
					$value['labels'][] = lang('show');
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicomposite.edit', 'id' => $value['id'])));
					$value['labels'][] = lang('edit');
					foreach($create_types as $create_type) {
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_from_composite', 'id' => $value['id'], 'responsibility_id' => $create_type[0])));
						$value['labels'][] = lang('create_contract_'.$create_type[1]);
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

			$editable		= phpgw::get_var('editable', 'bool');
			$user_is		= $this->type_of_user;

			$appname = lang('rc');
			$type = 'all_composites';

			$function_msg = lang('list %1', $appname);

			$data = array(
				'datatable_name'	=> $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
							 (
								 'type'	=> 'date-picker',
								 'id'	=> 'availability_date_from',
								 'name'	=> 'availability_date_from',
								 'value'	=> '',
								 'text' => lang('from')
							 ),
							 array
							 (
								 'type'	=> 'date-picker',
								 'id'	=> 'availability_date_to',
								 'name'	=> 'availability_date_to',
								 'value'	=> '',
								 'text' => lang('to')
							 ),
							array(
								'type'   => 'link',
								'value'  => lang('new'),
								'href'   => self::link(array(
									'menuaction'	=> 'rental.uicomposite.add'
								)),
								'class'  => 'new_item'
							)
						)
					)
				),
				'datatable' => array(
					'source'	=> self::link(array(
						'menuaction'	=> 'rental.uicomposite.index', 
						'editable'		=> ($editable) ? 1 : 0,
						'type'			=> $type,
						'phpgw_return_as' => 'json'
					)),
					'download'	=> self::link(array('menuaction' => 'rental.uicomposite.download',
							'type'		=> $type,
							'export'    => true,
							'allrows'   => true
					)),
					'allrows'	=> true,
					'editor_action' => '',
					'field' => array(
						array(
							'key'		=> 'id', 
							'label'		=> lang('serial'), 
							'className'	=> '', 
							'sortable'	=> false, 
							'hidden'	=> true
						),
						array(
							'key'		=> 'location_code', 
							'label'		=> lang('object_number'), 
							'className'	=> '', 
							'sortable'	=> true, 
							'hidden'	=> false
						),
						array(
							'key'		=> 'name',
							'label'		=> lang('name'),
							'sortable'	=> true,
							'hidden'	=> false
						),
						array(
							'key'		=> 'address',
							'label'		=> lang('address'),
							'sortable'	=> true, 
							'hidden'	=> false
						),
						array(
							'key'		=> 'gab_id',
							'label'		=> lang('propertyident'),
							'sortable'	=> false,
							'hidden'	=> false
						),
						array(
							'key'		=> 'status',
							'label'		=> lang('status'),
							'sortable'	=> true,
							'hidden'	=> false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach($filters as $filter){
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			if(!empty($this->config->config_data['contract_future_info']))
			{
				array_push($data['datatable']['field'], array("key"=>"contracts", "label"=>lang('contract_future_info'), "sortable"=>false, "hidden"=>false));
			}
			if(!empty($this->config->config_data['contract_furnished_status']))
			{
				array_push($data['datatable']['field'], array("key"=>"furnished_status", "label"=>lang('furnish_type'), "sortable"=>false, "hidden"=>false));
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
							'name'		=> 'id',
							'source'	=> 'id'
						),
					)
				);

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'view',
					'text' 			=> lang('show'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'rental.uicomposite.view'
					)),
					'parameters'	=> json_encode($parameters)
				);

			$data['datatable']['actions'][] = array
				(
					'my_name'		=> 'edit',
					'text' 			=> lang('edit'),
					'action'		=> $GLOBALS['phpgw']->link('/index.php',array
					(
						'menuaction'	=> 'rental.uicomposite.edit'
					)),
					'parameters'	=> json_encode($parameters)
				);
			
			$contract_types = rental_socontract::get_instance()->get_fields_of_responsibility();
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
			}
			
			foreach($create_types as $create_type) 
			{
				$data['datatable']['actions'][] = array
					(
						'my_name'		=> $create_type[1],
						'text' 			=> lang('create_contract_'.$create_type[1]),
						'action'		=> $GLOBALS['phpgw']->link('/index.php',array
						(
							'menuaction'		=> 'rental.uicontract.add_from_composite',
							'responsibility_id' => $create_type[0]
						)),
						'parameters'	=> json_encode($parameters)
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
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			//Retrieve the composite object
			$composite_id = (int)phpgw::get_var('id');
			if(isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('invalid_request')));
				return;
			}

			if(isset($composite) && $composite->has_permission(PHPGW_ACL_READ))
			{
				return $this->render(
					'composite.php', 
					array (
						'composite' 	=> $composite,
						'editable' => false,
						'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.index', 'populate_form' => 'yes'))
					)
				);	
			}
			else
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_view_composite')));
			}
			
		}

		/**
		 * Public method. Called when user wants to edit a composite.
		 * @param HTTP::id	the composite ID
		 */
		public function edit($values = array(), $mode = 'edit')
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
			
			// Retrieve the party object or create a new one if correct permissions
			if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_edit')));
			}
			
			$composite_id = (int)phpgw::get_var('id');
			
			if (!empty($values['composite_id']))
			{
				$composite_id = $values['composite_id'];
			}
		
			if(isset($composite_id) && $composite_id > 0)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$composite = new rental_composite();
			}
			
			if($composite_id)
			{
				$tabletools1 = array
				(
					array('my_name'	=> 'select_all'),
					array('my_name'	=> 'select_none')
				);

				$tabletools1[] = array
					(
						'my_name'		=> 'view',
						'text'			=> lang('show'),
						'action'		=> self::link(array(
								'menuaction'	=> 'property.uilocation.view'
						)),
						'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'location_code', 'source'=>'location_code'))))
					);
				
				$remove_unit_link = self::link(array('menuaction'=>'rental.uicomposite.remove_unit', 'phpgw_return_as'=>'json'));

				$tabletools1[] = array
					(
						'my_name'		=> 'delete',
						'text'			=> lang('remove_location'),
						'type'			=> 'custom',
						'custom_code'	=> "    
											var oTT = TableTools.fnGetInstance( 'datatable-container_0' );
											var selected = oTT.fnGetSelectedData();
											var numSelected = selected.length;

											if (numSelected == '0'){
												alert('None selected');
												return false;
											}

											var values = {};

											for ( var n = 0; n < selected.length; ++n )
											{
												var aData = selected[n];
												values[n] = aData['id'];
											}

											var data = {'ids': values};
											var requestUrl = '".$remove_unit_link."';
											JqueryPortico.execute_ajax(requestUrl, function(result){
											
												document.getElementById('message').innerHTML = '';

												if (typeof(result.message) !== 'undefined')
												{
													$.each(result.message, function (k, v) {
														document.getElementById('message').innerHTML += v.msg + '<br/>';
													});
												}

												if (typeof(result.error) !== 'undefined')
												{
													$.each(result.error, function (k, v) {
														document.getElementById('message').innerHTML += v.msg + '<br/>';
													});
												}
												oTable0.fnDraw();

											}, data, 'POST', 'JSON');"
					);
		
				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_0',
					'requestUrl'	=> json_encode(self::link(array('menuaction'=>'rental.uiunit.query', 'composite_id'=>$composite_id, 'editable'=>1, 'phpgw_return_as'=>'json'))),
					'ColumnDefs'	=> array(
								array('key'=>'location_code', 'label'=>lang('object_number'), 'sortable'=>true),
								array('key'=>'loc1_name', 'label'=>lang('property'), 'sortable'=>false),
								array('key'=>'loc2_name', 'label'=>lang('building'), 'sortable'=>false),
								array('key'=>'loc3_name', 'label'=>lang('floor'), 'sortable'=>false),
								array('key'=>'loc4_name', 'label'=>lang('section'), 'sortable'=>false),
								array('key'=>'loc5_name', 'label'=>lang('room'), 'sortable'=>false),
								array('key'=>'address', 'label'=>lang('address'), 'sortable'=>false),
								array('key'=>'area_gros', 'label'=>lang('area_gros'), 'sortable'=>false),
								array('key'=>'area_net', 'label'=>lang('area_net'), 'sortable'=>false)
					),
					'tabletools'	=> $tabletools1,
					'config'		=> array(
						array('disableFilter' => true)
					)
				);
				
				$tabletools2[] = array
					(
						'my_name'		=> 'view',
						'text'			=> lang('show'),
						'action'		=> self::link(array(
								'menuaction'	=> 'property.uilocation.view'
						)),
						'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'location_code', 'source'=>'location_code'))))
					);
				
				$add_unit_link = self::link(array('menuaction'=>'rental.uicomposite.add_unit', 'composite_id'=>$composite_id, 'phpgw_return_as'=>'json'));

				$tabletools2[] = array
					(
						'my_name'		=> 'add',
						'text'			=> lang('add_location'),
						'type'			=> 'custom',
						'custom_code'	=> "    
											var oTT = TableTools.fnGetInstance( 'datatable-container_1' );
											var selected = oTT.fnGetSelectedData();
											var numSelected = selected.length;

											if (numSelected == '0'){
												alert('None selected');
												return false;
											}

											var values = {};

											for ( var n = 0; n < selected.length; ++n )
											{
												var aData = selected[n];
												values[n] = aData['location_code'];
											}

											var data = {'location_code': values};
											var requestUrl = '".$add_unit_link."';
											var level = document.getElementById('type_id').value;
											requestUrl += '&level=' + level;
											JqueryPortico.execute_ajax(requestUrl, function(result){

												document.getElementById('message').innerHTML = '';

												if (typeof(result.message) !== 'undefined')
												{
													$.each(result.message, function (k, v) {
														document.getElementById('message').innerHTML += v.msg + '<br/>';
													});
												}

												if (typeof(result.error) !== 'undefined')
												{
													$.each(result.error, function (k, v) {
														document.getElementById('message').innerHTML += v.msg + '<br/>';
													});
												}
												oTable0.fnDraw();

											}, data, 'POST', 'JSON');"
					);
				
				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_1',
					'requestUrl'	=> json_encode(self::link(array('menuaction'=>'rental.uiproperty_location.query', 'composite_id'=>$composite_id, 'phpgw_return_as'=>'json'))),
					'ColumnDefs'	=> array(
								array('key'=>'location_code', 'label'=>lang('location_code'), 'sortable'=>true),
								array('key'=>'loc1_name', 'label'=>lang('name'), 'sortable'=>false),
								array('key'=>'loc2_name', 'label'=>lang('building'), 'sortable'=>false),
								array('key'=>'loc3_name', 'label'=>lang('floor'), 'sortable'=>false),
								array('key'=>'loc4_name', 'label'=>lang('section'), 'sortable'=>false),
								array('key'=>'loc5_name', 'label'=>lang('room'), 'sortable'=>false),
								array('key'=>'adresse1', 'label'=>lang('address'), 'sortable'=>false),
								array('key'=>'postnummer', 'label'=>lang('post_code'), 'sortable'=>false),
								array('key'=>'poststed', 'label'=>lang('post_place'), 'sortable'=>false),
								array('key'=>'gab', 'label'=>lang('gab'), 'sortable'=>false)
					),
					'tabletools'	=> $tabletools2,
					'config'		=> array(
						array('disableFilter' => true)
					)
				);
				
				$tabletools3[] = array
					(
						'my_name'		=> 'edit',
						'text'			=> lang('edit'),
						'action'		=> self::link(array(
								'menuaction'	=> 'rental.uicontract.edit',
								'initial_load'	=> 'no'
						)),
						'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
					);
				
				$tabletools3[] = array
					(
						'my_name'		=> 'copy',
						'text'			=> lang('copy'),
						'action'		=> self::link(array(
								'menuaction'	=> 'rental.uicontract.copy_contract'
						)),
						'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
					);
				
				$tabletools3[] = array
					(
						'my_name'		=> 'show',
						'text'			=> lang('show'),
						'action'		=> self::link(array(
								'menuaction'	=> 'rental.uicontract.view',
								'initial_load'	=> 'no'
						)),
						'parameters'	=> json_encode(array('parameter'=>array(array('name'=>'id', 'source'=>'id'))))
					);
				
				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_2',
					'requestUrl'	=> json_encode(self::link(array('menuaction'=>'rental.uicontract.query', 'type'=>'contracts_for_composite', 'composite_id'=>$composite_id, 'editable'=>0, 'phpgw_return_as'=>'json'))),
					'ColumnDefs'	=> array(
								array('key'=>'old_contract_id', 'label'=>lang('contract_id'), 'sortable'=>true),
								array('key'=>'date_start', 'label'=>lang('date_start'), 'sortable'=>true),
								array('key'=>'date_end', 'label'=>lang('date_end'), 'sortable'=>true),
								array('key'=>'type', 'label'=>lang('title'), 'sortable'=>false),
								array('key'=>'party', 'label'=>lang('party'), 'sortable'=>false),
								array('key'=>'term_label', 'label'=>lang('billing_term'), 'sortable'=>true),
								array('key'=>'total_price', 'label'=>lang('total_price'), 'sortable'=>false),
								array('key'=>'rented_area', 'label'=>lang('area'), 'sortable'=>false),
								array('key'=>'contract_status', 'label'=>lang('contract_status'), 'sortable'=>false),
								array('key'=>'contract_notification_status', 'label'=>lang('notification_status'), 'sortable'=>false)
					),
					'tabletools'	=> $tabletools3,
					'config'		=> array(
						array('disableFilter' => true)
					)
				);
			}
			
			$link_index = array
				(
					'menuaction'	=> 'rental.uicomposite.index'
				);
		
			$GLOBALS['phpgw']->jqcal->add_listener('date_status');

			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();
	
			$cur_standard_id = $composite->get_standard_id();
			$composite_standard_arr = $composite->get_standards($cur_standard_id);
			$composite_standard_options = array();
			foreach($composite_standard_arr as $composite_standard)
			{
				$selected = ($composite_standard['selected']) ? 1 : 0;
				$composite_standard_options[] = array('id'=>$composite_standard['id'], 'name'=>$composite_standard['name'], 'selected'=>$selected);				
			}			
		
			$furnish_types_arr = $composite->get_furnish_types();
			$cur_furnish_type_id = $composite->get_furnish_type_id();
			$furnish_types_options = array();
			foreach($furnish_types_arr as $id => $title)
			{
				$selected = ($cur_furnish_type_id == $id) ? 1 : 0;
				$furnish_types_options[] = array('id'=>$id, 'name'=>$title, 'selected'=>$selected);				
			}
			
			$search_options[] = array('id'=>'objno_name_address', 'name'=>lang('objno_name_address'), 'selected'=>1);
			$search_options[] = array('id'=>'gab', 'name'=>lang('gab'), 'selected'=>0);
			
			$level_options[] = array('id'=>'1', 'name'=>lang('property'), 'selected'=>0);
			$level_options[] = array('id'=>'2', 'name'=>lang('building'), 'selected'=>1);
			$level_options[] = array('id'=>'3', 'name'=>lang('floor'), 'selected'=>0);
			$level_options[] = array('id'=>'4', 'name'=>lang('section'), 'selected'=>0);
			$level_options[] = array('id'=>'5', 'name'=>lang('room'), 'selected'=>0);
			
			$contracts_search_options[] = array('id'=>'all', 'name'=>lang('all'), 'selected'=>1);
			$contracts_search_options[] = array('id'=>'id', 'name'=>lang('contract_id'), 'selected'=>0);
			$contracts_search_options[] = array('id'=>'party_name', 'name'=>lang('party_name'), 'selected'=>0);
			$contracts_search_options[] = array('id'=>'composite', 'name'=>lang('composite_name'), 'selected'=>0);
			$contracts_search_options[] = array('id'=>'composite_address', 'name'=>lang('composite_address'), 'selected'=>0);
			$contracts_search_options[] = array('id'=>'location_id', 'name'=>lang('object_number'), 'selected'=>0);
			
			$status_options[] = array('id'=>'all', 'name'=>lang('all'), 'selected'=>1);
			$status_options[] = array('id'=>'under_planning', 'name'=>lang('under_planning'), 'selected'=>0);
			$status_options[] = array('id'=>'active', 'name'=>lang('active_plural'), 'selected'=>0);
			$status_options[] = array('id'=>'under_dismissal', 'name'=>lang('under_dismissal'), 'selected'=>0);
			$status_options[] = array('id'=>'ended', 'name'=>lang('ended'), 'selected'=>0);
			
			$fields_of_responsibility_options[] = array('id'=>'all', 'name'=>lang('all'), 'selected'=>0);
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach($types as $id => $label)
			{
				$fields_of_responsibility_options[] = array('id'=>$id, 'name'=>lang($label), 'selected'=>0);
			}
			
			$tabs = array();
			$tabs['details']	= array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';
		
			if ($composite_id)
			{
				$tabs['units']	= array('label' => lang('Units'), 'link' => '#units');
				$tabs['contracts']	= array('label' => lang('Contracts'), 'link' => '#contracts');
			}
			
			$data = array
			(
				'datatable_def'					=> $datatable_def,
				'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab),		
				'form_action'					=> $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'rental.uicomposite.save')),
				'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_index),
				'lang_save'						=> lang('save'),
				'lang_cancel'					=> lang('cancel'),			
				'editable'						=> true,
				
				'lang_name'						=> lang('name'),
				'lang_address'					=> lang('address'),
				'lang_composite_standard'		=> lang('composite standard'),
				'lang_furnish_type'				=> lang('furnish_type'),
				'lang_has_custom_address'		=> lang('has_custom_address'),
				'lang_overridden_address'		=> lang('overridden_address'),
				'lang_house_number'				=> lang('house_number'),
				'lang_post_code'				=> lang('post_code'),
				'lang_post_place'				=> lang('post_place'),
				'lang_area_gros'				=> lang('area_gros'),
				'lang_area_net'					=> lang('area_net'),
				'lang_available'				=> lang('available ?'),
				'lang_description'				=> lang('description'),
				
				'lang_search_options'			=> lang('search_options'),
				'lang_search_for'				=> lang('search_for'),
				'lang_search_where'				=> lang('search_where'),
				'lang_level'					=> lang('level'),

				'value_name'					=> $composite->get_name(),
				'list_composite_standard'		=> array('options' => $composite_standard_options),
				'list_furnish_type'				=> array('options' => $furnish_types_options),
				'has_custom_address'			=> ($composite->has_custom_address()) ? 1 : 0,
				'value_custom_address_1'		=> $composite->get_custom_address_1(),
				'value_custom_house_number'		=> $composite->get_custom_house_number(),
				'value_custom_address_2'		=> $composite->get_custom_address_2(),
				'value_custom_postcode'			=> $composite->get_custom_postcode(),
				'value_custom_place'			=> $composite->get_custom_place(),
				'value_area_gros'				=> $composite->get_area_gros(). ' ' .(($config->config_data['area_suffix']) ? $config->config_data['area_suffix'] : 'kvm'),
				'value_area_net'				=> $composite->get_area_net(). ' ' .(($config->config_data['area_suffix']) ? $config->config_data['area_suffix'] : 'kvm'),
				'is_active'						=> ($composite->is_active()) ? 1 : 0,
				'value_description'				=> $composite->get_description(),
				
				'list_search_option'			=> array('options' => $search_options),
				'list_type_id'					=> array('options' => $level_options),
				
				'list_contracts_search_options'	=> array('options' => $contracts_search_options),
				'list_status_options'			=> array('options' => $status_options),
				'list_fields_of_responsibility_options'	=> array('options' => $fields_of_responsibility_options),
				
				'composite_id'					=> $composite_id,

				'validator'				=> phpgwapi_jquery::formvalidator_generate(array('location', 'date', 'security', 'file'))
			);

			self::add_javascript('rental', 'rental', 'composite.edit.js');
			self::render_template_xsl(array('composite', 'datatable_inline'), array('edit' => $data));
		}

		
		public function save()
		{
			if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$this->render('permission_denied.php',array('error' => lang('permission_denied_edit')));
			}
			
			$composite_id = (int)phpgw::get_var('id');

			if($composite_id)
			{
				$composite = rental_socomposite::get_instance()->get_single($composite_id);
			}
			else
			{
				$composite = new rental_composite();
			}

			if(isset($composite))
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
				$composite->set_standard_id(phpgw::get_var('composite_standard_id','int'));

				if(rental_socomposite::get_instance()->store($composite))
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					$composite_id = $composite->get_id();					
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
			}
			
			$this->edit(array('composite_id'=>$composite_id));
		}
	
		/**
		 * Public method. Called when user wants to add a unit to a composite
		 * @param HTTP::id	the composite ID
		 * @param HTTP::location_id
		 * @param HTTP::loc1
		 */
		function add_unit()
		{
			if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
				return;
			}
			
			$composite_id = (int)phpgw::get_var('composite_id');
			$location_code = phpgw::get_var('location_code');
			$level = (int)phpgw::get_var('level');
			
			$result = array();
			if(isset($composite_id) && $composite_id > 0)
			{
				foreach ($location_code as $code) 
				{
					$property_location = new rental_property_location($code, '', $level);
					$unit = new rental_unit(0,$composite_id, $property_location);
					$resp = rental_sounit::get_instance()->store($unit);			
					if ($resp) {
						$result['message'][] = array('msg'=>$code.' '.lang('has been added'));
					} else {
						$result['error'][] = array('msg'=>$code.' '.lang('not added'));
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
			if(!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
				return;
			}
			$unit_ids = phpgw::get_var('ids');
			
			$result = array();
			if(count($unit_ids)> 0 )
			{
				foreach($unit_ids as $id) 
				{
					$resp = rental_sounit::get_instance()->delete($id);
					if ($resp) {
						$result['message'][] = array('msg'=>'id '.$id.' '.lang('has been deleted'));
					} else {
						$result['error'][] = array('msg'=>'id '.$id.' '.lang('not deleted'));
					}					
				}
			}
			
			return $result;
		}

		/**
		 * Get a list of rental units or areas that are not tied to any rental composite
		 *
		 */
		public function orphan_units()
		{
			if(!$this->isExecutiveOfficer())
			{
				$this->render('permission_denied.php', array('message' => lang('permission_denied')));
				return;
			}

			self::set_active_menu('rental::composites::orphan_units');

			$data = array
			(
				'message' => phpgw::get_var('message'),
				'error' =>  phpgw::get_var('error'),
				'cancel_link' => self::link(array('menuaction' => 'rental.uicomposite.orphan_units'))
			);

			$this->render('orphan_unit_list.php', $data);
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
				$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
				$GLOBALS['phpgw']->preferences->read();
				$GLOBALS['phpgw']->preferences->add('rental','rental_columns_composite',$values['columns'],'user');
				$GLOBALS['phpgw']->preferences->save_repository();
			}
		}
	}
?>
