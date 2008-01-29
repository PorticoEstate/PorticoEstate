<?php
	/**
	* Authentication based on SQL table
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.auth_sql.inc.php 17062 2006-09-03 06:15:27Z skwashd $
	*/

	/**
	* Authentication based on SQL table
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class auth_sql extends auth_
	{

		function auth_sql()
		{
			parent::auth();
		}

		function authenticate($username, $passwd, $passwd_type)
		{
			$db =& $GLOBALS['phpgw']->db;

			if ($passwd_type == 'text')
			{
				$_passwd = md5($passwd);
			}

			if ($passwd_type == 'md5')
			{
				$_passwd = $passwd;
			}

			$db->query("SELECT * FROM phpgw_accounts WHERE account_lid = '$username' AND "
				. "account_pwd='" . $_passwd . "' AND account_status ='A'",__LINE__,__FILE__);
			$db->next_record();

			if ($db->f('account_lid'))
			{
				$this->previous_login = $db->f('account_lastlogin');
				return true;
			}
			else
			{
				return false;
			}
		}
	}
?>
