<?php
	/**
	* View and manipulate account records using SQL and replicate changes to LDAP
	*
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v3 or later
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/*
	   This program is free software: you can redistribute it and/or modify
	   it under the terms of the GNU Lesser General Public License as published by
	   the Free Software Foundation, either version 3 of the License, or
	   (at your option) any later version.

	   This program is distributed in the hope that it will be useful,
	   but WITHOUT ANY WARRANTY; without even the implied warranty of
	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	   GNU General Public License for more details.

	   You should have received a copy of the GNU Lesser General Public License
	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Include accounts_sql parent class
	*/
	include_once(PHPGW_API_INC . '/accounts/class.accounts_sql.inc.php');

	/**
	* View and manipulate handling user and group account records using SQL
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class accounts_SQLLDAP extends accounts_sql
	{
		var $ldap;
		
		function __construct($account_id = null, $account_type = null)
		{
			parent::__construct($account_id, $account_type);
			include_once(PHPGW_API_INC . '/accounts/class.accounts_ldap.inc.php');
			$this->ldap = new accounts_ldap($account_id, $account_type);
		}

		/**
		* Save/update account information to/in database
		*/
		function save_repository()
		{
			$this->ldap->data = $this->data;
			$this->ldap->save_repository();
			return parent::save_repository();
		}

		function delete($accountid = '')
		{
			$this->ldap->delete($accountid);
			return parent::delete($accountid);
		}

		function create($account_info,$default_prefs=True)
		{
			$this->ldap->create($account_info,$default_prefs);
			return parent::create($account_info,$default_prefs);
		}
	}
