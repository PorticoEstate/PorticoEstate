<?php
	/**
	 * Object Factory
	 *
	 * @author Dirk Schaller <dschaller@probusiness.de>
	 * @copyright Copyright (C) 2003 Free Software Foundation, Inc. http://www.fsf.org/
	 * @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	 * @package phpgwapi
	 * @subpackage application
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
	 * Object factory for phpgwapi
	 * 
	 * @package phpgwapi
	 * @subpackage application
	 */
	class phpgwapi_ofphpgwapi extends phpgwapi_object_factory
	{
		/**
		  * Load a class and include the class file if not done so already.
		  *
		  * @author mdean
		  * @author milosch
		  * @author (thanks to jengo and ralf)
		  * This function is used to create an instance of a class, and if the class file has not been included it will do so. 
		  * $GLOBALS['phpgw']->acl = createObject('phpgwapi.acl');
		  * @param $classname name of class
		  * @param $p1-$p16 class parameters (all optional)
		 */
		function CreateObject($class,
			$p1='_UNDEF_',$p2='_UNDEF_',$p3='_UNDEF_',$p4='_UNDEF_',
			$p5='_UNDEF_',$p6='_UNDEF_',$p7='_UNDEF_',$p8='_UNDEF_',
			$p9='_UNDEF_',$p10='_UNDEF_',$p11='_UNDEF_',$p12='_UNDEF_',
			$p13='_UNDEF_',$p14='_UNDEF_',$p15='_UNDEF_',$p16='_UNDEF_')
		{
			list($appname,$classname) = explode('.', $class, 2);
			switch($classname)
			{
				case 'auth':
					return ofphpgwapi::create_auth_object();
				
				case 'accounts':
					$account_id   = ($p1 != '_UNDEF_')? $p1 : null;
					$account_type = ($p2 != '_UNDEF_')? $p2 : null;
					return ofphpgwapi::create_account_object($account_id, $account_type);

				case 'mapping':
					$auth_info = ($p1 != '_UNDEF_')? $p1 : null;
					return ofphpgwapi::CreateMappingObject($auth_info);

				default:
					return parent::CreateObject($class, $p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8, $p9, $p10, $p11, $p12, $p13, $p14, $p15, $p16); 
			}
		}

		function create_auth_object()
		{
			require_once(PHPGW_API_INC . '/auth/class.auth_.inc.php');
			
			$auth_type = $GLOBALS['phpgw_info']['server']['auth_type'];
			switch($auth_type)
			{
				case 'sqlssl':
					require_once(PHPGW_API_INC . '/auth/class.auth_sql.inc.php');
					// fall through

				case 'ads':
				case 'exchange':
				case 'http':
				case 'ldap':
				case 'mail':
				case 'nis':
				case 'ntlm':
				case 'remoteuser':
				case 'sql':
					$classname = "auth_{$auth_type}";
					require_once(PHPGW_API_INC . "/auth/class.{$classname}.inc.php");

					$classname = "phpgwapi_{$classname}";
					return new $classname();

				default:
					require_once(PHPGW_API_INC . '/auth/class.auth_sql.inc.php');
					return new phpgwapi_auth_sql();
			}
		}
		
		function create_account_object($account_id, $account_type)
		{
			require_once(PHPGW_API_INC . "/accounts/class.accounts_.inc.php");
			
			$acct_type = strtolower($GLOBALS['phpgw_info']['server']['account_repository']);
			switch($acct_type)
			{
				case 'sqlldap':
					require_once(PHPGW_API_INC . "/accounts/class.accounts_sql.inc.php");
					//fall through

				case 'ldap':
					require_once(PHPGW_API_INC . "/aaccounts/class.accounts_ldap.inc.php");
					break;
				
				default:
					$acct_type = 'sql';
			}
			require_once(PHPGW_API_INC . "/accounts/class.accounts_{$acct_type}.inc.php");
			$acct_type = "phpgwapi_accounts_{$acct_type}";
			return new $acct_type($account_id, $account_type);
		}

		/**
		* Create a new mapping object
		*/
		function CreateMappingObject($auth_info)
		{
			require_once(PHPGW_API_INC.'/mapping/class.mapping_.inc.php');

			switch($GLOBALS['phpgw_info']['server']['account_repository'])
			{
				case 'ldap':
					require_once(PHPGW_API_INC. '/mapping/class.mapping_ldap.inc.php');
					return new mapping_ldap($auth_info);

				default:	
					require_once(PHPGW_API_INC. '/mapping/class.mapping_sql.inc.php');
					return new mapping_sql($auth_info);
			}
		}
	}
