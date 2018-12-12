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

	/* Authorize the user to use setup app and load the database */
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

	$common =& $GLOBALS['phpgw']->common;
	$GLOBALS['phpgw_setup']->loaddb();
	$GLOBALS['phpgw']->db = $GLOBALS['phpgw_setup']->db;

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array(
		'ldap'   => 'ldap.tpl',
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl'
	));

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

	$acct	= $GLOBALS['phpgw']->accounts	= CreateObject('phpgwapi.accounts');

	// connect to ldap server
	if (! $ldap = $common->ldapConnect())
	{
		$noldapconnection = true;
	}

	if (isset($noldapconnection))
	{
		Header('Location: config.php?error=badldapconnection');
		exit;
	}

	$sr = ldap_search($ldap,$config['ldap_context'],'(|(uid=*))',array('cn','givenname','uid','uidnumber'));
	$info = ldap_get_entries($ldap, $sr);
	$tmp = '';

	for ($i=0; $i<$info['count']; ++$i)
	{
		if (!isset($GLOBALS['phpgw_info']['server']['global_denied_users'][$info[$i]['uid'][0]]) || !$GLOBALS['phpgw_info']['server']['global_denied_users'][$info[$i]['uid'][0]])
		{
			$account_info[$info[$i]['uidnumber'][0]] = $info[$i];
		}
	}

	if ($GLOBALS['phpgw_info']['server']['ldap_group_context'])
	{
		$srg = ldap_search($ldap,$config['ldap_group_context'],'(|(cn=*))',array('gidnumber','cn','memberuid'));
		$info = ldap_get_entries($ldap, $srg);
		$tmp = '';

		for ($i=0; $i<$info['count']; ++$i)
		{
			if ((!isset($GLOBALS['phpgw_info']['server']['global_denied_groups'][$info[$i]['cn'][0]]) || !$GLOBALS['phpgw_info']['server']['global_denied_groups'][$info[$i]['cn'][0]]) &&
				(!isset($account_info[$i][$info[$i]['cn'][0]]) || !$account_info[$i][$info[$i]['cn'][0]]))
			{
				$group_info[$info[$i]['gidnumber'][0]] = $info[$i];
			}
		}
	}
	else
	{
		$group_info = array();
	}

	$GLOBALS['phpgw_setup']->db->query('SELECT app_name FROM phpgw_applications'
					. ' WHERE app_enabled !=0 '
					. ' AND app_enabled !=3 ORDER BY app_name',__LINE__,__FILE__);
	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$apps[$GLOBALS['phpgw_setup']->db->f('app_name')] = lang($GLOBALS['phpgw_setup']->db->f('app_name'));
	}

	if ( isset($_POST['cancel']) )
	{
		Header("Location: ldap.php");
		exit;
	}

	$GLOBALS['phpgw_setup']->html->show_header('LDAP Modify','','config',$ConfigDomain);

	if (isset($_POST['submit']) && $_POST['submit'])
	{
		$acl = CreateObject('phpgwapi.acl');
		if ( isset($_POST['ldapgroups']) && count($_POST['ldapgroups']) )
		{
			$groups = CreateObject('phpgwapi.accounts');
			foreach($_POST['ldapgroups'] as $key => $groupid)
			{
				$id_exist = 0;
				$entry = array();
				$thisacctid    = $group_info[$groupid]['gidnumber'][0];
				$thisacctlid   = $group_info[$groupid]['cn'][0];
				/* echo "Updating GROUPID : ".$thisacctlid."<br />\n"; */
				$thisfirstname = $group_info[$groupid]['cn'][0];
				$thismembers   = $group_info[$groupid]['memberuid'];
				$thisdn        = $group_info[$groupid]['dn'];

				// Do some checks before we try to import the data.
				if (!empty($thisacctid) && !empty($thisacctlid))
				{
					$groups->set_account(intval($thisacctid));

					$sr = ldap_search($ldap,$config['ldap_group_context'],'cn='.$thisacctlid);
					$entry = ldap_get_entries($ldap, $sr);

					//reset($entry[0]['objectclass']);
					$addclass = True;
					//while(list($key,$value) = @each($entry[0]['objectclass']))
					if (is_array($entry[0]['objectclass']))
					{
						foreach($entry[0]['objectclass'] as $key => $value)
						{
							if(strtolower($value) == 'phpgwGroup')
							{
								$addclass = False;
							}
						}
					}
					
					if($addclass)
					{
						reset($entry[0]['objectclass']);
						$replace['objectclass'] = $entry[0]['objectclass'];
						unset($replace['objectclass']['count']); // breaks things
						$replace['objectclass'][]       = 'phpgwGroup';

						// We add this here as it is mandatory
						$replace['phpgwGroupID'] = $thisacctlid;
						$ok = @ldap_mod_replace($ldap,$thisdn,$replace);
						if (!$ok) // give user some feedback
						{
							echo lang('failed to modify: %1', $thisdn) . '<br />';
						}
						
						unset($replace);
						unset($addclass);
						unset($ok);
					}
					unset($add);

					// Now make the members a member of this group in phpgw.
					//while (list($key,$members) = each($thismembers))
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
								$tmpid = $y['account_id'];
							}
						}
						// Insert acls for this group based on memberuid field.
						// Since the group has app rights, we don't need to give users
						// these rights.  Instead, we maintain group membership here.
						if($tmpid)
						{
							$acl->set_account_id(intval($tmpid));

							$acl->delete('phpgw_group',$thisacctid,1);
							$acl->add('phpgw_group',$thisacctid,1);

							// Now add the acl to let them change their password
							$acl->delete('preferences','changepassword',1);
							$acl->add('preferences','changepassword',1);

							$acl->save_repository();
						}
					}
					// Now give this group some rights
					$GLOBALS['phpgw_info']['user']['account_id'] = $thisacctid;
					$acl->set_account_id(intval($thisacctid));
					//@reset($s_apps);
					//while (list($key,$app) = @each($s_apps))
					foreach($s_apps as $key => $app)
					{
						$acl->delete($app,'run',1);
						$acl->add($app,'run',1);
					}
					$acl->save_repository();
					$defaultgroupid = $thisacctid;
				}
			}
		}

		if( isset($_POST['users']) && count($_POST['users']) )
		{
			$accounts = CreateObject('phpgwapi.accounts');
		//	$accounts->db = $GLOBALS['phpgw_setup']->db;
			foreach($_POST['users'] as $key => $id)
			{
				$id_exist = 0;
				$thisacctid  = $account_info[$id]['uidnumber'][0];
				$thisacctlid = $account_info[$id]['uid'][0];
				/* echo "Updating USERID : ".$thisacctlid."<br />\n"; */
				$thisdn      = $account_info[$id]['dn'];

				/* Do some checks before we try to import the data. */
				if (!empty($thisacctid) && !empty($thisacctlid))
				{
					$accounts->set_account(intval($thisacctid));
					$sr = ldap_search($ldap,$config['ldap_context'],'uid='.$thisacctlid);
					$entry = ldap_get_entries($ldap, $sr);
					//reset($entry[0]['objectclass']);
					$addclass = True;
					//while(list($key,$value) = each($entry[0]['objectclass']))
					foreach($entry[0]['objectclass'] as $key => $value)
					{
						if(strtolower($value) == 'phpgwaccount')
						{
							$addclass = False;
						}
					}
					if($addclass)
					{
						reset($entry[0]['objectclass']);
						$addmod['objectclass']		= $entry[0]['objectclass'];
						$addmod['objectclass'][]	= 'phpgwAccount';
						unset($addmod['objectclass']['count']);
					}
					
					if(!@isset($entry[0]['phpgwaccountstatus']))
					{
						$addmod['phpgwaccountstatus'][]	= 'A';
					}
					
					if(!@isset($entry[0]['phpgwaccountexpires']))
					{
						$addmod['phpgwaccountexpires'][] = -1;
					}

					if(!@isset($entry[0]['phpgwAccountID']))
					{
						$addmod['phpgwAccountID'][]	= $thisacctid;
					}
					
					if($addmod)
					{						
						$ok = ldap_mod_replace($ldap,$thisdn,$addmod);
						
						if (!$ok) // give user some feedback
						{
							echo lang('failed to modify: ', $thisdn) . '<br />';
						}
						
						unset($replace);
						unset($addclass);
					}
					unset($addmod);
					if(@isset($add))
					{
						echo "<pre>ldap_mod_add($ldap,$thisdn,"; print_r($add); echo '</pre>';
						ldap_mod_add($ldap,$thisdn,$add);
					}

					/*
					Insert default acls for this user.
					Since the group has app rights, we don't need to give users
					these rights.
					*/
					$acl->set_account_id(intval($thisacctid));

					/*
					However, if no groups were imported, we do need to give each user
					apps access
					*/
					if(! (isset($_POST['ldapgroups']) && count($_POST['ldapgroups']) ) )
					{
						//@reset($s_apps);
						//while (list($key,$app) = @each($s_apps))
						foreach($s_apps as $key => $app)
						{
							$acl->delete($app,'run',1);
							$acl->add($app,'run',1);
						}
					}
					// Now add the acl to let them change their password
					$acl->delete('preferences','changepassword',1);
					$acl->add('preferences','changepassword',1);

					/*
					Only give them admin if we asked for them to have it.
					This is typically an exception to apps for run rights
					as a group member.
					*/
					if(isset($admins) && is_array($admins)) // Sigurd: don't seems to defined at all
					{
						for ($a=0; $a < count($admins); ++$a)
						{
							if ($admins[$a] == $thisacctid)
							{
								$acl->delete('admin','run',1);
								$acl->add('admin','run',1);
							}
						}
					}
					/* Save these new acls. */
					$acl->save_repository();
				}
			}
		}
		$setup_complete = True;
	}

	if (isset($error) && $error)
	{
		/* echo '<br /><center><b>Error:</b> '.$error.'</center>'; */
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',$error);
	}

	if (isset($setup_complete) && $setup_complete)
	{
		echo '<br /><center>'.lang('Modifications have been completed!').' '.lang('Click <a href="index.php">here</a> to return to setup.').'<br /><center>';
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
	$admin_list = '';
	
	if (is_array($account_info))
	{
		foreach($account_info as $key => $account)
		{
			$user_list .= '<option value="' . $account['uidnumber'][0] . '">' . $account['cn'][0] . '(' . $account['uid'][0] . ')</option>';
		}

		foreach($account_info as $key => $account)
		{
			$admin_list .= '<option value="' . $account['uidnumber'][0] . '">' . $account['cn'][0] . '(' . $account['uid'][0] . ')</option>';
		}
	}

	$group_list = '';
	if (is_array($group_info))
	{
		foreach($group_info as $key => $group)
		{
			$group_list .= '<option value="' . $group['gidnumber'][0] . '">' . $group['cn'][0]  . '</option>';
		}
	}

	$app_list = '';
	//while(list($appname,$apptitle) = each($apps)) // TODO: IMHO This needs to go - skwashd Jul-04
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
				$app_list .= '<option value="' . $appname . '" selected="selected">' . $apptitle . '</option>';
			}
		}
	}

	$setup_tpl->set_var('action_url','ldapmodify.php');
	$setup_tpl->set_var('users',$user_list);
	$setup_tpl->set_var('admins',$admin_list);
	$setup_tpl->set_var('ldapgroups',$group_list);
	$setup_tpl->set_var('s_apps',$app_list);

	$setup_tpl->set_var('ldap_import',lang('LDAP Modify'));
	$setup_tpl->set_var('description',lang("This section will help you setup your LDAP accounts for use with phpGroupWare").'.');
	$setup_tpl->set_var('select_users',lang('Select which user(s) will be modified'));
	$setup_tpl->set_var('select_admins',lang('Select which user(s) will also have admin privileges'));
	$setup_tpl->set_var('select_groups',lang('Select which group(s) will be modified (group membership will be maintained)'));
	$setup_tpl->set_var('select_apps',lang('Select the default applications to which your users will have access').'.');
	$setup_tpl->set_var('form_submit',lang('Modify'));
	$setup_tpl->set_var('cancel',lang('Cancel'));

	$setup_tpl->pfp('out','header');
	$setup_tpl->pfp('out','user_list');
	$setup_tpl->pfp('out','admin_list');
	$setup_tpl->pfp('out','group_list');
	$setup_tpl->pfp('out','app_list');
	$setup_tpl->pfp('out','submit');
	$setup_tpl->pfp('out','footer');

	$GLOBALS['phpgw_setup']->html->show_footer();
