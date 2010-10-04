<?php
	/**
	* Authentication based on ntlm auth
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Authentication based on ntlm auth
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class phpgwapi_auth_ntlm extends phpgwapi_auth_
	{

		public function __construct()
		{
			parent::__construct();
		}

		public function authenticate($username, $passwd)
		{
			return isset($_SERVER['REMOTE_USER']) && !!strlen($_SERVER['REMOTE_USER']);
		}

		public function change_password($old_passwd, $new_passwd, $account_id = 0)
		{
			// not yet supported - this script would change the windows domain password
			return '';
		}

		/**
		* Update when the user last logged in
		*
		* @param int $account_id the user's account id
		* @param string $ip the source IP adddress for the request
		*/
		public function update_lastlogin($account_id, $ip)
		{
			$ip = $GLOBALS['phpgw']->db->db_addslashes($ip);
			$account_id = (int) $account_id;
			$now = time();

			$sql = 'UPDATE phpgw_accounts'
				. " SET account_lastloginfrom = '{$ip}',"
					. " account_lastlogin = {$now}"
				. " WHERE account_id = {$account_id}";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
		}

	}
