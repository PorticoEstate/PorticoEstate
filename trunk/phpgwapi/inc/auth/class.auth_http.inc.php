<?php
	/**
	* Authentication based on HTTP auth
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.auth_http.inc.php 15793 2005-03-22 14:53:43Z fipsfuchs $
	*/

	/**
	* Authentication based on HTTP auth
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class auth_http extends auth_
	{

		function auth_http()
		{
			parent::auth();
		}

		function authenticate($username, $passwd)
		{
			if (isset($GLOBALS['PHP_AUTH_USER']))
			{
				return True;
			}
			else
			{
				return False;
			}
		}

		function change_password($old_passwd, $new_passwd)
		{
			return False;
		}

	}
?>
