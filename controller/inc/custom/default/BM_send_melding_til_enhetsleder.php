<?php
	/*
	 * This file will only work for the implementation of EBE/BM
	 */

	/**
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("controller_alert_head_of_school"))
	{

		class controller_alert_head_of_school
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

			public function get_head_of_school($location_code)
			{


$sql = "SELECT org_enhet_id, rental_party.* FROM rental_party
 JOIN rental_contract_party ON rental_party.id = rental_contract_party.party_id
 JOIN rental_contract ON rental_contract_party.contract_id = rental_contract.id
 JOIN rental_contract_composite ON rental_contract_composite.contract_id = rental_contract.id
 JOIN rental_unit ON rental_contract_composite.composite_id = rental_unit.composite_id
 WHERE location_code = '1135-01'
 AND rental_contract.date_end > extract(epoch from now())";


			}
		}
	}
