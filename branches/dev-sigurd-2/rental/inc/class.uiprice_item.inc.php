<?php
	phpgw::import_class('rental.uicommon');
	
	include_class('rental', 'price_item', 'inc/model/');
	include_class('rental', 'contract', 'inc/model/');
	
	class rental_uiprice_item extends rental_uicommon
	{
		public $public_functions = array
		(
			'add' => true,
			'index' => true,
			'query' => true,
			'view'		=> true,
			'edit'		=> true
		);
		
		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('admin::rental::contract_type_list');
		}
		
		public function index()
		{
			if(!$this->hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;	
			}
			$this->render('admin_price_item_list.php');
		}
		
		/*
		 * View the price item with the id given in the http variable 'id'
		 */
		public function view()
		{
			if(!self::hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			$id = (int)phpgw::get_var('id');
			$price_item = rental_price_item::get($id);
			return $this->viewedit(false, $price_item);
		}
		
		/*
		 * Edit the price item with the id given in the http variable 'id'
		 */
		public function edit()
		{
			if(!self::hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;
			}
			
			$id = (int)phpgw::get_var('id');
			$price_item = rental_price_item::get($id);
			
			// Save the price item if it was posted
			if(isset($_POST['save']))
			{
				$price_item->set_title(phpgw::get_var('title'));
				$price_item->set_agresso_id(phpgw::get_var('agresso_id'));
				$price_item->set_is_area(phpgw::get_var('is_area') == 'true' ? true : false);
				$price_item->set_price(phpgw::get_var('price'));
				if ($price_item->store()) {
					return $this->viewedit(true, $price_item, lang('rental_messages_saved_form'));
				} else {
					return $this->viewedit(true, $price_item, '', lang('rental_messages_form_error'));
				}
				
			}
			
			return $this->viewedit(true, $price_item);
		}
		
		/*
		 * Add a new price item to the database.  Requires only a title.
		 */
		public function add()
		{
			$title = phpgw::get_var('price_item_title');
			if ($title) {
				$price_item = new rental_price_item();
				$price_item->set_title($title);
				if ($price_item->store()) {
					// The object was stored, forward to edit it further
					return $this->viewedit(true, $price_item);
				}
			}
			
			return $this->index();
		}
		
		/**
		 * View or edit rental price_item
		 * 
		 * @param $editable true renders fields editable, false renders fields disabled
		 * @param $price_item the price item to display
		 */
		protected function viewedit($editable, $price_item, $message = '', $error = '')
		{
			$data = array
			(
				'price_item' 	=> $price_item,
				'editable' => $editable,
				'message' => $message,
				'error' => $error,
				'cancel_link' => self::link(array('menuaction' => 'rental.uiprice_item.index'))
			);				
			$this->render('admin_price_item.php', $data);
		}
		
		public function query()
		{
			$type = phpgw::get_var('type');
			$records = array();
			switch($type)
			{
				case 'included_price_items':
					$contract_id = phpgw::get_var('contract_id');
					$contract = rental_contract::get($contract_id);
					$records = $contract->get_price_items();
					break;
				case 'not_included_price_items': // We want to show price items in the source list even after they've been added to a contract
				default:
					$records = rental_price_item::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option')
					);
					break;
			}
			
			$rows = array();
			foreach ($records as $record) {
				$rows[] = $record->serialize();
			}
			$data = array('results' => $rows, 'total_records' => count($rows));
					
			//Add action column to each row in result table
			array_walk($data['results'], array($this, 'add_actions'), array(phpgw::get_var('id'),$type));
			return $this->yui_results($data, 'total_records', 'results');			
		}
		
		/**
		 * Add action links and labels for the context menu of the list items
		 * 
		 * @param $value pointer to 
		 * @param $key ?
		 * @param $params [price_item.id, type of query]
		 */
		public function add_actions(&$value, $key, $params)
		{
		
			$value['actions'] = array();
			$value['labels'] = array();
			
			switch($params[1])
			{
				case 'included_price_items':
					if($this->hasWritePermission())
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.remove_price_item', 'price_item_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_common_remove');
						
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.reset_price_item', 'price_item_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_price_item_reset');
					}
					break;
				case 'not_included_price_items':
					if($this->hasWritePermission())
					{
						$value['ajax'][] = true;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.add_price_item', 'price_item_id' => $value['id'], 'contract_id' => phpgw::get_var('contract_id'))));
						$value['labels'][] = lang('rental_common_add');
					}
					break;
				default:
					$value['ajax'][] = false;
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_common_show');
					
					if($this->hasWritePermission()) 
					{
						$value['ajax'][] = false;
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_common_edit');
					}
			}
		}
	}