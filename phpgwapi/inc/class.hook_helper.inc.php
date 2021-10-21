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

			$config = array(
				'alternative_domain_ip'	 => array('10.120.67.11', '10.120.67.12', '10.120.67.13'), //hardcoded for now
				'alternative_domain'	 => 'bergen.kommune.no'
			);

			$ip_address = phpgw::get_ip_address();

			if (!empty($config['alternative_domain']) && in_array($ip_address, $config['alternative_domain_ip']))
			{
				$GLOBALS['phpgw_info']['server']['cookie_domain'] = $config['alternative_domain'];
			}
		}
	}