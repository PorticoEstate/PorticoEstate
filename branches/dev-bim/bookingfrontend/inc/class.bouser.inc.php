<?php
	class bookingfrontend_bouser
	{
		const ORGNR_SESSION_KEY = 'orgnr';
		
		protected
			$default_module = 'bookingfrontend',
			$orgnr = null,
			$module;
		
		public function __construct() {
			$this->set_module();
			$this->orgnr = $this->get_user_orgnr_from_session();
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
			$this->orgnr = $this->get_user_orgnr_from_auth_header();
			if ($this->is_logged_in()) {
				$this->write_user_orgnr_to_session();
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
			return $this->orgnr;
		}
		
		public function is_logged_in()
		{
			return !!$this->get_user_orgnr();
		}
		
		public function is_organization_admin($organization_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			return true;
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				return false;
			}
			$so = CreateObject('booking.soorganization');
			$organization = $so->read_single($organization_id);
			return $organization['organization_number'] == $this->orgnr;
		}

		public function is_group_admin($group_id = null)
		{
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			return true;
			// FIXME!!!!!! REMOVE THIS ONCE ALTINN IS OPERATIONAL
			if(!$this->is_logged_in()) {
				return false;
			}
			$so = CreateObject('booking.sogroup');
			$group = $so->read_single($group_id);
			return $this->is_organization_admin($group['organization_id']);
		}
		
		protected function write_user_orgnr_to_session()
		{
			if (!$this->is_logged_in()) {
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
		
		protected function get_user_orgnr_from_auth_header()
		{
			try  {
				// FIXME: Extract this from some HTTP header and not a GET parameter
				return createObject('booking.sfValidatorNorwegianOrganizationNumber')->clean(phpgw::get_var('orgnr', 'string', '0'));
			} catch (sfValidatorError $e) {
				return null;
			}
		}
	}