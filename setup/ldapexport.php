<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id: ldapexport.php 15836 2005-04-17 13:18:24Z powerstat $
	*/

	$phpgw_info = array();
	$phpgw_info["flags"] = array(
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

	//This stops timeout problems for larger conversions
	@set_time_limit(0);

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
	}
	$phpgw = new phpgw;
	$phpgw->common = CreateObject('phpgwapi.common');

	$common = $phpgw->common;
	$GLOBALS['phpgw_setup']->loaddb();
	$phpgw->db = $GLOBALS['phpgw_setup']->db;

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
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
		$noldapconnection = True;
	}

	if($noldapconnection)
	{
		Header('Location: config.php?error=badldapconnection');
		exit;
	}

	$sql = "SELECT * FROM phpgw_accounts WHERE account_type='u'";
	$GLOBALS['phpgw_setup']->db->query($sql,__LINE__,__FILE__);
	while($GLOBALS['phpgw_setup']->db->next_record())
	{
		$i = $GLOBALS['phpgw_setup']->db->f('account_id');
		$account_info[$i]['account_id']        = $GLOBALS['phpgw_setup']->db->f('account_id');
		$account_info[$i]['account_lid']       = $GLOBALS['phpgw_setup']->db->f('account_lid');
		$account_info[$i]['account_firstname'] = $GLOBALS['phpgw_setup']->db->f('account_firstname');
		$account_info[$i]['account_lastname']  = $GLOBALS['phpgw_setup']->db->f('account_lastname');
		$account_info[$i]['account_status']    = $GLOBALS['phpgw_setup']->db->f('account_status');
		$account_info[$i]['account_expires']   = $GLOBALS['phpgw_setup']->db->f('account_expires');
	}

	while(list($key,$data) = @each($account_info))
	{
		$tmp = $data['account_id'];
		$newaccount[$tmp] = $data;
	}
	$account_info = $newaccount;

	$sql = "SELECT * FROM phpgw_accounts WHERE account_type='g'";
	$GLOBALS['phpgw_setup']->db->query($sql,__LINE__,__FILE__);
	while($GLOBALS['phpgw_setup']->db->next_record())
	{
		$i = $GLOBALS['phpgw_setup']->db->f('account_id');
		$group_info[$i]['account_id']        = $GLOBALS['phpgw_setup']->db->f('account_id');
		$group_info[$i]['account_lid']       = $GLOBALS['phpgw_setup']->db->f('account_lid');
		$group_info[$i]['account_firstname'] = $GLOBALS['phpgw_setup']->db->f('account_firstname');
		$group_info[$i]['account_lastname']  = $GLOBALS['phpgw_setup']->db->f('account_lastname');
		$group_info[$i]['account_status']    = $GLOBALS['phpgw_setup']->db->f('account_status');
		$group_info[$i]['account_expires']   = $GLOBALS['phpgw_setup']->db->f('account_expires');
	}

	if($_POST['cancel'])
	{
		Header('Location: ldap.php');
		exit;
	}

	if($_POST['submit'])
	{
		if($_POST['ldapgroups'] && is_array($_POST['ldapgroups']))
		{

			foreach($_POST['ldapgroups'] as $key => $groupid)
			{
				$id_exist = 0;
				$thisacctid    = $group_info[$groupid]['account_id'];
				$thisacctlid   = $group_info[$groupid]['account_lid'];
				$thisfirstname = $group_info[$groupid]['account_firstname'];
				$thislastname  = $group_info[$groupid]['account_lastname'];
				$thismembers   = $group_info[$groupid]['members'];

				// Do some checks before we try to import the data to LDAP.
				if(!empty($thisacctid) && !empty($thisacctlid))
				{
					$groups = CreateObject('phpgwapi.accounts',intval($thisacctid));
					$groups->db = $GLOBALS['phpgw_setup']->db;

					// Check if the account is already there.
					// If so, we won't try to create it again.
					$acct_exist = $acct->groupName2id($thisacctlid);
					if($acct_exist)
					{
						$thisacctid = $acct_exist;
					}
					$id_exist = $groups->group_exists(intval($thisacctid));
					
					/*
					echo '<br />accountid: ' . $thisacctid;
					echo '<br />accountlid: ' . $thisacctlid;
					echo '<br />exists: ' . $id_exist;
					*/
					
					/* If not, create it now. */
					if(!$id_exist)
					{
						echo "<br />\nAdding Group:  $thisacctlid (gid: $thisacctid)";
						$groups->create(
							array(
								'account_type'      => 'g',
								'account_id'        => $thisacctid,
								'account_lid'       => $thisacctlid,
								'account_passwd'    => 'x',
								'account_firstname' => $thisfirstname,
								'account_lastname'  => $thislastname,
								'account_status'    => 'A',
								'account_expires'   => -1
							)
						);
					}
					else
					{
						echo "<br />\nSkipping Group: $thisacctlid (gid: $thisacctlid) - Exists";
					}
				}
			}
		}

		if($_POST['users'] && is_array($_POST['users']))
		{
			foreach($_POST['users'] as $key => $accountid)
			{
				$id_exist = 0; $acct_exist = 0;
				$thisacctid    = $account_info[$accountid]['account_id'];
				$thisacctlid   = $account_info[$accountid]['account_lid'];
				$thisfirstname = $account_info[$accountid]['account_firstname'];
				$thislastname  = $account_info[$accountid]['account_lastname'];

				// Do some checks before we try to import the data.
				if(!empty($thisacctid) && !empty($thisacctlid))
				{
					$accounts = CreateObject('phpgwapi.accounts',intval($thisacctid));
					$accounts->db = $GLOBALS['phpgw_setup']->db;

					// Check if the account is already there.
					// If so, we won't try to create it again.
					$acct_exist = $acct->name2id($thisacctlid);
					if($acct_exist)
					{
						$thisacctid = $acct_exist;
					}
					/* create_account handles existing accounts
					$id_exist = $accounts->exists(intval($thisacctid));
					*/
					// If not, create it now.
					if(!$id_exist)
					{
						echo "<br />\nAdding User: $thisacctlid (uid: $thisacctid)";
						$accounts->create(
								array(
									'account_type'      => 'u',
									'account_id'        => $thisacctid,
									'account_lid'       => $thisacctlid,
									'account_passwd'    => 'x',
									'account_firstname' => $thisfirstname,
									'account_lastname'  => $thislastname,
									'account_status'    => 'A',
									'account_expires'   => -1,
									'homedirectory'     => $config['ldap_account_home'] 
												. '/' . $thisacctlid,
									'loginshell'        => $config['ldap_account_shell']
								)
						);
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

	if($error)
	{
		//echo '<br /><center><b>Error:</b> '.$error.'</center>';
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',$error);
	}

	if($setup_complete)
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

	while(list($key,$account) = @each($account_info))
	{
		$user_list .= '<option value="' . $account['account_id'] . '">'
			. $common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
			. '</option>';
	}

	@reset($account_info);
	while(list($key,$account) = @each($account_info))
	{
		$admin_list .= '<option value="' . $account['account_id'] . '">'
			. $common->display_fullname($account['account_lid'],$account['account_firstname'],$account['account_lastname'])
			. '</option>';
	}

	while(list($key,$group) = @each($group_info))
	{
		$group_list .= '<option value="' . $group['account_id'] . '">'
			. $group['account_lid']
			. '</option>';
	}

	$setup_tpl->set_var('action_url','ldapexport.php');
	$setup_tpl->set_var('users',$user_list);
	$setup_tpl->set_var('admins',$admin_list);
	$setup_tpl->set_var('ldapgroups',$group_list);
	$setup_tpl->set_var('s_apps',$app_list);

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
?>
