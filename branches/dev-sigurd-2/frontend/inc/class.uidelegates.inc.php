<?php
    phpgw::import_class('frontend.uifrontend');

	class frontend_uidelegates extends frontend_uifrontend
	{	
		public $public_functions = array
			(
			'index'				=> true,
			'add_delegate'		=> true,
			'remove_delegate'	=> true
		);

		public function __construct()
		{
			phpgwapi_cache::user_set('frontend','tab',$GLOBALS['phpgw']->locations->get_id('frontend','.delegates'), $GLOBALS['phpgw_info']['user']['account_id']);
			parent::__construct();	
		}
		
		

		public function index()
		{			
			$delegates = frontend_bofrontend::get_delegates(null);
			$GLOBALS['phpgw']->js->validate_file('yahoo', 'delegate.list' , 'frontend');
			$GLOBALS['phpgw']->css->validate_file('property');
			$GLOBALS['phpgw']->css->add_external_file('property/templates/base/css/property.css');

			$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend','msgbox');
			
			$data = array (
				'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'header' 		=>	$this->header_state,
				'tabs' 			=> 	$this->tabs,
				'delegate_data' => 	array (
					'delegate' => $delegates
				),
				'lightbox_name'	=> lang('add delegate')
				
			);
			
			
			$GLOBALS['phpgw']->xslttpl->set_var('phpgw',array('app_data' => $data));
			$GLOBALS['phpgw']->xslttpl->add_file(array('frontend','delegate'));
		}
		
		public function add_delegate()
		{
			/*
			 * (0). Assume that user is a phpgw user
			 * 1. Add access to frontend
			 * 2. Add access to frontend areas (helpdesk, contracts, ...)
			 * 3. Insert delegate user_name and unit leader in database
			 */
			
		}
		
		public function remove_delegate()
		{
			$account_id = phpgw::get_var('account_id'); 
			$owner_id = phpgw::get_var('owner_id'); 
			
			frontend_bofrontend::remove_delegate($account_id,$owner_id);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'frontend.uidelegates.index'));
		}
	}
