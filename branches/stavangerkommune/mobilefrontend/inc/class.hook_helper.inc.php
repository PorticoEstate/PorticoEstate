<?php
	/**
	 * Mobilefrontend - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package controller
	 * @version $Id: class.hook_helper.inc.php 11511 2013-12-08 20:57:07Z sigurdne $
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
	 * @package controller
	 */
	class mobilefrontend_hook_helper
	{
		/**
		 * set auth_type for custom login - called from login
		 *
		 * @return void
		 */
		public function set_auth_type()
		{
			//get from local config

			$config		= CreateObject('phpgwapi.config','mobilefrontend');
			$config->read();
			
			if(isset($config->config_data['auth_type']) && $config->config_data['auth_type'])
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = $config->config_data['auth_type'];
			}

//_debug_array($GLOBALS['phpgw_info']['server']);die();
		}
	}
