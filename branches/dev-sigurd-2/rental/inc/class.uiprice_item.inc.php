<?php
	phpgw::import_class('rental.uicommon');
	
	include_class('rental', 'price_item', 'inc/model/');
	
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
			return $this -> viewedit(false, $id);
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
			
			// Save the price item if it was posted
			if(isset($_POST['save']))
			{
				$price_item = new rental_price_item($id);
				$price_item->set_title(phpgw::get_var('title'));
				$price_item->set_agresso_id(phpgw::get_var('agresso_id'));
				$price_item->set_is_area(phpgw::get_var('is_area') == 'true' ? true : false);
				$price_item->set_price(phpgw::get_var('price'));
				$price_item->store();
				// XXX: How to get error msgs back to user?
			}
			
			return $this -> viewedit(true, $id);
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
				$price_item->store();
				
				return $this->viewedit(true, $price_item->get_id());
			}
		}
		
		/**
		 * View or edit rental price_item
		 * 
		 * @param $editable true renders fields editable, false renders fields disabled
		 * @param $id	the rental price_item id	
		 */
		protected function viewedit($editable, $id)
		{
			if ($id > 0) {
				$price_item = rental_price_item::get($id);
				$data = array
				(
					'price_item' 	=> $price_item,
					'editable' => $editable,
					'message' => phpgw::get_var('message'),
					'error' =>  phpgw::get_var('error'),
					'cancel_link' => self::link(array('menuaction' => 'rental.uiprice_item.index'))
				);				
				$this->render('admin_price_item.php', $data);
			}
		}
		
		public function query()
		{
			$type = phpgw::get_var('type');
			$records = array();
			switch($type)
			{
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
				default:
					$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.view', 'id' => $value['id'])));
					$value['labels'][] = lang('rental_cm_show');
					
					if($this->hasWritePermission()) 
					{
						$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiprice_item.edit', 'id' => $value['id'])));
						$value['labels'][] = lang('rental_cm_edit');
					}
			}
		}
	}