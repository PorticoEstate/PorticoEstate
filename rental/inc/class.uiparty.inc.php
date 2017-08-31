<?php
	phpgw::import_class('rental.uicommon');

	phpgw::import_class('rental.soparty');
	phpgw::import_class('rental.socontract');
	phpgw::import_class('rental.sodocument');
	phpgw::import_class('rental.bofellesdata');
	include_class('rental', 'party', 'inc/model/');
	include_class('rental', 'unit', 'inc/model/');
	include_class('rental', 'location_hierarchy', 'inc/locations/');

	class rental_uiparty extends rental_uicommon
	{

		public $public_functions = array
			(
			'add' => true,
			'save' => true,
			'edit' => true,
			'index' => true,
			'query' => true,
			'view' => true,
			'download' => true,
			'download_agresso' => true,
			'sync' => true,
			'update_all_org_enhet_id' => true,
			'syncronize_party' => true,
			'syncronize_party_name' => true,
			'create_user_based_on_email' => true,
			'get_synchronize_party_info' => true,
			'delete_party' => true
		);

		public function __construct()
		{
			parent::__construct();

			self::set_active_menu('rental::parties');
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang('parties');
		}

		private function _get_filters()
		{
			$filters = array();

			$search_option = array
				(
				array('id' => 'all', 'name' => lang('all')),
				array('id' => 'name', 'name' => lang('name')),
				array('id' => 'address', 'name' => lang('address')),
				array('id' => 'identifier', 'name' => lang('identifier')),
				array('id' => 'customer_id', 'name' => lang('customer id') . ' (Agresso)'),
				array('id' => 'reskontro', 'name' => lang('reskontro')),
				array('id' => 'result_unit_number', 'name' => lang('result_unit_number')),
			);
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'search_option',
				'text' => lang('search option'),
				'list' => $search_option
			);

			$types = rental_socontract::get_instance()->get_fields_of_responsibility();
			$party_types = array();
			array_unshift($party_types, array('id' => 'all', 'name' => lang('all')));
			foreach ($types as $id => $label)
			{
				$party_types[] = array('id' => $id, 'name' => lang($label));
			}
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'party_type',
				'text' => lang('part_of_contract'),
				'list' => $party_types
			);

			$status_option = array
				(
				array('id' => 'all', 'name' => lang('not_available_nor_hidden')),
				array('id' => 'active', 'name' => lang('available_for_pick')),
				array('id' => 'inactive', 'name' => lang('hidden_for_pick')),
			);
			$filters[] = array
				(
				'type' => 'filter',
				'name' => 'active',
				'text' => lang('marked_as'),
				'list' => $status_option
			);

			return $filters;
		}

		/**
		 * (non-PHPdoc)
		 * @see rental/inc/rental_uicommon#query()
		 */
		public function query()
		{
			$length = phpgw::get_var('length', 'int');

			$user_rows_per_page = $length > 0 ? $length : $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs'];
			$num_of_objects = $length == -1 ? 0 : $user_rows_per_page;

			$search = phpgw::get_var('search');
			$order = phpgw::get_var('order');
			$draw = phpgw::get_var('draw', 'int', 'REQUEST', 1);
			$columns = phpgw::get_var('columns');

			$start_index = phpgw::get_var('start', 'int', 'REQUEST', 0);
			$sort_field = ($columns[$order[0]['column']]['data']) ? $columns[$order[0]['column']]['data'] : 'identifier';
			$sort_ascending = ($order[0]['dir'] == 'desc') ? false : true;
			// Form variables
			$search_for = (is_array($search)) ? $search['value'] : $search;
			$search_type = phpgw::get_var('search_option', 'string', 'REQUEST', 'all');
			$party_type = phpgw::get_var('party_type', 'string', 'REQUEST', 'all');
			$active = phpgw::get_var('active', 'string', 'REQUEST', 'all');
			$export = phpgw::get_var('export', 'bool');

			$editable = phpgw::get_var('editable', 'bool');

			// Create an empty result set
			$result_objects = array();
			$result_count = 0;

			if ($export)
			{
				$num_of_objects = 0;
			}

			//Retrieve a contract identifier and load corresponding contract
			$contract_id = phpgw::get_var('contract_id');
			if (isset($contract_id))
			{
				$contract = rental_socontract::get_instance()->get_single($contract_id);
			}

			//Retrieve the type of query and perform type specific logic
			$type = phpgw::get_var('type');

			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();
			$use_fellesdata = $config->config_data['use_fellesdata'];
			switch ($type)
			{
				case 'included_parties': // ... get all parties incolved in the contract
					$filters = array('contract_id' => $contract_id);
					break;
				case 'not_included_parties': // ... get all parties not included in the contract
					$filters = array('not_contract_id' => $contract_id, 'party_type' => $party_type);
					break;
				case 'sync_parties':
				case 'sync_parties_res_unit':
				case 'sync_parties_identifier':
				case 'sync_parties_org_unit':
					$filters = array('sync' => $type, 'party_type' => $party_type, 'active' => $active);
					if ($use_fellesdata)
					{
						$bofelles = rental_bofellesdata::get_instance();
					}
					break;
				default: // ... get all parties of a given type
					phpgwapi_cache::session_set('rental', 'party_query', $search_for);
					phpgwapi_cache::session_set('rental', 'party_search_type', $search_type);
					phpgwapi_cache::session_set('rental', 'party_type', $party_type);
					phpgwapi_cache::session_set('rental', 'party_status', $active);
					$filters = array('party_type' => $party_type, 'active' => $active);
					break;
			}

			$result_objects = rental_soparty::get_instance()->get($start_index, $num_of_objects, $sort_field, $sort_ascending, $search_for, $search_type, $filters);
			$result_count = rental_soparty::get_instance()->get_count($search_for, $search_type, $filters);

			// Create an empty row set
			$rows = array();
			foreach ($result_objects as $party)
			{
				if (isset($party))
				{
					$serialized = $party->serialize($contract);
					if ($use_fellesdata)
					{
						$sync_data = $party->get_sync_data();
						if ($type == 'sync_parties')
						{
							$unit_name_and_id = $bofelles->responsibility_id_exist($sync_data['responsibility_id']);
						}
						else if ($type == 'sync_parties_res_unit')
						{
							$unit_name_and_id = $bofelles->result_unit_exist($sync_data['result_unit_number']);
						}
						else if ($type == 'sync_parties_identifier')
						{
							$unit_name_and_id = $bofelles->result_unit_exist($party->get_identifier());
						}
						else if ($type == 'sync_parties_org_unit')
						{
							$unit_name_and_id = $bofelles->org_unit_exist($sync_data['org_enhet_id']);
						}

						if (isset($unit_name_and_id) && $unit_name_and_id)
						{
							$unit_id = $unit_name_and_id['UNIT_ID'];
							$unit_name = $unit_name_and_id['UNIT_NAME'];

							if (isset($unit_id) && is_numeric($unit_id))
							{
								$serialized['org_unit_name'] = isset($unit_name) ? $unit_name : lang('no_name');
								$serialized['org_unit_id'] = $unit_id;
							}

							// Fetches data from Fellesdata
							$org_unit_id = $sync_data['org_enhet_id'];

							$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
							$org_department = $bofelles->get_department_for_org_unit($org_unit_id);

							$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
							$org_email = $org_unit_with_leader['ORG_EMAIL'];
							$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];
							$dep_org_name = $org_department['DEP_ORG_NAME'];

							// Fields are displayed in syncronization table
							$serialized['org_unit_name'] = $org_name;
							$serialized['unit_leader'] = $unit_leader_fullname;
							$serialized['org_email'] = $org_email;
							$serialized['dep_org_name'] = $dep_org_name;
						}
					}

					//check if party is a part of a contract
					$party_in_contract = rental_soparty::get_instance()->has_contract($party->get_id());
					$serialized['party_in_contract'] = $party_in_contract ? true : false;

					if (!$export)
					{
						$serialized['other_operations'] = $this->get_actions($serialized, array(// Parameters (non-object pointers)
							$contract_id, // [1] The contract id
							$type, // [2] The type of query
							isset($contract) ? $contract->serialize() : null, // [3] Serialized contract
							$editable, // [4] Editable flag
							$this->type_of_user   // [5] User role
						));
					}

					$rows[] = $serialized;
				}
			}

			if ($export)
			{
				return $rows;
			}

			$result_data = array('results' => $rows);
			$result_data['total_records'] = $result_count;
			$result_data['draw'] = $draw;

			return $this->jquery_results($result_data);
		}

		private function get_sync_candidates()
		{
			$config_data = CreateObject('phpgwapi.config', 'rental')->read();
			$filters = array('sync' => 'sync_parties_org_unit', 'party_type' => 'all', 'active' => 'all');
			if( !$config_data['use_fellesdata'])
			{
				return array();
			}

			$bofelles = rental_bofellesdata::get_instance();
			$result_objects = rental_soparty::get_instance()->get(0, 0, '', true, '', 'all', $filters);
			$candidates = array();
			foreach ($result_objects as $party)
			{
				if (isset($party))
				{
					$sync_data = $party->get_sync_data();
					$unit_name_and_id = $bofelles->org_unit_exist($sync_data['org_enhet_id']);

					if (isset($unit_name_and_id) && $unit_name_and_id)
					{
						$unit_id = $unit_name_and_id['UNIT_ID'];
						$unit_name = $unit_name_and_id['UNIT_NAME'];

						if (isset($unit_id) && is_numeric($unit_id))
						{
							$candidates[] = array(
								'party_id' => $party->get_id(),
								'org_unit_id' => $unit_id
							);
						}
					}
				}
			}

			return $candidates;
		}

		/*
		 * One time job for updating the parties with no org_enhet_id.
		 * The org_enhet_id will be set according to the suggestions given in
		 * the synchronize function in the rental model UI.
		 *
		 */

		public function update_all_org_enhet_id()
		{
			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();

			$use_fellesdata = $config->config_data['use_fellesdata'];
			if (!$use_fellesdata)
			{
				return;
			}
			$bofelles = rental_bofellesdata::get_instance();

			$parties = rental_soparty::get_instance()->get();
			$result_count = rental_soparty::get_instance()->get_count();

			echo "Total number of parties: {$result_count}";

			if (($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$count = 0;
				$count_result_unit_number = 0;
				$count_identifier = 0;
				$count_responsibility = 0;

				foreach ($parties as $party)
				{
					$unit_found = false;
					$fellesdata = NULL;

					if (isset($party))
					{
						$sync_data = $party->get_sync_data();

						$fellesdata = $bofelles->result_unit_exist($sync_data['result_unit_number'], 4);
						if ($fellesdata)
						{
							echo "Unit id found {$fellesdata['UNIT_ID']} by result unit number check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
							$count_result_unit_number++;
						}
						else
						{
							$fellesdata = $bofelles->result_unit_exist($party->get_identifier(), 4);
							if ($fellesdata)
							{
								echo "Unit id found {$fellesdata['UNIT_ID']} by identifier check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
								$count_identifier++;
							}
							else
							{
								$fellesdata = $bofelles->responsibility_id_exist($sync_data['responsibility_id']);
								if ($fellesdata)
								{
									echo "Unit id found {$fellesdata['UNIT_ID']} by responsibility id check. The unit name is {$fellesdata['UNIT_NAME']}<br />";
									$count_responsibility++;
								}
							}
						}

						if ($fellesdata && isset($fellesdata['UNIT_ID']) && is_numeric($fellesdata['UNIT_ID']))
						{
							// We found a match, so store the new connection
							$party->set_org_enhet_id($fellesdata['UNIT_ID']);
						}
						else
						{
							// No match was found. Set the connection to NULL
							$party->set_org_enhet_id(NULL);
						}
						rental_soparty::get_instance()->store($party);
					}
				}

				echo "Number of parties found through result unit number {$count_result_unit_number}<br />";
				echo "Number of parties found through identifier {$count_identifier}<br />";
				echo "Number of parties found through responsibility id {$count_responsibility}<br />";
				echo "Number of parties that have been updated {$count}<br />";
			}
		}

		/**
		 * Add action links for the context menu of the list item
		 *
		 * @param $value pointer to
		 * @param $key ?
		 * @param $params [composite_id, type of query, contract editable]
		 */
		public function get_actions( $value, $params )
		{
			// Get parameters
			$contract_id = $params[0];
			$type = $params[1];
			$serialized_contract = $params[2];
			$editable = $params[3];
			$user_is = $params[4];
			$actions = array();

			switch ($type)
			{
				case 'included_parties':
					if ($editable)
					{
						if ($value['id'] != $serialized_contract['payer_id'])
						{
							$url = html_entity_decode(self::link(array('menuaction' => 'rental.uicontract.set_payer',
									'party_id' => $value['id'], 'contract_id' => $params[0], 'phpgw_return_as' => 'json')));
							$actions[] = '<a onclick="setPayer(\'' . $url . '\')">' . lang('set_payer') . '</a>';
						}
					}
					break;
				case 'not_included_parties':
					break;
				default:

					if ($user_is[ADMINISTRATOR] || $user_is[EXECUTIVE_OFFICER])
					{
						if ((isset($value['party_in_contract']) && $value['party_in_contract'] == false) && (!isset($value['org_enhet_id']) || $value['org_enhet_id'] == ''))
						{
							$url1 = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.delete_party',
									'id' => $value['id'], 'phpgw_return_as' => 'json')));
							$actions[] = '<a onclick="onDelete_party(\'' . $url1 . '\')">' . lang('delete') . '</a>';
						}

						if (isset($value['org_enhet_id']) && $value['org_enhet_id'] != '')
						{
							$url2 = html_entity_decode(self::link(array('menuaction' => 'frontend.uihelpdesk.index',
									'org_enhet_id' => $value['org_enhet_id'])));
							$actions[] = '<a href="' . $url2 . '">' . lang('frontend_access') . '</a>';

							$url3 = html_entity_decode(self::link(array('menuaction' => 'rental.uiparty.syncronize_party',
									'org_enhet_id' => $value['org_enhet_id'], 'party_id' => $value['id'], 'phpgw_return_as' => 'json')));
							$actions[] = '<a onclick="onSyncronize_party(\'' . $url3 . '\')">' . lang('syncronize_party') . '</a>';
						}
					}
			}

			return implode(' | ', $actions);
		}

		/**
		 * Public method. View all contracts.
		 */
		public function index()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$editable = phpgw::get_var('editable', 'bool');
			$user_is = $this->type_of_user;

			self::set_active_menu('rental::parties');
			$appname = lang('parties');
			$type = 'all_parties';

			$function_msg = lang('list %1', $appname);

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array(
						)
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uiparty.index',
						'editable' => ($editable) ? 1 : 0,
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'rental.uiparty.download',
						'type' => $type,
						'export' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'new_item' => self::link(array('menuaction' => 'rental.uiparty.add')),
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'identifier',
							'label' => lang('identifier'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'customer_id',
							'label' => lang('customer id') . ' (Agresso)',
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'name',
							'label' => lang('name'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'address',
							'label' => lang('address'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			array_push($data['datatable']['field'], array("key" => "other_operations", "label" => lang('other operations'),
				"sortable" => false, "hidden" => false, "className" => 'dt-center all'));

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'download_agresso',
				'text' => lang('Download Agresso import file'),
				'type' => 'custom',
				'className'	=> 'download', // If there is a className - the button is not per record, but global for the table
				'custom_code' => "
					var oArgs = " . json_encode(array(
					'menuaction' => 'rental.uiparty.download_agresso',
					'export' => true
				)) . ";
					downloadAgresso(oArgs);
				"
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiparty.view'
				)),
				'parameters' => json_encode($parameters)
			);

			if ($user_is[ADMINISTRATOR] || $user_is[EXECUTIVE_OFFICER])
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uiparty.edit'
					)),
					'parameters' => json_encode($parameters)
				);
			}

			$alertMessage_deleteParty = '"Du er i ferd med å slette en kontraktspart.\n\n Operasjonen kan ikke angres.\n\n Vil du gjøre dette?";';
			$alertMessage_syncParty = '"Du er i ferd med å overskrive data med informasjon hentet fra Fellesdata.\n\n Følgende felt vil bli overskrevet: Foretak, Avdeling, Enhetsleder, Epost. \n\n Vil du gjøre dette?";';

			$jscode = <<<JS

				var confirm_msg_sync = $alertMessage_syncParty
				var confirm_msg_delete = $alertMessage_deleteParty

