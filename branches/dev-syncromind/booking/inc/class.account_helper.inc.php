<?php

	class booking_account_helper
	{

		const ADMIN_GROUP = 'Admins';

		protected static $account_is_admin;
		protected static $current_account_lid;

		/**
		 * Returns the current user's account_id in the phpgw_accounts table
		 */
		public static function current_account_id()
		{
			return get_account_id();
		}

		/**
		 * Returns the current user's login name
		 */
		public function current_account_lid()
		{
			return $GLOBALS['phpgw_info']['user']['account_lid'];
		}

		/**
		 * Returns the current user's full name
		 */
		public function current_account_fullname()
		{
			return $GLOBALS['phpgw_info']['user']['fullname'];
		}

		public static function current_account_memberships()
		{
			return $GLOBALS['phpgw']->accounts->membership();
		}
		/* 		public static function current_account_member_of_admins()
		  {
		  if (!isset(self::$account_is_admin))
		  {
		  self::$account_is_admin = false;

		  $memberships = self::current_account_memberships();
		  while($memberships && list($index,$group_info) = each($memberships))
		  {
		  if ($group_info->lid == self::ADMIN_GROUP)
		  {
		  self::$account_is_admin = true;
		  break;
		  }
		  }
		  }

		  return self::$account_is_admin;
		  } */

		public static function current_account_member_of_admins()
		{
			if (!isset(self::$account_is_admin))
			{
				self::$account_is_admin = false;
				if ($GLOBALS['phpgw']->acl->check('run', phpgwapi_acl::READ, 'admin') || $GLOBALS['phpgw']->acl->check('admin', phpgwapi_acl::ADD, 'booking'))
				{
					self::$account_is_admin = true;
				}
			}

			return self::$account_is_admin;
		}
	}