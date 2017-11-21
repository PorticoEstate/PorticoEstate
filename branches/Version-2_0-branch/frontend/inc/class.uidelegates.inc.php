<?php
	phpgw::import_class('frontend.uicommon');

	class frontend_uidelegates extends frontend_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'remove_delegate' => true,
			'query' => true
		);

		public function __construct()
		{
			phpgwapi_cache::session_set('frontend', 'tab', $GLOBALS['phpgw']->locations->get_id('frontend', '.delegates'), $GLOBALS['phpgw_info']['user']['account_id']);
			parent::__construct();
		}

		public function index()
		{
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			if (isset($_POST['search']))
			{
				$username = phpgw::get_var('username');
				if (empty($username))
				{
					$msglog['error'][] = array('msg' => lang('lacking_username'));
				}
				else if ($username == $GLOBALS['phpgw_info']['user']['account_lid'])
				{
					$msglog['error'][] = array('msg' => lang('searching_for_self'));
				}
				else
				{
					$account_id = frontend_bofrontend::delegate_exist($username);
					if ($account_id)
					{
						$search = frontend_bofrontend::get_account_info($account_id);
						$msglog['message'][] = array('msg' => lang('user_found_in_PE'));
					}
					else
					{
						if ($use_fellesdata)
						{
							$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($username);

							if ($fellesdata_user)
							{
								$search = $fellesdata_user;
								$msglog['message'][] = array('msg' => lang('user_found_in_Fellesdata'));
							}
						}
						else
						{
							$msglog['error'][] = array('msg' => lang('no_hits'));
						}
					}
				}
			}
			else if (isset($_POST['add']))
			{
				$account_id = phpgw::get_var('account_id');
				$success = false;
				if ($use_fellesdata && !empty($account_id))
				{
					$org_units = frontend_bofellesdata::get_instance()->get_result_units($GLOBALS['phpgw_info']['user']['account_lid']);
					//Parameter to delegate access to only a single organisational unit
					$org_unit_id = $this->header_state['selected_org_unit'];

					foreach ($org_units as $org_unit)
					{
						if ($org_unit_id == 'all' || $org_unit['ORG_UNIT_ID'] == $org_unit_id)
						{
							//$curr_success = true;
							$res = $this->add_delegate($account_id, $org_unit['ORG_UNIT_ID'], $org_unit['ORG_NAME']);
							if ($res)
							{
								//$mail_contents[] = $res;
								$org_unit_names[] = $org_unit['ORG_NAME'];
							}
							else
							{
								$msglog['error'][] = array('msg' => lang('error_delegating_unit', $org_unit['ORG_NAME']));
							}

							$success = $success && $res;
						}
					}
				}
				if ($success)
				{
					//Retrieve the usernames
					$user_account = $GLOBALS['phpgw']->accounts->get($account_id);
					$owner_account = $GLOBALS['phpgw']->accounts->get($GLOBALS['phpgw_info']['user']['account_id']);
					$user_name = $user_account->__get('lid');
					$owner_name = $owner_account->__get('lid');
					$org_name_string = implode(',', $org_unit_names);

					//If the usernames are set retrieve account data from Fellesdata
					if (isset($user_name) && $user_name != '' && $owner_name && $owner_name != '')
					{
						$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
						$fellesdata_owner = frontend_bofellesdata::get_instance()->get_user($owner_name);

						if ($fellesdata_user && $fellesdata_owner)
						{
							//Send email notification to delegate
							$email = $fellesdata_user['email'];
							if (isset($email) && $email != '')
							{

								$title = lang('email_add_delegate_title');
								$message = lang('email_add_delegate_message', $fellesdata_user['firstname'], $fellesdata_user['lastname'], $fellesdata_owner['firstname'], $fellesdata_owner['lastname'], $org_name_string);
								frontend_bofrontend::send_system_message($email, $title, $message);
							}
						}
					}
					$msglog['message'][] = array('msg' => lang('delegation_successful'));
					/*
					  //send e-mail to user
					  $user_account = $GLOBALS['phpgw']->accounts->get($account_id);
					  $user_name = $user_account->__get('lid');
					  $fellesdata_user = frontend_bofellesdata::get_instance()->get_user($user_name);
					  if($fellesdata_user)
					  {
					  $email = $fellesdata_user['email'];
					  if(isset($email) && $email != '')
					  {
					  $title = lang('email_add_delegate_title');
					  $mail_content = implode(',',$mail_contents);
					  frontend_bofrontend::send_system_message($email,$title,$mail_content);
					  }
					  } */
				}
				else
				{
					$msglog['error'][] = array('msg' => lang('delegation_error'));
				}
			}
			else if (isset($_POST['remove']))
			{
				$account_id = phpgw::get_var('account_id');
				$result = frontend_bofrontend::remove_delegate($account_id, null, null);
				if ($result)
				{
					$msglog['message'][] = array('msg' => lang('remove_delegate_successful'));
				}
				else
				{
					$msglog['error'][] = array('msg' => lang('remove_delegate_error'));
				}
			}
			else if (isset($_POST['remove_specific']))
			{
				$account_id = phpgw::get_var('account_id');
				//Parameter to delegate access to only a single organisational unit
				$org_unit_id = $this->header_state['selected_org_unit'];
				$result = frontend_bofrontend::remove_delegate($account_id, null, $org_unit_id);
				if ($result)
				{
					$msglog['message'][] = array('msg' => lang('remove_delegate_successful'));
				}
				else
				{
					$msglog['error'][] = array('msg' => lang('remove_delegate_error'));
				}
			}

			$form_action = $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'frontend.uidelegates.index',
				'location_id' => $this->location_id));
			$delegates_per_org_unit = frontend_bofrontend::get_delegates($this->header_state['selected_org_unit']);
			$delegates_per_user = frontend_bofrontend::get_delegates(null, true);

			$number_of_delegates = count($delegates_per_org_unit);
			$number_of_user_delegates = count($delegates_per_user);

			$config = CreateObject('phpgwapi.config', 'frontend');
			$config->read();

			$delegateLimit = $config->config_data['delegate_limit'];
			if (!is_numeric($delegateLimit))
				$delegateLimit = 3;
			$error_message = lang('max %1 delegates', $delegateLimit);

			//$msglog = phpgwapi_cache::session_get('frontend','msgbox');
			phpgwapi_cache::session_clear('frontend', 'msgbox');

			$data = array(
				'header' => $this->header_state,
				'section' => array(
					'form_action' => $form_action,
					'tab_selected' => $this->tab_selected,
					'delegate' => $delegates_per_org_unit,
					'user_delegate' => $delegates_per_user,
					'number_of_delegates' => isset($number_of_delegates) ? $number_of_delegates : 0,
					'number_of_user_delegates' => isset($number_of_user_delegates) ? $number_of_user_delegates : 0,
					'search' => isset($search) ? $search : array(),
					'msgbox_data' => $GLOBALS['phpgw']->common->msgbox($GLOBALS['phpgw']->common->msgbox_data($msglog)),
					'delegate_limit' => $delegateLimit,
					'error_message' => $error_message,
					'tabs' => $this->tabs,
					'tabs_content' => $this->tabs_content
				)
			);

			self::render_template_xsl(array('delegate', 'datatable_inline', 'frontend'), $data);
		}

		public function query()
		{

		}

		public function add_delegate( int $account_id, $org_unit_id, $org_name )
		{
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();

			$use_fellesdata = $config->config_data['use_fellesdata'];
			if (!$use_fellesdata)
			{
				return;
			}
			if (!isset($account_id) || $account_id == '')
			{
				//User is only registered in Fellesdata
				$username = phpgw::get_var('username');
				$firstname = phpgw::get_var('firstname');
				$lastname = phpgw::get_var('lastname');
//				$password = 'TEst1234';
				$password = 'PEre' . mt_rand(100, mt_getrandmax()) . '&';

				$account_id = frontend_bofrontend::create_delegate_account($username, $firstname, $lastname, $password);

				if (isset($account_id) && !is_numeric($account_id))
				{
					return false;
				}
			}

			$owner_id = (int) $GLOBALS['phpgw_info']['user']['account_id'];

			return frontend_bofrontend::add_delegate($account_id, $owner_id, $org_unit_id, $org_name);
		}

		public function remove_delegate()
		{
			$account_id = phpgw::get_var('account_id');
			$owner_id = (int)phpgw::get_var('owner_id');

			frontend_bofrontend::remove_delegate($account_id, $owner_id, 0);
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'frontend.uidelegates.index'));
		}
	}