<?php
	/**
	 * phpGroupWare - Administration - ACL manager logic
	 *
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author Others <unknown>
	 * @copyright Copyright (C) 2007-2008 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgroupware
	 * @subpackage admin
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

	/*
	 * phpGroupWare - Administration - ACL manager logic
	 *
	 * @package phpgroupware
	 * @subpackage admin
	 */

	class admin_boaclmanager
	{
		/**
		 * Publicly available methods of the class
		 */
		public $public_functions = array
		(
			'submit' => true
		);

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct()
		{
			//i do nothing!
		}

		/**
		 * Handle the form submission
		 *
		 * @return void
		 */
		public function submit()
		{
			if ( phpgw::get_var('cancel', 'bool') )
			{
				return false;
			}

			$app = phpgw::get_var('acl_app', 'string');
			$account_id = phpgw::get_var('account_id', 'int');
			$location = phpgw::get_var('location', 'string');

			$total_rights = 0;
			$acl_rights = phpgw::get_var('acl_rights', 'int');
			if ( !is_array($acl_rights) )
			{
				return;
			}

			foreach ( $acl_rights as $rights )
			{
				$total_rights += $rights;
			}

			$GLOBALS['phpgw']->acl->add_repository($acl_app, $location, $account_id, $total_rights);
		}

		/**
		 * Get the list of "addressmasters" ids
		 *
		 * @return array list of addressmaster id
		 */
		public function get_addressmaster_ids()
		{
			return $GLOBALS['phpgw']->acl->get_ids_for_location('addressmaster', 7, 'addressbook');
		}

		/**
		 * Get a list of users who are address masters
		 *
		 * @return array list of addressmasters
		 */
		public function list_addressmasters()
		{
			$admins = $this->get_addressmaster_ids();
			//_debug_array($admins);

			$data = array();
			foreach ( $admins as $admin )
			{
				$acct = $GLOBALS['phpgw']->accounts->get($admin);

				if ( is_object($acct) )
				{
					$data[] = array
					(
						'account_id'	=> $acct->id,
						'lid'			=> $acct->lid,
						'firstname'		=> $acct->firstname,
						'lastname'		=> $acct->lastname
					);
				}
			}
			return $data;
		}

		/**
		 * Check the the values for the addressmasters are valid
		 *
		 * @param array $users  list of users to check
		 * @param array $groups list of groups to check
		 *
		 * @return array empty array if ok or list of errors if invalid
		 */
		public function check_values($users = array(), $groups = array())
		{
			$errors = array();
			if ( !count($users) && !count($groups) )
			{
				$errors[] = lang('please choose at least one addressmaster');
			}
			return $errors;
		}

		/**
		 * Update the list of addressmasters
		 *
		 * @param array $masters list of users who are addressmasters
		 * @param array $groups  list of groups who are addressmasters
		 *
		 * @return void
		 */
		public function edit_addressmasters($masters, $groups = array())
		{
			$GLOBALS['phpgw']->acl->delete_repository('addressbook', 'addressmaster', false);

			foreach ( $masters as $master )
			{
				$GLOBALS['phpgw']->acl->add_repository('addressbook', 'addressmaster', $master, 7);
			}

			foreach ( $groups as $group )
			{
				$GLOBALS['phpgw']->acl->add_repository('addressbook', 'addressmaster', $group, 7);
			}
		}
	}
