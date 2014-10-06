<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.bofellesdata');
	phpgw::import_class('frontend.bofrontend');
	phpgw::import_class('frontend.bofellesdata');

	class rental_uiresultunit extends rental_uicommon
	{
		public $public_functions = array
		(
			'index'				=> true,
			'edit'				=> true,
			'query'				=> true,
			'remove_delegate'	=> true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::resultunit');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('delegates');
		}

		public function query()
		{
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			if($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else {
				$user_rows_per_page = 10;
			}
			// YUI variables for paging and sorting
			$start_index	= phpgw::get_var('startIndex', 'int');
			if(!$start_index) $start_index = 0;
			$num_of_objects	= phpgw::get_var('results', 'int', 'GET', $user_rows_per_page);
			$sort_field		= phpgw::get_var('sort', 'string', 'GET', 'identifier');
			$sort_ascending	= phpgw::get_var('dir') == 'desc' ? false : true;
			// Form variables
			$search_for 	= phpgw::get_var('query');
			$search_type	= phpgw::get_var('search_option');

			phpgwapi_cache::session_set('rental', 'resultunit_query', $search_for);
			phpgwapi_cache::session_set('rental', 'resultunit_search_type', $search_type);

			// Create an empty result set
			$result_count = 0;
			// get all result unit from fellesdata
			$bofelles = rental_bofellesdata::get_instance();

			$result_units = $bofelles->get_result_units_with_leader($start_index,$num_of_objects,$sort_field, $sort_ascending, $search_for, $search_type);

			$result_count = $bofelles->get_result_units_count($search_for, $search_type);

			foreach($result_units as &$unit){
				$unit['UNIT_NO_OF_DELEGATES'] = count(frontend_bofrontend::get_delegates($unit['ORG_UNIT_ID']));
			}

			$resultunit_data = array('results' => $result_units, 'total_records' => $result_count);

			$editable = phpgw::get_var('editable') == 'true' ? true : false;

			array_walk($resultunit_data['results'], array($this, 'add_actions'));

			return $this->yui_results($resultunit_data, 'total_records', 'results');
		}

		/**
		* View a list of all resultunits
		*/
		public function index()
		{
			$search_for = phpgw::get_var('search_for');
			if($search_for)
			{
				phpgwapi_cache::session_set('rental', 'resultunit_query', $search_for);
			}
			$this->render('resultunit_list.php');
		}


		public function add_actions(&$value)
		{
			if(($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$value['ajax'][] = false;
				$value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiresultunit.edit', 'id' => $value['ORG_UNIT_ID'], 'level' => $value['ORG_UNIT_LEVEL'])));
				$value['labels'][] = lang('edit');
			}
		}

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::'.lang('edit');
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];

			$unit_id = (int)phpgw::get_var('id');
			$unit_level = (int)phpgw::get_var('level');

			if (isset($unit_id) && $unit_id > 0 && $use_fellesdata) {

				$msglog['error']['msg'] = phpgw::get_var('error');
				$msglog['message']['msg'] = phpgw::get_var('message');

				if(isset($_POST['search']))
				{
					$username = phpgw::get_var('username');
					if(!isset($username))
					{
						$msglog['error']['msg'] = lang('lacking_username');
					}
					else
					{
						$account_id = frontend_bofrontend::delegate_exist($username);
						if($account_id)
						{
							$search_result = frontend_bofrontend::get_account_info($account_id);
							$msglog['message']['msg'] = lang('user_found_in_PE');
						}
						else
						{
							$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);
							if($fellesdata_user)
							{
								$search_result = $fellesdata_user;
								$msglog['message']['msg'] = lang('user_found_in_Fellesdata');
							}
							else
							{
								$msglog['error']['msg'] = lang('no_hits');
							}
						}
					}
				}
				else if(isset($_POST['add']))
				{
					$account_id = phpgw::get_var('account_id');
					//var_dump($account_id);

					$bofelles = rental_bofellesdata::get_instance();
					$unit = $bofelles->get_result_unit($unit_id, $unit_level);
					//var_dump($unit);
					if($account_id){
						$res = $this->add_delegate($account_id,$unit['ORG_UNIT_ID'],$unit['ORG_NAME']);
						//var_dump($res);
						if(!$res)
						{
							$msglog['error']['msg'] = lang('delegation_error');
						}
						else
						{
							$msglog['message']['msg'] = lang('delegation_successful');
						}
					}
					else{
						$msglog['error']['msg'] = lang('unknown_user');
					}
				}

				$bofelles = rental_bofellesdata::get_instance();
				$unit = $bofelles->get_result_unit_with_leader($unit_id, $unit_level);

				$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit_id);
				$unit['UNIT_NO_OF_DELEGATES'] = count($delegates_per_org_unit);

				$form_action = $GLOBALS['phpgw']->link('/index.php',array('menuaction' => 'rental.uiresultunit.edit', 'id' => $unit_id, 'level' => $unit_level));

				$this->render('resultunit.php', array ('unit' => $unit,
														'form_action' => $form_action,
														'search_result' => isset($search_result) ? $search_result : array(),
														'msglog' => $msglog,
														'cancel_link' => self::link(array('menuaction' => 'rental.uiresultunit.index', 'populate_form' => 'yes'))));
			}
		}

		public function add_delegate(int $account_id, $org_unit_id, $org_name)
		{
			$config	= CreateObject('phpgwapi.config','rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			if(!isset($account_id) || $account_id == '' && $use_fellesdata)
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

			$success = frontend_bofrontend::add_delegate($account_id, null, $org_unit_id, $org_name);
			if($success)
			{
				//Retrieve the usernames
				$user_account = $GLOBALS['phpgw']->accounts->get($account_id);
				$owner_account = $GLOBALS['phpgw']->accounts->get($GLOBALS['phpgw_info']['user']['account_id']);
				$user_name = $user_account->__get('lid');
				$owner_name = $owner_account->__get('lid');
				$org_name_string = $org_name;

				//If the usernames are set retrieve account data from Fellesdata
				if(isset($user_name) && $user_name != '' && $owner_name && $owner_name != '' && $use_fellesdata)
				{
					$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
					$fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);

					if($fellesdata_user && $fellesdata_owner)
					{
						//Send email notification to delegate
						$email = $fellesdata_user['email'];
						if(isset($email) && $email != '')
						{

							$title = lang('email_add_delegate_title');
							$message = lang('email_add_delegate_message',$fellesdata_user['firstname'],$fellesdata_user['lastname'],$fellesdata_owner['firstname'],$fellesdata_owner['lastname'],$org_name_string);
							frontend_bofrontend::send_system_message($email,$title,$message);
							return true;
						}
					}
				}
			}
			return false;
		}

		public function remove_delegate()
		{
			$unit_id = phpgw::get_var('id');
			$account_id = phpgw::get_var('account_id');

			$result = frontend_bofrontend::remove_delegate($account_id,null,$unit_id);

			$args = array('menuaction' => 'rental.uiresultunit.edit', 'id' => $unit_id);

			if($result){
				$args['message'] = lang('delegate_removed');
			}
			else{
				$args['error'] = lang('failed_removing_delegate');
			}
			$GLOBALS['phpgw']->redirect_link('/index.php', $args);
		}
	}
