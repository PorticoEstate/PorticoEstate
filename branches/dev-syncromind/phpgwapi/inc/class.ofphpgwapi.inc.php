<?php
	/**
	 * Object Factory
	 *
	 * @author Dirk Schaller <dschaller@probusiness.de>
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @author mdean
	 * @author milosch
	 * @author (thanks to jengo and ralf)
	 * @copyright Copyright (C) 2003-2008 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Object factory
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	*/
	class phpgwapi_ofphpgwapi extends phpgwapi_object_factory
	{
		/**
		 * Instantiate a class
		 *
		 * @param string $class name of class
		 * @param mixed  $p1    paramater for constructor of class (optional)
		 * @param mixed  $p2    paramater for constructor of class (optional)
		 * @param mixed  $p3    paramater for constructor of class (optional)
		 * @param mixed  $p4    paramater for constructor of class (optional)
		 * @param mixed  $p5    paramater for constructor of class (optional)
		 * @param mixed  $p6    paramater for constructor of class (optional)
		 * @param mixed  $p7    paramater for constructor of class (optional)
		 * @param mixed  $p8    paramater for constructor of class (optional)
		 * @param mixed  $p9    paramater for constructor of class (optional)
		 * @param mixed  $p10   paramater for constructor of class (optional)
		 * @param mixed  $p11   paramater for constructor of class (optional)
		 * @param mixed  $p12   paramater for constructor of class (optional)
		 * @param mixed  $p13   paramater for constructor of class (optional)
		 * @param mixed  $p14   paramater for constructor of class (optional)
		 * @param mixed  $p15   paramater for constructor of class (optional)
		 * @param mixed  $p16   paramater for constructor of class (optional)
		 *
		 * @return object the instantiated class
		 */
		public static function createObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{
			list($appname, $classname) = explode('.', $class, 2);
			switch ( $classname )
			{
				case 'auth':
					return self::_create_auth_object();

				case 'accounts':
					$account_id   = ($p1 != '_UNDEF_')? $p1 : null;
					$account_type = ($p2 != '_UNDEF_')? $p2 : null;
					return self::_create_account_object($account_id, $account_type);

				case 'mapping':
					$auth_info = ($p1 != '_UNDEF_')? $p1 : null;
					return self::_create_mapping_object($auth_info);

				default:
					return parent::createObject($class, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8,
												$p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16);
			}
		}

		/**
		 * Create a new authentication object
		 *
		 * @return object new authentication object
		 */
		protected static function _create_auth_object()
		{
			include_once PHPGW_API_INC . '/auth/class.auth_.inc.php';

			$auth_type = $GLOBALS['phpgw_info']['server']['auth_type'];
			switch ( $auth_type )
			{
				case 'sqlssl':
					include_once PHPGW_API_INC . '/auth/class.auth_sql.inc.php';
					// fall through

				case 'ads':
				case 'exchange':
				case 'http':
				case 'ldap':
				case 'mail':
				// case 'nis': - doesn't currently work AFAIK - skwashd may08
				case 'customsso':
				case 'ntlm':
				case 'remoteuser':
				case 'sql':
					$class = "auth_{$auth_type}";
					include_once PHPGW_API_INC . "/auth/class.{$class}.inc.php";

					$class = "phpgwapi_{$class}";
					return new $class();

				default:
					include_once PHPGW_API_INC . '/auth/class.auth_sql.inc.php';
					return new phpgwapi_auth_sql();
			}
		}

		/**
		 * Create a new account object
		 *
		 * @param integer $account_id   the user account to use
		 * @param string  $account_type the type of account - group or user
		 *
		 * @return object account object
		 */
		protected static function _create_account_object($account_id, $account_type)
		{
			include_once PHPGW_API_INC . "/accounts/class.accounts_.inc.php";

			$acct_type = strtolower($GLOBALS['phpgw_info']['server']['account_repository']);
			switch ( $acct_type )
			{
				case 'sqlldap':
					include_once PHPGW_API_INC . "/accounts/class.accounts_sql.inc.php";
					//fall through

				case 'ldap':
					include_once PHPGW_API_INC . "/accounts/class.accounts_ldap.inc.php";
					break;

				default:
					$acct_type = 'sql';
			}
			include_once PHPGW_API_INC . "/accounts/class.accounts_{$acct_type}.inc.php";
			$acct_type = "phpgwapi_accounts_{$acct_type}";
			return new $acct_type($account_id, $account_type);
		}

		/**
		* Create a new mapping object
		*
		* @param string $auth_info the authentication system being used
		*
		* @return object new account mapping object
		*/
		protected static function _create_mapping_object($auth_info)
		{
			include_once PHPGW_API_INC.'/mapping/class.mapping_.inc.php';

			switch ( $GLOBALS['phpgw_info']['server']['account_repository'] )
			{
				case 'ldap':
					include_once PHPGW_API_INC. '/mapping/class.mapping_ldap.inc.php';
					return new mapping_ldap($auth_info);

				default:
					include_once PHPGW_API_INC. '/mapping/class.mapping_sql.inc.php';
					return new mapping_sql($auth_info);
			}
		}
	}
