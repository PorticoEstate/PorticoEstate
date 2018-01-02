<?php
	/**
	 * property - Hook helper
	 *
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2015 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @package rental
	 * @version $Id: class.hook_helper.inc.php 11076 2013-04-25 07:19:14Z sigurdne $
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
	 * @package rental
	 */
	class rental_hook_helper
	{

		/**
		 * Add a contact to a location
		 *
		 * @return void
		 */
		public function add_contract_from_composite( &$data )
		{
			if (!isset($data['location_code']) || !$data['location_code'])
			{
				phpgwapi_cache::message_set("location_code not set", 'error');
				return false;
			}

			$criteria = array
				(
				'appname' => 'rental',
				'location' => $data['acl_location'],
				'pre_commit' => true,
				'allrows' => true
			);

			$custom_functions = $GLOBALS['phpgw']->custom_functions->find($criteria);

			foreach ($custom_functions as $entry)
			{
				// prevent path traversal
				if (preg_match('/\.\./', $entry['file_name']))
				{
					continue;
				}

				$file = PHPGW_SERVER_ROOT . "/rental/inc/custom/{$GLOBALS['phpgw_info']['user']['domain']}/{$entry['file_name']}";
				if ($entry['active'] && is_file($file) && !$entry['client_side'])
				{
					require $file;
				}
			}
		}
	}