<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader'   => True,
		'nonavbar'   => True,
		'currentapp' => 'home',
		'noapi'      => True
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
	* phpGroupWare class
	* @package setup
	* @ignore
	*/
	class phpgw
	{
		/**
		 * Common
		 * @var object
		 */
		var $common;
		
		/**
		 * Accounts
		 * @var object
		 */
		var $accounts;
		
		/**
		 * Applications
		 * @var object
		 */
		var $applications;
		
		/**
		 * Database
		 * @var object
		 */
		var $db;
		
		/**
		 * Hooks
		 * @var object
		 */
		var $hooks;
		
		/**
		 * Access control list
		 * @var object
		 */
		var $acl;
	}
	$GLOBALS['phpgw'] = new phpgw;
	$GLOBALS['phpgw']->common = CreateObject('phpgwapi.common');

	$common = $GLOBALS['phpgw']->common;
	$GLOBALS['phpgw_setup']->loaddb();
	$GLOBALS['phpgw']->db = $GLOBALS['phpgw_setup']->db;
	$GLOBALS['phpgw']->hooks = createObject('phpgwapi.hooks');
	
	$GLOBALS['phpgw']->acl = createObject('phpgwapi.acl');
	$GLOBALS['phpgw']->acl->db = $GLOBALS['phpgw_setup']->db;

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
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

	$GLOBALS['phpgw_setup']->db->query("SELECT config_name,config_value FROM phpgw_config WHERE config_name LIKE 'ldap%' OR config_name='account_repository'",__LINE__,__FILE__);
	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$config[$GLOBALS['phpgw_setup']->db->f('config_name')] = $GLOBALS['phpgw_setup']->db->f('config_value');
	}
	$GLOBALS['phpgw_info']['server']['ldap_host']          = $config['ldap_host'];
	$GLOBALS['phpgw_info']['server']['ldap_context']       = $config['ldap_context'];
	$GLOBALS['phpgw_info']['server']['ldap_group_context'] = $config['ldap_group_context'];
	$GLOBALS['phpgw_info']['server']['ldap_root_dn']       = $config['ldap_root_dn'];
	$GLOBALS['phpgw_info']['server']['ldap_root_pw']       = $config['ldap_root_pw'];
	$GLOBALS['phpgw_info']['server']['account_repository'] = $config['account_repository'];

	$GLOBALS['phpgw']->accounts = CreateObject('phpgwapi.accounts');
	$acct = $GLOBALS['phpgw']->accounts;

	// First, see if we can connect to the LDAP server, if not send `em back to config.php with an
	// error message.

	// connect to ldap server
	if (! $ldap = $common->ldapConnect())
	{
		$noldapconnection = True;
	}

	if ($noldapconnection)
	{
		Header('Location: config.php?error=badldapconnection');
		exit;
	}

	$sr = ldap_search($ldap,$config['ldap_context'],'(|(uid=*))',array('sn','givenname','uid','uidnumber'));
	$info = ldap_get_entries($ldap, $sr);
	$tmp = '';

	for ($i=0; $i<$info['count']; ++$i)
	{
		if (! $GLOBALS['phpgw_info']['server']['global_denied_users'][$info[$i]['uid'][0]])
		{
			$tmp = $info[$i]['uidnumber'][0];
			$account_info[$tmp]['account_id']        = $info[$i]['uidnumber'][0];
			$account_info[$tmp]['account_lid']       = $info[$i]['uid'][0];
			$account_info[$tmp]['account_firstname'] = $info[$i]['givenname'][0];
			$account_info[$tmp]['account_lastname']  = $info[$i]['sn'][0];
			$account_info[$tmp]['account_passwd']    = $info[$i]['userpassword'][0];
		}
	}

	if ($GLOBALS['phpgw_info']['server']['ldap_group_context'])
	{
		$srg = ldap_search($ldap,$config['ldap_group_context'],'(|(cn=*))',array('gidnumber','cn','memberuid'));
		$info = ldap_get_entries($ldap, $srg);
		$tmp = '';

		for ($i=0; $i<$info['count']; ++$i)
		{
			if (! $GLOBALS['phpgw_info']['server']['global_denied_groups'][$info[$i]['cn'][0]] &&
				! $account_info[$i][$info[$i]['cn'][0]])
			{
				$tmp = $info[$i]['gidnumber'][0];
				$group_info[$tmp]['account_id']        = $info[$i]['gidnumber'][0];
				$group_info[$tmp]['account_lid']       = $info[$i]['cn'][0];
				$group_info[$tmp]['members']           = $info[$i]['memberuid'];
				$group_info[$tmp]['account_firstname'] = $info[$i]['cn'][0];
				$group_info[$tmp]['account_lastname']  = 'Group';
			}
		}
	}
	else
	{
		$group_info = array();
	}

	$GLOBALS['phpgw_setup']->db->query("SELECT app_name FROM phpgw_applications WHERE app_enabled!='0' AND app_enabled!='3' ORDER BY app_name",__LINE__,__FILE__);
	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$apps[$GLOBALS['phpgw_setup']->db->f('app_name')] = lang($GLOBALS['phpgw_setup']->db->f('app_name'));
	}

	if ($_POST['cancel'])
	{
		Header("Location: ldap.php");
		exit;
	}

	if ($_POST['submit'])
	{
		if (! @count($_POST['admins']) )
		{
			$error = '<br />You must select at least 1 admin';
		}

		if (! @count($_POST['s_apps']) )
		{
			$error .= '<br />You must select at least 1 application';
		}

		if (!$error)
		{
			if ( isset($_POST['ldapgroups']) && count($_POST['ldapgroups']) )
			{
				foreach($ldapgroups as $key => $groupid)
				{
					$id_exist = 0;
					$thisacctid    = $group_info[$groupid]['account_id'];
					$thisacctlid   = $group_info[$groupid]['account_lid'];
					$thisfirstname = $group_info[$groupid]['account_firstname'];
					$thislastname  = $group_info[$groupid]['account_lastname'];
					$thismembers   = $group_info[$groupid]['members'];

					// Do some checks before we try to import the data.
					if (!empty($thisacctid) && !empty($thisacctlid))
					{
						$groups = CreateObject('phpgwapi.accounts',intval($thisacctid));
						$groups->db = $GLOBALS['phpgw_setup']->db;
	
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
								'account_type'      => 'g',
								'account_lid'       => $thisacctlid,
								'account_passwd'    => $passwd,
								'account_firstname' => $thisfirstname,
								'account_lastname'  => $thislastname,
								'account_status'    => 'A',
								'account_expires'   => -1
							);
							$groups->create($thisgroup_info);
							$thisacctid = $acct->name2id($thisacctlid);
						}

						// Now make them a member of this group in phpgw.
						while ( list($key,$members) = @each($thismembers))
						{
							if ($key == 'count')
							{
								continue;
							}
							/* echo '<br />members: ' . $members; */
							$tmpid = 0;
							@reset($account_info);
							while(list($x,$y) = each($account_info))
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
								$GLOBALS['phpgw']->acl->acl($tmpid);
								$GLOBALS['phpgw']->acl->read();

								$GLOBALS['phpgw']->acl->delete('phpgw_group',$thisacctid,1);
								$GLOBALS['phpgw']->acl->add('phpgw_group',$thisacctid,1);

								/* Now add the acl to let them change their password */
								$GLOBALS['phpgw']->acl->delete('preferences','changepassword',1);
								$GLOBALS['phpgw']->acl->add('preferences','changepassword',1);

								$GLOBALS['phpgw']->acl->save_repository();

								/* Add prefs for selected apps here, since they are per-user.
									App access is added below.
								*/
								$pref = CreateObject('phpgwapi.preferences',$tmpid);
								$pref->db = $GLOBALS['phpgw_setup']->db;
								$pref->account_id = intval($tmpid);
								$pref->read();
								@reset($_POST['s_apps']);
								while (list($key,$app) = each($_POST['s_apps']))
								{
									$GLOBALS['phpgw']->hooks->single('add_def_pref',$app);
								}
								$pref->save_repository();
							}
						}
						/* Now give this group some rights */
						$GLOBALS['phpgw']->acl->acl($thisacctid);
						$GLOBALS['phpgw']->acl->read();
						@reset($_POST['s_apps']);
						while (list($key,$app) = each($_POST['s_apps']))
						{
							$GLOBALS['phpgw']->acl->delete($app,'run',1);
							$GLOBALS['phpgw']->acl->add($app,'run',1);
						}
						$acl->add('preferences','changepassword', 1);
						$GLOBALS['phpgw']->acl->save_repository();
						$defaultgroupid = $thisacctid;
					}
				}
			}
			else
			{
				/* Create the 'Default' group */
				$groups = CreateObject('phpgwapi.accounts',$defaultgroupid);
				$groups->db = $GLOBALS['phpgw_setup']->db;

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
				$thisgroup_info = array
				(
					'account_type'      => 'g',
					'account_lid'       => 'Default',
					'account_passwd'    => $passwd,
					'account_firstname' => 'Default',
					'account_lastname'  => 'Group',
					'account_status'    => 'A',
					'account_expires'   => -1
				);
				$acct->create($thisgroup_info);

				$defaultgroupid = $acct->name2id('Default');

				$acl = CreateObject('phpgwapi.acl',$defaultgroupid);
				$acl->db = $GLOBALS['phpgw_setup']->db;
				$acl->set_account_id(intval($defaultgroupid))
				foreach ( $_POST['s_apps'] as $app )
				{
					$acl->delete($app,'run',1);
					$acl->add($app,'run',1);
				}
				$acl->add('preferences','changepassword', 1);
				$acl->save_repository();
			} //end default group creation
		}

		if ( isset($_POST['users']) && is_array($_POST['users']) )
		{
			foreach($_POST['users'] as $key => $id)
			{
				$id_exist = 0;
				$thisacctid    = $account_info[$id]['account_id'];
				$thisacctlid   = $account_info[$id]['account_lid'];
				$thisfirstname = $account_info[$id]['account_firstname'];
				$thislastname  = $account_info[$id]['account_lastname'];
				$thispasswd    = $account_info[$id]['account_passwd'];

				// Do some checks before we try to import the data.
				if (!empty($thisacctid) && !empty($thisacctlid) )
				{
					$accounts = CreateObject('phpgwapi.accounts',intval($thisacctid));
					$accounts->db = $GLOBALS['phpgw_setup']->db;

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
							'account_type'      => 'u',
							'account_lid'       => $thisacctlid,
							'account_passwd'    => 'x',
						/*	'account_passwd'    => $thispasswd, */
							'account_firstname' => $thisfirstname,
							'account_lastname'  => $thislastname,
							'account_status'    => 'A',
							'account_expires'   => -1
						);
						$accounts->create($thisaccount_info);
						$thisacctid = $acct->name2id($thisacctlid);
					}

					// Insert default acls for this user.
					// Since the group has app rights, we don't need to give users
					//  these rights.  Instead, we make the user a member of the Default group
					//  below.
					$GLOBALS['phpgw']->acl->acl($thisacctid);
					$GLOBALS['phpgw']->acl->read();

					// Only give them admin if we asked for them to have it.
					// This is typically an exception to apps for run rights
					//  as a group member.
					$cnt_admins = count($_POST['admins']);
					for ($a = 0; $a < $cnt_admins; ++$a)
					{
						if ($admins[$a] == $thisacctlid)
						{
							$GLOBALS['phpgw']->acl->delete('admin','run',1);
							$GLOBALS['phpgw']->acl->add('admin','run',1);
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
						$GLOBALS['phpgw']->acl->delete('phpgw_group',$defaultgroupid,1);
						$GLOBALS['phpgw']->acl->add('phpgw_group',$defaultgroupid,1);
					}

					// Save these new acls.
					$GLOBALS['phpgw']->acl->save_repository();
				}
			}
		}
		$setup_complete = True;
	}

	$GLOBALS['phpgw_setup']->html->show_header('LDAP Import','','config',$_COOKIE['ConfigDomain']);

	if ($error)
	{
		//echo '<br /><center><b>Error:</b> '.$error.'</center>';
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',$error);
	}

	if ($setup_complete)
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

	while (list($key,$account) = each($account_info))
	{
		$user_list .= '<option value="' . $account['account_id'] . '">'
			. $common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
			. '</option>';
	}

	@reset($account_info);
	while (list($key,$account) = each($account_info))
	{
		$admin_list .= '<option value="' . $account['account_lid'] . '">'
			. $common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
			. '</option>';
	}

	while (list($key,$group) = each($group_info))
	{
		$group_list .= '<option value="' . $group['account_id'] . '">'
			. $group['account_lid']
			. '</option>';
	}

	while(list($appname,$apptitle) = each($apps))
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
?>