JS;

			$GLOBALS['phpgw']->js->add_code('', $jscode);

			self::add_javascript('rental', 'rental', 'party.sync.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		/**
		 * Public method. Forwards the user to edit mode.
		 */
		public function add()
		{
			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit'));
		}

		/**
		 * Public method. Called when a user wants to view information about a party.
		 * @param HTTP::id	the party ID
		 */
		public function view()
		{
			$party_id = (int)phpgw::get_var('id');

			if (isset($party_id) && $party_id > 0)
			{
				$party = rental_soparty::get_instance()->get_single($party_id);
			}
			else
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('invalid_request'));
			}

			if (isset($party) && $party->has_permission(PHPGW_ACL_READ))
			{
				$this->edit(array(), $mode = 'view');
			}
			else
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_view_party'));
			}
		}

		/**
		 * Public method. Called when user wants to edit a contract party.
		 * @param HTTP::id	the party ID
		 */
		public function edit( $values = array(), $mode = 'edit' )
		{
			$GLOBALS['phpgw_info']['flags']['app_header'] .= '::' . lang($mode);

			$party_id = (int)phpgw::get_var('id');

			if ($mode == 'edit')
			{
				// Retrieve the party object or create a new one if correct permissions
				if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
				{
					phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit'));
				}
			}

			if (isset($party_id) && $party_id > 0)
			{
				$party = rental_soparty::get_instance()->get_single($party_id);
			}
			else
			{
				$party = new rental_party();
			}

			$config = CreateObject('phpgwapi.config', 'rental');
			$config->read();

			$datatable_def = array();

			$link_index = array
				(
				'menuaction' => 'rental.uiparty.index',
				'populate_form' => 'yes'
			);

			$link_sync_info = array
				(
				'menuaction' => 'rental.uiparty.get_synchronize_party_info',
				'phpgw_return_as' => 'json'
			);

			$tabs = array();
			$tabs['details'] = array('label' => lang('Details'), 'link' => '#details');
			$active_tab = 'details';

			if ($party_id)
			{
				$GLOBALS['phpgw']->jqcal->add_listener('status_date');

				$tabletools_contracts[] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.edit',
						'initial_load' => 'no'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools_contracts[] = array
					(
					'my_name' => 'copy',
					'text' => lang('copy'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.copy_contract'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools_contracts[] = array
					(
					'my_name' => 'show',
					'text' => lang('show'),
					'action' => self::link(array(
						'menuaction' => 'rental.uicontract.view',
						'initial_load' => 'no'
					)),
					'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
								'source' => 'id'))))
				);

				$tabletools_contracts[] = array
					(
					'my_name' => 'download_contracts',
					'text' => lang('download'),
					'type' => 'custom',
					'custom_code' => "
						var oArgs = " . json_encode(array(
						'menuaction' => 'rental.uicontract.download',
						'party_id' => $party_id,
						'type' => 'contracts_part',
						'export' => true
					)) . ";
						downloadContracts(oArgs);
					"
				);

				$tabs['contracts'] = array('label' => lang('Contracts'), 'link' => '#contracts');
				$datatable_def[] = array
					(
					'container' => 'datatable-container_0',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uicontract.query',
							'editable' => 1, 'type' => 'contracts_part', 'party_id' => $party_id,
							'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'old_contract_id', 'label' => lang('contract_id'), 'sortable' => true),
						array('key' => 'date_start', 'label' => lang('date_start'), 'sortable' => true),
						array('key' => 'date_end', 'label' => lang('date_end'), 'sortable' => true),
						array('key' => 'type', 'label' => lang('title'), 'sortable' => false),
						array('key' => 'composite', 'label' => lang('composite'), 'sortable' => false),
						array('key' => 'term_label', 'label' => lang('billing_term'), 'sortable' => true),
						array('key' => 'total_price', 'label' => lang('total_price'), 'sortable' => false,
							'className' => 'right', 'formatter' => 'formatterPrice'),
						array('key' => 'rented_area', 'label' => lang('area'), 'sortable' => false,
							'className' => 'right', 'formatter' => 'formatterArea'),
						array('key' => 'contract_status', 'label' => lang('contract_status'), 'sortable' => false),
						array('key' => 'contract_notification_status', 'label' => lang('notification_status'),
							'sortable' => false)
					),
					'tabletools' => $tabletools_contracts,
					'config' => array(
						array('disableFilter' => true)
					)
				);

				$tabs['documents'] = array('label' => lang('Documents'), 'link' => '#documents');
				$tabletools_documents = array();
				$tabletools_documents[] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => self::link(array(
					'menuaction' => 'rental.uidocument.view'
				)),
				'parameters' => json_encode(array('parameter' => array(array('name' => 'id',
							'source' => 'id'))))
				);
				if ($mode == 'edit')
				{
					$tabletools_documents[] = array
						(
						'my_name' => 'delete',
						'text' => lang('remove'),
						'type' => 'custom',
						'custom_code' => "
							var oArgs = " . json_encode(array(
							'menuaction' => 'rental.uidocument.delete',
							'phpgw_return_as' => 'json'
						)) . ";
							var parameters = " . json_encode(array('parameter' => array(array('name' => 'id',
									'source' => 'id')))) . ";
							removeDocument(oArgs, parameters);
						"
					);
				}

				$datatable_def[] = array
					(
					'container' => 'datatable-container_1',
					'requestUrl' => json_encode(self::link(array('menuaction' => 'rental.uidocument.query',
							'editable' => 1, 'type' => 'documents_for_party', 'party_id' => $party_id,
							'phpgw_return_as' => 'json'))),
					'ColumnDefs' => array(
						array('key' => 'title', 'label' => lang('title'), 'sortable' => true),
						array('key' => 'type', 'label' => lang('type'), 'sortable' => true),
						array('key' => 'name', 'label' => lang('name'), 'sortable' => true)
					),
					'tabletools' => $tabletools_documents,
					'config' => array(
						array('disableFilter' => true),
						array('disablePagination' => true)
					)
				);

				$search_contract_options = array
					(
					array('id' => 'all', 'name' => lang('all')),
					array('id' => 'id', 'name' => lang('contract_id')),
					array('id' => 'party_name', 'name' => lang('party_name')),
					array('id' => 'composite', 'name' => lang('composite_name')),
					array('id' => 'composite_address', 'name' => lang('composite_address')),
					array('id' => 'location_id', 'name' => lang('object_number'))
				);

				$status_options = array
					(
					array('id' => 'all', 'name' => lang('all')),
					array('id' => 'under_planning', 'name' => lang('under_planning')),
					array('id' => 'active', 'name' => lang('active_plural')),
					array('id' => 'under_dismissal', 'name' => lang('under_dismissal')),
					array('id' => 'ended', 'name' => lang('ended'))
				);

				$field_of_responsibility = rental_socontract::get_instance()->get_fields_of_responsibility();
				$field_of_responsibility_options = array();
				array_unshift($field_of_responsibility_options, array('id' => 'all', 'name' => lang('all')));
				foreach ($field_of_responsibility as $id => $label)
				{
					$field_of_responsibility_options[] = array('id' => $id, 'name' => lang($label));
				}

				$link_upload_document = json_encode(self::link(array('menuaction' => 'rental.uidocument.add',
						'party_id' => $party_id, 'phpgw_return_as' => 'json')));

				/*				 * ***************************** document filters */
				$document_types = rental_sodocument::get_instance()->get_document_types();
				$document_types_options = array();
				foreach ($document_types as $id => $label)
				{
					$document_types_options[] = array('id' => $id, 'name' => lang($label));
				}

				$document_search_options[] = array('id' => 'all', 'name' => lang('all'));
				$document_search_options[] = array('id' => 'title', 'name' => lang('document_title'));
				$document_search_options[] = array('id' => 'name', 'name' => lang('document_name'));
				/*				 * ******************************************************************************** */
			}

			$party_org_enhet_id = $party->get_org_enhet_id();
			$valid_email = 0;
			$organization_options = array();

			if ($mode == 'view')
			{
				if (!empty($party_org_enhet_id))
				{
					$result_unit = rental_bofellesdata::get_instance()->get_result_unit($party_org_enhet_id);
					$organization_name = $result_unit['ORG_NAME'];
				}
				else
				{
					$organization_name = lang('no_party_location');
				}
			}
			else
			{
				$result_units = rental_bofellesdata::get_instance()->get_result_units();
				$organization_options[] = array('id' => '', 'name' => lang('no_party_location'),
					'selected' => 0);
				foreach ($result_units as $result_unit)
				{
					$selected = ($result_unit['ORG_UNIT_ID'] == $party_org_enhet_id) ? 1 : 0;
					$organization_options[] = array('id' => $result_unit['ORG_UNIT_ID'], 'name' => $result_unit['UNIT_ID'] . ' - ' . $result_unit['ORG_UNIT_NAME'],
						'selected' => $selected);
				}

				$email = $party->get_email();
				$email_validator = CreateObject('phpgwapi.EmailAddressValidator');
				if ($email_validator->check_email_address($email) && !$GLOBALS['phpgw']->accounts->exists($email))
				{
					$valid_email = 1;
					$link_create_user = array
						(
						'menuaction' => 'rental.uiparty.create_user_based_on_email',
						'id' => $party_id
					);
				}
			}

			$alertMessage_get_syncData = '"Du må velge organisasjonsenhet før du kan synkronisere";';
			$jscode = <<<JS
				var msg_get_syncData = $alertMessage_get_syncData
				var thousandsSeparator = '$this->thousandsSeparator';
				var decimalSeparator = '$this->decimalSeparator';
				var decimalPlaces = '$this->decimalPlaces';
				var currency_suffix = '$this->currency_suffix';
				var area_suffix = '$this->area_suffix';
