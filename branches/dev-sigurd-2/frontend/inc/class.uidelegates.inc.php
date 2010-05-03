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
			
			if(isset($_POST['search']))
			{
				$username = phpgw::get_var('username');
				if(!isset($username))
				{
					$msglog['error'] = 'Vennligst fyll ut et brukernavn';
				}
				else
				{
					$account_id = frontend_bofrontend::delegate_exist($username);
					if($account_id)
					{
						$search = frontend_bofrontend::get_account_info($account_id);
						
					}
					else
					{
						$msglog['error'] = 'Ingen treff';
					}
				}
			} 
			else if(isset($_POST['add']))
			{
				$search = array();
				$modules = array
				(
					'frontend',
					'preferences'
				);
				
				$acls = array
				(
					array
					(
						'appname'	=> 'preferences',
						'location'	=> 'changepassword',
						'rights'	=> 1
					),
					array
					(
						'appname'	=> 'frontend',
						'location'	=> 'run',
						'rights'	=> 1
					),
					array
					(
						'appname'	=> 'frontend',
						'location'	=> '.ticket',
						'rights'	=> 1
					),
					array
					(
						'appname'	=> 'frontend',
						'location'	=> '.rental.contract',
						'rights'	=> 1
					),
					array
					(
						'appname'	=> 'frontend',
						'location'	=> '.rental.contract_in',
						'rights'	=> 1
					)
				);
				
				$aclobj =& $GLOBALS['phpgw']->acl;
				//Add user
			}
			
			$form_action = $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'frontend.uidelegates.index'));
			
			$data = array (
				'msgbox_data'   => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
				'header' 		=>	$this->header_state,
				'tabs' 			=> 	$this->tabs,
				'delegate_data' => 	array (
					'form_action' => $form_action,
					'delegate' 	=> $delegates,
					'search'	=> isset($search) ? $search : array()
				),
				
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
