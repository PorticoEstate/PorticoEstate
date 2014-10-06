<?php
	/**
	* Access Control List - Security scheme based on ACL design
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
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
		* @var string $account_type Account type
		*/
		public $account_type;

		/**
		* @var array $_data Array with ACL records
		*/
		protected $_data = array();

		/**
		* @var array $_clear_cache Array with locations to clear from cache
		*/
		protected $_clear_cache = array();

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
		* @var bool $enable_inheritance determines whether rights are inherited down the hierarchy when saving permissions
		*/
		public $enable_inheritance = false;

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
		
		protected $global_lock = false;

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

			$this->_like		=& $this->_db->like;
			$this->_join		=& $this->_db->join;
			$this->_left_join	=& $this->_db->left_join;		
			
			if($account_id)
			{
				$this->set_account_id($account_id);
			}
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
		public function set_account_id($account_id = 0, $read_repo = false, $appname = '', $location = '', $account_type = 'accounts')
		{
			$this->_account_id = (int) $account_id;

			if ( !$this->_account_id )
			{
				$this->_account_id = get_account_id($account_id);
			}

			if ( $read_repo )
			{
				$app_id		 = 0;
				$location_id = 0;
				if($location)
				{
					$app_id			= $GLOBALS['phpgw']->applications->name2id($appname);
					$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
				}

				$this->_read_repository($account_type, $app_id, $location_id);
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
		*						   -1 means that this is a ordinary ACL - record
		* @param boolean $mask     Mask (1) or Right (0): Mask revoke rights
		*
		* @return array Array with ACL records
		*/
		public function add($appname, $location, $rights, $grantor = -1, $mask = 0)
		{
			$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);
			
			if( !$location_id > 0)
			{
				return $this->_data;
			}
			$this->_clear_cache[$location_id] = true;

			if ( !isset($this->_data[$this->_account_id]) || !is_array($this->_data[$this->_account_id]) )
			{
				$this->_data[$this->_account_id] = array();
			}

			$this->_data[$this->_account_id][$app_id][$location_id][] = array
			(
				'account'	=> $this->_account_id,
				'rights'	=> $rights,
				'grantor'	=> $grantor,
				'type'		=> $mask
			);
			return $this->_data;
		}

		/**
		* Delete ACL records
		*
		* @param string  $appname  Application name
		* @param string  $location Application location
		* @param integer $grantor  account_id of the user that has granted access to their records.
		*						   -1 means that this is a ordinary ACL - record
		* @param integer $mask     mask or right (1 means mask , 0 means right)
		*
		* @return array Array with ACL records
		*/
		public function delete($appname, $location, $grantor = -1, $mask = 0)
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$app_id = $GLOBALS['phpgw']->applications->name2id($appname);

			$locations = array();
			$locations[] = $GLOBALS['phpgw']->locations->get_id($appname, $location);

			if( $this->enable_inheritance )
			{
				$subs = $GLOBALS['phpgw']->locations->get_subs($appname, $location);
				$locations = array_merge($locations, array_keys($subs));
			}

			foreach ($locations as $location_id )
			{
				$this->_clear_cache[$location_id] = true;
				if(isset($this->_data[$this->_account_id][$app_id][$location_id]) && is_array($this->_data[$this->_account_id][$app_id][$location_id]))
				{
					foreach ( $this->_data[$this->_account_id][$app_id][$location_id] as $idx => $value )
					{
						if ( $value['account'] == $this->_account_id
							&& $value['grantor'] == $grantor
							&& $value['type'] == $mask )
						{
							unset($this->_data[$this->_account_id][$app_id][$location_id][$idx]);
							if(!count($this->_data[$this->_account_id][$app_id][$location_id]))
							{
						//		unset($this->_data[$this->_account_id][$app_id][$location_id]);
								$this->_data[$this->_account_id][$app_id][$location_id] = array();
							}
						}
					}
				}
			}
			reset($this->_data[$this->_account_id]);
			return $this->_data;
		}

		/**
		* Save repository in database
		*
		* @param string $appname  used for a spesific location
		* @param string $location used for a spesific location
		*
		* @return array Array with ACL records
		*/
		public function save_repository($appname = '', $location = '')
		{
			$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);

			$_locations = array();
			if($appname)
			{
				$_locations[] = $location_id;
			}
			else
			{
				foreach ($this->_data[$this->_account_id] as $_app => $_location)
				{
					foreach ($_location as $location_id => $dummy)
					{
						$_locations[] = $location_id;
					}
				}
			}

			if( !$_locations )
			{
				return; //nothing more to do here.
			}
			$acct_id = (int) $this->_account_id;
			$locations =& $GLOBALS['phpgw']->locations;
			$subs = array();
			$sub_delete = '';

			if( $this->enable_inheritance )
			{
				foreach ($_locations as $location_id)
				{
					$location_info = $locations->get_name($location_id);
					$_subs = $locations->get_subs($location_info['appname'], $location_info['location']);
					if($_subs)
					{
						$subs = array_merge($subs, array_keys($_subs));
					}
				}
			}
			
			if($subs)
			{
				$_locations = array_merge($_locations, $subs);
				$_locations = array_unique($_locations);
			}
			$sql_delete_location = ' AND location_id IN (' . implode(',', $_locations) . ')';
			unset($subs);
			unset($_locations);

			if ( $this->_db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->_db->transaction_begin();
			}


			$sql = 'DELETE FROM phpgw_acl'
					. " WHERE acl_account = {$acct_id} {$sql_delete_location}";

			$this->_db->query($sql, __LINE__, __FILE__);

			if ( !isset($this->_data[$acct_id])
				|| !is_array($this->_data[$acct_id])
				|| !count($this->_data[$acct_id])
				)
			{

				if ( !$this->global_lock )
				{
					$this->_db->transaction_commit();
				}

				$this->_data[$acct_id] = array();

				return $this->_data[$acct_id];
			}

			$new_data = array();
			foreach ( $this->_data[$acct_id] as $app => $loc )
			{
				foreach ( $loc as $location_id => $at_location )
				{
					$location_info = $locations->get_name($location_id);
					foreach ($at_location as $entry)
					{
						$entry['grantor']	= $entry['grantor'] ? $entry['grantor'] : -1; 
						$entry['type']		= $entry['type'] ? $entry['type'] : 0; 
					
						if ( !isset($new_data[$location_id][$entry['grantor']][$entry['type']]) )
						{
							$new_data[$location_id][$entry['grantor']][$entry['type']] = 0;
						}
						$new_data[$location_id][$entry['grantor']][$entry['type']] |= $entry['rights'];

						/*
							FIXME The inheritence model should be handled in the check
						*/

						if( $this->enable_inheritance )
						{
							$subs = $locations->get_subs($location_info['appname'], $location_info['location']);

							foreach ( array_keys($subs) as $sub )
							{
								if ( !isset($new_data[$sub][$entry['grantor']][$entry['type']]) )
								{
									$new_data[$sub][$entry['grantor']][$entry['type']] = 0;
								}

								$new_data[$sub][$entry['grantor']][$entry['type']] |= $entry['rights'];
							}
						}
					}
				}
			}

			// using stored prosedures
			$sql = 'INSERT INTO phpgw_acl (acl_account, acl_rights, acl_grantor, acl_type, location_id, modified_on, modified_by)'
							. ' VALUES(?, ?, ?, ?, ?, ?, ?)';

			$now 			= time();
			$mod_account	= $GLOBALS['phpgw_info']['user']['account_id'] ? $GLOBALS['phpgw_info']['user']['account_id'] : -1;

			$valueset=array();

			foreach ( $new_data as $loc_id => $grants )
			{
				foreach ( $grants as $grantor => $right_types )
				{
					foreach ( $right_types as $mask => $rights )
					{
						$valueset[] = array
						(
							1	=> array
							(
								'value'	=> $acct_id,
								'type'	=> 1 //PDO::PARAM_INT
							),
							2	=> array
							(
								'value'	=> $rights,
								'type'	=>	1 //PDO::PARAM_INT
							),
							3	=> array
							(
								'value'	=> $grantor,
								'type'	=> 1 //PDO::PARAM_INT
							),
							4	=> array
							(
								'value'	=> $mask,
								'type'	=>	1 //PDO::PARAM_INT
							),
							5	=> array
							(
								'value'	=> $loc_id,
								'type'	=> 1 //PDO::PARAM_INT
							),
							6	=> array
							(
								'value'	=> $now,
								'type'	=> 1 //PDO::PARAM_INT
							),
							7	=> array
							(
								'value'	=> $mod_account,
								'type'	=> 1 //PDO::PARAM_INT
							)
						);
					}
				}
			}

			$this->_db->insert($sql, $valueset, __LINE__, __FILE__);
			unset($sql);
			unset($valueset);


			/*remove duplicates*/

			$sql = "SELECT acl_account, acl_rights, acl_grantor, acl_type, location_id FROM phpgw_acl WHERE acl_account = {$acct_id}"
			. ' GROUP BY acl_account, acl_rights, acl_grantor, acl_type, location_id';
			$this->_db->query($sql,__LINE__,__FILE__);

			$test = array();

			while($this->_db->next_record())
			{
				$_acl_account	= (int) $this->_db->f('acl_account');
				$_acl_rights	= (int) $this->_db->f('acl_rights');
				$_acl_grantor	= (int) $this->_db->f('acl_grantor');
				$_acl_type		= (int) $this->_db->f('acl_type');
				$_location_id	= (int) $this->_db->f('location_id');

				//avoid doubled set of rights
		//		if(!$test[$_acl_account][$_acl_grantor][$_acl_type][$_location_id])
				{
					$unique_data[]= array
					(
						1	=> array
						(
							'value'	=> $_acl_account,
							'type'	=> 1 //PDO::PARAM_INT
						),
						2	=> array
						(
							'value'	=> $_acl_rights,
							'type'	=>	1 //PDO::PARAM_INT
						),
						3	=> array
						(
							'value'	=> $_acl_grantor,
							'type'	=> 1 //PDO::PARAM_INT
						),
						4	=> array
						(
							'value'	=> $_acl_type,
							'type'	=>	1 //PDO::PARAM_INT
						),
						5	=> array
						(
							'value'	=> $_location_id,
							'type'	=> 1 //PDO::PARAM_INT
						),
						6	=> array
						(
							'value'	=> $now,
							'type'	=> 1 //PDO::PARAM_INT
						),
						7	=> array
						(
							'value'	=> $mod_account,
							'type'	=> 1 //PDO::PARAM_INT
						)
					);
				}
		//		$test[$_acl_account][$_acl_grantor][$_acl_type][$_location_id] = true;
			}
