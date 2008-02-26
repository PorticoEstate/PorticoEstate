<?php
	/**
	* Authentication based on SQL table
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on SQL table
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_sql extends phpgwapi_auth_
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
			if ( !$GLOBALS['phpgw']->db->next_record() )
			{
				return false;
			}

			$hash = $GLOBALS['phpgw']->db->f('account_pwd', true);
			return $this->verify_hash($passwd, $hash);
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

			$hash = $this->generate_hash($new_password);
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
