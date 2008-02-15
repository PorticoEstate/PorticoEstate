<?php
	/**
	* Authentication based on Exchange 5.5
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on LDAP
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class auth_exchange extends auth_
	{
		/**
		*
		* ldap connection
		*/
		var $ldap;
		
		/**
		*
		* your windows domain
		*/
		var $domain = '';

		/**
		*
		* your exchange host
		*/
		var $host = '';

		function auth_exchange()
		{
			parent::auth();
			if(!$this->ldap = ldap_connect($this->host))
			{
				die('not connected');
				return false;
			}
		}
		
		function get_base_dn()
		{
			return 'DC='.$this->domain;
		}
		
		function transform_username($username)
		{
			return $username;
		}
		
		function authenticate($username, $passwd, $pwType)
		{
			if($pwType == 'none')
			{
				return true;
			}

			// empty pw will connect as anonymous user
			if (empty($passwd))
			{
				$passwd = crypt(microtime());
			}

			$passwd = stripslashes($passwd);

			/* Try to bind to the repository */
			if(@ldap_bind($this->ldap,
						  'cn='.$this->transform_username($username).','.$this->get_base_dn(),
						  $passwd
						 ))
			{
				return true;
			}

			return false;
		}

		function change_password($old_passwd, $new_passwd, $_account_id='') 
		{
			return false;
		}
	}
?>
