<?php
	/**
	* Authentication based on SQL, with optional SSL authentication
	* @author Andreas 'Count' Kotes <count@flatline.de>
	* @copyright Copyright (C) 200x Andreas 'Count' Kotes <count@flatline.de>
	* @copyright Portions Copyright (C) 2004-2008 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Authentication based on SQL, with optional SSL authentication
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_sqlssl extends phpgwapi_auth_sql
	{

		/**
		* Constructor
		*/
		public function __construct()
		{
			parent::__construct();
		}

		/**
		* Authenticate a user
		*
		* @param string $username the login to authenticate
		* @param string $passwd the password supplied by the user
		* @return bool did the user authenticate?
		* @return bool did the user sucessfully authenticate
		*/
		public function authenticate($username, $passwd)
		{
			if ( isset($_SERVER['SSL_CLIENT_S_DN']) )
			{
				$username = $GLOBALS['phpgw']->db->db_addslashes($username);

				$sql = 'SELECT account_lid FROM phpgw_accounts'
					. " WHERE account_lid = '{$username}'"
						. " AND account_status = 'A'";
				$GLOBALS['phpgw']->db->query($sql, __LINE__, __FILE__);
				return $GLOBALS['phpgw']->db->next_record();
			}
			return parent::authenticate($username, $passwd);
		}
	}
