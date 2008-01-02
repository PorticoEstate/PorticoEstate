<?php
	/**
	* Authentication based on Apache
	* @author DANG Quang Vu <quang_vu.dang@int-evry.fr>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage auth
	* @version $Id: class.auth_remoteuser.inc.php 17321 2006-10-03 14:05:03Z Caeies $
	*/
	
	/**
	* By using an Apache authentication method, phpGroupware does not authenticate users internally 
	* in its accounts directory (LDAP, MySQL,...). Instead of that, it depends on the Apache session's 
	* environment variable REMOTE_USER
	*
	* Using with Single Sign-On(Shibboleth, CAS, ...)
	*/
	
	class auth_remoteuser extends auth_
	{
		
		function auth_remoteuser()
		{
			parent::auth();
		}
		
		function authenticate($username, $passwd, $passwd_type)
		{
			if(isset($_SERVER['REMOTE_USER']) && strlen($_SERVER['REMOTE_USER']) > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		
		function change_password($old_passwd, $new_passwd, $account_id = '')
		{
			return false;
		}

		function update_lastlogin($account_id, $ip)
		{
		}
	}
?>
