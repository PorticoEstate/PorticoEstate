<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sobilling');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	include_class('rental', 'notification', 'inc/model/');

	class rental_uicontract extends rental_uicommon
	{
		public $public_functions = array
		(
			'add'					=> true,
			'add_from_composite'	=> true,
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
			'download'              => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contracts');
		}

		public function query()
		{
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', 1000);
			$sort_field		= phpgw::get_var('sort');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');
			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			$type = phpgw::get_var('type');	
			switch($type)
			{
				case 'contracts_part': 						// Contracts for this party
					$filters = array('party_id' => phpgw::get_var('party_id'));
					break;
				case 'contracts_for_executive_officer': 	// Contracts for this executive officer
					$filters = array('executive_officer' => $GLOBALS['phpgw_info']['user']['account_id']);
					break;
				case 'ending_contracts' OR 'ended_contracts' OR 'last_edited':					
					// Queries that depend on areas of responsibility
					$types = rental_socontract::get_instance()->get_fields_of_responsibility();
					$ids = array();
					foreach($types as $id => $label)
					{
						$names = $this->locations->get_name($id);
						if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
						{
							if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
							{
								$ids[] = $id;
							}
						}
					}
					$comma_seperated_ids = implode(',',$ids);
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
							break;
					}
					
					break;
				case 'contracts_for_composite': // ... all contracts this composite is involved in, filters (status and date)
					$filters = array('composite_id' => phpgw::get_var('composite_id'),phpgw::get_var('contract_status'),phpgw::get_var('contract_date'));
				case 'all_contracts':
				default:
					$filters = array('contract_status' => phpgw::get_var('contract_status'), 'contract_type' => phpgw::get_var('contract_type'), 'status_date_hidden' => phpgw::get_var('status_date_hidden'));
			}

			$result_objects = rental_socontract::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_socontract::get_instance()->get_count($search_for, $search_type, $filters);
			
			//Serialize the contracts found
			$rows = array();
			foreach ($result_objects as $result) {
				if(isset($result))
				{
					if($result->has_permission(PHPGW_ACL_READ)) // check for read permission
					{
						$rows[] = $result->serialize();
					}
				}
			}

			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($type));

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
			$permissions = $value['permissions'];
			
			
			switch($type)
			{
				case 'last_edited_by':
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit_contract');
					}
					break;
				case 'contracts_for_executive_officer':
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit_contract');
					}
					break;
				case 'ending_contracts':
				case 'ended_contracts':
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit_contract');
					}
					break;
				default:
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
					if($permissions[PHPGW_ACL_READ])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
						$value['labels'][] = lang('show');		
					}
				}
		}

		/**
		 * View a list of all contracts
		 */
		public function index()
		{
			$this->render('contract_list.php');
		}

		/**
		 * Common function for viewing or editing a contract
		 *
		 * @param $editable whether or not the contract should be editable in the view
		 * @param $contract_id the id of the contract to show
		 */
		public function viewedit($editable, $contract_id, $notification = null, string $message = null, string $error = null)
		{
			
			if (isset($contract_id) && $contract_id > 0) {
				$contract = rental_socontract::get_instance()->get_single($contract_id);
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
						'cancel_link' => self::link(array('menuaction' => 'rental.uicontract.index')),
					);
					
					$this->render('contract.php', $data);
				}
			}
			else
			{
				if($this->isAdministrator() || $this->isExecutiveOfficer()){
					$contract = new rental_contract();
					if ($contract) {
						$data = array
						(
							'contract' 	=> $contract,
							'notification' => $notification,
							'editable' => true,
							'message' => isset($message) ? $message : phpgw::get_var('message'),
							'error' => isset($error) ? $error : phpgw::get_var('error'),
							'cancel_link' => self::link(array('menuaction' => 'rental.uicontract.index')),
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
		public function view() {
			$contract_id = (int)phpgw::get_var('id');
			return $this->viewedit(false, $contract_id);
		}

		/**
		 * Edit a contract
		 */
		public function edit()
		{
			$contract_id = (int)phpgw::get_var('id');
			$message = null;
			$error = null;

			if(isset($_POST['save_contract']))
			{
				if(isset($contract_id))
				{
					$contract = rental_contract::get($contract_id);
					if(!$contract->has_permission(PHPGW_ACL_EDIT))
					{
						unset($contract);
						$this->render('permission_denied.php',array('error' => lang('permission_denied_edit_contract')));
					}
				}
				else
				{
					if($this->isExecutiveOfficer() || $this->isAdministrator()){
						$contract = new rental_contract();
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
					$contract->set_location_id(phpgw::get_var('location_id'));
					$contract->set_term_id(phpgw::get_var('billing_term'));
					$contract->set_billing_start_date(strtotime(phpgw::get_var('billing_start_date_hidden')));
					
					if($contract->store())
					{
						$message = lang('messages_saved_form');
						$contract_id = $contract->get_id();
					}
					else
					{
						$error = lang('messages_form_error');
					}
				}
			}
			else if(isset($_POST['add_notification']))
			{
				$contract = rental_contract::get($contract_id);
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
					if ($notification->store())
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
			return $this->viewedit(true, $contract_id, $notification, $message, $error);
		}

		/**
		 * Create a new empty contract
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit'));
		}

		/**
		 * Create a new contract tied to the composite provided in the composite_id parameter
		 */
		public function add_from_composite()
		{
			if($this->isExcutiveOfficer())
			{
				$contract = new rental_contract();
				
				// Sets the first location this user is executive officer (add access) for
				$types = rental_socontract::get_instance()->get_fields_of_responsibility();
				foreach($types as $id => $label)
				{
					$names = $this->locations->get_name($id);
					if($names['appname'] == $GLOBALS['phpgw_info']['flags']['currentapp'])
					{
						if($this->hasPermissionOn($names['location'],PHPGW_ACL_ADD))
						{
							$contract->set_location_id($id);
							break;
						}
					}
				}
				
				if($contract->store())
				{
					// Get the composite object the user asked for from the DB
					$composite = rental_composite::get(phpgw::get_var('composite_id'));
					// Add that composite to the new contract
					$contract->add_composite($composite);
					
					
					
					if($contract->store())
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_form_error')));
					}
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
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				return $so_contract->remove_composite($contract_id. $composite_id);
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
	}
?>