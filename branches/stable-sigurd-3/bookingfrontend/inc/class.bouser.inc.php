<?php
	class bookingfrontend_bouser
	{
		const SSN_SESSION_KEY = 'ssn';
		
		protected
			$default_module = 'bookingfrontend',
			$ssn = null,
			$organizations = null,
			$module;
		
		public function __construct() {
			$this->set_module();
			$this->ssn = $this->get_user_ssn_from_session();
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
			$this->ssn = $this->get_user_ssn_from_auth_header();
			
			if ($this->is_logged_in()) {
				$this->write_ssn_to_session();
			}
			
			return $this->is_logged_in();
		}
		
		public function log_off()
		{
			$this->clear_ssn();
			$this->clear_organizations();
			$this->clear_user_ssn_from_session();
		}
		
		protected function clear_ssn()
		{
			$this->ssn = null;
		}
		
		public function get_ssn()
		{
			return $this->ssn;
		}
		
		public function is_logged_in()
		{
			return !!$this->get_ssn();
		}
		
		public function is_organization_admin($organization_id = null)
		{
			if ($this->is_logged_in() && count($organizations = $this->administrated_organizations()) > 0) {
				return is_null($organization_id) ? true : $organizations[$organization_id];
			}
			return false;
		}
		
		protected function clear_organizations()
		{
			$this->organizations = null;
		}
		
		public function administrated_organizations()
		{
			if (!is_array($this->organizations))
			{
				$result = null;
				if ($this->is_logged_in()) {
					$org_contact_so = CreateObject('booking.socontact_organization');
					$result = $org_contact_so->read(array('filters' => array('ssn' => $this->ssn)));
				}
				
				$result = is_array($result) ? $result['results'] : array();
				$this->organizations = array();
				
				foreach($result as &$record) {
					$this->organizations[$record['organization_id']] = true;
				}
			}
			
			return $this->organizations;
		}
		
		protected function write_ssn_to_session()
		{
			if (!$this->is_logged_in()) {
				throw new LogicException('Cannot write ssn to session unless user is logged on');
			}
			
			phpgwapi_cache::session_set($this->get_module(), self::SSN_SESSION_KEY, $this->get_ssn());
		}
		
		protected function clear_user_ssn_from_session()
		{
			phpgwapi_cache::session_clear($this->get_module(), self::SSN_SESSION_KEY);
		}
		
		protected function get_user_ssn_from_session()
		{
			try {
				return createObject('booking.sfValidatorNorwegianSSN')->clean(phpgwapi_cache::session_get($this->get_module(), self::SSN_SESSION_KEY));
			} catch (sfValidatorError $e) {
				return null;
			}
		}
		
		protected function get_user_ssn_from_auth_header()
		{
			try {
				#return createObject('booking.sfValidatorNorwegianSSN')->clean(phpgw::get_var('ssn', 'string', '0'));
				return createObject('booking.sfValidatorNorwegianSSN')->clean('20027811111');
			} catch (sfValidatorError $e) {
				return null;
			}
		}
	}