<?php

	/**
	 * Account data objects
	 * 
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v3 or later
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @version $Id: class.accounts_.inc.php 779 2008-02-26 09:53:55Z dave $
	 */

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
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
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category accounts
	 */
	abstract class phpgwapi_account
	{
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
			'enabled'			=> true,
			'expires'			=> 0,
			'person_id'			=> 0,
			'quota'				=> 0
		);

		/**
		 * @var string the hash of initial data, used for tracking changes
		 */
		protected $hash = '';

		/**
		 * Check to see if the class data has changed since it was loaded
		 *
		 * @return boolean is the data dirty?
		 */
		public function is_dirty()
		{
			return $this->hash == $this->_generate_hash();
		}

		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @param string $name the name of the value to lookup
		 */
		abstract public function __get($name);

		/**
		 * Magic isset for checking if a value of $_data is set or not
		 *
		 * @param string $name the name of the value to set
		 */
		public function __isset($name)
		{
			return isset($this->_data[$name]);
		}

		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @param string $name the name of the value to set
		 * @param string|int the value to be assigned
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
		 * @throws Exception when name is empty
		 * @param string $name the name to validate
		 * @return bool is the name valid?
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
		 * @param int $id the contact id to lookup
		 * @param string $type the contact type to lookup
		 * @return bool does the contact id exist?
		 */
		protected function _validate_contact_id($id, $type)
		{
			$contacts = createObject('phpgwapi.contacts');
			switch ( $type )
			{
				case 'org':
					return !!count($contacts->get_principal_org_data($id));

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
		public function __construct()
		{
			$this->_data['lastname'] = 'Group';
		}

		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @param string $name the name of the value to lookup
		 */
		public function __get($name)
		{
			switch($name)
			{
				case 'id':
				case 'lid':
				case 'firstname':
				case 'lastname':
				case 'enabled':
				case 'person_id':
					return $this->_data[$name];
				default:
					throw new Exception("Unknown value: $name");
			}
		}

		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @param string $name the name of the value to set
		 * @param string|int the value to be assigned
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
					$this->_validate_person_id($id, 'org');
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
		 * @throws Exception when group name is invalid
		 * @param string $username the group name to validate
		 * @param boolean $lookup check if the account already exists
		 * @return boolean is the group name valid?
		 */
		private function _validate_groupname($group, $lookup = true)
		{
			if ( !strlen($group) )
			{
				throw new Exception('Group name is too short');
			}

			if ( $lookup && $GLOBALS['phpgw']->accounts->search(array('lid' => $group, 'type' => 'g')) )
			{
				throw new Exception('Group name already in use');
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
		/**
		 * Initialise the values of the object - this should only be called from phpgwapi_accounts
		 *
		 * @internal FIXME this needs to work properly
		 */
		public function init($arr)
		{
			foreach ( $arr as $key => $val )
			{
				$this->_data[$key] = $val;
			}
		}

		/**
		 * Convert object to an array - for backwards compatiability
		 *
		 * return array the object as an array
		 */
		public function toArray()
		{
			$array = $this->_data;
			unset($array['passwd'], $array['passwd_hash']);
			return $array;
		}

		/**
		 * Magic getter function, for getting values from $_data
		 *
		 * @todo handle LDAP extended attributes 
		 * @param string $name the name of the value to lookup
		 */
		public function __get($name)
		{
			switch($name)
			{
				case 'id':
				case 'lid':
				case 'firstname':
				case 'lastname':
				//case 'passwd':
				case 'last_login':
				case 'last_login_from':
				case 'last_passwd_change':
				case 'enabled':
				case 'expired':
				case 'person_id':
				case 'quota':
					return $this->_data[$name];
				default:
					throw new Exception("Unknown value: {$name}");
			}
		}
	
		/**
		 * Magic setter function, for setting values in $_data
		 *
		 * @todo handle LDAP extended attributes 
		 * @param string $name the name of the value to set
		 * @param string|int the value to be assigned
		 */
		public function __set($name, $value)
		{
			switch($name)
			{
				case 'lid':
					$this->_validate_username($value);
					break;

				case 'firstname':
					$this->_validate_firstname($value);
					break;

				case 'lastname':
					$this->_validate_lastname($value);
					break;

				case 'password':
					$this->_validate_password($value);
					$this->_data['password_hash'] = ExecMethod('phpgwapi.auth.create_hash', $value);
					$this->_data['last_passwd_change'] = time();
					break;

				case 'password_hash':
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
					$this->_validate_person_id($value, 'person');
					break;

				case 'quota':
					$this->_validate_quota($value);
					break;

				case 'acl':
					// just let it through
					break;

				default:
					$class = get_class($this);
					// trigger notice here to allow the execution to continue, we just won't set anything
					trigger_error("Attempted to set {$class}::{$name} to $value, {$class}::{$name} is unknown", E_USER_NOTICE);
					return false;
			}
			$this->_data[$name] = $value;
		}

		/**
		 * Shortcut for getting the fullname of the account
		 *
		 * @return string the full name of the account in the user's preferred format
		 */
		public function __toString()
		{
			switch($GLOBALS['phpgw_info']['user']['preferences']['common']['account_display'])
			{
				case 'lastname':
					return "{$this->_data['lastname']}, {$this->_data['firstname']}";

				default:
					return "{$this->_data['firstname']} {$this->_data['lastname']}";
			}
		}

		/**
		 * Check that the account expiry time stamp is valid
		 *
		 * @throws Exception when expiry timestamp is invalid
		 * @param int $exp the account expiry expresses as a unix timestamp
		 * @return bool is the expiry valid?
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
		 * @throws Exception when name is empty
		 * @param string $name the name to validate
		 * @return bool is the name valid?
		 */
		protected function _validate_lastname($name)
		{
			return strlen($name) >= 1;
		}

		/**
		 * Check that a password is valid and secure
		 *
		 * @throws Exception when password is invalid/insecure
		 * @param string $passwd the password to check
		 * @return bool is the password valid and secure?
		 */
		protected function _validate_password($passwd)
		{
			if ( function_exists('crack_check')
				&& $dict_loc = ini_get('crack.default_dictionary') )
			{
				$dict = crack_opendict($dict_loc);
				if ( !crack_check($passwd) )
				{
					throw new Exception(crack_getlastmessage());
				}
				return true;
			}
			else
			{
				if ( strlen($passwd) >= 8 )
				{
					throw new Exception('Password must be at least 8 characters long');
				}

				$m = array();
				if ( preg_match_all('/[a-z]/', $passwd, $m) <= 2 )
				{
					throw new Exception('Password must contain at least 2 lower case characters');
				}

				$m = array();
				if ( preg_match_all('/[A-Z]/', $passwd, $m) <= 2 )
				{
					throw new Exception('Password must contain at least 2 upper case characters');
				}

				$m = array();
				if ( !preg_match_all('/[0-9]/', $passwd, $m) )
				{
					throw new Exception('Password must contain at least 1 number');
				}

				$m = array();
				if ( !preg_match_all('/\W/', $passwd, $m)  )
				{
					throw new Exception('Password must contain at least 1 non alphanumeric character');
				}
			}
		}

		/**
		 * Check if the specified quota is a valid value
		 *
		 * @throws Exception if quota is invalid
		 * @param int $quota the users quota in Kb
		 * @return boolean is the quota valid?
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
				if ( $quota == $val * 1024 ) // compare kb to kb
				{
					return true;
				}
			}
			throw new Exception('Quota value not supported');
		}

		/**
		 * Validate a username
		 *
		 * @throws Exception when username is invalid
		 * @param string $username the username to validate
		 * @param boolean $lookup check if the account already exists
		 * @return boolean is the username valid?
		 */
		private function _validate_username($username, $lookup = true)
		{
			if ( !strlen($username) )
			{
				throw new Exception('Username is too short');
			}

			if ( $lookup && $GLOBALS['phpgw']->accounts->search(array('lid' => $username, 'type' => 'u')) )
			{
				throw new Exception('Username already in use');
			}

			phpgw::import_class('phpgwapi.globally_denied');
			if ( phpgwapi_globally_denied::user($username) )
			{
				throw new Exception('Username is blocked');
			}
			return true;
		}

	}

