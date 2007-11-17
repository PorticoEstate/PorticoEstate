<?php
	/**
	* Access Control List - Security scheme based on ACL design
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.acl.inc.php,v 1.111 2007/08/14 16:31:15 sigurdne Exp $
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
	* @internal example: $acl = createObject('phpgwapi.acl',5);  // 5 is the user id
	* @internal example: $acl = createObject('phpgwapi.acl',10);  // 10 is the user id
	*/
	class acl
	{
		/**
		* Account id
		* @var integer Account id
		*/
		var $account_id;
		/**
		* Account type
		* @var string Account type
		*/
		var $account_type;
		/**
		* Array with ACL records
		* @var array Array with ACL records
		*/
		var $data = array();
		/**
		* Database connection
		* @var object Database connection
		*/
		var $db;

		/**
		* @var object $db2 cloned database object
		*/
		var $bb2;

		/**
		* @var string like ???
		*/
		var $like = 'LIKE';

		/**
		* @var string $join ???
		*/
		var $join = 'JOIN';

		/**
		* @var bool $load_from_shm  ACL data loaded from shared memory
		*/
		var $load_from_shm = false;

		/**
		* ACL constructor for setting account id
		*
		* Sets the ID for $account_id. Can be used to change a current instances id as well.
		* Some functions are specific to this account, and others are generic.
		* @param integer $account_id Account id
		*/
		function acl($account_id = 0)
		{	
			$this->db =& $GLOBALS['phpgw']->db;
			$this->db2 = clone($this->db);

			$this->like =& $this->db->like;
			$this->join =& $this->db->join;
			
			if (!($this->account_id = intval($account_id)))
			{
				$this->account_id = get_account_id($account_id);
			}

			if ( !isset($GLOBALS['phpgw']->shm) || !is_object($GLOBALS['phpgw']->shm) )
			{
				$GLOBALS['phpgw']->shm = createObject('phpgwapi.shm');
			}
		}

		/**
		* Checks whether the system is set to utilise shared memory
		* @return bool ACL data loaded from shared memory
		*/
		function load_from_shm()
		{
			if ((isset($GLOBALS['phpgw_info']['server']['shm_lang']) 
				&& $GLOBALS['phpgw_info']['server']['shm_lang'])
				&& function_exists('sem_get'))
			{
				return true;
			}
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
		function list_methods($_type='xmlrpc')
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


		// These are the standard $account_id specific functions


		/**
		* Reads ACL records from database and return array along with storing it
		*
		* @param string $account_type the type of accounts sought accounts|groups
		* @return array Array with ACL records
		* @access private
		*/
		function read_repository($account_type = 'both')
		{
			/*
			For some reason, calling this via XML-RPC doesn't call the constructor.
			Here is yet another work around(tm) (jengo)
			*/
			if (! $this->account_id)
			{
				$this->acl();
			}
			if ($this->load_from_shm())
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

		function _read_repository($account_type = 'both')
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
		function read()
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
		function add($appname = '', $location, $rights, $grantor = False, $type = False)
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			if(!is_array($this->data[$this->account_id]))
			{
				$this->data[$this->account_id] = array();
			}
			$this->data[$this->account_id][] = array('appname' => $appname, 'location' => $location, 'account' => $this->account_id, 'rights' => $rights, 'grantor' => $grantor, 'type' => $type);
			reset($this->data[$this->account_id]);
			return $this->data;
		}
		
		/**
		 * Add an ACL location
		 * 
		 * @param string $location the name of the location
		 * @param string $description the description of the location - seen by users
		 * @param string $appname the name of the application for the location
		 * @return bool was the location added?
		 */
		 function add_location($location, $descr, $appname = '', $allow_grant = true, $custom_tbl = '')
		 {
		 	if ( $appname === '' )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

		 	$location = $this->db->db_addslashes($location);
			$descr = $this->db->db_addslashes($descr);
		 	$appname = $this->db->db_addslashes($appname);
		 	$allow_grant = (int) $allow_grant;

		 	$this->db->query('SELECT COUNT(id) AS cnt_id FROM phpgw_acl_location'
		 			. " WHERE appname = '{$appname}' AND id = '{$location}'",
		 		 __LINE__, __FILE__);
		 	if ( $this->db->next_record() && (int)$this->db->next_record() > 0 )
		 	{
		 		return false; //already exists - so bail out
		 	}
		 	if ( $custom_tbl === '' )
		 	{
		 		$sql = 'INSERT INTO phpgw_acl_location (appname, id, descr, allow_grant)'
		 			. " VALUES ('{$appname}', '{$location}', '{$descr}', {$allow_grant})";
		 	}
		 	else
		 	{
		 		$custom_tbl = $this->db->db_addslashes($custom_tbl);
		 		$sql = 'INSERT INTO phpgw_acl_location (appname, id, descr, allow_grant, allow_c_attrib, c_attrib_table)'
		 			. " VALUES ('{$appname}', '{$location}', '{$descr}', {$allow_grant}, 1, '{$custom_tbl}')";
		 	}
			$this->db->query($sql, __LINE__, __FILE__);
			
			return true;//bad but lets assume it works :)
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
		function delete($appname = '', $location, $grantor = 0, $type = 0)
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
		* Deletes an ACL and all associated grants/masks for that location
		*
		* @param string $appname the application name
		* @param string $location the location
		* @param bool $remove_table remove the associate custom attributes table if it exists
		* @return bool was the location found and deleted?
		*/
		function delete_location($appnane, $location, $remove_table = false)
		{
			$appname = $this->db->db_addslashes($appname);
			$location = $this->db->db_addslashes($location);

			$sql = 'SELECT c_attrib_table FROM phpgw_acl_location'
				. " WHERE appname = '{$appname}'"
				. " AND id = '{$location}'";

			$this->db->query($sql, __LINE__, __FILE__);
			if ( !$this->db->next_record() )
			{
				return false; //invalid location
			}

			$tbl = $this->db->f('c_attrib_table');
			
			$oProc = createObject('phpgwapi.schema_proc',$GLOBALS['phpgw_info']['server']['db_type']);
			$oProc->m_odb =& $this->db;
			$Proc->m_odb->Halt_On_Error = 'report';

			$this->db->transaction_begin();

			if ( $remove_table )
			{
				$GLOBALS['phpgw']->oProc->DropTable($tbl);
			}

			$this->db->query('DELETE FROM phpgw_acl_location'
				. " WHERE appname = '{$appname}'"
				. " AND id = '{$location}'", __LINE__, __FILE__);

			$this->delete_repository($appname, $location);

			$this->db->transaction_commit();

			return true;
		}

		/**
		* Save repository in database
		*
		* @param string $appname Application name (default empty string is converted to false $GLOBALS['phpgw_info']['flags']['currentapp'])
		* @param string $location location within application 
		* @return array Array with ACL records
		*/
		function save_repository($appname = '', $location='')
		{
			if ($appname == '')
			{
				$appname = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			
			$location_filter = ($location?" AND acl_location $this->like '" . $location . "%'":'');

			$this->db->transaction_begin();

			$sql = 'DELETE FROM phpgw_acl WHERE acl_account = '. (int) $this->account_id . " AND acl_appname = '$appname'" . $location_filter;
			$this->db->query($sql ,__LINE__,__FILE__);

			$inherit_data = array();
			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
				reset ($this->data[$this->account_id]);			

				if($location)
				{
		//			while(list($idx,$value) = each($this->data[$this->account_id]))
					foreach($this->data[$this->account_id] as $idx => $value)
					{
						if ( is_array($this->data[$this->account_id][$idx]) && count($this->data[$this->account_id][$idx]) && strpos($this->data[$this->account_id][$idx]['location'],$location)===0)
						{
							$sql = "SELECT id as location FROM phpgw_acl_location WHERE id $this->like '" . $location . "%' AND appname='" . $this->data[$this->account_id][$idx]['appname'] . "' AND id != '" . $location . "'";
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

				reset ($this->data[$this->account_id]);
				if(count($inherit_data)>0)
				{
					$this->data[$this->account_id] = array_merge($this->data[$this->account_id], $inherit_data);
				}
			
				array_unique($this->data[$this->account_id]);

			//	while(list($idx,$value) = each($this->data[$this->account_id]))
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
		function get_rights($location,$appname = '', $grantor = False, $type = False, $account_type = False)
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
				if($this->load_from_shm())
				{
					if(!$this->data[$this->account_id] = $GLOBALS['phpgw']->shm->get_value($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id))
					{
						$this->data[$this->account_id] = array();
						$this->_read_repository($account_type);
						if(count($this->data[$this->account_id])>0)
						{
							$GLOBALS['phpgw']->shm->store_value($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id,$this->data[$this->account_id]);
						}
					}
				}
				else if(!$this->data[$this->account_id] = $GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_' . $account_type . '_' . $this->account_id, 'acl_data')) // get value
				{
					$this->data[$this->account_id] = array();
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

/*			if(isset($this->data[$this->account_id]))
			{
				reset ($this->data[$this->account_id]);
			}
*/
			if(isset($this->data[$this->account_id]) && is_array($this->data[$this->account_id]))
			{
//				while(list($idx,$value) = each($this->data[$this->account_id]))
				foreach($this->data[$this->account_id] as $idx => $value)
				{
					if ($this->data[$this->account_id][$idx]['appname'] == $appname)
					{
						if ($this->data[$this->account_id][$idx]['location'] == $location || $this->data[$this->account_id][$idx]['location'] == 'everywhere')
						{
							if ($this->data[$this->account_id][$idx]['type'] == $type)
							{
								if($grantor || $this->data[$this->account_id][$idx]['grantor'])
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
		function check($location, $required, $appname = '')
		{
			$rights = $this->check_brutto($location, $required, $appname, false, $type=0);
			$mask = $this->check_brutto($location, $required, $appname, false, $type=1);

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
		function check_brutto($location, $required, $appname = False,$grantor=False,$type=false,$account_type='')
		{
			if(is_array($account_type)) //This is only for setting new rights / grants
			{
				$continue = true;
				while ($continue && list(,$entry) = each($account_type))
				{				
					$this->data[$this->account_id]=array();
					$rights = $this->get_rights($location,$appname,$grantor,$type,$entry);

					if(!!($rights & $required)>0)
					{
						$continue = False;
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
		function get_specific_rights($location, $appname = '')
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
					if ($value['appname'] == $appname &&
						($value['location'] == $location ||
						$value['location'] == 'everywhere') &&
						$value['account'] == $this->account_id)
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
		function check_specific($location, $required, $appname = '')
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
		function get_location_list($app, $required)
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
			$sql = "SELECT acl_location, acl_rights FROM phpgw_acl WHERE acl_appname = '$app' AND acl_account IN(" . implode(',', $acct_ids) . ')'; 
			$this->db->query($sql ,__LINE__,__FILE__);
			$rights = 0;
			if ($this->db->num_rows() == 0 )
			{
				return $locations;
			}
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
				else
				{
//					return False; //Sigurd: I think this is wrong
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
		function add_repository($app, $location, $account_id, $rights)
		{
			$this->delete_repository($app, $location, $account_id);

			$inherit_location = array();
			$inherit_location[] = $location; // in case the location is not found in the location table

			$sql = "SELECT id as location FROM phpgw_acl_location WHERE id $this->like '".$location."%' AND appname='" . $app . "' AND id != '" .$location . "'";
			$this->db->query($sql,__LINE__,__FILE__);
			while($this->db->next_record())
			{
				$inherit_location[] = $this->db->f('location');	
			}

			foreach($inherit_location as $acl_location)
			{
				$sql = 'insert into phpgw_acl (acl_appname, acl_location, acl_account, acl_rights,acl_grantor,acl_type)';
				$sql .= " values ('" . $app . "','" . $acl_location . "','" . $account_id . "','" . intval($rights) . "', NULL ,'0')";
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
		* @return integer Number of deleted entries
		*/
		function delete_repository($app, $location, $accountid = 0)
		{
			static $cache_accountid;
			
			$account_sel = '';

			$accountid = intval($accountid);
			if ($accountid > 0)
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
				$account_sel = ' AND acl_account = ' . $account_id;
			}

			$sql = "DELETE FROM phpgw_acl WHERE acl_appname LIKE '{$app}' AND acl_location LIKE '{$location}' $account_sel";
			$this->db->query($sql ,__LINE__,__FILE__);

			$this->delete_cache($account_id);
			
			return $this->db->num_rows();
		}
			
		/**
		* Get application list for an account id
		*
		* @param string $location location within application
		* @param integer $required Access rights as bitmap
		* @param integer $account_id Account id defaults to 0 which is translated to $GLOBALS['phpgw_info']['user']['account_id']
		* @return boolean|array Array with list of applications or false
		*/
		function get_app_list_for_id($location, $required, $accountid = 0 )
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
			$sql  = 'SELECT acl_appname, acl_rights from phpgw_acl ';
			$sql .= "WHERE acl_location = '" . $this->db->db_addslashes($location) . "' ";
			$sql .= 'AND acl_account = ' . intval($account_id);
//			die("acl::get_app_list_for_id $sql == {$sql}");
			$this->db->query($sql ,__LINE__,__FILE__);
			$rights = 0;
			if ($this->db->num_rows() == 0 )
			{
				return false;
			}
			while ($this->db->next_record())
			{
				if ($this->db->f('acl_rights') == 0)
				{
					return false;
				}
				$rights |= $this->db->f('acl_rights');
				if (!!($rights & $required) == true)
				{
					$apps[] = $this->db->f('acl_appname');
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
		* @return array|boolean Array with location list or false
		*/
		function get_location_list_for_id($app, $required, $accountid = 0)
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
			$sql  = 'SELECT acl_location, acl_rights ';
			$sql .= "FROM phpgw_acl where acl_appname = '" . $this->db->db_addslashes($app) . "' ";
			$sql .= 'AND acl_account =' . intval($account_id);
			$this->db->query($sql ,__LINE__,__FILE__);
		
			$rights = 0;
			if ($this->db->num_rows() == 0 )
			{
				return false;
			}
			while ($this->db->next_record())
			{
				if ($this->db->f('acl_rights'))
				{
					$rights |= $this->db->f('acl_rights');
					if ( !!($rights & $required) )
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
		* @return array Array with account ids
		*/
		function get_ids_for_location($location, $required, $app = '')
		{
			if ($app == '')
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}
			$sql = "SELECT acl_account, acl_rights FROM phpgw_acl WHERE acl_appname = '$app' AND acl_location = '$location'";
			$this->db2->query($sql ,__LINE__,__FILE__);
			$accounts = array();
			while ($this->db2->next_record())
			{
				$rights = 0;
				$rights |= $this->db2->f('acl_rights');
				if (!!($rights & $required) == True)
				{
					$accounts[] = intval($this->db2->f('acl_account'));
				}
			}
			return $accounts;
		}

		/**
		* Get a list of applications a user has rights to
		*
		* @param integer $account_id Account id, defaults to 0 which in translated to $GLOBALS['phpgw_info']['user']['account_id']
		* @return array|boolean Associativ array containing list of application rights in bitmap form or false
		*/
		function get_user_applications($accountid = 0)
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

			$memberships = $GLOBALS['phpgw']->accounts->membership($account_id);
			$sql = "select acl_appname, acl_rights from phpgw_acl where acl_location = 'run' and "
				. 'acl_account in ';
			$sql .= '('.$account_id;
			while($groups = @each($memberships))
			{
				$group = each($groups);
				$sql .= ','.$group[1]['account_id'];
			}
			$sql .= ')';
			$this->db->query($sql, __LINE__, __FILE__);

			if ($this->db->num_rows() == 0)
			{
				return false;
			}
			while ($this->db->next_record())
			{
				if(isset($apps[$this->db->f('acl_appname')]))
				{
					$rights = $apps[$this->db->f('acl_appname')];
				}
				else
				{
					$rights = 0;
					$apps[$this->db->f('acl_appname')] = 0;
				}
				$rights |= $this->db->f('acl_rights');
				$apps[$this->db->f('acl_appname')] |= $rights;
			}
			return $apps;
		}

		/**
		* Get a list of users that has grants rights to their records at a location within an application
		* @param string $location location within application
		* @param string $app Application name, defaults to empty string which translates to $GLOBALS['phpgw_info']['flags']['currentapp']
		* @return array Array with account ids and corresponding rights
		*/
		function get_grants($app='',$location='')
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
		function get_grants_type($app='',$location='',$type = '')
		{
			$grants = array();
			
			if (!$app)
			{
				$app = $GLOBALS['phpgw_info']['flags']['currentapp'];
			}

			$at_location = '';
			if ($location)
			{
				$location = $this->db->db_addslashes($location);
				$at_location = " AND acl_location = '$location'";
			}

			$acct_ids = array($this->account_id);
			
			$myaccounts = createObject('phpgwapi.accounts');
			$my_memberships = $myaccounts->membership($this->account_id);
			unset($myaccounts);
			if(is_array($my_memberships))
			{
				foreach ( $my_memberships as $key => $group )
				{
					$acct_ids[] = $group['account_id'];
				}
			}
			
			$sql = "SELECT acl_account,acl_grantor, acl_rights FROM phpgw_acl WHERE acl_appname = '$app' $at_location AND acl_grantor IS NOT NULL AND acl_type = $type AND "
				. 'acl_account IN (' . implode(',', $acct_ids) . ')';

			$this->db->query($sql, __LINE__, __FILE__);
			$rights = 0;
			$accounts = Array();
			if ($this->db->num_rows() == 0 && $type==0)
			{
				$grants[$GLOBALS['phpgw_info']['user']['account_id']] = 31;
				return $grants;
			}

			while ($this->db->next_record())
			{
				$grantor = $this->db->f('acl_grantor');
				$rights = $this->db->f('acl_rights');
				if(!isset($accounts[$grantor]))// cache the group-members for performance 
				{
					$is_group[$grantor] = false;
					// if $grantor is a group, get its members
					$members = $this->get_ids_for_location($grantor,1,'phpgw_group');
					if(!$members)
					{
						$accounts[$grantor] = array($grantor);
					}
					else
					{
						$accounts[$grantor] = $members;
						$is_group[$grantor] = True;
					}
				}
				if($is_group[$grantor])
				{
					// Don't allow to override private!
					$rights &= (~ PHPGW_ACL_PRIVATE);
					if(!isset($grants[$grantor]))
					{
						$grants[$grantor] = 0;
					}
					$grants[$grantor] |= $rights;
					if(!!($rights & PHPGW_ACL_READ))
					{
						$grants[$grantor] |= PHPGW_ACL_READ;
					}
				}
				while(list($nul,$grantors) = each($accounts[$grantor]))
				{
					if(!isset($grants[$grantors]))
					{
						$grants[$grantors] = 0;
					}
					$grants[$grantors] |= $rights;
				}
				reset($accounts[$grantor]);
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
		function update_location_description($location, $description, $appname = '')
		{
		 	if ( $appname === '' )
		 	{
		 		$appname = $GLOBALS['phpgw']['flags']['currentapp'];
		 	}

		 	$location = $this->db->db_addslashes($location);
			$description = $this->db->db_addslashes($description);
		 	$appname = $this->db->db_addslashes($appname);

		 	$this->db->query('UPDATE phpgw_acl_location'
		 			. " SET descr = '{$description}'"
		 			. " WHERE appname = '{$appname}' AND id = '{$location}'",
		 		 __LINE__, __FILE__);
			return true;
		}

		/**
		* This does something
		*
		* @param ??? $apps_wtih_acl ???
		* @return ???
		*/
		function verify_location($apps_with_acl)
		{
			while($apps_with_acl && (list($appname,$value) = each($apps_with_acl)))
			{
				$sql = "SELECT appname from phpgw_acl_location WHERE appname = '$appname' AND id = '.'";
				$this->db->query($sql ,__LINE__,__FILE__);

				if ($this->db->num_rows()==0)
				{
					$sql = "INSERT into phpgw_acl_location (appname,id,descr,allow_grant) VALUES ('$appname','.','Top','" . intval($value['top_grant']) . "')";
					$this->db->query($sql ,__LINE__,__FILE__);
				}
			}
		}

		/**
		* Reads ACL records from database for LDAP accounts and return array and caches the data for future look ups
		*
		* @param string $account_type the type of accounts sought accounts|groups
		* @return array Array with ACL records
		* @access private
		*/
		function _read_repository_ldap($account_type)
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
					'appname'	=> $this->db->f('acl_appname'),
					'location'	=> $this->db->f('acl_location'), 
					'account'	=> $this->db->f('acl_account'),
					'rights'	=> $this->db->f('acl_rights'),
					'grantor'	=> $this->db->f('acl_grantor'),
					'type'		=> $this->db->f('acl_type'),
					'account_type' =>  $GLOBALS['phpgw']->accounts->get_type($this->db->f('account_type'))
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
		function _read_repository_sql($account_type)
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
					'appname'	=> $this->db->Record['acl_appname'],// $this->db->f('acl_appname'),
					'location'	=> $this->db->Record['acl_location'],// $this->db->f('acl_location'), 
					'account'	=> $this->db->Record['acl_account'],// $this->db->f('acl_account'),
					'rights'	=> $this->db->Record['acl_rights'],// $this->db->f('acl_rights'),
					'grantor'	=> $this->db->Record['acl_grantor'],// $this->db->f('acl_grantor'),
					'type'		=> $this->db->Record['acl_type'],// $this->db->f('acl_type'),
					'account_type' => $this->db->Record['account_type'] // $this->db->f('account_type')
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
		function get_accounts_at_location($appname = '', $location ='', $grantor=0 ,$type ='')
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
		function delete_cache($account_id)
		{
			if($this->load_from_shm())
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
		function clear_shm($account_id)
		{
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id);
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id);
			$GLOBALS['phpgw']->shm->delete_key($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id);

			$members = $this->get_ids_for_location($account_id, 1, 'phpgw_group');

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
		function clear_cache($account_id)
		{

			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id, 'acl_data', '##DELETE##');
			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id, 'acl_data', '##DELETE##');
			$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id, 'acl_data', '##DELETE##');

			$members = $this->get_ids_for_location($account_id, 1, 'phpgw_group');

			if (is_array($members) && count($members) > 0)
			{
				foreach ( $members as $account_id )
				{
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_groups_' . $account_id, 'acl_data', '##DELETE##');
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_accounts_' . $account_id, 'acl_data', '##DELETE##');
					$GLOBALS['phpgw']->session->phpgw_cache($GLOBALS['phpgw_info']['user']['domain'] . 'acl_data_both_' . $account_id, 'acl_data', '##DELETE##');
				}
			}

//			$this->db->query("DELETE FROM phpgw_app_sessions WHERE loginid = '-1' AND app='acl_data'",__LINE__,__FILE__);
		}
	}
