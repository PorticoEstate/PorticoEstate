<?php
	/**
	* View and manipulate account records using LDAP
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Lars Kneschke <lkneschke@phpgw.de>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @author Dave Hall <skwashd@phpgroupware.org>
	* @copyright Copyright (C) 2000-2002 Joseph Engo, Lars Kneschke
	* @copyright Copyright (C) 2003 Lars Kneschke, Bettina Gille
	* @copyright Portions Copyright (C) 2000-2008 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgroupware
	* @subpackage phpgwapi
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
	* View and manipulate account records using LDAP
	*
	* @package phpgroupware
	* @subpackage phpgwapi
	* @category accounts
	*/
	class phpgwapi_accounts_ldap extends phpgwapi_accounts_
	{
		/**
		* @var string $fallback_homedirectory the default home directory for users
		* @internal secure by default - no home directory
		*/
		const FALLBACK_HOMEDIRECTORY = '/dev/null';

		/**
		* @var string $fallback_loginshell the default user shell
		* @internal secure by default - no login allowed
		*/
		const FALLBACK_LOGINSHELL    = '/bin/false';

		/**
		* @var resource $ds ldap connection resource
		*/
		protected $ds;

		/**
		* @var string $user_context the ldap accounts context
		*/
		protected $user_context  = '';

		/**
		* @var string $group_context the ldap groups context
		*/
		protected $group_context = '';

		/**
		* @var string $rdn_account the user attribute
		* @internal TODO move to setup
		*/
		protected $rdn_account = 'uid';

		/**
		* @var string $rdn_group the group attribute
		* @internal TODO move to setup
		*/
		protected $rdn_group   = 'cn';

		public function __construct($account_id = null, $account_type = null)
		{
			$this->ds = $GLOBALS['phpgw']->common->ldapConnect();
			$this->user_context  = $GLOBALS['phpgw_info']['server']['ldap_context'];
			$this->group_context = $GLOBALS['phpgw_info']['server']['ldap_group_context'];
			parent::__construct($account_id, $account_type);
		}

		/**
		* Add an account to a group entry by adding the account name to the memberuid attribute
		*
		* @param integer $account_id Account id
		* @param integer $groupID Group id
		* @return boolean True on success otherwise false
		*/
		public function add_account2group($account_id, $group_id)
		{
			if ($account_id && $groupID)
			{
				$groupEntry = $this->_group_exists($group_id);
				$memberUID = $this->id2name($account_id);
				if ($groupEntry && $memberUID)
				{
					if (!is_array($groupEntry['memberuid']) || !in_array($memberUID, $groupEntry['memberuid']))
					{
						$entry['memberuid'][] = $memberUID;
						return ldap_mod_add($this->ds, $groupEntry['dn'], $entry);
					}
				}
			}
			return false;
		}

		/**
		 * Create a new user account  - this only creates the acccount
		 *
		 * For creating a fully working user, use self::create()
		 * @see self::create
		 * @param object $account the phpgwapi_user object for the new account
		 * @return integer the new user id
		 */
		public function create_user_account($account)
		{
			//FIXME Implement me!
		}

		/**
		* Delete an account or group
		*
		* @param integer $id Id of group/account to delete
		* @return boolean True on success otherwise false
		*/
		public function delete($id = '')
		{
			$id = (int) get_account_id($id);
			$type = $this->get_type($id);

			if ($type == 'g')
			{
				$sri = ldap_search($this->ds, $this->group_context, "(&(objectclass=phpgwgroup)(gidnumber={$id}))");
				$allValues = ldap_get_entries($this->ds, $sri);
			}
			else
			{
				$sri = ldap_search($this->ds, $this->user_context, "(&(objectclass=phpgwaccount)(uidnumber={$id}))");
				$allValues = ldap_get_entries($this->ds, $sri);
			}

			if ($allValues[0]['dn'])
			{
				return ldap_delete($this->ds, $allValues[0]['dn']);
			}
			else
			{
				return false;
			}
		}

		/**
		* Delete an account for a group entry by removing the account name from the memberuid attribute
		*
		* @param integer $account_id Account id
		* @param integer $groupID Group id
		* @return boolean True on success otherwise false
		*/
		public function delete_account4group($account_id, $group_id)
		{
			if ($account_id && $group_id)
			{
				$groupEntry = $this->_group_exists($group_id);
				$memberUID = $this->id2name($account_id);
				if ($groupEntry && $memberUID)
				{
					if (is_array($groupEntry['memberuid']))
					{
						for ($i=0; $i < count($groupEntry['memberuid']); $i++)
						{
							if ($groupEntry['memberuid'][$i] == $memberUID)
							{
								$entry = array('memberuid' => array($memberUID));
								return ldap_mod_del($this->ds, $groupEntry['dn'], $entry);
							}
						}
					}
				}
			}
			return false;
		}

		/**
		* Test if a group or an account exists
		*
		* @param integer $id Account or group id
		* @return integer|boolean 1 : account or group exist; 2 : account and group exist; 0/false nothing exist
		*/
		public function exists($id)
		{
			if (!is_int($id) && $id != '')
			{
				$id = $this->name2id($id);
			}

			if ($id)
			{
				$return = 0;
				if ($this->_user_exists($id))
				{
					$return++;
				}
				if ($this->_group_exists($id));
				{
					$return++;
				}
				return $return;
			}
			else
			{
				return false;
			}
		}

		/**
		* Read all accounts or groups
		*
		* @param string $_type Type of list 'accounts', 'groups' or 'both'
		* @param integer $start Start position
		* @param string $sort 'ASC'ending or 'DESC'ending sort order
		* @param string $order Order by 'account_' Field. Defaults to 'account_lid'
		* @param string $query LDAP query
		* @param integer $offset Offset from start position (-1 == no limit)
		* @return array|boolean List with all accounts|groups or false
		*/
		public function get_list($_type = 'both', $start = -1, $sort = '', $order = '', $query = '', $offset = -1)
		{
			//echo "accounts_ldap:get_list($_type, $start, $sort, $order, $query, $offset) called<br>";
			$query = strtolower($query);
			if ($offset != -1)
			{
				$limitclause = '';
			}
			elseif ($start != -1 && $offset == -1)
			{
				$limitclause = '';
			}

			if (! $sort)
			{
				$sort = '';//"desc";
			}

			if ($_type == 'accounts')
			{
				$listentries = $this->_get_user_list($query);
			}
			elseif ($_type == 'groups')
			{
				$listentries = $this->_get_group_list($query);
			}
			else
			{
				$listentries = array_merge($this->_get_user_list($query), $this->_get_group_list($query));
			}

			//echo 'listentries <pre>' . print_r($listentries, true) . '</pre>';

			// sort the array
			$arrayFunctions = createObject('phpgwapi.arrayfunctions');
			if(empty($order))
			{
				$order = 'account_lid';
			}
			$sortedlist = $arrayFunctions->arfsort($listentries,array($order),$sort);
			$this->total = count($listentries); // this shouldn't be an obejct var for one account/group whatever
			unset($listentries);

			if ( is_array($sortedlist) )
			{
				if( $start > 0 && $offset > 0 )
				{
					//echo "defined limit - start: $start, offset: $offset<br>";
					return array_slice($sortedlist, $start, $offset);
				}
				elseif($start != -1)
				{
					//echo "defined limit - start: $start, offset: user[max]<br>";
					return array_slice($sortedlist, $start, $GLOBALS['phpgw_info']['user']['preferences']['common']['maxmatchs']);
				}
				else
				{
					//echo "return all<br>";
					return $sortedlist;
				}
			}
			return array();
		}

		/**
		* Convert an id into its corresponding account login or group name
		*
		* @param integer $id Account or group id
		* @return string account login id or the group - empty string means not found
		*/
		function id2lid($id)
		{
			static $lid_list;
			if(isset($lid_list[$id]))
			{
				return $id_list[$id];
			}

			$type = $this->get_type($id);

			if ($type == 'g')
			{
				$group = $this->_group_exists($id);
				$name = $group['cn'][0];
			}
			elseif ($type == 'u')
			{
				$account = $this->_user_exists($id);
				$name = $account['uid'][0];
			}
			else
			{
				return '';
			}
			$lid_list[$id] = $name;
			return $name;
		}

		/**
		* Convert an id into its corresponding account or group name
		*
		* @param integer $id Account or group id
		* @return string Name of the account or the group when found othwerwise empty string
		*/
		function id2name($id)
		{
			static $id_list;
			if(isset($id_list[$id]))
			{
				return $id_list[$id];
			}

			$type = $this->get_type($id);

			if ($type == 'g')
			{
				$group = $this->_group_exists($id);
				$name = lang('%1 group', $group['cn'][0]);
			}
			elseif ($type == 'u')
			{
				$account = $this->_user_exists($id);
				$name = $account['cn'][0];
			}
			else
			{
				return '';
			}
			$id_list[$id] = $name;
			return $name;
		}

		/**
		* Convert id to the corresponding account (or group) name
		*
		* @param string $account_lid Account name or group name for which you want the id
		* @return integer|boolean Id of the account/group when found otherwise false
		*/
		public function name2id($account_lid)
		{
			static $name_list;

			if ( isset($name_list[$account_lid]) && $name_list[$account_lid] )
			{
				return $name_list[$account_lid];
			}

			$id  = $this->_group_name2id($account_lid);
			$uid = $this->_user_name2id($account_lid);

			if ($uid)
			{
				$id = $uid;
			}
			return $id;
		}

		public function search_person($person_id)
		{
			static $person_list;
			if(isset($person_list[$person_id]))
			{
				return intval($person_list[$person_id]);
			}

			$allValues = array();
			// Groups are person? are you sure?
			$sri = ldap_search($this->ds, $this->group_context, "(&(phpgwContactID={$person_id})(phpgwGroupID=*))");
			$allValues = ldap_get_entries($this->ds, $sri);

			if (@$allValues[0]['gidnumber'][0])
			{
				$person_list[$person_id] = intval($allValues[0]['gidnumber'][0]);
				return $person_list[$person_id];
			}

			$allValues = array();
			$sri = ldap_search($this->ds, $this->user_context, "(&(phpgwContactID={$person_id})(phpgwAccountID=*))");
			$allValues = ldap_get_entries($this->ds, $sri);

			if (@$allValues[0]['uidnumber'][0])
			{
				$id_list[$person_id] = intval($allValues[0]['uidnumber'][0]);
				return $person_list[$person_id];
			}

			return $person_list[$person_id];
		}

		/**
		* Get type (account or group) for an id
		*
		* @internal FIXME this should not contain a die
		* @param integer $id Account or group id
		* @return string|boolean 'u' : account (user); 'g' : group; empty string for none existing id
		*/
		public function get_type($id = 0) // get_type() without an id - what do you expect me to return!?
		{
			$type = '';
			if ( $id == 0 )
			{
				return $type;
			}

			if ($this->_user_exists($id))
			{
				$type = 'u';
			}

			if ($this->_group_exists($id))
			{
				if ($type == 'u')
				{
					//FIXME handle this more gracefully
					die('account/group id ('.$id.')conflict - bad luck');
				}
				else
				{
					$type = 'g';
				}
			}
			return $type;
		}

		/**
		* Get new id for an account/group
		*
		* @param string $type 'u' : account (user); 'g' : group
		* @return integer New id for an account/group, 0 == failure
		*/
		protected function _get_nextid($type = 'u')
		{
			if ($type == 'u')
			{
				return $this->_get_next_account_id();
			}
			elseif ($type == 'g')
			{
				return $this->_get_next_group_id();
			}
			else
			{
				return 0;
			}
		}

		/**
		* Get new id for an account
		*
		* @return integer|boolean New id for an account or false
		*/
		protected function _get_next_account_id()
		{
			$filter = '(|(objectclass=posixaccount)(objectclass=phpgwaccount))';
			$result = ldap_search($this->ds, $this->user_context, $filter, array('uidnumber'));
			$entries = ldap_get_entries($this->ds, $result);

			if ( !is_array($entries) || !count($entries) )//this shouldn't happen
			{
				return (int) $GLOBALS['phpgw_info']['server']['account_min_id'];
			}

			// parse all LDAP uidnumbers in a single array '$IDs'
			foreach ( $entries as $entry )
			{
				if ( isset($entry['uidnumber'][0]) && is_numeric($entry['uidnumber'][0]) )
				{
					$ids[$entry['uidnumber'][0]] = true;
				}
			}
			return $this->_id_tester($ids, $GLOBALS['phpgw_info']['server']['account_min_id'], $GLOBALS['phpgw_info']['server']['account_max_id'] );
		}

		/**
		* Get new id for a group
		*
		* @return integer|boolean New id for a group or false
		*/
		protected function _get_next_group_id()
		{
			$filter = '(|(objectclass=posixgroup)(objectclass=phpgwgroup))';
			$result = ldap_search($this->ds, $this->group_context, $filter, array('gidnumber'));
			$entries = ldap_get_entries($this->ds, $result);

			if ( !is_array($entries) || !count($entries) )//this shouldn't happen
			{
				return (int) $GLOBALS['phpgw_info']['server']['group_min_id'];
			}

			$ids = array();
			// parse all LDAP uidnumbers in a single array '$IDs'
			foreach ( $entries as $entry )
			{
				if ( isset($entry['gidnumber'][0]) && is_numeric($entry['gidnumber'][0]) )
				{
					$ids[$entry['gidnumber'][0]] = true;
				}
			}
			//echo 'accts_ldap::_get_next_group_id - ids == <pre>' . print_r($ids, true) . "</pre><br>range - min: {$GLOBALS['phpgw_info']['server']['group_min_id']} max: {$GLOBALS['phpgw_info']['server']['group_max_id']}";
			return $this->_id_tester($ids, $GLOBALS['phpgw_info']['server']['group_min_id'], $GLOBALS['phpgw_info']['server']['group_max_id'] );
		}

		/**
		* Test if group exists
		*
		* @param integer $id Group id
		* @param string $dn LDAP distinguised name
		* @return array|boolean Array with 'dn' infos or false
		*/
		protected function _group_exists($id, $dn = '')
		{
			if ($id)
			{
				$result = ldap_search($this->ds, $this->group_context, "(&(gidnumber={$id})(objectclass=posixgroup))");
				$entries = ldap_get_entries($this->ds, $result);
				if ( !is_array($entries) || !count($entries) )
				{
					return array();
				}

				if ( isset($entries[0]) && is_array($entries[0]) )
				{
					return $entries[0];
				}
				else
				{
					return $this->_dn_exists($dn);
				}
			}
			return false;
		}

		/**
		* Test if account exists
		*
		* @param integer $id Account id
		* @param string $dn LDAP distinguised name
		* @return array|boolean Array with 'dn' infos or false
		*/
		protected function _user_exists($id, $dn = null)
		{
			if ($id)
			{
				$result = ldap_search($this->ds, $this->user_context, "(&(uidnumber={$id})(objectclass=posixaccount))");
				$allValues = ldap_get_entries($this->ds, $result);
				if ( isset($allValues[0]['dn']) )
				{
					return $allValues[0];
				}
			}
			if($dn)
			{
				return $this->_dn_exists($dn);
			}
			return false;
		}

		/**
		* Test if contact exists
		*
		* @param integer $id Contact id
		* @param string $dn LDAP distinguised name
		* @return array|boolean Array with 'dn' infos or false
		*/
		function _person_exists($id, $dn = '')
		{
			if ($id)
			{
				$result = ldap_search($this->ds, $this->user_context, "phpgwcontact={$id}");
				$allValues = ldap_get_entries($this->ds, $result);
				if ($allValues[0]['dn'])
				{
					return $allValues[0];
				}
				else
				{
					return $this->_dn_exists($dn);
				}
			}
			return false;
		}


		/**
		* Test if the given dn exists
		*
		* @param string $dn LDAP distinguised name
		* @return array|boolean Array with 'dn', 'count' and attributes or false
		*/
		function _dn_exists($dn)
		{
			if ($dn != '')
			{
				$result = @ldap_search($this->ds, $dn, 'objectClass=*');
				if ($result)
				{
					$allValues = ldap_get_entries($this->ds, $result);
					if ($allValues[0]['dn'])
					{
						return $allValues[0];
					}
				}
			}
			return false;
		}

		/**
		* Create account or group
		*
		* @param array $account_info Account/group information
		* @param string $default_prefs Unused
		* @return array|boolean Id of the newly created account or false
		*/
		function create($account_info, $default_prefs = true)
		{
			//echo 'accounts_ldap::create called!';
			if ( !isset($account_info['account_id']) || empty($account_info['account_id']) || !$account_info['account_id'] == 0 )
			{
				//echo 'need new acct id for new entry';
				$account_info['account_id'] = $this->get_nextid($account_info['account_type']);
				//echo "- got given {$account_info['account_id']}<br>";
			}

			if ($account_info['account_type'] == 'u')
			{
				$this->create_account($account_info);
			}
			elseif($account_info['account_type'] == 'g')
			{
				$this->create_group($account_info);
			}
			else
			{
				return false;
			}
			return parent::create($account_info, $default_prefs);
		}

		/**
		* Create new account
		*
		* @param array $account_info Account information: account_id, account_expires, account_status, lastlogin, lastloginfrom, lastpasswd_change, account_firstname, account_lastname, account_passwd, homedirectory, ...
		* @param string $default_prefs Unused
		*/
		function create_account($account_info)
		{
			$dn = $this->rdn_account .
				  '=' .
				  $this->_get_leaf_Name($account_info['account_firstname'], $account_info['account_lastname'], $account_info['account_lid']) .
				  ',' .
				  $this->user_context;

			$entry = array();
			$entry['objectclass'] = array();

			// phpgw attributes
			$entry['objectclass'][]       = 'phpgwAccount';
			$entry['phpgwaccountid']      = $account_info['account_id'];
			$entry['phpgwaccountexpires'] = isset($account_info['account_expires'])  ? $account_info['account_expires'] : $account_info['expires'];
			if (isset($account_info['account_status']) || isset($account_info['status']))
			{
				$entry['phpgwaccountstatus'] = isset($account_info['account_status']) ? $account_info['account_status'] : $account_info['status'];
			}
			else
			{
				$entry['phpgwaccountstatus'] = 'I'; // 'I' for inactiv
			}
			if (isset($account_info['lastlogin']))
			{
				$entry['phpgwlastlogin'] = $account_info['lastlogin'];
			}
			if (isset($account_info['lastloginfrom']))
			{
				$entry['phpgwlastloginfrom'] = $account_info['lastloginfrom'];
			}
			if (isset($account_info['lastpasswd_change']))
			{
				$entry['phpgwlastpasswordchange'] = $account_info['lastpasswd_change'];
			}
			if (isset($account_info['quota']))
			{
				$entry['phpgwquota'] = $account_info['quota'];
			}
			else
			{
				$entry['phpgwquota'] = isset($this->quota) ? $this->quota : 0;
			}
			$structural_modification = false;
			if(isset($account_info['person_id']) && (int) $account_info['person_id'])
			{
				$entry['objectclass'][] = 'phpgwContact'; // shouldn't be structural
				$entry['phpgwcontactid'] = (int)$account_info['person_id'];
			}
			else
			{
				$entry['objectclass'][]       = 'account';
			}

			// additional attributes from the phpgw for groups
			$entry['objectclass'][]       = 'posixAccount';
			$entry['cn']                  = $this->get_fullname($account_info['account_firstname'], $account_info['account_lastname']);
			$entry['uidnumber']           = $account_info['account_id'];
			$entry['uid']                 = $account_info['account_lid'];
			$entry['description']         = str_replace('*','',lang('phpgw-created account'));
			if ( isset($account_info['account_firstname']) )
			{
				$entry['givenname'] = $account_info['account_firstname'];
			}
			if ( isset($account_info['account_lastname']) )
			{
				$entry['sn'] = $account_info['account_lastname'];
			}
			else
			{
				$entry['sn'] = ' ';
			}
			if ( isset($account_info['account_passwd']) )
			{
				$entry['userpassword'] = $GLOBALS['phpgw']->auth->generate_hash($account_info['account_passwd']);
			}

			// Fields are must for LDAP - so we write them in any case
			$entry['homedirectory']       = $this->_get_homedirectory($account_info['homedirectory'], $account_info['account_lid']);
			$entry['loginshell']          = $this->_get_loginshell($account_info['loginshell']);


			// special gidnumber handling
			if ($GLOBALS['phpgw_info']['server']['ldap_group_id'])
			{
				$enty['gidnumber'] = $GLOBALS['phpgw_info']['server']['ldap_group_id'];
			}
			else
			{
				$entry['gidnumber']           = $account_info['account_id'];
			}

			$oldEntry = $this->_user_exists($account_info['account_id'], $dn);

			if ($oldEntry) // found an existing entry in LDAP
			{
				if ($this->createMode == 'replace')
				{
					ldap_delete($this->ds, $oldEntry['dn']);
					$this->add_ldap_entry($dn, $entry);
				}
				elseif ($this->createMode == 'extend')
				{
					/* not yet implemented */
				}
				else  // createMode == 'modify'
				{
					while (list($key,$val) = each($oldEntry))
					{
						if (!is_int($key))
						{
							if ( isset($oldEntry[$key]) && is_array($oldEntry[$key]) )
							{
								unset($oldEntry[$key]['count']);
							}
							switch ($key)
							{
								case 'dn':
									if ($oldEntry['dn'] != $dn)  // new group name DN should renamed as well
									{
										$oldEntry['dn'] = $this->rename_ldap_entry($oldEntry['dn'], $dn, $this->user_context);
										if (!$oldEntry)
										{
											die('ldap_rename FAILED: [' . ldap_errno($this->ds) . '] ' . ldap_error($this->ds));
										}
									}
									break;

								case 'count':
								case 'cn':
								case 'description':
								case 'phpgwaccountid':
								case 'gidnumber':
								case 'phpgwaccountstatus':
								case 'phpgwaccountexpires':
								case 'uidnumber':
								case 'uid':
								case 'userpassword':
								case 'homedirectory':
								case 'loginshell':
								case 'givenname':
								case 'sn':
								case 'phpgwlastlogin':
								case 'phpgwlastloginfrom':
								case 'phpgwlastpasswordchange':
								case 'phpgwcontactid':
								case 'phpgwquota':
									break;

								case 'objectclass':
									if( !in_array('phpgwAccount', $oldEntry[$key]) && !in_array('phpgwContact', $oldEntry[$key]) )
									{
										$entry[$key] = $oldEntry[$key];
										array_push($entry[$key], 'phpgwAccount');
									}
									elseif((in_array('phpgwContact',$entry[$key]) && ! in_array('phpgwContact',$oldEntry[$key])))
									{
										$structural_modification = true;
									}
									else
									{
										$entry[$key] = $oldEntry[$key];
									}
									break;

								default:
									$entry[$key] = $oldEntry[$key];
							}
						}
					}

					//Caeies Bonification
					//When a structural object is modified you need to remove it then re add it ...
					//So You need to add to entry all the old stuff not modified in $entry .
					if ( $structural_modification )
					{
						for( $i = 0; $i < $oldEntry['count']; ++$i)
						{
							if ( !empty($oldEntry[$i]) && !(array_key_exists($oldEntry[$i],$entry)) )
							{
								if ( count($oldEntry[$oldEntry[$i]]) == 1 )
								{
									$entry[$oldEntry[$i]] = $oldEntry[$oldEntry[$i]][0];
								}
								else
								{
									$entry[$oldEntry[$i]] = $oldEntry[$oldEntry[$i]];
								}
							}
						}
						ldap_delete($this->ds, $oldEntry['dn']);
						return $this->add_ldap_entry($oldEntry['dn'], $entry);
					}
					else
					{
						return $this->modify_ldap_entry($oldEntry['dn'], $entry);
					}
					$dn = $oldEntry['dn'];
				}
			}
			else // entry not yet in LDAP
			{
				return $this->add_ldap_entry($dn, $entry);
			}
		}

		/**
		* Create new group
		*
		* @param array $account_info Group information: account_id, account_lid, ...
		* @param string $default_prefs Unused
		*/
		function create_group($account_info)
		{
			$dn = $this->rdn_group . '=' . $account_info['account_lid'] . ',' . $this->group_context;

			// phpgw needed attributes

			$entry['objectclass'][]  = 'phpgwGroup';
			$entry['phpgwgroupID']   = $account_info['account_id'];
			$entry['gidnumber']      = $account_info['account_id'];

			// additional attributes from the phpgw for groups
			$entry['objectclass'][]  = 'posixGroup';
			$entry['cn']             = $account_info['account_lid'];
			$entry['description']    = utf8_encode(str_replace('*', '', lang('phpgw-created group')));
			$entry['memberuid']      = $this->_get_member_uids($account_info['account_id']);
			if (!$entry['memberuid'])
			{
				unset ($entry['memberuid']);
			}
			if (isset($account_info['quota']))
			{
				$entry['phpgwquota'] = $account_info['quota'];
			}
			else
			{
				$entry['phpgwquota'] = isset($this->quota) ? $this->quota : 0;
			}

			$oldEntry = $this->_group_exists($account_info['account_id'], $dn);

			if ($oldEntry) // found an existing entry in LDAP
			{
				if ($this->createMode == 'replace')
				{
					ldap_delete($this->ds, $oldEntry['dn']);
					$this->add_ldap_entry($dn, $entry);
				}
				elseif ($this->createMode == 'extend')
				{
					/* not yet implemented */
				}
				else  // createMode == 'modify'
				{
					while (list($key,$val) = each($oldEntry))
					{
						if (!is_int($key))
						{
							if ( isset($oldEntry[$key]) && is_array($oldEntry[$key]) )
							{
								unset($oldEntry[$key]['count']);
							}
							switch ($key)
							{
								case 'dn':
									if ($oldEntry['dn'] != $dn)  // new group name DN should renamed as well
									{
										$oldEntry['dn'] = $this->rename_ldap_entry($oldEntry['dn'], $dn, $this->group_context);
										if (!$oldEntry)
										{
																					  die('ldap_rename FAILED: [' . ldap_errno($this->ds) . '] ' . ldap_error($this->ds));
										}
									}
									break;
								case 'count':
								case 'cn':
								case 'description':
								case 'phpgwgroupid':
								case 'gidnumber':
								case 'memberuid':
									break;

								case 'objectclass':
									if( !in_array('phpgwGroup', $oldEntry[$key]) && !in_array('phpgwgroup', $oldEntry[$key]) )
									{
										$entry[$key] = $oldEntry[$key];
										array_push($entry[$key], 'phpgwGroup');
									}
									else
									{
											$entry[$key] = $oldEntry[$key];
									}
									break;

								default:
									$entry[$key] = $oldEntry[$key];
							}
						}
					}
					$this->modify_LDAP_Entry($oldEntry['dn'], $entry);
				}
			}
			else // entry not yet in LDAP
			{
				$this->add_ldap_entry($dn, $entry);
			}
		}

		/**
		* Add entry to LDAP
		*
		* @param string $dn The distinguised name which should be added
		* @param array $entry Array of all LDAP attributes to be added
		* @return boolean True when successful otherwise false (die for now)
		*/
		function add_ldap_entry($dn, $entry)
		{
			//die("<pre>" . print_r($entry, true) . "</pre>\n<br>");
			$success = ldap_add($this->ds, $dn, $entry);
			if (!$success)
			{
				echo 'ldap_add FAILED: [' . ldap_errno($this->ds) . '] ' . ldap_error($this->ds).'<br><br>';
				echo "<strong>Adds: {$dn}</strong><br>";
				die("<pre>" . print_r($entry, true) . "</pre>\n<br>");
			}
			else
			{
				return true;
			}
		}

		/**
		* Modify an entry in LDAP
		*
		* @param string $dn the distinguised name which should be modified
		* @param array $entry Array of all LDAP attributes which are going to be modified
		* @return boolean True on success otherwise false (die for now)
		*/
		function modify_ldap_entry($dn, $entry)
		{
			$success = ldap_modify($this->ds, $dn, $entry);
			if (!$success)
			{
				echo 'ldap_modified FAILED: [' . ldap_errno($this->ds) . '] ' . ldap_error($this->ds).'<br /><br />';
				echo "<strong>Modifies: {$dn}</strong><br>";
				die("<pre>" . print_r($entry, true) . "</pre>\n<br>");
			}
			else
			{
				return true;
			}
		}

		/**
		* Rename LDAP entry
		*
		* @param string $oldDN Old distinguised name that should be renamed
		* @param string $newDN New distinguised name to which the old one should be renamed
		* @param string $baseDN Base distinguised name for the rename operation
		* @return string|boolean The new distinguised name on success otherwise false
		*/
		function rename_ldap_entry($oldDN, $newDN, $baseDN)
		{
			$newDN_array = (ldap_explode_dn($newDN, 0));
			$oldDN_array = (ldap_explode_dn($oldDN, 0));

			unset($newDN_array['count']);
			unset($oldDN_array['count']);

			$newDN_RDN = $newDN_array[0];
			$oldDN_RDN = array_shift($oldDN_array);
			$oldDN_base  = implode(',', $oldDN_array);
			if (($newDN_RDN != $oldDN_RDN) && ($oldDN_base == $baseDN))
			{
				$success = ldap_rename ( $this->ds, $oldDN, $newDN_RDN, $baseDN, false);
				if ($success)
				{
					return $newDN;
				}
				else
				{
					return false;
				}
			}
		}

		function get_account_name($accountid, &$lid, &$fname, &$lname)
		{
			static $account_name;

			$account_id = get_account_id($accountid);
			if(isset($account_name[$account_id]))
			{
				$lid = $account_name[$account_id]['lid'];
				$fname = $account_name[$account_id]['fname'];
				$lname = $account_name[$account_id]['lname'];
				return;
			}
			$acct_type = $this->get_type($account_id);

			/* search the dn for the given uid */
			if ( ($acct_type == 'g') && $this->group_context )
			{
				$sri = ldap_search($this->ds, $this->group_context, 'gidnumber='.$account_id);
			}
			else
			{
				$sri = ldap_search($this->ds, $this->user_context, 'uidnumber='.$account_id);
			}
			$allValues = ldap_get_entries($this->ds, $sri);

			if($acct_type =='g')
			{
				$account_name[$account_id]['lid']   = $allValues[0]['cn'][0];
				$account_name[$account_id]['fname'] = utf8_decode($allValues[0]['cn'][0]);
				$account_name[$account_id]['lname'] = 'Group';
			}
			else
			{
				$account_name[$account_id]['lid']   = $allValues[0]['uid'][0];
				$account_name[$account_id]['fname'] = utf8_decode($allValues[0]['givenname'][0]);
				$account_name[$account_id]['lname'] = utf8_decode($allValues[0]['sn'][0]);
			}
			$lid = $account_name[$account_id]['lid'];
			$fname = $account_name[$account_id]['fname'];
			$lname = $account_name[$account_id]['lname'];
			return;
		}

		/**
		* Get the DN for the given account id
		*
		* @param interger $id Account id
		* @return string|boolean Distinguised name or false
		*/
		function get_dn_for_id($id = '')
		{
			return $this->get_dn_for_account_id($id);
		}

		/**
		* Get the DN for the account id
		*
		* @param integer $_accountid Account id
		* @return string|boolean Distinguised name or false
		*/
		function get_dn_for_account_id($id = '')
		{
			$_account_id = get_account_id($id);

			$sri = ldap_search($this->ds, $this->user_context, 'uidnumber='.$id, array('dn'));
			$allValues = ldap_get_entries($this->ds, $sri);
			if ($allValues[0]['dn'])
			{
				return $allValues[0]['dn'];
			}
			else
			{
				return false;
			}
		}

		function get_account_with_contact()
		{
			$sri = ldap_search($this->ds, $this->user_context, "(&(phpgwaccounttype=u)(phpgwcontactid=*))", array('uidnumber', 'phpgwcontactid'));
			$allValues = ldap_get_entries($this->ds, $sri);
			if(is_array($allValues))
			{
				$count = intval($allValues['count']);
				for($i=0;$i<$count; $i++)
				{
					$value = &$allValue[$i];
					$accounts[$value['uidnumber'][0]] = $value['phpgwcontactid'][0];
				}
			}

			return $accounts;
		}

		function get_account_without_contact()
		{
			$sri = ldap_search($this->ds, $this->user_context, "(&(phpgwaccounttype=u)(!(phpgwcontactid=*)))", array('uidnumber'));
			$allValues = ldap_get_entries($this->ds, $sri);
			if(is_array($allValues))
			{
				$count = intval($allValues['count']);
				for ( $i = 0; $i < $count; ++$i)//foreach(allValues as $value)
				{
					$value = &$allValue[$i];
					$accounts[] = $value['uidnumber'][0];
				}
			}
			return $accounts;
		}


		/**
		* Full name generation
		*
		* @param string $first Firstname
		* @param string $last Lastname
		* @return string Fullname
		*/
		function get_fullname($first, $last)
		{
			return $first.' '.$last;
		}

		/**
		* Test an array with ids for a free id in respect to a min and max id
		*
		* @param array $ids Array with existing id's
		* @param integer $min Minimum for id number
		* @param integer $max Maximum for id number
		* @return integer New id that can be used
		*/
		protected function _id_tester($ids, $min = 1, $max = 0)
		{
			//echo 'IDs<pre>' . print_r($ids, true) . '</pre>';
			if ( $min > $max )
			{
				throw new Exception(lang('Account settings are invalid'));
				return 0;
			}

			if ( !is_array($ids) )
			{
				$ids = array();
			}

			for ( $i = $min; $i <= $max; ++$i )
			{
				if ( !isset($ids[$i]) )
				{
					//echo "found $i as the next id<br>";
					return $i;
				}
			}

			throw new Exception(lang('No user IDs available'));
			//return 0;
		}

		/**
		* Get a list of member account ids for a group
		*
		* @return arrray list of members of the current group
		*/
		public function get_members($group_id = null)
		{
			if ( is_null($group_id) )
			{
				$group_id = $this->account_id;
			}
			$group_id = get_account_id($group_id);

			$sri = ldap_search($this->ds, $this->group_context, "gidnumber={$group_id}");
			$entries = ldap_get_entries($this->ds, $sri);
			if ( !is_array($entries) )
			{
				$entries = array();
			}

			$members = array();
			foreach ( $entries as $entry )
			{
				$members[] = $entry['uidnumber'];
			}
			return $members;
		}

		/**
		* Get a list of members of the group
		*
		* @param int $group_id the group to check
		* @return array list of members
		*/
		public function member($group_id = 0)
		{
			$group_id = get_account_id($group_id);

			if ( isset($this->members[$group_id]) )
			{
				return $this->members[$group_id];
			}

			$sri = ldap_search($this->ds, $this->group_context, "gidnumber={$group_id}");
			$entries = ldap_get_entries($this->ds, $sri);
			if ( !is_array($entries) )
			{
				$entries = array();
			}

			foreach ( $entries as $entry )
			{
				$this->members[$account_id][] = array
				(
					'account_id'	=> $entry['uidnumber'],
					'account_name'	=> $GLOBALS['phpgw']->common->display_fullname($entry[$this->rdn_account], $entry['givenname'], $entry['sn'])
				);
			}
			return $this->members[$group_id];
		}

		/**
		* Get a list of groups the user is a member of
		*
		* @param int $account_id the user account to lookup
		* @return array the groups the user is a member of
		* @internal return structure array(array('account_id' => id, 'account_name' => group name))
		*/
		public function membership($account_id = 0)
		{
			$account_id = get_account_id($account_id);

			if ( isset($this->memberships[$account_id]) )
			{
				return $this->memeberships[$account_id];
			}

			$this->memberships[$account_id] = array();

			$sql = 'SELECT phpgw_accounts.account_id, phpgw_accounts.account_firstname FROM phpgw_accounts, phpgw_group_map'
				. ' WHERE phpgw_accounts.account_id = phpgw_group_map.group_id'
					. " AND phpgw_group_map.account_id = {$account_id}";

			$this->db->query($sql, __LINE__, __FILE__);

			while ( $this->db->next_record() )
			{
				$this->memberships[$account_id][] = array
				(
					'account_id'	=> $this->db->f('account_id'),
					'account_name'	=> lang('%1 group', $this->db->f('account_firstname'))
				);
			}
			return $this->memberships[$account_id];
		}

		public function read_repository()
		{
			$this->account = $this->get($this->account_id);
			return $this->account;
		}

		public function get($id, $use_cache = true)
		{
			$id = (int) $id;
			$account = null;

			$acct_type = $this->get_type($this->account_id);

			/* search the dn for the given uid */
			if ( $acct_type == phpgwapi_account::TYPE_GROUP
				&& $this->group_context )
			{
				$sri = ldap_search($this->ds, $this->group_context, "gidnumber={$id}");
			}
			else if ( $acct_type == phpgwapi_account::TYPE_USER
				&& $this->user_context )
			{
				$sri = ldap_search($this->ds, $this->user_context, "uidnumber={$id}");
			}
			else
			{
				throw new Exception('Invalid account requested');
			}
			$entries = ldap_get_entries($this->ds, $sri);
			// first in best dressed - we can't tell which one is the correct one
			$entry = $entries[0];
			unset($entries);

			/* Now dump it into the array; take first entry found */
			$this->data['account_dn']             = $entries[0]['dn'];
			$this->data['fullname']               = $entries[0]['cn'][0];
			if($acct_type == 'g')
			{
				$this->account_id	= $this->data['account_id']			= $entries[0]['gidnumber'][0];
				$this->lid			= $this->data['account_lid']		= $entries[0]['cn'][0];
				$this->firstname	= $this->data['account_firstname']	= $entries[0]['cn'][0];
				$this->lastname		= $this->data['account_lastname']	= lang('group');
				$this->account_type	= $this->data['type']				= 'g';
			}
			else
			{
				$this->account_id	= $this->data['account_id']			= $entries[0]['uidnumber'][0];
				$this->lid			= $this->data['account_lid']		= $entries[0]['uid'][0];
				$this->firstname	= $this->data['account_firstname']	= (isset($entries[0]['givenname']) && isset($entries[0]['givenname'][0])) ? $entries[0]['givenname'][0] : '';
				$this->lastname		= $this->data['account_lastname']	= (isset($entries[0]['sn']) && isset($entries[0]['sn'][0])) ? $entries[0]['sn'][0] : '';
				$this->expires 		= $this->data['expires'] = $this->data['account_expires'] = $entries[0]['phpgwaccountexpires'][0];
				$this->data['homedirectory']          = isset($entries[0]['homedirectory']) ? $entries[0]['homedirectory'][0] : self::FALLBACK_HOMEDIRECTORY;
				$this->data['loginshell']             = isset($entries[0]['loginshell']) ? $entries[0]['loginshell'][0] : self::FALLBACK_LOGINSHELL;
				$this->status = $this->data['status'] = isset($entries[0]['phpgwaccountstatus']) && $entries[0]['phpgwaccountstatus'][0] == 'A' ? 'A' : '';
				$this->account_type	= $this->data['type']				= 'u';
			}
			if ( isset($entries[0]['phpgwcontactid']) )
			{
				$this->person_id	= $this->data['person_id'] = $entries[0]['phpgwcontactid'][0];
			}
			if ( !isset($entries[0]['phpgwquota']) || $entries[0]['phpgwquota'] === '')
			{
				$this->data['quota'] = $this->quota; // set to 0 by default
			}
			else
			{
				$this->quota = $this->data['quota'] = $entries[0]['phpgwquota'];
			}
			return $this->data;
		}

		public function save_repository()
		{
			$acct_type = $this->get_type($this->account_id);

			if ($acct_type == 'g')
			{
				return $this->create_group($this->data, '');
			}
			else
			{
				return $this->create_account($this->data, '');
			}
		}

		/**
		* Reads groups into an array
		*
		* @param string $query LDAP query
		* @return array Array with group fields 'account_id', 'account_lid', 'account_type'
		*/
		protected function _get_group_list($query)
		{
			$groups = array();

			if(empty($query) || $query == "*")
			{
				$filter = '(&(gidnumber=*)(objectclass=posixgroup))';
			}
			else
			{
				$filter = "(&(gidnumber=*)(objectclass=posixgroup)(|(uid=*$query*)(sn=*$query*)(cn=*$query*)(givenname=*$query*)))";
			}
			$sri = ldap_search($this->ds, $this->group_context, $filter);
			$entries = ldap_get_entries($this->ds, $sri);

			if (!is_array($entries) )
			{
				return $groups;
			}

			foreach ( $entries as $entry )
			{
				if ( !strlen($test = $entry['cn'][0]) )
				{
					continue;
				}

				if ( !isset($GLOBALS['phpgw_info']['server']['global_denied_groups'][$test]) )
				{
					$groups[] = array
					(
						'account_id'        => $entry['gidnumber'][0],
						'account_lid'       => $entry['cn'][0],
						'account_firstname' => $entry['cn'][0],
						'account_lastname'  => lang('group'),
						'account_type'      => 'g',
					);
				}
			}
			return $groups;
		}

		protected function _get_homedirectory($newValue, $login)
		{
			if ($newValue != '' && $newValue != $GLOBALS['phpgw_info']['server']['ldap_account_home'])
			{
				$return = $newValue;
			}
			else
			{
				if ($GLOBALS['phpgw_info']['server']['ldap_account_home'] != '')
				{
					$return = "{$GLOBALS['phpgw_info']['server']['ldap_account_home']}/{$login}";
				}
				else
				{
					$return = self::FALLBACK_HOMEDIRECTORY;
				}
			}
			return $return;
		}

		/**
		* Distinguised name leaf name generation
		*
		* @param string $first Firstname
		* @param string $last Lastname
		* @param string $login Login name
		* @return string Generated name of leaf of the distinguised name
		*/
		public function _get_leaf_name($first, $last, $login)
		{
			return $login;
		}

		protected function _get_loginshell($newValue)
		{
			if ($newValue != '')
			{
				$return = $newValue;
			}
			else
			{
				if ($GLOBALS['phpgw_info']['server']['ldap_account_shell'] != '')
				{
					$return = $GLOBALS['phpgw_info']['server']['ldap_account_shell'];
				}
				else
				{
					$return = self::FALLBACK_LOGINSHELL;
				}
			}
			return $return;
		}

		// FIXME replace this with an existing method ?
		protected function _get_member_uids($account_id = '')
		{
			if ( !empty($account_id) )
			{
   			$members = $this->member($account_id);
			}
			else
			{
   			$members = $this->member($this->data['account_id']);
			}
			$return = array();
			for ($i=0; $i<count($members); $i++)
			{
				$member = $this->id2name($members[$i]['account_id']);
				// function $this->member returns duplicated entries and empty entries :-(
				if (!in_array($member, $return) && $member != '')
				{
					$return[] = $member;
				}
			}
			if (count($return))
			{
				return $return;
			}
			else
			{
				return false;
			}
		}

		/**
		* Read accounts into an array
		*
		* @param string $query LDAP query
		* @return array Array with account fields 'acount_id', 'account_lid', 'account_type', 'account_firstname', 'account_lastname', 'account_status'
		*/
		protected function _get_user_list($query)
		{
			$accounts = array();
			if(empty($query) || $query == "*")
			{
				$filter = '(&(uidnumber=*)(objectclass=posixaccount))';
			}
			else
			{
				//escaping
				$query = str_replace('\\', '\\\\', $query);
				$filter = "(&(uidnumber=*)(objectclass=posixaccount)(|(uid=*$query*)(sn=*$query*)(cn=*$query*)(givenname=*$query*)))";
			}

			$sri = ldap_search($this->ds, $this->user_context, $filter);
			$entries = ldap_get_entries($this->ds, $sri);
			if ( !is_array($entries) )
			{
				return $accounts;
			}

			foreach ( $entries as $entry )
			{
				if ( !strlen($test = $entry['uid'][0]) )
				{
					continue;
				}

				if (!isset($GLOBALS['phpgw_info']['server']['global_denied_users'][$test]) )
				{
					$accounts[] = array
					(
						'account_id'        => $entry['uidnumber'][0],
						'account_lid'       => $entry['uid'][0],
						'account_type'      => 'u',
						'account_firstname' => $entry['givenname'][0],
						'account_lastname'  => $entry['sn'][0],
						'account_status'    => $entry['phpgwaccountstatus'][0] == 'A' ? 'A' : 'I'
					);
				}
			}
			//echo '<pre>' . print_r($accounts, true) .'</pre>';
			return $accounts;
		}

		/**
		* Convert group name to the corresponding id
		*
		* @param string $name Group name for which you want the id
		* @return integer|boolean Id of the group when found otherwise false
		*/
		protected function _group_name2id($name)
		{
			$sri = ldap_search($this->ds, $this->group_context, "(&({$this->rdn_group}={$name})(objectclass=phpgwgroup))");
			$allValues = ldap_get_entries($this->ds, $sri);

			//echo "searching for name {$name} <pre>" . print_r($allValues, true) . '</pre>';
			if ( isset($allValues[0]['gidnumber'][0]) )
			{
				return (int) $allValues[0]['gidnumber'][0];
			}
			else
			{
				return false;
			}
		}

		/**
		* Convert account name into corresponding id
		*
		* @param string $name Account name
		* @return integer|boolean Id of the account when found otherwise false
		*/
		protected function _user_name2id($name)
		{
			$sri = ldap_search($this->ds, $this->user_context, "(&({$this->rdn_account}={$name})(objectclass=phpgwaccount))");
			$allValues = ldap_get_entries($this->ds, $sri);

			if ( isset($allValues[0]['uidnumber'][0]) )
			{
				return (int) $allValues[0]['uidnumber'][0];
			}
			else
			{
				return false;
			}
		}
	}