//_debug_array($unique_data);
			$sql = 'DELETE FROM phpgw_acl'
					. " WHERE acl_account = {$acct_id}";
			$this->_db->query($sql, __LINE__, __FILE__);

			$sql = 'INSERT INTO phpgw_acl (acl_account, acl_rights, acl_grantor, acl_type, location_id, modified_on, modified_by)'
							. ' VALUES(?, ?, ?, ?, ?, ?, ?)';

			$this->_db->insert($sql, $unique_data, __LINE__, __FILE__);
			unset($unique_data);
/*
			//FIXME: this one is temporary to avoid problems with the old acl_grantor being NULL
			$this->_db->query('UPDATE phpgw_acl SET acl_grantor = -1 WHERE acl_grantor is NULL',__LINE__,__FILE__,true);
*/
			if ( !$this->global_lock )
			{
				$this->_db->transaction_commit();
			}

			$clear_cache = array_keys($this->_clear_cache);
			foreach($clear_cache as $location_id)
			{
				$this->_delete_cache($this->_account_id, $location_id);
			}

	//		$this->remove_duplicates($acct_id);
	//		$this->remove_duplicates();
			return $this->_data[$this->_account_id];
		}



		/**
		* experimental clean up in case of duplicates or references to deleted accounts
		*
		* @$param integer $acct_id  account to clean - all accounts if not given
		*
		* @return void
		*/
		public function remove_duplicates($acct_id = 0)
		{
			$this->_db->transaction_begin();

			$acconts_to_delete = array();
			$sql = "SELECT acl_account FROM phpgw_acl $this->_left_join phpgw_accounts ON phpgw_acl.acl_account = phpgw_accounts.account_id WHERE phpgw_accounts.account_id IS NULL GROUP BY acl_account";
			$this->_db->query($sql,__LINE__,__FILE__);
			while($this->_db->next_record())
			{
				$acconts_to_delete[] = $this->_db->f('acl_account');
			}

			if($acconts_to_delete)
			{
				$sql = 'DELETE FROM phpgw_acl WHERE acl_account IN (' . implode(',', $acconts_to_delete) . ')';
				$this->_db->query($sql,__LINE__,__FILE__);
			}

			//This one is temporary to avoid problems with the old acl_grantor being NULL
			$this->_db->query('UPDATE phpgw_acl SET acl_grantor = -1 WHERE acl_grantor is NULL',__LINE__,__FILE__,true);
			$this->_db->query('DELETE FROM phpgw_acl WHERE location_id = 0',__LINE__,__FILE__,true);

			$condition = '';
			if($acct_id > 0)
			{
				$condition = "WHERE acl_account = {$acct_id}";
			}
			$sql = "SELECT * FROM phpgw_acl {$condition}"
			. ' GROUP BY acl_account, acl_rights, acl_grantor, acl_type, location_id';
			$this->_db->query($sql,__LINE__,__FILE__);

			$now 			= time();
			$mod_account	= $GLOBALS['phpgw_info']['user']['account_id'] ? $GLOBALS['phpgw_info']['user']['account_id'] : -1;

			$cache_info = array();
			while($this->_db->next_record())
			{
				$cache_info[$this->_db->f('acl_account')][$this->_db->f('location_id')] = $this->_db->f('location_id');

				$unique_data[]= array
				(
					1	=> array
					(
						'value'	=> $this->_db->f('acl_account'),
						'type'	=> 1 //PDO::PARAM_INT
					),
					2	=> array
					(
						'value'	=> $this->_db->f('acl_rights'),
						'type'	=>	1 //PDO::PARAM_INT
					),
					3	=> array
					(
						'value'	=> $this->_db->f('acl_grantor'),
						'type'	=> 1 //PDO::PARAM_INT
					),
					4	=> array
					(
						'value'	=> (int) $this->_db->f('acl_type'),
						'type'	=>	1 //PDO::PARAM_INT
					),
					5	=> array
					(
						'value'	=> $this->_db->f('location_id'),
						'type'	=> 1 //PDO::PARAM_INT
					),
					6	=> array
					(
						'value'	=> $now,
						'type'	=> 1 //PDO::PARAM_INT
					),
					7	=> array
					(
						'value'	=> $mod_account,
						'type'	=> 1 //PDO::PARAM_INT
					)
				);
			}

			$sql = "DELETE FROM phpgw_acl {$condition}";
			$this->_db->query($sql, __LINE__, __FILE__);

			$sql = 'INSERT INTO phpgw_acl (acl_account, acl_rights, acl_grantor, acl_type, location_id, modified_on, modified_by)'
							. ' VALUES(?, ?, ?, ?, ?, ?, ?)';

			$this->_db->insert($sql, $unique_data, __LINE__, __FILE__);
			unset($unique_data);

			$this->_db->transaction_commit();

			foreach($cache_info as $account_id => $location)
			{
				foreach($location as $location_id)
				{
					$this->_delete_cache($account_id, $location_id);
				}
			}
		}


		/**
		* Get rights from the repository not specific to this object
		*
		* @param string  $location     location within application
		* @param string  $appname      Application name
		* @param integer $grantor      account_id of the user that has granted access to their records
		*							  -1 means that this is a ordinary ACL - record
		* @param integer $mask         mask or right (1 means mask , 0 means right)
		* @param string  $account_type used to disiguish between checkpattern
		*						"accounts","groups" and "both" - the normal behaviour is ("both")
		*						to first check for rights given to groups -
		*						and then to override by rights/mask given to users (accounts)
		*
		* @return integer Access rights in bitmask form
		*/
		public function get_rights($location, $appname = '', $grantor = -1, $mask = 0, $account_type = 'both', $required = '')
		{
			// For XML-RPC, change this once its working correctly for passing parameters (jengo)
			if (is_array($location))
			{
				$a			= $location;
				$location	= $a['location'];
				$appname	= $a['appname'];
				$grantor	= $a['grantor'];
				$mask		= $a['type'];
			}

			if ( !$appname )
			{
				trigger_error('phpgwapi_acl::get_rights() called with empty appname argument'
						. ' - check your calls to phpgwapi_acl::check()', E_USER_NOTICE);
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
			
			if( !$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location))
			{
				//not a valid location
				return 0;
			}

			if ( !isset($this->_data[$this->_account_id][$app_id][$location_id])
				|| count($this->_data[$this->_account_id][$app_id][$location_id]) == 0)
			{
				$this->_data[$this->_account_id][$app_id][$location_id] = array();
				$this->_read_repository($account_type, $app_id, $location_id);
			}

			$rights = 0;

			if(isset($this->_data[$this->_account_id][$app_id][$location_id]) && is_array($this->_data[$this->_account_id][$app_id][$location_id]))
			{
				foreach ( $this->_data[$this->_account_id][$app_id][$location_id] as $values )
				{
					if ( $values['type'] == $mask && $values['rights'] > 0 && $values['grantor'] ==$grantor)
					{
						$this->account_type = $values['account_type'];
						$rights |= $values['rights'];
						//stop looking when found
						if ($rights & $required)
						return $rights;
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
			$rights = $this->check_rights($location, $required, $appname, -1, 0);
			$mask = $this->check_rights($location, $required, $appname, -1, 1);

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
		* @param integer $mask         mask or right (1 means mask , 0 means right) to check against
		* @param string  $account_type to check for righst given by groups and accounts separately
		*
		* @return boolean true when $required bitmap matched otherwise false
		*/
		public function check_rights($location, $required, $appname = '',
									$grantor = -1, $mask = 0, $account_type = '')
		{
			//This is only for setting new rights / grants
			if ( is_array($account_type) )
			{
				foreach ( $account_type as $entry )
				{
					$this->_data[$this->_account_id] = array();
					$rights = $this->get_rights($location, $appname, $grantor, $mask, $entry, $required);
					if ( !!($rights & $required) )
					{
						break;
					}
				}
			}
			else
			{
				$rights = $this->get_rights($location, $appname, $grantor, $mask, 'both', $required);
			}
			return !!($rights & $required);
		}

		/**
		* Get specific rights
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

			$app_id = $GLOBALS['phpgw']->applications->name2id($appname);
			$location_id	= $GLOBALS['phpgw']->locations->get_id($appname, $location);

			if(isset($this->_data[$this->_account_id][$app_id][$location_id]) && count($this->_data[$this->_account_id][$app_id][$location_id]))
			{
				foreach ( $this->_data[$this->_account_id][$app_id][$location_id] as $value )
				{
					if ($value['account'] == $this->_account_id)
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
					$acct_ids[] = $group->id;
				}
			}

			$locations = array();
			$ids = implode(',', $acct_ids);
			$sql = 'SELECT phpgw_locations.name, phpgw_acl.acl_rights'
				. ' FROM phpgw_locations'
				. " {$this->_join} phpgw_acl ON phpgw_locations.location_id = phpgw_acl.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.app_name = '{$app}' AND acl_account IN($ids)";
			$this->_db->query($sql, __LINE__, __FILE__);

			while ($this->_db->next_record())
			{
				if ($this->_db->f('acl_rights') == 0)
				{
					return false;
				}
				$rights = $this->_db->f('acl_rights');
				if (!!($rights & $required) == true)
				{
					$locations[] = $this->_db->f('name');
				}
			}
			return array_unique($locations);
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

			$now 			= time();
			$mod_account	= $GLOBALS['phpgw_info']['user']['account_id'] ? $GLOBALS['phpgw_info']['user']['account_id'] : -1;

			foreach ( $inherit_location as $acl_location )
			{
				$sql = 'INSERT INTO phpgw_acl (location_id, acl_account, acl_rights, acl_grantor, acl_type, modified_on, modified_by)'
					. " VALUES ('{$acl_location}', {$account_id}, {$rights}, -1 , 0, {$now}, {$mod_account})";
				$this->_db->query($sql, __LINE__, __FILE__);
			}

			$location_id	= $GLOBALS['phpgw']->locations->get_id($app, $location);
			$this->_delete_cache($account_id, $location_id);

			return true;
		}

		/**
		* Clear cached permissions for all locations for a given user
		*
		* @param integer $account_id Account id - 0 = current user
		*
		* @return void
		*/
		public function clear_user_cache($account_id = 0)
		{
			$account_id = (int)$account_id;
			if(!$account_id)
			{
				$account_id = $this->_account_id;
			}
			$locations = array();

			$sql = 'SELECT location_id FROM phpgw_locations';
			$this->_db->query($sql, __LINE__, __FILE__);
			while ($this->_db->next_record())
			{
				$locations[] = $this->_db->f('location_id');
			}

			foreach ($locations as $location_id)
			{
				$this->_delete_cache($account_id, $location_id);
			}
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
			$account_id = 0;
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
				$account_sel = " AND (acl_account = {$account_id} OR acl_grantor = {$account_id})";
			}

			$app = $this->_db->db_addslashes($app);

			// this slows things down but makes the code easier to read & this isn't a common operation
			$sql = 'SELECT location_id FROM phpgw_locations'
				. " {$this->_join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.app_name {$this->_like} '{$app}'"
					. " AND phpgw_locations.name {$this->_like} '{$location}'";

			$this->_db->query($sql, __LINE__, __FILE__);

			$locations = array();
			while ($this->_db->next_record())
			{
				$locations[] = $this->_db->f('location_id');
			}

			if(!$locations)
			{
				return;
			}
			$location_filter = implode(',', $locations);

			$sql = 'DELETE FROM phpgw_acl'
				. " WHERE location_id IN ({$location_filter}) $account_sel";
			$this->_db->query($sql, __LINE__, __FILE__);

			$ret = !!$this->_db->affected_rows();

			if ( $ret )
			{
				foreach ($locations as $location_id)
				{
					if($account_id)
					{
						$this->_delete_cache($account_id, $location_id);
					}
					else
					{
						$account_objects = $GLOBALS['phpgw']->accounts->get_list('both', -1, 'ASC', '', '', -1);
						foreach($account_objects as $account)
						{
							$this->_delete_cache($account->id, $location_id);
						}
					}
				}
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

			if(isset($cache_accountid[$accountid]) && $cache_accountid[$accountid])
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
			$grant_rights = phpgwapi_cache::session_get('phpgwapi', "get_grants_{$app}_{$location}");
			if ( !is_null($grant_rights) )
			{
				return $grant_rights; // nothing more to do
			}

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
			phpgwapi_cache::session_set('phpgwapi', "get_grants_{$app}_{$location}", $grant_rights);
			return $grant_rights;
		}
		/**
		* Get application specific account based granted rights list
		*
		* @param string  $app      Application name
		*				if emptry string, value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		* @param string  $location location within application
		* @param integer $mask     mask or right (1 means mask , 0 means right) to check against
		*
		* @return array Associative array with granted access rights for accounts
		*
		* @internal FIXME this should be simplified - if it is actually used
		*/
		public function get_grants_type($app = '', $location = '', $mask = 0)
		{
			$grants = array();
			$accounts = array();
			if(!$this->_account_id)
			{
				return $accounts;
			}

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

			$ids = implode(',', $acct_ids);
			$sql = 'SELECT acl_account, acl_grantor, acl_rights'
				. ' FROM phpgw_acl'
				. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
				. " WHERE phpgw_applications.app_name = '$app' $at_location"
					. " AND acl_grantor > 0 AND acl_type = $mask"
					. " AND acl_account IN ($ids)";

			$this->_db->query($sql, __LINE__, __FILE__);
			if ( $this->_db->num_rows() == 0 && $mask == 0  && isset($GLOBALS['phpgw_info']['user']['account_id']))
			{
				return array($GLOBALS['phpgw_info']['user']['account_id'] => 31);
			}

			$records = array();
			while ($this->_db->next_record())
			{
				$records[] = array
				(
					'account'	=> $this->_db->f('acl_account'),
					'grantor'	=> $this->_db->f('acl_grantor'),
					'rights'	=> $this->_db->f('acl_rights')
				);
			}

			foreach ($records as $record )
			{
				$grantor = $record['grantor'];
				$rights = $record['rights'];

				if( $grantor > 0 )
				{
					if (!isset($accounts[$grantor]))
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
			}

			if ( $mask == 0 && isset($GLOBALS['phpgw_info']['user']['account_id']))
			{
				$grants[$GLOBALS['phpgw_info']['user']['account_id']] = 31;
			}
			else
			{
				if ( isset($GLOBALS['phpgw_info']['user']['account_id']) && isset($grants[$GLOBALS['phpgw_info']['user']['account_id']]) )
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
		*						   -1 means that this is a ordinary ACL - record
		* @param integer $mask     mask or right (1 means mask , 0 means right) to check against
		*
		* @return array Array with accounts
		*/
		public function get_accounts_at_location($appname = '', $location = '',
												$grantor = -1 ,$mask = 0)
		{
			$acl_accounts = array();
			if ( !$appname )
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$filter_grants = ' AND acl_grantor = -1';
			if($grantor > 0)
			{
				$filter_grants = ' AND acl_grantor > 0';
			}

			$sql = 'SELECT acl_account FROM phpgw_acl'
					. " $this->_join phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
					. " $this->_join phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE app_name = '{$appname}'"
					. " AND phpgw_locations.name {$this->_like} '{$location}%' {$filter_grants}"
					. " AND acl_type = '{$mask}'"
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
		protected function _delete_cache($account_id, $location_id)
		{
			$accounts = array();
			if($GLOBALS['phpgw']->accounts->get_type($account_id) == phpgwapi_account::TYPE_GROUP)
			{
				$accounts = $GLOBALS['phpgw']->accounts->get_members($account_id);
			}
			$accounts[] = $account_id;

			$sql = "SELECT app_id FROM phpgw_locations WHERE location_id = {$location_id}";
			$this->_db->query($sql, __LINE__, __FILE__);
			$this->_db->next_record();
			$app_id	= $this->_db->f('app_id');

			foreach($accounts as $id)
			{
				phpgwapi_cache::user_clear('phpgwapi', "acl_data_{$app_id}_{$location_id}", $id);
			}
		}

		/**
		 * Reads ACL records from database and return array along with storing it
		 *
		 * @param string $account_type the type of accounts sought accounts|groups
		 *
		 * @return array Array with ACL records
		 */
		protected function _read_repository($account_type = 'both',  $app_id = '', $location_id= '')
		{
			if ( !$this->_account_id )
			{
				$this->set_account_id($this->_account_id, false);
			}

			if(!$app_id && $account_type != 'accounts')
			{
				$data = phpgwapi_cache::user_get('phpgwapi', 'acl_data', $this->_account_id);
				if ( !is_null($data) )
				{
					$this->_data[$this->_account_id] = $data;
					return; // nothing more to do
				}
			}
			elseif($account_type != 'accounts')
			{
				$data = phpgwapi_cache::user_get('phpgwapi', "acl_data_{$app_id}_{$location_id}", $this->_account_id);
				if ( !is_null($data) )
				{
					$this->_data[$this->_account_id][$app_id][$location_id] = $data;
					return; // nothing more to do
				}
			}

			switch( $GLOBALS['phpgw_info']['server']['account_repository'] )
			{
				case 'ldap':
					$this->_read_repository_ldap($account_type, $app_id, $location_id);
					break;

				default:
					$this->_read_repository_sql($account_type, $app_id, $location_id);
			}
			if($account_type != 'accounts')
			{
				if(!$app_id && $this->_data[$this->_account_id])
				{
					phpgwapi_cache::user_set('phpgwapi', 'acl_data', $this->_data[$this->_account_id], $this->_account_id);
				}
				else
				{
					if(isset($this->_data[$this->_account_id][$app_id][$location_id]) && is_array($this->_data[$this->_account_id][$app_id][$location_id]))
					{
//						throw new Exception("user_set ({$app_id}, {$location_id}) not set");
						phpgwapi_cache::user_set('phpgwapi', "acl_data_{$app_id}_{$location_id}", $this->_data[$this->_account_id][$app_id][$location_id], $this->_account_id);			
					}
				}
			}
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
		protected function _read_repository_ldap($account_type, $app_id = '', $location_id= '')
		{
			if(!$account_type || $account_type == 'accounts' || $account_type == 'both')
			{
				$account_list[] = $this->_account_id;
				$account_list[] = 0;
			}

			if($account_type == 'groups' || $account_type == 'both')
			{
				$groups = createObject('phpgwapi.accounts')->membership($this->_account_id);
				$account_list = array_merge($account_list, array_keys($groups));
				unset($groups);
			}

			if(!is_array($account_list))
			{
				return array();
			}

			$at_location = '';
			if($location_id)
			{
				$location_id = (int) $location_id;
				$at_location = " AND phpgw_acl.location_id = {$location_id}";
				$this->_data[$this->_account_id][$app_id][$location_id] = array();
			}
			else
			{
				$this->_data[$this->_account_id] = array();
			}

			$sql = 'SELECT phpgw_applications.app_id, phpgw_locations.location_id,'
					. ' phpgw_acl.acl_account, phpgw_acl.acl_grantor,'
					. ' phpgw_acl.acl_rights, phpgw_acl.acl_type'
					. ' FROM phpgw_acl'
					. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. ' WHERE acl_account in (' . implode(',', $account_list) . "){$at_location}";

			$this->_db->query($sql, __LINE__, __FILE__);

			while ( $this->_db->next_record() )
			{
				$this->_data[$this->_account_id][$this->_db->f('app_id')][$this->_db->f('location_id')][] = array
				(
					'account'		=> $this->_db->f('acl_account'),
					'rights'		=> $this->_db->f('acl_rights'),
					'grantor'		=> $this->_db->f('acl_grantor'),
					'type'			=> $this->_db->f('acl_type'),
					'account_type'	=> $this->_get_type_ldap($this->_db->f('acl_account'))
				);
			}
			return $this->_data;
		}

		/**
		* Get account_type for LDAP user and cache the result for performance
		*
		* @param integer $account_id Account id
		*
		* @return string account_type for ldap-user 'g' (group) or 'u' (user)
		*/
		protected function _get_type_ldap($account_id)
		{
			static $ldap_user = array();

			if( !isset($ldap_user[$account_id]) )
			{
				$ldap_user[$account_id] = $GLOBALS['phpgw']->accounts->get_type($account_id);
			}

			return $ldap_user[$account_id];
		}

		/**
		* Reads ACL records from database for SQL accounts and return array and caches the data for future look ups
		*
		* @param string $account_type the type of accounts sought accounts|groups
		*
		* @return array Array with ACL records
		*/
		protected function _read_repository_sql($account_type, $app_id = '', $location_id= '')
		{
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

			if(!count($account_list))
			{
				return array();
			}

			$at_location = '';
			if($location_id)
			{
				$location_id = (int) $location_id;
				$at_location = " AND phpgw_acl.location_id = {$location_id}";
				$this->_data[$this->_account_id][$app_id][$location_id] = array();
			}
			else
			{
				$this->_data[$this->_account_id] = array();
			}
			$ids = implode(',', $account_list);
			$sql = 'SELECT phpgw_applications.app_id, phpgw_locations.location_id,'
					. ' phpgw_acl.acl_account, phpgw_acl.acl_grantor,'
					. ' phpgw_acl.acl_rights, phpgw_acl.acl_type, phpgw_accounts.account_type'
				. ' FROM phpgw_acl'
					. " {$this->_join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
					. " {$this->_join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
					. " {$this->_join} phpgw_accounts ON phpgw_acl.acl_account = phpgw_accounts.account_id "
				. " WHERE acl_account IN ($ids){$at_location}";

			$this->_db->query($sql, __LINE__, __FILE__);

			while ( $this->_db->next_record() )
			{
				$this->_data[$this->_account_id][$this->_db->f('app_id')][$this->_db->f('location_id')][] = array
				(
					'account'		=> $this->_db->f('acl_account'),
					'rights'		=> $this->_db->f('acl_rights'),
					'grantor'		=> $this->_db->f('acl_grantor'),
					'type'			=> $this->_db->f('acl_type'),
					'account_type'	=> $this->_db->f('account_type')
				);
			}
			return $this->_data;
		}

		/**
		* Reads ACL accounts from database and return array with accounts that have certain rights for a given location
		*
		* @param integer $required  Required access rights in bitmap form
		* @param string  $location location within Application name
		* @param string  $appname  Application name
		*		if empty string the value of $GLOBALS['phpgw_info']['flags']['currentapp'] is used
		*
		* @return array Array with accounts
		*/
		public function get_user_list_right($required, $location, $appname = '')
		{
			$myaccounts			= & $GLOBALS['phpgw']->accounts;
			$active_accounts	= array();
			$accounts			= array();
			$users				= array();

			if ( !$appname )
			{
		 		$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$appname = $this->_db->db_addslashes($appname);
			$location = $this->_db->db_addslashes($location);

			if( $GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap' )
			{
				$account_objects = $GLOBALS['phpgw']->accounts->get_list('both', -1, 'ASC', 'account_lastname', $query = '', -1); // maybe $query could be used for filtering on active accounts?
				$active_accounts = array();

				foreach ($account_objects as $account_object)
				{
					$active_accounts[] = array
					(
						'account_id'	=> $account_object->id,
						'account_type'	=> $account_object->type
					);
				}
			}
			else
			{
				$sql = "SELECT account_id, account_type FROM phpgw_accounts"
					. " {$this->_join} phpgw_acl on phpgw_accounts.account_id = phpgw_acl.acl_account"
					. " {$this->_join} phpgw_locations on phpgw_acl.location_id = phpgw_locations.location_id"
					. " WHERE account_status = 'A' AND phpgw_locations.name = '{$location}'"
					. " ORDER BY account_lastname ASC";

				$this->_db->query($sql,__LINE__,__FILE__);

				while ($this->_db->next_record())
				{
					$active_accounts[] = array
					(
						'account_id'	=> $this->_db->f('account_id'),
						'account_type'	=> $this->_db->f('account_type')
					);
				}
			}

			foreach ($active_accounts as $entry)
			{
				$this->_account_id = $entry['account_id'];

				if($this->check($location, $required, $appname))
				{
					if($entry['account_type']=='g')
					{
						$members = $myaccounts->member($entry['account_id'], true);

						if (isset($members) AND is_array($members))
						{
							foreach($members as $user)
							{
								$accounts[$user['account_id']] = $user['account_id'];
							}
							unset($members);
						}
					}
					else
					{
						$accounts[$entry['account_id']] = $entry['account_id'];
					}
				}
			}

			unset($active_accounts);
			unset($myaccounts);

			$sql = "SELECT account_id FROM phpgw_accounts WHERE account_status = 'I'";
			$this->_db->query($sql,__LINE__,__FILE__);
			while ($this->_db->next_record())
			{
				unset($accounts[$this->_db->f('account_id')]);
			}

			if (isset($accounts) AND is_array($accounts))
			{
				foreach($accounts as $account_id)
				{
					$this->_account_id = $account_id;

					if(!$this->check($location,$required, $appname))
					{
						unset($accounts[$account_id]);
					}
				}
			}

			$accounts = array_keys($accounts);

			if(isset($accounts) && count($accounts) > 0)
			{
				$sql = 'SELECT * FROM phpgw_accounts where account_id in ('. implode(',',$accounts) . ') ORDER BY account_lastname';
				$this->_db->query($sql,__LINE__,__FILE__);
				while ($this->_db->next_record())
				{
					$users[] = array
					(
						'account_id'        => $this->_db->f('account_id'),
						'account_lid'       => $this->_db->f('account_lid'),
						'account_type'      => $this->_db->f('account_type'),
						'account_firstname' => $this->_db->f('account_firstname'),
						'account_lastname'  => $this->_db->f('account_lastname'),
						'account_status'    => $this->_db->f('account_status'),
						'account_expires'   => $this->_db->f('account_expires')
					);
				}
			}
			return $users;
		}
	}
