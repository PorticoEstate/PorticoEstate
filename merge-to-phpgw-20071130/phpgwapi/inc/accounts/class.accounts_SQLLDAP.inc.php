<?php
	/**
	* View and manipulate account records using SQL
	* add copy is been mirrored to LDAP
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.accounts_SQLLDAP.inc.php 15869 2005-04-26 06:45:23Z powerstat $
	*/

	/**
	* Include accounts_ parent class
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
		var $LDAPRepository;
		
		function accounts_SQLLDAP($account_id = null, $account_type = null)
		{
			parent::accounts_sql($account_id, $account_type);
			include_once(PHPGW_API_INC . '/accounts/class.accounts_ldap.inc.php');
			$this->LDAPRepository = new Accounts_LDAP($account_id, $account_type);
		}

		/**
		* Save/update account information to/in database
		*/
		function save_repository()
		{
			$this->LDAPRepository->data = $this->data;
			$this->LDAPRepository->save_repository();
			return parent::save_repository();
		}

		function delete($accountid = '')
		{
			$this->LDAPRepository->delete($accountid);
			return parent::delete($accountid);
		}

		function create($account_info,$default_prefs=True)
		{
			$this->LDAPRepository->create($account_info,$default_prefs);
			return parent::create($account_info,$default_prefs);
		}
	}
?>
