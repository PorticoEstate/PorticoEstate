<?php
	/**
	 * Bookingfrontend - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package Property
	 * @version $Id: class.hook_helper.inc.php 14728 2016-02-11 22:28:46Z sigurdne $
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
	 * @package bookingfrontend
	 */
	class bookingfrontend_hook_helper
	{

		private $perform_action = false;

		public function __construct()
		{
			$script_path = dirname(phpgw::get_var('SCRIPT_FILENAME', 'string', 'SERVER'));

			if(preg_match('/bookingfrontend/', $script_path))
			{
				$this->perform_action = true;
			}
		}

		public function set_cookie_domain()
		{
			if(!$this->perform_action)
			{
				return;
			}
			//get from local config
			$config = CreateObject('phpgwapi.config', 'bookingfrontend');
			$config->read();

			$GLOBALS['phpgw_info']['server']['cookie_domain'] = !empty($GLOBALS['phpgw_info']['server']['cookie_domain']) ? $GLOBALS['phpgw_info']['server']['cookie_domain'] : '';

			if (!empty($config->config_data['cookie_domain']))
			{
				$GLOBALS['phpgw_info']['server']['cookie_domain'] = $config->config_data['cookie_domain'];
			}
			$GLOBALS['phpgw_info']['server']['usecookies'] = $config->config_data['usecookies'];
		}
	}