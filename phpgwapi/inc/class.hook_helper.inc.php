<?php
	/**
	 * phpgwapi - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Property
	 * @version $Id$
	 */
	/*
	  This program is free software: you can redistribute it and/or modify
	  it under the terms of the GNU General Public License as published by
	  the Free Software Foundation, either version 2 of the License, or
	  (at your option) any later version.

	  This program is distributed in the hope that it will be useful,
	  but WITHOUT ANY WARRANTY; without even the implied warranty of
	  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	  GNU General Public License for more details.

	  You should have received a copy of the GNU General Public License
	  along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	 * Hook helper
	 *
	 * @package phpgwapi
	 */
	class phpgwapi_hook_helper
	{

		private $perform_action = false;

		public function __construct()
		{
			$script_path = dirname(phpgw::get_var('SCRIPT_FILENAME', 'string', 'SERVER'));
			if (PHPGW_SERVER_ROOT == $script_path)
			{
				$this->perform_action = true;
			}
		}

		public function set_cookie_domain()
		{
			if (!$this->perform_action)
			{
				return;
			}

			if ($this->is_external_login() && !empty($GLOBALS['phpgw_info']['server']['alternative_cookie_domain']))
			{
				$GLOBALS['phpgw_info']['server']['cookie_domain'] = $GLOBALS['phpgw_info']['server']['alternative_cookie_domain'];
			}
		}

		/**
		 * This one is a temporary hack to get around a poorly configured reverse proxy 
		 * @return boolean
		 */
		private function is_external_login()
		{
			$ssn = isset($_SERVER['HTTP_UID']) ? (string)$_SERVER['HTTP_UID'] : '';
			try
			{
				$sf_validator = createObject('booking.sfValidatorNorwegianSSN', array(), array(
					'invalid' => 'ssn is invalid'));
				$sf_validator->setOption('required', true);
				$sf_validator->clean($ssn);
			}
			catch (sfValidatorError $e)
			{
				return false;
			}

			return true;
		}
	}