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
			$this->db = & $GLOBALS['phpgw']->db;
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
			$this->orgname = $this->get_orgname_from_db($this->get_user_orgnr_from_session());
			$this->config = CreateObject('phpgwapi.config', 'bookingfrontend');
			$this->config->read();
		}

		protected function get_orgname_from_db( $orgnr )
		{
			if(!$orgnr)
			{
				return null;
			}
			$this->db->limit_query("SELECT name FROM bb_organization WHERE organization_number ='" . $orgnr . "'", 0, __LINE__, __FILE__, 1);
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


		protected function current_app()
		{
			return $GLOBALS['phpgw_info']['flags']['currentapp'];
		}
		/**
		 * Validate external safe login - and return to me
		 * @param array $redirect
		 */
		public function validate_ssn_login( $redirect = array())
		{
			if(!empty($this->config->config_data['test_ssn']))
			{
				$_SERVER['HTTP_UID'] = 	$this->config->config_data['test_ssn'];
				phpgwapi_cache::message_set('Warning: ssn is set by test-data', 'error');
			}

			$ssn = (string)$_SERVER['HTTP_UID'];

			if( isset($this->config->config_data['bypass_external_login']) && $this->config->config_data['bypass_external_login'] )
			{
				return array(
					'ssn'	=> $ssn,
					'phone' => (string)$_SERVER['HTTP_MOBILTELEFONNUMMER'],
					'email'	=> (string)$_SERVER['HTTP_EPOSTADRESSE']
					);
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

			return $ret;
		}

	}