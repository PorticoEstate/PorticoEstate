<?php
	class bookingfrontend_bouser
	{
		const ORGNR_SESSION_KEY = 'orgnr';

		public
			$orgnr = null;

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

		public function __construct() {
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
			$this->config		= CreateObject('phpgwapi.config','bookingfrontend');
			$this->config->read();
		}

		protected function set_module($module = null)
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

			$authentication_method	= isset($this->config->config_data['authentication_method']) && $this->config->config_data['authentication_method'] ? $this->config->config_data['authentication_method'] : '';
			
			if(!$authentication_method)
			{
				throw new LogicException('authentication_method not chosen');			
			}
			
			$file = PHPGW_SERVER_ROOT."/bookingfrontend/inc/custom/default/{$authentication_method}";

			if(!is_file($file))
			{
				throw new LogicException("authentication method \"{$authentication_method}\" not available");			
			}

			require_once $file;

			$external_user = new bookingfrontend_external_user();
			
			$this->orgnr = $external_user->get_user_org_id();

			if ($this->is_logged_in())
			{
				$this->write_user_orgnr_to_session();
			}

			if($this->debug)
			{
				echo 'is_logged_in():<br>';
				_debug_array($this->is_logged_in());
				echo 'Session:<br>';
				_debug_array($_SESSION);
				die();
			}

			return $this->is_logged_in();
		}

		public function log_off()
		{
			$this->clear_user_orgnr();
			$this->clear_user_orgnr_from_session();
		}

		protected function clear_user_orgnr()
		{
			$this->orgnr = null;
		}

		public function get_user_orgnr()
		{
			if(!$this->orgnr)
			{
				$this->orgnr = $this->get_user_orgnr_from_session();
			}
			return $this->orgnr;
		}

		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}

		public function is_organization_admin($organization_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
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

		public function is_group_admin($group_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if (strcmp($_SERVER['SERVER_NAME'], 'dev.redpill.se') == 0 || strcmp($_SERVER['SERVER_NAME'], 'bk.localhost') == 0)
			{
				//return true;
			}
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
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

		protected function get_user_orgnr_from_session()
		{
			try {
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean(phpgwapi_cache::session_get($this->get_module(), self::ORGNR_SESSION_KEY));
			} catch (sfValidatorError $e) {
				return null;
			}
		}
	}
