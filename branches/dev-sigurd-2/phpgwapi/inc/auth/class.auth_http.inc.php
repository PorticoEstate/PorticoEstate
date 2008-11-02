<?php
	/**
	* Authentication based on HTTP auth
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc http://www.fsf.org/
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