JS;
			$GLOBALS['phpgw']->js->add_code('', $jscode);

			$data = array
				(
				'datatable_def' => $datatable_def,
				'tabs' => phpgwapi_jquery::tabview_generate($tabs, $active_tab),
				'form_action' => $GLOBALS['phpgw']->link('/index.php', array('menuaction' => 'rental.uiparty.save')),
				'cancel_url' => $GLOBALS['phpgw']->link('/index.php', $link_index),
				'lang_save' => lang('save'),
				'lang_sync_data' => lang('get_sync_data'),
				'lang_cancel' => lang('cancel'),
				'party_id' => $party_id,
				'value_name' => $party->get_name(),
				'value_identifier' => $party->get_identifier(),
				'value_customer_id' => $party->get_customer_id(),
				'value_firstname' => $party->get_first_name(),
				'value_lastname' => $party->get_last_name(),
				'value_job_title' => $party->get_title(),
				'value_company' => $party->get_company_name(),
				'value_department' => $party->get_department(),
				'value_address1' => $party->get_address_1(),
				'value_address2' => $party->get_address_2(),
				'value_postal_code' => $party->get_postal_code(),
				'value_place' => $party->get_place(),
				'is_inactive_party' => $party->is_inactive() ? 1 : 0,
				'value_account_number' => $party->get_account_number(),
				'value_phone' => $party->get_phone(),
				'value_mobile_phone' => $party->get_mobile_phone(),
				'value_fax' => $party->get_fax(),
				'value_email' => $party->get_email(),
				'value_url' => $party->get_url(),
				'value_unit_leader' => $party->get_unit_leader(),
				'value_comment' => $party->get_comment(),
				'sync_info_url' => $GLOBALS['phpgw']->link('/index.php', $link_sync_info),
				'use_fellesdata' => $config->config_data['use_fellesdata'],
				'list_organization' => array('options' => $organization_options),
				'value_organization' => $organization_name,
				'valid_email' => $valid_email,
				'link_create_user' => $GLOBALS['phpgw']->link('/index.php', $link_create_user),
				'list_search_contract' => array('options' => $search_contract_options),
				'list_status' => array('options' => $status_options),
				'list_field_of_responsibility' => array('options' => $field_of_responsibility_options),
				'list_document_types' => array('options' => $document_types_options),
				'list_document_search' => array('options' => $document_search_options),
				'link_upload_document' => $link_upload_document,
				'validator' => phpgwapi_jquery::formvalidator_generate(array('location',
					'date',
					'security', 'file'))
			);

			self::add_javascript('rental', 'rental', 'party.edit.js');
			phpgwapi_jquery::load_widget('numberformat');
			self::render_template_xsl(array('party', 'datatable_inline'), array($mode => $data));
		}

		public function save()
		{
			if (!($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit'));
			}

			$party_id = (int)phpgw::get_var('id');

			if (isset($party_id) && $party_id > 0)
			{
				$party = rental_soparty::get_instance()->get_single($party_id);
			}
			else
			{
				$party = new rental_party();
			}

			if (isset($party)) // If a party object is created
			{
				// ... set all parameters
				$party->set_identifier(phpgw::get_var('identifier'));
				$party->set_customer_id(phpgw::get_var('customer_id', 'int'));
				$party->set_first_name(phpgw::get_var('firstname'));
				$party->set_last_name(phpgw::get_var('lastname'));
				$party->set_title(phpgw::get_var('title'));
				$party->set_company_name(phpgw::get_var('company_name'));
				$party->set_department(phpgw::get_var('department'));
				$party->set_address_1(phpgw::get_var('address1'));
				$party->set_address_2(phpgw::get_var('address2'));
				$party->set_postal_code(phpgw::get_var('postal_code'));
				$party->set_place(phpgw::get_var('place'));
				$party->set_phone(phpgw::get_var('phone'));
				$party->set_mobile_phone(phpgw::get_var('mobile_phone'));
				$party->set_fax(phpgw::get_var('fax'));
				$party->set_email(phpgw::get_var('email'));
				$party->set_url(phpgw::get_var('url'));
				$party->set_account_number(phpgw::get_var('account_number'));
				$party->set_reskontro(phpgw::get_var('reskontro'));
				$party->set_is_inactive(phpgw::get_var('is_inactive') == 'on' ? true : false);
				$party->set_comment(phpgw::get_var('comment'));
				//$party->set_location_id(phpgw::get_var('location_id'));
				$party->set_org_enhet_id(phpgw::get_var('org_enhet_id'));
				$party->set_unit_leader(phpgw::get_var('unit_leader'));

				if (rental_soparty::get_instance()->store($party)) // ... and then try to store the object
				{
					phpgwapi_cache::message_set(lang('messages_saved_form'), 'message');
					$party_id = $party->get_id();
				}
				else
				{
					phpgwapi_cache::message_set(lang('messages_form_error'), 'error');
				}
			}

			$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'rental.uiparty.edit',
				'id' => $party_id));
		}

		public function download()
		{
			$list = $this->query();

			$keys = array();

			if (count($list[0]) > 0)
			{
				foreach ($list[0] as $key => $value)
				{
					if (!is_array($value))
					{
						array_push($keys, $key);
					}
				}
			}

			// Remove newlines from output
			$count = count($list);
			for ($i = 0; $i < $count; $i++)
			{
				foreach ($list[$i] as $key => &$data)
				{
					$data = str_replace(array("\n", "\r\n", "<br>"), '', $data);
				}
			}

			// Use keys as headings
			$headings = array();
			$count_keys = count($keys);
			for ($j = 0; $j < $count_keys; $j++)
			{
				array_push($headings, lang($keys[$j]));
			}

			$property_common = CreateObject('property.bocommon');
			$property_common->download($list, $keys, $headings);
		}

		public function download_agresso()
		{
			$browser = CreateObject('phpgwapi.browser');
			$browser->content_header('export.txt', 'text/plain');
			print rental_soparty::get_instance()->get_export_data();
		}

		public function sync()
		{
			if (phpgw::get_var('phpgw_return_as') == 'json')
			{
				return $this->query();
			}

			$editable = phpgw::get_var('editable', 'bool');
			$sync_job = phpgw::get_var('sync', 'string', 'GET');
			$contract_id = phpgw::get_var('contract_id');
			$user_is = $this->type_of_user;

			switch ($sync_job)
			{
				case 'resp_and_service':
					self::set_active_menu('rental::parties::sync::sync_resp_and_service');
					$appname = lang('sync_parties_service_and_responsibiity');
					$type = 'sync_parties';
					$extra_cols = array(
						array("key" => "responsibility_id", "label" => lang('responsibility_id'),
							"sortable" => false,
							"hidden" => false),
						array("key" => "sync_message", "label" => lang('sync_message'), "sortable" => false,
							"hidden" => false),
						array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable" => false,
							"hidden" => false)
					);
					break;
				case 'res_unit_number':
					self::set_active_menu('rental::parties::sync::sync_res_units');
					$appname = lang('sync_parties_result_unit_number');
					$type = 'sync_parties_res_unit';
					$extra_cols = array(
						array("key" => "result_unit_number", "label" => lang('result_unit_number'),
							"sortable" => false, "hidden" => false),
						array("key" => "sync_message", "label" => lang('sync_message'), "sortable" => false,
							"hidden" => false),
						array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable" => false,
							"hidden" => false)
					);
					break;
				case 'identifier':
					self::set_active_menu('rental::parties::sync::sync_identifier');
					$appname = lang('sync_parties_identifier');
					$type = 'sync_parties_identifier';
					$extra_cols = array(
						array("key" => "service_id", "label" => lang('service_id'), "sortable" => false,
							"hidden" => false),
						array("key" => "responsibility_id", "label" => lang('responsibility_id'),
							"sortable" => false,
							"hidden" => false),
						array("key" => "identifier", "label" => lang('identifier'), "sortable" => false,
							"hidden" => false),
						array("key" => "sync_message", "label" => lang('sync_message'), "sortable" => false,
							"hidden" => false),
						array("key" => "org_unit_name", "label" => lang('org_unit_name'), "sortable" => false,
							"hidden" => false)
					);
					break;
				case 'org_unit':
					self::set_active_menu('rental::parties::sync::sync_org_unit');
					$appname = lang('sync_parties_fellesdata_id');
					$type = 'sync_parties_org_unit';
					$extra_cols = array(
						array("key" => "org_unit_name", "label" => lang('sync_org_name_fellesdata'),
							"sortable" => false, "hidden" => false),
						array("key" => "dep_org_name", "label" => lang('sync_org_department_fellesdata'),
							"sortable" => false, "hidden" => false),
						array("key" => "unit_leader", "label" => lang('sync_org_unit_leader_fellesdata'),
							"sortable" => false, "hidden" => false),
						array("key" => "org_email", "label" => lang('sync_org_email_fellesdata'),
							"sortable" => false,
							"hidden" => false)
					);
					break;
			}

			$function_msg = lang('list %1', $appname);

			$data = array(
				'datatable_name' => $function_msg,
				'form' => array(
					'toolbar' => array(
						'item' => array()
					)
				),
				'datatable' => array(
					'source' => self::link(array(
						'menuaction' => 'rental.uiparty.sync',
						'editable' => ($editable) ? 1 : 0,
						'type' => $type,
						'phpgw_return_as' => 'json'
					)),
					'download' => self::link(array('menuaction' => 'rental.uiparty.download',
						'type' => $type,
						'export' => true,
						'allrows' => true
					)),
					'allrows' => true,
					'editor_action' => '',
					'field' => array(
						array(
							'key' => 'identifier',
							'label' => lang('identifier'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'name',
							'label' => lang('name'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						),
						array(
							'key' => 'address',
							'label' => lang('address'),
							'className' => '',
							'sortable' => true,
							'hidden' => false
						)
					)
				)
			);

			$filters = $this->_get_Filters();
			krsort($filters);
			foreach ($filters as $filter)
			{
				array_unshift($data['form']['toolbar']['item'], $filter);
			}

			foreach ($extra_cols as $col)
			{
				array_push($data['datatable']['field'], $col);
			}
			array_push($data['datatable']['field'], array("key" => "other_operations", "label" => lang('other operations'),
				"sortable" => false, "hidden" => false, "className" => 'dt-center all'));

			$parameters = array
				(
				'parameter' => array
					(
					array
						(
						'name' => 'id',
						'source' => 'id'
					),
				)
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'download_agresso',
				'text' => lang('Download Agresso import file'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiparty.download_agresso'
				)),
				'parameters' => json_encode(array())
			);

			$data['datatable']['actions'][] = array
				(
				'my_name' => 'view',
				'text' => lang('show'),
				'action' => $GLOBALS['phpgw']->link('/index.php', array
					(
					'menuaction' => 'rental.uiparty.view'
				)),
				'parameters' => json_encode($parameters)
			);

			if ($user_is[ADMINISTRATOR] || $user_is[EXECUTIVE_OFFICER])
			{
				$data['datatable']['actions'][] = array
					(
					'my_name' => 'edit',
					'text' => lang('edit'),
					'action' => $GLOBALS['phpgw']->link('/index.php', array
						(
						'menuaction' => 'rental.uiparty.edit'
					)),
					'parameters' => json_encode($parameters)
				);

				if($sync_job == 'org_unit')
				{
					//not really columns - but a placeholder for mass-sync.
					$data['datatable']['columns'] = array('name' => lang('syncronize all'), 'onclick' => "PartyMassSync({type:'{$type}'})");
				}
			}

			$alertMessage_deleteParty = '"Du er i ferd med å slette en kontraktspart.\n\n Operasjonen kan ikke angres.\n\n Vil du gjøre dette?";';
			$alertMessage_syncParty = '"Du er i ferd med å overskrive data med informasjon hentet fra Fellesdata.\n\n Følgende felt vil bli overskrevet: Foretak, Avdeling, Enhetsleder, Epost. \n\n Vil du gjøre dette?";';

			$jscode = <<<JS

				var confirm_msg_mass_sync = $alertMessage_syncParty
				var confirm_msg_sync = $alertMessage_syncParty
				var confirm_msg_delete = $alertMessage_deleteParty

JS;

			$GLOBALS['phpgw']->js->add_code('', $jscode);

			self::add_javascript('rental', 'rental', 'party.sync.js');
			self::render_template_xsl('datatable_jquery', $data);
		}

		public function syncronize_party()
		{
			if (($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$config_data = CreateObject('phpgwapi.config', 'rental')->read();
				if (empty($config_data['use_fellesdata']))
				{
					return;
				}
				$bofelles = rental_bofellesdata::get_instance();


				$multisync = phpgw::get_var('multisync', 'bool');

				if($multisync)
				{
					$candidates = $this->get_sync_candidates();
				}
				else
				{
					$candidates = array();
					$candidates[] = array(
						'party_id' => phpgw::get_var('party_id', 'int'),
						'org_unit_id' => phpgw::get_var('org_enhet_id', 'int')
					);
				}
				$i = 0;
				foreach ($candidates as $candidate)
				{
					if(!$candidate['party_id'] > 0 && !$candidate['org_unit_id'] > 0)
					{
						continue;
					}

					$party_id = $candidate['party_id'];
					$org_unit_id = $candidate['org_unit_id'];

					$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
					$org_department = $bofelles->get_department_for_org_unit($org_unit_id);

					$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
					$org_email = $org_unit_with_leader['ORG_EMAIL'];
					$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];
					$dep_org_name = $org_department['DEP_ORG_NAME'];

					$party = rental_soparty::get_instance()->get_single($party_id);

					if (!empty($dep_org_name) & $dep_org_name != '')
						$party->set_department($dep_org_name);

					if (!empty($unit_leader_fullname) & $unit_leader_fullname != '')
						$party->set_unit_leader($unit_leader_fullname);

					if (!empty($org_name) & $org_name != '')
						$party->set_company_name($org_name);

					if (!empty($org_email) & $org_email != '')
						$party->set_email($org_email);

					if (!empty($org_unit_id) & $org_unit_id != '')
						$party->set_org_enhet_id($org_unit_id);

					rental_soparty::get_instance()->store($party);

			//		echo  json_encode( array('message'=> lang('party %1 is updated', $party_id)));
					$i++;
				}

				return array('message'=> lang('synchronized: %1',  $i));
			}
			else
			{
				return array('message'=> lang('no access'));
			}
		}

		/**
		 * Public method. Called when a user wants to sync data with Fellesdata.
		 * Returns a json string with the following fields: email, org_name, unit_leader_fullname and department
		 */
		public function get_synchronize_party_info()
		{
			if (($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				$org_unit_id = phpgw::get_var("org_enhet_id");

				if (isset($org_unit_id) && $org_unit_id > 0)
				{
					$config = CreateObject('phpgwapi.config', 'rental');
					$config->read();

					$use_fellesdata = $config->config_data['use_fellesdata'];
					if (!$use_fellesdata)
					{
						return;
					}

					$bofelles = rental_bofellesdata::get_instance();

					$org_unit_with_leader = $bofelles->get_result_unit_with_leader($org_unit_id);
					$org_department = $bofelles->get_department_for_org_unit($org_unit_id);

					$org_name = $org_unit_with_leader['ORG_UNIT_NAME'];
					$org_email = $org_unit_with_leader['ORG_EMAIL'];
					$unit_leader_fullname = $org_unit_with_leader['LEADER_FULLNAME'];

					$dep_org_name = $org_department['DEP_ORG_NAME'];

					$jsonArr = array("email" => trim($org_email), "org_name" => trim($org_name),
						"unit_leader_fullname" => trim($unit_leader_fullname), "department" => trim($dep_org_name));

					return json_decode(json_encode($jsonArr));
				}
			}
		}

		/**
		 * Function to create Portico Estate users based on email, first- and lastname on contract parties.
		 */
		public function create_user_based_on_email()
		{
			//Get the party identifier from the reuest
			$party_id = phpgw::get_var('id');

			//Access control: only executive officers and administrators can create such accounts
			if (($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				if (isset($party_id) && $party_id > 0)
				{
					//Load the party from the database
					$party = rental_soparty::get_instance()->get_single($party_id);
					$email = $party->get_email();

					//Validate the email
					$validator = CreateObject('phpgwapi.EmailAddressValidator');
					if (!$validator->check_email_address($email))
					{
						phpgwapi_cache::message_set(lang('error_create_user_based_on_email_not_valid_address'), 'error');
						$this->edit();
						return;
					}
					if ($GLOBALS['phpgw']->accounts->exists($email))
					{
						phpgwapi_cache::message_set(lang('error_create_user_based_on_email_account_exist'), 'error');
						$this->edit();
						return;
					}

					//Read group configuration
					$config = CreateObject('phpgwapi.config', 'rental');
					$config->read();
					$renter_group = $config->config_data['create_user_based_on_email_group'];

					//Get namae and generate password
					$first_name = $party->get_first_name();
					$last_name = $party->get_last_name();
					$passwd = $GLOBALS['phpgw']->common->randomstring(6) . "ABab1!";


					try
					{
						//Create account which never expires
						$account = new phpgwapi_user();
						$account->lid = $email;
						$account->firstname = $first_name;
						$account->lastname = $last_name;
						$account->passwd = $passwd;
						$account->enabled = true;
						$account->expires = -1;
						$frontend_account = $GLOBALS['phpgw']->accounts->create($account, array($renter_group), array(), array(
							'frontend'));

						//Specify the accounts access to modules
						$aclobj = & $GLOBALS['phpgw']->acl;
						$aclobj->set_account_id($frontend_account, true);
						$aclobj->add('frontend', '.', 1);
						$aclobj->add('frontend', 'run', 1);
						$aclobj->add('manual', '.', 1);
						$aclobj->add('manual', 'run', 1);
						$aclobj->add('preferences', 'changepassword', 1);
						$aclobj->add('preferences', '.', 1);
						$aclobj->add('preferences', 'run', 1);
						$aclobj->save_repository();

						//Set the default module for the account
						$preferences = createObject('phpgwapi.preferences', $frontend_account);
						$preferences->add('common', 'default_app', 'frontend');
						$preferences->save_repository();
					}
					catch (Exception $e)
					{

						phpgwapi_cache::message_set($e->getMessage(), 'error');
						$this->edit();
						return;
					}

					if (isset($GLOBALS['phpgw_info']['server']['smtp_server']) && $GLOBALS['phpgw_info']['server']['smtp_server'])
					{
						if (!is_object($GLOBALS['phpgw']->send))
						{
							$GLOBALS['phpgw']->send = CreateObject('phpgwapi.send');
						}

						//Get addresses from module configuration
						$from = $config->config_data['from_email_setting'];
						$address = $config->config_data['http_address_for_external_users'];

						// Define email content
						$title = lang('email_create_user_based_on_email_title');
						$message = lang('email_create_user_based_on_email_message', $first_name, $last_name, $passwd, $address);

						//Send email
						$rcpt = $GLOBALS['phpgw']->send->msg('email', $email, $title, stripslashes(nl2br($message)), '', '', '', $from, 'System message', 'html', '', array(), false);

						//Redirect with sucess message if receipt is ok
						if ($rcpt)
						{
							phpgwapi_cache::message_set(lang('success_create_user_based_on_email'), 'message');
							$this->edit();
							return;
						}
					}
				}
			}

			phpgwapi_cache::message_set(lang('error_create_user_based_on_email'), 'error');
			$this->edit();
			return;
		}

		public function delete_party()
		{
			$receipt = array();
			$party_id = phpgw::get_var('id');
			if (($this->isExecutiveOfficer() || $this->isAdministrator()))
			{
				if (isset($party_id) && $party_id > 0)
				{
					if (rental_soparty::get_instance()->delete_party($party_id)) // ... delete the party
					{
						$message = lang('messages_saved_form');
						$receipt['message'][] = array('msg' => $message);
					}
					else
					{
						$error = lang('messages_form_error');
						$receipt['error'][] = array('msg' => $error);
					}

					if (phpgw::get_var('phpgw_return_as') == 'json')
					{
						return $receipt;
					}
				}
			}
			else
			{
				phpgw::no_access($GLOBALS['phpgw_info']['flags']['currentapp'], lang('permission_denied_edit'));
			}
		}
	}