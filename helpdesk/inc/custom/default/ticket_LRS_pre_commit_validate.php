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
				$like;
			function __construct()
			{
				parent::__construct();
				$this->db = & $GLOBALS['phpgw']->db;
				$this->join = & $this->db->join;
				$this->left_join = & $this->db->left_join;
				$this->like = & $this->db->like;
			}

			/**
			 * Do your magic
			 * @param integer $id
			 * @param array $data
			 * @param array $values_attribute
			 */
			function validate( $id = 0, &$data, $values_attribute = array() )
			{
				if($id) // only on add
				{
					return;
				}

				if(!empty($data['reverse_id']))
				{
					return true;
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
	//				$data['group_id'] = 3159;
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.6.1");
				}
				else if($parent_id == 256)//LRS-refusjon
				{
	//				$data['group_id'] = 3233;
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.6.2");
				}
				else if($parent_id == 268)//LRS-Økonomi
				{
					$data['group_id'] = 4169;
				}
				else if($parent_id == 286)//LRS-Bestilling av endring i UBW
				{
					$data['group_id'] = 4173;
				}
				else if($parent_id == 301)//LRS-EDD telefon
				{
					$data['group_id'] = 4174;
				}
				else
				{
					$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.6.2");
				}

				if($location_id)
				{
					$sql = "SELECT json_representation->>'alias' as alias FROM fm_bim_item WHERE location_id = {$location_id}"
					. " AND CAST(json_representation->>'arbeidssted_start' AS INTEGER) <= {$arbeidssted}"
					. " AND CAST(json_representation->>'arbeidssted_slutt' AS INTEGER) >= {$arbeidssted}";

					$this->db->query($sql);
					$this->db->next_record();
					$alias = strtolower($this->db->f('alias'));

					if(!$data['assignedto'] = $GLOBALS['phpgw']->accounts->name2id($alias))
					{
						$data['assignedto'] = isset($GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault']) ? $GLOBALS['phpgw_info']['user']['preferences']['helpdesk']['assigntodefault'] : '';
					}

					$current_prefs_user = $this->bocommon->create_preferences('helpdesk',$GLOBALS['phpgw_info']['user']['account_id']);
					if(empty($current_prefs_user['email']))
					{
						$GLOBALS['phpgw']->preferences->add('helpdesk', 'email', "{$GLOBALS['phpgw_info']['user']['account_lid']}@bergen.kommune.no");
						$GLOBALS['phpgw']->preferences->save_repository();
					}

					$assigned_prefs = createObject('phpgwapi.preferences', (int)$data['assignedto']);
					$assigned_prefs_data = $assigned_prefs->read();
					if(empty($assigned_prefs_data['helpdesk']['email']))
					{
						$assigned_prefs->add('helpdesk', 'email', "{$alias}@bergen.kommune.no");
						$assigned_prefs->save_repository();
					}
				}

				return true;
			}
		}
	}
	$ticket_LRS_pre_commit_validate = new ticket_LRS_pre_commit_validate();
	$ticket_LRS_pre_commit_validate->validate($id, $data, $values_attribute);
