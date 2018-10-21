<?php
	/**
	* View and manipulate account records using SQL
	*
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @copyright Copyright (C) 2000-2010 Free Software Foundation, Inc. http://www.fsf.org/
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
	* View and manipulate handling user and group account records using SQL
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category accounts
	*/
	class phpgwapi_accounts_sql extends phpgwapi_accounts_
	{
		protected $global_lock = false;
		/**
		* Constructor
		*
		* @param integer $account_id   Account id defaults to current account id
		* @param string  $account_type Account type 'u': account; 'g' : group; defaults to current account type
		*
		* @return void
		*/
		public function __construct($account_id = null, $account_type = null)
		{
			parent::__construct($account_id, $account_type);
		}

		/**
		* Add an account to a group entry
		*
		* @param integer $account_id Account id
		* @param integer $group_id   Group id
		*
		* @return boolean true on success otherwise false
		*/
		public function add_user2group($account_id, $group_id)
		{
			$account_id = (int) $account_id;
			$group_id = (int) $group_id;
			$read = phpgwapi_acl::READ;

			if ( !$account_id || !$group_id )
			{
				return false;
			}

			// Check if it already exists
			$sql = 'SELECT group_id FROM phpgw_group_map'
				. " WHERE	group_id = {$group_id}"
					. " AND account_id = {$account_id}";

			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				return true;
			}

			$sql = 'INSERT INTO phpgw_group_map'
				. " VALUES({$group_id}, {$account_id}, {$read})";

			return !!$this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		 * Create a new group account  - this only creates the acccount
		 *
		 * For creating a fully working user, use self::create()
		 *
		 * @param object $account the phpgwapi_user object for the new account
		 *
		 * @return integer the new user id
		 *
		 * @see self::create
		 */
		public function create_group_account($account)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$id = (int) $account->id;
			if ( !$id || $this->exists($id) )
			{
				$id = $this->_get_nextid('g');
			}

			$data = array
			(
				'account_id'		=> $id,
				'account_lid'		=> "'" . $this->db->db_addslashes($account->lid) . "'",
				'account_pwd'		=> "''",
				'account_firstname'	=> "'" . $this->db->db_addslashes($account->firstname) ."'",
				'account_lastname'	=> "'" . $this->db->db_addslashes($account->lastname) . "'",
				'account_expires'	=> -1,
				'account_type'		=> "'" . phpgwapi_account::TYPE_GROUP . "'",
				'account_status'	=> "'A'",
				'person_id'			=> (int) $account->person_id
			);

			$this->db->query('INSERT INTO phpgw_accounts (' . implode(', ', array_keys($data)) . ') '.
							'VALUES (' . implode(', ', $data) . ')', __LINE__, __FILE__);

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			$account->id = $id;
			return $account->id;
		}


		/**
		 * Create a new user account  - this only creates the acccount
		 *
		 * For creating a fully working user, use self::create()
		 *
		 * @param object $account the phpgwapi_user object for the new account
		 *
		 * @return integer the new user id
		 *
		 * @see self::create
		 */
		public function create_user_account($account)
		{
			if ( $this->db->get_transaction() )
			{
				$this->global_lock = true;
			}
			else
			{
				$this->db->transaction_begin();
			}

			$id = (int) $account->id;
			if ( !$id || $this->exists($id) )
			{
				$id = $this->_get_nextid('u');
			}

			$data = array
			(
				'account_id'=> $id,
				'account_lid'		=> "'" . $this->db->db_addslashes($account->lid) . "'",
				'account_type'		=> "'" . phpgwapi_account::TYPE_USER . "'",
				'account_firstname'	=> "'" . $this->db->db_addslashes($account->firstname) ."'",
				'account_lastname'	=> "'" . $this->db->db_addslashes($account->lastname) . "'",
				'account_pwd'		=> "'" . $this->db->db_addslashes($account->passwd_hash) . "'",
				'account_status'	=> $account->enabled ? "'A'" : "'I'", // FIXME this really has to become a bool
				'account_expires'	=> (int) $account->expires,
				'person_id'			=> (int) $account->person_id,
				'account_quota'		=> (int) $account->quota,
			);

			$this->db->query('INSERT INTO phpgw_accounts (' . implode(', ', array_keys($data)) . ') '.
							'VALUES (' . implode(', ', $data) . ')', __LINE__, __FILE__);

			if ( !$this->global_lock )
			{
				$this->db->transaction_commit();
			}

			$account->id = $id;

			$this->account = $account;

			return $account->id;
		}

		/**
		 * Delete an account
		 *
		 * @param integer $account_id the account to delete
		 *
		 * @return boolean was the account deleted?
		 */
		public function delete($account_id)
		{
			$account_id = (int) $account_id;

			//TODO decide if we should silently allow deletion of non existent accounts
			$acct = $this->get($account_id);
			if ( !is_object($acct) )
			{
				return false;
			}

			$this->db->transaction_begin();


			$deleted = !!$this->db->query("DELETE FROM phpgw_accounts WHERE account_id={$account_id}");
			if ( $deleted )
			{
				// Delete all ACLs
				$GLOBALS['phpgw']->acl->delete_repository('%%', '%%', $account_id);

				if ( get_class($acct) == phpgwapi_account::CLASS_TYPE_GROUP )
				{
					$sql = 'DELETE FROM phpgw_group_map'
						. " WHERE group_id = {$account_id}";

					$GLOBALS['phpgw']->hooks->process('deletegroup');
				}
				else
				{
					$GLOBALS['phpgw']->hooks->process('deleteaccount');
					$sql = 'DELETE FROM phpgw_group_map'
						. " WHERE account_id = {$account_id}";
				}

				// The cached object is needed for the hooks
				phpgwapi_cache::system_clear('phpgwapi', "account_{$account_id}");

				// delete the group mappings
				if ( !!$this->db->query($sql) )
				{
					$this->db->transaction_commit();
					return true;
				}
			}
			$this->db->transaction_abort();
			return false;
		}

		/**
		* Delete an account from a group
		*
		* @param integer $account_id Account id
		* @param integer $group_id   Group id
		*
		* @return boolean true on success otherwise false
		*/
		public function delete_account4group($account_id, $group_id)
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

		/**
		 * Does the user account exist?
		 *
		 * @param integer|string $account_lid the accound login or id to check
		 *
		 * @return boolean does the account exist or not?
		 */
		public function exists($account_lid)
		{
			static $by_id;
			static $by_lid;

			$sql = 'SELECT count(account_id) as cnt FROM phpgw_accounts WHERE ';
			if ( is_int($account_lid) )
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

			$this->db->query($sql, __LINE__, __FILE__);
			$this->db->next_record();
			$ret_val = $this->db->f('cnt') > 0;
			if(is_int($account_lid))
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

		/**
		 * Fetch an account
		 *
		 * @param integer $id        the account id to fetch
		 * @param boolean $use_cache read the record from the cache, should (just about) always be true
		 *
		 * @return object the account as a phpgw_account derived object
		 */
		public function get($id, $use_cache = true)
		{
			$id = (int) $id;
			$account = null;
			static $cache = array();


			if(isset($cache[$id]))
			{
				return $cache[$id];
			}

			if ( $use_cache )
			{
				$account = phpgwapi_cache::system_get('phpgwapi', "account_{$id}");
				if ( is_object($account) )
				{
					return $account;
				}
			}

			$this->db->query("SELECT * FROM phpgw_accounts WHERE account_id = {$id}", __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				$record = array
				(
					'id'				=> $this->db->f('account_id'),
					'lid'				=> $this->db->f('account_lid'),
					'passwd_hash'		=> $this->db->f('account_pwd', true),
					'firstname'			=> $this->db->f('account_firstname', true),
					'lastname'			=> $this->db->f('account_lastname', true),
					'last_login'		=> $this->db->f('account_lastlogin'),
					'last_login_from'	=> $this->db->f('account_lastloginfrom'),
					'last_passwd_change'=> $this->db->f('account_lastpwd_change'),
					'enabled'			=> $this->db->f('account_status') == 'A',
					'expires'			=> $this->db->f('account_expires'),
					'person_id'			=> $this->db->f('person_id'),
					'quota'				=> $this->db->f('account_quota'),
					'type'				=> $this->db->f('account_type'),
				);

				if ( $this->db->f('account_type') == 'g' )
				{
					$account = new phpgwapi_group();
				}
				else
				{
					$account = new phpgwapi_user();
				}
				$account->init($record);

				phpgwapi_cache::system_set('phpgwapi', "account_{$id}", $account);
			}
			$cache[$id] = $account;
			return $account;
		}

		/**
		 * Get a list of accounts which have contacts linked to them
		 *
		 * @return array account_id => contact_id mappings
		 */
		public function get_account_with_contact()
		{
			$accounts = array();

			$sql = 'SELECT account_id, person_id FROM phpgw_accounts '
				. 'WHERE person_id IS NOT NULL OR person_id != 0';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$accounts[$this->db->f('account_id')] = $this->db->f('person_id');
			}
			return $accounts;
		}

		/**
		 * Get a list of accounts which don't have contacts associated with them
		 *
		 * @return array list of account_ids without contacts
		 */
		public function get_account_without_contact()
		{
			$sql = 'SELECT account_id FROM phpgw_accounts '
				. 'WHERE person_id IS NULL OR person_id = 0';
			$this->db->query($sql, __LINE__, __FILE__);
			while ($this->db->next_record())
			{
				$accounts[] = $this->db->f('account_id');
			}
			return $accounts;
		}

		/**
		 * Get a list of accounts based on a search criteria
		 *
		 * @param string  $_type  type of accounts sought
		 * @param integer $start  the position to start at in the result set
		 * @param string  $sort   the direction to sort - valid values "ASC" or "DESC"
		 * @param string  $order  the field to sort on
		 * @param string  $query  the search criteria - matches firstname, lastname and lid
		 * @param integer $offset the number of records to return
		 *
		 * @return array list of accounts that match criteria
		 */
		public function get_list($_type='both', $start = -1, $sort = '',
								$order = '', $query = '', $offset = -1, $filter = array())
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

			if ( $sort != 'DESC' )
			{
				$sort = 'ASC';
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

			$where = 'WHERE';

			if($filter['active'] == 1)
			{
				$whereclause = "{$where} account_status = 'A'";
				$where = 'AND';
			}

			switch($_type)
			{
				case 'accounts':
					$whereclause .= " {$where} account_type = 'u'";
					$where = 'AND';
					break;
				case 'groups':
					$whereclause .= " {$where} account_type = 'g'";
					$where = 'AND';
					break;
			}

			if ($query)
			{
				$whereclause .= " {$where} (";

				if(ctype_digit($query))
				{
					$whereclause .= 'person_id =' . (int)$query . ')';
				}
				else
				{
					$query = $this->db->db_addslashes($query);

					if(strpos($query, ',' ))
					{
						$whereclause .= "account_lastname || ', ' || account_firstname $this->like '$query%'"
							. " OR account_lastname || ',' || account_firstname $this->like '$query%')";
					}
					else
					{
						$whereclause .= "account_firstname $this->like '%$query%' OR account_lastname $this->like "
							. "'%$query%' OR account_lid $this->like '%$query%')";
					}

				}
			}

			$sql = "SELECT * FROM phpgw_accounts $whereclause $orderclause";

			if ($offset == -1 && $start == -1)
			{
				$this->db->query($sql, __LINE__, __FILE__);
			}
			else if ( $start != -1 )
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__);
			}
			else
			{
				$this->db->limit_query($sql, $start, __LINE__, __FILE__, $offset);
			}

			$accounts = array();
			while ($this->db->next_record())
			{
				$record = array
				(
					'id'			=> $this->db->f('account_id'),
					'lid'			=> $this->db->f('account_lid'),
					'passwd_hash'	=> $this->db->f('account_pwd', true),
					'firstname'		=> $this->db->f('account_firstname', true),
					'lastname'		=> $this->db->f('account_lastname', true),
					'lastlogin'		=> $this->db->f('account_lastlogin'),
					'lastloginfrom'	=> $this->db->f('account_lastloginfrom'),
					'lastpasswd_change'		=> $this->db->f('account_lastpwd_change'),
					'enabled'		=> $this->db->f('account_status') == 'A',
					'expires'		=> $this->db->f('account_expires'),
					'person_id'		=> $this->db->f('person_id'),
					'quota'			=> $this->db->f('account_quota')
				);

				$id = $record['id'];

				$accounts[$id] = new phpgwapi_user();
				$accounts[$id]->init($record);
			}

			$this->db->query("SELECT count(account_id) as cnt FROM phpgw_accounts $whereclause");
			$this->db->next_record();
			$this->total = $this->db->f('cnt');

			return $accounts;
		}

		/**
		* Get a list of member account ids for a group
		*
		* @param integer $group_id the group to get members from
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

			$sql = 'SELECT phpgw_group_map.account_id'
				. " FROM phpgw_accounts $this->join phpgw_group_map ON phpgw_accounts.account_id = phpgw_group_map.group_id"
				. " WHERE phpgw_group_map.group_id = {$group_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			$members = array();
			while ($this->db->next_record())
			{
				$members[] =  $this->db->f('account_id');
			}
			return $members;
		}

		/**
		* Convert an id into its corresponding account or group name
		*
		* @param integer $id Account or group id
		*
		* @return string Name of the account or the group when found othwerwise empty string
		*/
		public function id2name($id)
		{
			static $id_list;

			$id = (int) $id;

			if ( !$id)
			{
				return '';
			}

			if( isset($id_list[$id]) )
			{
				return $id_list[$id];
			}

			$acct = $this->get($id);

			if ( is_object($acct) )
			{
				$id_list[$id] = $acct->__toString();
			}
			else
			{
				$id_list[$id] = '';
			}

			return $id_list[$id];
		}

		/**
		* Get a list of members of the group
		*
		* @param integer $group_id the group to check
		* @param bool $active only return active members
		*
		* @return array list of members
		*/
		public function member($group_id = 0, $active = false)
		{
			$group_id = get_account_id($group_id);

			if ( isset($this->members[$group_id]) )
			{
				return $this->members[$group_id];
			}

			$this->members[$group_id] = array();

			$Where = 'WHERE';
			$sql = 'SELECT phpgw_group_map.account_id'
				. ' FROM phpgw_group_map';

			if($active)
			{
				$sql .= " {$this->join} phpgw_accounts ON phpgw_group_map.account_id = phpgw_accounts.account_id"
				. " WHERE account_status = 'A'";
				$Where = 'AND';
			}

			$sql .= " {$Where} group_id = {$group_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$id = $this->db->f('account_id');
				$this->members[$group_id][$id] = array
				(
					'account_id'	=> $id
				);
			}

			foreach ( $this->members[$group_id] as $id => &$acct )
			{
				$acct['account_name'] = $this->get($id)->__toString();
			}
			return $this->members[$group_id];
		}

		/**
		* Get a list of groups the user is a member of
		*
		* @param integer $account_id the user account to lookup
		*
		* @return array the groups the user is a member of
		*
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

			$sql = 'SELECT group_id'
				. ' FROM phpgw_group_map'
				. " WHERE phpgw_group_map.account_id = {$account_id}";
			$this->db->query($sql, __LINE__, __FILE__);

			$ids = array();
			while ( $this->db->next_record() )
			{
				$ids[] = $this->db->f('group_id');
			}

			$this->memberships[$account_id] = array();
			foreach ( $ids as $id )
			{
				$this->memberships[$account_id][$id] = $this->get($id);
			}

			return $this->memberships[$account_id];
		}

		/**
		 * Convert an account login id to an account id
		 *
		 * @param string $account_lid the login id to look up
		 *
		 * @return integer the account id - 0 if not found
		 */
		public function name2id($account_lid)
		{
			static $name_list;

			if ( !$account_lid )
			{
				return 0;
			}

			if ( isset($name_list[$account_lid]) )
			{
				return $name_list[$account_lid];
			}

			$name_list[$account_lid] = 0;
			$account_lid = $this->db->db_addslashes($account_lid);

			$this->db->query('SELECT account_id FROM phpgw_accounts '
				. " WHERE account_lid='{$account_lid}'", __LINE__, __FILE__);

			if ( $this->db->next_record() )
			{
				$name_list[$account_lid] = (int) $this->db->f('account_id');
			}

			return $name_list[$account_lid];
		}

		/**
		* Read account information from database
		*
		* @return object phpgwapi_account derived object containing account data
		*/
		public function read_repository()
		{
			$this->account = $this->get($this->account_id, false);
			return $this->account;
		}

		/**
		* Save/update account information to database
		*
		* @return void
		*/
		public function save_repository()
		{
			if ( !$this->account->is_dirty() )
			{
				return true; // nothing to do here
			}

			$data = array
			(
				'id'		=> (int) $this->account->id,
				'lid'		=> $this->db->db_addslashes($this->account->lid),
				'firstname'	=> $this->db->db_addslashes($this->account->firstname),
				'lastname'	=> $this->db->db_addslashes($this->account->lastname),
				'passwd'	=> $this->db->db_addslashes($this->account->passwd_hash),
				'status'	=> $this->account->enabled ? 'A' : 'I', // this really has to become a bool
				'expires'	=> (int) $this->account->expires,
				'person_id'	=> (int) $this->account->person_id,
				'quota'		=> (int) $this->account->quota
			);

			$where_lid = '';
			//Sigurd 28 june 2010: condition on id should be enough
/*
			if ( $this->account->lid != $this->account->old_loginid )
			{
				$lid = $this->db->db_addslashes($this->account->old_loginid);
				$where_lid = " AND account_lid = '{$lid}'";
			}
*/
			$sql = 'UPDATE phpgw_accounts'
					. " SET account_lid = '{$data['lid']}', "
						. " account_firstname = '{$data['firstname']}', "
						. " account_lastname = '{$data['lastname']}', "
						. " account_pwd = '{$data['passwd']}', "
						. " account_status = '{$data['status']}', "
						. " account_expires = {$data['expires']}, "
						. " person_id = {$data['person_id']}, "
						. " account_quota = {$data['quota']}"
					. " WHERE account_id = {$data['id']}"
						. $where_lid;

			$this->_cache_account($this->account);

			return $this->db->query($sql, __LINE__, __FILE__);
		}

		/**
		* Match a contact ID with an account id
		*
		* @param integer $person_id the contact person ID
		*
		* @return integer account id - 0 if not found
		*/
		public function search_person($person_id)
		{
			static $person_list;

			$person_id = (int) $person_id;
			if ( !$person_id)
			{
				return 0;
			}

			if ( isset($person_list[$person_id]) )
			{
				return $person_list[$person_id];
			}

			$person_list[$person_id] = 0;

			$sql ="SELECT account_id FROM phpgw_accounts WHERE person_id = {$person_id}";
			$this->db->query($sql, __LINE__, __FILE__);
			if ( $this->db->next_record() )
			{
				$person_list[$person_id] = $this->db->f('account_id');
			}

			return $person_list[$person_id];
		}
	}
