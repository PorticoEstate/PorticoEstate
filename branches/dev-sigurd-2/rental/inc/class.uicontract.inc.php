<?php
	phpgw::import_class('rental.uicommon');
	
	class rental_uicontract extends rental_uicommon
	{	
		public $public_functions = array
		(
			'index'		=> true
		);

		public function __construct()
		{
			parent::__construct();
			//$this->bo = CreateObject('rental.bocomposite');
			self::set_active_menu('rental::contract');
		}
		
		///View all contracts
		public function index()
		{			
			self::add_javascript('rental', 'rental', 'rental.js');
			phpgwapi_yui::load_widget('datatable');
			phpgwapi_yui::load_widget('paginator');
			//phpgwapi_yui::load_widget('calendar');
			$data = array
			(
				'dateFormat' 	=> $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat']
			);
			self::render_template('contract_list',$data);
		}
	}
?>