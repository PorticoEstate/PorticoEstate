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

	class rental_uicontract extends rental_uicommon
	{
		private $pdf_templates = array();
		
		public $public_functions = array
		(
			'add'					=> true,
			'add_from_composite'	=> true,
			'copy_contract'			=> true,
			'edit'					=> true,
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
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;
			
			$price_items_only = phpgw::get_var('price_items'); //should only export contract price items
			$exp_param 	= phpgw::get_var('export');
			$export = false;
			if(isset($exp_param)){
				$export=true;
				$num_of_objects = null;
			}
			
			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'contracts_for_adjustment':
					$adjustment_id = (int)phpgw::get_var('id');
					$adjustment = rental_soadjustment::get_instance()->get_single($adjustment_id);
					$filters = array('contract_type' => $adjustment->get_responsibility_id(), 'adjustment_interval' => $adjustment->get_interval(), 'adjustment_year' => $adjustment->get_year(), 'adjustment_is_executed' => $adjustment->is_executed());
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
					$filters = array('composite_id' => phpgw::get_var('composite_id'),'contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'), 'status_date_hidden' => phpgw::get_var('date_status_hidden'));
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
					$filters = array('contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'), 'status_date_hidden' => phpgw::get_var('date_status_hidden'));
			}
			if($type != 'get_contract_warnings'){
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
				$config	= CreateObject('phpgwapi.config','rental');
				
				//Check if user has access to Catch module
				$access = $this->acl->check('.',PHPGW_ACL_READ,'catch');
				if($access)
				{
					$config->read();
					$entity_id_in = $config->config_data['entity_config_move_in'];
					$entity_id_out = $config->config_data['entity_config_move_out'];
					$category_id_in = $config->config_data['category_config_move_in'];	
					$category_id_out = $config->config_data['category_config_move_out'];		
				}
				
				array_walk($rows, array($this, 'add_actions'), array($type,$ids,$adjustment_id,$entity_id_in,$entity_id_out,$category_id_in,$category_id_out));
			}
			//var_dump("Usage " .memory_get_usage() . " bytes after menu");
			
			
			//Build a YUI result from the data
			$result_data = array('results' => $rows, 'total_records' => $result_count);
			return $this->yui_results($result_data, 'total_records', 'results');
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
			$value['ajax'] = array();
			$value['actions'] = array();
			$value['labels'] = array();

			$type = $params[0];
			$ids = $params[1];
			$adjustment_id = $params[2];
			$entity_id_in = $params[3];
			$entity_id_out = $params[4];
			$category_id_in = $params[5];
			$category_id_out = $params[6];
			
			
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
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
						$value['labels'][] = lang('edit_contract');
					}
					else
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
						$value['labels'][] = lang('show');
					}
					break;
				case 'contracts_for_adjustment':
					if(!isset($ids) || count($ids) > 0)
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit',
																					'id' => $value['id'], 
																					'initial_load' => 'no',
																					'adjustment_id' => $adjustment_id)));
						$value['labels'][] = lang('edit');
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract',
																									'id' => $value['id'],
																									'adjustment_id' => $adjustment_id)));
						$value['labels'][] = lang('copy');
					}
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view',
																									'id' => $value['id'], 
																									'initial_load' => 'no',
																									'adjustment_id' => $adjustment_id)));
					$value['labels'][] = lang('show');
			
					break;
				default:
					if(!isset($ids) || count($ids) > 0)
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'], 'initial_load' => 'no')));
						$value['labels'][] = lang('edit');
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.copy_contract', 'id' => $value['id'])));
						$value['labels'][] = lang('copy');
					}
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'], 'initial_load' => 'no')));
					$value['labels'][] = lang('show');
					$temlate_counter = 0;
					foreach ($this->pdf_templates as $pdf_template){
						
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uimakepdf.view', 'id' => $value['id'], 'pdf_template' => $temlate_counter )));
						$value['labels'][] = lang('make_pdf').": ". $pdf_template[0];
						$temlate_counter++;
					}
					//http://portico/pe/index.php?menuaction=property.uientity.index&second_display=1&entity_id=3&cat_id=1&type=catch&district_id=0&query=Tes&start_date=&end_date=&click_history=06014d0abc7293bfb52ff5d1c04f3cb8&phpgw_return_as=json
					if(isset($entity_id_in) && $entity_id_in != '' && isset($category_id_in) && $category_id_in != '')
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_in, 'cat_id' => $category_id_in,'query' => $value['old_contract_id'], 'type' => 'catch')));
						$value['labels'][] = lang('show_move_in_reports');
					}

					if(isset($entity_id_out) && $entity_id_out != '' && isset($category_id_out) && $category_id_out != '')
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'property.uientity.index', 'entity_id' => $entity_id_out, 'cat_id' => $category_id_out,'query' => $value['old_contract_id'], 'type' => 'catch')));
						$value['labels'][] = lang('show_move_out_reports');
					}
				}
		}

		/**
		 * View a list of all contracts
		 */
		public function index()
		{

			$search_for = phpgw::get_var('search_for');
			if($search_for)
			{
				phpgwapi_cache::session_set('rental', 'contract_query', $search_for);
				phpgwapi_cache::session_set('rental', 'contract_search_type', phpgw::get_var('search_type'));
				phpgwapi_cache::session_set('rental', 'contract_status', phpgw::get_var('contract_status'));
				phpgwapi_cache::session_set('rental', 'contract_status_date', phpgw::get_var('date_status'));
				phpgwapi_cache::session_set('rental', 'contract_type', phpgw::get_var('contract_type'));
			}
			$this->render('contract_list.php');
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
		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
			$contract_id = (int)phpgw::get_var('id');
			$location_id = (int)phpgw::get_var('location_id');
			$update_price_items = false;
			
			$message = null;
			$error = null;
			$add_default_price_items = false;
			
			if(isset($_POST['save_contract']))
			{
				if(isset($contract_id) && $contract_id > 0)
				{
					$contract = rental_socontract::get_instance()->get_single($contract_id);
					
					// Gets responsibility area from db (ex: eksternleie, internleie)
					$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($contract->get_location_id());
					
					// Redirect with error message if responsibility area is eksternleie and contract type not set
					if( !is_numeric( phpgw::get_var('contract_type') ) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0) ){
						$error = lang('billing_removed_external_contract');
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message, 'error' => $error));	
					}
					
					if(!$contract->has_permission(PHPGW_ACL_EDIT))
					{
						unset($contract);
						$this->render('permission_denied.php',array('error' => lang('permission_denied_edit_contract')));
					}
				}
				else
				{
					// Gets responsibility area from db (ex: eksternleie, internleie) 
					$responsibility_area = rental_socontract::get_instance()->get_responsibility_title($location_id);
					
					// Redirect with error message if responsibility area is eksternleie and contract type not set
					if( !is_numeric( phpgw::get_var('contract_type') ) && (strcmp($responsibility_area, "contract_type_eksternleie") == 0) ){
						$error = lang('billing_removed_external_contract');
						
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'location_id' => $location_id, 'message' => $message, 'error' => $error));
					}
				
					if(isset($location_id) && ($this->isExecutiveOfficer() || $this->isAdministrator())){
						$contract = new rental_contract();
						$fields = rental_socontract::get_instance()->get_fields_of_responsibility();
						$contract->set_location_id($location_id);
						$contract->set_contract_type_title($fields[$location_id]);
						$add_default_price_items = true;
					}
				}
				
				$date_start =  strtotime(phpgw::get_var('date_start_hidden'));
				$date_end =  strtotime(phpgw::get_var('date_end_hidden'));
				
				if(isset($contract)){ 
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
					$contract->set_billing_start_date(strtotime(phpgw::get_var('billing_start_date_hidden')));
					$contract->set_service_id(phpgw::get_var('service_id'));
					$contract->set_responsibility_id(phpgw::get_var('responsibility_id'));
					$contract->set_reference(phpgw::get_var('reference'));
					$contract->set_invoice_header(phpgw::get_var('invoice_header'));
					$contract->set_account_in(phpgw::get_var('account_in'));
					
					/*
					if($contract->get_contract_type_id() != phpgw::get_var('contract_type'))
					{
						// New contract type id set, retrieve correct account out
						$type_id = phpgw::get_var('contract_type');
						if(isset($type_id) && $type_is != ''){
							$account = rental_socontract::get_instance()->get_contract_type_account($type_id);
							$contract->set_account_out($account);
						}
						else
						{
							$contract->set_account_out(phpgw::get_var('account_out'));
						}
					}
					else
					{*/
						$contract->set_account_out(phpgw::get_var('account_out'));
					//}
					
					$contract->set_project_id(phpgw::get_var('project_id'));
					$contract->set_due_date(strtotime(phpgw::get_var('due_date_hidden')));
					$contract->set_contract_type_id(phpgw::get_var('contract_type'));
					$old_rented_area = $contract->get_rented_area();
					$new_rented_area = phpgw::get_var('rented_area');
					$new_rented_area = str_replace(',','.',$new_rented_area);
					$validated_numeric=false;
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
					
					if($validated_numeric){
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
						return $this->viewedit(true, $contract_id, $contract, $location_id,$notification, $message, $error);
					}
				}
				
				$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => $message, 'error' => $error));		
			}
			else if(isset($_POST['add_notification']))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
				if($contract->has_permission(PHPGW_ACL_EDIT))
				{
					$account_id = phpgw::get_var('notification_target');
					$location_id = phpgw::get_var('notification_location');
					$date = phpgw::get_var('date_notification_hidden');
					if($date)
					{
						$date = strtotime($date);
					}
					$notification = new rental_notification(-1, $account_id, $location_id, $contract_id, $date, phpgw::get_var('notification_message'), phpgw::get_var('notification_recurrence'));
					if (rental_sonotification::get_instance()->store($notification))
					{
						$message = lang('messages_saved_form');
						$notification = null; // We don't want to display the date/message when it was sucessfully stored.
					}
					else
					{
	
						$error = lang('messages_form_error');
					}
				}
				else
				{
					$error = lang('permission_denied_edit_contract');
				}
			}
			return $this->viewedit(true, $contract_id, null, $location_id,$notification, $message, $error);
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
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$so_contract = rental_socontract::get_instance();
				$db_contract = $so_contract->get_db();
				$db_contract->transaction_begin();
				if($so_contract->store($contract))
				{
					// Add that composite to the new contract
					$success = $so_contract->add_composite($contract->get_id(), phpgw::get_var('id'));
					if($success){
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
			$so_contract = rental_socontract::get_instance();
			$contract = $so_contract->get_single($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return rental_soprice_item::get_instance()->add_price_item($contract_id, $price_item_id);
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
		
		public function get_total_price(){
			$so_contract = rental_socontract::get_instance();
			$so_contract_price_item = rental_socontract_price_item::get_instance();
			
			$contract_id = (int)phpgw::get_var('contract_id');
			$total_price =  $so_contract_price_item->get_total_price($contract_id);
			$contract = $so_contract->get_single($contract_id);
			$area = $contract->get_rented_area();
			
			if(isset($area) && $area > 0)
			{
				$price_per_unit = $total_price / $area;
			}
			
			$result_array = array('total_price' => $total_price, 'area' => $area, 'price_per_unit' => $price_per_unit);
			$result_data = array('results' => $result_array, 'total_records' => 1);
			return $this->yui_results($result_data, 'total_records', 'results');
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
