<?php
	/**
	* View and manipulate account records using SQL
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v3 or later
	* @package phpgroupware
	* @subpackage phpgwapi
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
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* View and manipulate handling user and group account records using SQL
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category accounts
	*/
	class phpgwapi_accounts_sql extends phpgwapi_accounts_
	{
		function __construct($account_id = null, $account_type = null)
		{
			parent::__construct($account_id, $account_type);
		}

		/**
		* Read account information from database
		*
		* @return array Array with the following information: userid, account_id, account_lid, firstname, lastname, account_firstname, account_lastname, fullname, lastlogin, lastloginfrom, lastpasswd_change, status, expires, person_id
		*/
		function read_repository()
		{
			$id = (int) $this->account_id;
			$this->db->query('SELECT * FROM phpgw_accounts WHERE account_id=' . intval($this->account_id),__LINE__,__FILE__);
			if ( $this->db->next_record() )
			{
				$record = array
				(
					'id'				=> $this->db->f('account_id'),
					'lid'				=> $this->db->f('account_lid'),
					'passwd_hash'		=> $this->db->f('account_pwd'),
					'firstname'			=> $this->db->f('account_firstname'),
					'lastname'			=> $this->db->f('account_lastname'),
					'last_login'		=> $this->db->f('account_lastlogin'),
					'last_login_from'	=> $this->db->f('account_lastloginfrom'),
					'last_passwd_change'=> $this->db->f('account_lastpwd_change'),
					'status'			=> $this->db->f('account_status') == 'A',
					'expires'			=> $this->db->f('account_expires'),
					'person_id'			=> $this->db->f('person_id'),
					'quota'				=> $this->db->f('account_quota')
				);

				$this->account = new phpgwapi_user();
				$this->account->init($record);
			}
			return $this->account;
		}

		/**
		* Save/update account information to/in database
		*/
		function save_repository()
		{
			if ( !$this->account->is_dirty() )
			{
				return true; // nothing to do here
			}

			$data = array
			(
				'id'		=> (int) $this->account->id,
				'lid'		=> $this->db->db_addslahes($this->account->lid),
				'firstname'	=> $this->db->db_addslahes($this->account->firstname),
				'lastname'	=> $this->db->db_addslahes($this->account->lastname),
				'status'	=> $this->account->status ? 'A' : 'I', // this really has to become a bool
				'expires'	=> (int) $this->account->expires,
				'person_id'	=> (int) $this->account->person_id,
				'quota'		=> (int) $this->account->quota,
			);

			$sql = 'UPDATE phpgw_accounts'
					. " SET account_lid = '{$data['lid']}', " 
						. " account_firstname = '{$data['firstname']}', "
						. " account_lastname = '{$data['lastname']}', "
						. " account_status = '{$data['status']}', "
						. " account_expires = {$data['expires']}, "
						. " person_id = {$data['person_id']}, "
						. " account_quota = {$data['quota']}"
					. " WHERE account_id = {$data['account_id']}";
							
			return $this->db->query($sql, __LINE__, __FILE__);
		}

		function delete($accountid = '')
		{
			$account_id = get_account_id($accountid);

			/* Do this last since we are depending upon this record to get the account_lid above */
			$tables_array = Array('phpgw_accounts');
			$this->db->lock($tables_array);
			$this->db->query('DELETE FROM phpgw_accounts WHERE account_id=' . $account_id);
			$this->db->unlock();
			return true;
		}

		function get_list($_type='both',$start = -1,$sort = '', $order = '', $query = '', $offset = -1)
		{
			// For XML-RPC
/*			if (is_array($_type))
			{
				$p      = $_type;
				$_type  = $p[0]['type'];
				$start  = $p[0]['start'];
				$order  = $p[0]['order'];
				$query  = $p[0]['query'];
				$offset = $p[0]['offset'];
			}
*/
			$start = (isset($start) ? (int) $start : 0 );
			$offset = (isset($offset) ? (int) $offset : 0 );

			if (! $sort)
			{
				$sort = "DESC";
			}

			if ($order)
			{
				$orderclause = "ORDER BY $order $sort";
			}
			else
			{
				$orderclause = "ORDER BY account_lid ASC";
			}

			$whereclause = '';
			switch($_type)
			{
				case 'accounts':
					$whereclause = "WHERE account_type = 'u'";
					break;
				case 'groups':
					$whereclause = "WHERE account_type = 'g'";
					break;
			}

			if ($query)
			{
				$query = $this->db->db_addslashes($query);
				if ($whereclause)
				{
					$whereclause .= ' AND ( ';
				}
				else
				{
					$whereclause = ' WHERE ( ';
				}

				$whereclause .= " account_firstname $this->like '%$query%' OR account_lastname $this->like "
					. "'%$query%' OR account_lid $this->like '%$query%' OR person_id $this->like '%$query%')";
			}

			$sql = "SELECT * FROM phpgw_accounts $whereclause $orderclause";
			if ($offset == -1 && $start == -1)
			{
				$this->db->query($sql, __LINE__ ,__FILE__);
			} 
			elseif ($start != -1)
			{
				$this->db->limit_query($sql, $start,__LINE__,__FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start,__LINE__,__FILE__, $offset);
			}

			$accounts = array();
			while ($this->db->next_record())
			{
				$record = array
				(
					'id'			=> $this->db->f('account_id'),
					'lid'			=> $this->db->f('account_lid'),
					'passwd_hash'	=> $this->db->f('account_pwd'),
					'firstname'		=> $this->db->f('account_firstname'),
					'lastname'		=> $this->db->f('account_lastname'),
					'lastlogin'		=> $this->db->f('account_lastlogin'),
					'lastloginfrom'	=> $this->db->f('account_lastloginfrom'),
					'lastpasswd_change'		=> $this->db->f('account_lastpwd_change'),
					'status'		=> $this->db->f('account_status') == 'A',
					'expires'		=> $this->db->f('account_expires'),
					'person_id'		=> $this->db->f('person_id'),
					'quota'			=> $this->db->f('account_quota')
				);

				$id = $record['id'];

				$accounts[$id] = new phpgwapi_user();
				$accounts[$id]->init($record);
			}

			$this->db->query("SELECT count(account_id) FROM phpgw_accounts $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f(0);

			return $accounts;
		}
		
		function name2id($account_lid)
		{
			static $name_list;

			if (! $account_lid)
			{
				return False;
			}

			if ( isset($name_list[$account_lid])
				&& $name_list[$account_lid] != '')
			{
				return $name_list[$account_lid];
			}

			$account_lid = $this->db->db_addslashes($account_lid);

			$this->db->query('SELECT account_id FROM phpgw_accounts '
				. " WHERE account_lid='" . $account_lid . "'",__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$name_list[$account_lid] = intval($this->db->f('account_id'));
			}
			else
			{
				$name_list[$account_lid] = False;
			}
			return $name_list[$account_lid];
		}

		/**
		* Convert an id into its corresponding account login or group name
		*
		* @param integer $id Account or group id
		* @return string account login id or the group - empty string means not found
		*/
		function id2lid($account_id)
		{
			static $lid_list;

			$account_id = (int)$account_id;

			if (! $account_id)
			{
				return '';
			}

			if( isset($lid_list[$account_id]) ) 
			{
				return $lid_list[$account_id];
			}

			$this->db->query("SELECT account_lid FROM phpgw_accounts WHERE account_id={$account_id}",__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$lid_list[$account_id] = $this->db->f('account_lid');
			}
			else
			{
				$lid_list[$account_id] = '';
			}
			return $lid_list[$account_id];
		}

		/**
		* Convert an id into its corresponding account or group name
		*
		* @param integer $id Account or group id
		* @param bool $only_lid only return the account_lid for the user, should not be used when output is displayed to other users
		* @return string Name of the account or the group when found othwerwise empty string
		*/
		function id2name($account_id)
		{
			static $id_list;

			$account_id = (int) $account_id;

			if (! $account_id)
			{
				return '';
			}

			if( isset($id_list[$account_id]) ) 
			{
				return $id_list[$account_id];
			}

			$this->db->query("SELECT account_lid, account_firstname, account_lastname FROM phpgw_accounts WHERE account_id={$account_id}", __LINE__, __FILE__);
			if($this->db->next_record())
			{
				$id_list[$account_id] = $GLOBALS['phpgw']->common->display_fullname($this->db->f('account_lid'), $this->db->f('account_firstname'), $this->db->f('account_lastname') );
			}
			else
			{
				$id_list[$account_id] = '';
			}
			return $id_list[$account_id];
		}

		/**
		* Match a contact ID with an account id
		*
		* @param int $person_id the contact person ID
		* @param int account id - 0 if not found
		*/
		function search_person($person_id)
		{
			static $person_list;

			if (! $person_id)
			{
				return 0;
			}

			if ( isset($person_list[$person_id]) )
			{
				return $person_list[$person_id];
			}

			$this->db->query('SELECT account_id FROM phpgw_accounts WHERE person_id=' . (int) $person_id ,__LINE__,__FILE__);
			if($this->db->num_rows())
			{
				$this->db->next_record();
				$person_list[$person_id] = $this->db->f('account_id');
			}
			else
			{
				$person_list[$person_id] = 0;
			}
			return $person_list[$person_id];
		}

		function get_type($accountid)
		{
			static $account_type;
			$account_id = get_account_id($accountid);
			
			if (isset($this->account_type) && $account_id == $this->account_id)
			{
				return $this->account_type;
			}

			if(@isset($account_type[$account_id]) && @$account_type[$account_id])
			{
				return $account_type[$account_id];
			}
			elseif($account_id == '')
			{
				return False;
			}
			$this->db->Halt_On_Error = 'no';
			$this->db->query('SELECT account_type FROM phpgw_accounts WHERE account_id=' .intval($account_id), __LINE__,__FILE__);
			if ($this->db->num_rows())
			{
				$this->db->next_record();
				$account_type[$account_id] = $this->db->f('account_type');
			}
			else
			{
				$account_type[$account_id] = False;
			}
			$this->db->Halt_On_Error = 'yes';
			return $account_type[$account_id];
		}

		function exists($account_lid) // imho this should take $id, $lid as args
		{
			static $by_id, $by_lid;

			$sql = 'SELECT count(account_id) FROM phpgw_accounts WHERE ';
			if(is_integer($account_lid))
			{
				if(@isset($by_id[$account_lid]) && $by_id[$account_lid] != '')
				{
					return $by_id[$account_lid];
				}
				$sql .= 'account_id=' . intval($account_lid);
			}
			else
			{
				if(@isset($by_lid[$account_lid]) && $by_lid[$account_lid] != '')
				{
					return $by_lid[$account_lid];
				}
				$sql .= "account_lid = '" . $this->db->db_addslashes($account_lid) . "'";
			}

			$this->db->query($sql,__LINE__,__FILE__);
			$this->db->next_record();
			$ret_val = $this->db->f(0) > 0;
			if(is_integer($account_lid))
			{
				$by_id[$account_lid] = $ret_val;
				$by_lid[$this->id2name($account_lid)] = $ret_val;
			}
			else
			{
				$by_lid[$account_lid] = $ret_val;
				$by_id[$this->name2id($account_lid)] = $ret_val;
			}
			return $ret_val;
		}

		function set_data($data)
		{
			parent::set_data($data);
			return true;
		}
			
		/**
		 * Create a new account
		 *
		 * 
		 */
		function create($account_info, $default_prefs = true)
		{
			$this->set_data($account_info);
			$this->db->transaction_begin();
			
			$person_id = 0;
			if ( $account_info['account_type'] == 'u' )
			{
				// FIXME this should use the contacts classes via this->save_contact_for_account
				$values= array
				(
					-3,
					'public',
					1,
				);

				$values	= $this->db->validate_insert($values);

				$this->db->query("INSERT INTO phpgw_contact (owner,access,contact_type_id) "
					. "VALUES ($values)",__LINE__,__FILE__);
				$person_id = $this->db->get_last_insert_id('phpgw_contact','contact_id');

				$ts = time();
				$values= array
				(
					$person_id,
					$this->db->db_addslashes($this->firstname),
					$this->db->db_addslashes($this->lastname),
					((isset($this->status) && $this->status == 'A') ? 'Y':'N'),
					$ts,
					-3,
					$ts,
					-3	
				);

				$values	= $this->db->validate_insert($values);

				$this->db->query("INSERT INTO phpgw_contact_person (person_id,first_name,last_name,active,created_on,created_by,modified_on,modified_by) "
					. "VALUES ($values)",__LINE__,__FILE__);
			}


			$fields = array
			(
				'account_lid',
				'account_type',
				'account_pwd',
				'account_firstname',
				'account_lastname',
				'account_status',
				'account_expires',
				'person_id',
				'account_quota'
			);

			$data = array
			(
				'lid'		=> "'" . $this->db->db_addslahes($this->account->lid) . "'",
				'firstname'	=> "'" . $this->db->db_addslahes($this->account->firstname) ."'",
				'lastname'	=> "'" . $this->db->db_addslahes($this->account->lastname) . "'",
				'status'	=> "'" . $this->account->status ? 'A' : 'I', // this really has to become a bool
				'expires'	=> (int) $this->account->expires,
				'person_id'	=> (int) $this->account->person_id,
				'quota'		=> (int) $this->account->quota,
			);

			if ( (int)$this->account->id && !$this->exists($this->account->id) )
			{
				$fields[] = 'account_id';
				$values[] = (int) $this->account->id;
			}
			$this->db->query('INSERT INTO phpgw_accounts ('.implode($fields, ',').') '.
							'VALUES ('.implode($values, ',').')',  __LINE__, __FILE__);

			$this->account->id = $this->db->get_last_insert_id('phpgw_accounts','account_id');
			$this->db->transaction_commit();
			return parent::create($this->account, $default_prefs);
		}

		function get_account_name($accountid,&$lid,&$fname,&$lname)
		{
			static $account_name;
			
			$account_id = (int) get_account_id($accountid);
			if(isset($account_name[$account_id]))
			{
				$lid = $account_name[$account_id]['lid'];
				$fname = $account_name[$account_id]['fname'];
				$lname = $account_name[$account_id]['lname'];
				return;
			}
			$db =& $GLOBALS['phpgw']->db;
			$db->query("SELECT account_lid, account_firstname, account_lastname FROM phpgw_accounts WHERE account_id={$account_id}", __LINE__, __FILE__);
			if ( $db->next_record() )
			{
				$lid	= $account_name[$account_id]['lid']   = $db->f('account_lid');
				$fname	= $account_name[$account_id]['fname'] = $db->f('account_firstname');
				$lname	= $account_name[$account_id]['lname'] = $db->f('account_lastname');
			}
			return;
		}

		function get_account_with_contact()
		{
			$accounts = array();
			
			$sql = 'SELECT account_id, person_id FROM phpgw_accounts '
				. 'WHERE person_id IS NOT NULL OR person_id != 0';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$accounts[$this->db->f('account_id')] = $this->db->f('person_id');
			}
			return $accounts;
		}

		function get_account_without_contact()
		{
			$sql = 'SELECT account_id FROM phpgw_accounts '
				. 'WHERE person_id IS NULL OR person_id = 0';
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$accounts[] = $this->db->f('account_id');
			}
			return $accounts;
		}

		/**
		* Get a list of groups the user is a member of
		*
		* @param int $account_id the user account to lookup
		* @return array the groups the user is a member of 
		* @internal return structure array(array('account_id' => id, 'account_name' => group name))
		*/
		public function membership($account_id = 0)
		{
			$account_id = get_account_id($account_id);

			if ( isset($this->memberships[$account_id]) 
				&& is_array($this->memberships[$account_id]) )
			{
				return $this->memberships[$account_id];
			}

			$this->memberships[$account_id] = array();

			$sql = 'SELECT phpgw_accounts.account_id, phpgw_accounts.account_firstname FROM phpgw_accounts'
				. " {$this->db->join} phpgw_group_map ON phpgw_accounts.account_id = phpgw_group_map.group_id"
				. " WHERE phpgw_group_map.account_id = {$account_id}";
			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$this->memberships[$account_id][] = array
				(
					'account_id'	=> $this->db->f('account_id'),
					'account_name'	=> $this->db->f('account_firstname', true)
				);
			}
			foreach ( $this->memberships[$account_id] as &$member )
			{
				$member['account_name']	= lang('%1 group', $member['account_name']);
			}
			return $this->memberships[$account_id];
		}

		/**
		* Get a list of members of the group
		*
		* @param int $group_id the group to check
		* @return array list of members
		*/
		public function member($group_id = 0)
		{
			$group_id = get_account_id($group_id);

			if ( isset($this->members[$group_id]) )
			{
				return $this->members[$group_id];
			}

			$this->members[$group_id] = array();

			$sql = 'SELECT phpgw_accounts.account_id, phpgw_accounts.account_lid, phpgw_accounts.account_firstname, phpgw_accounts.account_lastname'
				. ' FROM phpgw_accounts'
				. " {$this->db->join} phpgw_group_map ON phpgw_accounts.account_id = phpgw_group_map.group_id"
					. " AND phpgw_group_map.group_id = {$group_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$this->members[$account_id][] = array
				(
					'account_id'	=> $this->db->f('account_id'),
					'account_name'	=> $GLOBALS['phpgw']->common->display_fullname($this->db->f('account_lid'), $this->db->f('account_firstname', true), $this->db->f('account_lastname', true))
				);
			}
			return $this->members[$group_id];
		}

		/**
		* Get a list of member account ids for a group
		*
		* @return arrray list of members of the current group
		*/
		public function get_members($group_id = null)
		{
			if ( is_null($group_id) )
			{
				$group_id = $this->account_id;
			}
			$group_id = get_account_id($group_id);

			$sql = 'SELECT phpgw_accounts.account_id'
				. ' FROM phpgw_accounts, phpgw_group_map'
				. ' WHERE phpgw_accounts.account_id = phpgw_group_map.group_id'
					. " AND phpgw_group_map.group_id = {$this->account_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			$members = array();
			while ($this->db->next_record())
			{
				$members[] =  $this->db->f('account_id');
			}
			return $members;
		}

		/**
		* Add an account to a group entry
		*
		* @param integer $account_id Account id
		* @param integer $group_id Group id
		* @return boolean true on success otherwise false
		*/
		public function add_account2group($account_id, $group_id)
		{
			$account_id = (int) $account_id;
			$group_id = (int) $group_id;
			$read = phpgw_acl::READ;

			if ( !$account_id || !$group_id )
			{
				return false;
			}

			$sql = 'INSERT INTO phpgw_group_map'
				. " VALUES({$group_id}, {$account_id}, {$read})";

			return !!$this->db->query($sql, __LINE__, __FILE__);
		}
		
		/**
		* Delete an account from a group
		*
		* @param integer $account_id Account id
		* @param integer $group_id Group id
		* @return boolean true on success otherwise false
		*/
		public function delete_account4Group($account_id, $group_id)
		{
			$account_id = (int) $account_id;
			$group_id = (int) $group_id;

			if ( !$account_id || !$group_id )
			{
				return false;
			}

			$sql = 'DELETE FROM phpgw_group_map'
				. " WHERE group_id = {$group_id} AND account_id = {$account_id}";

			return !!$this->db->query($sql, __LINE__, __FILE__);
		}
	}
