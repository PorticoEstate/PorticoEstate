<?php

	class bookingfrontend_bouser
	{

		const ORGNR_SESSION_KEY = 'orgnr';
		const ORGID_SESSION_KEY = 'org_id';
		const ORGARRAY_SESSION_KEY = 'orgarray';
		const USERARRAY_SESSION_KEY = 'userarray';

		public $ssn					 = null;
		/*
		 * Official public identificator
		 */
		public $orgnr				 = null;
		public $orgname				 = null;

		/*
		 * Internal identificator
		 */
		public $org_id				 = null;
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
		var $db;

		public function __construct($get_external_login_info = null)
		{
			$this->db = & $GLOBALS['phpgw']->db;
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
			$this->org_id = $this->get_user_org_id_from_session();
			
			$session_org_id = phpgw::get_var('session_org_id', 'int', 'GET');
//			if($get_external_login_info && $this->is_logged_in())
			if($session_org_id && $this->is_logged_in())
			{
				$orgs = phpgwapi_cache::session_get($this->get_module(), self::ORGARRAY_SESSION_KEY);

				if ($session_org_id && ($session_org_id != $this->org_id) && in_array($session_org_id, array_map("self::get_ids_from_array", $orgs)))
				{
					try
					{
						$session_org_nr = '';
						foreach ($orgs as $org)
						{
							if($org['org_id'] == $session_org_id)
							{
								$session_org_nr = $org['orgnr'];
							}
						}
						$org_number = createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean($session_org_nr);
						if ($org_number)
						{
							$this->change_org($session_org_id);
						}
					}
					catch (sfValidatorError $e)
					{
						$session_org_id = -1;
					}
				}
				$external_login_info = $this->validate_ssn_login();
				$this->ssn = $external_login_info['ssn'];
			}

			$this->orgname = $this->get_orgname_from_db($this->orgnr, $this->ssn);
			$this->config = CreateObject('phpgwapi.config', 'bookingfrontend');
			$this->config->read();
			if (!empty($this->config->config_data['debug']))
			{
				$this->debug = true;
			}
		}

		function get_ids_from_array( $org )
		{
			return $org['org_id'];
		}

		protected function get_orgname_from_db( $orgnr, $customer_ssn = null, $org_id = null)
		{
			if(!$orgnr)
			{
				return null;
			}

			if($org_id)
			{
				$this->db->query("SELECT name FROM bb_organization WHERE id =". (int)$org_id, __LINE__, __FILE__);
			}
			else if($orgnr == '000000000' && $customer_ssn)
			{
				$this->db->limit_query("SELECT name FROM bb_organization WHERE customer_ssn ='{$customer_ssn}'", 0, __LINE__, __FILE__, 1);
			}
			else
			{
				$this->db->limit_query("SELECT name FROM bb_organization WHERE organization_number ='{$orgnr}'", 0, __LINE__, __FILE__, 1);
			}
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

			$orginfo = $external_user->get_user_orginfo();
			$this->orgnr = $orginfo['orgnr'];
			$this->org_id = $orginfo['org_id'];
			$this->orgname = $this->get_orgname_from_db($orginfo['orgnr'], $orginfo['ssn'], $orginfo['org_id'] );

			if ($this->is_logged_in())
			{
				$this->write_user_orgnr_to_session();
			}

			if ($this->debug)
			{
//				echo 'is_logged_in():<br>';
//				_debug_array($this->is_logged_in());
			}

			return $this->is_logged_in();
		}

		public function change_org( $org_id )
		{
			$orgs = phpgwapi_cache::session_get($this->get_module(), self::ORGARRAY_SESSION_KEY);
			$orglist = array();
			foreach ($orgs as $org)
			{
				$orglist[] = $org['org_id'];

				if($org['org_id'] == $org_id)
				{
					$this->orgnr = $org['orgnr'];
				}
			}
			if (in_array($org_id, $orglist))
			{

				$this->org_id = $org_id;
				$this->orgname = $this->get_orgname_from_db($this->orgnr, $this->ssn, $this->org_id);

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
			$this->clear_user_org_id_from_session();
		}

		protected function clear_user_orgnr()
		{
			$this->org_id = null;
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

		public function get_user_org_id()
		{
			if (!$this->org_id)
			{
				$this->org_id = $this->get_user_org_id_from_session();
			}
			return $this->org_id;
		}

		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}

		public function is_organization_admin( $organization_id = null, $organization_number = null )
		{
			if (!$this->is_logged_in())
			{
				return false;
			}

			/**
			 * On user adding organization from bookingfrontend
			 */
			if(!$organization_id && $organization_number)
			{
				$orgs = (array)phpgwapi_cache::session_get($this->get_module(), self::ORGARRAY_SESSION_KEY);

				$orgs_map = array();
				foreach ($orgs as $org)
				{
					$orgs_map[] = $org['orgnr'];
				}
				unset($org);
				return in_array($organization_number, $orgs_map);
			}

			$so = CreateObject('booking.soorganization', true);
			$organization = $so->read_single($organization_id);
			$customer_ssn = $organization['customer_ssn'];

			if ($organization_id && $customer_ssn)
			{
				$external_login_info = $this->validate_ssn_login();
				return $customer_ssn == $external_login_info['ssn'];
			}

			if ($organization['organization_number'] == '')
			{
				return false;
			}

			return $organization_id == $this->org_id;
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
			phpgwapi_cache::session_set($this->get_module(), self::ORGID_SESSION_KEY, $this->get_user_org_id());

		}

		protected function clear_user_orgnr_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::ORGNR_SESSION_KEY);
		}

		protected function clear_user_org_id_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::ORGID_SESSION_KEY);
		}

		protected function clear_user_orglist_from_session()
		{
#			phpgwapi_cache::session_clear($this->get_module(), self::ORGARRAY_SESSION_KEY);
		}

		protected function get_user_org_id_from_session()
		{
			return phpgwapi_cache::session_get($this->get_module(), self::ORGID_SESSION_KEY);
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


		protected function current_app()
		{
			return $GLOBALS['phpgw_info']['flags']['currentapp'];
		}
		/**
		 * Validate external safe login - and return to me
		 * @param array $redirect
		 */
		public function validate_ssn_login( $redirect = array(), $skip_redirect = false)
		{
			static $user_data = array();
			if(!$user_data)
			{
				$user_data = phpgwapi_cache::session_get($this->get_module(), self::USERARRAY_SESSION_KEY);
			}
			if(!empty($user_data['ssn']))
			{
				return $user_data;
			}

			if(!empty($this->config->config_data['test_ssn']))
			{
				$ssn = 	$this->config->config_data['test_ssn'];
				phpgwapi_cache::message_set('Warning: ssn is set by test-data', 'error');
			}
			else if (!empty($_SERVER['HTTP_UID']))
			{
				$ssn = (string)$_SERVER['HTTP_UID'];
			}
			else
			{
				$ssn = (string)$_SERVER['OIDC_pid'];
			}

			if( isset($this->config->config_data['bypass_external_login']) && $this->config->config_data['bypass_external_login'] )
			{
				$ret =  array(
					'ssn'	=> $ssn,
					'phone' => (string)$_SERVER['HTTP_MOBILTELEFONNUMMER'],
					'email'	=> (string)$_SERVER['HTTP_EPOSTADRESSE']
					);
				phpgwapi_cache::session_set($this->get_module(), self::USERARRAY_SESSION_KEY, $ret);

				return $ret;
			}

			$configfrontend	= CreateObject('phpgwapi.config','bookingfrontend')->read();

			try
			{
				$sf_validator = createObject('booking.sfValidatorNorwegianSSN', array(), array(
				'invalid' => 'ssn is invalid'));
				$sf_validator->setOption('required', true);
				$sf_validator->clean($ssn);
			}
			catch (sfValidatorError $e)
			{
				if($skip_redirect)
				{
					return array();
				}

				if(phpgw::get_var('second_redirect', 'bool'))
				{
					phpgw::no_access($this->current_app(), 'Du må logge inn via ID-porten');
				}

				phpgwapi_cache::session_set('bookingfrontend', 'redirect', json_encode($redirect));

				$login_parameter = isset($configfrontend['login_parameter']) && $configfrontend['login_parameter'] ? $configfrontend['login_parameter'] : '';
				$custom_login_url = isset($configfrontend['custom_login_url']) && $configfrontend['custom_login_url'] ? $configfrontend['custom_login_url'] : '';
				if($custom_login_url && $login_parameter)
				{
					if(strpos($custom_login_url, '?'))
					{
						$sep = '&';
					}
					else
					{
						$sep = '?';
					}
					$login_parameter = ltrim($login_parameter, '&');
					$custom_login_url .= "{$sep}{$login_parameter}";
				}

				if($custom_login_url)
				{
					header('Location: ' . $custom_login_url);
					exit;
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link('/bookingfrontend/login.php');
				}
			}

			$ret = array(
				'ssn'	=> $ssn,
				'phone' => (string)$_SERVER['HTTP_MOBILTELEFONNUMMER'],
				'email'	=> (string)$_SERVER['HTTP_EPOSTADRESSE']
				);

			$get_name_from_external = isset($configfrontend['get_name_from_external']) && $configfrontend['get_name_from_external'] ? $configfrontend['get_name_from_external'] : '';

			$file = PHPGW_SERVER_ROOT . "/bookingfrontend/inc/custom/default/{$get_name_from_external}";

			if (is_file($file))
			{
				require_once $file;
				$external_user = new bookingfrontend_external_user_name();
				try
				{
					$external_user->get_name_from_external_service( $ret );
				}
				catch (Exception $exc)
				{
				}
			}

			phpgwapi_cache::session_set($this->get_module(), self::USERARRAY_SESSION_KEY, $ret);

			return $ret;
		}

	}