<?php
	/**
	* Shared functions for other account repository managers and loader
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
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
		protected $account;

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
		 * @var int $total the number of records found in previous search
		 */
		public $total;

		/**
		 * @var array $xmlrpc_methods the methods of the class available via xmlrpc
		 */
		public $xmlrpc_methods = array();

		/**
		* Standard constructor for setting account_id
		*
		* This constructor sets the account id, if string is set, converts to id
		* @param integer $account_id Account id defaults to current account id
		* @param string $account_type Account type 'u': account; 'g' : group; defaults to current account type
		* @internal I might move this to the accounts_shared if it stays around
		*/
		function __construct($account_id = null, $account_type = null)
		{
			$this->db =& $GLOBALS['phpgw']->db;
			$this->like = $this->db->like;
			
			$this->set_account($account_id, $account_type);
		}

		/**
		 * Set the account id of the class
		 *
		 * @param int $account_id the id of the user/group
		 * @param string $account_type the type of account - 'user'/'group'
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
		 * Synchronises accounts with contacts
		 */
		public function sync_accounts_contacts()
		{
			$accounts = $this->get_account_without_contact();

			if(is_array($accounts))
			{
				$contacts = createObject('phpgwapi.contacts');
				
				foreach($accounts as $account)
				{
					//$this->get_account_name($account,$lid,$fname,$lname);
					if($account)
					{
						$this->account_id = $account;
						$user = $this->read_repository();
						$principal = array
						(
							'per_prefix'		=> '',
							'per_first_name'	=> $user->firstname,
							'per_last_name'		=> $user->lastname,
							'access'			=> 'public',
							'owner'				=> $GLOBALS['phpgw_info']['server']['addressmaster']
						);
						$contact_type = $contacts->search_contact_type('Persons');
						$user->person_id = $contacts->add_contact($contact_type, $principal);
						$this->update_data($user_account);
						$this->save_repository();
					}
				}
			}
		}

		/**
		 * Save the contact details for the associated user
		 *
		 * @param object $user phpgwapi_account_user object with information about the user.
		 * @return boolean was the contact created/edited?
		 */
		protected function _save_contact_for_user(&$user)
		{
			$primary = array
			(
				'owner'				=> $GLOBALS['phpgw_info']['server']['addressmaster'],
				'access'			=> 'public',
				'per_first_name'	=> $user->firstname,
				'per_last_name'		=> $user->lastname,
			);

			$contacts = createObject('phpgwapi.contacts');

			// does the user already exist in the addressbook?
			if ( $user->person_id && $contacts->exist_contact($user->person_id) )
			{
				return !!$contacts->edit_person($user->person_id, $primary);
			}

			$type = $contacts->search_contact_type('Persons');

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

			$user->person_id = $contacts->add_contact($type, $primary, $comms);
			return !!$user->person_id;
		}
		
		/**
		 * Save the contact details for the associated group
		 *
		 * @param object $user phpgwapi_account_group object with information about the group.
		 * @return boolean was the contact created/edited?
		 */
		protected function _save_contact_for_group(&$group)
		{
			$primary = array
			(
				'owner'		=> $GLOBALS['phpgw_info']['server']['addressmaster'],
				'access'	=> 'public',
				'org_name'	=> (string) $group
			);

			$contacts = createObject('phpgwapi.contacts');

			// does the user already exist in the addressbook?
			if ( $group->person_id && $group->exist_contact($group->person_id) )
			{
				return !!$contacts->edit_org($group->person_id, $primary);
			}

			$type = $contacts->search_contact_type('Organizations');

			$group->person_id = $contacts->add_contact($type, $primary);
			return !!$user->person_id;
		}
		
		/**
		 * Is the current account expired?
		 *
		 * @return bool has the account expired?
		 */
		function is_expired()
		{
			$expires = (int) $this->account->expires;
			return $expires <> -1 && $expires < time();
		}

		/**
		 * Read the currently selected account
		 *
		 * @return object the account
		 */
		function read()
		{
			if ( !is_object($this->account) )
			{
				$this->read_repository();
			}
			return $this->account();
		}

		/**
		 * Update the account data
		 *
		 * @internal does not write it to the storage backend
		 * @param array the account data
		 * @return object the account
		 */
		function update_data($data)
		{
			if ( $data['account_type'] == 'g' )
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
		* Get a list of groups the user is a member of
		*
		* @param int $account_id the user account to lookup
		* @return array the groups the user is a member of 
		* @internal return structure array(array('account_id' => id, 'account_name' => group name))
		*/
		abstract public function membership($account_id = 0);

		/**
		* Get a list of members of the group
		*
		* @param int $group_id the group to check
		* @return array list of members
		*/
		abstract public function member($group_id = 0);

		/**
		* Get a list of member account ids for a group
		*
		* @return arrray list of members of the current group
		*/
		abstract public function get_members($group_id = null);

		/**
		* Find the next available account_id
		*
		* @param string $account_type Account type 'u' : user; 'g' : group
		* @return integer New account id
		*/
		protected function get_nextid($account_type='u')
		{
			$min = $GLOBALS['phpgw_info']['server']['account_min_id'] ? $GLOBALS['phpgw_info']['server']['account_min_id'] : 0;
			$max = $GLOBALS['phpgw_info']['server']['account_max_id'] ? $GLOBALS['phpgw_info']['server']['account_max_id'] : 0;

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
			$free = 0;
			while (!$free)
			{
				$account_lid = '';
				//echo '<br />calling search for id: '.$nextid;
				if ( $this->exists($nextid) )
				{
					$nextid = (int) $GLOBALS['phpgw']->common->next_id($type, $min, $max);
				}
				else
				{
					$account_lid = $this->id2name($nextid);
					/* echo '<br />calling search for lid: '.$account_lid . '(from account_id=' . $nextid . ')'; */
					if ( $this->exists($account_lid) )
					{
						$nextid = (int) $GLOBALS['phpgw']->common->next_id($type,$min,$max);
					}
					else
					{
						$free = true;
					}
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
		* Get an array of users and groups seperated, including all members of groups, which i.e. have acl access for an application
		*
		* @param array $app_users Array with user/group names
		* @return array 'users' contains the user names for the given group or application
		*/
		function return_members($app_users = array() )
		{
			$users = array();
			$groups = array();

			foreach ( $app_users as $app_user )
			{
				$type = $GLOBALS['phpgw']->accounts->get_type($app_user);
				if($type == 'g')
				{
					$groups[$app_user] = true;

					$members = $this->get_members($app_user);
					if(is_array($memb))
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
		* Add an account to a group entry
		*
		* @param integer $account_id Account id
		* @param integer $group_id Group id
		* @return boolean true on success otherwise false
		*/
		abstract public function add_account2Group($account_id, $group_id);
		
		/**
		* Delete an account from a group
		*
		* @param integer $account_id Account id
		* @param integer $group_id Group id
		* @return boolean true on success otherwise false
		*/
		abstract public function delete_account4Group($account_id, $group_id);
		
		/**
		 * Create a account account
		 *
		 * @param object $account the new account object
		 * @param array $group group information 
		 *	- memberships for users / members for groups
		 * @param array $acls list of access controls to set for the user
		 * @param array $modules the list of modules to enable for the user
		 * @return integer the new account id
		 */
		public function create($account, $group, $acls = array(), $modules = array())
		{
			$this->db->transaction_begin();

			try
			{

				$class = get_class($account);
				switch( $class )
				{
					case 'phpgwapi_user':
						$this->_create_user($account, $group);
						break;

					case 'phpgwapi_group':
						$this->_create_group($account);
						break;

					default:
						throw new Exception("Invalid account type: {$class}");
				}

				if ( !$account->id )
				{
					throw new Exception('Invalid account id');
				}
				
				$aclobj = $GLOBALS['phpgw']->acl->set_account_id($account->id);
				foreach ( $acls as $acl )
				{
					$aclobj->add($acl['appname'], $acl['location'], $acl['rights']);
				}

				foreach ( $modules as $module )
				{
					$aclobj->add($module, 'run', phpgwapi_acl::READ);
				}
				$aclobj->save_repository();

			}
			catch (Exception $e)
			{
				$this->db->transaction_abort();
				// throw it again so it can be caught higher up
				throw $e;
			}

			$this->db->transaction_commit();
			return $account->id;
		}

		/**
		 * Handle the user specific parts of account creation
		 *
		 * @param object $user the phpgwapi_user to be stored
		 * @param array $groups the groups the user is to be a member of
		 * @return integer the id of the new user account
		 */
		protected function _create_user($user, $groups)
		{
			$this->_save_contact_for_user($user);
			if ( !$this->create_user_account($user) )
			{
				return false;
			}

			foreach ( $groups as $group )
			{
				$this->add_user2Group($user->id, $group);
			}
			return $user->id;
		}

		/**
		 * Handle the group specific parts of account creation
		 *
		 * @param object $group the phpgwapi_group to be stored
		 * @param array $members the users which are members of the group
		 * @return integere the id of the newly created group
		 */
		protected function _create_group($group, $members)
		{
			$this->_save_contact_for_group($group);
			if ( !$this->create_group_account($group) )
			{
				return false;
			}

			foreach ( $members as $group )
			{
				$this->add_user2Group($member, $group->id);
			}
			return $group->id;
		}

		function set_data($data)
		{
			$this->account = new phpgwapi_user();
			$this->account->init($data);
			return true;
		}
		
		function get_account_data($account_id)
		{
			$this->account_id = $account_id; // what is this good for? (get is not set)
			$this->read_repository();


			$data[$this->data['account_id']]['lid']       = $this->data['account_lid'];
			$data[$this->data['account_id']]['firstname'] = $this->data['firstname'];
			$data[$this->data['account_id']]['lastname']  = $this->data['lastname'];
			$data[$this->data['account_id']]['fullname']  = $this->data['fullname'];
			
			// type or account_type, this is the question
			if ( isset($this->data['account_type']) && strlen($this->data['account_type']) )
			{
				$data[$this->data['account_id']]['type'] =  $this->data['account_type'];
			}
			else if ( isset($this->data['type']) )
			{
				$data[$this->data['account_id']]['type'] = $this->data['type'];
			}
			else
			{
				$data[$this->data['account_id']]['type'] = 'u';
			}
			$data[$this->data['account_id']]['person_id'] = $this->data['person_id'];
			return $data;
		}

		/**
		* Create a non existing but authorized user 
		*
		* @param string $accountname User name
		* @param string $passwd User password
		* @param boolean $default_prefs Default preferences for this new user
		* @param boolean $default_acls Acls (modules) for this new user
		* @param integer $expiredate Expire date of this account. '-1' for never. Defaults to 'in 30 days'
		* @param char $account_status Status for new user. 'A' for active user.
		* @return integer Account id 
		*/
		function auto_add($accountname, $passwd, $default_prefs = false, $default_acls = false, $expiredate = 0, $account_status = 'A')
		{
			if ($expiredate)
			{
				$expires = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
			}
			else
			{
				if($GLOBALS['phpgw_info']['server']['auto_create_expire'])
				{
					if($GLOBALS['phpgw_info']['server']['auto_create_expire'] == 'never')
					{
						$expires = -1;
					}
					else
					{
						$expiredate = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire'];
						$expires   = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
					}
				}
				else
				{
					/* expire in 30 days by default */
					$expiredate = time() + ( ( 60 * 60 ) * (30 * 24) );
					$expires   = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
				}
			}

			$acct_info = array(
				'account_lid'       => $accountname,
				'account_type'      => 'u',
				'account_passwd'    => $passwd,
				'account_firstname' => '',
				'account_lastname'  => '',
				'account_status'    => $account_status,
				'account_expires'   => $expires,
				'person_id'         => 'NULL'
			);

			$this->db->transaction_begin();
			$accountid = $this->create($acct_info, $default_prefs);

			// FIXME this needs to be done via the acl class not direct db calls
			if ($default_acls == false)
			{
				$default_group_lid = intval($GLOBALS['phpgw_info']['server']['default_group_lid']);
				$default_group_id  = $this->name2id($default_group_lid);
				$defaultgroupid = $default_group_id ? $default_group_id : $this->name2id('Default');
				if ($defaultgroupid)
				{
					// FIXME need a method to handle this now
					$this->db->query('INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights)'
						. "VALUES('phpgw_group', " . $defaultgroupid . ', ' 
						.	intval($accountid) . ', 1'
						. ')',__LINE__,__FILE__);
					$this->db->query('INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights)'
						. "VALUES('preferences' , 'changepassword', " 
						.	intval($accountid) . ', 1'
						. ')',__LINE__,__FILE__);
				}
				else
				{
					// If they don't have a default group, they need some sort of permissions.
					// This generally doesn't / shouldn't happen, but will (jengo)
					$this->db->query("insert into phpgw_acl (acl_appname, acl_location, acl_account, acl_rights) values('preferences', 'changepassword', " . $accountid . ', 1)',__LINE__,__FILE__);

					$apps = Array(
						'addressbook',
						'calendar',
						'email',
						'notes',
						'todo',
						'phpwebhosting',
						'manual'
					);

					@reset($apps);
					while(list($key,$app) = each($apps))
					{
						$this->db->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights) VALUES ('" . $app . "', 'run', " . intval($accountid) . ', 1)',__LINE__,__FILE__);
					}
				}
			}
			$this->db->transaction_commit();
			return $accountid;
		}
		
	}
