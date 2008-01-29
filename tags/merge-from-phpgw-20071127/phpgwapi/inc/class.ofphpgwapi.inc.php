<?php
	/**
	* Object Factory
	*
	* @author Dirk Schaller <dschaller@probusiness.de>
	* @copyright Copyright (C) 2003 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/gpl.html GNU General Public License
	* @package phpgwapi
	* @subpackage application
	* @version $Id: class.ofphpgwapi.inc.php 17789 2006-12-27 12:24:55Z skwashd $
	*/

	/**
	* Object factory for phpgwapi
	* 
	* @package phpgwapi
	* @subpackage application
	*/
	class ofphpgwapi extends object_factory
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
					return ofphpgwapi::CreateAuthObject();
				break;
				
				case 'accounts':
					$account_id   = ($p1 != '_UNDEF_')? $p1 : null;
					$account_type = ($p2 != '_UNDEF_')? $p2 : null;
					return ofphpgwapi::CreateAccountObject($account_id, $account_type);
				break;

				case 'sessions':
					return ofphpgwapi::CreateSessionObject();
				break;
				
				case 'mapping':
					$auth_info = ($p1 != '_UNDEF_')? $p1 : null;
					return ofphpgwapi::CreateMappingObject($auth_info);
				break;

				default:
					return parent::CreateObject($class,$p1,$p2,$p3,$p4,$p5,$p6,$p7,$p8,$p9,$p10,$p11,$p12,$p13,$p14,$p15,$p16); 
			}
		}

		function CreateAuthObject()
		{
			include_once(PHPGW_API_INC . '/auth/class.auth_.inc.php');
			
			switch($GLOBALS['phpgw_info']['server']['auth_type'])
			{
				case 'http':
				include_once(PHPGW_API_INC . '/auth/class.auth_http.inc.php');
				return new Auth_http();
				break;
				
				case 'ldap':
				include_once(PHPGW_API_INC . '/auth/class.auth_ldap.inc.php');
				return new Auth_ldap();
				break;

				case 'mail':
				include_once(PHPGW_API_INC . '/auth/class.auth_mail.inc.php');
				return new Auth_mail();
				break;

				case 'nis':
				include_once(PHPGW_API_INC . '/auth/class.auth_nis.inc.php');
				return new Auth_nis();
				break;

				case 'ntlm':
				include_once(PHPGW_API_INC . '/auth/class.auth_ntlm.inc.php');
				return new Auth_ntlm();
				break;

				case 'sqlssl':
				include_once(PHPGW_API_INC . '/auth/class.auth_sqlssl.inc.php');
				return new Auth_sqlssl();
				break;

				case 'exchange':
				include_once(PHPGW_API_INC . '/auth/class.auth_exchange.inc.php');
				return new Auth_exchange();
				break;

				case 'ads':
				include_once(PHPGW_API_INC . '/auth/class.auth_ads.inc.php');
				return new Auth_ads();
				break;

				case 'remoteuser':
				include_once(PHPGW_API_INC . '/auth/class.auth_remoteuser.inc.php');
				return new Auth_remoteuser();
				break;

				default:
				include_once(PHPGW_API_INC . '/auth/class.auth_sql.inc.php');
				return new Auth_sql();
			}
		}
		
		function CreateAccountObject($account_id, $account_type)
		{
			include_once(PHPGW_API_INC.'/accounts/class.accounts_.inc.php');
			
			switch($GLOBALS['phpgw_info']['server']['account_repository'])
			{
				case 'ldap':
				include_once(PHPGW_API_INC . '/accounts/class.accounts_ldap.inc.php');
				return new Accounts_LDAP($account_id, $account_type);
				break;
				
				case 'sqlldap':
				include_once(PHPGW_API_INC . '/accounts/class.accounts_SQLLDAP.inc.php');
				return new Accounts_SQLLDAP($account_id, $account_type);
				break;

				default:
				include_once(PHPGW_API_INC . '/accounts/class.accounts_sql.inc.php');
				return new Accounts_sql($account_id, $account_type);
			}
		}

		/**
		* Create a new mapping object
		*/
		
		function CreateMappingObject($auth_info)
		{
			include_once(PHPGW_API_INC.'/mapping/class.mapping_.inc.php');

			switch($GLOBALS['phpgw_info']['server']['account_repository'])
			{
				case 'ldap':
				include_once(PHPGW_API_INC. '/mapping/class.mapping_ldap.inc.php');
				return new mapping_ldap($auth_info);

				case 'sql':
				include_once(PHPGW_API_INC. '/mapping/class.mapping_sql.inc.php');
				return new mapping_sql($auth_info);

				default:
				die('Unknow mapping requested !');
			}
		}

		/**
		* Create a new session object
		*/
		function CreateSessionObject()
		{
			include_once(PHPGW_API_INC.'/sessions/class.sessions.inc.php');
			
			switch($GLOBALS['phpgw_info']['server']['sessions_type'])
			{
				case 'db':
					include_once(PHPGW_API_INC . '/sessions/class.sessions_db.inc.php');
					return new sessions_db();
				
				case 'php':
				case 'php4': //legacy, should be dropped
				default:
					include_once(PHPGW_API_INC . '/sessions/class.sessions_php.inc.php');
					return new sessions_php();
			}
		}
		
	}
?>
