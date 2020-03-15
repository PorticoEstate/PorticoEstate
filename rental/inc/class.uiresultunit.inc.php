<?php
	phpgw::import_class('rental.uicommon');
	phpgw::import_class('rental.bofellesdata');
	phpgw::import_class('frontend.bofrontend');
	phpgw::import_class('frontend.bofellesdata');

	class rental_uiresultunit extends rental_uicommon
	{

		public $public_functions = array
			(
			'index' => true,
			'edit' => true,
			'query' => true,
			'remove_delegate' => true,
			'search_user' => true,
			'add' => true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::resultunit');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('delegates');
		}

		public function query()
		{
			if ($GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'] > 0)
			{
				$user_rows_per_page = $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			}
			else
			{
				$user_rows_per_page = 10;
			}

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int');
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$num_of_objects = (phpgw::get_var('length', 'int') <= 0) ? $user_rows_per_page : phpgw::get_var('length', 'int');
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'ORG_UNIT_ID';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = $search['value'];
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'unit_name');

			// Create an empty result set
			$result_count = 0;
			// get all result unit from fellesdata
			$bofelles = rental_bofellesdata::get_instance();

			$result_units = $bofelles->get_result_units_with_leader($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type);

			$result_count = $bofelles->get_result_units_count($search_for, $search_type);

			foreach ($result_units as &$unit)
			{
				$unit['UNIT_NO_OF_DELEGATES'] = count(frontend_bofrontend::get_delegates($unit['ORG_UNIT_ID']));
			}

			$result_data = array('results' => $result_units);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		/**
		 * View a list of all resultunits
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$appname = lang('delegates');
			$type = 'all_result_units';

			$data = array(
				'datatable_name' => $appname,
				'form' => array(
					'toolbar' => array(
						'item' => array(
							array
								(
								'type' => 'filter',
								'name' => 'search_option',
								'text' => lang('search_where'),
								'list' => array
									(
									array('id' => 'unit_name', 'name' => lang('unit_name')),
									array('id' => 'unit_leader', 'name' => lang('unit_leader'))
								)
							)
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uiresultunit.index',
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'ORG_UNIT_ID',
							'label' => lang('unit_id'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'ORG_UNIT_NAME',
							'label' => lang('unit_name'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'LEADER_FULLNAME',
							'label' => lang('unit_leader_name'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'UNIT_NO_OF_DELEGATES',
							'label' => lang('unit_no_of_delegates'),
							'className' => 'center',
							'sortable' => false,
							'hidden' => false
						)
					)
				)
			);

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'ORG_UNIT_ID'
					),
					array
						(
						'name' => 'level',
						'source' => 'ORG_UNIT_LEVEL'
					)
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'edit',
				'text' => lang('edit'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiresultunit.edit'
				)),
				'parameters' => json_encode($parameters)
			);


			self::render_template_xsl('datatable_jquery', $data);
		}
		/* public function add_actions(&$value)
		  {
		  if(($this->isExecutiveOfficer() || $this->isAdministrator()))
		  {
		  $value['ajax'][] = false;
		  $value['actions'][] = html_entity_decode(self::link(array('menuaction' => 'rental.uiresultunit.edit', 'id' => $value['ORG_UNIT_ID'], 'level' => $value['ORG_UNIT_LEVEL'])));
		  $value['labels'][] = lang('edit');
		  }
		  } */

		public function edit()
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('edit');
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];

			$unit_id = (int)phpgw::get_var('id');
			$unit_level = (int)phpgw::get_var('level');

			$datatable_def = array();

			$link_index = array(
				'menuaction' => 'rental.uiresultunit.index'
			);

			$tabletools = array();
			if (($this->isExecutiveOfficer() || $this->isAdministrator()) && $use_fellesdata)
			{
				$tabletools[] = array
					(
					'my_name' => 'delete',
					'text' => lang('delete'),
					'type' => 'custom',
					'custom_code' => "
							var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uiresultunit.remove_delegate',
						'id' => $unit_id,
						'phpgw_return_as' => 'json'
					)) . ";
							var parameters = " . json_encode(array('parameter' => array(array('name' => 'account_id',
								'source' => 'account_id'), array('name' => 'owner_id', 'source' => 'owner_id')))) . ";
							removeDelegate(oArgs, parameters);
						"
				);
			}

			$datatable_def[] = array
				(
				'container' => 'datatable-container_0',
				'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uidelegate.query',
						'unit_id' => $unit_id, 'type' => 'included_delegates', 'phpgw_return_as' => 'json'))),
				'ColumnDefs' => array(
					array('key' => 'account_lastname', 'label' => lang('lastname'), 'sortable' => true),
					array('key' => 'account_firstname', 'label' => lang('firstname'), 'sortable' => true)
				),
				'tabletools' => $tabletools,
				'config' => array(
					array('disableFilter' => true),
					array('disablePagination' => true)
				)
			);

			$tabs = array();
			$tabs['delegates'] = array('label' => lang('Delegates'), 'link' => '#delegates');
			$active_tab = 'delegates';

			$bofelles = rental_bofellesdata::get_instance();
			$unit = $bofelles->get_result_unit_with_leader($unit_id, $unit_level);

			$delegates_per_org_unit = frontend_bofrontend::get_delegates($unit_id);
			$unit['UNIT_NO_OF_DELEGATES'] = count($delegates_per_org_unit);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_index),
				'lang_search' => lang('search'),
				'lang_add' => lang('add'),
				'lang_cancel' => lang('cancel'),
				'value_org_unit_id' => $unit["ORG_UNIT_ID"],
				'value_org_unit_name' => $unit["ORG_UNIT_NAME"],
				'value_leader_fullname' => $unit["LEADER_FULLNAME"],
				'value_unit_no_of_delegates' => $unit["UNIT_NO_OF_DELEGATES"],
				'unit_id' => $unit_id,
				'unit_level' => $unit_level,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab)
			);

			self::add_javascript('rental', 'rental', 'resultunit.edit.js');
			self::render_template_xsl(array('resultunit', 'datatable_inline'), array('edit' => $data));
		}

		public function search_user()
		{
			$username = phpgw::get_var('username');
			$result = array();
			if (!isset($username))
			{
				$result['error']['msg'] = lang('lacking_username');
			}
			else
			{
				$account_id = frontend_bofrontend::delegate_exist($username);
				if ($account_id)
				{
					$search_result = frontend_bofrontend::get_account_info($account_id);
					$msg = lang('user_found_in_PE');
				}
				else
				{
					$search_result = frontend_bofellesdata::get_instance()->get_user($username);
					$msg = lang('user_found_in_Fellesdata');
				}

				if ($search_result)
				{
					$result['message']['msg'] = $msg;
					$result['data'] = $search_result;
				}
				else
				{
					$result['error']['msg'] = lang('no_hits');
				}
			}

			return $result;
		}

		public function add()
		{
			$unit_id = (int)phpgw::get_var('id');
			$unit_level = (int)phpgw::get_var('level');
			$account_id = phpgw::get_var('account_id');

			$bofelles = rental_bofellesdata::get_instance();
			$unit = $bofelles->get_result_unit($unit_id, $unit_level);

			$result = array();
			if ($account_id)
			{
				$res = $this->add_delegate($account_id, $unit['ORG_UNIT_ID'], $unit['ORG_NAME']);
				switch ($res)
				{
					case 1:
						$result['message']['msg'] = lang('delegation_successful');
						break;
					case 2:
						$result['message']['msg'] = lang('delegation_successful_message_sendt');
						break;
					default:
						$result['error']['msg'] = lang('delegation_error');
						break;
				}
			}
			else
			{
				$result['error']['msg'] = lang('unknown_user');
			}

			return $result;
		}

		public function add_delegate( int $account_id, $org_unit_id, $org_name )
		{
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			if (!isset($account_id) || $account_id == '' && $use_fellesdata)
			{
				//User is only registered in Fellesdata
				$username = phpgw::get_var('username');
				$firstname = phpgw::get_var('firstname');
				$lastname = phpgw::get_var('lastname');
				$password = 'TEst1234';

				$account_id = frontend_bofrontend::create_delegate_account($username, $firstname, $lastname, $password);

				if (isset($account_id) && !is_numeric($account_id))
				{
					return false;
				}
			}

			$success = frontend_bofrontend::add_delegate($account_id, 0, $org_unit_id, $org_name);
			$ret = 0;
			if ($success)
			{
				$ret = 1;
				//Retrieve the usernames
				$user_account = $GLOBALS['phpgw']->accounts->get($account_id);
				$owner_account = $GLOBALS['phpgw']->accounts->get($GLOBALS['phpgw_info']['user']['account_id']);
				$user_name = $user_account->__get('lid');
				$owner_name = $owner_account->__get('lid');
				$org_name_string = $org_name;

				//If the usernames are set retrieve account data from Fellesdata
				if (isset($user_name) && $user_name != '' && $owner_name && $owner_name != '' && $use_fellesdata)
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
							$ret = 2;
						}
					}
				}
			}
			return $ret;
		}

		public function remove_delegate()
		{
			$unit_id = phpgw::get_var('id');
			$list_account_id = phpgw::get_var('account_id');

			$message = array();
			foreach ($list_account_id as $account_id)
			{
				$result = frontend_bofrontend::remove_delegate($account_id, null, $unit_id);
				if ($result)
				{
					$message['message'][] = array('msg' => lang('delegate_removed'));
				}
				else
				{
					$message['error'][] = array('msg' => lang('failed_removing_delegate'));
				}
			}

			return $message;
		}
	}