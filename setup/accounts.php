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
	require_once './inc/functions.inc.php';

	// Authorize the user to use setup app and load the database
	// Does not return unless user is authorized
	if ( !$GLOBALS['phpgw_setup']->auth('Config')
		|| phpgw::get_var('cancel', 'bool', 'POST'))
	{
		Header('Location: index.php');
		exit;
	}

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
			$account->lid		= $acct['username'];
			$account->firstname	= $acct['firstname'];
			$account->lastname	= $acct['lastname'];
			$account->passwd	= $acct['password'];
			$account->enabled	= true;
			$account->expires	= -1;
		}
		else
		{
			$account			= new phpgwapi_group();
			$account->lid		= $acct['username'];
			$account->firstname = ucfirst($acct['username']);
		}

		return $GLOBALS['phpgw']->accounts->create($account, $groups, $acls, $modules);
	}

	/**
	 * Insert system default preferences
	 *
	 * @param integer $defaultgroup the id of the "default" group
	 *
	 * @return void
	 */
	function insert_default_prefs($defaultgroup)
	{
		$accountid = -2;
		$defaultprefs = array
		(
			'common' => array
			(
				'maxmatchs'		=> 10,
				'template_set'	=> 'portico',
				'theme'			=> 'portico',
				'tz_offset'		=> 0,
				'dateformat'	=> 'Y/m/d',
				'lang'			=> substr(phpgw::get_var('ConfigLang'), 0, 2),
				'timeformat'	=> 24,
				'default_app'	=> '',
				'currency'		=> '$',
				'show_help'		=> 0,
				'account_display'=> 'lastname',
				'rteditor'		=> 'ckeditor',
				'export_format'		=> 'excel',
			),

			'addressbook' => array(),

			'calendar' => array
			(
				'workdaystarts'				=> 9,
				'workdayends'				=> 17,
				'weekdaystarts'				=> 'Monday',
				'defaultcalendar'			=> 'month',
				'planner_start_with_group'	=> $defaultgroup
			)
		);

		foreach ($defaultprefs as $app => $prefs)
		{
			$prefs = $GLOBALS['phpgw_setup']->db->db_addslashes(serialize($prefs));
			$sql = 'INSERT INTO phpgw_preferences(preference_owner, preference_app, preference_value)'
					. " VALUES({$accountid}, '{$app}', '{$prefs}')";
			$GLOBALS['phpgw_setup']->db->query($sql, __LINE__, __FILE__);
		}
	}

	/**
	 * Validate the data for the admin user account
	 *
	 * @param string &$username the login id for the admin user -
	 * @param string $passwd    the password for the new user
	 * @param string $passwd2   the verification password for the new user
	 * @param string $fname     the first name of the administrator
	 * @param string $lname     the lastname of the administrator
	 *
	 * @return array list of errors - empty array if valid
	 *
	 * @internal we pass the username by ref so it can be unset if invalid
	 */
	function validate_admin(&$username, $passwd, &$passwd2, $fname, $lname)
	{
		phpgw::import_class('phpgwapi.globally_denied');
		$errors = array();

		if ( $passwd != $passwd2 )
		{
			$errors[] = lang('Passwords did not match, please re-enter');
		}
		else
		{
			$account	= new phpgwapi_user();
			try
			{
				$account->validate_password($passwd);
			}
			catch(Exception $e)
			{
				$errors[] = $e->getMessage();
			}
		}

		if ( !$username )
		{
			$errors[] = lang('You must enter a username for the admin');
		}
		else if ( phpgwapi_globally_denied::user($username) )
		{
			$errors[] = lang('You can not use %1 as the admin username, please try again with another username', $username);
			$username = '';
		}

		return $errors;
	}


	$db =& $GLOBALS['phpgw_setup']->db;

	// set some sane default values
	$passwd		= '';
	$passwd2	= $passwd;
	$username	= 'sysadmin';
	$fname		= 'System';
	$lname		= 'Administrator';

	$errors = array();
	$GLOBALS['phpgw_setup']->loaddb();
	if ( phpgw::get_var('submit', 'string', 'POST') )
	{
		// set some sane defaults
		$GLOBALS['phpgw_info']['server']['ldap_host']				= '';
		$GLOBALS['phpgw_info']['server']['ldap_context']			= '';
		$GLOBALS['phpgw_info']['server']['ldap_group_context']		= '';
		$GLOBALS['phpgw_info']['server']['ldap_root_dn']			= '';
		$GLOBALS['phpgw_info']['server']['ldap_root_pw']			= '';
		$GLOBALS['phpgw_info']['server']['ldap_extra_attributes']	= false;
		$GLOBALS['phpgw_info']['server']['ldap_account_home']		= '/dev/null';
		$GLOBALS['phpgw_info']['server']['ldap_account_shell']		= '/bin/false';
		$GLOBALS['phpgw_info']['server']['ldap_encryption_type']	= 'ssha';
		$GLOBALS['phpgw_info']['server']['account_repository']		= 'sql';
		$GLOBALS['phpgw_info']['server']['auth_type']				= 'sql';
		$GLOBALS['phpgw_info']['server']['encryption_type']			= 'ssha';
		$GLOBALS['phpgw_info']['server']['password_level']         = 'NONALPHA';
		$GLOBALS['phpgw_info']['server']['account_min_id']			= 1000;
		$GLOBALS['phpgw_info']['server']['account_max_id']			= 65535;
		$GLOBALS['phpgw_info']['server']['group_min_id']			= 500;
		$GLOBALS['phpgw_info']['server']['group_max_id']			= 999;

		// Load up the real config values
		$sql = 'SELECT config_name,config_value FROM phpgw_config'
				. " WHERE config_name LIKE 'ldap%' OR config_name LIKE '%_id'"
					. " OR config_name = 'account_repository'"
					. " OR config_name = 'auth_type'"
					. " OR config_name = 'encryption_type'"
					. " OR config_name = 'encryptkey'"
					. " OR config_name = 'password_level'"
					. " OR config_name = 'webserver_url'";

		$GLOBALS['phpgw_setup']->db->query($sql, __LINE__, __FILE__);
		while ( $GLOBALS['phpgw_setup']->db->next_record() )
		{
			$GLOBALS['phpgw_info']['server'][$db->f('config_name', true)] = $db->f('config_value', true);
		}

		$GLOBALS['phpgw'] = new phpgw;
		$GLOBALS['phpgw']->db       =& $db;
		$GLOBALS['phpgw']->accounts = CreateObject('phpgwapi.accounts');
		$GLOBALS['phpgw']->acl		= CreateObject('phpgwapi.acl');
		$GLOBALS['phpgw']->crypto->init(array(md5(session_id() . $GLOBALS['phpgw_info']['server']['encryptkey']), $GLOBALS['phpgw_info']['server']['mcrypt_iv']));

		/* Posted admin data */
		// We need to reverse the entities or the password can be mangled
		$passwd			= html_entity_decode(phpgw::get_var('passwd', 'string', 'POST'));
		$passwd2		= html_entity_decode(phpgw::get_var('passwd2', 'string', 'POST'));
		$username		= phpgw::get_var('username', 'string', 'POST');
		$fname			= phpgw::get_var('fname', 'string', 'POST');
		$lname			= phpgw::get_var('lname', 'string', 'POST');

		if ( ($GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap')
			&& !$GLOBALS['phpgw']->accounts->connected )
		{
			echo "<strong>Error: Error connecting to LDAP server {$GLOBALS['phpgw_info']['server']['ldap_host']}</strong><br>";
			exit;
		}

		$errors = validate_admin($username, $passwd, $passwd2, $fname, $lname);

		if(in_array($username, array('admins', 'default')))
		{
			$errors[] = lang('That loginid has already been taken');
		}

		if ( !count($errors) )
		{
			$admin_acct = array
			(
				'username'	=> $username,
				'firstname'	=> $fname,
				'lastname'	=> $lname,
				'password'	=> $passwd
			);

			// Begin transaction for acl, etc
			// FIXME: Conflicting transactions - there are transactions in phpgwapi_accounts_::create() and acl::save_repository()
			//$GLOBALS['phpgw_setup']->db->transaction_begin();

			// Now, clear out existing tables
			$contacts_to_delete = $GLOBALS['phpgw']->accounts->get_account_with_contact();
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_accounts');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_preferences');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_acl');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_mapping');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_group_map');
			$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_nextid WHERE appname = 'groups' OR appname = 'accounts'");
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_contact');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_contact_person');
			$GLOBALS['phpgw_setup']->db->query('DELETE FROM phpgw_contact_org');

			// Clean out LDAP
			if( $GLOBALS['phpgw_info']['server']['account_repository'] == 'ldap' || $GLOBALS['phpgw_info']['server']['account_repository'] = 'sqlldap')
			{
				$accounts = $GLOBALS['phpgw']->accounts->get_list('accounts', -1, '', '', '',-1);

				foreach ($accounts as $account)
				{
					$GLOBALS['phpgw']->accounts->delete($account->id);
				}
				$accounts = $GLOBALS['phpgw']->accounts->get_list('groups', -1, '', '', '',-1);
				foreach ($accounts as $account)
				{
					$GLOBALS['phpgw']->accounts->delete($account->id);
				}
			}

			$contacts = CreateObject('phpgwapi.contacts');
			if(is_array($contacts_to_delete))
			{
				foreach($contacts_to_delete as $contact_id)
				{
					$contacts->delete($contact_id, '', false);
				}
			}
			unset($contacts_to_delete);

			/* Create the groups */
			// Group perms for the default group
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

			$acls[] = array
			(
				'appname'	=> 'preferences',
				'location'	=> 'changepassword',
				'rights'	=> 1
			);

			$group = array('username' => 'default');
			$defaultgroupid = add_account($group, 'g', array(), $modules);

			$group = array('username' => 'admins');
			$admingroupid   = add_account($group, 'g', array(), array('admin'));

			insert_default_prefs($defaultgroupid);	// set some default prefs

			$groups = array($defaultgroupid, $admingroupid);

			$accountid = add_account($admin_acct, 'u', $groups, array('admin'), $acls);
			Header('Location: index.php');
			exit;
		}
	}

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);
	$setup_tpl->set_file(array(
		'T_head'       => 'head.tpl',
		'T_footer'     => 'footer.tpl',
		'T_alert_msg'  => 'msg_alert_msg.tpl',
		'T_login_main' => 'login_main.tpl',
		'T_login_stage_header' => 'login_stage_header.tpl',
		'T_accounts' => 'accounts.tpl'
	));
	$setup_tpl->set_block('T_login_stage_header','B_multi_domain','V_multi_domain');
	$setup_tpl->set_block('T_login_stage_header','B_single_domain','V_single_domain');
	$setup_tpl->set_var('lang_cookies_must_be_enabled', lang('<b>NOTE:</b> You must have cookies enabled to use setup and header admin!') );

	$GLOBALS['phpgw_setup']->html->show_header(lang('Demo Server Setup'));

	$setup_tpl->set_var('action_url', 'accounts.php');

	/* detect whether anything will be deleted before alerting */
	$db->query('SELECT config_value FROM phpgw_config'
			. " WHERE config_name = 'account_repository'", __LINE__, __FILE__);
	$db->next_record();
	$account_repository = $db->f('config_value');

	$account_creation_notice = lang("This will create an admininstrator account");
	if ( $account_repository == 'sql' )
	{
		$db->query('SELECT COUNT(*) AS cnt FROM phpgw_accounts', __LINE__, __FILE__);
		$db->next_record();
		$number_of_accounts = $db->f('cnt');
		if ( $number_of_accounts )
		{
			$account_creation_notice .= "\n"
				. lang('<b>!!!THIS WILL DELETE ALL EXISTING ACCOUNTS!!!</b><br>');
		}
	}

	$error_msg = '';
	if ( count($errors) )
	{
		$error_msg = '<div class="msg">' . implode("<br>\n", $errors) . '</div>';
	}

	$setup_tpl->set_var(array
	(
		'errors'			=> $error_msg,
		'description'		=> $account_creation_notice,
		'title'				=> lang('create accounts'),
		'detailadmin'		=> lang('Details for admininstrator account'),
		'adminusername'		=> lang('Admin username'),
		'adminfirstname'	=> lang('Admin first name'),
		'adminlastname'		=> lang('Admin last name'),
		'adminpassword'		=> lang('Admin password'),
		'adminpassword2'	=> lang('Re-enter password'),
		'lang_submit'		=> lang('Save'),
		'lang_cancel'		=> lang('Cancel'),
		'val_username'		=> $username,
		'val_fname'			=> $fname,
		'val_lname'			=> $lname,
	));

	$setup_tpl->pparse('out','T_accounts');
	$GLOBALS['phpgw_setup']->html->show_footer();
