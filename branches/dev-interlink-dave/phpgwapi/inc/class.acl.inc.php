<?php
	/**
	* Access Control List - Security scheme based on ACL design
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License Version 3 or later
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
	* This can manage rights to 'run' applications, and limit certain features within an application.
	* It is also used for granting a user "membership" to a group, or making a user have the security 
	* equivilance of another user. It is also used for granting a user or group rights to various records,
	* such as todo or calendar items of another user.
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
		* @var integer Account id
		*/
		private $account_id;

		/**
		* Account type
		* @var string Account type
		*/
		private $account_type;

		/**
		* Array with ACL records
		* @var array Array with ACL records
		*/
		private $data = array();

		/**
		* Database connection
		* @var object Database connection
		*/
		private $db;

		/**
		* @var string like ???
		*/
		private $like = 'LIKE';

		/**
		* @var bool $load_from_shm  ACL data loaded from shared memory
		*/
		private $load_from_shm = false;

		/**
		* @var string $join ???
		*/
		private $join = 'JOIN';

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
		* @param integer $account_id Account id
		*/
		public function __construct($account_id = 0)
		{	
			$this->db =& $GLOBALS['phpgw']->db;

			$this->like =& $this->db->like;
			$this->join =& $this->db->join;

			$this->set_account_id($account_id);
		}

		public function set_account_id($account_id = 0)
		{			
			if ( !($this->account_id = (int)$account_id) )
			{
				$this->account_id = get_account_id($account_id);
			}
			$this->load_from_shm = $GLOBALS['phpgw']->shm->is_enabled();
		}

		/**
		* Get list of xmlrpc or soap functions
		*
		* @param string|array $_type Type of methods to list. Could be xmlrpc or soap
		* @return array Array with xmlrpc or soap functions. Might also be empty.
		* This handles introspection or discovery by the logged in client,
		* in which case the input might be an array.  The server always calls
		* this function to fill the server dispatch map using a string.
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
							'signature' => array(array(xmlrpcStruct)),
							'docstring' => lang('FIXME!')
						),
						'get_rights' => array(
							'function'  => 'get_rights',
							'signature' => array(array(xmlrpcStruct,xmlrpcStruct)),
							'docstring' => lang('FIXME!')

						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array(xmlrpcStruct,xmlrpcString)),
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
		 * Reads ACL records from database and return array along with storing it
		 *
		 * @param string $account_type the type of accounts sought accounts|groups
		 * @return array Array with ACL records
		 * @access private
		 */
		public function read_repository($account_type = 'both')
		{
			/*
			For some reason, calling this via XML-RPC doesn't call the constructor.
			Here is yet another work around(tm) (jengo)
			*/
			if (! $this->account_id)
			{
				$this->acl();
			}
			if ( $this->load_from_shm )
			{
				if(!($this->data[$this->account_id] = $GLOBALS['phpgw']->shm->get_value($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id)))
				{
					$this->_read_repository($account_type);
				}
			}
			else
			{
				if(!($this->data[$this->account_id] = $GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id, 'acl_data'))) // get value
				{
					$this->_read_repository($account_type);
				}
			}			
		}

		public function _read_repository($account_type = 'both')
		{
			if ( $GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap' )
			{
				return $this->_read_repository_ldap($account_type);
			}
			else
			{
				return $this->_read_repository_sql($account_type);
			}
		}
		/**
		* Get acl records
		*
		* @return array Array with ACL records
		*/
		public function read()
		{
			if (count($this->data[$this->account_id]) == 0)
			{
				$this->read_repository();
			}
			reset ($this->data[$this->account_id]);
			return $this->data;
		}

		/**
		* Add ACL record
		*
		* @param string|boolean $appname Application name. Default: false derives value from $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param string $location Application location
		* @param integer $rights Access rights in bitmask form
		* @return array Array with ACL records
		*/
		public function add($appname, $location, $rights, $grantor = false, $type = false)
		{
			if ( $appname == '' )
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if ( !is_array($this->data[$this->account_id]) )
			{
				$this->data[$this->account_id] = array();
			}

			$this->data[$this->account_id][] = array
			(
				'appname'	=> $appname,
				'location'	=> $location,
				'account'	=> $this->account_id,
				'rights'	=> $rights,
				'grantor'	=> $grantor,
				'type'		=> $type
			);
			return $this->data;
		}
		
		/**
		* Delete ACL records
		*
		* @param string $appname Application name, empty string is translated to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param string $location Application location
		* @param integer $grantor account_id of the user that has granted access to his/hers records. No value means that this is a ordinary ACL - record
		* @param integer $type mask or right (1 means mask , 0 means right)
		* @return array Array with ACL records
		*/
		public function delete($appname = '', $location, $grantor = 0, $type = 0)
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
				foreach ( $this->data[$this->account_id] as $idx => $value )
				{
					if ( (isset($value['appname']) && $value['appname'] == $appname )
						&& strpos($value['location'], $location) === 0
						&& $value['account'] == $this->account_id
						&& $value['grantor'] == $grantor
						&& $value['type'] == $type )
					{
						unset($this->data[$this->account_id][$idx]);
					}
				}
				reset($this->data[$this->account_id]);
			}
			return $this->data;
		}

		/**
		* Save repository in database
		*
		* @param string $appname Application name (default empty string is converted to false $GLOBALS['phpgw_info']['flags']['currentapp'])
		* @param string $location location within application 
		* @return array Array with ACL records
		*/
		public function save_repository($appname = '', $location='')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			$appname = $this->db->db_addslashes($appname);
			
			$location_filter = '';
			if ( $location )
			{
				$location = $this->db->db_addslashes($location);
				$location_filter = " AND phpgw_locations.name {$this->like} '{$location}%'";
			}

			$this->db->transaction_begin();

			$sql = 'DELETE FROM phpgw_acl'
				. ' USING acl_applications, phpgw_locations'
				. " WHERE phpgw_locations.app_id = phpgw_applications.app_id AND phpgw_applications.appname = '$appname'"
					. "AND acl_account = $this->account_id  . $location_filter";
			$this->db->query($sql ,__LINE__,__FILE__);

			$inherit_data = array();
			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
				if($location)
				{
		//			while(list($idx,$value) = each($this->data[$this->account_id]))
					foreach($this->data[$this->account_id] as $idx => $value)
					{
						if ( is_array($this->data[$this->account_id][$idx]) && count($this->data[$this->account_id][$idx]) && strpos($this->data[$this->account_id][$idx]['location'],$location)===0)
						{
							$sql = 'SELECT location_id FROM phpgw_locations, phpgw_applications'
								. ' WHERE phpgw_locations.app_id = phpgw_applications.app_id'
									. " AND phpgw_locations.name {$this->like} '{$location}%' AND phpgw_locations.name != '{$location}'"
									. " AND phpgw_applications.appname='{$value['appname']}'";

							$this->db->query($sql,__LINE__,__FILE__);
							while($this->db->next_record())
							{
								$inherit_data[] = array
								(
									'appname'		=> $this->data[$this->account_id][$idx]['appname'],
									'location'		=> $this->db->f('location'),
									'account'		=> $this->account_id,
									'rights'		=> $this->data[$this->account_id][$idx]['rights'],
									'grantor'		=> $this->data[$this->account_id][$idx]['grantor'],
									'type'			=> $this->data[$this->account_id][$idx]['type'],
									'account_type'		=> (isset($this->data[$this->account_id][$idx]['account_type'])?$this->data[$this->account_id][$idx]['account_type']:''),		
								);
							}
						}
					}
				}

				if ( count($inherit_data) )
				{
					$this->data[$this->account_id] = array_merge($this->data[$this->account_id], $inherit_data);
				}
			
				array_unique($this->data[$this->account_id]);

				foreach ($this->data[$this->account_id] as $idx => $value)
				{
					if ( isset($this->data[$this->account_id][$idx]['account'])
						&& $this->data[$this->account_id][$idx]['account'] == $this->account_id
						&& (($this->data[$this->account_id][$idx]['appname'] == $appname
						&& strpos($this->data[$this->account_id][$idx]['location'],$location)===0)
						|| (!$location && $this->data[$this->account_id][$idx]['location']=='run')))
					{
						$sql = 'INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights,acl_grantor,acl_type)';
						$sql .= " VALUES('".$this->data[$this->account_id][$idx]['appname']."', '"
							. $this->data[$this->account_id][$idx]['location']."', "
							.$this->account_id.', '
							. intval($this->data[$this->account_id][$idx]['rights']) . ', '
							. ($this->data[$this->account_id][$idx]['grantor']?$this->data[$this->account_id][$idx]['grantor']:'NULL')  . ', '
							. intval($this->data[$this->account_id][$idx]['type'])
							.')';

						$this->db->query($sql ,__LINE__,__FILE__);
					}
				}
			}
			/*remove duplicates*/

			$sql = "SELECT * FROM phpgw_acl WHERE acl_account='" . $this->account_id . "' AND acl_appname = '$appname'" . $location_filter . " GROUP BY acl_appname, acl_location, acl_account, acl_rights,acl_grantor,acl_type";
			$this->db->query($sql,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				$unique_data[]= array(
					'appname' => $this->db->f('acl_appname'),
					'location' => $this->db->f('acl_location'),
					'account' => $this->account_id,
					'rights' => $this->db->f('acl_rights'),
					'grantor' => $this->db->f('acl_grantor'),
					'type' => $this->db->f('acl_type')
					);
			}

			if(isset($unique_data) && is_array($unique_data))
			{
				$sql = "DELETE FROM phpgw_acl where acl_account = '" . intval($this->account_id) . "' AND acl_appname = '$appname'" . $location_filter;
				$this->db->query($sql ,__LINE__,__FILE__);

		//		while(list($idx,$value) = each($unique_data))
				foreach($unique_data as $idx => $value)
				{
					$sql = 'insert into phpgw_acl (acl_appname, acl_location, acl_account, acl_rights,acl_grantor,acl_type)';
					$sql .= " values('".$unique_data[$idx]['appname']."', '"
						. $unique_data[$idx]['location']."', "
						.$this->account_id.', '
						. intval($unique_data[$idx]['rights']) . ', '
						. ($unique_data[$idx]['grantor']?$unique_data[$idx]['grantor']:'NULL')  . ', '
						. intval($unique_data[$idx]['type'])
						.')';

					$this->db->query($sql ,__LINE__,__FILE__);
				}
			}

			$this->db->transaction_commit();

			$this->delete_cache($this->account_id);

