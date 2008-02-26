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
	class phpgwapi_auth_exchange extends phpgwapi_auth_
	{
		/**
		* @var resource $ldap ldap connection
		*/
		var $ldap;
		
		/**
		* @var string $domain your windows domain
		*/
		var $domain = '';

		/**
		* @var string $host your exchange host
		*/
		var $host = '';

		public function __construct()
		{
			parent::__construct();
			if(!$this->ldap = ldap_connect($this->host))
			{
				die('not connected');
				return false;
			}
		}
		
		protected function get_base_dn()
		{
			return 'DC='.$this->domain;
		}
		
		protected function transform_username($username)
		{
			return $username;
		}
		
		public function authenticate($username, $passwd)
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
			return !! @ldap_bind($this->ldap, 'cn='.$this->transform_username($username).','.$this->get_base_dn(),
						  $passwd
						 ))
			{
				return true;
			}

			return false;
		}

		public function change_password($old_passwd, $new_passwd, $_account_id='') 
		{
			return false;
		}
	}
