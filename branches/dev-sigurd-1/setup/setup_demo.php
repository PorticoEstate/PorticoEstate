<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	// Little file to setup a demo install

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
	// Does not return unless user is authorized
	if(!$GLOBALS['phpgw_setup']->auth('Config') || phpgw::get_var('cancel', 'bool', 'POST'))
	{
		Header('Location: index.php');
		exit;
	}

	/**
	 * Add account
	 * 
	 * @param string $username Username to add
	 * @param string $first First name ow new user
	 * @param string $last Last name of new user
	 * @param string $passwd Password for new user
	 * @param string $type Account type; u = user
	 * @return string Account ID
	 */
	function add_account($username, $first, $last, $passwd, $type='u')
	{
		$person_id = 0;
		if($type=='u')
		{
			$addressmaster_id = -3;//default value
			$contacts = CreateObject('phpgwapi.contacts');
			$principal = array
			(
				'per_first_name'	=> $first,
				'per_last_name'		=> $last,
				'access'			=> 'public',
				'owner'				=> $addressmaster_id,
				'preferred_org'		=> 0,
				'preferred_address'	=> 0
			);
			$contact_type = $contacts->search_contact_type('Persons');
			$person_id = $contacts->add_contact($contact_type, $principal);
		}
		$account_info = array
		(
			'account_type'		=> $type,
			'account_lid'		=> $username,
			'account_passwd'	=> $passwd,
			'account_firstname'	=> $first,
			'account_lastname'	=> $last,
			'account_status'	=> 'A',
			'account_expires'	=> -1,
			'person_id'			=> $person_id
		);

		$GLOBALS['phpgw']->accounts->create($account_info);

		$account_id = $GLOBALS['phpgw']->accounts->name2id($username);

		return $account_id;
	}

	/**
	 * Insert default preferences
	 * 
	 * @param int $accountid the account
	 * @param int $defaultgroupid the primary group id
	 */
	function insert_default_prefs($accountid, $defaultgroupid)
	{
		$defaultprefs = unserialize('a:3:{s:6:"common";a:10:{s:9:"maxmatchs";s:2:"15";s:12:"template_set";s:5:"idots";s:5:"theme";s:5:"idots";s:13:"navbar_format";s:14:"text_and_icons";s:9:"tz_offset";s:0:"";s:10:"dateformat";s:5:"Y/m/d";s:10:"timeformat";s:2:"24";s:4:"lang";s:2:"en";s:11:"default_app";s:0:"";s:8:"currency";s:1:"$";}s:11:"addressbook";a:1:{s:0:"";s:4:"True";}s:8:"calendar";a:4:{s:13:"workdaystarts";s:1:"9";s:11:"workdayends";s:2:"17";s:13:"weekdaystarts";s:6:"Monday";s:15:"defaultcalendar";s:9:"month.php";}}');
		$defaultprefs['common']['show_help'] = '1';
		$defaultprefs['calendar']['planner_start_with_group'] = $defaultgroupid;

		foreach ($defaultprefs as $app => $prefs)
		{
			$prefs = $GLOBALS['phpgw_setup']->db->db_addslashes(serialize($prefs));
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_preferences(preference_owner,preference_app,preference_value) VALUES($accountid,'$app','$prefs')",__FILE__,__LINE__);
		}
	}

	$passwd		= $passwd2 = '';
	$username	= '';
	$fname		= '';
	$lname		= '';
	$create_demo = false;
	
	$errors = array();
	$GLOBALS['phpgw_setup']->loaddb();
	if ( isset($_POST['submit']) && $_POST['submit'] )
	{
		/* Posted admin data */
		$passwd		= $_POST['passwd'];
		$passwd2	= $_POST['passwd2'];
		$username	= $_POST['username'];
		$fname		= $_POST['fname'];
		$lname		= $_POST['lname'];
		$create_demo = isset($_POST['create_demo']) && $_POST['create_demo'];

		// We do this here so the denied accounts array is available - these is some expense in this, but it is a run once in a lifetime function - skwashd Nov2006
		/* Load up some configured values */
		$GLOBALS['phpgw_setup']->db->query("SELECT config_name,config_value FROM phpgw_config WHERE config_name LIKE 'ldap%' OR config_name LIKE '%_id' OR config_name='account_repository'",__LINE__,__FILE__);
		while ($GLOBALS['phpgw_setup']->db->next_record())
		{
			$config[$GLOBALS['phpgw_setup']->db->f('config_name')] = $GLOBALS['phpgw_setup']->db->f('config_value');
		}
		$GLOBALS['phpgw_info']['server']['ldap_host']				= isset($config['ldap_host']) ? $config['ldap_host'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_context']			= isset($config['ldap_context']) ? $config['ldap_context'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_group_context']		= isset($config['ldap_group_context']) ? $config['ldap_group_content'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_root_dn']			= isset($config['ldap_root_dn']) ? $config['ldap_root_dn'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_root_pw']			= isset($config['ldap_root_pw']) ? $config['ldap_root_pw'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_extra_attributes']	= isset($config['ldap_extra_attributes']) ? $config['ldap_extra_attributes'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_account_home']		= isset($config['ldap_account_home']) ? $config['ldap_account_home'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_account_shell']		= isset($config['ldap_account_shell']) ? $config['ldap_account_shell'] : '';
		$GLOBALS['phpgw_info']['server']['ldap_encryption_type']	= isset($config['ldap_encryption_type']) ? $config['ldap_encryption_type'] : '';
		$GLOBALS['phpgw_info']['server']['account_repository']		= isset($config['account_repository']) ? $config['account_repository'] : '';
		$GLOBALS['phpgw_info']['server']['account_min_id']			= isset($config['account_min_id']) ? $config['account_min_id'] : 1000;
		$GLOBALS['phpgw_info']['server']['account_max_id']			= isset($config['account_max_id']) ? $config['account_max_id'] : 65535;
		$GLOBALS['phpgw_info']['server']['group_min_id']			= isset($config['group_min_id']) ? $config['group_min_id'] : 500;
		$GLOBALS['phpgw_info']['server']['group_max_id']			= isset($config['group_max_id']) ? $config['group_max_id'] : 999;
		unset($config);
		
		$GLOBALS['phpgw'] = new phpgw;
		$GLOBALS['phpgw']->db       = $GLOBALS['phpgw_setup']->db;
		$GLOBALS['phpgw']->common   = CreateObject('phpgwapi.common');
		$GLOBALS['phpgw']->accounts = CreateObject('phpgwapi.accounts');
		if(($GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap') &&
			!$GLOBALS['phpgw']->accounts->ds)
		{
			printf("<b>Error: Error connecting to LDAP server %s!</b><br />",$GLOBALS['phpgw_info']['server']['ldap_host']);
			exit;
		}
		
		if ( $passwd == '' || $passwd2 == '' )
		{
			$errors[] = lang('Password can not be empty');
		}
		
		if ( $passwd != $passwd2 )
		{
			$errors[] = lang('Passwords did not match, please re-enter');
		}

		if ( !$username )
		{
			$errors[] = lang('You must enter a username for the admin');
		}
		else if ( isset($GLOBALS['phpgw_info']['server']['global_denied_users'][$username]) )
		{
			$errors[] = lang('You can not use %1 as the admin username, please try again with another username', $username);
			$username = '';
		}

		if ( !count($errors) )
		{
			// Begin transaction for acl, etc
			$GLOBALS['phpgw_setup']->db->transaction_begin();

			// Now, clear out existing tables
			$contacts_to_delete = $GLOBALS['phpgw']->accounts->get_account_with_contact();
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_accounts');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_preferences');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_acl');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_mapping');
			$contacts = CreateObject('phpgwapi.contacts');
			if(is_array($contacts_to_delete))
			{
				foreach($contacts_to_delete as $contact_id)
				{
					$contacts->delete($contact_id, '', False);
				}
			}
			unset($contacts_to_delete);
			/* Create the demo groups */
			$defaultgroupid = intval(add_account('Default','Default','Group',$passwd,'g'));
			$admingroupid   = intval(add_account('Admins','Admin', 'Group',$passwd,'g'));

			// Group perms for the default group
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('addressbook','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('filemanager','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('calendar','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('email','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('notes','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('todo','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('manual','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('preferences','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('felamimail','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('property','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('hrm','run'," . $defaultgroupid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('sms','run'," . $defaultgroupid . ", 1)");

			insert_default_prefs(-2, $defaultgroupid);	// set some default prefs

			/* Creation of the demo accounts is optional - the checkbox is on by default. */
			if ( $create_demo )
			{
				foreach ( array('demo', 'demo2', 'demo3') as $lid )
				{
					/* Create records for demo accounts */
					$accountid = add_account($lid, 'Demo', 'Account', 'guest');

					/* User permissions based on group membership with additional user perm to deny password change for demo users  */
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('preferences','changepassword', " . $accountid . ",0)");
					$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('phpgw_group', '" . $defaultgroupid."'," . $accountid . ",1)");
				}
			}

			/* Create records for administrator account */
			$accountid = add_account($username,$fname,$lname,$passwd);

			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('phpgw_group','" . $defaultgroupid."'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('phpgw_group','" . $admingroupid."'," . $accountid . ",1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('preferences','changepassword', " . $accountid . ",1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('admin','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('addressbook','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('filemanager','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('calendar','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('email','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('notes','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('nntp','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('todo','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('manual','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_acl(acl_appname,acl_location,acl_account,acl_rights) VALUES('infolog','run'," . $accountid . ", 1)");
			$GLOBALS['phpgw_setup']->db->transaction_commit();

			Header('Location: index.php');
			exit;
		}
	}
	
	if( !isset($_POST['submit']) || count($errors) )
	{
		$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
		$setup_tpl = CreateObject('phpgwapi.Template',$tpl_root);
		$setup_tpl->set_file(array(
			'T_head'       => 'head.tpl',
			'T_footer'     => 'footer.tpl',
			'T_alert_msg'  => 'msg_alert_msg.tpl',
			'T_login_main' => 'login_main.tpl',
			'T_login_stage_header' => 'login_stage_header.tpl',
			'T_setup_demo' => 'setup_demo.tpl'
		));
		$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
		$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
		$setup_tpl->set_var('lang_cookies_must_be_enabled', lang('<b>NOTE:</b> You must have cookies enabled to use setup and header admin!') );

		$GLOBALS['phpgw_setup']->html->show_header(lang('Demo Server Setup'));

		$setup_tpl->set_var('action_url','setup_demo.php');

		/* detect whether anything will be deleted before alerting */
		$phpgw_setup->db->query("select config_value FROM phpgw_config WHERE config_name='account_repository'");
		$phpgw_setup->db->next_record();
		$account_repository = $phpgw_setup->db->f(0);

		$account_creation_notice = lang('This will create an admin account and (optionally) 3 demo accounts.<br />The username/passwords are: demo/guest, demo2/guest and demo3/guest.<br />');
		if ($account_repository == 'sql')
		{
			$phpgw_setup->db->query("select count(*) from phpgw_accounts");
			$phpgw_setup->db->next_record();
			$number_of_accounts = (int) $phpgw_setup->db->f(0);
			if ($number_of_accounts>0)
			{
			 	$account_creation_notice .= lang('<b>!!!THIS WILL DELETE ALL EXISTING ACCOUNTS!!!</b><br />');
			}
		}
		$setup_tpl->set_var(array
		(
			'errors'			=> count($errors) ? ('<div class="msg">'.implode("<br>\n", $errors).'</div>') : '',
			'description'		=> $account_creation_notice,
			'detailadmin'		=> lang('Details for Admin account'),
			'adminusername'		=> lang('Admin username'),
			'adminfirstname'	=> lang('Admin first name'),
			'adminlastname'		=> lang('Admin last name'),
			'adminpassword'		=> lang('Admin password'),
			'adminpassword2'	=> lang('Re-enter password'),
			'create_demo_accounts' => lang('Create demo accounts'),
			'lang_submit'		=> lang('Save'),
			'lang_cancel'		=> lang('Cancel'),
			'val_username'		=> $username,
			'val_fname'			=> $fname,
			'val_lname'			=> $lname,
			'checked_demo'		=> $create_demo ? ' checked' : ''
		));

		$setup_tpl->pparse('out','T_setup_demo');
		$GLOBALS['phpgw_setup']->html->show_footer();
	}
