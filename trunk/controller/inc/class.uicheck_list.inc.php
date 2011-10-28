<?php
	phpgw::import_class('controller.uicommon');
	phpgw::import_class('controller.socheck_list');
	
	class controller_uicheck_list extends controller_uicommon
	{
		private $so;
				
		public $public_functions = array
		(
			'index'	=>	true
		);

		public function __construct()
		{
			parent::__construct();
			$this->so = CreateObject('controller.socheck_list');
			$GLOBALS['phpgw_info']['flags']['menu_selection'] = "controller::check_list";
		}
		
		public function index()
		{
		
			$check_list_array = $this->so->get_check_list();
			
			$data = array
			(
				'check_list_array'	=> $check_list_array
			);
			
				
			self::render_template_xsl('control_check_lists', $data);
		}
		
		public function query(){}
	}
