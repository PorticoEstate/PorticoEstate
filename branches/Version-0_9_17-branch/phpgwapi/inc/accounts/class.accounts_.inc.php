<?php
	/**
	* Shared functions for other account repository managers and loader
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Bettina Gille <ceb@phpgroupware.org>
	* @author Philipp Kamps <pkamps@probusiness.de>
	* @copyright Copyright (C) 2000-2004 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage accounts
	* @version $Id: class.accounts_.inc.php 18358 2007-11-27 04:43:37Z skwashd $
	*/
	if (empty($GLOBALS['phpgw_info']['server']['account_repository']))
	{
		if (!empty($GLOBALS['phpgw_info']['server']['auth_type']))
		{
			$GLOBALS['phpgw_info']['server']['account_repository'] = $GLOBALS['phpgw_info']['server']['auth_type'];
		}
		else
		{
			$GLOBALS['phpgw_info']['server']['account_repository'] = 'sql';
		}
	}
	/**
	* Include child class
	*/

	/**
	* @ignore 
	* @global array list of banned user account names
	*/
	$GLOBALS['phpgw_info']['server']['global_denied_users'] = array
	(
		'adm'			=> true,
		'alias'			=> true,
		'amanda'		=> true,
		'apache'		=> true,
		'avahi'			=> true,
		'backup'		=> true,
		'backup'		=> true,
		'beagleindex'	=> true,
		'bin'			=> true,
		'cupsys'		=> true,
		'cvs'			=> true,
		'cyrus'			=> true,
		'daemon'		=> true,
		'dhcp'			=> true,
		'dnsmasq'		=> true,
		'fetchmail'		=> true,
		'ftp'			=> true,
		'games'			=> true,
		'gdm'			=> true,
		'gnats'			=> true,
		'gopher'		=> true,
		'haldaemon'		=> true,
		'hal'			=> true,
		'halt'			=> true,
		'hplip'			=> true,
		'ident'			=> true,
		'irc'			=> true,
		'klog'			=> true,
		'ldap'			=> true,
		'list'			=> true,
		'lp'			=> true,
		'mailnull'		=> true,
		'mail'			=> true,
		'messagebus'	=> true,
		'mysql'			=> true,
		'named'			=> true,
		'news'			=> true,
		'nobody'		=> true,
		'nscd'			=> true,
		'operator'		=> true,
		'oracle'		=> true,
		'pgsql'			=> true,
		'postfix'		=> true,
		'postgres'		=> true,
		'proxy'			=> true,
		'pvm'			=> true,
		'qmaild'		=> true,
		'qmaillog'		=> true,
		'qmaill'		=> true,
		'qmailp'		=> true,
		'qmailq'		=> true,
		'qmailr'		=> true,
		'qmails'		=> true,
		'root'			=> true,
		'rpc'			=> true,
		'rpcuser'		=> true,
		'sabayon-admin'	=> true,
		'saned'			=> true,
		'shutdown'		=> true,
		'squid'			=> true,
		'sshd'			=> true,
		'sweep'			=> true,
		'sync'			=> true,
		'syslog'		=> true,
		'sys'			=> true,
		'uucp'			=> true,
		'web'			=> true,
		'www-data'		=> true,
		'xfs'			=> true
	);

	/**
	* @ignore 
	* @global array list of banned user group names
	*/
	$GLOBALS['phpgw_info']['server']['global_denied_groups'] = array
	(
		'admin'			=> true,
		'adm'			=> true,
		'audio'			=> true,
		'avahi'			=> true,
		'backup'		=> true,
		'bin'			=> true,
		'cdrom'			=> true,
		'console'		=> true,
		'crontab'		=> true,
		'cvs'			=> true,
		'daemon'		=> true,
		'dba'			=> true,
		'dhcp'			=> true,
		'dialout'		=> true,
		'dip'			=> true,
		'dirmngr'		=> true,
		'disk'			=> true,
		'dnstools'		=> true,
		'fax'			=> true,
		'floppy'		=> true,
		'ftp'			=> true,
		'games'			=> true,
		'gdm'			=> true,
		'gnats'			=> true,
		'haldaemon'		=> true,
		'hal'			=> true,
		'irc'			=> true,
		'klog'			=> true,
		'kmem'			=> true,
		'ldap'			=> true,
		'list'			=> true,
		'lpadmin'		=> true,
		'lp'			=> true,
		'lp'			=> true,
		'mail'			=> true,
		'man'			=> true,
		'messagebus'	=> true,
		'mysql'			=> true,
		'named'			=> true,
		'news'			=> true,
		'nobody'		=> true,
		'nofiles'		=> true,
		'nogroup'		=> true,
		'oinstall'		=> true,
		'operator'		=> true,
		'oracle'		=> true,
		'plugdev'		=> true,
		'popusers'		=> true,
		'postdrop'		=> true,
		'postfix'		=> true,
		'postgres'		=> true,
		'pppusers'		=> true,
		'proxy'			=> true,
		'qmail'			=> true,
		'root'			=> true,
		'sabayon-admin'	=> true,
		'saned'			=> true,
		'sasl'			=> true,
		'scanner'		=> true,
		'shadow'		=> true,
		'slipusers'		=> true,
		'slocate'		=> true,
		'src'			=> true,
		'ssh'			=> true,
		'ssl-cert'		=> true,
		'staff'			=> true,
		'sudo'			=> true,
		'sweep'			=> true,
		'syslog'		=> true,
		'sys'			=> true,
		'tape'			=> true,
		'tty'			=> true,
		'users'			=> true,
		'utmp'			=> true,
		'uucp'			=> true,
		'video'			=> true,
		'voice'			=> true,
		'web'			=> true,
		'wheel'			=> true,
		'www-data'		=> true,
		'xfs'			=> true,
	);

	/**
	* Class for handling user and group accounts
	*
	* @package phpgwapi
	* @subpackage accounts
	* @abstract
	*/
	class accounts_
	{
		var $account_id;
		var $lid;
		var $firstname;
		var $lastname;
		var $password;
		var $status;
		var $expires;
		var $person_id;
		/**
		* @var int the user's quota in Mb - i think
		*/
		var $quota = 0; //sane default
		var $data;
		var $db;
		var $memberships = array();
		var $members = array();
		var $total;
		var $xmlrpc_methods = array();

		/**
		* Standard constructor for setting account_id
		*
		* This constructor sets the account id, if string is set, converts to id
		* @param integer $account_id Account id defaults to current account id
		* @param string $account_type Account type 'u': account; 'g' : group; defaults to current account type
		* @internal I might move this to the accounts_shared if it stays around
		*/
		function __construct($account_id = null, $account_type = null)
		{
			$this->db =& $GLOBALS['phpgw']->db;
			$this->like = $this->db->like;
			
			// FIXME move me to a proper instance variable
			$this->xmlrpc_methods[] = array(
				'name'        => 'get_list',
				'description' => 'Returns a list of accounts and/or groups'
			);
			$this->xmlrpc_methods[] = array(
				'name'        => 'name2id',
				'description' => 'Cross reference account_lid with account_id'
			);
			$this->xmlrpc_methods[] = array(
				'name'        => 'id2name',
				'description' => 'Cross reference account_id with account_lid'
			);

			$this->set_account($account_id, $account_type);
		}

		public function set_account($account_id = null, $account_type = null)
		{
			if ( !is_null($account_id) )
			{
				$this->account_id = get_account_id($account_id);
			}

			if( !is_null($account_type))
			{
				$this->account_type = $account_type;
			}
		}

		function sync_accounts_contacts()
		{
			$accounts = $this->get_account_without_contact();

			if(is_array($accounts))
			{
				$contacts = createObject('phpgwapi.contacts');
				
				foreach($accounts as $account)
				{
					//$this->get_account_name($account,$lid,$fname,$lname);
					if($account)
					{
						$this->account_id = $account;
						$user_account = $this->read_repository();
						$principal = array('per_prefix'     => $user_account['account_lid'],
								   'per_first_name' => $user_account['firstname'],
								   'per_last_name'  => $user_account['lastname'],
								   'access'	    => 'public',
								   'owner'	    => $GLOBALS['phpgw_info']['server']['addressmaster']);
						$contact_type = $contacts->search_contact_type('Persons');
						$user_account['person_id'] = $contacts->add_contact($contact_type, $principal);
						$this->update_data($user_account);
						$this->save_repository();
					}
				}
			}
		}

		function save_contact_for_account($userData)
		{
			$owner = $GLOBALS['phpgw_info']['server']['addressmaster'];
			$contacts = createObject('phpgwapi.contacts');
			$type = $contacts->search_contact_type('Persons');

			$comms=(is_array($userData['extra_contact']['comms'])) ? $userData['extra_contact']['comms'] : false;
						$principal=(is_array($userData['extra_contact']['principal'])) ? $userData['extra_contact']['principal'] : false;
						$locations=(is_array($userData['extra_contact']['locations'])) ? $userData['extra_contact']['locations'] : false;
						$categories=(is_array($userData['extra_contact']['categories'])) ? $userData['extra_contact']['categories'] : false;
						$others=(is_array($userData['extra_contact']['others'])) ? $userData['extra_contact']['others'] : false;
						$notes=(is_array($userData['extra_contact']['notes'])) ? $userData['extra_contact']['notes'] : false;
						$relationship=(is_array($userData['extra_contact']['relationship'])) ? $userData['extra_contact']['relationship'] : false;

			$principal['owner'] = $owner;
						$principal['access']= 'public';
						$principal['per_prefix'] = $userData['account_lid'];
						$principal['per_first_name'] = $userData['account_firstname'];
						$principal['per_last_name'] = $userData['account_lastname'];
			
			if(isset($userData['domain']))
						{
								$domain=$userData['domain'];
						}
						else
						{
								$domain=$GLOBALS['phpgw_info']['server']['mail_server'];
						}
						if($domain)//Attempts to grab domain succeded
						{
								$comm['comm_descr']=$contacts->search_comm_descr('work email');
								$comm['comm_data']=$userData['account_lid'].'@'.$domain;
								$comm['comm_preferred']='Y';
				$comms = array($comm);
						}
						else
						{
								$comms='';
						}

			if ($userData['person_id'] && $contacts->exist_contact($userData['person_id']))
			{
				$contacts->edit_person($userData['person_id'], $principal);
				$person_id = $userData['person_id'];
			}
			else
			{
				$person_id = $contacts->add_contact($type, $principal,$comms,$locations,$categories,$others,$relationship,$notes);
			}
			$this->account_contact_id = $person_id;
			return $person_id;
		}
		
		function is_expired()
		{
			if ($this->data['expires'] != -1 && $this->data['expires'] < time())
			{
				return true;
			}
			else
			{
				return False;
			}
		}

		function read()
		{
			if (count($this->data) == 0)
			{
				$this->read_repository();
			}

			reset($this->data);
			return $this->data;
		}

		function update_data($data)
		{
			reset($data);
			$this->data = Array();
			$this->data = $data;

			reset($this->data);
			return $this->data;
		}

		function membership($accountid = '')
		{
			$account_id = get_account_id($accountid);

			$security_equals = Array();
			$security_equals = $GLOBALS['phpgw']->acl->get_location_list_for_id('phpgw_group', 1, $account_id);

			if ( !$security_equals )
			{
				return false;
			}

			$this->memberships = array();

			for ($idx=0; $idx<count($security_equals); $idx++)
			{
				$groups = intval($security_equals[$idx]);
				$this->memberships[] = Array('account_id' => $groups, 'account_name' => $this->id2name($groups));
			}

			return $this->memberships;
		}

		function member($accountid = '')
		{
			$account_id = get_account_id($accountid);

			$security_equals = Array();
			$acl = createObject('phpgwapi.acl');
			$security_equals = $acl->get_ids_for_location($account_id, 1, 'phpgw_group');
			unset($acl);

			if ($security_equals == False)
			{
				return False;
			}

			for ($idx=0; $idx<count($security_equals); $idx++)
			{
				$name = $this->id2name(intval($security_equals[$idx]));
				$this->members[] = Array('account_id' => intval($security_equals[$idx]), 'account_name' => $name);
			}

			return $this->members;
		}

		/**
		* Get a list of members of the current group
		*
		* @return arrray list of members of the current group
		*/
		function get_members()
		{
			$members = array();
			$sql = "SELECT acl_account FROM phpgw_acl WHERE acl_appname = 'phpgw_group' and acl_location =" . (int) $this->account_id;
			$this->db->query($sql,__LINE__,__FILE__);
			while ($this->db->next_record())
			{
				$members[] =  $this->db->f('acl_account');
			}
			return $members;
		}


		/**
		* Find the next available account_id
		*
		* @param string $account_type Account type 'u' : user; 'g' : group
		* @return integer New account id
		*/
		function get_nextid($account_type='u')
		{
			$min = $GLOBALS['phpgw_info']['server']['account_min_id'] ? $GLOBALS['phpgw_info']['server']['account_min_id'] : 0;
			$max = $GLOBALS['phpgw_info']['server']['account_max_id'] ? $GLOBALS['phpgw_info']['server']['account_max_id'] : 0;

			if ($account_type == 'g')
			{
				$type = 'groups';
			}
			else
			{
				$type = 'accounts';
			}
			$nextid = intval($GLOBALS['phpgw']->common->last_id($type,$min,$max));

			/* Loop until we find a free id */
			$free = 0;
			while (!$free)
			{
				$account_lid = '';
				//echo '<br />calling search for id: '.$nextid;
				if ($this->exists($nextid))
				{
					$nextid = intval($GLOBALS['phpgw']->common->next_id($type,$min,$max));
				}
				else
				{
					$account_lid = $this->id2name($nextid);
					/* echo '<br />calling search for lid: '.$account_lid . '(from account_id=' . $nextid . ')'; */
					if ($this->exists($account_lid))
					{
						$nextid = intval($GLOBALS['phpgw']->common->next_id($type,$min,$max));
					}
					else
					{
						$free = true;
					}
				}
			}
			if	($GLOBALS['phpgw_info']['server']['account_max_id'] &&
				($nextid > $GLOBALS['phpgw_info']['server']['account_max_id']))
			{
				return False;
			}
			/* echo '<br />using'.$nextid;exit; */
			return $nextid;
		}

		/**
		* Get an array of users and groups seperated, including all members of groups, which i.e. have acl access for an application
		*
		* @param array|integer $app_users Array with user/group names
		* @return array 'users' contains the user names for the given group or application
		*/
		function return_members($app_users = 0)
		{
			$members = array();
			for ($i = 0;$i<count($app_users);$i++)
			{
				$type = $GLOBALS['phpgw']->accounts->get_type($app_users[$i]);
				if($type == 'g')
				{
					$add_users['groups'][] = $app_users[$i];
					$memb = $GLOBALS['phpgw']->acl->get_ids_for_location($app_users[$i],1,'phpgw_group');

					if(is_array($memb))
					{
						$members[] = $memb;
					}
				}
				else
				{
					$add_users['users'][] = $app_users[$i];
				}
			}

			if ( !isset($addusers['users']) || !is_array($add_users['users']))
			{
				$add_users['users'] = array();
			}

			$i = count($add_users['users']);

			while(is_array($members) && (list(,$mem) = each($members)))
			{
				for($j=0;$j<count($mem);$j++)
				{
					if(!in_array($mem[$j],$add_users['users']))
					{
						$add_users['users'][$i] = $mem[$j];
						$i++;
					}
				}
			}
			return $add_users;
		}

		function accounts_popup($app)
		{
			$group_id = phpgw::get_var('group_id', 'int');

			$query = phpgw::get_var('query', 'string', 'POST');
			$start = phpgw::get_var('start', 'int', 'POST');
			$order = phpgw::get_var('order', 'string', 'POST', 'account_lid');
			$sort = phpgw::get_var('sort', 'string', 'POST', 'ASC');

			$this->nextmatchs = createObject('phpgwapi.nextmatchs');

			$GLOBALS['phpgw']->template->set_root(PHPGW_TEMPLATE_DIR);

			$GLOBALS['phpgw']->template->set_file(array('accounts_list_t' => 'accounts_popup.tpl'));
			$GLOBALS['phpgw']->template->set_block('accounts_list_t','group_select','select');
			$GLOBALS['phpgw']->template->set_block('accounts_list_t','group_other','other');
			$GLOBALS['phpgw']->template->set_block('accounts_list_t','group_all','all');

			$GLOBALS['phpgw']->template->set_block('accounts_list_t','withperm_intro','withperm');
			//$GLOBALS['phpgw']->template->set_block('accounts_list_t','other_intro','iother');
			$GLOBALS['phpgw']->template->set_block('accounts_list_t','withoutperm_intro','withoutperm');


			$GLOBALS['phpgw']->template->set_block('accounts_list_t','accounts_list','list');


			$GLOBALS['phpgw']->template->set_var('title', isset($GLOBALS['phpgw_info']['site_title']) ? $GLOBALS['phpgw_info']['site_title'] : '');
			$GLOBALS['phpgw']->template->set_var('charset', 'urf-8');
			$GLOBALS['phpgw']->template->set_var('lang_search',lang('search'));
			$GLOBALS['phpgw']->template->set_var('lang_groups',lang('user groups'));
			$GLOBALS['phpgw']->template->set_var('lang_accounts',lang('user accounts'));

			$GLOBALS['phpgw']->template->set_var('img',$GLOBALS['phpgw']->common->image('phpgwapi','select'));
			$GLOBALS['phpgw']->template->set_var('lang_select_user',lang('Select user'));
			$GLOBALS['phpgw']->template->set_var('lang_select_group',lang('Select group'));
			$GLOBALS['phpgw']->template->set_var('css_file',$GLOBALS['phpgw_info']['server']['webserver_url'] . SEP . 'phpgwapi' . SEP . 'templates'
															. SEP . 'idots' . SEP . 'css' . SEP . 'idots.css');

			switch($app)
			{
				case 'admin':
					$action = 'admin.uiaccounts.accounts_popup';
					$GLOBALS['phpgw']->template->set_var('select_name',"account_user[]']");
					$GLOBALS['phpgw']->template->set_var('js_function','ExchangeAccountSelect');
					$GLOBALS['phpgw']->template->set_var('lang_perm',lang('group name'));
					$GLOBALS['phpgw']->template->fp('withperm','withperm_intro',true);
					break;
				case 'admin_acl':
					$action = 'admin.uiaclmanager.accounts_popup';
					$app = 'addressbook';
					$GLOBALS['phpgw']->template->set_var('select_name',"account_addressmaster[]']");
					$GLOBALS['phpgw']->template->set_var('js_function','ExchangeAccountSelect');
					$GLOBALS['phpgw']->template->fp('withperm','withperm_intro',true);
					$GLOBALS['phpgw']->template->fp('withoutperm','withoutperm_intro',true);
					break;
				case 'projects':
					$action = 'projects.uiprojects.accounts_popup';
					$GLOBALS['phpgw']->template->set_var('select_name',"values[coordinator]']");
					$GLOBALS['phpgw']->template->set_var('js_function','ExchangeAccountText');
					$GLOBALS['phpgw']->template->fp('withperm','withperm_intro',true);
					$GLOBALS['phpgw']->template->fp('withoutperm','withoutperm_intro',true);
					break;
				case 'e_projects':
					$action = 'projects.uiprojects.e_accounts_popup';
					$app = 'projects';
					$GLOBALS['phpgw']->template->set_var('select_name',"employees[]']");
					$GLOBALS['phpgw']->template->set_var('js_function','ExchangeAccountSelect');
					$GLOBALS['phpgw']->template->fp('withperm','withperm_intro',true);
					$GLOBALS['phpgw']->template->fp('withoutperm','withoutperm_intro',true);
					break;
			}

			$GLOBALS['phpgw']->template->set_var('lang_perm',lang('Groups with permission for %1',lang($app)));
			$GLOBALS['phpgw']->template->set_var('lang_nonperm',lang('Groups without permission for %1',lang($app)));

			$link_data = array
			(
				'menuaction'	=> $action,
				'group_id'		=> $group_id
			);

			$app_groups = array();

			if ($app != 'admin')
			{
				$user_groups = $this->membership($this->account_id);
				$aclusers = $GLOBALS['phpgw']->acl->get_ids_for_location('run', 1, $app);
				$acl_users = $this->return_members($aclusers);
				$app_user	= $acl_users['users'];
				$app_groups	= $acl_users['groups'];
				/*
				$app_groups	= $this->get_list('groups');
				$app_user	= $this->get_list('accounts');
				*/

			}
			else
			{
				$all_groups	= $this->get_list('groups');
				$all_user	= $this->get_list('accounts');

				while(is_array($all_groups) && (list(,$agroup) = each($all_groups)))
				{
					$user_groups[] = array
					(
						'account_id'	=> $agroup['account_id'],
						'account_name'	=> $agroup['account_firstname']
					);
				}

				$i = 0;
				for($j=0;$j<count($user_groups); ++$j)
				{
					$app_groups[$i] = $user_groups[$j]['account_id'];
					++$i;
				}

				for($j=0;$j<count($all_user);$j++)
				{
					$app_user[$i] = $all_user[$j]['account_id'];
					++$i;
				}
			}

			while ( isset($user_groups) && is_array($user_groups) && (list(,$group) = each($user_groups)) )
			{
				$i = 0;
				if (in_array($group['account_id'], $app_groups))
				{
					$GLOBALS['phpgw']->template->set_var('tr_class', $this->nextmatchs->alternate_row_class(++$i%2));
					//$link_data['group_id'] = $group['account_id'];
					$GLOBALS['phpgw']->template->set_var('link_user_group', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $action, 'group_id' => (int)$group['account_id']) ) );
					$GLOBALS['phpgw']->template->set_var('name_user_group', $group['account_name']);
					$GLOBALS['phpgw']->template->set_var('account_display', $GLOBALS['phpgw']->common->grab_owner_name($group['account_id']));
					$GLOBALS['phpgw']->template->set_var('accountid', $group['account_id']);
					switch($app)
					{
						case 'addressbook':
						default:
							$GLOBALS['phpgw']->template->fp('other','group_other',true);
					}
				}
				else
				{
					if ($app != 'admin')
					{
						$GLOBALS['phpgw']->template->set_var('link_all_group', $GLOBALS['phpgw']->link('/index.php', array('menuaction' => $action, 'group_id' => (int)$group['account_id']) ) );
						$GLOBALS['phpgw']->template->set_var('name_all_group', $group['account_name']);
						$GLOBALS['phpgw']->template->set_var('accountid', $group['account_id']);
						$GLOBALS['phpgw']->template->fp('all', 'group_all', true);
					}
				}
			}

			if ( !$query )
			{
				$val_users = array();
				if (isset($group_id) && !empty($group_id))
				{
					//echo 'GROUP_ID: ' . $group_id;
					$users = $GLOBALS['phpgw']->acl->get_ids_for_location($group_id,1,'phpgw_group');

					for ($i=0;$i<count($users); ++$i)
					{
						if (in_array($users[$i],$app_user))
						{
							$GLOBALS['phpgw']->accounts->account_id = $users[$i];
							$GLOBALS['phpgw']->accounts->read_repository();

							switch ($order)
							{
								case 'account_firstname':
									$id = $GLOBALS['phpgw']->accounts->data['firstname'];
									break;
								case 'account_lastname':
									$id = $GLOBALS['phpgw']->accounts->data['lastname'];
									break;
								case 'account_lid':
								default:
									$id = $GLOBALS['phpgw']->accounts->data['account_lid'];
									break;
							}
							$id .= $GLOBALS['phpgw']->accounts->data['lastname'];	// default sort-order
							$id .= $GLOBALS['phpgw']->accounts->data['firstname'];
							$id .= $GLOBALS['phpgw']->accounts->data['account_id'];	// make our index unique

							$val_users[$id] = array
							(
								'account_id'		=> $GLOBALS['phpgw']->accounts->data['account_id'],
								'account_firstname'	=> $GLOBALS['phpgw']->accounts->data['firstname'],
								'account_lastname'	=> $GLOBALS['phpgw']->accounts->data['lastname']
							);
						}
					}

					if (is_array($val_users))
					{
						if ($sort != 'DESC')
						{
							ksort($val_users);
						}
						else
						{
							krsort($val_users);
						}
					}
					$val_users = array_values($val_users);	// get a numeric index
				}
				$total = count($val_users);
			}
			else
			{
				switch($app)
				{
					case 'calendar':	$select = 'both'; break;
					default:			$select = 'accounts'; break;
				}
				$entries	= $this->get_list($select, $start, $sort, $order, $query);
				$total		= $this->total;
				for ($i=0;$i<count($entries);$i++)
				{
					if (in_array($entries[$i]['account_id'],$app_user))
					{
						$val_users[] = array
						(
							'account_id'		=> $entries[$i]['account_id'],
							'account_firstname'	=> $entries[$i]['account_firstname'],
							'account_lastname'	=> $entries[$i]['account_lastname']
						);
					}
				}
			}

