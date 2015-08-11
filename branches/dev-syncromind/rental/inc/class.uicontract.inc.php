<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.sobilling');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.socomposite');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.soinvoice');
	phpgw::import_class('rental.sonotification');
	phpgw::import_class('rental.soprice_item');
	phpgw::import_class('rental.socontract_price_item');
	phpgw::import_class('rental.soadjustment');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'notification', 'inc/model/');

	phpgw::import_class('phpgwapi.datetime');

	class rental_uicontract extends rental_uicommon
	{
		private $pdf_templates = array();
		private $config;
		/*private $decimalSeparator;
		private $thousandsSeparator;
		private $decimalPlaces;*/
		
		public $public_functions = array
		(
			'add'					=> true,
			'add_from_composite'	=> true,
			'copy_contract'			=> true,
			'edit'					=> true,
			'save'					=> true,
			'index'					=> true,
			'query'					=> true,
			'view'					=> true,
			'add_party'				=> true,
			'remove_party'			=> true,
			'add_composite'			=> true,
			'remove_composite'		=> true,
			'set_payer'				=> true,
			'add_price_item'		=> true,
			'remove_price_item'		=> true,
			'reset_price_item'		=> true,
			'download'              => true,
			'get_total_price'		=> true
		);

		public function __construct()
		{
			$this->get_pdf_templates();
			parent::__construct();
			self::set_active_menu('rental::contracts');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('contracts');
			
			$this->config = CreateObject('phpgwapi.config','rental');
			$this->config->read();
			/*$this->thousandsSeparator = ($GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['thousands_separator'] : ' ';
			$this->decimalSeparator = ($GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['decimal_separator'] : ',';
			$this->decimalPlaces = ($GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places']) ? $GLOBALS['phpgw_info']['user']['preferences']['rental']['currency_decimal_places'] : 2;*/
			
		}

	private function _get_filters()
	{
		$filters = array();

		if($this->isAdministrator() || $this->isExecutiveOfficer())
		{
			/*$config	= CreateObject('phpgwapi.config','rental');
			$config->read();*/
			$valid_contract_types = array();
			if(isset($this->config->config_data['contract_types']) && is_array($this->config->config_data['contract_types']))
			{
				foreach ($this->config->config_data['contract_types'] as $_key => $_value)
				{
					if($_value)
					{
						$valid_contract_types[] = $_value;
					}
				}
			}
			$new_contract_options = array();
			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			foreach($types as $id => $label)
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
						$new_contract_options[] = array('id' => $id, 'name' => lang($label));
					}
				}
			}
			$filters[] = array
						(
							'type'   => 'filter',
							'name'   => 'location_id',
							'text'   => lang('t_new_contract'),
							'list'   => $new_contract_options
						);
		}	

		$search_option = array
		(
			array('id' => 'all', 'name' => lang('all')),
			array('id' => 'id', 'name' => lang('contract_id')),
			array('id' => 'party_name', 'name' => lang('party_name')),
			array('id' => 'composite', 'name' => lang('composite_name')),
			array('id' => 'composite_address', 'name' => lang('composite_address')),
			array('id' => 'location_id', 'name' => lang('object_number'))
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'search_option',
						'text'   => lang('search_where'),
						'list'   => $search_option
					);
		
		$status_option = array
		(
			array('id' => 'all', 'name' => lang('all')),
			array('id' => 'under_planning', 'name' => lang('under_planning')),
			array('id' => 'active', 'name' => lang('active_plural')),
			array('id' => 'under_dismissal', 'name' => lang('under_dismissal')),
			array('id' => 'ended', 'name' => lang('ended'))
		);
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'contract_status',
						'text'   => lang('status'),
						'list'   => $status_option
					);
		
		$types = rental_socontract::get_instance()->get_fields_of_responsibility();
		$types_options = array();
		array_unshift ($types_options, array('id'=>'all', 'name'=>lang('all')));
		foreach($types as $id => $label)
		{
			$types_options[] = array('id' => $id, 'name' =>lang($label));
		}
		$filters[] = array
					(
						'type'   => 'filter',
						'name'   => 'contract_type',
						'text'   => lang('field_of_responsibility'),
						'list'   => $types_options
					);
		
		return $filters;
	}
	
	public function query()
	{
		if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
		{
			$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
		}
		else {
			$user_rows_per_page = 10;
		}
			
		$search			= phpgw::get_var('search');
		$order			= phpgw::get_var('order');
		$draw			= phpgw::get_var('draw', 'int');
		$columns		= phpgw::get_var('columns');

		$start_index	= phpgw::get_var('start', 'int', 'REQUEST', 0);
		$num_of_objects	= (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
		$sort_field		= ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'old_contract_id'; 
		$sort_ascending	= ($order[0]['dir'] == 'desc') ? false : true;
		// Form variables
		$search_for 	= $search['value'];
		$search_type	= phpgw::get_var('search_option', 'string', 'REQUEST', 'all');
		
		$export			= phpgw::get_var('export', 'bool');
		//$editable		= phpgw::get_var('editable', 'bool');
			
		// Create an empty result set
		$result_objects = array();
		$result_count = 0;
		
		if ($export)
		{
			$num_of_objects = null;
		}
		
		$price_items_only = phpgw::get_var('price_items'); //should only export contract price items
		
		$type = phpgw::get_var('type');
		switch($type)
		{
			case 'contracts_for_adjustment':
				$adjustment_id = (int)phpgw::get_var('id');
				$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);
				$filters = array('contract_type' => $adjustment->get_responsibility_id(), 'adjustment_interval' => $adjustment->get_interval(), 'adjustment_year' => $adjustment->get_year(), 'adjustment_is_executed' => $adjustment->is_executed(), 'extra_adjustment' => $adjustment->is_extra_adjustment());
				break;
			case 'contracts_part': 						// Contracts for this party
				$filters = array('party_id' => phpgw::get_var('party_id'),'contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'), 'status_date_hidden' => phpgw::get_var('status_date_hidden'));
				break;
			case 'contracts_for_executive_officer': 	// Contracts for this executive officer
				$filters = array('executive_officer' => $GLOBALS['phpgw_info']['user']['account_id']);
				break;
			case 'ending_contracts':
			case 'ended_contracts':
			case 'last_edited':	
			case 'closing_due_date':
			case 'terminated_contracts':				
				// Queries that depend on areas of responsibility
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				$ids = array();
				$read_access = array();
				foreach($types as $id => $label)
				{
					$names = $this->locations->get_name($id);
					if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
					{
						if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
						{
							$ids[] = $id;
						}
						else
						{
							$read_access[] = $id;
						}
					}
				}

				if(count($ids) > 0)
				{
					$comma_seperated_ids = implode(',',$ids);
				}
				else
				{
					$comma_seperated_ids = implode(',',$read_access);
				}

				switch($type)
				{
					case 'ending_contracts':			// Contracts that are about to end in areas of responsibility
						$filters = array('contract_status' => 'under_dismissal', 'contract_type' => $comma_seperated_ids);
						break;
					case 'ended_contracts': 			// Contracts that are ended in areas of responsibility
						$filters = array('contract_status' => 'ended', 'contract_type' => $comma_seperated_ids);
						break;
					case 'last_edited': 				// Contracts that are last edited in areas of resposibility
						$filters = array('contract_type' => $comma_seperated_ids);
						$sort_field = 'contract.last_updated';
						$sort_ascending = false;
						break;
					case 'closing_due_date':			//Contracts closing due date in areas of responsibility
						$filters = array('contract_status' => 'closing_due_date', 'contract_type' => $comma_seperated_ids);
						break;
					case 'terminated_contracts':
						$filters = array('contract_status' => 'terminated_contracts', 'contract_type' => $comma_seperated_ids);
						break;
				}

				break;
			case 'contracts_for_composite': // ... all contracts this composite is involved in, filters (status and date)
				$filters = array('composite_id' => phpgw::get_var('composite_id'),'contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'));
				$filters['status_date']			= phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_status'));
				break;
			case 'get_contract_warnings':	//get the contract warnings
				$contract = rental_socontract::get_instance()->get_single(phpgw::get_var('contract_id'));
				$contract->check_consistency();
				$rows = $contract->get_consistency_warnings();
				$result_count = count($rows);
				$export=true;
				break;
			case 'all_contracts':
			default:
				phpgwapi_cache::session_set('rental', 'contract_query', $search_for);
				phpgwapi_cache::session_set('rental', 'contract_search_type', $search_type);
				phpgwapi_cache::session_set('rental', 'contract_status', phpgw::get_var('contract_status'));
				phpgwapi_cache::session_set('rental', 'contract_status_date', phpgw::get_var('date_status'));
				phpgwapi_cache::session_set('rental', 'contract_type', phpgw::get_var('contract_type'));
				$filters = array('contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'));
				$filters['status_date']			= phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_status'));
				$filters['start_date_report']	= phpgwapi_datetime::date_to_timestamp(phpgw::get_var('start_date_report'));
				$filters['end_date_report']		= phpgwapi_datetime::date_to_timestamp(phpgw::get_var('end_date_report'));
		}
		
		if($type != 'get_contract_warnings')
		{
			$result_objects = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_socontract::get_instance()->get_count($search_for, $search_type, $filters);

			//Serialize the contracts found
			$rows = array();
			foreach ($result_objects as $result) {
				if(isset($result))
				{
					if(isset($price_items_only))
					{
						//export contract price items
						$result_objects_pi = rental_socontract_price_item::get_instance()->get(null, null, null, null, null, null, array('contract_id' => $result->get_id(),'export'=>'true','include_billed'=>'true'));
						foreach ($result_objects_pi as $result_pi) {
							if(isset($result_pi))
							{
								$rows[] = $result_pi->serialize();
							}
						}
					}
					else
					{
						//export contracts
						$rows[] = $result->serialize();
					}
				}
			}
			//var_dump("Usage " .memory_get_usage() . " bytes after serializing");
		}

		if(!$export){
			//Add context menu columns (actions and labels)
			//$config	= CreateObject('phpgwapi.config','rental');

			//Check if user has access to Catch module
			$access = $this->acl->check('.',PHPGW_ACL_READ,'catch');
			if($access)
			{
				//$config->read();
				$entity_id_in = $this->config->config_data['entity_config_move_in'];
				$entity_id_out = $this->config->config_data['entity_config_move_out'];
				$category_id_in = $this->config->config_data['category_config_move_in'];	
				$category_id_out = $this->config->config_data['category_config_move_out'];		
			}

			array_walk($rows, array($this, 'add_actions'), array($type,$ids,$adjustment_id,$entity_id_in,$entity_id_out,$category_id_in,$category_id_out));
		}

		$result_data    =   array('results' =>  $rows);
		$result_data['total_records']	= $result_count;
		$result_data['draw']    = $draw;

		return $this->jquery_results($result_data);
	}

	/**
	 * Add data for context menu
	 *
	 * @param $value pointer to
	 * @param $key ?
	 * @param $params [type of query, editable]
	 */
	public function add_actions(&$value, $key, $params)
	{
		/*$value['ajax'] = array();
		$value['actions'] = array();
		$value['labels'] = array();*/

		$type = $params[0];
		$ids = $params[1];
		$adjustment_id = $params[2];
		$entity_id_in = $params[3];
		$entity_id_out = $params[4];
		$category_id_in = $params[5];
		$category_id_out = $params[6];
		$actions = array();

		switch($type)
		{
			case 'last_edited_by':
			case 'contracts_for_executive_officer':
			case 'ending_contracts':
			case 'ended_contracts':
			case 'closing_due_date':
			case 'terminated_contracts':
				if(count($ids) > 0)
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
					$value['labels'][] = lang('edit_contract');*/
					$url  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
					$actions[] = '<a href="'.$url.'">'.lang('edit_contract').'</a>';
				}
				else
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
					$value['labels'][] = lang('show');*/
					$url  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
					$actions[] = '<a href="'.$url.'">'.lang('show').'</a>';
				}
				break;
			case 'contracts_for_adjustment':
				if(!isset($ids) || count($ids) > 0)
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit',
																				'id' => $value['id'], 
																				'initial_load' => 'no',
																				'adjustment_id' => $adjustment_id)));
					$value['labels'][] = lang('edit');*/
					$url1  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit',
																				'id' => $value['id'], 
																				'initial_load' => 'no',
																				'adjustment_id' => $adjustment_id)));
					$actions[] = '<a href="'.$url1.'">'.lang('edit').'</a>';
					
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract',
																								'id' => $value['id'],
																								'adjustment_id' => $adjustment_id)));
					$value['labels'][] = lang('copy');*/
					$url2  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract',
																								'id' => $value['id'],
																								'adjustment_id' => $adjustment_id)));
					$actions[] = '<a href="'.$url2.'">'.lang('copy').'</a>';
				}
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view',
																								'id' => $value['id'], 
																								'initial_load' => 'no',
																								'adjustment_id' => $adjustment_id)));
				$value['labels'][] = lang('show');*/
				$url3  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view',
																								'id' => $value['id'], 
																								'initial_load' => 'no',
																								'adjustment_id' => $adjustment_id)));
				$actions[] = '<a href="'.$url3.'">'.lang('show').'</a>';

				break;
			default:
				if(!isset($ids) || count($ids) > 0)
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
					$value['labels'][] = lang('edit');*/
					$url1  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
					$actions[] = '<a href="'.$url1.'">'.lang('edit').'</a>';
					
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract', 'id' => $value['id'])));
					$value['labels'][] = lang('copy');*/
					$url2  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract', 'id' => $value['id'])));
					$actions[] = '<a href="'.$url2.'">'.lang('copy').'</a>';
				}
				/*$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
				$value['labels'][] = lang('show');*/
				$url3  = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
				$actions[] = '<a href="'.$url3.'">'.lang('show').'</a>';
				$temlate_counter = 0;
				foreach ($this->pdf_templates as $pdf_template){

					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uimakepdf.view', 'id' => $value['id'], 'pdf_template' => $temlate_counter )));
					$value['labels'][] = lang('make_pdf').": ". $pdf_template[0];*/
					$url4  = html_entity_decode(self::link(array('menuaction' => 'rental.uimakepdf.view', 'id' => $value['id'], 'pdf_template' => $temlate_counter )));
					$actions[] = '<a href="'.$url4.'">'.lang('make_pdf').": ". $pdf_template[0].'</a>';
					$temlate_counter++;
				}
				//http://portico/pe/index.php?menuaction=property.uientity.index&second_display=1&entity_id=3&cat_id=1&type=catch&district_id=0&query=Tes&start_date=&end_date=&click_history=06014d0abc7293bfb52ff5d1c04f3cb8&phpgw_return_as=json
				if(isset($entity_id_in) && $entity_id_in != '' && isset($category_id_in) && $category_id_in != '')
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_in, 'cat_id' => $category_id_in,'query' => $value['old_contract_id'], 'type' => 'catch')));
					$value['labels'][] = lang('show_move_in_reports');*/
					$url5  = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_in, 'cat_id' => $category_id_in,'query' => $value['old_contract_id'], 'type' => 'catch')));
					$actions[] = '<a href="'.$url5.'">'.lang('show_move_in_reports').'</a>';
				}

				if(isset($entity_id_out) && $entity_id_out != '' && isset($category_id_out) && $category_id_out != '')
				{
					/*$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_out, 'cat_id' => $category_id_out,'query' => $value['old_contract_id'], 'type' => 'catch')));
					$value['labels'][] = lang('show_move_out_reports');*/
					$url6  = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_out, 'cat_id' => $category_id_out,'query' => $value['old_contract_id'], 'type' => 'catch')));
					$actions[] = '<a href="'.$url6.'">'.lang('show_move_out_reports').'</a>';
				}
		}
		
		$value['actions'] = implode(' | ', $actions);
	}

	/**
	 * View a list of all contracts
	 */
	public function index()
	{		
		if (phpgw::get_var('phpgw_return_as') == 'json')
		{
			return $this->query();
		}
		
		$editable		= phpgw::get_var('editable', 'bool');
		$user_is		= $this->type_of_user;
			
		self::set_active_menu('rental::parties');
		$appname = lang('contracts');
		$type = 'all_contracts';
				
		$function_msg = lang('list %1', $appname);

		$data = array(
			'datatable_name'	=> $function_msg,
			'form' => array(
				'toolbar' => array(
					'item' => array(
						array(
							'type'   => 'link',
							'value'  => lang('new'),
							'onclick'=> 'onNew_contract()',
							'class'  => 'new_item'
						)
					)
				)
			),
			'datatable' => array(
				'source'	=> self::link(array(
					'menuaction'	=> 'rental.uicontract.index', 
					'editable'		=> ($editable) ? 1 : 0,
					'type'			=> $type,
					'phpgw_return_as' => 'json'
				)),
				'download'	=> self::link(array('menuaction' => 'rental.uicontract.download',
						'type'		=> $type,
						'export'    => true,
						'allrows'   => true
				)),
				'allrows'	=> true,
				'editor_action' => '',
				'field' => array(
					array(
						'key'		=> 'old_contract_id', 
						'label'		=> lang('contract_id'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'date_start', 
						'label'		=> lang('date_start'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'date_end', 
						'label'		=> lang('date_end'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'type', 
						'label'		=> lang('responsibility'), 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'composite', 
						'label'		=> lang('composite'), 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'party', 
						'label'		=> lang('party'), 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'department', 
						'label'		=> lang('department'), 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'term_label', 
						'label'		=> lang('billing_term'), 
						'className'	=> '', 
						'sortable'	=> true, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'total_price', 
						'label'		=> lang('total_price'), 
						'className'	=> 'right', 
						'sortable'	=> false, 
						'hidden'	=> false,
						'formatter' => 'formatterPrice'
					),
					array(
						'key'		=> 'rented_area', 
						'label'		=> lang('area'), 
						'className'	=> 'right', 
						'sortable'	=> false, 
						'hidden'	=> false,
						'formatter' => 'formatterArea'
					),
					array(
						'key'		=> 'contract_status', 
						'label'		=> lang('contract_status'), 
						'className'	=> '', 
						'sortable'	=> false, 
						'hidden'	=> false
					),
					array(
						'key'		=> 'contract_notification_status', 
						'label'		=> lang('notification_status'), 
						'sortable'	=> false,
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
			
		array_push($data['datatable']['field'], array("key" => "actions", "label" => lang('actions'), "sortable"=>false, "hidden"=>false, "className"=>'dt-center all'));
		
		$code =	<<<JS
			var thousandsSeparator = '$this->thousandsSeparator';
			var decimalSeparator = '$this->decimalSeparator';
			var decimalPlaces = '$this->decimalPlaces';
			var currency_suffix = '$this->currency_suffix';
			var area_suffix = '$this->area_suffix';
JS;
		$GLOBALS['phpgw']->js->add_code('', $code);
			
		self::add_javascript('rental', 'rental', 'contract.index.js');
		phpgwapi_jquery::load_widget('numberformat');
		self::render_template_xsl('datatable_jquery', $data);
		
	}

		public function save()
		{
			$contract_id = (int)phpgw::get_var('id');
			$location_id = (int)phpgw::get_var('location_id');
			$update_price_items = false;

			$message = null;
			$error = null;
			$add_default_price_items = false;

			if(isset($contract_id) && $contract_id > 0)
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);

				// Gets responsibility area from db (ex: eksternleie, internleie)
				$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($contract->get_location_id());

				// Redirect with error message if responsibility area is eksternleie and contract type not set
				if( !is_numeric( phpgw::get_var('contract_type') ) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0) )
				{
					//$error = lang('billing_removed_external_contract');
					//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message, 'error' => $error));	
					phpgwapi_cache::message_set(lang('billing_removed_external_contract'), 'error'); 
					$this->edit();
				}

				if(!$contract->has_permission(PHPGW_ACL_EDIT))
				{
					unset($contract);
					//$this->render('permission_denied.php',array('error' => lang('permission_denied_edit_contract')));
					phpgwapi_cache::message_set(lang('permission_denied_edit_contract'), 'error'); 
					$this->edit();
				}
			}
			else
			{
				// Gets responsibility area from db (ex: eksternleie, internleie) 
				$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($location_id);

				// Redirect with error message if responsibility area is eksternleie and contract type not set
				if( !is_numeric( phpgw::get_var('contract_type') ) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0) )
				{
					//$error = lang('billing_removed_external_contract');						
					//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'location_id' => $location_id, 'message' => $message, 'error' => $error));
					phpgwapi_cache::message_set(lang('billing_removed_external_contract'), 'error'); 
					$this->edit();					
				}

				if(isset($location_id) && ($this->isExecutiveOfficer() || $this->isAdministrator())){
					$contract = new rental_contract();
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					$contract->set_location_id($location_id);
					$contract->set_contract_type_title($fields[$location_id]);
					$add_default_price_items = true;
				}
			}

			$date_start =  phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_start'));
			$date_end =  phpgwapi_datetime::date_to_timestamp(phpgw::get_var('date_end'));

			if(isset($contract))
			{ 
				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));
				$contract->set_security_type(phpgw::get_var('security_type'));
				$contract->set_security_amount(phpgw::get_var('security_amount'));
				$contract->set_executive_officer_id(phpgw::get_var('executive_officer'));
				$contract->set_comment(phpgw::get_var('comment'));

				if(isset($location_id) && $location_id > 0)
				{
					$contract->set_location_id($location_id); // only present when new contract
				}
				$contract->set_term_id(phpgw::get_var('billing_term'));
				$contract->set_billing_start_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('billing_start_date')));
				$contract->set_billing_end_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('billing_end_date')));
				$contract->set_service_id(phpgw::get_var('service_id'));
				$contract->set_responsibility_id(phpgw::get_var('responsibility_id'));
				$contract->set_reference(phpgw::get_var('reference'));
				$contract->set_invoice_header(phpgw::get_var('invoice_header'));
				$contract->set_account_in(phpgw::get_var('account_in'));

				$contract->set_account_out(phpgw::get_var('account_out'));

				$contract->set_project_id(phpgw::get_var('project_id'));
				$contract->set_due_date(phpgwapi_datetime::date_to_timestamp(phpgw::get_var('due_date')));
				$contract->set_contract_type_id(phpgw::get_var('contract_type'));
				$old_rented_area = $contract->get_rented_area();
				$new_rented_area = phpgw::get_var('rented_area');
				$new_rented_area = str_replace(',','.',$new_rented_area);
				$validated_numeric = false;
				if(!isset($new_rented_area) || $new_rented_area == ''){
					$new_rented_area = 0;
				}
				if($old_rented_area != $new_rented_area){
					$update_price_items = true;
				}
				$contract->set_rented_area($new_rented_area);
				$contract->set_adjustment_interval(phpgw::get_var('adjustment_interval'));
				$contract->set_adjustment_share(phpgw::get_var('adjustment_share'));
				$contract->set_adjustable(phpgw::get_var('adjustable') == 'on' ? true : false);
				$contract->set_publish_comment(phpgw::get_var('publish_comment') == 'on' ? true : false);
				$validated_numeric = $contract->validate_numeric();

				if($validated_numeric)
				{
					$so_contract = rental_socontract::get_instance();
					$db_contract = $so_contract->get_db();
					$db_contract->transaction_begin();
					if($so_contract->store($contract))
					{
						if($update_price_items){
							$success = $so_contract->update_price_items($contract->get_id(), $new_rented_area);
							if($success){
								$db_contract->transaction_commit();
								$message = lang('messages_saved_form');
								$contract_id = $contract->get_id();
							}
							else{
								$db_contract->transaction_abort();
								$error = lang('messages_form_error');
							}
						}
						else if($add_default_price_items)
						{
							$so_price_item = rental_soprice_item::get_instance();
							//get default price items for location_id
							$default_price_items = $so_contract->get_default_price_items($contract->get_location_id());

							//add price_items to contract
							foreach($default_price_items as $price_item_id)
							{
								$so_price_item->add_price_item($contract->get_id(), $price_item_id);
							}
							$db_contract->transaction_commit();
							$message = lang('messages_saved_form');
							$contract_id = $contract->get_id();
						}
						else{
							$db_contract->transaction_commit();
							$message = lang('messages_saved_form');
							$contract_id = $contract->get_id();
						}
					}
					else
					{
						$db_contract->transaction_abort();
						$error = lang('messages_form_error');
					}
				}
				else{
					$error = $contract->get_validation_errors();
					//return $this->viewedit(true, $contract_id, $contract, $location_id,$notification, $message, $error);
				}
			}
			
			if (!empty($error))
			{
				phpgwapi_cache::message_set($error, 'error'); 
			}
			if (!empty($message))
			{
				phpgwapi_cache::message_set($message, 'message'); 
			}
			$this->edit(array('contract_id'=>$contract_id));	
		}
		
		/**
		 * Common function for viewing or editing a contract
		 *
		 * @param $editable whether or not the contract should be editable in the view
		 * @param $contract_id the id of the contract to show
		 */
		public function viewedit($editable, $contract_id, $contract = null, $location_id = null, $notification = null, string $message = null, string $error = null)
		{				
			$cancel_link = self::link(array('menuaction' => 'rental.uicontract.index', 'populate_form' => 'yes'));
			$adjustment_id = (int)phpgw::get_var('adjustment_id');
			if($adjustment_id){
				$cancel_link = self::link(array('menuaction' => 'rental.uiadjustment.show_affected_contracts','id' => $adjustment_id));
				$cancel_text = 'contract_regulation_back';
			}
			
			if (isset($contract_id) && $contract_id > 0) {
				if($contract == null){
					$contract = rental_socontract::get_instance()->get_single($contract_id);
				}
				if ($contract) {
					
					if($editable && !$contract->has_permission(PHPGW_ACL_EDIT))
					{
						$editable = false;
						$error .= '<br/>'.lang('permission_denied_edit_contract');
					}
					
					if(!$editable && !$contract->has_permission(PHPGW_ACL_READ))
					{
						$this->render('permission_denied.php',array('error' => lang('permission_denied_view_contract')));
						return;
					}
					
					$data = array
					(
						'contract' 	=> $contract,
						'notification' => $notification,
						'editable' => $editable,
						'message' => isset($message) ? $message : phpgw::get_var('message'),
						'error' => isset($error) ? $error : phpgw::get_var('error'),
						'cancel_link' => $cancel_link,
						'cancel_text' => $cancel_text
					);
					$contract->check_consistency();
					$this->render('contract.php', $data);
				}
			}
			else
			{
				if($this->isAdministrator() || $this->isExecutiveOfficer()){
					$created = strtotime('now');
					$created_by = $GLOBALS['phpgw_info']['user']['account_id'];
					if(!isset($contract)){
						$contract = new rental_contract();
						$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
						$contract->set_location_id($location_id);
						$contract->set_contract_type_title($fields[$location_id]);
					}
					if ($contract) {
						$data = array
						(
							'contract' 	=> $contract,
							'notification' => $notification,
							'created' => $created,
							'created_by' => $created_by,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'cancel_link' => $cancel_link,
							'cancel_text' => $cancel_text
						);
						$this->render('contract.php', $data);
					}
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('permission_denied_new_contract')));
					return;	
				}
			}
		}

		/**
		 * View a contract
		 */
		public function view()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('view');
			$contract_id = (int)phpgw::get_var('id');
			return $this->viewedit(false, $contract_id);
		}

		/**
		 * Edit a contract
		 */
		public function edit($values = array(), $mode = 'edit')
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
			
			$contract_id = (int)phpgw::get_var('id');
			$location_id = (int)phpgw::get_var('location_id');
			
			$message = null;
			$error = null;
			
			/*$config	= CreateObject('phpgwapi.config','rental');
			$config->read();*/
	
			if ($values['contract_id'])
			{
				$contract_id = $values['contract_id'];
			}
				
			if (!empty($contract_id)) 
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
				$created = date($this->dateFormat, $contract->get_last_updated());  
				$created_by = $contract->get_last_edited_by();
					
				if ($contract) {
					
					if($editable && !$contract->has_permission(PHPGW_ACL_EDIT))
					{
						$editable = false;
						$error .= '<br/>'.lang('permission_denied_edit_contract');
					}
					
					if(!$editable && !$contract->has_permission(PHPGW_ACL_READ))
					{
						$this->render('permission_denied.php',array('error' => lang('permission_denied_view_contract')));
						return;
					}
				}
			}
			else
			{
				if($this->isAdministrator() || $this->isExecutiveOfficer())
				{
					$created = date($this->dateFormat, strtotime('now'));
					$created_by = $GLOBALS['phpgw']->accounts->id2name($GLOBALS['phpgw_info']['user']['account_id']);

					$contract = new rental_contract();
					$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
					$contract->set_location_id($location_id);
					$contract->set_contract_type_title($fields[$location_id]);
				}
				else
				{
					$this->render('permission_denied.php',array('error' => lang('permission_denied_new_contract')));
					return;	
				}
			}
			
			$GLOBALS['phpgw']->jqcal->add_listener('date_start');
			$GLOBALS['phpgw']->jqcal->add_listener('date_end');
			$GLOBALS['phpgw']->jqcal->add_listener('due_date');
			$GLOBALS['phpgw']->jqcal->add_listener('billing_start_date');
			$GLOBALS['phpgw']->jqcal->add_listener('billing_end_date');
			
			$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($contract->get_location_id());
			$current_contract_type_id = $contract->get_contract_type_id();
			if( strcmp($responsibility_area, "contract_type_eksternleie") != 0 )
			{ 
				$contract_type_options[] = array('id'=>'', 'name'=>lang('Ingen type'), 'selected'=>0);
			}
			$contract_types = rental_socontract::get_instance()->get_contract_types($contract->get_location_id());		
			foreach($contract_types as $contract_type_id => $contract_type_label)
			{
				$selected = ($contract_type_id == $current_contract_type_id) ? 1 : 0;
				$contract_type_options[] = array('id'=>$contract_type_id, 'name'=>lang($contract_type_label), 'selected'=>$selected);
			}
		
			if(!$executive_officer = $contract->get_executive_officer_id())
			{
				$executive_officer = $GLOBALS['phpgw_info']['user']['account_id'];
			}
			$location_name = $contract->get_field_of_responsibility_name();
			$accounts = $GLOBALS['phpgw']->acl->get_user_list_right(PHPGW_ACL_ADD, $location_name, 'rental');
			$executive_officer_options[] = array('id'=>'', 'name'=>lang('nobody'), 'selected'=>0);
			foreach($accounts as $account)
			{
				$selected = ($account['account_id'] == $executive_officer) ? 1 : 0;
				$executive_officer_options[] = array('id'=>$account['account_id'], 'name'=>$GLOBALS['phpgw']->accounts->get($account['account_id'])->__toString(), 'selected'=>$selected);
			}
			
			$start_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_start_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_start_date()) : '';
			$end_date = ($contract->get_contract_date() && $contract->get_contract_date()->has_end_date()) ? date($this->dateFormat, $contract->get_contract_date()->get_end_date()) : '';
			$due_date = ($contract->get_due_date()) ? date($this->dateFormat, $contract->get_due_date()) : '';
			
			
			if(!$current_term_id = $contract->get_term_id())
			{
				$current_term_id = $this->config->config_data['default_billing_term'];
			}
			$billing_terms = rental_sobilling::get_instance()->get_billing_terms();
			$billing_term_options = array();
			foreach($billing_terms as $term_id => $term_title)
			{
				$selected = ($term_id == $current_term_id) ? 1 : 0;
				$billing_term_options[] = array('id'=>$term_id, 'name'=>lang($term_title), 'selected'=>$selected);
			}
			
			$billing_start_date = ($contract->get_billing_start_date()) ? date($this->dateFormat, $contract->get_billing_start_date()) : '';
			$billing_end_date = ($contract->get_billing_end_date()) ? date($this->dateFormat, $contract->get_billing_end_date()) : '';
			
			$cur_responsibility_id = $contract->get_responsibility_id();
			$contract_responsibility_arr = $contract->get_responsibility_arr($cur_responsibility_id);
			$responsibility_options = array();
			if($contract_responsibility_arr)
			{
				foreach($contract_responsibility_arr as $contract_responsibility)
				{
					$selected = ($contract_responsibility['selected'] == 1) ? 1 : 0;
					$responsibility_options[] = array('id'=>$contract_responsibility['id'], 'name'=>$contract_responsibility['name'], 'selected'=>$selected);
				}
			} 		
			
			if(empty($contract->get_id()))
			{
				$account_in = rental_socontract::get_instance()->get_default_account($contract->get_location_id(), true);
				$account_out = rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false);
				$project_id = rental_socontract::get_instance()->get_default_project_number($contract->get_location_id(), false);
			}
			else
			{
				$account_in = $contract->get_account_in(); 
				$account_out = $contract->get_account_out();
				$project_id = $contract->get_project_id() ;
			}
			
			$current_security_type = $contract->get_security_type();
			$security_options[] = array('id'=>'', 'name'=>lang('nobody'), 'selected'=>0);
			$security_options[] = array('id'=>rental_contract::SECURITY_TYPE_BANK_GUARANTEE, 'name'=>lang('bank_guarantee'), 'selected'=>(($current_security_type == rental_contract::SECURITY_TYPE_BANK_GUARANTEE) ? 1 : 0));
			$security_options[] = array('id'=>rental_contract::SECURITY_TYPE_DEPOSIT, 'name'=>lang('deposit'), 'selected'=>(($current_security_type == rental_contract::SECURITY_TYPE_DEPOSIT) ? 1 : 0));
			$security_options[] = array('id'=>rental_contract::SECURITY_TYPE_ADVANCE, 'name'=>lang('advance'), 'selected'=>(($current_security_type == rental_contract::SECURITY_TYPE_ADVANCE) ? 1 : 0));
			$security_options[] = array('id'=>rental_contract::SECURITY_TYPE_OTHER_GUARANTEE, 'name'=>lang('other_guarantee'), 'selected'=>(($current_security_type == rental_contract::SECURITY_TYPE_OTHER_GUARANTEE) ? 1 : 0));

			$current_interval = $contract->get_adjustment_interval();
			$adjustment_interval_options[] = array('id'=>'1', 'name'=>'1 '.lang('year'), 'selected'=>(($current_interval == '1') ? 1 : 0));
			$adjustment_interval_options[] = array('id'=>'2', 'name'=>'2 '.lang('year'), 'selected'=>(($current_interval == '2') ? 1 : 0));
			$adjustment_interval_options[] = array('id'=>'10', 'name'=>'10 '.lang('year'), 'selected'=>(($current_interval == '10') ? 1 : 0));
			
			$current_share = $contract->get_adjustment_share();
			$adjustment_share_options[] = array('id'=>'100', 'name'=>'100%', 'selected'=>(($current_share == '100') ? 1 : 0));
			$adjustment_share_options[] = array('id'=>'90', 'name'=>'90%', 'selected'=>(($current_share == '90') ? 1 : 0));
			$adjustment_share_options[] = array('id'=>'80', 'name'=>'80%', 'selected'=>(($current_share == '80') ? 1 : 0));
			$adjustment_share_options[] = array('id'=>'67', 'name'=>'67%', 'selected'=>(($current_share == '67') ? 1 : 0));
			
			$link_save = array
				(
					'menuaction'	=> 'rental.uicontract.save'
				);

			$link_index = array
				(
					'menuaction'	=> 'rental.uicontract.index',
				);
			
			$tabs = array();
			$tabs['details']	= array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';
			
			$datatable_def = array();
		
			if($contract_id)
			{
				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_0',
					'requestUrl'	=> json_encode(self::link(array('menuaction'=>'rental.uicontract.get_total_price', 'contract_id'=>$contract_id,  'phpgw_return_as'=>'json'))),
					'ColumnDefs'	=> array(
								array('key'=>'total_price', 'label'=>lang('total_price'), 'sortable'=>false),
								array('key'=>'area', 'label'=>lang('area'), 'sortable'=>false),
								array('key'=>'price_per_unit', 'label'=>lang('price_per_unit'), 'sortable'=>false)					
					),
					'config'		=> array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);
				
				$tabs['composite']		= array('label' => lang('Composite'), 'link' => '#composite', 'function' => 'get_composite_data()');
				$tabs['parties']		= array('label' => lang('Parties'), 'link' => '#parties');
				$tabs['price']			= array('label' => lang('Price'), 'link' => '#price');
				$tabs['invoice']		= array('label' => lang('Invoice'), 'link' => '#invoice');
				$tabs['documents']		= array('label' => lang('Documents'), 'link' => '#documents');
				$tabs['notifications']	= array('label' => lang('Notifications'), 'link' => '#notifications');
				
				$uicols_composite = rental_socomposite::get_instance()->get_uicols();
				$composite_def = array();
				$uicols_count	= count($uicols_composite['descr']);
				for ($i=0;$i<$uicols_count;$i++)
				{
					if ($uicols_composite['input_type'][$i]!='hidden')
					{
						$composite_def[$i]['key'] 	= $uicols_composite['name'][$i];
						$composite_def[$i]['label']	= $uicols_composite['descr'][$i];
					}
				}

				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_1',
					'requestUrl'	=> "''",
					'data'			=> json_encode(array()),
					'ColumnDefs'	=> $composite_def,
					'config'		=> array(
						array('disableFilter'	=> true)
					)
				);
				
				$datatable_def[] = array
				(
					'container'		=> 'datatable-container_2',
					'requestUrl'	=> "''",
					'data'			=> json_encode(array()),
					'ColumnDefs'	=> $composite_def,
					'config'		=> array(
						array('disableFilter'	=> true)
					)
				);
				
				$link_included_composites = json_encode(self::link(array('menuaction'=>'rental.uicomposite.query', 'type'=>'included_composites', 'contract_id'=>$contract->get_id(), 'phpgw_return_as'=>'json')));
				$link_not_included_composites = json_encode(self::link(array('menuaction'=>'rental.uicomposite.query', 'type'=>'not_included_composites', 'contract_id'=>$contract->get_id(), 'phpgw_return_as'=>'json')));
			}
			
			$data = array
				(
					'datatable_def'					=> $datatable_def,
					'form_action'					=> $GLOBALS['phpgw']->link('/index.php',$link_save),
					'cancel_url'					=> $GLOBALS['phpgw']->link('/index.php',$link_index),
					'lang_save'						=> lang('save'),
					'lang_cancel'					=> lang('cancel'),
				
					'lang_contract_number'			=> lang('contract_number'),
					'lang_parties'					=> lang('parties'),
					'lang_last_updated'				=> lang('last_updated'),
					'lang_name'						=> lang('name'),
					'lang_composite'				=> lang('composite'),
				
					'lang_field_of_responsibility'	=> lang('field_of_responsibility'),
					'lang_contract_type'			=> lang('contract_type'),
					'lang_executive_officer'		=> lang('executive_officer'),
					'lang_date_start'				=> lang('date_start'),
					'lang_date_end'					=> lang('date_end'),
					'lang_due_date'					=> lang('due_date'),
					'lang_invoice_header'			=> lang('invoice_header'),
					'lang_billing_term'				=> lang('billing_term'),
					'lang_billing_start'			=> lang('billing_start'),
					'lang_billing_end'				=> lang('billing_end'),
					'lang_reference'				=> lang('reference'),
					'lang_responsibility'			=> lang('responsibility'),
					'lang_service'					=> lang('service'),
					'lang_account_in'				=> lang('account_in'),
					'lang_account_out'				=> lang('account_out'),
					'lang_project_id'				=> lang('project_id'),
					'lang_security'					=> lang('security'),
					'lang_security_amount'			=> lang('security_amount'),
					'lang_rented_area'				=> lang('rented_area'),
					'lang_rented_area'				=> lang('rented_area'),
					'lang_adjustable'				=> lang('adjustable'),
					'lang_adjustment_interval'		=> lang('adjustment_interval'),
					'lang_adjustment_share'			=> lang('adjustment_share'),
					'lang_adjustment_year'			=> lang('adjustment_year'),
					'lang_comment'					=> lang('comment'),
					'lang_publish_comment'			=> lang('publish_comment'),

					'value_contract_number'			=> $contract->get_old_contract_id(),
					'value_parties'					=> $contract->get_party_name_as_list(),
					'value_last_updated'			=> $created,
					'value_name'					=> $created_by,
					'value_composite'				=> $contract->get_composite_name_as_list(),
				
					'value_field_of_responsibility'	=> lang($contract->get_contract_type_title()),
					'list_contract_type'			=> array('options' => $contract_type_options),
					'list_executive_officer'		=> array('options' => $executive_officer_options),
					'value_date_start'				=> $start_date,
					'value_date_end'				=> $end_date,
					'value_due_date'				=> $due_date,
					'value_invoice_header'			=> $contract->get_invoice_header(),
					'list_billing_term'				=> array('options' => $billing_term_options),
					'value_billing_start'			=> $billing_start_date,
					'value_billing_end'				=> $billing_end_date,
					'value_reference'				=> $contract->get_reference(),
					'list_responsibility'			=> array('options' => $responsibility_options),
					'value_responsibility_id'		=> $cur_responsibility_id,
					'value_service'					=> $contract->get_service_id(),
					'value_account_in'				=> $account_in,
					'value_account_out'				=> $account_out, 
					'value_project_id'				=> $project_id,
					'list_security'					=> array('options' => $security_options),
					'security_amount_simbol'		=> $GLOBALS['phpgw_info']['user']['preferences']['common']['currency'],
					'value_security_amount'			=> $contract->get_security_amount(), 
					'value_rented_area'				=> $contract->get_rented_area(), 
					'rented_area_simbol'			=> $this->area_suffix,			
					'is_adjustable'					=> $contract->is_adjustable(),
				
					'list_adjustment_interval'		=> array('options' => $adjustment_interval_options),
					'list_adjustment_share'			=> array('options' => $adjustment_share_options),
					'value_adjustment_year'			=> $contract->get_adjustment_year(),
					'value_comment'					=> $contract->get_comment(),
					'value_publish_comment'			=> $contract->get_publish_comment(),
				
					'location_id'					=> $contract->get_location_id(),
					'contract_id'					=> $contract_id,
					'mode'							=> 'edit',
					'link_included_composites'		=> $link_included_composites,
					'link_not_included_composites'	=> $link_not_included_composites,
				
					'tabs'							=> phpgwapi_jquery::tabview_generate($tabs, $active_tab)
				);

			//$appname	=  $this->location_info['name'];

			//$GLOBALS['phpgw_info']['flags']['app_header'] = $GLOBALS['phpgw']->translation->translate($this->location_info['acl_app'], array(), false, $this->location_info['acl_app']) . "::{$appname}::{$function_msg}";
			self::add_javascript('rental', 'rental', 'contract.edit.js');
			self::render_template_xsl(array('contract', 'datatable_inline'), array('edit' => $data));
			
			//return $this->viewedit(true, $contract_id, null, $location_id, array(), $message, $error);
		}

		/**
		 * Create a new empty contract
		 */
		public function add()
		{
			$location_id = phpgw::get_var('location_id');
			if(isset($location_id) && $location_id > 0)
			{
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'location_id' => $location_id));
			}
		}

		/**
		 * Create a new contract tied to the composite provided in the composite_id parameter
		 */
		public function add_from_composite()
		{
			$contract = new rental_contract();
			$contract->set_location_id(phpgw::get_var('responsibility_id'));
			$contract->set_account_in(rental_socontract::get_instance()->get_default_account($contract->get_location_id(), true));
			$contract->set_account_out(rental_socontract::get_instance()->get_default_account($contract->get_location_id(), false));
			$contract->set_executive_officer_id($GLOBALS['phpgw_info']['user']['account_id']);

			/*$config	= CreateObject('phpgwapi.config','rental');
			$config->read();*/
			$default_billing_term = $this->config->config_data['default_billing_term'];

			$contract->set_term_id($default_billing_term);

			$units = rental_socomposite::get_instance()->get_single(phpgw::get_var('id'))->get_units();
			$location_code = $units[0]->get_location()->get_location_code();

			$args = array
			(
				'acl_location'	=> '.contract',
				'location_code' => $location_code,
				'contract'		=> &$contract
			);

			$hook_helper = CreateObject('rental.hook_helper');
			$hook_helper->add_contract_from_composite($args);

	//		_debug_array($contract); die();

			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$so_contract = rental_socontract::get_instance();
				$db_contract = $so_contract->get_db();
				$db_contract->transaction_begin();
				if($so_contract->store($contract))
				{
					// Add standard price items to contract
					if($contract->get_location_id() && ($this->isExecutiveOfficer() || $this->isAdministrator()))
					{
						$so_price_item = rental_soprice_item::get_instance();
						//get default price items for location_id
						$default_price_items = $so_contract->get_default_price_items($contract->get_location_id());

						foreach($default_price_items as $price_item_id)
						{
							$so_price_item->add_price_item($contract->get_id(), $price_item_id);
						}
					}
					// Add that composite to the new contract
					$success = $so_contract->add_composite($contract->get_id(), phpgw::get_var('id'));
					if($success)
					{
						$db_contract->transaction_commit();
						$comp_name = rental_socomposite::get_instance()->get_single(phpgw::get_var('id'))->get_name();
						$message = lang('messages_new_contract_from_composite').' '.$comp_name;
					
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message));
					}
					else{
						$db_contract->transaction_abort();
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_form_error')));
					}
				}
				else
				{
					$db_contract->transaction_abort();
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_form_error')));
				}
			}
		
			// If no executive officer 
			$this->render('permission_denied.php',array('error' => lang('permission_denied_new_contract')));
		}
		
		/**
		 * Create a new contract based on an existing contract
		 */
		public function copy_contract()
		{
			$adjustment_id = (int)phpgw::get_var('adjustment_id');
			
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single(phpgw::get_var('id'));
			$old_contract_old_id = $contract->get_old_contract_id();
			$db_contract = $so_contract->get_db();
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$db_contract->transaction_begin();
				//reset id's and contract dates
				$contract->set_id(null);
				$contract->set_old_contract_id(null);
				$contract->set_contract_date(null);
				$contract->set_due_date(null);
				$contract->set_billing_start_date(null);
				$contract->set_billing_end_date(null);
				if($so_contract->store($contract))
				{
					// copy the contract
					$success = $so_contract->copy_contract($contract->get_id(), phpgw::get_var('id'));
					if($success){
						$db_contract->transaction_commit();
						$message = lang(messages_new_contract_copied).' '.$old_contract_old_id;
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message, 'adjustment_id' => $adjustment_id));
					}
					else{
						$db_contract->transaction_abort();
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_form_error'),'adjustment_id' => $adjustment_id));
					}
				}
				else
				{
					$db_contract->transaction_abort();
					$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_form_error'),'adjustment_id' => $adjustment_id));
				}
			}
		
			// If no executive officer 
			$this->render('permission_denied.php',array('error' => lang('permission_denied_new_contract')));
		}

		/**
		 * Public function. Add a party to a contract
		 * @param HTTP::contract_id	the contract id
		 * @param HTTP::party_id the party id
		 * @return true if successful, false otherwise
		 */
		public function add_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->add_party($contract_id,$party_id);
			}
			return false;
		}

		/**
		 * Public function. Remove a party from a contract
		 * @param HTTP::contract_id the contract id
		 * @param HTTP::party_id the party id
		 * @return true if successful, false otherwise
		 */
		public function remove_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->remove_party($contract_id, $party_id);
			}
			return false;
		}

		/**
		 * Public function. Set the payer on a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::party_id	the party id
		 * @return true if successful, false otherwise
		 */
		public function set_payer(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->set_payer($contract_id,$party_id);
			}
			return false;
		}

		/**
		 * Public function. Add a composite to a contract.
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::composite_id	the composite id
		 * @return boolean true if successful, false otherwise
		 */
		public function add_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->add_composite($contract_id, $composite_id);
			}
			return false;
		}

		/**
		 * Public function. Remove a composite from a contract.
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::composite_id	the composite id
		 * @return boolean true if successful, false otherwise
		 */
		public function remove_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if(isset($contract) && $contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->remove_composite($contract_id, $composite_id);
			}
			return false;
		}

		/**
		 * Public function. Add a price item to a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return boolean true if successful, false otherwise
		 */
		public function add_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$factor =  phpgw::get_var('factor','float');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return rental_soprice_item::get_instance()->add_price_item($contract_id, $price_item_id, $factor);
			}
			return false;
		}

		/**
		 * Public function. Remove a price item from a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return boolean true if successful, false otherwise
		 */
		public function remove_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return rental_soprice_item::get_instance()->remove_price_item($contract_id, $price_item_id);
			}
			return false;
		}

		/**
		 * Public function. Reset a price item on a contract
		 * @param	HTTP::contract_id	the contract id
		 * @param	HTTP::price_item_id	the price item id
		 * @return boolean true if successful, false otherwise
		 */
		public function reset_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return rental_soprice_item::get_instance()->reset_contract_price_item($price_item_id);
			}
			return false;
		}
		
		public function get_total_price()
		{
			$so_contract = rental_socontract::get_instance();
			$so_contract_price_item = rental_socontract_price_item::get_instance();
			
			$contract_id = (int)phpgw::get_var('contract_id');
			$total_price =  $so_contract_price_item->get_total_price($contract_id);
			$contract = $so_contract->get_single($contract_id);
			$area = $contract->get_rented_area();
			
			if(isset($area) && $area > 0)
			{
				$price_per_unit = $total_price / $area;
				$price_per_unit = number_format($price_per_unit, $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator);
			}
			
			$total_price = number_format($total_price, $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator);
			$area = number_format($area, $this->decimalPlaces, $this->decimalSeparator, $this->thousandsSeparator);
						
			$result_array[] = array('total_price' => $total_price.' '.$this->currency_suffix, 'area' => $area.' Kvm', 'price_per_unit' => $price_per_unit.' '.$this->currency_suffix);
			
			$result_data    =   array('results' =>  $result_array);
			$result_data['total_records']	= 1;
			$result_data['draw']    = 1;

			return $this->jquery_results($result_data);
		}
		
		public function get_max_area(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$total_price =  rental_socontract_price_item::get_instance()->get_max_area($contract_id);
			$result_array = array('max_area' => $max_area);
			$result_data = array('results' => $result_array, 'total_records' => 1);
			return $this->yui_results($result_data, 'total_records', 'results');
		}
		

		/**
		 * 
		 * Public function scans the contract template directory for pdf contract templates 
		 */
		public function get_pdf_templates(){
			$get_template_config= true;
			$files = scandir('rental/templates/base/pdf/');			
			foreach ($files as $file){
				$ending = substr($file, -3, 3);
				if($ending=='php'){
					include 'rental/templates/base/pdf/'.$file;
					$template_files = array($template_name,$file);
					$this->pdf_templates[] = $template_files;
				}
			}	
		}
	}
?>
