<?php
	phpgw::import_class('rental.uicommon');
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
			'viewedit'				=> true,
			'add_party'				=> true,
			'remove_party'			=> true,
			'add_composite'			=> true,
			'remove_composite'		=> true,
			'set_payer'				=> true,
			'add_price_item'		=> true,
			'remove_price_item'		=> true,
			'reset_price_item'		=> true,
			'delete_notification'	=> true,
			'dismiss_notification'	=> true,
			'dismiss_notification_for_all'	=> true,
			'download'              => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contracts');
		}

		public function query()
		{
			$resultArray = array();

			$type = phpgw::get_var('type');
			switch($type)
			{
				case 'contracts_part':
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'party_id' => phpgw::get_var('party_id')
						)
					);
					break;
				case 'contracts_for_executive_officer':
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'executive_officer' => $GLOBALS['phpgw_info']['user']['account_id']
						)
					);
					break;
				case 'last_edited_by':
					$resultArray = rental_contract::get_last_edited_by();
					break;
				case 'ending_contracts':
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'contract_status' => 'under_dismissal'
						)
					);
					break;
				case 'contracts_for_executive_officer':
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'executive_officer' => $GLOBALS['phpgw_info']['user']['account_id']
						)
					);
					break;
				case 'last_edited_by':
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'last_edited_by' => $GLOBALS['phpgw_info']['user']['account_id']
						)
					);
					break;
				case 'notifications':
					$resultArray = rental_notification::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							//'account_id' => $GLOBALS['phpgw_info']['user']['account_id'], (show all notifications for each contract)
							'contract_id' => phpgw::get_var('contract_id')
						)
					);
					break;
				case 'notifications_for_user':
					$resultArray = rental_notification::get_workbench_notifications(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						$GLOBALS['phpgw_info']['user']['account_id']
					);
					break;
				case 'all_contracts':
				default:
					$resultArray = rental_contract::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'contract_status' => phpgw::get_var('contract_status'),
							'contract_type' => phpgw::get_var('contract_type'),
							'status_date_hidden' => phpgw::get_var('status_date_hidden')
						)
					);
			}

			//Serialize the contracts found
			$rows = array();
			foreach ($resultArray as $result) {
				if(isset($result))
				{
					if($result->has_permission(PHPGW_ACL_READ)) // check for read permission
					{
						$rows[] = $result->serialize();
					}
				}
			}

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), array($type, $editable));

			//Build a YUI result from the data
			$result_data = array('results' => $rows, 'total_records' => count($rows));
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

			$editable = $params[1];
			$type = $params[0];
			$permissions = $value['permissions'];
			
			
			switch($type)
			{
				case 'notifications':
					if($permissions[PHPGW_ACL_DELETE])
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.delete_notification', 'id' => $value['id'], 'contract_id' => $value['contract_id'])));
						$value['labels'][] = lang('delete');
					}
					break;
				case 'notifications_for_user':
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['contract_id'])));
						$value['labels'][] = lang('edit_contract');
					}
					
					$value['ajax'][] = true;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.dismiss_notification', 'id' => $value['id'])));
					$value['labels'][] = lang('remove_from_workbench');
					
					if($permissions[PHPGW_ACL_DELETE])
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.dismiss_notification_for_all', 'id' => $value['originated_from'], 'contract_id' => $value['contract_id'])));
						$value['labels'][] = lang('remove_from_all_workbenches');
					}
					break;
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
					if($permissions[PHPGW_ACL_EDIT])
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit_contract');
					}
					break;
				default:
					if($permissions[PHPGW_ACL_EDIT] && $editable == true)
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('edit');
					}
					if($permissions[PHPGW_ACL_READ] && $editable == true)
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
			$data = array('editable' => true);
			$this->render('contract_list.php', $data);
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
				$contract = rental_contract::get($contract_id);
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
				$types = rental_contract::get_fields_of_responsibility();
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

		public function add_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$party = rental_party::get($party_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->add_party($party);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function remove_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$party = rental_party::get($party_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->remove_party($party);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function set_payer(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->set_payer($party_id);
				return true;
			}
			return false;
		}


		public function add_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$composite = rental_composite::get($composite_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->add_composite($composite);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function remove_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$composite = rental_composite::get($composite_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->remove_composite($composite);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function add_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$price_item = rental_price_item::get($price_item_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->add_price_item($price_item);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function remove_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$price_item = rental_contract_price_item::get($price_item_id);
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				$contract->remove_price_item($price_item);
				return true;
			}
			return false;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('messages_new_contract')));
		}

		public function reset_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$price_item = rental_contract_price_item::get($price_item_id);
			if($this->isExecutiveOfficer() || $this->isAdministrator())
			{
				$price_item->reset();
				return true;
			}
			return false;
		}

		/**
		 * Visible controller function for deleting a contract notification.
		 * 
		 * @return true on success/false otherwise
		 */
		public function delete_notification()
		{
			$notification_id = (int)phpgw::get_var('id');
			$contract_id = (int)phpgw::get_var('contract_id');
			$contract = rental_contract::get($contract_id);
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{	
				rental_notification::delete_notification($notification_id);
				return true;
			}
			return false;
		}

		
		/**
		 * Visible controller function for dismissing a single workbench notification
		 * 
		 * @return true on success/false otherwise
		 */
		public function dismiss_notification()
		{
			$notification_id = (int)phpgw::get_var('id');
			
			//TODO: should we check to see if the notification exists on the current users workbench? 
			
			rental_notification::dismiss_notification($notification_id,strtotime('now'));
		}
		
		/**
		 * Visible controller function for dismissing all workbench notifications originated 
		 * from a given notification. The user must have EDIT privileges on a contract for
		 * this action.
		 * 
		 * @return true on success/false otherwise
		 */
		public function dismiss_notification_for_all()
		{
			//the source notification
			$notification_id = (int)phpgw::get_var('id');
			$contract_id = (int)phpgw::get_var('contract_id');
			$contract = rental_contract::get($contract_id);

			//TODO: should we check to see if the notification exists on the current users workbench? 
						
			if($contract->has_permission(PHPGW_ACL_EDIT))
			{
				rental_notification::dismiss_notification_for_all($notification_id);
				return true;
			}
			return false;
			
		}

        /**
		 * Download xls, csv or similar file representation of the data table
		 */
        public function download()
		{
            $list = $this->query();
            $list = $list[ResultSet][Result];

            $keys = array();
            foreach($list[0] as $key => $value) {
                if(!is_array($value)) {
                    array_push($keys, $key);
                }
            }

            // Use keys as headings
            $headings = $keys;

			$property_common = CreateObject('property.bocommon');
            $property_common->download($list, $keys, $headings);
		}
	}
?>