<?php
	/**
	* Authentication based on LDAP Server
	* @author Lars Kneschke <kneschke@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
  * @copyright Copyright (C) 2000,2001 Lars Kneschke, Joseph Engo
	* @copyright Portions Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id$
	*/

	/**
	* Authentication based on LDAP Server
	*
	* @package phpgwapi
	* @subpackage accounts
	* @ignore
	*/
	class auth_ldap extends auth_
	{
		
		function auth_ldap()
		{
			parent::auth();
		}
		
		function authenticate($username, $passwd)
		{
			/*
				this avoids warings with ldap_bind when user / password are not correct
				it is reset before this method is completed
			*/
			$error_level = error_reporting();
			error_reporting(0); 

			//Connect as Admin with v3 or v2 in LDAP server
			if ( !$ldap = $GLOBALS['phpgw']->common->ldapConnect() )
			{
				$GLOBALS['phpgw']->log->message('F-Abort, Failed connecting to LDAP server for authenication, execution stopped');
				$GLOBALS['phpgw']->log->commit();
				return false;
			}
			//Search for the dn
			$attributes = array( 'uid', 'dn', 'phpgwaccountstatus' );
			$sri = ldap_search($ldap, $GLOBALS['phpgw_info']['server']['ldap_context'], "uid=$username", $attributes);
			$allValues = ldap_get_entries($ldap, $sri);
			if ($allValues['count'] > 0)
			{
				// let's check if its an inactive account
				if($allValues[0]['phpgwaccountstatus'][0] != 'I')
				{
					/* we only care about the first dn */
					$userDN = $allValues[0]['dn'];
					/*
					generate a bogus password to pass if the user doesn't give us one 
					this gets around systems that are anonymous search enabled
					*/
					if (empty($passwd))
					{
						$passwd = crypt(microtime());
					}
					/* try to bind as the user with user suplied password */
					if (ldap_bind($ldap, $userDN, $passwd))
					{
						ldap_unbind($ldap); // we don't need this connection anymore, so avoid a leak.
						error_reporting($error_level);
						return true;
					}
				}
			}
			@ldap_unbind($ldap);
			/* Turn error reporting back to normal */
			error_reporting($error_level);

			/* dn not found or password wrong */
			return False;
		}

		function change_password($old_passwd, $new_passwd, $_account_id='') 
		{
			if ('' == $_account_id)
			{
				$_account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}
	
			$ds = $GLOBALS['phpgw']->common->ldapConnect();
			$sri = ldap_search($ds, $GLOBALS['phpgw_info']['server']['ldap_context'], 'uidnumber='.$_account_id);
			$allValues = ldap_get_entries($ds, $sri);
			$dn = $allValues[0]['dn'];
			
			$entry['userpassword'] = $GLOBALS['phpgw']->common->encrypt_password($new_passwd);
			if (is_array($allValues[0]['objectclass']) &&
				  ( in_array('phpgwAccount', $allValues[0]['objectclass']) ||
					in_array('phpgwaccount', $allValues[0]['objectclass'])
				  )
			   )
			{
				$entry['phpgwlastpasswordchange'] = time();
			}

			if (@ldap_modify($ds, $dn, $entry)) 
			{
				$GLOBALS['phpgw']->session->appsession('password','phpgwapi',$new_passwd);
				return $entry['userpassword'];
			}
			else
			{
				return false;
			}
		}

		function update_lastlogin($account_id, $ip)
		{
			$entry['phpgwlastlogin']     = time();
			$entry['phpgwlastloginfrom'] = $ip;
			$ds = $GLOBALS['phpgw']->common->ldapConnect();
			$sri = ldap_search($ds, $GLOBALS['phpgw_info']['server']['ldap_context'], '(&(uidnumber=' . $account_id.')(objectclass=phpgwaccount))');
			$allValues = ldap_get_entries($ds, $sri);
			
			if ($dn = $allValues[0]['dn'])
			{
				$this->previous_login = $allValues[0]['phpgwlastlogin'][0];
				ldap_modify($ds, $dn, $entry);
			}
		}
	}
?>
