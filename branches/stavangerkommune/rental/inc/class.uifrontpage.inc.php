<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.socontract');

	class rental_uifrontpage extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'	=> true,
			'query' => true
		);

		public function __construct()
		{
      		parent::__construct();
			self::set_active_menu('rental');
		}

		public function query()
		{
			$type = phpgw::get_var('type');
			
			switch($type){
				case 'save_panel_settings':
					$panel = phpgw::get_var('name');
					$visibility = phpgw::get_var('visibility');
					$x = phpgw::get_var('x');
					$y = phpgw::get_var('y');
					$panel_config = array($visibility,$x,$y);
					$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
					$GLOBALS['phpgw']->preferences->read();
					$GLOBALS['phpgw']->preferences->add('rental','rental_frontpage_panel_'.$panel,$panel_config,'user');
					$GLOBALS['phpgw']->preferences->save_repository();
					break;
				case 'reset_panel_settings':
					$panel = phpgw::get_var('name');
					$GLOBALS['phpgw']->preferences->account_id=$GLOBALS['phpgw_info']['user']['account_id'];
					$GLOBALS['phpgw']->preferences->read();
					$GLOBALS['phpgw']->preferences->delete('rental','rental_frontpage_panel_'.$panel,'','user');
					$GLOBALS['phpgw']->preferences->save_repository();
					break;
			}
		}
		
		public function index()
		{
			$this->render('frontpage.php');
		}
	}
?>