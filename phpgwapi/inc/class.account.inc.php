<?php

	/**
	 * Account data objects
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	 * @package phpgroupware
	 * @subpackage phpgwapi
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
	 * Abstract account data object, used for storing account data
	 *
	 * @property		integer	$id the account id
	 * @property		string	$lid the account login id
	 * @property		string	$firstname the first name of the account
	 * @property		string	$lastname the lastname of the account
	 * @property		string	$passwd the password for the account
	 * @property-read	string	$passwd_hash the account's hashed password
	 * @property		integer	$last_login the unix timestamp of when the user last logged in
	 * @property		string	$last_login_from the IP address which the user last logged in from
	 * @property		integer	$last_passwd_change the unix timestamp of when the user last changed their password
	 * @property		boolean	$enabled is the account currently enabled?
	 * @property		integer	$expires unix timestamp of when the account is due to expire
	 * @property		integer	$person_id the contact id for the account - FIXME rename to contact_id - skwashd apr08
	 * @property		integer $quota the amount of storage for the user in Mb
	 * @property-read	string	$old_loginid the account's hashed password
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category accounts
	 */
	abstract class phpgwapi_account
	{
		/**
		 * User object class name
		 */
		const CLASS_TYPE_USER = 'phpgwapi_user';

		/**
		 * Group object class name
		 */
		const CLASS_TYPE_GROUP = 'phpgwapi_group';

		/**
		 * Group Type account
		 */
		const TYPE_GROUP = 'g';

		/**
		 * User Type account
		 */
		const TYPE_USER = 'u';

		/**
		 * @var array $_data the account data
		 */
		protected $_data = array
		(
			'id'				=> 0,
			'lid'				=> '',
			'firstname'			=> '',
			'lastname'			=> '',
			'passwd'			=> '',
			'passwd_hash'		=> '',
			'last_login'		=> 0,
			'last_login_from'	=> '0.0.0.0',
			'last_passwd_change'=> 0,
			'enabled'			=> false,
			'expires'			=> 0,
			'person_id'			=> 0,
			'quota'				=> 0,
			'old_loginid'		=> '',
			'type' 				=> ''
		);

		/**
		 * @var string $_hash the hash of initial data, used for tracking changes
		 */
		protected $_hash = '';

		/**
		 * Initialise the values of the object - this should only be called from phpgwapi_accounts
		 *
		 * @param array $arr the values to initialise the values of the object with
		 *
		 * @return void
		 *
		 * @internal doesn't validate input or throw Exceptions
		 */
		abstract public function init($arr);

		/**
		 * Check to see if the class data has changed since it was loaded
		 *
		 * @return boolean is the data dirty?
		 */
		public function is_dirty()
		{
			return $this->_hash != $this->_generate_hash();
		}

		/**
		 * Convert object to an array - for backwards compatiability
		 *
		 * @return array the object as an array
		 */
		public function toArray()
		{
			return $this->_data;
		}


		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @param string $name the name of the value to lookup
		 *
		 * @return string|integer the property sought
		 *
		 * @throws Exception on invalid property requested
		 */
		abstract public function __get($name);

		/**
		 * Magic isset for checking if a value of $_data is set or not
		 *
		 * @param string $name the name of the value to set
		 *
		 * @return boolean is the property set?
		 */
		public function __isset($name)
		{
			return isset($this->_data[$name]);
		}

		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @param string         $name  the name of the value to set
		 * @param string|integer $value the value to be assigned
		 *
		 * @return boolean was the property set?
		 */
		abstract public function __set($name, $value);

		/**
		 * Shortcut for getting the fullname of the account
		 *
		 * @return string the full name of the account in the user's preferred format
		 */
		abstract public function __toString();

		/**
		 * Generate a hash of the current values, so quick checking if the values have changed
		 *
		 * @return string hash of current data stored
		 */
		protected function _generate_hash()
		{
			return sha1(serialize($this->_data));
		}

		/**
		 * Check that a firstname is valid
		 *
		 * @param string $name the name to validate
		 *
		 * @return bool is the name valid?
		 *
		 * @throws Exception when name is empty
		 */
		protected function _validate_firstname($name)
		{
			if ( !strlen($name) )
			{
				throw new Exception('First name must not be empty');
			}
		}

		/**
		 * Make sure a contact exists
		 *
		 * @param integer $id   the contact id to lookup
		 * @param string  $type the contact type to lookup
		 *
		 * @return bool does the contact id exist?
		 */
		protected function _validate_contact_id($id, $type)
		{
			$contacts = createObject('phpgwapi.contacts');
			switch ( $type )
			{
				case 'org':
					return !!count($contacts->get_principal_organizations_data($id));

				case 'person':
				default: // just in case?
					return !!count($contacts->get_principal_persons_data($id));
			}
		}
	}

	/**
	 * phpGroupWare group data object
	 *
	 * @category accounts
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 */
	class phpgwapi_group extends phpgwapi_account
	{

		/**
		 * @var array $_data the group data
		 */

		protected $_data = array
		(
			'id'				=> 0,
			'lid'				=> '',
			'firstname'			=> '',
			'lastname'			=> 'Group',
			'passwd_hash'		=> '',
			'enabled'			=> false,
			'expires'			=> 0,
			'person_id'			=> 0,
			'old_loginid'		=> '',
			'type' 				=> 'g'
		);

		/**
		 * Initialise the values of the object - this should only be called from phpgwapi_accounts
		 *
		 * @param array $arr the values to initialise the values of the object with
		 *
		 * @return void
		 *
		 * @internal doesn't validate input or throw Exceptions
		 */
		public function init($arr)
		{
			foreach ( $arr as $key => $val )
			{
				switch ( $key )
				{
					case 'id':
					case 'lid':
					case 'firstname':
					case 'passwd_hash':
					case 'expires':
					case 'enabled':
					case 'person_id':
					case 'quota':
					case 'old_loginid':
						$this->_data[$key] = $val;
					// we ignore the rest
				}
			}
		//	$this->_data['lastname'] = 'Group';
		//	$this->_data['type'] = 'g';
			$this->_hash = $this->_generate_hash();
		}

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			$this->_data['lastname']	= 'Group';
			$this->_data['type'] 		= parent::TYPE_GROUP;
		}

		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @param string $name the name of the value to lookup
		 *
		 * @return string|integer the property sought
		 *
		 * @throws Exception on invalid property requested
		 */
		public function __get($name)
		{
			switch($name)
			{
				case 'id':
				case 'lid':
				case 'passwd_hash':
				case 'firstname':
				case 'lastname':
				case 'expires':
				case 'enabled':
				case 'person_id':
				case 'quota':
				case 'old_loginid':
				case 'type':
					return $this->_data[$name];

				default:
					throw new Exception(lang('Unknown value: %1', $name));
			}
		}

		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @param string         $name  the name of the value to set
		 * @param string|integer $value the value to be assigned
		 *
		 * @return boolean was the property set?
		 */
		public function __set($name, $value)
		{
			switch($name)
			{
				case 'lid':
					$this->_validate_groupname($value);
					break;

				case 'firstname':
					$this->_validate_groupname($value, false);
					break;

				case 'enabled':
						$value = !!$value;

				case 'person_id':
					$this->_validate_contact_id($value, 'org');
					break;
			}
			$this->_data[$name] = $value;
		}

		/**
		 * Shortcut for getting the full group name
		 *
		 * @return string the full group name - in the user's local language
		 */
		public function __toString()
		{
			return lang('%1 Group', $this->_data['firstname']);
		}

		/**
		 * Validate a group name
		 *
		 * @param string  $group  the group name to validate
		 * @param boolean $lookup check if the account already exists
		 *
		 * @return boolean is the group name valid?
		 *
		 * @throws Exception when group name is invalid
		 */
		private function _validate_groupname($group, $lookup = true)
		{
			if ( !strlen($group) )
			{
				throw new Exception('Group name is too short');
			}

			if ( $lookup )
			{
				if ($this->_data['id'])
				{
					if($this->_data['id'] != $GLOBALS['phpgw']->accounts->name2id($group)
						&& $GLOBALS['phpgw']->accounts->name2id($group) )
					{
						throw new Exception('Group name already in use');
					}
				}
				else if($GLOBALS['phpgw']->accounts->name2id($group))
				{
					throw new Exception('Group name already in use');				
				}
			}

			phpgw::import_class('phpgwapi.globally_denied');
			if ( phpgwapi_globally_denied::user($group) )
			{
				throw new Exception('Group name is blocked');
			}
			return true;
		}
	}


	/**
	 * phpGroupWare user data object
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category accounts
	 */
	class phpgwapi_user extends phpgwapi_account
	{

		public function __construct()
		{
			$this->_data['type'] 		= parent::TYPE_USER;
		}

		/**
		 * Initialise the values of the object - this should only be called from phpgwapi_accounts
		 *
		 * @param array $arr the values to initialise the values of the object with
		 *
		 * @return void
		 *
		 * @internal doesn't validate input or throw Exceptions
		 */
		public function init($arr)
		{
			foreach ( $arr as $key => $val )
			{
				switch ( $key )
				{
					case 'lid':
						$this->_data['old_loginid'] = $val;

					case 'id':
					case 'firstname':
					case 'lastname':
					case 'passwd':
					case 'passwd_hash':
					case 'last_login':
					case 'last_login_from':
					case 'last_passwd_change':
					case 'enabled':
					case 'expires':
					case 'enabled':
					case 'person_id':
					case 'quota':
					case 'type':
						$this->_data[$key] = $val;
				}
			}
			$this->_hash = $this->_generate_hash();
		}

		/**
		 * Has the user account expired?
		 *
		 * @return boolean has the account expired?
		 */
		public function is_expired()
		{
			$expires = $this->_data['expires'];
			return $expires <> -1 && $expires < time();
		}

		/**
		 * Convert object to an array - for backwards compatiability
		 *
		 * @return array the object as an array
		 */
		public function toArray()
		{
			$array = $this->_data;
	//		unset($array['passwd'], $array['passwd_hash']);
			unset($array['passwd']);
			return $array;
		}

		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @param string $name the name of the value to lookup
		 *
		 * @return string|integer the property sought
		 *
		 * @throws Exception on invalid property requested
		 *
		 * @todo handle LDAP extended attributes
		 */
		public function __get($name)
		{
			switch($name)
			{
				case 'id':
				case 'lid':
				case 'firstname':
				case 'lastname':
				case 'passwd':
				case 'passwd_hash':
				case 'last_login':
				case 'last_login_from':
				case 'last_passwd_change':
				case 'enabled':
				case 'expires':
				case 'person_id':
				case 'quota':
				case 'old_loginid':
				case 'type':
					return $this->_data[$name];
				default:
					throw new Exception(lang('Unknown value: %1', $name));
			}
		}

		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @param string         $name  the name of the value to set
		 * @param string|integer $value the value to be assigned
		 *
		 * @return boolean was the property set?
		 */
		public function __set($name, $value)
		{
			switch($name)
			{
				case 'id':
					break;

				case 'lid':
					$this->_validate_username($value);
					$this->_data['old_loginid'] = $value;
					break;

				case 'firstname':
					$this->_validate_firstname($value);
					break;

				case 'lastname':
					$this->_validate_lastname($value);
					break;

				case 'passwd':
					$this->validate_password($value);
					$this->_data['passwd_hash'] = ExecMethod('phpgwapi.auth.create_hash', $value);
					$this->_data['last_passwd_change'] = time();
					break;

				case 'passwd_hash':
					$this->_data['password'] = null; // make it invalid
					break;

				case 'last_login':
					$this->_validate_last_login($value);
					$this->_data['last_login_from'] = phpgw::get_var('REMOTE_ADDR', 'ip', 'SERVER', '0.0.0.0');
					break;

				case 'enabled':
					$value = !!$value;
					break;

				case 'expires':
					$this->_validate_expires($value);
					break;

				case 'person_id':
					$this->_validate_contact_id($value, 'person');
					break;

				case 'quota':
					$this->_validate_quota($value);
					break;

				case 'type':
					$this->_data['type'] = 'u';
					break;

				case 'dn':
				case 'homedirectory':
				case 'loginshell':
				case 'mail':
					return $this->_set_ldap_extended($name, $value);

				default:
					$class = get_class($this);
					// trigger notice here & allow execution to continue, we just won't set anything
					$varname = "{$class}::{$name}";
					trigger_error("Attempted to set {$varname} to $value, {$varname} is unknown",
									E_USER_NOTICE);
					return false;
			}
			$this->_data[$name] = $value;
			return true;
		}

		/**
		 * Shortcut for getting the fullname of the account
		 *
		 * @return string the full name of the account in the user's preferred format
		 */
		public function __toString()
		{
			$display = 'firstname';
			if ( isset($GLOBALS['phpgw_info']['user']['preferences']['common']['account_display']) )
			{
				$display = $GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'];
			}

			switch ( $display )
			{
				case 'lastname':
					return "{$this->_data['lastname']}, {$this->_data['firstname']}";

				default:
					return "{$this->_data['firstname']} {$this->_data['lastname']}";
			}
		}

		/**
		 * Set the extended LDAP attributes
		 *
		 * @param string $name  the name of the value to set
		 * @param string $value the value to be assigned
		 *
		 * @return boolean was the property valid and set?
		 */
		protected function _set_ldap_extended($name, $value)
		{
			// only used by ldap
			if ( $GLOBALS['phpgw_info']['server']['account_repository'] != 'ldap' )
			{
				return false;
			}
			switch($name)
			{
				case 'dn':
				case 'homedirectory':
				case 'loginshell':
				case 'mail':
					$this->data['ldap_extended'][$name] = $value;
					return true;
			}
			$name = htmlentities($name, ENT_QUOTES, 'UTF-8');
			$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
			trigger_error("Attempted to set ldap extended attribute '{$name}' to '{$value}',"
					. " {$name} is not a supported attribute", E_USER_NOTICE);
			return false;

		}

		/**
		 * Check that the account expiry time stamp is valid
		 *
		 * @param integer $exp the account expiry expresses as a unix timestamp
		 *
		 * @return boolean is the expiry valid?
		 *
		 * @throws Exception when expiry timestamp is invalid
		 */
		protected function _validate_expires($exp)
		{
			if ( $exp !== 0
				&& (int) $exp != $exp )
			{
				throw new Exception('Expiry date is invalid');
			}
			return true;
		}

		/**
		 * Check that a lastname is valid
		 *
		 * @param string $name the name to validate
		 *
		 * @return boolean is the name valid?
		 *
		 * @throws Exception when name is empty
		 */
		protected function _validate_lastname($name)
		{
			return strlen($name) >= 1;
		}

		/**
		 * Check that a password is valid and secure
		 *
		 * @param string $passwd the password to check
		 *
		 * @return boolean is the password valid and secure?
		 *
		 * @throws Exception when password is invalid/insecure
		 */
		public function validate_password($passwd)
		{			
			$_error = array();
			switch ( $GLOBALS['phpgw_info']['server']['password_level'] )
			{
				default:
				case 'NONALPHA':
					$_error[] = self::_validate_password_level_nonalpha($passwd);
					// fall through
				case '1NUM':
					$_error[] = self::_validate_password_level_1num($passwd);
					// fall through
				case '2LOW':
					$_error[] = self::_validate_password_level_2low($passwd);
					// fall through
				case '2UPPER':
					$_error[] = self::_validate_password_level_2upper($passwd);
					// fall through
				case '8CHAR':
					$_error[] = self::_validate_password_level_8char($passwd);
			}

			$error = array();
			foreach($_error as $_msq)
			{
				if($_msq)
				{
					$error[] = $_msq;
				}
			}
			if($error)
			{
				throw new Exception(implode('<br/>',array_reverse($error)));
			}
		}

		/**
		 * Check that a password is at least 8 characters long
		 *
		 * @param string $passwd the password to check
		 *
		 * @throws Exception when password is shorter than 8 characters long
		 */
		protected static function _validate_password_level_8char($passwd)
		{
			$error = '';
			$len = strlen($passwd); 
			if ( $len < 8 )
			{
				$error = lang('Password must be at least 8 characters long, not %1', $len);
				//throw new Exception(lang('Password must be at least 8 characters long, not %1', $len));
			}
			return $error;
		}

		/**
		 * Check that a password contain at least 2 upper case characters
		 *
		 * @param string $passwd the password to check
		 *
		 * @throws Exception when password do not contain at least 2 upper case characters
		 */
		protected static function _validate_password_level_2upper($passwd)
		{
			$error = '';
			$m = array();
			if ( preg_match_all('/[A-Z]/', $passwd, $m) < 2 )
			{
				$error = lang('Password must contain at least 2 upper case characters');
				//throw new Exception(lang('Password must contain at least 2 upper case characters'));
			}
			return $error;
		}
		/**
		 * Check that a password contain at least 2 lower case characters
		 *
		 * @param string $passwd the password to check
		 *
		 * @throws Exception when password do not contain at least 2 lower case characters
		 */
		protected static function _validate_password_level_2low($passwd)
		{
			$error = '';
			$m = array();
			if ( preg_match_all('/[a-z]/', $passwd, $m) < 2 )
			{
				$error = lang('Password must contain at least 2 lower case characters');
				//throw new Exception(lang('Password must contain at least 2 lower case characters'));
			}
			return $error;
		}
		/**
		 * Check that a password contain at least 1 number
		 *
		 * @param string $passwd the password to check
		 *
		 * @throws Exception when password is invalid/insecure do not contain at least 1 number
		 */
		protected static function _validate_password_level_1num($passwd)
		{
			$error = '';
			$m = array();
			if ( !preg_match_all('/[0-9]/', $passwd, $m) )
			{
				$error = lang('Password must contain at least 1 number');
				//throw new Exception(lang('Password must contain at least 1 number'));
			}
			return $error;
		}
		/**
		 * Check that a password contain at least 1 non alphanumeric character
		 *
		 * @param string $passwd the password to check
		 *
		 * @throws Exception when password do not contain at least 1 non alphanumeric character
		 */
		protected static function _validate_password_level_nonalpha($passwd)
		{
			$error = '';
			$m = array();
			if ( !preg_match_all('/\W/', $passwd, $m)  )
			{
				$error = lang('Password must contain at least 1 non alphanumeric character');
				//throw new Exception(lang('Password must contain at least 1 non alphanumeric character'));
			}
			return $error;
		}

		/**
		 * Check if the specified quota is a valid value
		 *
		 * @param integer $quota the users quota in Kb
		 *
		 * @return boolean is the quota valid?
		 *
		 * @throws Exception if quota is invalid
		 */
		private function _validate_quota($quota)
		{
			if ( (int) $quota != $quota )
			{
				throw new Exception('Invalid quota value');
			}

			//FIXME this should be stored in the API - not filemanager - it is a global value!
			$config = createObject('phpgwapi.config', 'filemanager')->read_repository();
			if ( !isset($config['set_quota']) )
			{
				return true; // misocnfigured, but lets just accept it - for now
			}
			$vals = explode(',', $config['set_quota']);
			foreach ( $vals as $val )
			{
		//		if ( $quota == $val * 1024 ) // compare kb to kb
				if ( $quota == $val )
				{
					return true;
				}
			}
			throw new Exception('Quota value not supported');
		}

		/**
		 * Validate a username
		 *
		 * @param string  $username the username to validate
		 * @param boolean $lookup   check if the account already exists
		 *
		 * @return boolean is the username valid?
		 *
		 * @throws Exception when username is invalid
		 */
		private function _validate_username($username, $lookup = true)
		{
			if ( !strlen($username) )
			{
				throw new Exception('Username is too short');
			}

			if ( $lookup )
			{
				$id = $GLOBALS['phpgw']->accounts->name2id($username);
				if ( $id && $id <> $this->_data['id'] )
				{
					throw new Exception('Username already in use');
				}
			}

			phpgw::import_class('phpgwapi.globally_denied');
			if ( phpgwapi_globally_denied::user($username) )
			{
				throw new Exception('Username is blocked');
			}
			return true;
		}
	}
