<?php
	phpgw::import_class('rental.uicommon');
	
	include_class('rental', 'price_item', 'inc/model/');
	
	class rental_uiadmin extends rental_uicommon
	{
		public $public_functions = array
		(
			'query' => true,
			'contract_type_list'		=> true,
			'price_item_list'		=> true
		);
		
		public function __construct()
		{
			parent::__construct();
			self::set_active_menu('admin::rental');
		}
		
		/**
		 * List available contract types with options to add and edit
		 */
		public function contract_type_list()
		{
			self::set_active_menu('admin::rental::contract_type_list');
		}
		
		/**
		 * List available price items (prisbok) with options to add and edit
		 */
		public function price_item_list()
		{
			self::set_active_menu('admin::rental::price_item_list');
			
			if(!$this->hasReadPermission())
			{
				$this->render('permission_denied.php');
				return;	
			}
			$this->render('admin_price_item_list.php');
		}
		
	public function query()
		{
			$type = phpgw::get_var('type');
			$records = array();
			switch($type)
			{
				case 'price_item_list':
					$records = rental_price_item::get_all(
						phpgw::get_var('startIndex'),
						phpgw::get_var('results'),
						phpgw::get_var('sort'),
						phpgw::get_var('dir'),
						phpgw::get_var('query'),
						phpgw::get_var('search_option')
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
			}
			
			$rows = array();
			foreach ($records as $record) {
				$rows[] = $record->serialize();
			}
			$data = array('results' => $rows, 'total_records' => count($rows));
					
			//Add action column to each row in result table
			//array_walk($data['results'], array($this, 'add_actions'), array(phpgw::get_var('id'),$type));
			return $this->yui_results($data, 'total_records', 'results');			
		}
	}
?>