<?php

	class bookingfrontend_bouser
	{

		const ORGNR_SESSION_KEY = 'orgnr';
		const ORGARRAY_SESSION_KEY = 'orgarray';

		public $orgnr = null;
		public $orgname = null;
		protected
			$default_module = 'bookingfrontend',
			$module,
			$config;

		/**
		 * Debug for testing
		 * @access public
		 * @var bool
		 */
		public $debug = false;

		public function __construct()
		{
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
			$this->orgname = $this->get_orgname_from_db($this->get_user_orgnr_from_session());
			$this->config = CreateObject('phpgwapi.config', 'bookingfrontend');
			$this->config->read();
		}

		protected function get_orgname_from_db( $orgnr )
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->db->limit_query("select name from bb_organization where organization_number ='" . $orgnr . "'", 0, __LINE__, __FILE__, 1);
			if (!$this->db->next_record())
			{
				return $orgnr;
			}
			return $this->db->f('name', false);
		}

		protected function get_organizations()
		{
			$results = array();
			$this->db = & $GLOBALS['phpgw']->db;
			$this->db->query("select organization_number from bb_organization ORDER by organization_number ASC", __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$results[] = $this->db->f('organization_number', false);
			}
			return $results;
		}

		protected function set_module( $module = null )
		{
			$this->module = is_string($module) ? $module : $this->default_module;
		}

		public function get_module()
		{
			return $this->module;
		}

		public function log_in()
		{
			$this->log_off();

			$authentication_method = isset($this->config->config_data['authentication_method']) && $this->config->config_data['authentication_method'] ? $this->config->config_data['authentication_method'] : '';

			if (!$authentication_method)
			{
				throw new LogicException('authentication_method not chosen');
			}

			$file = PHPGW_SERVER_ROOT . "/bookingfrontend/inc/custom/default/{$authentication_method}";

			if (!is_file($file))
			{
				throw new LogicException("authentication method \"{$authentication_method}\" not available");
			}

			require_once $file;

			$external_user = new bookingfrontend_external_user();

			$this->orgnr = $external_user->get_user_org_id();
			$this->orgname = $this->get_orgname_from_db($this->orgnr);

			if ($this->is_logged_in())
			{
				$this->write_user_orgnr_to_session();
			}

			if ($this->debug)
			{
				echo 'is_logged_in():<br>';
				_debug_array($this->is_logged_in());
				echo 'Session:<br>';
				_debug_array($_SESSION);
				die();
			}

			return $this->is_logged_in();
		}

		public function change_org( $orgnumber )
		{
			$orgs = phpgwapi_cache::session_get($this->get_module(), self::ORGARRAY_SESSION_KEY);
			$orglist = array();
			foreach ($orgs as $org)
			{
				$orglist[] = $org['orgnumber'];
			}
			if (in_array($orgnumber, $orglist))
			{

				$this->orgnr = $orgnumber;
				$this->orgname = $this->get_orgname_from_db($this->orgnr);

				if ($this->is_logged_in())
				{
					$this->write_user_orgnr_to_session();
				}

				return $this->is_logged_in();
			}
			else
			{

				if ($this->is_logged_in())
				{
					$this->write_user_orgnr_to_session();
				}

				return $this->is_logged_in();
			}
		}

		public function log_off()
		{
			$this->clear_user_orgnr();
			$this->clear_user_orgnr_from_session();
			$this->clear_user_orglist_from_session();
		}

		protected function clear_user_orgnr()
		{
			$this->orgnr = null;
			$this->orgname = null;
		}

		public function get_user_orgnr()
		{
			if (!$this->orgnr)
			{
				$this->orgnr = $this->get_user_orgnr_from_session();
			}
			return $this->orgnr;
		}

		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}

		public function is_organization_admin( $organization_id = null )
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (!$this->is_logged_in())
			{
				//return false;
			}
			$so = CreateObject('booking.soorganization');
			$organization = $so->read_single($organization_id);

			if ($organization['organization_number'] == '')
			{
				return false;
			}

			return $organization['organization_number'] == $this->orgnr;
		}

		public function is_group_admin( $group_id = null )
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (!$this->is_logged_in())
			{
				//return false;
			}
			$so = CreateObject('booking.sogroup');
			$group = $so->read_single($group_id);
			return $this->is_organization_admin($group['organization_id']);
		}

		protected function write_user_orgnr_to_session()
		{
			if (!$this->is_logged_in())
			{
				throw new LogicException('Cannot write orgnr to session unless user is logged on');
			}

			phpgwapi_cache::session_set($this->get_module(), self::ORGNR_SESSION_KEY, $this->get_user_orgnr());
		}

		protected function clear_user_orgnr_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::ORGNR_SESSION_KEY);
		}

		protected function clear_user_orglist_from_session()
		{
#			phpgwapi_cache::session_clear($this->get_module(), self::ORGARRAY_SESSION_KEY);
		}

		protected function get_user_orgnr_from_session()
		{
			try
			{
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean(phpgwapi_cache::session_get($this->get_module(), self::ORGNR_SESSION_KEY));
			}
			catch (sfValidatorError $e)
			{
				return null;
			}
		}

		protected function get_breg_orgs( $fodselsnr )
		{
			$db = createObject('phpgwapi.db', null, null, true);

			$db->Host = $GLOBALS['phpgw_domain']['default']['db_host'];
			$db->Port = '5432';
			$db->Type = 'postgres';
			$db->Database = 'breg';
			$db->User = $GLOBALS['phpgw_domain']['default']['db_user'];
			$db->Password = $GLOBALS['phpgw_domain']['default']['db_pass'];

			try
			{
				$db->connect();
				$this->connected = true;
			}
			catch (Exception $e)
			{
				$status = lang('unable_to_connect_to_database');
			}

			$sql = "SELECT DISTINCT orgnr FROM personcurrent WHERE fodselsnr ='{$fodselsnr}'";
			$results = array();
			$db = & $GLOBALS['phpgw']->db;
			$db->query($sql, __LINE__, __FILE__);
			while ($db->next_record())
			{
				$results[] = $db->f('orgnr', true);
			}
			return $results;
		}

      protected function get_breg_orgs_old($fodselsnr) {
            $breg_conn = pg_connect("host=".$GLOBALS['phpgw_domain']['default']['db_host']." port=5432 dbname=breg user=".$GLOBALS['phpgw_domain']['default']['db_user']." password=".$GLOBALS['phpgw_domain']['default']['db_pass']) or die('connection failed');
            $sql = "SELECT distinct orgnr FROM breg.personcurrent WHERE fodselsnr ='".$fodselsnr."'";
            $results = pg_query($breg_conn, $sql);
            $orgs = pg_fetch_all($results);
            print_r($sql);
            print_r($orgs);
            pg_close($breg_conn);
            return $orgs;
        }

	}