// --------------------------------- nextmatch ---------------------------

			$left = $this->nextmatchs->left('/index.php',$start,$total,$link_data);
			$right = $this->nextmatchs->right('/index.php',$start,$total,$link_data);
			$GLOBALS['phpgw']->template->set_var('left',$left);
			$GLOBALS['phpgw']->template->set_var('right',$right);

			$GLOBALS['phpgw']->template->set_var('lang_showing',$this->nextmatchs->show_hits($total,$start));

// -------------------------- end nextmatch ------------------------------------

			$GLOBALS['phpgw']->template->set_var('search_action',$GLOBALS['phpgw']->link('/index.php',$link_data));
			$GLOBALS['phpgw']->template->set_var('search_list',$this->nextmatchs->search(array('query' => $query, 'search_obj' => 1)));

// ---------------- list header variable template-declarations --------------------------

// -------------- list header variable template-declaration ------------------------
			$GLOBALS['phpgw']->template->set_var('sort_lid',$this->nextmatchs->show_sort_order($sort,'account_lid',$order,'/index.php',lang('LoginID'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_firstname',$this->nextmatchs->show_sort_order($sort,'account_firstname',$order,'/index.php',lang('Firstname'),$link_data));
			$GLOBALS['phpgw']->template->set_var('sort_lastname',$this->nextmatchs->show_sort_order($sort,'account_lastname',$order,'/index.php',lang('Lastname'),$link_data));

// ------------------------- end header declaration --------------------------------
			$stop = $start + $this->nextmatchs->maxmatches;
			for ($i=$start;$i<count($val_users)&&$i<$stop;$i++)
			{
				$GLOBALS['phpgw']->template->set_var('tr_class', $this->nextmatchs->alternate_row_class($i%2));
				
				$firstname = $val_users[$i]['account_firstname'];
				if (!$firstname)
				{
					$firstname = '&nbsp;';
				}
				
				$lastname = $val_users[$i]['account_lastname'];
				if (!$lastname)
				{
					$lastname = '&nbsp;';
				}

// ---------------- template declaration for list records -------------------------- 

				$GLOBALS['phpgw']->template->set_var(array
				(
					'firstname'			=> $firstname,
					'lastname'			=> $lastname,
					'accountid'			=> $val_users[$i]['account_id'],
					'account_display'	=> $GLOBALS['phpgw']->common->grab_owner_name($val_users[$i]['account_id'])
				));

				$GLOBALS['phpgw']->template->fp('list','accounts_list',true);
			}

			$GLOBALS['phpgw']->template->set_var('start', $start);
			$GLOBALS['phpgw']->template->set_var('sort', $sort);
			$GLOBALS['phpgw']->template->set_var('order', $order);
			$GLOBALS['phpgw']->template->set_var('query', $query);
			$GLOBALS['phpgw']->template->set_var('group_id', $group_id);

			$GLOBALS['phpgw']->template->set_var('lang_done',lang('done'));
			$GLOBALS['phpgw']->template->pfp('out','accounts_list_t',true);
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		
		/**
		* Add an account to a group entry by adding the account name to the memberuid attribute
		*
		* @param integer $accountID Account id
		* @param integer $groupID Group id
		* @return boolean true on success otherwise false
		* @internal Required for LDAP support
		*/
		function add_account2Group($groupID)
		{
			return true;
		}
		
		/**
		* Delete an account for a group entry by removing the account name from the memberuid attribute
		*
		* @param integer $accountID Account id
		* @param integer $groupID Group id
		* @return boolean true on success otherwise false
		* @internal Required for LDAP support
		*/
		function delete_account4Group($groupID)
		{
			return true;
		}
		
		function create($data, $default_prefs = true)
		{
			if($data['account_id'] && is_object($GLOBALS['phpgw']->preferences) && $default_prefs)
			{
				$GLOBALS['phpgw']->preferences->create_defaults($data['account_id']);
			}
			return $data['account_id'];
		}
		
		function set_data($data)
		{
			$this->account_id		= isset($data['account_id']) ? (int)$data['account_id'] : $this->account_id;
			$this->lid				= isset($data['account_lid']) ? $data['account_lid'] : $this->lid;
			$this->firstname		= $data['account_firstname'] ? $data['account_firstname'] : $this->firstname;
			$this->lastname			= $data['account_lastname'] ? $data['account_lastname'] : $this->lastname;
			$this->password			= $data['account_passwd'] ? $data['account_passwd'] : $this->password;
			$data['account_status']	= !$data['account_status'] ? $data['status'] : $data['account_status'];
			$this->status			= $data['account_status'] ? $data['account_status'] : $this->status;
			$data['account_expires']= !$data['account_expires'] ? $data['expires'] : $data['account_expires'];
			$this->expires			= $data['account_expires'] ? $data['account_expires'] : $this->expires;
			$this->person_id		= $data['person_id'] ? $data['person_id'] : $this->person_id;
			$this->quota			= isset($data['quota']) ? (int)$data['quota'] : $this->quota;
			return true;
		}
		
		function get_account_data($account_id)
		{
			$this->account_id = $account_id; // what is this good for? (get is not set)
			$this->read_repository();

			$data[$this->data['account_id']]['lid']       = $this->data['account_lid'];
			$data[$this->data['account_id']]['firstname'] = $this->data['firstname'];
			$data[$this->data['account_id']]['lastname']  = $this->data['lastname'];
			$data[$this->data['account_id']]['fullname']  = $this->data['fullname'];
			
			// type or account_type, this is the question
			if ( isset($this->data['account_type']) && strlen($this->data['account_type']) )
			{
				$data[$this->data['account_id']]['type'] =  $this->data['account_type'];
			}
			else if ( isset($this->data['type']) )
			{
				$data[$this->data['account_id']]['type'] = $this->data['type'];
			}
			else
			{
				$data[$this->data['account_id']]['type'] = 'u';
			}
			$data[$this->data['account_id']]['person_id'] = $this->data['person_id'];
			return $data;
		}

		/**
		* Create a non existing but authorized user 
		*
		* @param string $accountname User name
		* @param string $passwd User password
		* @param boolean $default_prefs Default preferences for this new user
		* @param boolean $default_acls Acls (modules) for this new user
		* @param integer $expiredate Expire date of this account. '-1' for never. Defaults to 'in 30 days'
		* @param char $account_status Status for new user. 'A' for active user.
		* @return integer Account id 
		*/
		function auto_add($accountname, $passwd, $default_prefs = false, $default_acls = false, $expiredate = 0, $account_status = 'A')
		{
			if ($expiredate)
			{
				$expires = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
			}
			else
			{
				if($GLOBALS['phpgw_info']['server']['auto_create_expire'])
				{
					if($GLOBALS['phpgw_info']['server']['auto_create_expire'] == 'never')
					{
						$expires = -1;
					}
					else
					{
						$expiredate = time() + $GLOBALS['phpgw_info']['server']['auto_create_expire'];
						$expires   = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
					}
				}
				else
				{
					/* expire in 30 days by default */
					$expiredate = time() + ( ( 60 * 60 ) * (30 * 24) );
					$expires   = mktime(2,0,0,date('n',$expiredate), intval(date('d',$expiredate)), date('Y',$expiredate));
				}
			}

			$acct_info = array(
				'account_lid'       => $accountname,
				'account_type'      => 'u',
				'account_passwd'    => $passwd,
				'account_firstname' => '',
				'account_lastname'  => '',
				'account_status'    => $account_status,
				'account_expires'   => $expires,
				'person_id'         => 'NULL'
			);

			$this->db->transaction_begin();
			$this->create($acct_info, $default_prefs);
			$accountid = $this->name2id($accountname); //slow - a create should set the new accountid

			// this should be done via the acl class not direct db calls
			if ($default_acls == false)
			{
				$default_group_lid = intval($GLOBALS['phpgw_info']['server']['default_group_lid']);
				$default_group_id  = $this->name2id($default_group_lid);
				$defaultgroupid = $default_group_id ? $default_group_id : $this->name2id('Default');
				if ($defaultgroupid)
				{
					$this->db->query('INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights)'
						. "VALUES('phpgw_group', " . $defaultgroupid . ', ' 
						.	intval($accountid) . ', 1'
						. ')',__LINE__,__FILE__);
					$this->db->query('INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights)'
						. "VALUES('preferences' , 'changepassword', " 
						.	intval($accountid) . ', 1'
						. ')',__LINE__,__FILE__);
				}
				else
				{
					// If they don't have a default group, they need some sort of permissions.
					// This generally doesn't / shouldn't happen, but will (jengo)
					$this->db->query("insert into phpgw_acl (acl_appname, acl_location, acl_account, acl_rights) values('preferences', 'changepassword', " . $accountid . ', 1)',__LINE__,__FILE__);

					$apps = Array(
						'addressbook',
						'calendar',
						'email',
						'notes',
						'todo',
						'phpwebhosting',
						'manual'
					);

					@reset($apps);
					while(list($key,$app) = each($apps))
					{
						$this->db->query("INSERT INTO phpgw_acl (acl_appname, acl_location, acl_account, acl_rights) VALUES ('" . $app . "', 'run', " . intval($accountid) . ', 1)',__LINE__,__FILE__);
					}
				}
			}
			$this->db->transaction_commit();
			return $accountid;
		}
		
	}
?>
