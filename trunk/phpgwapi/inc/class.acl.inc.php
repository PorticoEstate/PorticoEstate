<?php
	/**
	* Access Control List - Security scheme based on ACL design
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v3 or later
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Access Control List - Security scheme based on ACL design
	*
	* This can manage rights to 'run' applications, and limit certain features
	* within an application.  It is also used for granting a user or group rights
	* to various records, such as todo or calendar items of another user.
	* @package phpgwapi
	* @subpackage accounts
	* @internal syntax: CreateObject('phpgwapi.acl',int account_id);
	* @internal example: $acl = createObject('phpgwapi.acl');  // user id is the current user
	* @internal example: $acl = createObject('phpgwapi.acl',10);  // 10 is the user id
	*/
	class phpgwapi_acl
	{
		/**
		* Account id
		* @var integer $_account_id Account id
		*/
		protected $_account_id;

		/**
		* Account type
		* @var string $_account_type Account type
		*/

		protected $_account_type;
		/**
		* @var array $_data Array with ACL records
		*/
		protected $_data = array();

		/**
		* @var object $_db Database connection
		*/
		protected $_db;

		/**
		* @var string $_like syntax for SQL like statement
		*/
		protected $_like = 'LIKE';

		/**
		* @var string $join database join statement syntax
		*/
		protected $_join = 'JOIN';

		/**
		* Read rights
		*/
        const READ = 1;

		/**
		* Add rights
		*/
		const ADD = 2;

		/**
		* Edit rights
		*/
		const EDIT = 4;

		/**
		* Delete rights
		*/
		const DELETE = 8;

		/**
		* Private rights
		*
		* @internal "private" is a reserved word
		*/
		const PRIV = 16;

		/**
		* Group Manager Rights
		*/
		const GROUP_MANAGERS = 32;

		/**
		* Custom 1 rights
		*
		* This is used for creating module specific rights, the module should
		* define its own constants which reference this value
		*/
		const CUSTOM_1 = 64;

		/**
		* Custom 2 rights
		*
		* This is used for creating module specific rights, the module should
		* define its own constants which reference this value
		*/
		const CUSTOM_2 = 128;

		/**
		* Custom 3 rights
		*
		* This is used for creating module specific rights, the module should
		* define its own constants which reference this value
		*/
		const CUSTOM_3 = 256;

		/**
		* ACL constructor for setting account id
		*
		* Sets the ID for $account_id. Can be used to change a current instances id as well.
		* Some functions are specific to this account, and others are generic.
		*
		* @param integer $account_id Account id
		*/
		public function __construct($account_id = 0)
		{
			$this->_db =& $GLOBALS['phpgw']->db;

			$this->_like =& $this->_db->like;
			$this->_join =& $this->_db->join;

			$this->set_account_id($account_id);
		}

		/**
		 * Set the account id used for lookups
		 *
		 * @param integer $account_id the account id to use - 0 = current user
		 * @param boolean $read_repo  call self::read_repository
		 *					- prevents infinite loops when called from read_repo
		 *
		 * @return null
		 */
		public function set_account_id($account_id = 0, $read_repo = true)
		{
			$this->_account_id = (int) $account_id;

			if ( !$this->_account_id )
			{
				$this->_account_id = get_account_id($account_id);
			}

			if ( $read_repo )
			{
				$this->_read_repository();
			}
		}

		/**
		* Get list of xmlrpc or soap functions
		*
		* @param string|array $_type Type of methods to list. Could be xmlrpc or soap
		*
		* @return array Array with xmlrpc or soap functions. Might also be empty.
		*/
		public function list_methods($_type='xmlrpc')
		{
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}

			switch($_type)
			{
				case 'xmlrpc':
				$xml_functions = array(
						'read_repository' => array(
							'function'  => 'read_repository',
							'signature' => array(array($GLOBALS['xmlrpcStruct'])),
							'docstring' => lang('FIXME!')
						),
						'get_rights' => array(
							'function'  => 'get_rights',
							'signature' => array(array($GLOBALS['xmlrpcStruct'],
											$GLOBALS['xmlrpcStruct'])),
							'docstring' => lang('FIXME!')

						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array($GLOBALS['xmlrpcStruct'],
											$GLOBALS['xmlrpcString'])),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
				case 'soap':
					return $this->soap_functions;
				default:
					return array();
			}
		}

		/*
		 * These are the standard $account_id specific functions
		 */

		/**
		* Get acl records
		*
		* @return array Array with ACL records
		*/
		public function read()
		{
			if ( count($this->_data[$this->_account_id]) == 0 )
			{
				$this->_read_repository();
			}
			return $this->_data;
		}

		/**
		* Add ACL record
		*
		* @param string  $appname  Application name.
		* @param string  $location Application location
		* @param integer $rights   Access rights in bitmask form
		* @param boolean $grantor  ID of user that grants right to others
		* @param boolean $type     Mask (1) or Right (0): Mask revoke rights
		*
		* @return array Array with ACL records
		*/
		public function add($appname, $location, $rights, $grantor = false, $type = false)
		{
			if ( !is_array($this->_data[$this->_account_id]) )
			{
				$this->_data[$this->_account_id] = array();
			}

			$this->_data[$this->_account_id][] = array
			(
				'appname'	=> $appname,
				'location'	=> $location,
				'account'	=> $this->_account_id,
				'rights'	=> $rights,
				'grantor'	=> $grantor,
				'type'		=> $type
			);
			return $this->_data;
		}

		/**
		* Delete ACL records
		*
		* @param string  $appname  Application name
		* @param string  $location Application location
		* @param integer $grantor  account_id of the user that has granted access to their records.
		*						0 means that this is a ordinary ACL - record
		* @param integer $type     mask or right (1 means mask , 0 means right)
		*
		* @return array Array with ACL records
		*/
		public function delete($appname, $location, $grantor = 0, $type = 0)
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			if(isset($this->_data[$this->_account_id]) && is_array($this->_data[$this->_account_id]))
			{
				foreach ( $this->_data[$this->_account_id] as $idx => $value )
				{
					if ( (isset($value['appname']) && $value['appname'] == $appname )
						&& strpos($value['location'], $location) === 0
						&& $value['account'] == $this->_account_id
						&& $value['grantor'] == $grantor
						&& $value['type'] == $type )
					{
						unset($this->_data[$this->_account_id][$idx]);
					}
				}
				reset($this->_data[$this->_account_id]);
			}
			return $this->_data;
		}

		/**
		* Save repository in database
		*
		* @param string $unused  This parameter is unused it is only for backwards compatibility
		* @param string $ignored This parameter is also ignored it is only for backwards compatibility
		*
		* @return array Array with ACL records
		*/
		public function save_repository($unused = null, $ignored = null)
		{
			$acct_id = (int) $this->_account_id;
			$locations =& $GLOBALS['phpgw']->locations;

			$this->_db->transaction_begin();

			$sql = 'DELETE FROM phpgw_acl'
					. " WHERE acl_account = {$acct_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			if ( !isset($this->_data[$acct_id])
				|| !count($this->_data[$acct_id]) )
			{
				$this->_db->transaction_commit();

				$this->_data[$acct_id] = array();

				return $this->_data[$acct_id];
			}

			$new_data = array();
			foreach ( $this->_data[$acct_id] as $entry )
			{
				$loc = $locations->get_id($entry['appname'], $entry['location']);

				if ( $entry['grantor'] == '' )
				{
					$entry['grantor'] = 'NULL';
				}

				if ( !isset($new_data[$loc][$entry['grantor']][$entry['type']]) )
				{
					$new_data[$loc][$entry['grantor']][$entry['type']] = 0;
				}

				$new_data[$loc][$entry['grantor']][$entry['type']] |= $entry['rights'];

				/*
					FIXME The inheritence model is broken
					this shouldn't be done here, it should be handled in the check
				*/
				$subs = $locations->get_subs($entry['appname'], $entry['location']);
				foreach ( array_keys($subs) as $sub )
				{
					if ( !isset($new_data[$sub][$entry['grantor']][$entry['type']]) )
					{
						$new_data[$sub][$entry['grantor']][$entry['type']] = 0;
					}

					$new_data[$sub][$entry['grantor']][$entry['type']] |= $entry['rights'];
				}
			}

			foreach ( $new_data as $loc => $grants )
			{
				foreach ( $grants as $grantor => $right_types )
				{
					foreach ( $right_types as $type => $rights )
					{
						$sql = 'INSERT INTO phpgw_acl'
							. " VALUES({$acct_id}, {$rights}, {$grantor}, {$type}, {$loc})";

						$this->_db->query($sql, __LINE__, __FILE__);
					}
				}
			}

			$this->_db->transaction_commit();

			$this->_delete_cache($this->_account_id);

			return $this->_data[$this->_account_id];
		}

		/**
		* Get rights from the repository not specific to this object
		*
		* @param string  $location     location within application
		* @param string  $appname      Application name
		* @param integer $grantor      account_id of the user that has granted access to their records
		*					No value means that this is a ordinary ACL - record
		* @param integer $type         mask or right (1 means mask , 0 means right)
		* @param string  $account_type used to disiguish between checkpattern
		*						"accounts","groups" and "both" - the normal behaviour is ("both")
		*						to first check for rights given to groups -
		*						and then to override by rights/mask given to users (accounts)
		*
		* @return integer Access rights in bitmask form
		*/
		public function get_rights($location, $appname = '', $grantor = null, $type = false, $account_type = 'both')
		{
			// For XML-RPC, change this once its working correctly for passing parameters (jengo)
			if (is_array($location))
			{
				$a			= $location;
				$location	= $a['location'];
				$appname	= $a['appname'];
				$grantor	= $a['grantor'];
				$type		= $a['type'];
			}

			if ( !isset($this->_data[$this->_account_id])
				|| count($this->_data[$this->_account_id]) == 0)
			{
				$this->_data[$this->_account_id] = array();
				$cached = phpgwapi_cache::system_get('phpgwapi',
																"acl_data_{$this->_account_id}");
				if ( is_array($cached) && count($cached) )
				{
					$this->_data[$this->_account_id] = $cached;
				}
				else
				{
					$this->_read_repository($account_type);
					phpgwapi_cache::system_set('phpgwapi',
										"acl_data_{$this->_account_id}",
										$this->_data[$this->_account_id]);
				}
			}

			if ( !$appname )
			{
				trigger_error('phpgwapi_acl::get_rights() called with empty appname argument'
						. ' - check your calls to phpgwapi_acl::check()', E_USER_NOTICE);
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}


			$count = (isset($this->_data[$this->_account_id])?count($this->_data[$this->_account_id]):0);
			$rights = 0;

			if(isset($this->_data[$this->_account_id]) && is_array($this->_data[$this->_account_id]))
			{
				foreach ( $this->_data[$this->_account_id] as $values )
				{
					if ( $values['appname'] == $appname
						&& ( $values['location'] == $location
							// FIXME this should probably be . not everywhere - skwashd jan08
							|| $values['location'] == 'everywhere')
						&& $values['type'] == $type
						&& (!$grantor || ( $grantor && $values['grantor'] ) ) )
					{
						if ( $values['grantor'] == $grantor)
						{
							if ( $values['rights'] == 0 )
							{
								return false;
							}
							$rights |= $values['rights'];
							$this->_account_type = $values['account_type'];
						}
					}
				}
			}
			return $rights;
		}

		/**
		* Check required rights (not specific to this object)
		*
		* @param string  $location location within application
		* @param integer $required Required right (bitmask) to check against
		* @param string  $appname  Application name
		*						if empty $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		*
		* @return boolean true when $required bitmap matched otherwise false
		*/
		public function check($location, $required, $appname = '')
		{
			$rights = $this->check_rights($location, $required, $appname, false, 0);
			$mask = $this->check_rights($location, $required, $appname, false, 1);

			if ( $mask > 0 && $rights > 0 )
			{
				$rights = false;
			}
			return $rights;
		}

		/**
		* Check required rights
		*
		* @param string  $location     location within application
		* @param integer $required     Required right (bitmask) to check against
		* @param string  $appname      Application name - default $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param integer $grantor      useraccount to check against
		* @param integer $type         mask or right (1 means mask , 0 means right) to check against
		* @param string  $account_type to check for righst given by groups and accounts separately
		*
		* @return boolean true when $required bitmap matched otherwise false
		*/
		public function check_rights($location, $required, $appname = '',
									$grantor=false, $type=false, $account_type='')
		{
			//This is only for setting new rights / grants
			if ( is_array($account_type) )
			{
				foreach ( $account_type as $entry )
				{
					$this->_data[$this->_account_id] = array();
					$rights = $this->get_rights($location, $appname, $grantor, $type, $entry);

					if ( !!($rights & $required) )
					{
						break;
					}
				}
			}
			else
			{
				$rights = $this->get_rights($location, $appname, $grantor, $type, 'both');
			}
			return !!($rights & $required);
		}

		/**
		* Get specific rights - yes really who woulda thunk it
		*
		* @param string $location location within application
		* @param string $appname  Application name
		*				Empty string shouldn't be used here - deprecated behaviour!
		*
		* @return integer Access rights in bitmask form
		*/
		public function get_specific_rights($location, $appname = '')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$count = count($this->_data[$this->_account_id]);
			if ($count == 0 && $GLOBALS['phpgw_info']['server']['acl_default'] != 'deny')
			{
				return true;
			}
			$rights = 0;

			if ( is_array($this->_data[$this->_account_id]) && count($this->_data[$this->_account_id]) )
			{
				foreach ( $this->_data[$this->_account_id] as $value )
				{
					if ($value['appname'] == $appname
						&& ($value['location'] == $location
							|| $value['location'] == 'everywhere')
						&& $value['account'] == $this->_account_id)
					{
						if ($value['rights'] == 0)
						{
							return false;
						}
						$rights |= $value['rights'];
					}
				}
			}

			return $rights;
		}

		/**
		* Check specific rights
		*
		* @param string  $location location within application
		* @param integer $required Required rights as bitmap
		* @param string  $appname  Application name
		*				Empty string shouldn't be used here - deprecated behaviour!
		*
		* @return boolean true when $required bitmap matched otherwise false
		*/
		public function check_specific($location, $required, $appname = '')
		{
			$rights = $this->get_specific_rights($location, $appname);
			return !!($rights & $required);
		}

		/**
		* Get location list for an application with specific access rights
		*
		* @param string  $app      Application name
		* @param integer $required Required rights as bitmap
		*
		* @return array list of locations or empty array for none
		*/
		public function get_location_list($app, $required)
		{
			$acct_ids = array(0, $this->_account_id);// group 0 covers all users

			$equalto = $GLOBALS['phpgw']->accounts->membership($this->_account_id);
			if (is_array($equalto) && count($equalto) > 0)
			{
				foreach ( $equalto as $group )
				{
					$acct_ids[] = $group['account_id'];
				}
			}

			$locations = array();
			$ids = implode(',', $acct_ids);
			$sql = 'SELECT phpgw_locations.name, phpgw_acl.acl_rights'
				. ' FROM phpgw_locations'
				. " {$this->_join} phpgw_acl ON phpgw_locations.location_id = phpgw_acl.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE applications.name = '{$app}' AND acl_account IN($ids)";
			$this->_db->query($sql, __LINE__, __FILE__);

			$rights = 0;
			while ($this->_db->next_record())
			{
				if ($this->_db->f('acl_rights') == 0)
				{
					return false;
				}
				$rights |= $this->_db->f('acl_rights');
				if (!!($rights & $required) == true)
				{
					$locations[] = $this->_db->f('acl_location');
				}
			}
			return $locations;
		}


		// These are the generic functions. Not specific to $account_id

		/**
		* Add repository information for an application
		*
		* @param string  $app        Application name
		* @param string  $location   location within application
		* @param integer $account_id Account id
		* @param integer $rights     Access rights in bitmap form
		*
		* @return boolean Always true, which seems pretty pointless really doesn't it
		*/
		public function add_repository($app, $location, $account_id, $rights)
		{
			$this->delete_repository($app, $location, $account_id);

			// FIXME another example of the broken inherentence model
			$inherit_location = array();

			$sql = 'SELECT phpgw_locations.location_id'
				. ' FROM phpgw_locations'
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_locations.name {$this->_like} '{$location}%'"
					. " AND phpgw_applications.app_name='$app'";

			$this->_db->query($sql, __LINE__, __FILE__);
			while ( $this->_db->next_record() )
			{
				$inherit_location[] = $this->_db->f('location_id');
			}

			foreach ( $inherit_location as $acl_location )
			{
				$sql = 'INSERT INTO phpgw_acl (location_id, acl_account, acl_rights, acl_grantor, acl_type)'
					. " VALUES ('{$acl_location}', {$account_id}, {$rights}, NULL , 0)";
				$this->_db->query($sql, __LINE__, __FILE__);
			}

			$this->_delete_cache($account_id);

			return true;
		}

		/**
		* Delete repository information for an application
		*
		* @param string  $app       Application name
		* @param string  $location  location within application
		* @param integer $accountid Account id - 0 = current user
		*
		* @return bool were the entries deleted?
		*/
		public function delete_repository($app, $location, $accountid = 0)
		{
			static $cache_accountid;

			$account_sel = '';

			$accountid = (int) $accountid;
			if ($accountid )
			{
				if ( isset($cache_accountid[$accountid])
					&& $cache_accountid[$accountid] )
				{
					$account_id = $cache_accountid[$accountid];
				}
				else
				{
					$account_id = get_account_id($accountid, $this->_account_id);
					$cache_accountid[$accountid] = $account_id;
				}
				$account_sel = " AND acl_account = {$account_id}";
			}

			$app = $this->_db->db_addslashes($app);

			// this slows things down but makes the code easier to read & this isn't a common operation
			$sub = 'SELECT location_id FROM phpgw_locations'
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.app_name {$this->_like} '{$app}'"
					. " AND phpgw_locations.name {$this->_like} '{$location}'";

			$sql = 'DELETE FROM phpgw_acl '
				. " WHERE location_id IN ({$sub}) $account_sel";
			$this->_db->query($sql, __LINE__, __FILE__);

			$ret = !!$this->_db->num_rows();

			if ( $ret )
			{
				$this->_delete_cache($account_id);
			}

			return $ret;
		}

		/**
		* Get application list for an account id
		*
		* @param string  $location  location within application
		* @param integer $required  Access rights as bitmap
		* @param integer $accountid Account id
		*				if 0, value of $GLOBALS['phpgw_info']['user']['account_id'] is used
		*
		* @return array list of applications or false
		*/
		public function get_app_list_for_id($location, $required, $accountid = 0 )
		{
			static $cache_accountid;

			if($cache_accountid[$accountid])
			{
				$account_id = $cache_accountid[$accountid];
			}
			else
			{
				$account_id = get_account_id($accountid, $this->_account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$location = $this->_db->db_addslashes($location);
			$rights = 0;
			$apps = array();

			$sql = 'SELECT phpgw_applications.app_name, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_locations.name = '{$location}'"
					. " AND acl_account = {$account_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			while ($this->_db->next_record())
			{
				$rights |= $this->_db->f('acl_rights');
				if ( $rights & $required )
				{
					$apps[] = $this->_db->f('app_name');
				}
			}
			return $apps;
		}

		/**
		* Get location list for id
		*
		* @param string  $app       Application name
		* @param integer $required  Required access rights in bitmap form
		* @param integer $accountid Account id
		*				if 0, value of $GLOBALS['phpgw_info']['user']['account_id'] is used
		*
		* @return array location list
		*/
		public function get_location_list_for_id($app, $required, $accountid = 0)
		{
			static $cache_accountid;

			if ( isset($cache_accountid[$accountid]) && $cache_accountid[$accountid] )
			{
				$account_id = $cache_accountid[$accountid];
			}
			else
			{
				$account_id = get_account_id($accountid, $this->_account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$app = $this->_db->db_addslashes($app);
			$rights = 0;
			$locations = array();

			$sql = 'SELECT phpgw_locations.name, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.app_name = '{$app}'"
					. " AND acl_account = {$account_id}";

			$this->_db->query($sql, __LINE__, __FILE__);
			while ($this->_db->next_record())
			{
				if ($this->_db->f('acl_rights'))
				{
					$rights |= $this->_db->f('acl_rights');
					if ( $rights & $required )
					{
						$locations[] = $this->_db->f('acl_location');
					}
				}
			}
			return $locations;
		}

		/**
		* Get ids for location - which does what exactly?
		*
		* @param string  $location location within application
		* @param integer $required Required access rights in bitmap format
		* @param string  $app      Application name
		*				if empty string, the value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		*
		* @return array list of account ids
		*/
		public function get_ids_for_location($location, $required, $app = '')
		{
			$accounts = array();

			if ( !$app )
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$app = $this->_db->db_addslashes($app);
			$location = $this->_db->db_addslashes($location);

			$sql = 'SELECT phpgw_acl.acl_account, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.app_name = '{$app}'"
					. " AND phpgw_locations.name = '{$location}'";

			$this->_db->query($sql, __LINE__, __FILE__);
			while ($this->_db->next_record())
			{
				$rights = 0;
				$rights |= (int) $this->_db->f('acl_rights');
				if ( $rights & $required )
				{
					$accounts[] = (int) $this->_db->f('acl_account');
				}
			}
			return $accounts;
		}

		/**
		* Get a list of applications a user has rights to
		*
		* @param integer $accountid Account id,
		*				if 0, value of $GLOBALS['phpgw_info']['user']['account_id'] is used
		*
		* @return array List of application rights in bitmap form
		*/
		public function get_user_applications($accountid = 0)
		{
			static $cache_accountid;

			if(isset($cache_accountid[$accountid]) && $cache_accountid[$accountid])
			{
				$account_id = $cache_accountid[$accountid];
			}
			else
			{
				$account_id = get_account_id($accountid, $this->_account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$id = array_keys($GLOBALS['phpgw']->accounts->membership($account_id));
			$id[] = $account_id;
			$ids = implode(',', $id);

			$sql = 'SELECT phpgw_applications.app_name, phpgw_acl.acl_rights'
				. ' FROM phpgw_applications'
				. " {$this->_join} phpgw_locations ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " {$this->_join} phpgw_acl ON phpgw_locations.location_id = phpgw_acl.location_id"
				. " WHERE phpgw_locations.name = 'run'"
					. " AND phpgw_acl.acl_account IN ({$ids})";
			$this->_db->query($sql, __LINE__, __FILE__);

			$apps = array();
			while ($this->_db->next_record())
			{
				$appname = $this->_db->f('app_name');
				if ( !isset($apps[$appname]) )
				{
					$apps[$appname] = 0;
				}
				$apps[$appname] |= $this->_db->f('acl_rights');
			}
			return $apps;
		}

		/**
		* Get a list of users that have grants rights to their records at a location within an app
		*
		* @param string $app      Application name
		*				if emptry string, value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		* @param string $location location within application
		*
		* @return array Array with account ids and corresponding rights
		*/
		public function get_grants($app = '', $location = '')
		{
			$grant_rights	= $this->get_grants_type($app, $location, 0);
			$grant_mask		= $this->get_grants_type($app, $location, 1);
			if ( is_array($grant_mask) )
			{
				foreach ( $grant_mask as $user_id => $mask )
				{
					if ( $grant_rights[$user_id] )
					{
						$grant_rights[$user_id] &= (~ $mask);
						if ( $grant_rights[$user_id] <= 0 )
						{
							unset($grant_rights[$user_id]);
						}
					}
				}
			}
			return $grant_rights;
		}
		/**
		* Get application specific account based granted rights list
		*
		* @param string  $app      Application name
		*				if emptry string, value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		* @param string  $location location within application
		* @param integer $type     mask or right (1 means mask , 0 means right) to check against
		*
		* @return array Associative array with granted access rights for accounts
		*
		* @internal FIXME this should be simplified - if it is actually used
		*/
		public function get_grants_type($app = '', $location = '', $type = 0)
		{
			$grants = array();

			if ( !$app )
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$at_location = '';
			if ( $location )
			{
				$location = $this->_db->db_addslashes($location);
				$at_location = " AND phpgw_locations.name = '$location'";
			}

			$accts =& $GLOBALS['phpgw']->accounts;
			$acct_ids = array_keys($accts->membership($this->_account_id));
			$acct_ids[] = $this->_account_id;

			$rights = 0;
			$accounts = array();

			$ids = implode(',', $acct_ids);
			$sql = 'SELECT acl_account, acl_grantor, acl_rights'
				. ' FROM phpgw_acl'
				. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
				. " WHERE phpgw_applications.app_name = '$app' $at_location"
					. " AND acl_grantor IS NOT NULL AND acl_type = $type"
					. " AND acl_account IN ($ids)";

			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->num_rows() == 0 && $type == 0 )
			{
				return array($GLOBALS['phpgw_info']['user']['account_id'] => 31);
			}

			$records = array();
			while ($this->_db->next_record())
			{
				$records[] = array
				(
					'acl_account'	=> $this->_db->f('account'),
					'acl_grantor'	=> $this->_db->f('grantor'),
					'acl_rights'	=> $this->_db->f('rights')
				);
			}

			// we do this so we don't have to use multiple db objects and other hacks
			foreach ($records as $record )
			{
				$grantor = $record['grantor'];
				$rights = $record['rights'];

				if ( !isset($accounts[$grantor]) )
				{
					$is_group[$grantor] = $accts->get_type($grantor) == phpgwapi_account::TYPE_GROUP;
					if ( !$is_group[$grantor] )
					{
						$accounts[$grantor] = array($grantor);
					}
					else
					{
						$accounts[$grantor] = $GLOBALS['phpgw']->accounts->get_members($grantor);
					}
				}

				if ( $is_group[$grantor] )
				{
					// Don't allow to override private!
					$rights &= (~ PHPGW_ACL_PRIVATE);
					if ( !isset($grants[$grantor]) )
					{
						$grants[$grantor] = 0;
					}

					$grants[$grantor] |= $rights;
					if ( !!($rights & self::READ) )
					{
						$grants[$grantor] |= self::READ;
					}
				}

				foreach ( $accounts[$grantor] as $grantors )
				{
					if ( !isset($grants[$grantors]) )
					{
						$grants[$grantors] = 0;
					}
					$grants[$grantors] |= $rights;
				}
			}

			if ( $type == 0 )
			{
				$grants[$GLOBALS['phpgw_info']['user']['account_id']] = 31;
			}
			else
			{
				if ( isset($grants[$GLOBALS['phpgw_info']['user']['account_id']]) )
				{
					unset ($grants[$GLOBALS['phpgw_info']['user']['account_id']]);
				}
			}

			return $grants;
		}

		/**
		 * Update the description of a location
		 *
		 * @param string $location    location within application
		 * @param string $description the description of the location - seen by users
		 * @param string $appname     the name of the application for the location
		 *
		 * @return null
		 */
		public function update_location_description($location, $description, $appname = '')
		{
		 	if ( !$appname )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

		 	$location = $this->_db->db_addslashes($location);
			$description = $this->_db->db_addslashes($description);
		 	$appid = $GLOBALS['phpgw']->applications->name2id($appname);

		 	$this->_db->query('UPDATE phpgw_locations'
		 			. " SET descr = '{$description}'"
		 			. " WHERE app_id = '{$appid}' AND name = '{$location}'",
		 		 __LINE__, __FILE__);
			return true;
		}

		/**
		* Reads ACL accounts from database and return array with accounts that have rights
		*
		* @param string  $appname  Application name
		*		if empty string the value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		* @param string  $location location within Application name
		* @param integer $grantor  check if this is grants or ordinary rights/mask
		* @param integer $type     mask or right (1 means mask , 0 means right) to check against
		*
		* @return array Array with accounts
		*/
		public function get_accounts_at_location($appname = '', $location = '',
												$grantor = 0 ,$type = '')
		{
			$acl_accounts = array();
			if ( !$appname )
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$filter_grants = ' AND acl_grantor IS NULL';
			if($grantor > 0)
			{
				$filter_grants = ' AND acl_grantor IS NOT NULL';
			}

			$sql = 'SELECT acl_account FROM phpgw_acl'
					. " $this->_join phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
					. " $this->_join phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE app_name = '{$appname}'"
					. " AND phpgw_locations.name {$this->_like} '{$location}%' {$filter_grants}"
					. " AND acl_type = '{$type}'"
				. ' GROUP BY acl_account';
			$this->_db->query($sql, __LINE__, __FILE__);

			while ($this->_db->next_record())
			{
				$acl_accounts[$this->_db->f('acl_account')] = true;
			}

			return $acl_accounts;
		}

		/**
		* Delete ACL information from cache
		*
		* @param integer $account_id the account to delete data from the cache for
		*
		* @return null
		*/
		protected function _delete_cache($account_id)
		{
			phpgwapi_cache::system_clear('phpgwapi', "acl_data_{$account_id}");
		}

		/**
		 * Reads ACL records from database and return array along with storing it
		 *
		 * @param string $account_type the type of accounts sought accounts|groups
		 *
		 * @return array Array with ACL records
		 */
		protected function _read_repository($account_type = 'both')
		{
			if ( !$this->_account_id )
			{
				$this->set_account_id($this->_account_id, false);
			}

			$data = phpgwapi_cache::system_get('phpgwapi', "acl_data_{$this->_account_id}");
			if ( !is_null($data) )
			{
				$this->_data[$this->_account_id] = $data;
				return; // nothing more to do
			}

			switch( $GLOBALS['phpgw_info']['server']['account_repository'] )
			{
				case 'ldap':
					$this->_read_repository_ldap($account_type);
					break;

				default:
					$this->_read_repository_sql($account_type);
			}
			$data = phpgwapi_cache::system_set('phpgwapi', "acl_data_{$this->_account_id}", $this->_data);
		}

		/**
		* Reads ACL records from database for LDAP accounts
		*
		* @param string $account_type the type of accounts sought accounts|groups
		*
		* @return array Array with ACL records
		*
		* @internal data is cached for future look ups
		*/
		protected function _read_repository_ldap($account_type)
		{
			$this->_data[$this->_account_id] = array();

			if(!$account_type || $account_type == 'accounts' || $account_type == 'both')
			{
				$account_list[] = $this->_account_id;
				$account_list[] = 0;
			}

			if($account_type == 'groups' || $account_type == 'both')
			{
				$groups = $this->get_location_list_for_id('phpgw_group', 1, $this->_account_id);
				if ( is_array($groups) && count($groups) )
				{
					foreach ( $groups as $key => $value )
					{
						if ( !$value )
						{
							continue;
						}
						$account_list[] = $value;
					}
				}
			}

			if(!is_array($account_list))
			{
				return array();
			}

			$sql = 'SELECT * FROM phpgw_acl WHERE acl_account in (' . implode(',', $account_list) . ')';

			$this->_db->query($sql, __LINE__, __FILE__);

			while ( $this->_db->next_record() )
			{
				$this->_data[$this->_account_id][] = array
				(
					'appname'		=> $this->_db->f('acl_appname'),
					'location'		=> $this->_db->f('acl_location'),
					'account'		=> $this->_db->f('acl_account'),
					'rights'		=> $this->_db->f('acl_rights'),
					'grantor'		=> $this->_db->f('acl_grantor'),
					'type'			=> $this->_db->f('acl_type'),
					'account_type'	=> $GLOBALS['phpgw']->accounts->get_type($this->_db->f('account_type'))
				);
			}
			return $this->_data;
		}

		/**
		* Reads ACL records from database for SQL accounts and return array and caches the data for future look ups
		*
		* @param string $account_type the type of accounts sought accounts|groups
		*
		* @return array Array with ACL records
		*/
		protected function _read_repository_sql($account_type)
		{
			$this->_data[$this->_account_id] = array();

			$account_list = array();
			if ( $account_type == 'accounts' || $account_type == 'both' )
			{
				$account_list = array($this->_account_id, 0);
			}

			if ( $account_type == 'groups' || $account_type == 'both' )
			{
				$groups = createObject('phpgwapi.accounts')->membership($this->_account_id);
				$account_list = array_merge($account_list, array_keys($groups));
				unset($groups);
			}

			if(!isset($account_list) || !is_array($account_list))
			{
				return array();
			}

			$ids = implode(',', $account_list);
			$sql = 'SELECT phpgw_applications.app_name, phpgw_locations.name,'
					. ' phpgw_acl.acl_account, phpgw_acl.acl_grantor,'
					. ' phpgw_acl.acl_rights, phpgw_acl.acl_type, phpgw_accounts.account_type'
				. ' FROM phpgw_acl'
					. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. "{$this->_join} phpgw_accounts ON phpgw_acl.acl_account = phpgw_accounts.account_id "
				. " WHERE acl_account IN ($ids)";

			$this->_db->query($sql, __LINE__, __FILE__);

			while ( $this->_db->next_record() )
			{
				$this->_data[$this->_account_id][] = array
				(
					'appname'		=> $this->_db->f('app_name'),
					'location'		=> $this->_db->f('name'),
					'account'		=> $this->_db->f('acl_account'),
					'rights'		=> $this->_db->f('acl_rights'),
					'grantor'		=> $this->_db->f('acl_grantor'),
					'type'			=> $this->_db->f('acl_type'),
					'account_type'	=> $this->_db->f('account_type')
				);
			}
			return $this->_data;
		}
	}
