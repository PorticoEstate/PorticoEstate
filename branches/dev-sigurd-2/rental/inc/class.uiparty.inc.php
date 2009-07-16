<?php
	phpgw::import_class('rental.uicommon');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	
	class rental_uiparty extends rental_uicommon
	{	
		public $public_functions = array
		(
			'add'		=> true,
			'edit'		=> true,
			'index'		=> true,
			'query'		=> true,
			'view'		=> true
		);

		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('rental::party');
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
		 * Return a JSON result of rental party related data
		 * 
		 * @param $party_id  rental party id
		 * @param $type	type of details
		 * @param $field_total the field name that holds the total number of records
		 * @param $field_result the field name that holds the query result
		 * @return 
		 */
		protected function json_query($party_id = null, $type = 'index', $field_total = 'total_records', $field_results = 'results')
		{	
			/*  HTTP get variables:
			 * 
			 * sort: column to sort
			 * dir: direction (ascending, descending)
			 * startIndex: the index to start from in result
			 * results: number of rows to return
			 * contract_status: filter for contract status
			 * contract_date: filter for contract dates
			 */
			switch($type)
			{
				case 'index':
					$rows = array();
					$parties = rental_party::get_all(
										phpgw::get_var('startIndex'),
										phpgw::get_var('results'),
										phpgw::get_var('sort'),
										phpgw::get_var('dir'),
										phpgw::get_var('query'),
										phpgw::get_var('search_option'),
										array('party_type' => phpgw::get_var('party_type'))
										);
					foreach ($parties as $party) {
						$rows[] = $party->serialize();
					}
					$party_data = array('results' => $rows, 'total_records' => count($rows));
					break;
				return $party_data;
			}
			
			//Add action column to each row in result table
			array_walk($party_data[$field_results], array($this, 'add_actions'), array($party_id,$type));
			return $this->yui_results($party_data, $field_total, $field_results);
		}
		
		/**
		 * Add action links for the context menu of the list item
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [composite_id, type of query]
		 */
		public function add_actions(&$value, $key, $params)
		{
			$value['actions'] = array();
			$value['labes'] = array();
				
			switch($params[1])
			{
				case 'index':
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_cm_show');
					if($this->hasWritePermission()) 
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_cm_edit');
					}
					break;
				case 'contracts':
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_cm_show');
					if($this->hasWritePermission()) 
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_cm_edit');
					}
					break;
			}
			
		}
		
		
		///View all contracts
		public function index()
		{	
			$this->render('party_list.php');
		}
		
		/**
		 * Adds a new party and forwards to edit mode for it.
		 * 
		 */
		public function add()
		{
			$receipt = rental_party::add();
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit', 'id' => $receipt['id'], 'message' => lang('rental_messages_new_party')));
		}
		
		/**
		 * Displays info about a party.
		 * 
		 */
		public function view() {
			return $this -> viewedit(false, (int)phpgw::get_var('id'));
		}
		
		/**
		 * Edits a party.
		 */
		public function edit(){
			$party_id = (int)phpgw::get_var('id');
			if(isset($_POST['save_party']))
			{
				$party = new rental_party($party_id);
				$party->set_personal_identification_number(phpgw::get_var('personal_identification_number'));
				$party->set_first_name(phpgw::get_var('firstname'));
				$party->set_last_name(phpgw::get_var('lastname'));
				$party->set_title(phpgw::get_var('title'));
				$party->set_company_name(phpgw::get_var('company_name'));
				$party->set_department(phpgw::get_var('department'));
				$party->set_address_1(phpgw::get_var('address1'));
				$party->set_address_2(phpgw::get_var('address2'));
				$party->set_postal_code(phpgw::get_var('postal_code'));
				$party->set_place(phpgw::get_var('place'));
				$party->set_phone(phpgw::get_var('phone'));
				$party->set_fax(phpgw::get_var('fax'));
				$party->set_email(phpgw::get_var('email'));
				$party->set_url(phpgw::get_var('url'));
				$party->set_type_id(phpgw::get_var('type_id'));
				$party->set_post_bank_account_number(phpgw::get_var('post_bank_account_number'));
				$party->set_account_number(phpgw::get_var('account_number'));
				$party->set_reskontro(phpgw::get_var('reskontro'));
				$party->set_is_active(phpgw::get_var('is_active') == 'on' ? true : false);
				$party->store();
				// XXX: How to get error msgs back to user?
			}
			return $this -> viewedit(true, $party_id);
		}
		
		/**
		 * View or edit party
		 * 
		 * @param $editable bool true renders fields editable, false renders fields disabled
		 * @param $party_id int with the party id	
		 */
		protected function viewedit($editable = false, $party_id)
		{
			$party_id = (int)$party_id;
			if($party_id > 0) // Id is set
			{
				$party = rental_party::get($party_id);
				if($party) {
					$data = array
					(
						'party' 	=> $party,
						'editable' => $editable,
						'message' => phpgw::get_var('message'),
						'error' => phpgw::get_var('error'),
						'cancel_link' => self::link(array('menuaction' => 'rental.uiparty.index')),
						);				
				$this->render('party.php', $data);	
				}
			}
		}
	}
?>