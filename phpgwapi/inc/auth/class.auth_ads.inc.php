<?php
	/**
	* Authentication based on MS Active Directory Service
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.auth_ads.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	*/

	/**
	* Include M$ Exchange authentification
	*/
	include_once(PHPGW_API_INC . '/auth/class.auth_exchange.inc.php');

	/**
	* Authentication based on MS Active Directory Service
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class auth_ads extends auth_exchange
	{
		
		/**
		*
		* your ADS base DN
		*/
		var $ldap_base = ''; //'DC=pbgroup,DC=lan';

		/**
		*
		* your ads host
		*/
		var $host = ''; // example: '192.168.100.1';

		function auth_ads()
		{
			parent::auth_exchange();
		}
		
		function transform_username($username)
		{
			// see this code as an example
			ldap_bind($this->ldap,
					  'CN=admin,CN=Users,DC=pbgroup,DC=lan',
					  'password'
					 );
			$sr = ldap_search($this->ldap,
							  'CN=Users,DC=pbgroup,DC=lan',
							  'mailNickname='.$username,
							  array('cn')
							 );
			$entries = ldap_get_entries($this->ldap, $sr);
			return $entries[0]['cn'][0];
		}
		
		function get_base_dn()
		{
			return 'CN=Users,'.$this->ldap_base;
		}
	}
?>
