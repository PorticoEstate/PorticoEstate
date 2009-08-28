<?php
	/**
	* phpGroupWare - property: a Facilities Management System.
	*
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
	* This file is part of phpGroupWare.
	*
	* phpGroupWare is free software; you can redistribute it and/or modify
	* it under the terms of the GNU General Public License as published by
	* the Free Software Foundation; either version 2 of the License, or
	* (at your option) any later version.
	*
	* phpGroupWare is distributed in the hope that it will be useful,
	* but WITHOUT ANY WARRANTY; without even the implied warranty of
	* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	* GNU General Public License for more details.
	*
	* You should have received a copy of the GNU General Public License
	* along with phpGroupWare; if not, write to the Free Software
	* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	* @package property
	* @subpackage core
 	* @version $Id$
	*/

	/**
	 * Description
	 * @package property
	 */

	phpgw::import_class('phpgwapi.datetime');

	class property_socommon
	{
		/**
		 * @var string $join the sql syntax to use for JOIN
		 */
		 var $join = ' INNER JOIN ';

		/**
		 * @var string $like the sql syntax to use for a case insensitive LIKE
		 */
		 var $like = 'LIKE';


		function __construct()
		{

			if(is_object($GLOBALS['phpgw']->db))
			{
				$this->db = & $GLOBALS['phpgw']->db;
				//$this->db = CreateObject('phpgwapi.db');
			}
			else // for setup
			{
				$this->db = CreateObject('phpgwapi.db');

				if(isset($GLOBALS['phpgw_info']['server']['db_name']) && $GLOBALS['phpgw_info']['server']['db_name'])
				{
					$this->db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
					$this->db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
					$this->db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
					$this->db->User = $GLOBALS['phpgw_info']['server']['db_user'];
					$this->db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
				}
				else
				{
					$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'COOKIE');
					if(!$ConfigDomain)
					{
						$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'POST');
					}
					$GLOBALS['phpgw_info']['user']['domain'] = $ConfigDomain;
					$phpgw_domain = $GLOBALS['phpgw_domain'];
					$this->db->Host     = $phpgw_domain[$ConfigDomain]['db_host'];
					$this->db->Database = $phpgw_domain[$ConfigDomain]['db_name'];
					$this->db->User     = $phpgw_domain[$ConfigDomain]['db_user'];
					$this->db->Password = $phpgw_domain[$ConfigDomain]['db_pass'];
				}
			}

			$this->account	= $GLOBALS['phpgw_info']['user']['account_id'];

			switch ( $GLOBALS['phpgw_info']['server']['db_type'] )
			{
				case 'pgsql':
					$this->join = " JOIN ";
					$this->like = "ILIKE";
					break;
				case 'postgres':
					$this->join = " JOIN ";
					$this->like = "ILIKE";
					break;
				default:
					//do nothing for now
			}

			$this->left_join = " LEFT JOIN ";
		}

		function fm_cache($name='',$value='')
		{
			if($name && $value)
			{
				$value = serialize($value);

				if(function_exists('gzcompress'))
				{
					$value =  base64_encode(gzcompress($value, 9));
				}
				else
				{
					$value = $GLOBALS['phpgw']->db->db_addslashes($value);
				}

				$this->db->query("SELECT value FROM fm_cache WHERE name='{$name}'");

				if($this->db->next_record())
				{
					$this->db->query("UPDATE fm_cache SET value = '{$value}' WHERE name='{$name}'",__LINE__,__FILE__);
				}
				else
				{
					$this->db->query("INSERT INTO fm_cache (name,value)VALUES ('$name','$value')",__LINE__,__FILE__);
				}

			}
			else
			{
				$this->db->query("SELECT value FROM fm_cache where name='$name'");
				if($this->db->next_record())
				{
					$ret= $this->db->f('value');

					if(function_exists('gzcompress'))
					{
						$ret =  gzuncompress(base64_decode($ret));
					}
					else
					{
						$ret = stripslashes($ret);
					}

					return unserialize($ret);
				}
			}
		}

		/**
		* Clear all content from cache
		*
		*/

		function reset_fm_cache()
		{
			$this->db->query("DELETE FROM fm_cache ",__LINE__,__FILE__);
		}

		/**
		* Clear computed userlist for location and rights from cache
		*
		* @return integer number of values was found and cleared
		*/

		function reset_fm_cache_userlist()
		{
			$this->db->query("DELETE FROM fm_cache WHERE name $this->like 'acl_userlist_%'",__LINE__,__FILE__, true);
			return $this->db->affected_rows();
		}

		function create_preferences($app='',$user_id='')
		{
				$this->db->query("SELECT preference_value FROM phpgw_preferences where preference_app = '$app' AND preference_owner=".(int)$user_id );
				$this->db->next_record();
				$value= unserialize($this->db->f('preference_value'));
				return $value;
		}

		function read_single_tenant($id)
		{
			$this->db->query("SELECT * FROM fm_tenant WHERE id =$id",__LINE__,__FILE__);
			$this->db->next_record();

				$tenant_data = array
				(
					'first_name'		=> $this->db->f('first_name'),
					'last_name'			=> $this->db->f('last_name'),
					'contact_phone'		=> $this->db->f('contact_phone')
				);

//_debug_array($tenant_data);

			return	$tenant_data;
		}

		function check_location($location_code='',$type_id='')
		{
			$this->db->query("SELECT count(*) FROM fm_location$type_id where location_code='$location_code'");
			$this->db->next_record();

			if ( $this->db->f(0))
			{
				return true;
			}
		}

		function select_part_of_town($district_id='')
		{
			$filter = '';
			$part_of_town = array();
			if($district_id)
			{
				$filter = "WHERE district_id = '$district_id'";
			}
			$this->db->query("SELECT name, part_of_town_id, district_id FROM fm_part_of_town $filter ORDER BY name ",__LINE__,__FILE__);

			while ($this->db->next_record())
			{
				$part_of_town[] = array
				(
					'id'			=> $this->db->f('part_of_town_id'),
					'name'			=> $this->db->f('name',true),
					'district_id'	=> $this->db->f('district_id')
				);
			}

			return $part_of_town;
		}

		function select_district_list()
		{
			$this->db->query("SELECT id, descr FROM fm_district where id >'0' ORDER BY id ");

			$i = 0;
			while ($this->db->next_record())
			{
				$district[$i]['id']				= $this->db->f('id');
				$district[$i]['name']				= stripslashes($this->db->f('descr'));
				$i++;
			}

			return $district;
		}

		/**
		* Finds the next ID for a record at a table
		*
		* @param string $table tablename in question
		* @param array $key conditions
		* @return int the next id
		*/

		function next_id($table='',$key='')
		{
			$where = '';
			if(is_array($key))
			{
			//	while (is_array($key) && list($column,$value) = each($key))
				foreach ($key as $column => $value)
				{
					if($value)
					{
						$condition[] = $column . "='" . $value;
					}
				}

				$where='WHERE ' . implode("' AND ", $condition) . "'";
			}

			$this->db->query("SELECT max(id) as maximum FROM $table $where",__LINE__,__FILE__);
			$this->db->next_record();
			$next_id = $this->db->f('maximum')+1;
			return $next_id;
		}
		function get_lookup_entity($location)
		{
			$this->db->query("SELECT entity_id,name FROM fm_entity_lookup $this->join fm_entity on fm_entity_lookup.entity_id=fm_entity.id WHERE type='lookup' AND location='$location'  ");

			$i = 0;

			while ($this->db->next_record())
			{
				$entity[$i]['id']				= $this->db->f('entity_id');
				$entity[$i]['name']				= $this->db->f('name');
				$i++;
			}
			return $entity;
		}

		function get_start_entity($location)
		{
			$this->db->query("SELECT entity_id,name FROM fm_entity_lookup $this->join fm_entity on fm_entity_lookup.entity_id=fm_entity.id WHERE type='start' AND location='$location'  ");

			$i = 0;
			while ($this->db->next_record())
			{
				$entity[$i]['id']				= $this->db->f('entity_id');
				$entity[$i]['name']				= $this->db->f('name');
				$i++;
			}

			if (isset($entity))
			{
				return $entity;
			}
		}

		function increment_id($name)
		{
			if($name == 'order') // FIXME: temporary hack
			{
				$name = 'workorder';
			}
			$this->db->query("SELECT value FROM fm_idgenerator WHERE name='{$name}'");
			$this->db->next_record();
			$next_id = $this->db->f('value') +1;

			$this->db->query("UPDATE fm_idgenerator SET value = $next_id WHERE name = '{$name}'");
			return $next_id;
		}

		function new_db($db ='' )
		{
			if(is_object($db))
			{
				$db = clone($db);
			}
			else if( is_object($GLOBALS['phpgw']->db) )
			{
				$db = & $GLOBALS['phpgw']->db;
			}
			else
			{
				$db = CreateObject('phpgwapi.db');
				if(isset($GLOBALS['phpgw_info']['server']['db_name']) && $GLOBALS['phpgw_info']['server']['db_name'])
				{
					$db->Host = $GLOBALS['phpgw_info']['server']['db_host'];
					$db->Type = $GLOBALS['phpgw_info']['server']['db_type'];
					$db->Database = $GLOBALS['phpgw_info']['server']['db_name'];
					$db->User = $GLOBALS['phpgw_info']['server']['db_user'];
					$db->Password = $GLOBALS['phpgw_info']['server']['db_pass'];
				}
				else
				{
					$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'COOKIE');
					if(!$ConfigDomain)
					{
						$ConfigDomain = phpgw::get_var('ConfigDomain', 'string' , 'POST');
					}
					$phpgw_domain = $GLOBALS['phpgw_domain'];
					$GLOBALS['phpgw_info']['user']['domain'] = $ConfigDomain;
					$db->Host     = $phpgw_domain[$ConfigDomain]['db_host'];
					$db->Database = $phpgw_domain[$ConfigDomain]['db_name'];
					$db->User     = $phpgw_domain[$ConfigDomain]['db_user'];
					$db->Password = $phpgw_domain[$ConfigDomain]['db_pass'];
				}
			}

			return $db;
		}

		function get_max_location_level()
		{
			$this->db->query("SELECT count(*) as level FROM fm_location_type ");
			$this->db->next_record();
			return $this->db->f('level');
		}

		function active_group_members($group_id = '')
		{
			$this->db->query("SELECT phpgw_accounts.account_id, phpgw_accounts.account_lid FROM phpgw_acl $this->join phpgw_accounts on phpgw_acl.acl_account = phpgw_accounts.account_id"
				. " WHERE phpgw_acl.acl_location = $group_id AND phpgw_acl.acl_appname = 'phpgw_group' AND account_status = 'A'");

			while ($this->db->next_record())
			{
				$members[] = array (
				'account_id' => $this->db->f('account_id'),
				'account_name' => $this->db->f('account_lid')
				);
			}
			return $members;
		}

		/**
		* Get list of accessible physical locations for current user
		*
		* @param integer $required Right the user has to be granted at location
		*
		* @return array $access_location list of accessible physical locations
		*/

		public function get_location_list($required)
		{
			$access_list	= $GLOBALS['phpgw']->acl->get_location_list('property',$required);

			$needle = ".location.1.";
			$needle_len = strlen($needle);
			$access_location = array();
			foreach($access_list as $location)
			{
				if(strrpos($location,$needle ) === 0)
				{
					$target_len = strlen($location)- $needle_len;
					$access_location[] = substr($location,-$target_len);
				}
			}
			return $access_location;
		}

		/**
		* pending action for items across the system.
		*
		* @param array   $data array containing string  'appname'			- the name of the module being looked up
		*										string  'location'			- the location within the module to look up
		* 										integer 'id'				- id of the referenced item - could possibly be a bigint
		* 										integer 'responsible'		- the user_id asked for approval
		* 										string  'responsible_type'  - what type of responsible is asked for action (user,vendor or tenant)
		* 										string  'action'			- what type of action is pending
		* 										string  'remark'			- a general remark - if any
		* 										integer 'deadline'			- unix timestamp if any deadline is given.
		*
		* @return integer $reminder  number of request for this action
		*/

		public function set_pending_action($data = array())
		{
			$appname		= $data['appname'];
			$location		= $data['location'];
			$item_id		= $data['id']; //possible bigint
			$responsible	= (int) $data['responsible'];
			$action			= $this->db->db_addslashes($data['action']);
			$remark			= $this->db->db_addslashes($data['remark']);
			$deadline		= (int) $data['deadline'];

			if( !$item_id)
			{
				throw new Exception("No item_id given");				
			}


			$valid_responsible_types = array
			(
				'user',
				'vendor',
				'tenant'
			);

			$responsible_type = isset($data['responsible_type']) && $data['responsible_type'] ? $data['responsible_type'] : 'user';

			if( !in_array($responsible_type, $valid_responsible_types))
			{
				throw new Exception("No valid responsible_type given");				
			}

			$sql = "SELECT id FROM fm_action_pending_category WHERE num = '{$action}'";
			$this->db->query($sql, __LINE__,__FILE__);
			$this->db->next_record();
			$action_category = $this->db->f('id');
			if ( !$action_category )
			{
				throw new Exception("No valid action_type given");							
			}

			$location_id = $GLOBALS['phpgw']->locations->get_id($appname, $location);
			
			if ( !$location_id )
			{
				throw new Exception("phpgwapi_locations::get_id ({$appname}, {$location}) returned 0");
			}

			$reminder = 1;
			$this->db->transaction_begin();

			$condition = " WHERE location_id = {$location_id}"
				. " AND item_id = {$item_id}"
				. " AND responsible = {$responsible}"
				. " AND action_category = {$action_category}"
				. " AND action_performed IS NULL"
				. " AND expired_on IS NULL";

			$sql = "SELECT id, reminder FROM fm_action_pending {$condition}";
				

			$this->db->query($sql, __LINE__,__FILE__);
			$this->db->next_record();
			if($this->db->f('reminder'))
			{
				$reminder	= $this->db->f('reminder') + 1;
				$id			= $this->db->f('id');

				$value_set = array
				(
					'expired_on' 		=> phpgwapi_datetime::user_localtime(),
					'expired_by' 		=> $this->account,
				);

				if ( $deadline > 0 )
				{
					$value_set['deadline'] = $deadline;
				}

				$value_set	= $this->db->validate_update($value_set);
				$sql = "UPDATE fm_action_pending SET {$value_set} WHERE id = $id";
				$this->db->query($sql, __LINE__,__FILE__);
			}

			$values= array
			(
				$item_id,								//item_id
				$location_id,
				$responsible,							// responsible
				$responsible_type,						// responsible_type
				$action_category, 						//action_category
				phpgwapi_datetime::user_localtime(),	// action_requested
				$reminder,
				$deadline,								//action_deadline
				phpgwapi_datetime::user_localtime(),	//created_on
				$this->account,							//created_by
				$remark									//remark
			);
				
			$values	= $this->db->validate_insert($values);
			$sql = "INSERT INTO fm_action_pending ("
				. "item_id, location_id, responsible, responsible_type,"
				. "action_category, action_requested, reminder, action_deadline,"
				. "created_on, created_by, remark) VALUES ( $values $vals)";
			$this->db->query($sql, __LINE__,__FILE__);

			$this->db->transaction_commit();
			return $reminder;
		}

		public function get_pending_action($data = array())
		{

			$appname		= isset($data['appname']) && $data['appname'] ? $data['appname'] : '';
			$location		= isset($data['location']) && $data['location'] ? $data['location'] : '';
			$item_id		= isset($data['id']) && $data['id'] ? $data['id'] : '';$data['id']; //possible bigint
			//FIXME
			$responsible	= (int) $data['responsible'];
			$action			= isset($data['action']) && $data['action'] ? $this->db->db_addslashes($data['action']) : '';
			$deadline		= isset($data['deadline']) && $data['deadline'] ? (int) $data['deadline'] : 0;
			
			
			$ret = array();
			$condition =   
				" WHERE action_performed IS NULL"
			//	. " AND responsible =  {$GLOBALS['phpgw_info']['user']['account_id']}"
				. " AND num = 'approval'"
				. " AND expired_on IS NULL";

			$sql = "SELECT * FROM fm_action_pending {$this->join} fm_action_pending_category ON fm_action_pending.action_category = fm_action_pending_category.id {$condition}";

			$this->db->query($sql, __LINE__,__FILE__);
			$ret = $this->db->resultSet;

			$interlink = CreateObject('property.interlink');
			
			foreach ($ret as &$entry)
			{
				if( !$location )
				{
					$location = $GLOBALS['phpgw']->locations->get_name($entry['location_id']);
				}
				$entry['url'] = $interlink->get_relation_link($location, $entry['item_id'], 'edit');
			}
			return $ret;
		}

		public function close_pending_action($data = array())
		{
		}
	}
