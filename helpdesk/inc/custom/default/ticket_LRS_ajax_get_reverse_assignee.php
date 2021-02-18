<?php
	/*
	 * This file will only work for the implementation of LRS
	 */

	/**
	 * Intended for custom validation of ajax-request from form.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("ticket_LRS_reverse_assignee"))
	{

		class ticket_LRS_reverse_assignee
		{

			protected $config, $db;

			function __construct()
			{
				$this->account	= (int)$GLOBALS['phpgw_info']['user']['account_id'];
				$this->config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));
			}

			function ping( $host )
			{
				exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
				return $rval === 0;
			}

			public function get_db()
			{
				if ($this->db && is_object($this->db))
				{
					return $this->db;
				}

				if (!$this->config->config_data['fellesdata']['host'] || !$this->ping($this->config->config_data['fellesdata']['host']))
				{
					$message = "Database server {$this->config->config_data['fellesdata']['host']} is not accessible";
					phpgwapi_cache::message_set($message, 'error');
					return false;
				}

				$db				 = createObject('phpgwapi.db_adodb', null, null, true);
				$db->debug		 = false;
				$db->Host		 = $this->config->config_data['fellesdata']['host'];
				$db->Port		 = $this->config->config_data['fellesdata']['port'];
				$db->Type		 = 'oracle';
				$db->Database	 = $this->config->config_data['fellesdata']['db_name'];
				$db->User		 = $this->config->config_data['fellesdata']['user'];
				$db->Password	 = $this->config->config_data['fellesdata']['password'];

				try
				{
					$db->connect();
					$this->connected = true;
				}
				catch (Exception $e)
				{
					$status = lang('unable_to_connect_to_database');
				}

				$this->db = $db;
				return $db;
			}

			function set_notify()
			{
				if (!$GLOBALS['phpgw']->acl->check('.ticket', PHPGW_ACL_EDIT, 'helpdesk'))
				{
					return;
				}

				$account_lid = phpgw::get_var('account_lid');
				$location_item_id = (int)phpgw::get_var('ticket_id', 'int');
				if($account_lid)
				{
					$helpdesk_account = new helpdesk_account();
					$helpdesk_account->register_accounts(array
						(
							$account_lid => true
						)
					);
				}
				else
				{
					return;
				}

				$set_notify_id = $GLOBALS['phpgw']->accounts->name2id($account_lid);
				$contact_id = $GLOBALS['phpgw']->accounts->get($set_notify_id)->person_id;
				$location_id = $GLOBALS['phpgw']->locations->get_id('helpdesk', '.ticket');

				$values_insert = array
					(
					'location_id'			 => $location_id,
					'location_item_id'		 => $location_item_id,
					'contact_id'			 => $contact_id,
					'is_active'				 => 1,
					'entry_date'			 => time(),
					'user_id'				 => $this->account,
					'notification_method'	 => 'email'
				);

				$ret = false;

				$sql = "SELECT id FROM phpgw_notification WHERE location_id = ? AND location_item_id = ? AND contact_id = ?";
				$condition =  array((int)$location_id, (int)$location_item_id, (int)$contact_id);

				$GLOBALS['phpgw']->db->select($sql, $condition, __LINE__, __FILE__);

				if(!$GLOBALS['phpgw']->db->next_record())
				{
					$ret =  $GLOBALS['phpgw']->db->query("INSERT INTO phpgw_notification (" . implode(',', array_keys($values_insert)) . ') VALUES ('
						. $GLOBALS['phpgw']->db->validate_insert(array_values($values_insert)) . ')', __LINE__, __FILE__);
				}

				return array('status' => $ret);

			}

			function get_user_info()
			{
				if (!$db = $this->get_db())
				{
					return;
				}

				$account_lid = phpgw::get_var('account_lid');

				if((int)$account_lid)
				{
					$account_lid = $GLOBALS['phpgw']->accounts->id2lid((int)$account_lid);
				}

				$account_lid = $db->db_addslashes($account_lid);

				$filtermethod = "BRUKERNAVN = '{$account_lid}'";

				if (preg_match("/^dummy\:\:/i", $account_lid))
				{
					$identificator_arr	 = explode("::", $account_lid);
					$filtermethod = "RESSURSNR = '{$identificator_arr[1]}'";
				}

				$sql = "SELECT TJENESTESTED, V_ORG_ENHET.ORG_ENHET_ID, V_ORG_ENHET.ORG_NAVN FROM FELLESDATA.V_PORTICO_ANSATT "
					. " JOIN V_ORG_ENHET ON V_ORG_ENHET.ORG_ENHET_ID = V_PORTICO_ANSATT.ORG_ENHET_ID WHERE {$filtermethod}";

				$db->query($sql, __LINE__, __FILE__);
				$values = array();

				if ($db->next_record())
				{
					$arbeidssted = $db->f('TJENESTESTED');
					$values = array(
						'org_unit_id'	 => $db->f('ORG_ENHET_ID'),
						'org_unit'		 => $arbeidssted . ' ' .$db->f('ORG_NAVN', true)
					);
				}
				return $values;
			}

			function get_on_behalf_of()
			{
				$query = phpgw::get_var('query');
				$search_options = phpgw::get_var('search_options');

				if (!$db = $this->get_db())
				{
					return;
				}

				if(strlen($query) < 4)
				{
					return array('ResultSet' => array('Result' => $values));
				}
				$query_arr	 = explode(" ", str_replace("  ", " ", $query));
				$query_arr2	 = explode(",", str_replace(" ", "", $query));

				$filtermethod =	"(BRUKERNAVN = '{$query}'"
				. " OR FODSELSNR  = '{$query}'"
				. " OR RESSURSNR  = '{$query}'";

				if(!empty($query_arr[1]) && empty($query_arr2[1]))
				{
					$filtermethod .= " OR (lower(FORNAVN)  LIKE '" . strtolower($query_arr[0]) ."%'"
					 . " AND lower(ETTERNAVN)  LIKE '" . strtolower($query_arr[1]) ."%')";
				}
				if(!empty($query_arr[2]) && empty($query_arr2[1]))
				{
					$filtermethod .= " OR (lower(FORNAVN)  LIKE '" . strtolower($query_arr[0]) . " " . strtolower($query_arr[1]) . "%'"
					 . " AND lower(ETTERNAVN)  LIKE '" . strtolower($query_arr[2]) ."%')";
				}
				else if(!empty($query_arr[0]) && !isset($query_arr2[1]))
				{
					$filtermethod .= " OR lower(ETTERNAVN)  LIKE '" . strtolower($query_arr[0]) ."%'";
				}
				else if(isset($query_arr2[1]))
				{
					$filtermethod .= " OR (lower(ETTERNAVN)  LIKE '" . strtolower($query_arr2[0]) ."%'"
					 . " AND lower(FORNAVN)  LIKE '" . strtolower($query_arr2[1]) ."%')";
				}

				if($search_options == 'ressurs_nr')
				{
					$filtermethod =	"RESSURSNR = '{$query}'";
				}
				else if($search_options == 'resultat_enhet')
				{
					$treff_utenfor_resultat_enhet = false;

					$sql = "SELECT ORG_ENHET_ID, ORG_NIVAA, BRUKERNAVN, FORNAVN, ETTERNAVN,STILLINGSTEKST, RESSURSNR FROM FELLESDATA.V_PORTICO_ANSATT"
						. " WHERE {$filtermethod})";


					$db->limit_query($sql, 0, __LINE__, __FILE__, 1);

					$org_units = array(-1);

					if($db->next_record())
					{
						$treff_utenfor_resultat_enhet = true;
						$org_units[] = $db->f('ORG_ENHET_ID');
					}

					$ticket_id = (int)phpgw::get_var('ticket_id');

					$GLOBALS['phpgw']->db->query("SELECT user_id FROM phpgw_helpdesk_tickets WHERE id = {$ticket_id}", __LINE__, __FILE__);
					$GLOBALS['phpgw']->db->next_record();
					$user_id = $GLOBALS['phpgw']->db->f('user_id');
					$user_lid = $GLOBALS['phpgw']->accounts->get($user_id)->lid;

					$sql = "SELECT ORG_ENHET_ID, ORG_NIVAA FROM FELLESDATA.V_PORTICO_ANSATT WHERE BRUKERNAVN = '{$user_lid}'";

					$db->query($sql, __LINE__, __FILE__);

					if ($db->next_record())
					{
						$org_unit	 = $db->f('ORG_ENHET_ID');
						$level		 = $db->f('ORG_NIVAA');
					}

					if (!$org_unit)
					{
						return;
					}

					$path = CreateObject('property.sogeneric')->get_path(array(
						'type' => 'org_unit',
						'id' => $org_unit,
						'path_by_id' => true
						));

					$levels = count($path);

					if ($levels > 1)
					{
						$parent_id = (int)$path[($levels - 2)];
					}
					else
					{
						$parent_id = (int)$path[0];
					}

					$sql = "SELECT id FROM fm_org_unit WHERE parent_id  = {$parent_id}";

					$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

					while ($GLOBALS['phpgw']->db->next_record())
					{
						$org_units[] = (int)$GLOBALS['phpgw']->db->f('id');
					}

					$filtermethod .= ") AND V_PORTICO_ANSATT.ORG_ENHET_ID IN (" . implode(',', $org_units) . ')';

				}
				else
				{
					$filtermethod .= ")";
				}

				$sql = "SELECT ORG_ENHET_ID, ORG_NIVAA, BRUKERNAVN, FORNAVN, ETTERNAVN,STILLINGSTEKST, RESSURSNR FROM FELLESDATA.V_PORTICO_ANSATT"
					. " WHERE {$filtermethod}";


				$db->limit_query($sql, 0, __LINE__, __FILE__, 10);
				$values = array();

				while ($db->next_record())
				{
					$user_lid = $db->f('BRUKERNAVN');
					$values[] = array(
						'id'		 => $user_lid ? $user_lid : 'dummy::' . $db->f('RESSURSNR'),
						'name'		 => $db->f('BRUKERNAVN') . ' [' . $db->f('RESSURSNR') .': ' . $db->f('ETTERNAVN', true) . ', ' . $db->f('FORNAVN', true) . ', ' . $db->f('STILLINGSTEKST', true) . '] ' ,
						'org_unit'	 => $db->f('ORG_ENHET_ID'),
						'level'		 => $db->f('ORG_NIVAA'),
					);
				}

				foreach ($values as &$value)
				{
					$path = CreateObject('property.sogeneric')->get_path(array(
						'type'			 => 'org_unit',
						'id'			 => $value['org_unit'],
						'path_by_id'	 => true
					));

					$levels = count($path);

					if ($levels > 1)
					{
						$parent_id = (int)$path[($levels - 2)];
					}
					else
					{
						$parent_id = (int)$path[0];
					}
					$sql = "SELECT name FROM fm_org_unit WHERE id  = {$parent_id}";

					$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

					$GLOBALS['phpgw']->db->next_record();
					{
						$org_unit_name = $GLOBALS['phpgw']->db->f('name', true);
					}

					$value['name'] .= " {$org_unit_name}";
				}


				/**
				 * fallback for external users
				 */

				if(!$values)
				{
					$_values = array();
					$filtermethod =	"account_lid ilike '{$query}'"
					. " OR account_lastname  = '{$query}'"
					. " OR account_firstname  = '{$query}'";

					if(!empty($query_arr[1]) && empty($query_arr2[1]))
					{
						$filtermethod .= " OR (account_firstname  ilike '" . strtolower($query_arr[0]) ."%'"
						 . " AND account_lastname ilike '" . strtolower($query_arr[1]) ."%')";
					}
					if(!empty($query_arr[2]) && empty($query_arr2[1]))
					{
						$filtermethod .= " OR (account_firstname  ilike '" . strtolower($query_arr[0]) . " " . strtolower($query_arr[1]) . "%'"
						 . " AND account_lastname  ilike '" . strtolower($query_arr[2]) ."%')";
					}
					else if(!empty($query_arr[0]) && !isset($query_arr2[1]))
					{
						$filtermethod .= " OR account_lastname ilike '" . strtolower($query_arr[0]) ."%'";
					}
					else if(isset($query_arr2[1]))
					{
						$filtermethod .= " OR (account_lastname  ilike '" . strtolower($query_arr2[0]) ."%'"
						 . " AND account_firstname ilike '" . strtolower($query_arr2[1]) ."%')";
					}

					$sql = "SELECT * FROM phpgw_accounts WHERE ({$filtermethod}) AND account_status = 'A'";
					$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
					while ($GLOBALS['phpgw']->db->next_record())
					{
						$_values[] = array(
							'id'		 => $GLOBALS['phpgw']->db->f('account_lid', true),
							'name'		 => $GLOBALS['phpgw']->db->f('account_lid') . ' [' . $GLOBALS['phpgw']->db->f('account_lastname', true) . ', ' . $GLOBALS['phpgw']->db->f('account_firstname', true) . ']' ,
							'org_unit'	 => $db->f('ORG_ENHET_ID'),
							'level'		 => $db->f('ORG_NIVAA'),
						);
					}
				}

				/**
				 * Remove false hit
				 */
