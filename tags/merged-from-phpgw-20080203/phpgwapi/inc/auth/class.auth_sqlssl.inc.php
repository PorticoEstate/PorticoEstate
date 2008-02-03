<?php
	/**
	* Authentication based on SQL, with optional SSL authentication
	* @author Andreas 'Count' Kotes <count@flatline.de>
	* @copyright Copyright (C) 200x Andreas 'Count' Kotes <count@flatline.de>
	* @copyright Portions Copyright (C) 2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on SQL, with optional SSL authentication
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class auth_sqlssl extends auth_
	{

		function auth_sqlssl()
		{
			parent::auth();
		}

		function authenticate($username, $passwd)
		{
			$db =& $GLOBALS['phpgw']->db;

			$local_debug = False;

			if($local_debug)
			{
				echo "<b>Debug SQL: uid - $username passwd - $passwd</b>";
			}

			// Apache + mod_ssl provide the data in the environment
			// Certificate (chain) verification occurs inside mod_ssl
			// see http://www.modssl.org/docs/2.8/ssl_howto.html#ToC6
			if(!isset($_SERVER['SSL_CLIENT_S_DN']))
			{
				// if we're not doing SSL authentication, behave like auth_sql
				$db->query("SELECT * FROM phpgw_accounts WHERE account_lid = '$username' AND "
					. "account_pwd='" . md5($passwd) . "' AND account_status ='A'",__LINE__,__FILE__);
				$db->next_record();
			}
			else
			{
				// use username only for authentication, ignore X.509 subject in $passwd for now
				$db->query('SELECT * FROM phpgw_accounts'
					. " WHERE account_lid = '" . $db->db_addslashes($username) . "'"
					. "AND account_status ='A'",__LINE__,__FILE__);
				$db->next_record();
			}

			if($db->f('account_lid'))
			{
				return True;
			}
			else
			{
				return False;
			}
		}
	}
?>
