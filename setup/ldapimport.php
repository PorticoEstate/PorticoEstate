<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2009 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'   => true,
		'nonavbar'   => true,
		'currentapp' => 'home',
		'noapi'      => true
	);

	/**
	 * Include setup functions
	 */
	include('./inc/functions.inc.php');

	// Authorize the user to use setup app and load the database
	if (!$GLOBALS['phpgw_setup']->auth('Config'))
	{
		Header('Location: index.php');
		exit;
	}
	// Does not return unless user is authorized


	/**
	 * Add account
	 * 
	 * @param array  $acct    Account name and other information to use
	 * @param string $type    Account type: u = user | g = group
	 * @param array  $groups  Groups to add account to
	 * @param array  $modules Modules to grant account access to
	 * @param array  $acls    ACLs to set for account
	 *
	 * @return integer Account ID
	 */
	function add_account($acct, $type, $groups = array(), $modules = array(), $acls = array())
	{
		$person_id = 0;
		if ( $type == 'u' )
		{
			$account			= new phpgwapi_user();
			$account->id		= $acct['id'];
			$account->lid		= $acct['lid'];
			$account->firstname	= $acct['firstname'];
			$account->lastname	= $acct['lastname'];
			$account->passwd	= $acct['password'];
			$account->enabled	= true;
			$account->expires	= -1;
		}
		else
		{
			$account			= new phpgwapi_group();
			$account->id		= $acct['id'];
			$account->lid		= $acct['lid'];
			$account->firstname = ucfirst($acct['lid']);
		}

		return $GLOBALS['phpgw']->accounts->create($account, $groups, $acls, $modules);
	}


	$GLOBALS['phpgw_info']['server']['account_repository'] = 'sql'; // importing into sql repository
	$common = $GLOBALS['phpgw']->common;
	$GLOBALS['phpgw_setup']->loaddb();
	$GLOBALS['phpgw']->db = $GLOBALS['phpgw_setup']->db;
	$GLOBALS['phpgw']->hooks = createObject('phpgwapi.hooks');
	
	$GLOBALS['phpgw']->acl = createObject('phpgwapi.acl');

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array
	(
		'ldap'   => 'ldap.tpl',
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl'
	));

	$GLOBALS['phpgw_info']['server']['auth_type'] = 'ldap';

	$GLOBALS['phpgw']->applications = CreateObject('phpgwapi.applications');
	$applications        = $GLOBALS['phpgw']->applications;

	$sql = "SELECT config_name,config_value FROM phpgw_config"
		. " WHERE config_name LIKE 'ldap%'"
		. " OR config_name='account_repository'"
		. " OR config_name='account_min_id'"
		. " OR config_name='account_max_id'"
		. " OR config_name='group_min_id'"
		. " OR config_name='group_max_id'"
		. " OR config_name='encryption_type'";

	$GLOBALS['phpgw_setup']->db->query($sql, __LINE__,__FILE__);

	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$config[$GLOBALS['phpgw_setup']->db->f('config_name')] = $GLOBALS['phpgw_setup']->db->f('config_value');
	}
	$GLOBALS['phpgw_info']['server']['ldap_host']          = $config['ldap_host'];
	$GLOBALS['phpgw_info']['server']['ldap_context']       = $config['ldap_context'];
	$GLOBALS['phpgw_info']['server']['ldap_group_context'] = $config['ldap_group_context'];
	$GLOBALS['phpgw_info']['server']['ldap_root_dn']       = $config['ldap_root_dn'];
	$GLOBALS['phpgw_info']['server']['ldap_root_pw']       = $config['ldap_root_pw'];
	$GLOBALS['phpgw_info']['server']['account_min_id']     = $config['account_min_id'];
	$GLOBALS['phpgw_info']['server']['account_max_id']     = $config['account_max_id'];
	$GLOBALS['phpgw_info']['server']['group_min_id']       = $config['group_min_id'];
	$GLOBALS['phpgw_info']['server']['group_max_id']       = $config['group_max_id'];
	$GLOBALS['phpgw_info']['server']['encryption_type']    = $config['encryption_type'];
	$GLOBALS['phpgw_info']['server']['password_level']     = '8CHAR';

	//$GLOBALS['phpgw_info']['server']['account_repository'] = $config['account_repository'];

	$GLOBALS['phpgw']->accounts = CreateObject('phpgwapi.accounts');
	$acct = $GLOBALS['phpgw']->accounts;

	// First, see if we can connect to the LDAP server, if not send `em back to config.php with an
	// error message.

	// connect to ldap server
	if (! $ldap = $common->ldapConnect())
	{
		Header('Location: config.php?error=badldapconnection');
		exit;
	}

	$sr = ldap_search($ldap,$config['ldap_context'],'(|(uid=*))',array('sn','givenname','uid','uidnumber'));
	$info = ldap_get_entries($ldap, $sr);

	$tmp = '';
	phpgw::import_class('phpgwapi.globally_denied');

	$account_info = array();
	for ($i=0; $i<$info['count']; ++$i)
	{
		if (! phpgwapi_globally_denied::user($info[$i]['uid'][0]) )
		{
			$tmp = $info[$i]['uidnumber'][0];
			$account_info[$tmp]['id']        = $info[$i]['uidnumber'][0];
			$account_info[$tmp]['lid']       = $info[$i]['uid'][0];
			$account_info[$tmp]['firstname'] = $info[$i]['givenname'][0];
			$account_info[$tmp]['lastname']  = $info[$i]['sn'][0];
			$account_info[$tmp]['password']    = isset($info[$i]['userpassword'][0]) ? $info[$i]['userpassword'][0] : '';
			//echo 'password?';
		}
	}

	$group_info = array();
	if ($GLOBALS['phpgw_info']['server']['ldap_group_context'])
	{
		$srg = ldap_search($ldap,$config['ldap_group_context'],'(|(cn=*))',array('gidnumber','cn','memberuid'));
		$info = ldap_get_entries($ldap, $srg);

		$tmp = '';
		for ($i=0; $i<$info['count']; ++$i)
		{
			if ( isset($info[$i]['cn'][0])
				 &&  ! phpgwapi_globally_denied::user($info[$i]['cn'][0]) 
				 &&	 ( !isset($account_info[$i][$info[$i]['cn'][0]]) || ! $account_info[$i][$info[$i]['cn'][0]]) )
			{
				$tmp = $info[$i]['gidnumber'][0];
				$group_info[$tmp]['id']				= $info[$i]['gidnumber'][0];
				$group_info[$tmp]['lid']			= $info[$i]['cn'][0];
				$group_info[$tmp]['members']		= $info[$i]['memberuid'];
				$group_info[$tmp]['firstname']		= $info[$i]['cn'][0];
				$group_info[$tmp]['lastname']		= 'Group';
			}
		}
	}

	$GLOBALS['phpgw_setup']->db->query("SELECT app_name FROM phpgw_applications WHERE app_enabled!='0' AND app_enabled!='3' ORDER BY app_name",__LINE__,__FILE__);
	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$apps[$GLOBALS['phpgw_setup']->db->f('app_name')] = lang($GLOBALS['phpgw_setup']->db->f('app_name'));
	}

	if (isset($_POST['cancel']) && $_POST['cancel'])
	{
		Header("Location: ldap.php");
		exit;
	}

	if (isset($_POST['submit']) && $_POST['submit'])
	{
		if (! @count($_POST['admins']) )
		{
			$error = '<br />You must select at least 1 admin';
		}

		if (! @count($_POST['s_apps']) )
		{
			$error .= '<br />You must select at least 1 application';
		}

		if (!isset($error) || !$error)
		{
			if ( $ldapgroups =  phpgw::get_var('ldapgroups', '', 'POST') )
			{
				$modules = array
				(
					'addressbook',
					'calendar',
					'email',
					'filemanager',
					'manual',
					'preferences',
					'notes',
					'todo'
				);

				foreach($ldapgroups as $key => $groupid)
				{
					$id_exist = 0;
					$thisacctid    = $group_info[$groupid]['id'];
					$thisacctlid   = $group_info[$groupid]['lid'];
					$thisfirstname = $group_info[$groupid]['firstname'];
					$thislastname  = $group_info[$groupid]['lastname'];
					$thismembers   = $group_info[$groupid]['members'];

					// Do some checks before we try to import the data.
					if ($thisacctid > 0  && !empty($thisacctlid))
					{
						$groups = CreateObject('phpgwapi.accounts',intval($thisacctid));
	
						// Check if the account is already there.
						// If so, we won't try to create it again.
						$acct_exist = $groups->name2id($thisacctlid);
						/* echo '<br<group: ' . $acct_exist; */
						if ($acct_exist)
						{
							$thisacctid = $acct_exist;
						}
						$id_exist = $groups->exists(intval($thisacctid));
						// If not, create it now.
						if(!$id_exist)
						{
							$thisgroup_info = array(
								'type'      => 'g',
								'id'       	=> $thisacctid,
								'lid'       => $thisacctlid,
					//			'passwd'    => $passwd,
								'firstname' => $thisfirstname,
								'lastname'  => $thislastname,
								'status'    => 'A',
								'expires'   => -1
							);

							add_account($thisgroup_info, 'g', array(), $modules);
					//		$thisacctid = $acct->name2id($thisacctlid);
						}

						// Now make them a member of this group in phpgw.
						//while ( list($key,$members) = @each($thismembers))
						foreach($thismembers as $key => $members)
						{
							if ($key == 'count')
							{
								continue;
							}
							/* echo '<br />members: ' . $members; */
							$tmpid = 0;
							//@reset($account_info);
							//while(list($x,$y) = each($account_info))
							foreach($account_info as $x => $y)
							{
								/* echo '<br />checking: '.$y['account_lid']; */
								if ($members == $y['account_lid'])
								{
									$tmpid = $acct->name2id($y['account_lid']);
								}
							}
							/*
							Insert acls for this group based on memberuid field.
							Since the group has app rights, we don't need to give users
							these rights.  Instead, we maintain group membership here.
							*/
							if($tmpid)
							{
								$acct->add_user2group($tmpid, $thisacctid);

								$GLOBALS['phpgw']->acl->set_account_id($tmpid);
								/* Now add the acl to let them change their password */
								$GLOBALS['phpgw']->acl->add('preferences','changepassword',1);

								$GLOBALS['phpgw']->acl->save_repository();

								/* Add prefs for selected apps here, since they are per-user.
									App access is added below.
								*/
								$pref = CreateObject('phpgwapi.preferences',$tmpid);
								$pref->set_account_id(intval($tmpid));
								$pref->read();
								//@reset($_POST['s_apps']);
								//while (list($key,$app) = each($_POST['s_apps']))
								if (is_array($_POST['s_apps']))
								{
									foreach($_POST['s_apps'] as $key => $app)
									{
										$GLOBALS['phpgw']->hooks->single('add_def_pref',$app);
									}
								}
								$pref->save_repository();
							}
						}
						/* Now give this group some rights */
						$GLOBALS['phpgw']->acl->set_account_id($thisacctid);
						//@reset($_POST['s_apps']);
						//while (list($key,$app) = each($_POST['s_apps']))
						if (is_array($_POST['s_apps']))
						{
							foreach($_POST['s_apps'] as $key => $app)
							{
								$GLOBALS['phpgw']->acl->add($app,'run',1);
							}
						}
						$GLOBALS['phpgw']->acl->add('preferences','changepassword', 1);
						$GLOBALS['phpgw']->acl->save_repository();
						$defaultgroupid = $thisacctid;
					}
				}
			}
			else
			{
				$acls = array();
				/* Create the 'Default' group */
				$groups = CreateObject('phpgwapi.accounts',$defaultgroupid);

				// Check if the group account is already there.
				// If so, set our group_id to that account's id for use below.
				$acct_exist = $groups->name2id('Default');
				if ($acct_exist)
				{
					$defaultgroupid = $acct_exist;
				}
				$id_exist   = $groups->exists(intval($defaultgroupid));
				// if not, create it, using our original groupid.
				if($id_exist)
				{
					$groups->delete($defaultgroupid);
				}

				foreach ( $_POST['s_apps'] as $app )
				{
					$acls[] = array
					(
						'appname'	=> $app,
						'location'	=> 'run',
						'rights'	=> 1
					);
				}
				$acls[] = array
				(
					'appname'	=> 'preferences',
					'location'	=> 'changepassword',
					'rights'	=> 1
				);
		
				add_account(array('username' => 'default'), 'g', array(), $modules, $acls);

			} //end default group creation
		}

		if ( isset($_POST['users']) && is_array($_POST['users']) )
		{
			foreach($_POST['users'] as $key => $id)
			{
				$acls = array();
				$id_exist = 0;
				$thisacctid    = $account_info[$id]['id'];
				$thisacctlid   = $account_info[$id]['lid'];
				$thisfirstname = $account_info[$id]['firstname'];
				$thislastname  = $account_info[$id]['lastname'];
				$thispasswd    = $account_info[$id]['password'];

				// Do some checks before we try to import the data.
				if (!empty($thisacctid) && !empty($thisacctlid) )
				{
					$accounts = CreateObject('phpgwapi.accounts',intval($thisacctid));

					// Check if the account is already there.
					// If so, we won't try to create it again.
					$acct_exist = $acct->name2id($thisacctlid);
					if ($acct_exist)
					{
						$thisacctid = $acct_exist;
					}
					$id_exist = $accounts->exists($thisacctlid);
					// If not, create it now.
					if(!$id_exist)
					{
						$thisaccount_info = array(
							'type'      => 'u',
							'id'       	=> $thisacctid,
							'lid'       => $thisacctlid,
							'password'  => 'xxxxxxxx',
						/*	'account_passwd'    => $thispasswd, */
							'firstname' => $thisfirstname,
							'lastname'  => $thislastname,
							'status'    => 'A',
							'expires'   => -1
						);
					}

					// Insert default acls for this user.
					// Since the group has app rights, we don't need to give users
					//  these rights.  Instead, we make the user a member of the Default group
					//  below.

					// Only give them admin if we asked for them to have it.
					// This is typically an exception to apps for run rights
					//  as a group member.
					$admins =  phpgw::get_var('admins', '', 'POST');
					$cnt_admins = count($admins);

					for ($a = 0; $a < $cnt_admins; ++$a)
					{
						if ($admins[$a] == $thisacctlid)
						{
							$acls[] = array
							(
								'appname'	=> 'admin',
								'location'	=> 'run',
								'rights'	=> 1
							);
						}
					}

					// Now make them a member of the 'Default' group.
					// But, only if the current user is not the group itself.
					if (!$defaultgroupid)
					{
						$defaultgroupid = $accounts->name2id('Default');
					}

					if($defaultgroupid)
					{
						$groups = array($defaultgroupid);
					}
					if(!$id_exist)
					{
						$thisacctid = add_account($thisaccount_info, 'u', $groups, array('admin'), $acls);
					}
					// Save these new acls.
				}
			}
		}
		$setup_complete = true;
	}

	$GLOBALS['phpgw_setup']->html->show_header('LDAP Import','','config',$_COOKIE['ConfigDomain']);

	if (isset($error) && $error)
	{
		//echo '<br /><center><b>Error:</b> '.$error.'</center>';
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',$error);
	}

	if (isset($setup_complete) && $setup_complete)
	{
		echo '<br /><center>'.lang('Import has been completed!').' '.lang('Click <a href="index.php">here</a> to return to setup.').'</center>';
		$GLOBALS['phpgw_setup']->html->show_footer();
		exit;
	}

	$setup_tpl->set_block('ldap','header','header');
	$setup_tpl->set_block('ldap','user_list','user_list');
	$setup_tpl->set_block('ldap','admin_list','admin_list');
	$setup_tpl->set_block('ldap','group_list','group_list');
	$setup_tpl->set_block('ldap','app_list','app_list');
	$setup_tpl->set_block('ldap','submit','submit');
	$setup_tpl->set_block('ldap','footer','footer');

	$user_list = '';
	//while (list($key,$account) = each($account_info))
	foreach($account_info as $key => $account)
	{
		$user_list .= '<option value="' . $account['id'] . '">'
			. $common->display_fullname($account['lid'],$account['firstname'],$account['lastname'])
			. '</option>';
	}

	//@reset($account_info);
	$admin_list = '';
	//while (list($key,$account) = each($account_info))
	foreach($account_info as $key => $account)
	{
		$admin_list .= '<option value="' . $account['lid'] . '">'
			. $common->display_fullname($account['lid'],$account['firstname'],$account['lastname'])
			. '</option>';
	}

	$group_list = '';
	//while (list($key,$group) = each($group_info))
	foreach($group_info as $key => $group)
	{
		$group_list .= '<option value="' . $group['id'] . '">'
			. $group['lid']
			. '</option>';
	}

	$app_list = '';
	//while(list($appname,$apptitle) = each($apps))
	if (is_array($apps))
	{
		foreach($apps as $appname => $apptitle)
		{
			if($appname == 'admin' ||
				$appname == 'skel' ||
				$appname == 'backup' ||
				$appname == 'netsaint' ||
				$appname == 'developer_tools' ||
				$appname == 'phpsysinfo' ||
				$appname == 'eldaptir' ||
				$appname == 'qmailldap')
			{
				$app_list .= '<option value="' . $appname . '">' . $apptitle . '</option>';
			}
			else
			{
				$app_list .= '<option value="' . $appname . '" selected>' . $apptitle . '</option>';
			}
		}
	}

	$setup_tpl->set_var('action_url','ldapimport.php');
	$setup_tpl->set_var('users',$user_list);
	$setup_tpl->set_var('admins',$admin_list);
	$setup_tpl->set_var('ldapgroups',$group_list);
	$setup_tpl->set_var('s_apps',$app_list);

	$setup_tpl->set_var('ldap_import',lang('LDAP import users'));
	$setup_tpl->set_var('description',lang("This section will help you import users and groups from your LDAP tree into phpGroupWare's account tables").'.');
	$setup_tpl->set_var('select_users',lang('Select which user(s) will be imported'));
	$setup_tpl->set_var('select_admins',lang('Select which user(s) will have admin privileges'));
	$setup_tpl->set_var('select_groups',lang('Select which group(s) will be imported (group membership will be maintained)'));
	$setup_tpl->set_var('select_apps',lang('Select the default applications to which your users will have access').'.');
	$setup_tpl->set_var('note',lang('Note: You will be able to customize this later').'.');
	$setup_tpl->set_var('form_submit','import');
	$setup_tpl->set_var('cancel',lang('Cancel'));

	$setup_tpl->pfp('out','header');
	$setup_tpl->pfp('out','user_list');
	$setup_tpl->pfp('out','admin_list');
	$setup_tpl->pfp('out','group_list');
	$setup_tpl->pfp('out','app_list');
	$setup_tpl->pfp('out','submit');
	$setup_tpl->pfp('out','footer');

	$GLOBALS['phpgw_setup']->html->show_footer();