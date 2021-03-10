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
			$ssn = phpgw::get_var('OIDC_pid', 'string', 'SERVER');

			$remote_user = explode('@', phpgw::get_var('OIDC_upn', 'string', 'SERVER'));

			$username  = $remote_user[0];

			/**
			 * Azure from inside firewall
			 */
			if($username && !$ssn)
			{
				return $username;
			}

			/**
			 * ID-porten from outside firewall
			 */
			if(!$ssn)
			{
				return;
			}

			$ssn_hash = "{SHA}" . base64_encode(phpgwapi_common::hex2bin(sha1($ssn)));

			$hash_safe = $GLOBALS['phpgw']->db->db_addslashes($ssn_hash); // just to be safe :)
			$sql = "SELECT account_lid FROM phpgw_accounts"
				. " JOIN phpgw_accounts_data ON phpgw_accounts.account_id = phpgw_accounts_data.account_id"
				. " WHERE account_data->>'ssn_hash' = '{$hash_safe}'";
			$GLOBALS['phpgw']->db->query($sql,__LINE__,__FILE__);
			$GLOBALS['phpgw']->db->next_record();
			$username = $GLOBALS['phpgw']->db->f('account_lid',true);

			if($username)
			{
				return $username;
			}

	
			return $username;

		}

		/**
		 * Create useraccount on login for SSO/ntlm
		 *
		 * @return void
		 */
		public function auto_addaccount()
		{
			$account_lid = $GLOBALS['hook_values']['account_lid'];

			if (!$GLOBALS['phpgw']->accounts->exists($account_lid))
			{
				$autocreate_user = !empty($GLOBALS['phpgw_info']['server']['autocreate_user']) ? true  : false;
				$required_group_id = !empty($GLOBALS['phpgw_info']['server']['required_group_id']) ? $GLOBALS['phpgw_info']['server']['required_group_id'] : 0;
				$required_group_lid = $GLOBALS['phpgw']->accounts->name2id($required_group_id);

				$ad_groups = json_decode(phpgw::get_var('OIDC_groups', 'string', 'SERVER'), true);

				if ($autocreate_user && in_array($required_group_lid, $ad_groups))
				{
					$user = array(
						'username'	=> $account_lid,
						'firstname'	=> phpgw::get_var('OIDC_given_name', 'string', 'SERVER'),
						'lastname'	=> phpgw::get_var('OIDC_family_name', 'string', 'SERVER'),
						'email'		=> phpgw::get_var('OIDC_email', 'string', 'SERVER'),
						'ssn'		=> phpgw::get_var('OIDC_pid', 'string', 'SERVER'),
					);

					if ($fellesdata_user)
					{
						$user['password'] = 'PEre' . mt_rand(100, mt_getrandmax()) . '&';
						if (self::create_account($user, $required_group_lid))
						{
							$GLOBALS['phpgw']->redirect_link('/login.php', array());
						}
					}
				}
			}
		}

		/**
		 * Try to create a phpgw user
		 *
		 * @param string $username	the username
		 * @param string $firstname	the user's first name
		 * @param string $lastname the user's last name
		 * @param string $password	the user's password
		 */
		public static function create_account( array $user, string $required_group_lid )
		{
			$username	 = $user['username'];
			$firstname	 = $user['firstname'];
			$lastname	 = $user['lastname'];
			$email		 = $user['email'];
			$password	 = $user['password'];

			// check for required
			if (!$GLOBALS['phpgw']->accounts->exists($required_group_lid)) // No group account exist
			{
				return false;
			}
			else
			{
				$required_group_id = $GLOBALS['phpgw']->accounts->name2id($required_group_lid);
			}

			if (!empty($username) && !empty($firstname) && !empty($lastname) && !empty($password))
			{
				if (!$GLOBALS['phpgw']->accounts->exists($username))
				{
					$account = new phpgwapi_user();
					$account->lid = $username;
					$account->firstname = $firstname;
					$account->lastname = $lastname;
					$account->passwd = $password;
					$account->enabled = true;
					$account->expires = -1;
					$result = $GLOBALS['phpgw']->accounts->create($account, array($required_group_id), array(), array());
					if ($result)
					{
						if (!empty($email))
						{
							$title = lang('User access');
							$message = lang('account has been created');
							$from = "noreply<noreply@{$GLOBALS['phpgw_info']['server']['hostname']}>";
							$send	 = CreateObject('phpgwapi.send');

							try
							{
								$send->msg('email', $email, $title, stripslashes(nl2br($message)), '', '', '', $from, 'System message', 'html', '', array(), false);
							} 
							catch (Exception $ex)
							{

							}
						}
						$preferences = createObject('phpgwapi.preferences', $result);
						$preferences->add('common', 'default_app', 'frontend');
						$preferences->save_repository();

						$GLOBALS['phpgw']->log->write(array('text' => 'I-Notification, user created %1',
							'p1' => $username));
					}

					return $result;
				}
			}
			return false;
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
