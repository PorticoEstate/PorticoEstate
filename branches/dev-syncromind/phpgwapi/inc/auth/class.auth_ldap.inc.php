<?php
	/**
	 * Authentication based on LDAP Server
	 * @author Lars Kneschke <kneschke@phpgroupware.org>
	 * @author Joseph Engo <jengo@phpgroupware.org>
	 * @author Benoit Hamet <caeies@phpgroupware.org>
	 * @author Dave Hall <skwashd@phpgroupware.org>
	 * @copyright Copyright (C) 2000,2001 Lars Kneschke, Joseph Engo
	 * @copyright Portions Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
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
	* Authentication based on LDAP Server
	*
	* @package phpgwapi
	* @subpackage accounts
	*/
	class phpgwapi_auth_ldap extends phpgwapi_auth_
	{
		/**
		* @var string $user_search_dn DN pattern used to search for a user
		*/
		protected $username_search_dn = "uid=%u";

		/**
		* @var string $user_search_dn DN pattern used to search for a user
		*/
		protected $userid_search_dn = "uidNumber=%i";

		/**
		* Constructor
		*/
		public function __construct()
		{
			parent::__construct();
		}

		/**
		* Substitute user specific values for DN search
		*
		* @param string $dn the pattern to substitute
		* @param string $username the login for the user
		* @param int $accountid the id of the user's account
		*/
		protected function _generate_dn($dn, $username = '', $accountid = 0)
		{
			if ( !$username )
			{
				$username = $GLOBALS['phpgw']->accounts->id2lid($accountid);
			}

			if ( !$accountid )
			{
				$accountid = $GLOBALS['phpgw']->accounts->name2id($username);
			}

			$search_pairs = array
			(
				'/%u/'	=> $username,
				'/%i/'	=> $accountid
				//'/%d/'	=> //phpgw domain here - once we know how to grab it
				// others could go here at some point
			);

			return preg_replace(array_keys($search_pairs), $search_pairs, $dn);
		}

		public function authenticate($username, $passwd)
		{
			// We use a common return here to make sure all LDAP connections are closed properly
			$ok = false;

			//Connect as Admin with v3 or v2 in LDAP server
			if ( !$ldap = $GLOBALS['phpgw']->common->ldapConnect() )
			{
				$GLOBALS['phpgw']->log->message('F-Abort, Failed connecting to LDAP server for authenication, execution stopped');
				$GLOBALS['phpgw']->log->commit();
				return $ok;
			}

			// Generate the search DN
			$search = $this->_generate_dn($this->username_search_dn, $username);

			//Search for the dn
			$attributes = array( 'uid', 'dn', 'shadowexpire' );
			$sri = ldap_search($ldap, $GLOBALS['phpgw_info']['server']['ldap_context'], $search, $attributes);
			$allValues = ldap_get_entries($ldap, $sri);

			if ($allValues['count'] > 0 
				&& (!isset($allValues[0]['shadowexpire'][0])
					|| $allValues[0]['shadowexpire'][0] >= (date('U') / phpgwapi_datetime::SECONDS_IN_DAY ) ) )
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
				$ok = @ldap_bind($ldap, $userDN, $passwd);
				@ldap_unbind($ldap); // we don't need this connection anymore, so avoid a leak.
			}
			@ldap_unbind($ldap);

			return $ok;
		}

		public function change_password($old_passwd, $new_passwd, $_account_id = 0) 
		{
			if ( !$_account_id )
			{
				$_account_id = $GLOBALS['phpgw_info']['user']['account_id'];
			}

			// Generate the search DN
			$search = $this->_generate_dn($this->userid_search_dn, '', $_account_id);
	
			$ds = $GLOBALS['phpgw']->common->ldapConnect();
			$sri = ldap_search($ds, $GLOBALS['phpgw_info']['server']['ldap_context'], $search, array('dn') );
			$allValues = ldap_get_entries($ds, $sri);
			if ( $allValues['count'] == 0 )
			{
				ldap_unbind($ds);
				return '';
			}

			$dn = $allValues[0]['dn'];
			
			$entry['userpassword'] = $this->create_hash($new_passwd); 
			if ( isset($allValues[0]['shadowlastchange']) )
			{
				$entry['shadowLastChange'] = date('U') / phpgwapi_datetime::SECONDS_IN_DAY;
			}

			$pass = '';
			if (@ldap_modify($ds, $dn, $entry)) 
			{
				$GLOBALS['phpgw']->session->appsession('password','phpgwapi',$new_passwd);
				$pass = $entry['userpassword'];
			}

			ldap_unbind($ds);
			return $pass;
		}

		public function update_lastlogin($account_id, $ip)
		{
			$entry['phpgwlastlogin']     = time();
			$entry['phpgwlastloginfrom'] = $ip;
			
			// Generate the search DN
			$search = $this->_generate_dn($this->userid_search_dn, '', $account_id);
	
			$ds = $GLOBALS['phpgw']->common->ldapConnect();
			$sri = ldap_search($ds, $GLOBALS['phpgw_info']['server']['ldap_context'], $search, array('dn') );
			$allValues = ldap_get_entries($ds, $sri);

			if ( $allValues['count']
				&& isset($allValues[0]['phpgwlastlogin']) )
			{
				$dn = $allValues[0]['dn'];
				ldap_modify($ds, $dn, $entry);
			}
			ldap_unbind($ds);
		}
	}
