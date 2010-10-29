<?php
	phpgw::import_class('rental.uicommon');
	
	class uidelegate extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'				=> true,
			'query'				=> true,
			'add_delegate'		=> true,
			'remove_delegate'	=> true
		);
		
		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::delegates');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('delegates');
		}
		
		public function query(){
			$unit_id = (int)phpgw::get_var('unit_id');
			
			if (isset($unit_id) && $unit_id > 0) {
				$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit_id);
			}
			
			$delegates_data = array('results' => $delegates_per_org_unit, 'total_records' => count($delegates_per_org_unit));
			
			$editable = phpgw::get_var('editable') == 'true' ? true : false;
			
			return $this->yui_results($resultunit_data, 'total_records', 'results');
		}
		
		public function index(){
			if(isset($_POST['search']))
			{
				$username = phpgw::get_var('username');
				if(!isset($username))
				{
					$msglog['error'][] = array('msg' => lang('lacking_username'));
				}
				else if($username == $GLOBALS['phpgw_info']['user']['account_lid'])
				{
					$msglog['error'][] = array('msg' => lang('searching_for_self'));
				}
				else
				{
					$account_id = frontend_bofrontend::delegate_exist($username);
					if($account_id)
					{
						$search = frontend_bofrontend::get_account_info($account_id);
						$msglog['message'][] = array('msg' => lang('user_found_in_PE'));
					}
					else
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);
						if($fellesdata_user)
						{
							$search = $fellesdata_user;
							$msglog['message'][] = array('msg' => lang('user_found_in_Fellesdata'));
						}
						else
						{
							$msglog['error'][] = array('msg' => lang('no_hits'));
						}
					}
				}
			} 
			else if(isset($_POST['add']))
			{
				$account_id = phpgw::get_var('account_id');
				
				$org_units = frontend_bofellesdata::get_instance()->get_result_units($GLOBALS['phpgw_info']['user']['account_lid']);
				
				//Parameter to delegate access to only a single organisational unit
				$org_unit_id = $this->header_state['selected_org_unit'];
				$success = true;
				
				foreach($org_units as $org_unit)
				{
					if($org_unit_id == 'all' || $org_unit['ORG_UNIT_ID'] == $org_unit_id)
					{
						$res = $this->add_delegate($account_id,$org_unit['ORG_UNIT_ID'],$org_unit['ORG_NAME']);
						if(!$res)
						{
							$msglog['error'][] = array('msg' => lang('error_delegating_unit',$org_unit['ORG_NAME']));
						}
						$success = $success  && $res;
					}
				}
				
				if($success)
				{
					$msglog['message'][] = array('msg' => lang('delegation_successful'));	
				}
				else
				{
					$msglog['error'][] = array('msg' => lang('delegation_error'));	
				}
			}
			
			
			
			
			
			$this->render('delegate_list.php');
		}
		
		public function add_delegate(int $account_id, $org_unit_id, $org_name)
		{
			if(!isset($account_id) || $account_id == '')
			{
				//User is only registered in Fellesdata
				$username = phpgw::get_var('username'); 
				$firstname = phpgw::get_var('firstname'); 
				$lastname = phpgw::get_var('lastname'); 
				$password = 'TEst1234';
				
				$account_id = frontend_bofrontend::create_delegate_account($username, $firstname, $lastname, $password);
				
				if(isset($account_id) && !is_numeric($account_id))
				{
					return false;
				}
			}	
			return frontend_bofrontend::add_delegate($account_id, null, $org_unit_id, $org_name);
		}
		
		public function remove_delegate()
		{
			$account_id = phpgw::get_var('account_id'); 
			$owner_id = phpgw::get_var('owner_id');
			
			frontend_bofrontend::remove_delegate($account_id,$owner_id);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiresultunit.edit'));
		}
	}