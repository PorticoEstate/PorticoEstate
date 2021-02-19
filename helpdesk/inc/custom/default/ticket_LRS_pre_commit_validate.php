<?php
	/*
	 * This file will only work for the implementation of LRS
	 */

	/**
	 * Intended for custom validation of tickets prior to commit.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("ticket_LRS_pre_commit_validate"))
	{

		class ticket_LRS_pre_commit_validate extends helpdesk_botts
		{

			protected
				$db,
				$join,
				$left_join,
				$like,
				$socat_assignment;
			function __construct()
			{
				parent::__construct();
				$this->db = & $GLOBALS['phpgw']->db;
				$this->join = & $this->db->join;
				$this->left_join = & $this->db->left_join;
				$this->like = & $this->db->like;
				$this->socat_assignment = createObject('helpdesk.socat_assignment');
			}

			/**
			 * Do your magic
			 * @param integer $id
			 * @param array $data
			 * @param array $values_attribute
			 */
			function validate( $id = 0, &$data, $values_attribute = array() )
			{
				if($id) // on edit
				{
					/**
					 * Forward to new owner
					 */
					if(!empty($data['set_user_lid']))
					{
						$helpdesk_account = new helpdesk_account();
						$helpdesk_account->register_accounts(array
							(
								$data['set_user_lid'] => true
							)
						);
					}
					
					return;
				}

				if(!empty($data['reverse_id']))
				{
					return true;
				}

				if(!empty($data['set_user_alternative_lid']))
				{
					$helpdesk_account = new helpdesk_account();
					$helpdesk_account->register_accounts(array
						(
							$data['set_user_alternative_lid'] => true
						)
					);
				}

				if(!empty($data['set_notify_lid']))
				{
					$helpdesk_account = new helpdesk_account();
					$helpdesk_account->register_accounts(array
						(
							$data['set_notify_lid'] => true
						)
					);
				}

				$org_unit = 0;
				foreach ($values_attribute as $key => $valueset)
				{
					if($valueset['name'] == 'arbeidssted')
					{
						$org_unit = (int)$valueset['value'];
						break;
					}
				}

				$sql = "SELECT arbeidssted FROM fm_org_unit WHERE id = {$org_unit}";
				$this->db->query($sql);
				$this->db->next_record();
				$arbeidssted = (int)$this->db->f('arbeidssted');
				$category =  CreateObject('phpgwapi.categories', -1, 'helpdesk', '.ticket')->return_single($data['cat_id']);
				$parent_id =  (int)$category[0]['parent'];

				if($parent_id == 255)//LRS-Lønn
				{
					$data['group_id'] = 3159;
				}
				else if($parent_id == 256)//LRS-refusjon
				{
					$data['group_id'] = 3233; //LRS-DRIFT_Refusjon
				}
				else if($parent_id == 268)//LRS-Økonomi
				{
					$data['group_id'] = 4169;
				}
				else if($parent_id == 286)//LRS-Bestilling av endring i UBW
				{
					$data['group_id'] = 4173;
				}
				else if($parent_id == 301)//LRS Auto fra helpdesk vedr Telefoni
				{
					$data['group_id'] = 4174;
				}
				else if($parent_id == 314)//LRS System Øk
				{
					$data['group_id'] = 4252;//LRS-System_Økonomi
				}
				else if($parent_id == 359)//LRS-Intern
				{
					$data['group_id'] = 4253;//LRS-DRIFT_Økonomi
				}

				$group_assignment = $this->socat_assignment->read_single($data['cat_id']);

				if($group_assignment)
				{
					$data['group_id'] = $group_assignment;
				}

				return true;
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

	$ticket_LRS_pre_commit_validate = new ticket_LRS_pre_commit_validate();
	$ticket_LRS_pre_commit_validate->validate(empty($id)?null:$id, $data, $values_attribute);
