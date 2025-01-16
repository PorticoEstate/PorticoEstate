<?php
	/**
	* Authentication based on SQL table
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2013 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Authentication based on SQL table
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_customsso extends phpgwapi_auth_
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

		/* php ping function
		*/
		private function ping($host)
		{
	        exec(sprintf('ping -c 1 -W 5 %s', escapeshellarg($host)), $res, $rval);
	        return $rval === 0;
		}


		public function get_username()
		{
			$headers = array_change_key_case(getallheaders(), CASE_LOWER);
			$ssn = $headers['uid'];

			$remote_user = $headers['REMOTE_USER'] ? $headers['REMOTE_USER'] : $headers['upn'];
			$username_arr  = explode('@', $remote_user);
			$username = $username_arr[0];

			/**
			 * Shibboleth from inside firewall
			 */
			if($username && !$ssn)
			{
				return $username;
			}

			/**
			 * Shibboleth from outside firewall
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

			$db = createObject('phpgwapi.db_adodb', null, null, true);

			// Alternative config
			$config = CreateObject('admin.soconfig', $GLOBALS['phpgw']->locations->get_id('property', '.admin'));

			if ($config->config_data['fellesdata']['host'])
			{
				if( !$this->ping($config->config_data['fellesdata']['host']))
				{
					$message = "Database server {$config->config_data['fellesdata']['host']} is not accessible";
					phpgwapi_cache::message_set($message, 'error');
				}

				$db->debug = false;
				$db->Host = $config->config_data['fellesdata']['host'];
				$db->Port = $config->config_data['fellesdata']['port'];
				$db->Type = 'oracle';
				$db->Database = $config->config_data['fellesdata']['db_name'];
				$db->User = $config->config_data['fellesdata']['user'];
				$db->Password = $config->config_data['fellesdata']['password'];

			}
			else
			{
				$config	= CreateObject('phpgwapi.config','rental')->read();

				if(! $config['external_db_host'] || !$this->ping($config['external_db_host']))
				{
					$message ="Database server {$config['external_db_host']} is not accessible";
					phpgwapi_cache::message_set($message, 'error');
				}

				$db->debug = !!$config['external_db_debug'];
				$db->Host = $config['external_db_host'];
				$db->Port = $config['external_db_port'];
				$db->Type = $config['external_db_type'];
				$db->Database = $config['external_db_name'];
				$db->User = $config['external_db_user'];
				$db->Password = $config['external_db_password'];
			}

			try
			{
				$db->connect();
			}
			catch(Exception $e)
			{
				$message = lang('unable_to_connect_to_database');
				phpgwapi_cache::message_set($message, 'error');
				return false;
			}

			$sql = "SELECT BRUKERNAVN FROM V_AD_PERSON WHERE FODSELSNR ='{$ssn}'";
			$db->query($sql,__LINE__,__FILE__);
			$db->next_record();
			$username = $db->f('BRUKERNAVN',true);
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
