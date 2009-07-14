<?php
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'contract', 'inc/model/');
	
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
			'viewedit' => true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::contract');
		}
		
		//Common method for JSON queries
		public function query()
		{
			if(phpgw::get_var('phpgw_return_as') == 'json')
			{
				if(phpgw::get_var('id') && $type = phpgw::get_var('type'))
				{
					$id = phpgw::get_var('id');
					$type = phpgw::get_var('type');
					return $this->json_query($id,$type);	
				} 
				else 
				{
					return $this->json_query();
				}
			}
		}
		
		/**
		 * View a list of all contracts
		 */
		public function index()
		{	
			self::add_javascript('rental', 'rental', 'rental.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
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
					$message = phpgw::get_var('message');
					$error = phpgw::get_var('error');
					
					self::add_javascript('rental', 'rental', 'rental.js');
					phpgwapi_yui::load_widget('datatable');
					phpgwapi_yui::load_widget('tabview');
					
					$tabs = array();
					
					foreach(array('rental_rc_details', 'rental_rc_area', 'rental_rc_contracts') as $tab) {
						$tabs[$tab] =  array('label' => lang($tab), 'link' => '#' . $tab);
					}
					
					phpgwapi_yui::tabview_setup('contract_edit_tabview');
	
					$documents = array();
					
					$active_tab = phpgw::get_var('active_tab');
					if (($active_tab == null) || ($active_tab == '')) {
						$active_tab = 'rental_rc_details';
					}
					
					$data = array
					(
						'contract' 	=> $contract,
						'contract_id' => $contract_id,
						'tabs'	=> phpgwapi_yui::tabview_generate($tabs, $active_tab),
						'editable' => $editable,
						'message' => $message,
						'error' => $error,
						'cancel_link' => self::link(array('menuaction' => 'rental.uicontract.index')),
						'dateFormat' 	=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
					);
					//self::render_template('contract', $data);
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
			
			// Get the composite object the user asked for from the DB
			$composite = rental_composite::get(phpgw::get_var('composite_id'));
			// Add that composite to the new contract
			$contract->add_composite($composite);
			
			// TODO: set type of contract.  Do we set a default one or should the
			// user be able to choose it from where this function is called?  (like the context
			// menu of the composite table)
			
			$contract->store();
			
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uicontract.edit', 'id' => $contract->get_id(), 'message' => lang('rental_messages_new_contract')));
		}

		/**
		 * Return a JSON result of rental contract related data
		 * 
		 * @param $contract_id  rental contract id
		 * @param $type	type of details
		 * @param $field_total the field name that holds the total number of records
		 * @param $field_result the field name that holds the query result
		 * @return 
		 */
		protected function json_query($contract_id = null, $type = 'index', $field_total = 'total_records', $field_results = 'results')
		{	
			/*  HTTP get variables:
			 * 
			 * sort: column to sort
			 * dir: direction (ascending, descending)
			 * startIndex: the index to start from in result
			 * results: number of rows to return
			 * level: (1-5) property to room
			 * contract_status: filter for contract status
			 * contract_date: filter for contract dates
			 */
			switch($type)
			{
				case 'index':
					$rows = array();
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
										));
					foreach ($contracts as $contract) {
						$rows[] = $this->get_contract_hash($contract);
					}
					$contract_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				return $contract_data;
			}
			
			//Add action column to each row in result table
			array_walk($contract_data[$field_results], array($this, '_add_actions'), array($contract_id,$type));
			return $this->yui_results($contract_data, $field_total, $field_results);
		}
		
		/**
		 * Add action links for the context menu of the list item
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [composite_id, type of query]
		 */
		public function _add_actions(&$value, $key, $params)
		{
			switch($params[1])
			{
				case 'index':
					$value['actions'] = array(
						'view' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id']))),
						'edit' => html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])))
					);
					break;	
			}
		}
		
		/**
		 * Convert a rental_contract object into a more XSL-friendly keyed array format
		 * 
		 * @param $contract rental_contract to be converted
		 * @return key=>value array of contract data
		 */
		protected function get_contract_hash($contract)
		{
			return array(
				'id' => $contract->get_id(),
				'date_start' => $contract->get_contract_date()->get_start_date(),
				'date_end' => $contract->get_contract_date()->get_end_date(),
				'title'	=> $contract->get_contract_type_title(),
				'composite' => $contract->get_composite_name(),
				'party' => $contract->get_party_name()
			);
		}
	}
?>