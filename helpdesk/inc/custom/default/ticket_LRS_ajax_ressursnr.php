<?php
	/*
	 * This file will only work for the implementation of LRS
	 */

	/**
	 * Intended for custom validation of ajax-request from form.
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 */
	if (!class_exists("ticket_LRS_validate_ressurs"))
	{
		class ticket_LRS_validate_ressurs
		{

			protected	$config;

			function __construct()
			{
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

				$db = createObject('phpgwapi.db_adodb', null, null, true);
				$db->debug = false;
				$db->Host = $this->config->config_data['fellesdata']['host'];
				$db->Port = $this->config->config_data['fellesdata']['port'];
				$db->Type = 'oracle';
				$db->Database = $this->config->config_data['fellesdata']['db_name'];
				$db->User = $this->config->config_data['fellesdata']['user'];
				$db->Password = $this->config->config_data['fellesdata']['password'];

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

			function get_ressurs_name()
			{
				$ressursnr_id = phpgw::get_var('ressursnr_id', 'int');

				if(!$ressursnr_id)
				{
					return;
				}

				if (!$db = $this->get_db())
				{
					return;
				}

				$sql = "SELECT * FROM V_SOA_ANSATT WHERE RESSURSNR = {$ressursnr_id}";

				$db->query($sql, __LINE__, __FILE__);

				if($db->next_record())
				{
					$last_name	= $db->f('ETTERNAVN', true);
					$first_name	= $db->f('FORNAVN', true);
					$email	= $db->f('EPOST', true);
					$ret = "{$last_name}, {$first_name} [{$email}]";
				}
				else
				{
					$ret = 'Ugyldig ressursnr';
				}

				return $ret;
			}

		}
	}

	$method = phpgw::get_var('method');

	if($method == 'get_ressurs_name')
	{
		$ressurs = new ticket_LRS_validate_ressurs();
		$ajax_result['ressurs_name'] =  $ressurs->get_ressurs_name();
	}