//			return $unique_data;
		}

		// These are the non-standard $account_id specific functions


		/**
		* Get rights from the repository not specific to this object
		*
		* @param string $location location within application
		* @param string|boolean $appname Application name, defaults to false which means $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param integer $grantor account_id of the user that has granted access to his/hers records. No value means that this is a ordinary ACL - record
		* @param integer $type mask or right (1 means mask , 0 means right)
		* @param string|array $account_type used to disiguish between checkpattern:"accounts","groups" and "both" - the normal behaviour is ("both") to first check for rights given to groups - and then to override by rights/mask given to users (accounts)
		* @return integer Access rights in bitmask form
		*/
		public function get_rights($location,$appname = '', $grantor = False, $type = False, $account_type = False)
		{
			// For XML-RPC, change this once its working correctly for passing parameters (jengo)
			if (is_array($location))
			{
				$a			= $location;
				$location	= $a['location'];
				$appname	= $a['appname'];
				$grantor  = $a['grantor'];
				$type  	  = $a['type'];
			}

			if (!isset($this->data[$this->account_id]) || count($this->data[$this->account_id]) == 0)
			{
				$this->data[$this->account_id] = array();
				if ( $this->load_from_shm )
				{
					if(!$this->data[$this->account_id] = $GLOBALS['phpgw']->shm->get_value($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id))
					{
						$this->_read_repository($account_type);
						if(count($this->data[$this->account_id])>0)
						{
							$GLOBALS['phpgw']->shm->store_value($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id,$this->data[$this->account_id]);
						}
					}
				}
				else if(!$this->data[$this->account_id] = $GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id, 'acl_data')) // get value
				{
					$this->_read_repository($account_type);
					if(count($this->data[$this->account_id])>0)
					{
						$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id,'acl_data', $this->data[$this->account_id]); //store value
					}
				}
			}
			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
				reset ($this->data[$this->account_id]);
			}
			if ($appname == False)
			{
				settype($appname,'string');
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			$count = (isset($this->data[$this->account_id])?count($this->data[$this->account_id]):0);
			if ($count == 0 && $GLOBALS['phpgw_info']['server']['acl_default'] != 'deny')
			{
//				return True;
			}
			$rights = 0;


			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
				foreach($this->data[$this->account_id] as $idx => $value)
				{
					if ($this->data[$this->account_id][$idx]['appname'] == $appname
						&& ($this->data[$this->account_id][$idx]['location'] == $location 
							|| $this->data[$this->account_id][$idx]['location'] == 'everywhere')
						&& $this->data[$this->account_id][$idx]['type'] == $type
						&& ($grantor || $this->data[$this->account_id][$idx]['grantor']) )
					{
						if ($this->data[$this->account_id][$idx]['grantor'] == $grantor)
						{
							if ($this->data[$this->account_id][$idx]['rights'] == 0)
							{
								return False;
							}
							$rights |= $this->data[$this->account_id][$idx]['rights'];
							$this->account_type = $this->data[$this->account_id][$idx]['account_type'];
						}
					}
					else
					{
						if ($this->data[$this->account_id][$idx]['rights'] == 0)
						{
							return False;
						}
						$rights |= $this->data[$this->account_id][$idx]['rights'];
						$this->account_type = $this->data[$this->account_id][$idx]['account_type'];
					}
				}
			}
			return $rights;
		}
		/**
		* Check required rights (not specific to this object)
		*
		* @param string $location location within application
		* @param integer $required Required right (bitmask) to check against
		* @param string $appname Application name (default empty string is converted to false $GLOBALS['phpgw_info']['flags']['currentapp'])
		* @return boolean True when $required bitmap matched otherwise false
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
		* Check  required rights
		*
		* @param string $location location within application
		* @param integer $required Required right (bitmask) to check against
		* @param string|boolean $appname Application name, defaults to false which means $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param integer $grantor useraccount to check against
		* @param integer $type mask or right (1 means mask , 0 means right) to check against
		* @param array $account_type to check for righst given by groups and accounts separately
		* @return boolean True when $required bitmap matched otherwise false
		*/
		public function check_rights($location, $required, $appname = false, $grantor=False, $type=false, $account_type='')
		{
			if ( is_array($account_type) ) //This is only for setting new rights / grants
			{
				$continue = true;
				foreach ( $account_type as $entry )
				{
					$this->data[$this->account_id] = array();
					$rights = $this->get_rights($location, $appname, $grantor, $type, $entry);

					if ( !!($rights & $required) )
					{
						break;
					}
				}
			}
			else
			{
				$rights = $this->get_rights($location,$appname,$grantor,$type,'both');
			}
			return !!($rights & $required);
		}
		
		/**
		* Get specific rights
		*
		* @param string $location location within application
		* @param string $appname Application name (default empty string is converted to false $GLOBALS['phpgw_info']['flags']['currentapp'])
		* @return integer Access rights in bitmask form
		*/
		public function get_specific_rights($location, $appname = '')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$count = count($this->data[$this->account_id]);
			if ($count == 0 && $GLOBALS['phpgw_info']['server']['acl_default'] != 'deny')
			{
				return True;
			}
			$rights = 0;
			
			if ( is_array($this->data[$this->account_id]) && count($this->data[$this->account_id]) )
			{
				foreach ( $this->data[$this->account_id] as $value )
				{
					if ($value['appname'] == $appname 
						&& ($value['location'] == $location
							|| $value['location'] == 'everywhere')
						&& $value['account'] == $this->account_id)
					{
						if ($value['rights'] == 0)
						{
							return False;
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
		* @param string $location location within application
		* @param integer $required Required rights as bitmap
		* @param string $appname Application name (default empty string is converted to false $GLOBALS['phpgw_info']['flags']['currentapp'])
		* @return boolean True when $required bitmap matched otherwise false
		*/
		public function check_specific($location, $required, $appname = '')
		{
			$rights = $this->get_specific_rights($location,$appname);
			return !!($rights & $required);
		}
		
		/**
		* Get location list for an application with specific access rights
		*
		* @param $app Application name
		* @param integer $required Required rights as bitmap
		* @return array list of locations or empty array for none
		*/
		public function get_location_list($app, $required)
		{
			$acct_ids = array(0, $this->account_id);// group 0 covers all users
			
			$equalto = $GLOBALS['phpgw']->accounts->membership($this->account_id);
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
				. " {$this->join} phpgw_acl ON phpgw_locations.location_id = phpgw_acl.location_id"
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE applications.name = '{$app}' AND acl_account IN($ids)";
			$this->db->query($sql ,__LINE__,__FILE__);

			$rights = 0;
			while ($this->db->next_record())
			{
				if ($this->db->f('acl_rights') == 0)
				{
					return False;
				}
				$rights |= $this->db->f('acl_rights');
				if (!!($rights & $required) == True)
				{
					$locations[] = $this->db->f('acl_location');
				}
			}
			return $locations;
		}


		// These are the generic functions. Not specific to $account_id


		/**
		* Add repository information for an application
		*
		* @param string $app Application name
		* @param string $location location within application
		* @param integer $account_id Account id
		* @param integer $rights Access rights in bitmap form
		* @return boolean Always true, which seems pretty pointless really doesn't it
		*/
		public function add_repository($app, $location, $account_id, $rights)
		{
			$this->delete_repository($app, $location, $account_id);

			$inherit_location = array();
			$inherit_location[] = $location; // in case the location is not found in the location table

			$sql = 'SELECT phpgw_locations.location_id'
				. ' FROM phpgw_acl_location'
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_locations.name {$this->like} '{$location}%'"
					. " AND phpgw_applications.appname='$app'"
					. " AND phpgw_locations.name != '$location'";
			$this->db->query($sql,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				$inherit_location[] = $this->db->f('location_id');	
			}

			foreach($inherit_location as $acl_location)
			{
				$sql = 'INSERT INTO phpgw_acl (location_id, acl_account, acl_rights, acl_grantor, acl_type)'
					. " VALUES ({$acl_location}, {$account_id}, {$rights}, NULL , 0)";
				$this->db->query($sql ,__LINE__,__FILE__);
			}

			$this->delete_cache($account_id);

			return true;
		}

		/**
		* Delete repository information for an application
		*
		* @param string $app Application name
		* @param string $location location within application
		* @param integer $account_id Account id - 0 = current user
		* @return bool were the entries deleted?
		*/
		public function delete_repository($app, $location, $accountid = 0)
		{
			static $cache_accountid;
			
			$account_sel = '';

			$accountid = (int) $accountid;
			if ($accountid )
			{
				if(isset($cache_accountid[$accountid]) && $cache_accountid[$accountid])
				{
					$account_id = $cache_accountid[$accountid];
				}
				else
				{
					$account_id = get_account_id($accountid,$this->account_id);
					$cache_accountid[$accountid] = $account_id;
				}
				$account_sel = " AND acl_account = {$account_id}";
			}

			// this will slow things down but makes the code easier to read/follow
			$sub = 'SELECT location_id FROM phpgw_locations'
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications = '{$appname}'"
					. " AND phpgw_locations.name = '{$location}'";

			$sql = 'DELETE FROM phpgw_acl '
				. " WHERE location_id IN ({$sub}) $account_sel";
			$this->db->query($sql, __LINE__, __FILE__);
			$ret = !!$this->db->num_rows();

			$this->delete_cache($account_id);
			return $ret;
		}
			
		/**
		* Get application list for an account id
		*
		* @param string $location location within application
		* @param integer $required Access rights as bitmap
		* @param integer $account_id Account id defaults to 0 which is translated to $GLOBALS['phpgw_info']['user']['account_id']
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
				$account_id = get_account_id($accountid,$this->account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$location = $this->db->db_addslashes($location);
			$rights = 0;
			$apps = array();

			$sql = 'SELECT phpgw_applications.name, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_locations.name = '{$location}'"
					. " AND acl_account = {$account_id}";
			$this->db->query($sql ,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$rights |= $this->db->f('acl_rights');
				if ( $rights & $required )
				{
					$apps[] = $this->db->f('name');
				}
			}
			return $apps;
		}

		/**
		* Get location list for id
		*
		* @param string $app Application name
		* @param integer $required Required access rights in bitmap form
		* @param integer $account_id Account id defaults to 0 which translates to $GLOBALS['phpgw_info']['user']['account_id']
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
				$account_id = get_account_id($accountid,$this->account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$app = $this->db->db_addslashes($app);
			$rights = 0;
			$locations = array();

			$sql = 'SELECT phpgw_locations.name, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.name = '{$app}'"
					. " AND acl_account = {$account_id}";

			$this->db->query($sql ,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				if ($this->db->f('acl_rights'))
				{
					$rights |= $this->db->f('acl_rights');
					if ( $rights & $required )
					{
						$locations[] = $this->db->f('acl_location');
					}
				}
			}
			return $locations;
		}

		/**
		* Get ids for location
		*
		* @param string $location location within application
		* @param integer $required Required access rights in bitmap format
		* @param string $app Application name, defaults to empty string which translates to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return array list of account ids
		*/
		public function get_ids_for_location($location, $required, $app = '')
		{
			$accounts = array();

			if ( !$app )
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$app = $this->db->db_addslashes($app);
			$location = $this->db->db_addslashes($location);

			$sql = 'SELECT phpgw_acl.acl_account, phpgw_acl.acl_rights FROM phpgw_acl'
				. " {$this->join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->join} phpgw_applications ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " WHERE phpgw_applications.name = '{$app}'"
					. " phpgw_locations.name = '{$location}'";

			$this->db->query("SELECT acl_account, acl_rights FROM phpgw_acl WHERE acl_appname = '{$app}' AND acl_location = '{$location}'" ,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$rights = 0;
				$rights |= (int) $this->db->f('acl_rights');
				if ( $rights & $required )
				{
					$accounts[] = (int) $this->db->f('acl_account');
				}
			}
			return $accounts;
		}

		/**
		* Get a list of applications a user has rights to
		*
		* @param integer $account_id Account id, defaults to 0 which in translated to $GLOBALS['phpgw_info']['user']['account_id']
		* @return array Associativ array containing list of application rights in bitmap form or false
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
				$account_id = get_account_id($accountid,$this->account_id);
				$cache_accountid[$accountid] = $account_id;
			}

			$id = array($accountid);
			$memberships = $GLOBALS['phpgw']->accounts->membership($account_id);
			foreach ( $memberships as $membership )
			{
				$id[] = $membership['account_id'];
			}
			unset($memberships);
			$ids = implode(',', $id);

			$sql = 'SELECT phpgw_applications.app_name, phpgw_acl.acl_rights'
				. ' FROM phpgw_applications'
				. " {$this->db->join} phpgw_locations ON phpgw_locations.app_id = phpgw_applications.app_id"
				. " {$this->db->join} phpgw_acl ON phpgw_locations.location_id = phpgw_acl.location_id"
				. " WHERE phpgw_locations.name = 'run'"
					. " AND phpgw_acl.acl_account IN ({$ids})";
			$this->db->query($sql, __LINE__, __FILE__);

			$apps = array();
			while ($this->db->next_record())
			{
				$appname = $this->db->f('app_name');
				if ( !isset($apps[$appname]) )
				{
					$apps[$appname] = 0;
				}
				$apps[$appname] |= $this->db->f('acl_rights');
			}
			return $apps;
		}

		/**
		* Get a list of users that has grants rights to their records at a location within an application
		* @param string $location location within application
		* @param string $app Application name, defaults to empty string which translates to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return array Array with account ids and corresponding rights
		*/
		public function get_grants($app='',$location='')
		{
			$grant_rights = $this->get_grants_type($app,$location,0);
			$grant_mask = $this->get_grants_type($app,$location,1);
			if(is_array($grant_mask))
			{
				while($grant_mask && (list($user_id,$mask) = each($grant_mask)))
				{
					if($grant_rights[$user_id])
					{
						$grant_rights[$user_id] &= (~ $mask);
						if($grant_rights[$user_id]<=0)
						{
							unset ($grant_rights[$user_id]);
						}
					}
				}
			}
			return $grant_rights;
		}
		/**
		* Get application specific account based granted rights list
		*
		* @param string $app Application name, defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param string $location location within application
		* @param integer $type mask or right (1 means mask , 0 means right) to check against
		* @return array Associative array with granted access rights for accounts
		*/
		public function get_grants_type($app='',$location='',$type = '')
		{
			//TODO finish posting this code - still needs some work - but doesn't look like it is called from anywhere
			$grants = array();
			
			if (!$app)
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$at_location = '';
			if ($location)
			{
				$location = $this->db->db_addslashes($location);
				$at_location = " AND phpgw_locations.name = '$location'";
			}


			$accts =& $GLOBALS['phpgw']->accounts;
			$memberships = $accts->membership($this->account_id);

			$acct_ids = array($this->account_id);
			foreach ( $memberships as $group )
			{
				$acct_ids[] = $group['account_id'];
			}
			unset($memberships);
			
			$rights = 0;
			$accounts = array();

			$ids = implode(',', $acct_ids);
			$sql = 'SELECT acl_account, acl_grantor, acl_rights'
				. ' FROM phpgw_acl'
				. " {$this->db->join} phpgw_locations ON phpgw_acl.location_id = phpgw_locations.location_id"
				. " {$this->db->join} phpgw_applications ON phpgw_applications.app_id = phpgw_locations.app_id"
				. " WHERE phpgw_applications.app_name = '$app' $at_location"
					. " AND acl_grantor IS NOT NULL AND acl_type = $type"
					. " AND acl_account IN ($ids)";

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->num_rows() == 0 && $type == 0 )
			{
				return array($GLOBALS['phpgw_info']['user']['account_id'] => 31);
			}

			$records = array();
			while ($this->db->next_record())
			{
				$records[] = array
				(
					'acl_account'	=> $this->db->f('account'),
					'acl_grantor'	=> $this->db->f('grantor'),
					'acl_rights'	=> $this->db->f('rights')
				);
			}

			// we do this so we don't have to use multiple db objects and other hacks
			foreach ($records as $record )
			{
				$grantor = $record['grantor'];
				$rights = $record['rights'];

				if ( !isset($accounts[$grantor]) )
				{
					if ( ($is_group[$grantor] = $accts->get_type($grantor)) == 'g' )
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
					if ( !!($rights & PHPGW_ACL_READ) )
					{
						$grants[$grantor] |= PHPGW_ACL_READ;
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
		 * @param string $location location within application
		 * @param string $description the description of the location - seen by users
		 * @param string $appname the name of the application for the location
		 */
		public function update_location_description($location, $description, $appname = '')
		{
		 	if ( !$appname )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

		 	$location = $this->db->db_addslashes($location);
			$description = $this->db->db_addslashes($description);
		 	$appid = $GLOBALS['phpgw']->applications->name2id($appname);

		 	$this->db->query('UPDATE phpgw_locations'
		 			. " SET descr = '{$description}'"
		 			. " WHERE app_id = '{$appid}' AND name = '{$location}'",
		 		 __LINE__, __FILE__);
			return true;
		}

		/**
		* Reads ACL records from database for LDAP accounts and return array and caches the data for future look ups
		*
		* @param string $account_type the type of accounts sought accounts|groups
		* @return array Array with ACL records
		* @access private
		*/
		public function _read_repository_ldap($account_type)
		{
			$this->data[$this->account_id] = array();

			if(!$account_type || $account_type == 'accounts' || $account_type == 'both')
			{
				$account_list[] = $this->account_id;
				$account_list[] = 0;
			}

			if($account_type == 'groups' || $account_type == 'both')
			{
				$groups = $this->get_location_list_for_id('phpgw_group', 1, $this->account_id);
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
			
			$this->db->query($sql ,__LINE__,__FILE__);
			
			while ( $this->db->next_record() )
			{
				$this->data[$this->account_id][] = array
				(
					'appname'		=> $this->db->f('acl_appname'),
					'location'		=> $this->db->f('acl_location'), 
					'account'		=> $this->db->f('acl_account'),
					'rights'		=> $this->db->f('acl_rights'),
					'grantor'		=> $this->db->f('acl_grantor'),
					'type'			=> $this->db->f('acl_type'),
					'account_type'	=> $GLOBALS['phpgw']->accounts->get_type($this->db->f('account_type'))
				);
			}
			return $this->data;
		}

		/**
		* Reads ACL records from database for SQL accounts and return array and caches the data for future look ups
		*
		* @param string $account_type the type of accounts sought accounts|groups
		* @return array Array with ACL records
		* @access private
		*/
		public function _read_repository_sql($account_type)
		{
			$this->data[$this->account_id] = array();

			if(!$account_type || $account_type == 'accounts' || $account_type == 'both')
			{
				$account_list[] = $this->account_id;
				$account_list[] = 0;
			}

			if($account_type == 'groups' || $account_type == 'both')
			{
				$groups = $this->get_location_list_for_id('phpgw_group', 1, $this->account_id);
				if ( is_array($groups) && count($groups) )
				{
					foreach ( $groups as $key => $value )
					{
							$account_list[] = $value;
					}
				}
			}

			if(!isset($account_list) || !is_array($account_list))
			{
				return array();
			}

			$sql = 'SELECT * FROM phpgw_acl '
				. "{$this->join} phpgw_accounts ON phpgw_acl.acl_account = phpgw_accounts.account_id "
				. 'WHERE acl_account in (' . implode(',', $account_list) . ')';
			
			$this->db->query($sql ,__LINE__,__FILE__);
			
			while ( $this->db->next_record() )
			{
				$this->data[$this->account_id][] = array
				(
					'appname'		=> $this->db->f('acl_appname'),
					'location'		=> $this->db->f('acl_location'), 
					'account'		=> $this->db->f('acl_account'),
					'rights'		=> $this->db->f('acl_rights'),
					'grantor'		=> $this->db->f('acl_grantor'),
					'type'			=> $this->db->f('acl_type'),
					'account_type'	=> $this->db->f('account_type')
				);
			}
			return $this->data;
		}

		/**
		* Reads ACL accounts from database and return array with accounts that have rights - this is used to minimize workload when adding/removing acl-data
		*
		* @param string $appname Application name, defaults to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @param string $location location within Application name
		* @param integer $grantor : check if this is grants or ordinary rights/mask
		* @param integer $type mask or right (1 means mask , 0 means right) to check against
		* @return array Array with accounts
		*/
		public function get_accounts_at_location($appname = '', $location ='', $grantor=0 ,$type ='')
		{
			$acl_accounts = array();
			if (!$appname)
			{
				settype($appname,'string');
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			if($grantor > 0)
			{
				$filter_grants = ' AND acl_grantor IS NOT NULL';
			}
			else
			{
				$filter_grants = ' AND acl_grantor IS NULL';
			}
			$sql = "SELECT acl_account from phpgw_acl WHERE acl_appname = '$appname' AND acl_location $this->like '$location%' $filter_grants AND acl_type = '$type' GROUP BY acl_account";
			$this->db->query($sql,__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$acl_accounts[$this->db->f('acl_account')] = true;
			}

			return $acl_accounts;
		}

		/**
		* Delete ACL information from cache
		*
		* @param integer $account_id
		*/
		private function delete_cache($account_id)
		{
			if ( $this->load_from_shm )
			{
				$this->clear_shm($account_id);
			}
			else
			{
				$this->clear_cache($account_id);
			}	
		}

		/**
		* Delete ACL information from shared memory
		*
		* @param integer $account_id
		*/
		private function clear_shm($account_id)
		{
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id);
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id);
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id);

			$members = $GLOBALS['phpgw']->get_members($account_id);

			if (is_array($members) && count($members) > 0)
			{
				foreach ( $members as $account_id )
				{
					$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id);
					$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id);
					$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id);
				}
			}
		}

		/**
		* Delete ACL information from phpgw_cache
		*
		* @param integer $account_id
		*/
		private function clear_cache($account_id)
		{

			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id, 'acl_data', '##DELETE##');
			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id, 'acl_data', '##DELETE##');
			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id, 'acl_data', '##DELETE##');

			$members = $GLOBALS['phpgw']->get_members($account_id);

			if (is_array($members) && count($members) > 0)
			{
				foreach ( $members as $account_id )
				{
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id, 'acl_data', '##DELETE##');
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id, 'acl_data', '##DELETE##');
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id, 'acl_data', '##DELETE##');
				}
			}
		}
	}
