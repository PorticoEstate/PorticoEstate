<?php
	/*
	 * This file will only work for the implementation of LRS
	 */

	/**
	 * Intended for custom validation of tickets prior to commit.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
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
			$org_unit = (int)$values_attribute['1']['value'];
			$sql = "SELECT arbeidssted FROM fm_org_unit WHERE id = {$org_unit}";
			$this->db->query($sql);
			$this->db->next_record();
			$arbeidssted = (int)$this->db->f('arbeidssted');

			$location_id = $GLOBALS['phpgw']->locations->get_id('property', ".entity.6.1");

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
			return true;
		}
	}
	$ticket_LRS_pre_commit_validate = new ticket_LRS_pre_commit_validate();
	$ticket_LRS_pre_commit_validate->validate($id, $data, $values_attribute);
