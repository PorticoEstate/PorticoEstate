<?php
	/**
	* Shared functions for other account repository managers and loader
	*
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
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

	/*
	 * Import account data objects
	 */
	phpgw::import_class('phpgwapi.account');

	/**
	* Class for handling user and group accounts
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category accounts
	*/
	abstract class phpgwapi_accounts_
	{
		/**
		 * @var object $account the currently selected user object
		 */
		public $account;

		/**
		 * @var integer $account_id track the current account_id - which may not match the this->account->id
		 */
		protected $account_id = 0;

		/**
		 * @var object $db reference to the global database object
		 */
		protected $db;

		/**
		 * @var array $memberships groups users are members of
		 */
		protected $memberships = array();

		/**
		 * @var array $members groups users are members of
		 */
		protected $members = array();

		/**
		 * @var integer $total the number of records found in previous search
		 */
		public $total;

		/**
		 * @var array $xmlrpc_methods the methods of the class available via xmlrpc
		 */
		public $xmlrpc_methods = array();

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
			$this->db =& $GLOBALS['phpgw']->db;
			$this->like = $this->db->like;
			$this->join = $this->db->join;

			$this->set_account($account_id, $account_type);
		}

		public function get_id()
		{
			return $this->account_id;
		}

		/**
		* Add an account to a group entry
		*
		* @param integer $account_id Account id
		* @param integer $group_id   Group id
		*
		* @return boolean true on success otherwise false
		*/
		abstract public function add_user2group($account_id, $group_id);

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
		abstract public function create_user_account($account);

		/**
		 * Delete an account
		 *
		 * @param integer $account_id the account to delete
		 *
		 * @return boolean was the account deleted?
		 */
		abstract public function delete($account_id);

		/**
		* Delete an account from a group
		*
		* @param integer $account_id The account to delete from the group
		* @param integer $group_id   The group to delete the account from
		*
		* @return boolean true on success otherwise false
		*/
		abstract public function delete_account4group($account_id, $group_id);

		/**
		 * Does the user account exist?
		 *
		 * @param integer|string $account_lid the accound login or id to check
		 *
		 * @return boolean does the account exist or not?
		 */
		abstract public function exists($account_lid);

		/**
		 * Fetch an account
		 *
		 * @param integer $id the account id to fetch
		 *
		 * @return object the account as a phpgw_account derived object
		 */
		abstract public function get($id);

		/**
		* Get a list of member account ids for a group
		*
		* @param integer $group_id the group to get members from
		*
		* @return arrray list of members of the current group
		*/
		abstract public function get_members($group_id = null);

		/**
		* Get a list of groups the user is a member of
		*
		* @param integer $account_id the user account to lookup
		*
		* @return array the groups the user is a member of
		*
		* @internal return structure array(array('account_id' => id, 'account_name' => group name))
		*/
		abstract public function membership($account_id = 0);

		/**
		* Get a list of members of the group
		*
		* @param integer $group_id the group to check
		*
		* @return array list of members
		*/
		abstract public function member($group_id = 0);

		/**
		 * Get a list of accounts which have contacts linked to them
		 *
		 * @return array account_id => contact_id mappings
		 */
		abstract public function get_account_with_contact();

		/**
		 * Get a list of accounts which don't have contacts associated with them
		 *
		 * @return array list of account_ids without contacts
		 */
		abstract public function get_account_without_contact();

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
		abstract public function get_list($_type='both', $start = -1, $sort = '',
											$order = '', $query = '', $offset = -1, $filter = array());

		/**
		 * Convert an account login id to an account id
		 *
		 * @param string $account_lid the login id to look up
		 *
		 * @return integer the account id - 0 if not found
		 */
		abstract public function name2id($account_lid);

		/**
		 * Read the currently selected account from the storage repository
		 *
		 * @return void
		 */
		abstract public function read_repository();

		/**
		 * Save/update account information to database
		 *
		 * @return void
		 */
		abstract public function save_repository();

		/**
		 * Match a contact ID with an account id
		 *
		 * @param integer $person_id the contact person ID
		 *
		 * @return integer account id - 0 if not found
		 */
		abstract public function search_person($person_id);

		/**
		* Create a non existing but authorized user
		*
		* @param string  $accountname    User name
		* @param string  $passwd         User password
		* @param integer $expiredate     Expire date of this account. '-1' for never.
		* @param string  $account_status Status for new user. 'A' for active user.
		*
		* @return integer Account id
		*/
		public function auto_add($accountname, $passwd, $expiredate = 0, $account_status = 'A')
		{
			if ($expiredate)
			{
				$expires = (int) $expiredate;
			}
			else if ( isset($GLOBALS['phpgw_info']['server']['auto_create_expire']) )
			{
				if ( $GLOBALS['phpgw_info']['server']['auto_create_expire'] == 'never' )
				{
					$expires = -1;
				}
				else
				{
					$expires = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire'];
				}
			}
			else
			{
				$expires = time() + (60 * 60 * 24 * 7); // 1 week - sane default
			}

			$acct_info = array
			(
				'account_lid'       => $accountname,
				'account_type'      => 'u',
				'account_passwd'    => $passwd,
				'account_firstname' => '',
				'account_lastname'  => '',
				'account_status'    => $account_status == 'A',
				'account_expires'   => $expires
			);

			$group = array
			(
				$this->name2id($GLOBALS['phpgw_info']['server']['default_group_lid'])
			);

			$account = $this->create($acct_info, $group);
			return $account->id;
		}

		/**
		 * Create a account account
		 *
		 * @param object $account the new account object
		 * @param array  $group   group information
		 *	- memberships for users / members for groups
		 * @param array  $acls    list of access controls to set for the user
		 * @param array  $modules the list of modules to enable for the user
		 * @param array  $contact_data for related contact in the addressbook
		 *
		 * @return integer the new account id
		 */
		public function create($account, $group, $acls = array(), $modules = array(),$contact_data = array())
		{
		// FIXME: Conflicting transactions - there is a transaction in acl::save_repository()
		//	$this->db->transaction_begin();
			try
			{
				$class = get_class($account);
				switch( $class )
				{
					case phpgwapi_account::CLASS_TYPE_USER:
						$this->_create_user($account, $group, $contact_data);
						break;

					case phpgwapi_account::CLASS_TYPE_GROUP:
						$this->_create_group($account, $group);
						break;

					default:
						throw new Exception("Invalid account type: {$class}");
				}

				if ( !$account->id )
				{
					throw new Exception('Failed to create account');
				}

				$this->_cache_account($account);

				$aclobj =& $GLOBALS['phpgw']->acl;
				$aclobj->set_account_id($account->id, true);
				foreach ( $acls as $acl )
				{
					$aclobj->add($acl['appname'], $acl['location'], $acl['rights']);
				}

				// application permissions
				foreach ( $modules as $module)
				{
					$aclobj->add($module, 'run', phpgwapi_acl::READ);
				}

				$aclobj->save_repository();
			}
			catch (Exception $e)
			{
		//		$this->db->transaction_abort();
				// throw it again so it can be caught higher up
				throw $e;
			}

		//	$this->db->transaction_commit();
			return $account->id;
		}

		/**
		 * Check which type of account the user id is
		 *
		 * @param integer $account_id the account id to look up
		 *
		 * @return string 'u' = user, 'g' = group
		 *
		 * @throws Exception invalid account id
		 */
		public function get_type($account_id)
		{
			if ( !is_numeric($account_id) )
			{
				$account_id = $this->name2id($account_id);
				trigger_error('Invalid account id specified in call to accounts::get_type', E_USER_NOTICE);
			}

			$account = $this->get($account_id);
			if ( !is_object($account) )
			{
				throw new Exception('Invalid account id specified');
			}

			switch ( get_class($account) )
			{
				case phpgwapi_account::CLASS_TYPE_USER:
					return phpgwapi_account::TYPE_USER;

				case phpgwapi_account::CLASS_TYPE_GROUP:
					return phpgwapi_account::TYPE_GROUP;

				default:
					throw new Exception('Invalid account type');
			}
		}

		/**
		 * Convert an account id to an account login id
		 *
		 * Generally self::id2name should be used as this exposes login information,
		 * which is a potential security risk
		 *
		 * @param integer $account_id the account_id to convert to login id
		 *
		 * @return string the account login id - empty if not found
		 */
		public function id2lid($account_id)
		{
			$acct = $this->get($account_id);
			if ( is_object($acct) )
			{
				return $acct->lid;
			}
			return '';
		}

		/**
		 * Convert an id into its corresponding account or group name
		 *
		 * @param integer $account_id Account or group id
		 *
		 * @return string Name of the account or the group when found othwerwise empty string
		 */
		public function id2name($account_id)
		{
			return (string) $this->get($account_id);
		}

		/**
		 * Is the current account expired?
		 *
		 * @return boolean has the account expired?
		 */
		public function is_expired()
		{
			return $this->account->is_expired();
		}

		/**
		 * Read the currently selected account
		 *
		 * @return object the account
		 */
		public function read()
		{
			if ( !is_object($this->account)
				|| $this->account->id != $this->account_id )
			{
				$this->read_repository();
			}
			return $this->account;
		}

		/**
		* Get an array of users and groups seperated, including all members of groups
		*
		* @param array $app_users Array with user/group names
		*
		* @return array 'users' contains the user names for the given group or application
		*/
		public function return_members($app_users = array() )
		{
			$users = array();
			$groups = array();

			if ( !is_array($app_users) )
			{
				return array
				(
					'groups'	=> array(),
					'users'		=> array()
				);
			}

			foreach ( $app_users as $app_user )
			{
				try
				{
					$type = $GLOBALS['phpgw']->accounts->get_type($app_user);
				}
				catch ( Exception $e )
				{
					// we ignore invalid accounts, this avoid problems with old crud
				}

				if ( $type == phpgwapi_account::TYPE_GROUP )
				{
					$groups[$app_user] = true;

					$members = $this->get_members($app_user);
					if ( is_array($members) )
					{
						foreach ( $members as $member )
						{
							$users[$member] = true;
						}
					}
				}
				else
				{
					$users[$app_user] = true;
				}
			}

			return array
			(
				'groups'	=> array_keys($groups),
				'users'		=> array_keys($users)
			);
		}

		/**
		 * Set the account id of the class
		 *
		 * @param integer $account_id   the id of the user/group
		 * @param string  $account_type the type of account - 'user'/'group'
		 *
		 * @return void
		 */
		public function set_account($account_id = null, $account_type = null)
		{
			if ( !is_null($account_id) )
			{
				$this->account_id = get_account_id($account_id);
			}

			if( !is_null($account_type))
			{
				$this->account_type = $account_type;
			}
		}

		/**
		 * Set data for the current account
		 *
		 * @param array $data the user data
		 *
		 * @return boolean was the data set properly
		 */
		public function set_data($data)
		{
			$this->account = new phpgwapi_user();
			try
			{
				$this->account->init($data);
			}
			catch ( Exception $e )
			{
				throw $e;
			}
			return true;
		}

		/**
		 * Synchronises accounts with contacts
		 *
		 * @return void
		 */
		public function sync_accounts_contacts()
		{
			$accounts = $this->get_account_without_contact();

			if ( !is_array($accounts) )
			{
				return;
			}
			$contacts = createObject('phpgwapi.contacts');

			if ( !isset($GLOBALS['phpgw_info']['server']['addressmaster']) )
			{
				$GLOBALS['phpgw_info']['server']['addressmaster'] = -3;
			}

			foreach($accounts as $account)
			{
				if ( $account )
				{
					$GLOBALS['phpgw']->db->transaction_begin();
					$this->account_id = $account;
					$user = $this->read_repository();
					$comms = array();

					switch ( $user->type )
					{
						case phpgwapi_account::TYPE_USER:
							$primary = array
							(
								'per_prefix'		=> '',
								'per_first_name'	=> $user->firstname,
								'per_last_name'		=> $user->lastname,
								'access'			=> 'public',
								'owner'		=> $GLOBALS['phpgw_info']['server']['addressmaster']
							);
							$type = $contacts->search_contact_type('Persons');

							$domain = '';
							if ( isset($GLOBALS['phpgw_info']['server']['mail_server']) )
							{
								$domain = $GLOBALS['phpgw_info']['server']['mail_server'];
							}

							if ( $domain )
							{
								$comm = array
								(
									'comm_descr'		=> $contacts->search_comm_descr('work email'),
									'comm_data'			=> "{$user->lid}@{$domain}",
									'comm_preferred'	=> 'Y'
								);
								$comms = array($comm);
							}

							break;

						case phpgwapi_account::TYPE_GROUP:
							$primary = array
							(
								'owner'		=> $GLOBALS['phpgw_info']['server']['addressmaster'],
								'access'	=> 'public',
								'org_name'	=> (string) $user
							);
							$type = $contacts->search_contact_type('Organizations');
							break;
						default:
							throw new Exception('Invalid account type');
					}

					$user->person_id = $contacts->add_contact($type, $primary, $comms);

					$this->account = $user;
					if($this->save_repository())
					{
						$GLOBALS['phpgw']->db->transaction_commit();
					}
				}
			}
		}

		/**
		 * Update the data for a group
		 *
		 * @param object $group   the phpgwapi_account_group object to use for the update
		 * @param array  $modules the list of modules the group shall have access to
		 *
		 * @return integer the group id
		 */
		public function update_group($group, $modules = null)
		{
			$this->account = $group;
			$this->account_id = $group->id;
			$this->save_repository();

			// module permissions
			if ( is_array($modules) )
			{
				$apps = createObject('phpgwapi.applications', $group->id);
				$apps->update_data(array_keys($modules));
				$apps->save_repository();
			}

			// FIXME This is broken and only supports localFS VFS
			if ( $group->old_loginid != $group->lid )
			{
				$basedir = "{$GLOBALS['phpgw_info']['server']['files_dir']}/groups/";
				@rename("{$basedir}{$group->old_loginid}", "{$basedir}/{$group->lid}");
			}

			$GLOBALS['hook_values'] = array
			(
				'account_id'	=> $group->id,
				'account_lid'	=> $group->lid,
			);

			$GLOBALS['phpgw']->hooks->process('editgroup');

			return $group->id;
		}

		/**
		 * Update an existing user account record
		 *
		 * @param object $user        the phpgw_account object to store
		 * @param array  $groups      the groups the user should be a member of
		 * @param array  $permissions ACLs to set for the user
		 * @param array  $modules     the modules the user has access to
		 *
		 * @return void
		 */
		public function update_user($user, $groups, $acls = array(), $modules = null)
		{
			$this->set_account($user->id);
			$this->account = $user;
			$this->save_repository();

			$this->_cache_account($user);

			// handle groups
			$old_groups = array_keys($this->membership($user->id));
			$new_groups = $groups;
			$drop_groups = array_diff($old_groups, $new_groups);

			if ( is_array($drop_groups) && count($drop_groups) )
			{
				foreach ( $drop_groups as $group )
				{
					$this->delete_account4group($user->id, $group);
				}
			}
			unset($old_groups, $groups, $drop_groups);

			foreach ( $new_groups as $group )
			{
				$this->add_user2group($user->id, $group);
			}

			//FIXME need permissions here

			$aclobj =& $GLOBALS['phpgw']->acl;
			$aclobj->set_account_id($user->id, true);
			$aclobj->clear_user_cache($user->id);
			foreach ($GLOBALS['phpgw_info']['apps'] as $app => $dummy)
			{
				if($app == 'phpgwapi')
				{
					continue;
				}
				$aclobj->delete_repository($app, 'admin', $user->id);			
			}

			$aclobj->delete_repository('preferences', 'changepassword', $user->id);
			$aclobj->delete_repository('phpgwapi', 'anonymous', $user->id);
			$aclobj->set_account_id($user->id, true); //reread the current repository
			foreach ( $acls as $acl )
			{
				$aclobj->add($acl['appname'], $acl['location'], $acl['rights']);
			}

			$aclobj->save_repository();

			// application permissions
			if ( is_array($modules) )
			{
				$apps = createObject('phpgwapi.applications', $user->id);
				$apps->update_data($modules);
				$apps->save_repository();
			}

			$GLOBALS['hook_values'] = array
			(
				'account_id'	=> $user->id,
				'account_lid'	=> $user->lid,
				'new_passwd'	=> $user->passwd
			);

			$GLOBALS['phpgw']->hooks->process('editaccount');

			return true;
		}

		/**
		 * Update the account data
		 *
		 * @param array $data the account data to use
		 *
		 * @return object the account
		 *
		 * @internal does not write it to the storage backend
		 */
		public function update_data($data)
		{
			if ( $this->get_type($data->id) == 'g' )
			{
				$account = new phpgwapi_group();
			}
			else
			{
				$account = new phpgwapi_user();
			}
			$account->init($data);
			$this->account = $account;
			return $this->account;
		}

		/**
		 * Cache an account object in the system cache
		 *
		 * @param object $account phpgw_account object to cache
		 *
		 * @return void
		 */
		protected function _cache_account($account)
		{
			phpgwapi_cache::system_set('phpgwapi', "account_{$account->id}", $account);
		}

		/**
		 * Handle the group specific parts of account creation
		 *
		 * @param object $group   the phpgwapi_group to be stored
		 * @param array  $members the users which are members of the group
		 *
		 * @return integer the id of the newly created group
		 */
		protected function _create_group($group, $members)
		{
			$this->_save_contact_for_group($group);
			if ( !$this->create_group_account($group) )
			{
				return false;
			}

			foreach ( $members as $member )
			{
				$this->add_user2Group($member, $group->id);
			}

			$GLOBALS['hook_values'] = array
			(
				'account_id'	=> $group->id,
				'account_lid'	=> $group->lid,
			);

			$GLOBALS['phpgw']->hooks->process('addgroup');

			return $group->id;
		}

		/**
		 * Handle the user specific parts of account creation
		 *
		 * @param object $user   the phpgwapi_user object to be stored
		 * @param array  $groups the groups the user is to be a member of
		 *
		 * @return integer the id of the new user account
		 */
		protected function _create_user($user, $groups, $contact_data = array())
		{
			$this->_save_contact_for_user($user, $contact_data);
			if ( !$this->create_user_account($user) )
			{
				return false;
			}

			foreach ( $groups as $group )
			{
				$this->add_user2Group($user->id, $group);
			}

			// preferences - this is ugly - but the def_pref hook is ugly too
			$GLOBALS['hook_values'] = array
			(
				'account_id'	=> $user->id,
				'account_lid'	=> $user->lid,
				'new_passwd'	=> $user->passwd
			);
			$GLOBALS['pref'] = CreateObject('phpgwapi.preferences', $user->id);
			$GLOBALS['phpgw']->hooks->process('addaccount');
			$GLOBALS['phpgw']->hooks->process('add_def_pref');
			$GLOBALS['pref']->save_repository(false);

			return $user->id;
		}

		/**
		* Find the next available account_id
		*
		* @param string $account_type Account type 'u' : user; 'g' : group
		*
		* @return integer New account id
		*/
		protected function _get_nextid($account_type='u')
		{

			$min = isset($GLOBALS['phpgw_info']['server']['account_min_id']) ? (int) $GLOBALS['phpgw_info']['server']['account_min_id'] : 0;

			$max = isset($GLOBALS['phpgw_info']['server']['account_max_id']) ? (int) $GLOBALS['phpgw_info']['server']['account_max_id'] : 2147483647;

			if ($account_type == 'g')
			{
				$type = 'groups';
			}
			else
			{
				$type = 'accounts';
			}

			$nextid = (int) $GLOBALS['phpgw']->common->last_id($type, $min, $max);

			/* Loop until we find a free id */
			$free = false;
			while ( !$free )
			{
				$account_lid = '';
				//echo '<br />calling search for id: '.$nextid;
				if ( $this->exists($nextid) )
				{
					$nextid = (int) $GLOBALS['phpgw']->common->next_id($type, $min, $max);
				}
				else
				{
					break;
				}
			}

			if	( $GLOBALS['phpgw_info']['server']['account_max_id'] &&
				$GLOBALS['phpgw_info']['server']['account_max_id'] < $nextid )
			{
				return false;
			}
			/* echo '<br />using'.$nextid;exit; */
			return $nextid;
		}

		/**
		 * Save the contact details for the associated group
		 *
		 * @param object &$group phpgwapi_account_group object with information about the group.
		 *
		 * @return boolean was the contact created/edited?
		 */
		protected function _save_contact_for_group(&$group)
		{
			if ( !isset($GLOBALS['phpgw_info']['server']['addressmaster']) )
			{
				$GLOBALS['phpgw_info']['server']['addressmaster'] = -3;
			}
			$primary = array
			(
				'owner'		=> $GLOBALS['phpgw_info']['server']['addressmaster'],
				'access'	=> 'public',
				'org_name'	=> (string) $group
			);

			$contacts = createObject('phpgwapi.contacts');

			// does the user already exist in the addressbook?
			if ( $group->person_id && $contacts->exist_contact($group->person_id) )
			{
				return !!$contacts->edit_org($group->person_id, $primary);
			}

			$type = $contacts->search_contact_type('Organizations');

			$group->person_id = $contacts->add_contact($type, $primary);
			return !!$group->person_id;
		}

		/**
		 * Save the contact details for the associated user
		 *
		 * @param object &$user phpgwapi_account_user object with information about the user.
		 *
		 * @return boolean was the contact created/edited?
		 */
		protected function _save_contact_for_user(&$user,$contact_data)
		{
			if(isset($contact_data['primary']) && $contact_data['primary'])
			{
				$primary = $contact_data['primary'];
			}
			else
			{
				$primary = array
				(
					'owner'				=> $GLOBALS['phpgw_info']['server']['addressmaster'],
					'access'			=> 'public',
					'per_first_name'	=> $user->firstname,
					'per_last_name'		=> $user->lastname,
				);
			}

			$contacts = createObject('phpgwapi.contacts');

			// does the user already exist in the addressbook?
			if ( $user->person_id && $contacts->exist_contact($user->person_id) )
			{
				return !!$contacts->edit_person($user->person_id, $primary);
			}

			$type = $contacts->search_contact_type('Persons');

			if(isset($contact_data['comms']) && $contact_data['comms'])
			{
				$comms = $contact_data['comms'];
			}
			else
			{

				$comms = array();
				$domain = '';
				if ( isset($GLOBALS['phpgw_info']['server']['mail_server']) )
				{
					$domain = $GLOBALS['phpgw_info']['server']['mail_server'];
				}

				if ( $domain )
				{
					$comm = array
					(
						'comm_descr'		=> $contacts->search_comm_descr('work email'),
						'comm_data'			=> "{$user->lid}@{$domain}",
						'comm_preferred'	=> 'Y'
					);
					$comms = array($comm);
				}
			}

			$locations = isset($contact_data['locations']) && $contact_data['locations'] ? $contact_data['locations'] : array();

			$user->person_id = $contacts->add_contact($type, $primary, $comms, $locations);

			return !!$user->person_id;
		}
	}
