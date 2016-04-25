<?php
	/**
	 * View and manipulate account records using SQL and replicate changes to LDAP.
	 *
	 * @author Philipp Kamps <pkamps@probusiness.de>
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. fsf.org
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
	 * View and manipulate account records using SQL and replicate changes to LDAP.
	 *
	 * @package phpgroupware
	 * @subpackage phpgwapi
	 * @category accounts
	 */
	class phpgwapi_accounts_sqlldap extends phpgwapi_accounts_sql
	{
		/**
		  * @var object $ldap phpgwapi_accounts_ldap object for 
		  * replicating changes to LDAP.
		  */
		protected $ldap;
		
		/**
		 * Constructor
		 */
		public function __construct($account_id = null, $account_type = null)
		{
			$this->ldap = new phpgwapi_accounts_ldap($account_id, $account_type);
			parent::__construct($account_id, $account_type);
		}

		/**
		 * Save/update account information to database
		 */
		public function save_repository()
		{
			$this->ldap->data = $this->data;
			if ( parent::save_repository() )
			{
				return $this->ldap->save_repository();
			}
			return false;
		}

		/**
		 * Delete an account
		 *
		 * @param integer $account_id the account to delete
		 * @return boolean was the account deleted?
		 */
		public function delete($accountid)
		{
			if ( parent::delete($accountid) )
			{
				return $this->ldap->delete($accountid);
			}
			return false;
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
			if ( parent::create_group_account($account) )
			{
				$members = parent::member($account->id);
				return $this->ldap->_create_group($account, $members);
			}
			return false;
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
			if ( $account->id = parent::create_user_account($account) )
			{
				$groups = parent::membership($account->id);
				return $this->ldap->_create_user($account, $groups);
			}
			return false;
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
			if ( parent::add_account2Group($account_id, $group_id) )
			{
				return $this->ldap->add_account2Group($account_id, $group_id);
			}
			return false;
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
			if ( parent::delete_account4Group($account_id, $group_id) )
			{
				$this->ldap->delete_account4Group($account_id, $group_id);
			}
			return false;
		}
	}
