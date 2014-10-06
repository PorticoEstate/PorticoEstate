<?php
	/**
	* Authentication based on Exchange 5.5
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Portions Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
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
			return  @ldap_bind($this->ldap,
						'cn='.$this->transform_username($username).','.$this->get_base_dn(),
						$passwd);
		}

		public function change_password($old_passwd, $new_passwd, $_account_id='') 
		{
			return false;
		}
	}
