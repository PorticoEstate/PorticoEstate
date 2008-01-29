<?php
	/**
	* Authentication based on SQL table
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.auth_.inc.php 15793 2005-03-22 14:53:43Z fipsfuchs $
	*/

	/**
	* Authentication based on SQL table
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class auth_
	{
		var $previous_login = -1;
		var $xmlrpc_methods = array();

		function auth()
		{
			$this->xmlrpc_methods[] = array(
				'name'       => 'change_password',
				'decription' => 'Change the current users password'
			);
		}

		function authenticate($username, $passwd, $passwd_type)
		{
		}

		function change_password($old_passwd, $new_passwd, $account_id = '')
		{
			// Don't allow passwords changes for other accounts when using XML-RPC
			if (! $account_id || $GLOBALS['phpgw_info']['flags']['currentapp'] == 'login')
			{
				$account_id = $GLOBALS['phpgw_info']['user']['account_id'];
				$pwd_check  = " and account_pwd='" . md5($old_passwd) . "'";
			}

			$encrypted_passwd = md5($new_passwd);

			$GLOBALS['phpgw']->db->query("update phpgw_accounts set account_pwd='" . md5($new_passwd) . "',"
				. "account_lastpwd_change='" . time() . "' where account_id='" . $account_id . "'" . $pwd_check,__LINE__,__FILE__);

			if ($GLOBALS['phpgw']->db->affected_rows())
			{
				$GLOBALS['phpgw']->session->appsession('password','phpgwapi',base64_encode($new_passwd));
				return $encrypted_passwd;
			}
			else
			{
				return false;
			}
		}

		function update_lastlogin($account_id, $ip)
		{
			$GLOBALS['phpgw']->db->query("update phpgw_accounts set account_lastloginfrom='"
				. "$ip', account_lastlogin='" . time()
				. "' where account_id='$account_id'",__LINE__,__FILE__);
		}

	}
?>
