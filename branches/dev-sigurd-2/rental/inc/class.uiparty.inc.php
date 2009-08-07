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
			self::set_active_menu('rental::parties');
		}
		
		public function query()
		{
			$type = phpgw::get_var('type');
			$parties = array();
			switch($type)
			{
				case 'included_parties':
					$contract_id = phpgw::get_var('contract_id');
					$contract = rental_contract::get($contract_id);
					$parties = $contract->get_parties();
					break;
				case 'not_included_parties':
					$parties = rental_party::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'party_type' => phpgw::get_var('party_type'),
							'contract_id' => phpgw::get_var('contract_id')
						)
					);
					break;
				default:
					$parties = rental_party::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option'),
						array(
							'party_type' => phpgw::get_var('party_type')
						)
					);
					break;
			}
			
			
			
			$rows = array();
			foreach ($parties as $party) {
				$rows[] = $party->serialize($contract);
			}
			$party_data = array('results' => $rows, 'total_records' => count($rows));
					
			//Add action column to each row in result table
			array_walk($party_data['results'], array($this, 'add_actions'), array(phpgw::get_var('contract_id'),$type,$contract));
			return $this->yui_results($party_data, 'total_records', 'results');			
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
			$value['labels'] = array();
			switch($params[1])
			{
				case 'included_parties':
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_party', 'party_id' => $value['id'], 'contract_id' => $params[0])));
						$value['labels'][] = lang('rental_common_remove');
						if($value['id'] != $params[2]->get_payer_id()){
							$value['ajax'][] = true;
							$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.set_payer', 'party_id' => $value['id'], 'contract_id' => $params[0])));
							$value['labels'][] = lang('rental_common_set_payer');
						}
					}
					break;
				case 'not_included_parties':
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = true;			
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_party', 'party_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_common_add');
						break;
					}
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_common_edit');
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
			$party = new rental_party();
			$party->store();
			// Redirect to edit
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit', 'id' => $party->get_id(), 'message' => lang('rental_messages_new_party')));
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