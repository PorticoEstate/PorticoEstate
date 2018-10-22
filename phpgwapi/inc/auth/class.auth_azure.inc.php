<?php
	/**
	* Authentication based on Azure AD
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2018 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Authentication based on Azure AD
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_azure extends phpgwapi_auth_
	{

		/**
		* Constructor
		*/
		public function __construct()
		{
			parent::__construct();
		}

		/**
		* Authenticate a user
		*
		* @param string $username the login to authenticate
		* @param string $passwd the password supplied by the user
		* @return bool did the user sucessfully authenticate
		*/
		public function authenticate($username, $passwd)
		{
			$username = $GLOBALS['phpgw']->db->db_addslashes($username);

			$sql = 'SELECT account_pwd FROM phpgw_accounts'
				. " WHERE account_lid = '{$username}'"
					. " AND account_status = 'A'";

			$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
			return !!$GLOBALS['phpgw']->db->next_record();

		}


		/**
		 * Ask azure for credential - and return the username
		 * @return string $usernamer
		 */
		public function get_username()
		{
			$headers = getallheaders();
			
			/**
			 * Will need some config values to be generic
			 */

			$username = 'some magic to validate username from azure';
	
			return $username;

		}

		/**
		* Set the user's password to a new value
		*
		* @param string $old_passwd the user's old password
		* @param string $new_passwd the user's new password
		* @param int $account_id the account to change the password for - defaults to current user
		* @return string the new encrypted hash, or an empty string on failure
		*/
		public function change_password($old_passwd, $new_passwd, $account_id = 0)
		{
			$account_id = (int) $account_id;
			// Don't allow passwords changes for other accounts when using XML-RPC
			if ( !$account_id )
			{
				$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			if ( $GLOBALS['phpgw_info']['flags']['currentapp'] == 'login')
			{
				if ( !$this->authenticate($GLOBALS['phpgw']->accounts->id2lid($account_id), $old_passwd) )
				{
					return '';
				}
			}

			$hash = $this->create_hash($new_passwd);
			$hash_safe = $GLOBALS['phpgw']->db->db_addslashes($hash); // just to be safe :)
			$now = time();

			$sql = 'UPDATE phpgw_accounts'
				. " SET account_pwd = '{$hash_safe}', account_lastpwd_change = {$now}"
				. " WHERE account_id = {$account_id}";

			if ( !!$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__) )
			{
				return $hash;
			}
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
