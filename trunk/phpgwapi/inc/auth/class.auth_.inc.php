<?php
	/**
	* Authentication based on SQL table
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
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

	phpgw::import_class('phpgwapi.common');
	/**
	* Authentication based on SQL table
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	abstract class phpgwapi_auth_
	{
		public $xmlrpc_methods = array
		(
			array
			(
				'name'       => 'change_password',
				'decription' => 'Change the current users password'
			)
		);

		/**
		* Constructor
		*/
		public function __construct()
		{}

		/**
		* Authenticate a user
		*
		* @param string $username the login to authenticate
		* @param string $passwd the password supplied by the user
		* @return bool did the user authenticate?
		* @return bool did the user sucessfully authenticate
		*/
		abstract public function authenticate($username, $passwd);

		/**
		* Set the user's password to a new value
		*
		* @param string $old_passwd the user's old password
		* @param string $new_passwd the user's new password
		* @param int $account_id the account to change the password for - defaults to current user
		*/
		abstract public function change_password($old_passwd, $new_passwd, $account_id = 0);

		/**
		* Generate random salt
		*
		* @param int $chars number of characters of salt required
		* @return string the salt
		*/
		private function _shake_salt($chars)
		{
			if ( $chars > 32 )
			{
				$chars = 32;
			}

			$salt = substr(md5(uniqid(rand(), true)), 0, $chars);
			return $salt;
		}

		/**
		* Generate a password hash
		*
		* @param string $passwd the password to turn into a hash
		* @return string the hashed password - ready for use
		*/
		public function create_hash($passwd)
		{
			switch ($GLOBALS['phpgw_info']['server']['encryption_type'])
			{
				case 'CRYPT':
					return '{CRYPT}' . crypt($passwd, $this->_shake_salt(CRYPT_SALT_LENGTH));

				case 'MD5':
					return "{MD5}" . base64_encode(phpgwapi_common::hex2bin(md5($passwd)));

				case 'SHA':
					return "{SHA}" . base64_encode(phpgwapi_common::hex2bin(sha1($passwd)));

				case 'SMD5':
					$salt = $this->_shake_salt(4);
					return "{SMD5}" . base64_encode(phpgwapi_common::hex2bin(md5($passwd . $salt) . $salt));

				case 'SSHA':
				default:
					$salt = $this->_shake_salt(4);
					return '{SSHA}' . base64_encode(phpgwapi_common::hex2bin(sha1($passwd . $salt) . $salt));
			}
		}

		/**
		* Verify that a hash matches a password
		* 
		* @param string $passwd the password contained in the hash
		* @param string $hash the hashed version of the password
		* @return bool does the password match the hash?
		*/
		public function verify_hash($passwd, $hash)
		{
			if ( !preg_match('/^{(.*)}(.*)$/', $hash, $m) || count($m) != 3  ) //full string, algorhythm, hash
			{
				// invalid hash
				return false;
			}
			$algo = $m[1];
			$hash = $m[2];
			unset($m);

			switch ( strtoupper($algo) )
			{
				case 'CRYPT':
					//TODO implement this
					return false;
				case 'MD5':
					$hash = bin2hex(base64_decode($hash));
					return $hash === md5($passwd);

				case 'SHA':
					$hash = bin2hex(base64_decode($hash));
					return $hash === sha1($passwd);

				case 'SMD5':
					$hash = bin2hex(base64_decode($hash));
					$salt = substr($hash, 32);
					$hash = substr($hash, 0, 32);
					return $hash === md5($passwd . $salt);

				case 'SSHA':
					$hash = bin2hex(base64_decode($hash));
					$salt = substr($hash, 40);
					$hash = substr($hash, 0, 40);
					return $hash === sha1($passwd . $salt);
			}
		}
	}
