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
			$account->person_id	= $acct['person_id'];
		}
		else
		{
			$account			= new phpgwapi_group();
			$account->id		= $acct['id'];
			$account->lid		= $acct['lid'];
			$account->firstname = ucfirst($acct['lid']);
			$account->person_id	= $acct['person_id'];
		}

		return $GLOBALS['phpgw']->accounts->create($account, $groups, $acls, $modules);
	}


	//This stops timeout problems for larger conversions
	@set_time_limit(0);

	/**
	* phpGroupWare class
	* @package setup
	* @ignore
	*/

	$common = $GLOBALS['phpgw']->common;
	$GLOBALS['phpgw_setup']->loaddb();
	$GLOBALS['phpgw']->db = $GLOBALS['phpgw_setup']->db;
	$GLOBALS['phpgw']->hooks = createObject('phpgwapi.hooks');	
	$GLOBALS['phpgw']->acl = createObject('phpgwapi.acl');


	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array(
		'ldap'   => 'ldap.tpl',
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl'
	));

	$GLOBALS['phpgw_setup']->db->query("SELECT config_name,config_value FROM phpgw_config WHERE config_name LIKE 'ldap%'",__LINE__,__FILE__);
	while ($GLOBALS['phpgw_setup']->db->next_record())
	{
		$phpgw_info['server'][$GLOBALS['phpgw_setup']->db->f('config_name')] = $GLOBALS['phpgw_setup']->db->f('config_value');
	}
	$phpgw_info['server']['account_repository'] = 'ldap';

	$phpgw->accounts     = CreateObject('phpgwapi.accounts');
	$acct                = $phpgw->accounts;

	// First, see if we can connect to the LDAP server, if not send `em back to config.php with an
	// error message.

	// connect to ldap server
	if(!$ldap = $common->ldapConnect())
	{
		Header('Location: config.php?error=badldapconnection');
		exit;
	}

	$sql = "SELECT * FROM phpgw_accounts WHERE account_type='u'";
	$GLOBALS['phpgw_setup']->db->query($sql,__LINE__,__FILE__);
	while($GLOBALS['phpgw_setup']->db->next_record())
	{
		$i = $GLOBALS['phpgw_setup']->db->f('account_id');
		$account_info[$i]['id']        = $GLOBALS['phpgw_setup']->db->f('account_id');
		$account_info[$i]['lid']       = $GLOBALS['phpgw_setup']->db->f('account_lid');
		$account_info[$i]['firstname'] = $GLOBALS['phpgw_setup']->db->f('account_firstname');
		$account_info[$i]['lastname']  = $GLOBALS['phpgw_setup']->db->f('account_lastname');
		$account_info[$i]['status']    = $GLOBALS['phpgw_setup']->db->f('account_status');
		$account_info[$i]['expires']   = $GLOBALS['phpgw_setup']->db->f('account_expires');
		$account_info[$i]['person_id'] = $GLOBALS['phpgw_setup']->db->f('person_id');
	}

	$newaccount = array();
	//while(list($key,$data) = @each($account_info))
	foreach($account_info as $key => $data)
	{
		$tmp = $data['id'];
		$newaccount[$tmp] = $data;
	}
	$account_info = $newaccount;

	$sql = "SELECT * FROM phpgw_accounts WHERE account_type='g'";
	$GLOBALS['phpgw_setup']->db->query($sql,__LINE__,__FILE__);
	while($GLOBALS['phpgw_setup']->db->next_record())
	{
		$i = $GLOBALS['phpgw_setup']->db->f('account_id');
		$group_info[$i]['id']        = $GLOBALS['phpgw_setup']->db->f('account_id');
		$group_info[$i]['lid']       = $GLOBALS['phpgw_setup']->db->f('account_lid');
		$group_info[$i]['firstname'] = $GLOBALS['phpgw_setup']->db->f('account_firstname');
		$group_info[$i]['lastname']  = $GLOBALS['phpgw_setup']->db->f('account_lastname');
		$group_info[$i]['status']    = $GLOBALS['phpgw_setup']->db->f('account_status');
		$group_info[$i]['expires']   = $GLOBALS['phpgw_setup']->db->f('account_expires');
		$group_info[$i]['person_id'] = $GLOBALS['phpgw_setup']->db->f('person_id');
	}
	if(isset($_POST['cancel']) && $_POST['cancel'])
	{
		Header('Location: ldap.php');
		exit;
	}

	if(isset($_POST['submit']) && $_POST['submit'])
	{
		if(isset($_POST['ldapgroups']) && $_POST['ldapgroups'] && is_array($_POST['ldapgroups']))
		{
			$groups = CreateObject('phpgwapi.accounts');
			foreach($_POST['ldapgroups'] as $key => $groupid)
			{
				$id_exist = 0;
				$thisacctid    = $group_info[$groupid]['id'];
				$thisacctlid   = $group_info[$groupid]['lid'];
				$thisfirstname = $group_info[$groupid]['firstname'];
				$thislastname  = $group_info[$groupid]['lastname'];
		//		$thismembers   = $group_info[$groupid]['members'];
				$thisperson    = $group_info[$groupid]['person_id'];

				// Do some checks before we try to import the data to LDAP.
				if(!empty($thisacctid) && !empty($thisacctlid))
				{
				//	$groups->set_account($thisacctid, 'g');
					// Check if the account is already there.
					// If so, we won't try to create it again.
					$acct_exist = $groups->name2id($thisacctlid);
					if($acct_exist)
					{
						$thisacctid = $acct_exist;
					}
					$id_exist = $groups->exists(intval($thisacctid));

					/*
					echo '<br />accountid: ' . $thisacctid;
					echo '<br />accountlid: ' . $thisacctlid;
					echo '<br />exists: ' . $id_exist;
					*/
					
					/* If not, create it now. */
					if(!$id_exist)
					{
						echo "<br />\nAdding Group:  $thisacctlid (gid: $thisacctid)";
						$thisgroup_info = array
						(
								'type'      => 'g',
								'id'        => $thisacctid,
								'lid'       => $thisacctlid,
						//		'passwd'    => 'x',
								'firstname' => $thisfirstname,
								'lastname'  => $thislastname,
								'status'    => 'A',
								'expires'   => -1,
								'person_id' => $thisperson
						);

						add_account($thisgroup_info, 'g');
					}
					else
					{
						echo "<br />\nSkipping Group: $thisacctlid (gid: $thisacctlid) - Exists";
					}
				}
			}
		}

		if(isset($_POST['users']) && $_POST['users'] && is_array($_POST['users']))
		{
			$accounts = CreateObject('phpgwapi.accounts');
			foreach($_POST['users'] as $key => $accountid)
			{
				$id_exist = 0; $acct_exist = 0;
				$thisacctid    = $account_info[$accountid]['id'];
				$thisacctlid   = $account_info[$accountid]['lid'];
				$thisfirstname = $account_info[$accountid]['firstname'];
				$thislastname  = $account_info[$accountid]['lastname'];
				$thisperson    = $account_info[$accountid]['person_id'];				

				// Do some checks before we try to import the data.
				if(!empty($thisacctid) && !empty($thisacctlid))
				{
					$accounts->set_account($thisacctid, 'u');

					// Check if the account is already there.
					// If so, we won't try to create it again.
					$acct_exist = $acct->name2id($thisacctlid);
					if($acct_exist)
					{
						$thisacctid = $acct_exist;
					}

					$id_exist = $accounts->exists(intval($thisacctid));

					// If not, create it now.
					if($id_exist)
					{
						echo "<br />\nAdding User: $thisacctlid (uid: $thisacctid)";
						$thisaccount_info  = array
						(
									'type'      => 'u',
									'id'        => $thisacctid,
									'lid'       => $thisacctlid,
									'password'    => '12345678XXxx_&',
									'firstname' => $thisfirstname,
									'lastname'  => $thislastname,
									'status'    => 'A',
									'expires'   => -1,
									'person_id' => $thisperson
							//		'homedirectory'     => $config['ldap_account_home'] 
							//					. '/' . $thisacctlid,
							//		'loginshell'        => $config['ldap_account_shell']
						);

						add_account($thisaccount_info, 'u');
					}
					else
					{
						echo "<br />\nSkipping User: $thisacctlid (uid: $thisacctid) - Exists";
					}
				}
			}
		}
		$setup_complete = True;
	}

	$GLOBALS['phpgw_setup']->html->show_header('LDAP Export','','config',$ConfigDomain);

	if(isset($error) && $error)
	{
		//echo '<br /><center><b>Error:</b> '.$error.'</center>';
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',$error);
	}

	if(isset($setup_complete) && $setup_complete)
	{
		echo '<br /><center>'.lang('Export has been completed!  You will need to set the user passwords manually.').'</center>';
		echo '<br /><center>'.lang('Click <a href="index.php">here</a> to return to setup.').'</center>';
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
			$user_list .= '<option value="' . $account['id'] . '">'
				. $common->display_fullname($account['lid'],$account['firstname'],$account['lastname'])
				. '</option>';
		}

		foreach($account_info as $key => $account)
		{
			$admin_list .= '<option value="' . $account['id'] . '">'
				. $common->display_fullname($account['lid'],$account['firstname'],$account['lastname'])
				. '</option>';
		}
	}

	$group_list = '';
	//while(list($key,$group) = @each($group_info))
	if (is_array($group_info))
	{
		foreach($group_info as $key => $group)
		{
			$group_list .= '<option value="' . $group['id'] . '">'
				. $group['lid']
				. '</option>';
		}
	}

	$setup_tpl->set_var('action_url','ldapexport.php');
	$setup_tpl->set_var('users',$user_list);
	$setup_tpl->set_var('admins',$admin_list);
	$setup_tpl->set_var('ldapgroups',$group_list);
//	$setup_tpl->set_var('s_apps',$app_list);

	$setup_tpl->set_var('ldap_import',lang('LDAP export users'));
	$setup_tpl->set_var('description',lang("This section will help you export users and groups from phpGroupWare's account tables into your LDAP tree").'.');
	$setup_tpl->set_var('select_users',lang('Select which user(s) will be exported'));
	$setup_tpl->set_var('select_groups',lang('Select which group(s) will be exported (group membership will be maintained)'));
	$setup_tpl->set_var('form_submit','export');
	$setup_tpl->set_var('cancel',lang('Cancel'));

	$setup_tpl->pfp('out','header');
	if($account_info)
	{
		$setup_tpl->pfp('out','user_list');
	}
	if($group_info)
	{
		$setup_tpl->pfp('out','group_list');
	}
	$setup_tpl->pfp('out','submit');
	$setup_tpl->pfp('out','footer');

	$GLOBALS['phpgw_setup']->html->show_footer();
