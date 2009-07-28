<?php
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'contract', 'inc/model/');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'composite', 'inc/model/');
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract_price_item', 'inc/model/');
	
	class rental_uicontract extends rental_uicommon
	{	
		public $public_functions = array
		(
			'add'			=> true,
			'add_from_composite' => true,
			'edit' => true,
			'index'		=> true,
			'query'		=> true,
			'view' => true,
			'viewedit' => true,
			'add_party' => true,
			'remove_party' => true,
			'add_composite' => true,
			'remove_composite' => true,
			'set_payer' => true
			'add_price_item' => true,
			'remove_price_item' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contract');
		}
		
		public function query()
		{
			$contracts = array();
			$type = phpgw::get_var('type');
			switch($type)
			{
				
				case 'contracts_part':
					$contracts = rental_contract::get_all(
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
					$contracts = rental_contract::get_all(
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
					$contracts = rental_contract::get_all(
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
				case 'contracts_for_executive_officer':
					$contracts = rental_contract::get_all(
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
					$contracts = rental_contract::get_all(
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
				default:
					$contracts = rental_contract::get_all(
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
			foreach ($contracts as $contract) {
				$rows[] = $contract->serialize();
			}
			
			//Add context menu columns (actions and labels)
			array_walk($rows, array($this, 'add_actions'), $type);
			
			//Build a YUI result from the data
			$contract_data = array('results' => $rows, 'total_records' => count($rows));
			return $this->yui_results($contract_data, 'total_records', 'results');
		}
		
		/**
		 * Add data for context menu
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $type
		 */
		public function add_actions(&$value, $key, $type)
		{
			$value['actions'] = array();
			$value['labels'] = array();
			
			switch($type)
			{
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_common_edit');
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
		public function viewedit($editable, $contract_id)
		{
			if ($contract_id > 0) {
				$contract = rental_contract::get($contract_id);
				if ($contract) {
					$data = array
					(
						'contract' 	=> $contract,
						'editable' => $editable,
						'message' => phpgw::get_var('message'),
						'error' => phpgw::get_var('error'),
						'cancel_link' => self::link(array('menuaction' => 'rental.uicontract.index')),
					);
					$this->render('contract.php', $data);
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
			
			if(isset($_POST['save_contract']))
			{
				$contract = rental_contract::get($contract_id);
				
				$date_start =  strtotime(phpgw::get_var('date_start_hidden'));
				$date_end =  strtotime(phpgw::get_var('date_end_hidden'));
				$contract->set_contract_date(new rental_contract_date($date_start, $date_end));
				
				
				$contract->set_account(phpgw::get_var('account_number'));
				
				$contract->store();
			}
			
			return $this->viewedit(true, $contract_id);
		}
		
		/**
		 * Create a new empty contract
		 */
		public function add()
		{
			$contract = new rental_contract();
			
			// Set the type of the new contract
			$contract->set_type_id(phpgw::get_var('new_contract_type'));
			$contract->store();
			
			// Redirect to edit
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		/**
		 * Create a new contract tied to the composite provided in the composite_id parameter 
		 */
		public function add_from_composite()
		{
			$contract = new rental_contract();
			$contract->store();
			
			// Get the composite ob		public function set_payer(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$contract = rental_contract::get($contract_id);
			$contract->set_payer($party_id);
		}
		
		
ject the user asked for from the DB
			$composite = rental_composite::get(phpgw::get_var('composite_id'));
			// Add that composite to the new contract
			$contract->add_composite($composite);
			
			// TODO: set type of contract.  Do we set a default one or should the
			// user be able to choose it from where this function is called?  (like the context
			// menu of the composite table)
			
			$contract->store();
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function add_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$party = rental_party::get($party_id);
			$contract = rental_contract::get($contract_id);
			$contract->add_party($party);
			return true;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function remove_party(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$party = rental_party::get($party_id);
			$contract = rental_contract::get($contract_id);
			$contract->remove_party($party);
			return true;
			//$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function set_payer(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$party_id = (int)phpgw::get_var('party_id');
			$contract = rental_contract::get($contract_id);
			$contract->set_payer($party_id);
		}
		
		
		public function add_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$composite = rental_composite::get($composite_id);
			$contract = rental_contract::get($contract_id);
			$contract->add_composite($composite);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function remove_composite(){
			$contract_id = (int)phpgw::get_var('contract_id');
			$composite_id = (int)phpgw::get_var('composite_id');
			$composite = rental_composite::get($composite_id);
			$contract = rental_contract::get($contract_id);
			$contract->remove_composite($composite);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function add_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$price_item = rental_price_item::get($price_item_id);
			$contract = rental_contract::get($contract_id);
			$contract->add_price_item($price_item);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
		
		public function remove_price_item()
		{
			$contract_id = (int)phpgw::get_var('contract_id');
			$price_item_id = (int)phpgw::get_var('price_item_id');
			$price_item = rental_contract_price_item::get($price_item_id);
			$contract = rental_contract::get($contract_id);
			$contract->remove_price_item($price_item);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}
	}
?>