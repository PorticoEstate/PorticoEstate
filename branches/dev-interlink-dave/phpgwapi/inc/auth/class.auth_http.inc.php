<?php
	/**
	* Authentication based on HTTP auth
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on HTTP auth
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class phpgwapi_auth_http extends phpgwapi_auth_
	{

		function __construct()
		{
			parent::__construct();
		}

		function authenticate($username, $passwd)
		{
			return isset($_SERVER['PHP_AUTH_USER']) && !!strlen($_SERVER['PHP_AUTH_USER']);
		}

		function change_password($old_passwd, $new_passwd)
		{
			return False;
		}

	}
