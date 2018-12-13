<?php
	/**
	* Setup
	*
	* @copyright Copyright (C) 2000-2005 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package setup
	* @version $Id$
	*/

	if ( isset($_POST['cancel']) && $_POST['cancel'] )
	{
		Header('Location: index.php');
		exit;
	}

	$GLOBALS['phpgw_info'] = array();
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'noheader' => True,
		'nonavbar' => True,
		'currentapp' => 'home',
		'noapi' => True
	);
	
	/**
	 * Include setup functions
	 */
	include('./inc/functions.inc.php');

	// Authorize the user to use setup app and load the database
	// Does not return unless user is authorized
	if(!$GLOBALS['phpgw_setup']->auth('Config'))
	{
		Header('Location: index.php');
		exit;
	}

	$tpl_root = $GLOBALS['phpgw_setup']->html->setup_tpl_dir('setup');
	$setup_tpl = CreateObject('phpgwapi.template',$tpl_root);

	/**
	 * Test if $path lies within the webservers document-root
	 * 
	 * @param string $path File/directory path
	 * @return boolean True when path is within webservers document-root; otherwise false
	 */
	function in_docroot($path)
	{
		$docroots = array(PHPGW_SERVER_ROOT, $_SERVER['DOCUMENT_ROOT']);
		
		foreach ($docroots as $docroot)
		{
			$len = strlen($docroot);

			if ($docroot == substr($path,0,$len))
			{
				$rest = substr($path,$len);

				if (!strlen($rest) || $rest[0] == '/')
				{
					return true;
				}
			}
		}
		return false;
	}

	$setup_tpl->set_file(array(
		'T_head' => 'head.tpl',
		'T_footer' => 'footer.tpl',
		'T_alert_msg' => 'msg_alert_msg.tpl',
		'T_config_pre_script' => 'config_pre_script.tpl',
		'T_config_post_script' => 'config_post_script.tpl'
	));

	$setup_tpl->set_var('lang_cookies_must_be_enabled', lang('<b>NOTE:</b> You must have cookies enabled to use setup and header admin!') );
	
	// Following to ensure windows file paths are saved correctly
	//set_magic_quotes_runtime(0);

	$GLOBALS['phpgw_setup']->loaddb();

	// Guessing default values.
	$GLOBALS['current_config']['hostname']  = $_SERVER['HTTP_HOST'];
	// files-dir is not longer allowed in document root, for security reasons !!!
	$GLOBALS['current_config']['files_dir'] = '/outside/webserver/docroot';

	if( @is_dir('/tmp') )
	{
		$GLOBALS['current_config']['temp_dir'] = '/tmp';
	}
	elseif( @is_dir('C:\\TEMP') )
	{
		$GLOBALS['current_config']['temp_dir'] = 'C:\\TEMP';
	}
	else
	{
		$GLOBALS['current_config']['temp_dir'] = '/path/to/temp/dir';
	}
	// guessing the phpGW url
	$parts = explode('/',$_SERVER['PHP_SELF']);
	unset($parts[count($parts)-1]); // config.php
	unset($parts[count($parts)-1]); // setup
	$GLOBALS['current_config']['webserver_url'] = implode('/',$parts);

	// Add some sane defaults for accounts
	$GLOBALS['current_config']['account_min_id'] = 1000;
	$GLOBALS['current_config']['account_max_id'] = 65535;
	$GLOBALS['current_config']['group_min_id'] = 500;
	$GLOBALS['current_config']['group_max_id'] = 999;
	$GLOBALS['current_config']['ldap_account_home'] = '/noexistant';
	$GLOBALS['current_config']['ldap_account_shell'] = '/bin/false';
	$GLOBALS['current_config']['ldap_host'] = 'localhost';
	
	$GLOBALS['current_config']['encryptkey'] = md5(time() . $_SERVER['HTTP_HOST']); // random enough


	$setup_info = $GLOBALS['phpgw_setup']->detection->get_db_versions();
	$newsettings = phpgw::get_var('newsettings', 'string', 'POST');
	
	$files_in_docroot = (isset($newsettings['files_dir']))? in_docroot($newsettings['files_dir']) : false ;
	if ( phpgw::get_var('submit', 'string', 'POST') && is_array($newsettings) && !$files_in_docroot)
	{
		phpgw::import_class('phpgwapi.datetime');
		switch (intval($newsettings['daytime_port']))
		{
			case 13:
				$newsettings['tz_offset'] = phpgwapi_datetime::getntpoffset();
				break;
			case 80:
				$newsettings['tz_offset'] = phpgwapi_datetime::gethttpoffset();
				break;
			default:
				$newsettings['tz_offset'] = phpgwapi_datetime::getbestguess();
				break;
		}

		$GLOBALS['phpgw_setup']->db->transaction_begin();
		
		foreach( $newsettings as $setting => $value ) 
		{
		//	echo '<br />Updating: ' . $setting . '=' . $value;
			
			$setting = $GLOBALS['phpgw_setup']->db->db_addslashes($setting);

			/* Don't erase passwords, since we also do not print them below */
			if ( $value 
				|| (!preg_match('/passwd/', $setting) && !preg_match('/password/', $setting) && !preg_match('/root_pw/', $setting)) )
			{
				$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_config WHERE config_name='{$setting}'", __LINE__, __FILE__);
			}
			/* cookie_domain has to allow an empty value*/
			if($value || $setting == 'cookie_domain')
			{
				$value = $GLOBALS['phpgw_setup']->db->db_addslashes($value);
				$GLOBALS['phpgw_setup']->db->query("INSERT INTO phpgw_config (config_app,config_name, config_value) VALUES ('phpgwapi', '{$setting}','{$value}')", __LINE__, __FILE__);
			}
		}
		$GLOBALS['phpgw_setup']->db->transaction_commit();		
		
		// Add cleaning of app_sessions per skeeter, but with a check for the table being there, just in case
		foreach ( (array) $GLOBALS['phpgw_setup']->db->table_names() as $key => $val)
		{
			$tables[] = $val;
		}
		if(in_array('phpgw_app_sessions',$tables))
		{
			$GLOBALS['phpgw_setup']->db->lock(array('phpgw_app_sessions'));
			$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_app_sessions WHERE sessionid = '0' and loginid = '0' and app = 'phpgwapi' and location = 'config'",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->query("DELETE FROM phpgw_app_sessions WHERE app = 'phpgwapi' and location = 'phpgw_info_cache'",__LINE__,__FILE__);
			$GLOBALS['phpgw_setup']->db->unlock();
		}
		
		if($newsettings['auth_type'] == 'ldap')
		{
			Header('Location: '.$newsettings['webserver_url'].'/setup/ldap.php');
			exit;
		}
		else
		{
			Header('Location: index.php');
			exit;
		}
		
		//exit;
	}

	if(!isset($newsettings['auth_type']) || $newsettings['auth_type'] != 'ldap')
	{
		$GLOBALS['phpgw_setup']->html->show_header(lang('Configuration'),False,'config',$ConfigDomain . '(' . $phpgw_domain[$ConfigDomain]["db_type"] . ')');
	}

	$GLOBALS['phpgw_setup']->db->query('SELECT * FROM phpgw_config');
	while($GLOBALS['phpgw_setup']->db->next_record())
	{
		$GLOBALS['current_config'][$GLOBALS['phpgw_setup']->db->f('config_name')] = $GLOBALS['phpgw_setup']->db->f('config_value');
	}
	
	// are we here because of an error: files-dir in docroot
	if (isset($_POST['newsettings']) && is_array($_POST['newsettings']) && $files_in_docroot)
	{
		echo '<p class="err">' . lang('Path to user and group files HAS TO BE OUTSIDE of the webservers document-root!!!') . "</strong></p>\n";

		foreach($_POST['newsettings'] as $key => $val)
		{
			$GLOBALS['current_config'][$key] = $val;
		}
	}

	if(isset($GLOBALS['error']) && $GLOBALS['error'] == 'badldapconnection')
	{
		// Please check the number and dial again :)
		$GLOBALS['phpgw_setup']->html->show_alert_msg('Error',
			lang('There was a problem trying to connect to your LDAP server. <br />'
				.'please check your LDAP server configuration') . '.');
	}

	$setup_tpl->pparse('out','T_config_pre_script');
	// Now parse each of the templates we want to show here
	
	$GLOBALS['phpgw']->common = CreateObject('phpgwapi.common');
	$GLOBALS['phpgw']->db     =& $GLOBALS['phpgw_setup']->db;

	$setup_tpl->set_unknowns('keep');
	$setup_tpl->set_file(array('config' => 'config.tpl'));
	$setup_tpl->set_block('config','body','body');

	$vars = $setup_tpl->get_undefined('body');
	$GLOBALS['phpgw_setup']->hook('config','setup');
	
	if ( !is_array($vars) )
	{
		$vars = array();
	}

	foreach ( $vars as $value )
	{
		$valarray = explode('_',$value);

		$var_type = $valarray[0];
		unset($valarray[0]);

		$newval = implode(' ', $valarray);
		unset($valarray);

		switch ($var_type)
		{
			case 'lang':
				$setup_tpl->set_var($value, lang($newval));
				break;
			case 'value':
				$newval = str_replace(' ','_',$newval);
				/* Don't show passwords in the form */
		//		if(ereg('passwd',$value) || ereg('password',$value) || ereg('root_pw',$value))
				if(preg_match('/(passwd|password|root_pw)/i', $value))
				{
					$setup_tpl->set_var($value,'');
				}
				else
				{
					$setup_tpl->set_var($value,@$current_config[$newval]);
				}
				break;
			case 'selected':
				$configs = array();
				$config  = '';
				$newvals = explode(' ',$newval);
				$setting = end($newvals);
				for($i=0;$i<(count($newvals) - 1); ++$i)
				{
					$configs[] = $newvals[$i];
				}
				$config = implode('_',$configs);
				/* echo $config . '=' . $current_config[$config]; */
				if( isset($current_config[$config])
					&& $current_config[$config] == $setting)
				{
					$setup_tpl->set_var($value,' selected');
				}
				else
				{
					$setup_tpl->set_var($value,'');
				}
				break;
			case 'hook':
				$newval = str_replace(' ','_',$newval);
				$setup_tpl->set_var($value, $newval($current_config) );
				break;
			default:
				$setup_tpl->set_var($value,'');
				break;
		}
	}
	$setup_tpl->pfp('out','body');
	$setup_tpl->set_var('more_configs',lang('Please login to phpgroupware and run the admin application for additional site configuration') . '.');

	$setup_tpl->set_var('lang_submit',lang('Save'));
	$setup_tpl->set_var('lang_cancel',lang('Cancel'));
	$setup_tpl->pparse('out','T_config_post_script');

	$GLOBALS['phpgw_setup']->html->show_footer();