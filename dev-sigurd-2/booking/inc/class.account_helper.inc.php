<?php
	class booking_account_helper {
		const ADMIN_GROUP = 'Admins';
		protected static $account_is_admin;
		
		public static function current_account_id()
		{
			return get_account_id();
		}
		
		public static function current_account_memberships()
		{
			return $GLOBALS['phpgw']->accounts->membership();
		}
		
		public static function current_account_member_of_admins()
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
		}
	}