//				if(in_array($search_options, array('ressurs_nr', 'resultat_enhet')))
				{
					foreach ($_values as $entry)
					{
						$sql = "SELECT BRUKERNAVN FROM FELLESDATA.V_PORTICO_ANSATT WHERE BRUKERNAVN ='{$entry['id']}'";
						$db->query($sql, __LINE__, __FILE__);
						if(!$db->next_record())
						{
							$values[] = $entry;
						}
					}
				}

				if(!$values && $treff_utenfor_resultat_enhet && $search_options == 'resultat_enhet')
				{
					$values = array(array(
						'id' => '0',
						'name' => 'Det er treff - men både den som eier saken og den som settes på kopi må være ansatt innenfor samme resultatenhet',
					));
				}

				return array('ResultSet' => array('Result' => $values));
			}

			/**
			 * Fetch data from outlook integration
			 * @return array
			 */
			function get_reverse_assignee()
			{
				$on_behalf_of_lid = phpgw::get_var('on_behalf_of_lid', 'string');


				if (!$on_behalf_of_lid)
				{
					return;
				}

				if (!$db = $this->get_db())
				{
					return;
				}

				$filtermethod = "BRUKERNAVN = '{$on_behalf_of_lid}'";

				if (preg_match("/^dummy\:\:/i", $on_behalf_of_lid))
				{
					$identificator_arr	 = explode("::", $on_behalf_of_lid);
					$filtermethod = "RESSURSNR = '{$identificator_arr[1]}'";
				}

				$sql = "SELECT ORG_ENHET_ID, ORG_NIVAA FROM FELLESDATA.V_PORTICO_ANSATT WHERE {$filtermethod}";

				$db->query($sql, __LINE__, __FILE__);

				if ($db->next_record())
				{
					$org_unit	 = $db->f('ORG_ENHET_ID');
					$level		 = $db->f('ORG_NIVAA');
				}

				if (!$org_unit)
				{
					return;
				}

				$path = CreateObject('property.sogeneric')->get_path(array(
					'type' => 'org_unit',
					'id' => $org_unit,
					'path_by_id' => true
					));

				$levels = count($path);

				if ($levels > 1)
				{
					$parent_id = (int)$path[($levels - 2)];
				}
				else
				{
					$parent_id = (int)$path[0];
				}



				$sql = "SELECT id FROM fm_org_unit WHERE parent_id  = {$parent_id}";

				$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

				$org_units = array();

				while ($GLOBALS['phpgw']->db->next_record())
				{
					$org_units[] = (int)$GLOBALS['phpgw']->db->f('id');
				}

				$sql = "SELECT BRUKERNAVN, STILLINGSTEKST, V_ORG_ENHET.ORG_NAVN FROM FELLESDATA.V_PORTICO_ANSATT AS V_PORTICO_ANSATT"
					. " JOIN FELLESDATA.V_ORG_ENHET AS V_ORG_ENHET ON V_ORG_ENHET.ORG_ENHET_ID = V_PORTICO_ANSATT.ORG_ENHET_ID"
					. " WHERE V_PORTICO_ANSATT.ORG_ENHET_ID IN (" . implode(',', $org_units) . ')';

				$db->query($sql, __LINE__, __FILE__);

				$candidates = array();
				while ($db->next_record())
				{
					$candidates[$db->f('BRUKERNAVN')] = array(
						'office' =>  $db->f('ORG_NAVN',true),
						'stilling' =>  $db->f('STILLINGSTEKST',true)
						);
				}

				$sql = "SELECT DISTINCT alias, name  FROM phpgw_helpdesk_email_out_recipient_list WHERE alias IN ('" . implode("','", array_keys($candidates)) . "')";

				$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);

				$candidate_assignees = array();

				while ($GLOBALS['phpgw']->db->next_record())
				{
					$lid					 = $GLOBALS['phpgw']->db->f('alias', true);
					$candidate_assignees[]	 = array
						(
						'lid'		 => $lid,
						'name'		 => $GLOBALS['phpgw']->db->f('name', true),
						'stilling'	 => $candidates[$lid]['stilling'],
						'office'	 => $candidates[$lid]['office'],
					);
				}

				$values = array();
				foreach ($candidate_assignees as $candidate_assignee)
				{
					$candidate_assignee['id'] = $GLOBALS['phpgw']->accounts->name2id($candidate_assignee['lid']);

					if (!$candidate_assignee['id'])
					{
						continue;
					}
					$values[] = $candidate_assignee;
				}

				return array(
					'total_records'	 => count($values),
					'results'		 => $values
				);
			}
		}
	}


	if (!class_exists("helpdesk_account"))
	{
		phpgw::import_class('helpdesk.hook_helper');

		class helpdesk_account extends helpdesk_hook_helper
		{

			public function __construct()
			{
				$this->config = CreateObject('phpgwapi.config', 'helpdesk')->read();
			}

			public function register_accounts( $values )
			{
				foreach ($values as $account_lid => $entry)
				{
					if (!$GLOBALS['phpgw']->accounts->exists($account_lid))
					{

						$autocreate_user = isset($this->config['autocreate_user']) && $this->config['autocreate_user'] ? $this->config['autocreate_user'] : 0;

						if ($autocreate_user)
						{
							$fellesdata_user = frontend_bofellesdata::get_instance()->get_user($account_lid);
							if ($fellesdata_user && $fellesdata_user['firstname'])
							{
								// Read default assign-to-group from config
								$default_group_id	 = isset($this->config['autocreate_default_group']) && $this->config['autocreate_default_group'] ? $this->config['autocreate_default_group'] : 0;
								$group_lid			 = $GLOBALS['phpgw']->accounts->id2lid($default_group_id);
								$group_lid			 = $group_lid ? $group_lid : 'frontend_delegates';

								$password	 = 'PEre' . mt_rand(100, mt_getrandmax()) . '&';
								$account_id	 = self::create_phpgw_account($account_lid, $fellesdata_user['firstname'], $fellesdata_user['lastname'], $password, $group_lid);
							}
						}
					}
				}
			}
		}
	}




	$method = phpgw::get_var('method');

	if ($method == 'get_reverse_assignee')
	{
		$reverse_assignee	 = new ticket_LRS_reverse_assignee();
		$ajax_result		 = $reverse_assignee->get_reverse_assignee();
	}
	else if ($method == 'get_on_behalf_of')
	{
		$reverse_assignee	 = new ticket_LRS_reverse_assignee();
		$ajax_result		 = $reverse_assignee->get_on_behalf_of();
	}
	else if ($method == 'get_user_info')
	{
		$reverse_assignee	 = new ticket_LRS_reverse_assignee();
		$ajax_result		 = $reverse_assignee->get_user_info();
	}
	else if ($method == 'set_notify')
	{
		$reverse_assignee	 = new ticket_LRS_reverse_assignee();
		$ajax_result		 = $reverse_assignee->set_notify();
	}

