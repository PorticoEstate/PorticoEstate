<?php
	/**
	* Authentication based on Apache
	* @author DANG Quang Vu <quang_vu.dang@int-evry.fr>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage auth
	* @version $Id$
	*/
	
	/**
	* By using an Apache authentication method, phpGroupware does not authenticate users internally 
	* in its accounts directory (LDAP, MySQL,...). Instead of that, it depends on the Apache session's 
	* environment variable REMOTE_USER
	*
	* Using with Single Sign-On(Shibboleth, CAS, ...)
	*/
	
	class phpgwapi_auth_remoteuser extends phpgwapi_auth_
	{
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function authenticate($username, $passwd)
		{
			return isset($_SERVER['REMOTE_USER']) && !!strlen($_SERVER['REMOTE_USER']);
		}
		
		public function change_password($old_passwd, $new_passwd, $account_id = '')
		{
			return false;
		}

		public function update_lastlogin($account_id, $ip)
		{
			return '';
		}
	}
