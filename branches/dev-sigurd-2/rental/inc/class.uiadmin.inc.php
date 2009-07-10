<?php
	phpgw::import_class('rental.uicommon');
	
	class rental_uiadmin extends rental_uicommon
	{
		public $public_functions = array
		(
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
		}
	}
?>