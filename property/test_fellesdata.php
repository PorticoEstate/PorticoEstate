<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');


	$fellesdata	 = new fellesdata();
	$ressurs	 = $fellesdata->get_ressurs();

	class fellesdata
	{

		// Instance variable
		protected $connected = false;
		protected $status;
		public $db			 = null;
		protected $debug	 = false;
		protected $config;

		function __construct()
		{
			$this->config	 = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));
			$this->db		 = $this->get_db();
		}

		/* our simple php ping function */

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

		public function get_db_old()
		{
			if ($this->db && is_object($this->db))
			{
				return $this->db;
			}

			$db_info = array
				(
				'db_host'	 => 'oradb31i.srv.bergenkom.no',
				'db_type'	 => 'oracle',
				'db_port'	 => '21521',
				'db_name'	 => 'FELPROD',
				'db_user'	 => 'PERSON_EBF',
				'db_pass'	 => 'EBF_FOR_0916',
			);

			if (!$db_info['db_host'] || !$this->ping($db_info['db_host']))
			{
				$message = "Database server {$db_info['db_host']} is not accessible";
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$db = createObject('phpgwapi.db_adodb', null, null, true);

			$db->debug		 = false;
			$db->Host		 = $db_info['db_host'];
			$db->Port		 = $db_info['db_port'];
			$db->Type		 = $db_info['db_type'];
			$db->Database	 = $db_info['db_name'];
			$db->User		 = $db_info['db_user'];
			$db->Password	 = $db_info['db_pass'];

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

		function get_ressurs()
		{
			$ressursnr_id = 119510;

			if (!$ressursnr_id)
			{
				return;
			}

			if (!$db = $this->get_db())
			{
				return;
			}

			$sql = "SELECT * FROM V_SOA_ANSATT WHERE ORG_NIVAA = 5"; //RESSURSNR > {$ressursnr_id}";
//			$sql = "SELECT count(*) as antall FROM V_SOA_ANSATT WHERE ORG_NIVAA = 5"; //RESSURSNR > {$ressursnr_id}";

			$num_rows = 50;
//			$sql = "SELECT * FROM ({$sql}) WHERE ROWNUM <= {$num_rows}";

			$db->query($sql, __LINE__, __FILE__);

			$values = array();
			while ($db->next_record())
			{
				$values[] = $db->Record;
			}

			_debug_array($values);

			return $ret;
		}
	}