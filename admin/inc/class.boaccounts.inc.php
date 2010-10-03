<?php
	/**
	* Shared functions for other account repository managers and loader
	* @author coreteam <phpgroupware-developers@gnu.org>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/ GNU General Public License v2 or later
	* @package admin
	* @subpackage accounts
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU General Public License as published by
	   the Free Software Foundation, either version 2 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	//FIXME define constants for rights so we can fuck off all these magic numbers

	class admin_boaccounts
	{
		public $public_functions = array
		(
			'add_group'          => true,
			'delete_group'       => true,
			'delete_user'        => true,
			'edit_group'         => true,
			'save_user'          => true,
		);

		public $xml_functions = array();

		public $soap_functions = array
		(
			'add_user'	=> array
			(
				'in'	=> array('int','struct'),
				'out'	=> array()
			)
		);

		/**
		 * List methods available via RPC
		 *
		 * @param string $_type the RPC type - xmlrpc or soap for now
		 *
		 * @return array list of methods and their signatures
		 */
		public function list_methods($_type = 'xmlrpc')
		{
			/*
			  This handles introspection or discovery by the logged in client,
			  in which case the input might be an array.  The server always calls
			  this function to fill the server dispatch map using a string.
			*/
			if (is_array($_type))
			{
				$_type = $_type['type'] ? $_type['type'] : $_type[0];
			}
			switch($_type)
			{
				case 'xmlrpc':
					$xml_functions = array(
						'rpc_add_user' => array(
							'function'  => 'rpc_add_user',
							'signature' => array(array($GLOBALS['xmlrpcStruct'], $GLOBALS['xmlrpcStruct'])),
							'docstring' => lang('Add a new account.')
						),
						'list_methods' => array(
							'function'  => 'list_methods',
							'signature' => array(array($GLOBALS['xmlrpcStruct'], $GLOBALS['xmlrpcString'])),
							'docstring' => lang('Read this list of methods.')
						)
					);
					return $xml_functions;
					break;
				case 'soap':
					return $this->soap_functions;
					break;
				default:
					return array();
			}
		}

		/**
		 * Check that the user has the required access rights for the action
		 *
		 * @param string $action the type of access the user requires
		 * @param string $access the area the user is attempting to access
		 *
		 * @return boolean does the user have the rights?
		 */
		public function check_rights($action, $access = 'group_access')
		{
			$right = 0;
			// this is ugly
			switch($action)
			{
				case 'view':
					$right = phpgwapi_acl::DELETE;
					break;
				case 'add':
					$right = phpgwapi_acl::EDIT;
					break;
				case 'edit':
					$right = phpgwapi_acl::PRIV;
					break;
				case 'delete':
					$right = phpgwapi_acl::GROUP_MANAGERS;
					break;
				case 'search':
					$right = phpgwapi_acl::ADD;
					break;
			}

			// the test is inverted as admins have all rights unless removed
			$result = !$GLOBALS['phpgw']->acl->check($access, $right, 'admin');
			return $result;
		}

		/**
		 * Save a group record
		 *
		 * @param array &$values the data for the group
		 *
		 * @return null
		 *
		 * @throws Exception
		 */
		public function save_group(&$values)
		{
			if ( $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::EDIT, 'admin') )
			{
				$link_args = array('menuaction' => 'admin.uiaccounts.list_groups');
				$GLOBALS['phpgw']->redirect_link('/index.php', $link_args);
			}

			if ( $values['account_id'] == 0
					&& $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::ADD, 'admin') )
			{
				throw new Exception(lang('no permission to add groups'));
			}

			if ( !$values['account_name'] )
			{
				throw new Exception(lang('You must enter a group name.'));
			}

			$old_group = $GLOBALS['phpgw']->accounts->get($values['account_id']);

			if ( is_object($old_group)
				&& $values['account_name'] != $old_group->lid )
			{
				if ( $GLOBALS['phpgw']->accounts->exists($values['account_name']) )
				{
					throw new Exception(lang('Sorry, that group name has already been taken.'));
				}
			}

			if ( !$values['account_id'] )
			{
				$new_group = new phpgwapi_group();
			}
			else
			{
				$new_group = $GLOBALS['phpgw']->accounts->get($values['account_id']);
			}
			$new_group->lid			= $values['account_name'];
			$new_group->old_loginid = $old_group->lid;
			$new_group->firstname	= $values['account_name'];
			$new_group->lastname	= lang('group');
			$new_group->expires		= -1;
			$new_group->enabled		= true;

			$id = (int) $values['account_id'];
			if ( !$id ) // add new group?
			{
				$new_group->id = $id;
				$id = $GLOBALS['phpgw']->accounts->create($new_group, $values['account_user'],
														array(), array_keys($values['account_apps']));
			}
			else //edit group
			{
				$GLOBALS['phpgw']->accounts->update_group($new_group, $values['account_user'],
														$values['account_apps']);
			}

			//Delete cached menu for members of group
			$members = $GLOBALS['phpgw']->accounts->member($id);
			foreach($members as $entry)
			{
				phpgwapi_cache::user_clear('phpgwapi', 'menu', $entry['account_id']);
			}
			return $id;
		}

		/**
		* Saves a new user (account) or update an existing one
		*
		* @param array &$values Account details
		*
		* @return integer the account id - 0 = error
		*/
		function save_user(&$values)
		{
			if ( !is_array($values) )
			{
				throw new Exception(lang('Invalid data'));
			}

			if ( !(isset($values['id']) && $values['id'])
					&& $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::ADD, 'admin'))
			{
				throw new Exception(lang('no permission to add users'));
			}

	
			if ( $values['id'] )
			{
				$user = $GLOBALS['phpgw']->accounts->get($values['id']);
			}
			else
			{
				$user = new phpgwapi_user();
			}
			
			if ( isset($values['expires_never']) && $values['expires_never'] )
			{
				$values['expires'] = -1;
				$values['account_expires'] = $values['expires'];
			}
			else
			{
				$date_valid = checkdate($values['account_expires_month'],
							$values['account_expires_day'],
							$values['account_expires_year']);

				if ( !$date_valid )
				{
					throw new Exception(lang('You have entered an invalid expiration date'));
				}
				$values['expires'] =  mktime(2, 0, 0, $values['account_expires_month'],
										$values['account_expires_day'],
										$values['account_expires_year']);

				$values['account_expires'] = $values['expires'];
			}

			if ( !$user->old_loginid && !$values['passwd'] )
			{
				throw new Exception('You must enter a password');
			}

			if ( !$values['lid'] )
			{
				throw new Exception(lang('You must enter a loginid'));
			}

			if ( $user->old_loginid != $values['lid'] )
			{
				if ($GLOBALS['phpgw']->accounts->exists($values['lid']))
				{
					throw new Exception(lang('That loginid has already been taken'));
				}
			}

			if ( $values['passwd'] || $values['passwd_2'] )
			{
				if ( $values['passwd'] != $values['passwd_2'] )
				{
					throw new Exception(lang('The passwords don\'t match'));
				}
			}

			if ( !count($values['account_permissions'])
				&& !count($values['account_groups']) )
			{
				throw new Exception(lang('You must add at least 1 application or group to this account'));
			}

			$user_data = array
			(
				'id'				=> (int) $values['id'],
				'lid'				=> $values['lid'],
				'firstname'			=> $values['firstname'],
				'lastname'			=> $values['lastname'],
				'enabled'			=> isset($values['enabled']) ? $values['enabled'] : '',
				'expires'			=> $values['expires'],
				'quota'				=> $values['quota']
			);

			if ( $values['passwd'] )
			{
				$user_data['passwd'] = $values['passwd'];
			}

			if ( false /* ldap extended attribs here */ )
			{
				$user_data['homedirectory'] = $values['homedirectory'];
				$user_data['loginshell'] = $values['loginshell'];
			}

			$groups = $values['account_groups'];
			$acls = array();
			if ( isset($values['changepassword']) && $values['changepassword'] )
			{
				$acls[] = array
				(
					'appname' 	=> 'preferences',
					'location'	=> 'changepassword',
					'rights'	=> 1
				);
			}
			if ( isset($values['anonymous']) && $values['anonymous'] )
			{
				$acls[] = array
				(
					'appname' 	=> 'phpgwapi',
					'location'	=> 'anonymous',
					'rights'	=> 1
				);
			}

			$apps_admin = $values['account_permissions_admin'] ? array_keys($values['account_permissions_admin']) : array();
			foreach ($apps_admin as $app_admin)
			{
				$acls[] = array
				(
					'appname' 	=> $app_admin,
					'location'	=> 'admin',
					'rights'	=> phpgwapi_acl::ADD
				);			
			}

			$apps = $values['account_permissions'] ? array_keys($values['account_permissions']) : array();

			unset($values['account_groups'], $values['account_permissions'], $values['account_permissions_admin']);

			try
			{
				foreach ( $user_data as $key => $val )
				{
					$user->$key = $val;
				}
			}
			catch ( Exception $e )
			{
				throw $e;
			}

			if ( $user->id )
			{
				phpgwapi_cache::user_clear('phpgwapi', 'menu', $user->id);
			}

			if ( !$user->is_dirty() )
			{
				return $user->id;
			}

			if ( $user->id )
			{
				if ( $GLOBALS['phpgw']->accounts->update_user($user, $groups, $acls, $apps) )
				{
					return $user->id;
				}
			}
			else
			{
				return $GLOBALS['phpgw']->accounts->create($user, $groups, $acls, $apps);
				{
					return $user->id;
				}
			}
			return 0;
		}

		/**
		 * Delete a group account
		 *
		 * @param integer $group_id the group to delete
		 *
		 * @return boolean was the group deleted?
		 */
		function delete_group($group_id)
		{
			if ( $GLOBALS['phpgw']->acl->check('group_access', phpgwapi_acl::GROUP_MANAGERS, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('index.php',
						array('menuaction' => 'admin.uiaccounts.list_groups'));
			}
			return $GLOBALS['phpgw']->accounts->delete($group_id);
		}

		/**
		 * Delete a user account
		 *
		 * @param integer $id       the account to delete
		 * @param integer $newowner the account to transfer the records to
		 *
		 * @return boolean was the user deleted?
		 */
		function delete_user($id, $newowner)
		{
			if ( $GLOBALS['phpgw']->acl->check('account_access', phpgwapi_acl::GROUP_MANAGERS, 'admin') )
			{
				$GLOBALS['phpgw']->redirect_link('index.php',
						array('menuaction' => 'admin.uiaccounts.list_users'));
			}
			return $GLOBALS['phpgw']->accounts->delete($id);
		}


		/**
		 * Get a list of members of a group
		 *
		 * @param integer $account_id the group to lookup
		 *
		 * @return array list of account id which are members of the group
		 */
		function load_group_users($account_id)
		{
			$temp_user = $GLOBALS['phpgw']->accounts->members($account_id);
			if ( !$temp_user )
			{
				return array();
			}
			else
			{
				$group_user = $temp_user;
			}

			$account_user = Array();
			foreach ( array_keys($group_user) as $user )
			{
				$account_user[$user] = ' selected';
			}
			return $account_user;
		}

		/**
		 * Get the user ID of the managers of the addressbook
		 *
		 * @return array addressmaster ids
		 */
		function get_addressmaster_ids()
		{
			// FIXME this shouldn't be a magic number
			return $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster', 7, 'addressbook');
		}

		/**
		 * Get a list of apps for a group
		 *
		 * @param integer $account_id the group to lookup
		 *
		 * @return array list of applications the group has access to
		 */
		function load_apps($account_id)
		{
			$account_id = (int) $account_id;
			$account_apps = array();
			if ( $account_id )
			{
				$apps = createObject('phpgwapi.applications', $account_id)
								->read_account_specific();

				foreach ( $apps as $app )
				{
					$account_apps[$app['name']] = true;
				}
			}
			return $account_apps;
		}

		/**
		 * Clear the cached session data for the nominated user
		 *
		 * @param integer $id the id of the user's data to be removed
		 *
		 * @return null
		 *
		 * @todo make this more granular and use the new cache system
		 */
		function refresh_session_data($id)
		{
			// If the user is logged in, it will force a refresh of the session_info
			$GLOBALS['phpgw']->session->delete_cache($id);
		}

		/**
		 * Add a user via RPC - not currently used
		 *
		 * @param array $data the data for the user to create
		 *
		 * @return array new id or error message wrapped in an array
		 */
		function rpc_add_user($data)
		{
			exit;
			/*
			$errors = $this->validate_user($data);
			if ( !$errors )
			{
				$result = $this->so->add_user($data);
			}
			else
			{
				$result = $errors;
			}
			return $result;
			*/
		}
	}
