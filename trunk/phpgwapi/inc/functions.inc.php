<?php
	/**
	* Has a few functions, but primary role is to load the phpgwapi
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Dave Hall skwashd at phpgroupware.org
	* @copyright Copyright (C) 2000-2006 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.fsf.org/licenses/lgpl.html GNU Lesser General Public License
	* @package phpgwapi
	* @subpackage utilities
	* @version $Id: functions.inc.php,v 1.216 2007/02/14 04:24:56 skwashd Exp $
	*/
	
	
	if (!function_exists('filter_var')) // ext/filter was added in 5.2.0
	{
		die('<p class="msg">'
			. lang('You appear to be using PHP %1, phpGroupWare requires 5.2.0 or later', PHP_VERSION). "\n"
			. '</p></body></html>');
	}

	include_once(PHPGW_API_INC.'/common_functions.inc.php');

	/**
	* Translate a string to a user's prefer language - convience method
	*
	* @param string $key phrase to translate (note: %n are replaces with $mn)
	* @param string $m1 substitution string
	* @param string $m1 substitution string
	* @param string $m2 substitution string
	* @param string $m3 substitution string
	* @param string $m4 substitution string
	* @param string $m5 substitution string
	* @param string $m6 substitution string
	* @param string $m7 substitution string
	* @param string $m8 substitution string
	* @param string $m9 substitution string
	* @param string $m10 substitution string
	* @returns string translated phrase
	*/
	function lang($key,$m1='',$m2='',$m3='',$m4='',$m5='',$m6='',$m7='',$m8='',$m9='',$m10='')
	{
		if(is_array($m1))
		{
			$vars = $m1;
		}
		else
		{
			$vars = array($m1,$m2,$m3,$m4,$m5,$m6,$m7,$m8,$m9,$m10);
		}
		if ( !isset($GLOBALS['phpgw']->translation) || !is_object($GLOBALS['phpgw']->translation) )
		{
			$str = $key;
			for ( $i = 10; $i > 0; --$i )
			{	
				$var = "m{$i}";
				$str = preg_replace("/(%$i)+/", $$var, $str);
			}
			return "$str*#*";
		}
		return $GLOBALS['phpgw']->translation->translate("$key", $vars);
	}

	/**
	* cleans up a backtrace array and converts it to a string
	*
	* @internal this is such an ugly piece of code due to a reference to the error context 
	* being in the backtrace and the error context can not be edited - see php.net/set_error_handler
	* @param array $bt php backtrace
	* @return string the formatted backtrace, empty if the user is not an admin
	*/
	function phpgw_parse_backtrace($bt)
	{
		if ( !is_array($bt) )
		{
			return '';
		}

		$trace = array();
		$trace[0] = array();

		if ( isset($bt[0]['function']) )
		{
			$trace[0]['function'] = $bt[0]['function'];
		}

		if ( isset($bt[0]['args']) && is_array($bt[0]['args']) && count($bt[0]['args']) )
		{
			$trace[0]['args'] = array($bt[0]['args'][0], $bt[0]['args'][1], $bt[0]['args'][2],  $bt[0]['args'][3], '***error_handler_content_data***');
		}

		if ( isset($bt[0]['file']) )
		{
			$trace[0]['file'] = $bt[0]['file'];
		}

		if ( isset($bt[0]['line']) )
		{
			$trace[0]['line'] = $bt[0]['line'];
		}
		unset($bt[0]);

		foreach ( $bt as $num => $entry )
		{
			if ( isset($entry['file']) )
			{
				$trace[$num]['file'] = '/path/to/phpgroupware' . substr($entry['file'], strlen(PHPGW_SERVER_ROOT) );
			}

			if ( isset($entry['line']) )
			{
				$trace[$num]['line'] = $entry['line'];
			}

			if ( isset($entry['line']) )
			{
				$trace[$num]['line'] = $entry['line'];
			}

			if ( isset($entry['type']) && isset($entry['class']) )
			{
				$trace[$num]['function'] = "{$entry['class']}{$entry['type']}{$entry['function']}";
			}
			else
			{
				$trace[$num]['function'] = $entry['function'];
			}

			if ( isset($entry['args']) && is_array($entry['args']) && count($entry['args']) )
			{
				foreach ( $entry['args'] as $anum => $arg )
				{
					if ( is_array($arg) )
					{
						$trace[$num]['args'][$anum] = print_r($arg, true);
						continue;
					}

					// Drop passwords from backtrace
					if ( !is_object($arg) 
						&& ($arg == $GLOBALS['phpgw_info']['server']['header_admin_password']
						|| (isset($GLOBALS['phpgw_info']['server']['db_pass']) && $arg == $GLOBALS['phpgw_info']['server']['db_pass'])
						|| (isset($GLOBALS['phpgw_info']['user']['passwd']) && $arg == $GLOBALS['phpgw_info']['user']['passwd'] ) )
					)
					{
						$trace[$num]['args'][$anum] = '***PASSWORD***';
					}
					else
					{
						$trace[$num]['args'][$anum] = $arg;
					}
				}
			}
			else
			{
				$trace[$num]['args'] = 'NONE';
			}
		}
		return print_r($trace, true);
	}

	/**
	* phpGroupWare Information level "error"
	*/
	define('PHPGW_E_INFO', -512);

	/**
	* phpGroupWare debug level "error"
	*/
	define('PHPGW_E_DEBUG', -1024);

	/**
	* phpGroupWare generic error handler
	*
	* @link http://php.net/set_error_handler
	* 
	*/
	function phpgw_handle_error($error_level, $error_msg, $error_file, $error_line, $error_context = array())
	{
		if ( error_reporting() == 0 ) // 0 == @function() so we ignore it, as the dev requested 
		{
			return true;
		}

		if ( !isset($GLOBALS['phpgw']->log)
			|| !is_object($GLOBALS['phpgw']->log) )
		{
			$GLOBALS['phpgw']->log = createObject('phpgwapi.log');
		}

		$log =& $GLOBALS['phpgw']->log;

		$error_file = str_replace(PHPGW_SERVER_ROOT, '/path/to/phpgroupware', $error_file);

		$bt = debug_backtrace();

		$log_args = array
		(
			'file'	=> $error_file,
			'line'	=> $error_line,
			'text'	=> "$error_msg\n" . phpgw_parse_backtrace($bt)
		);

		switch ( $error_level )
		{
			case E_USER_ERROR:
			case E_ERROR:
				$log_args['severity'] = 'F'; //all "ERRORS" should be fatal
				$log->fatal($log_args);
				break;
			case E_WARNING:
			case E_USER_WARNING:
				$log_args['severity'] = 'W';
				$log->warn($log_args);
				echo "\n<br>" . lang('ERROR Warning: %1 in %2 at line %3', $error_msg, $error_file, $error_line) . "<br>\n";
				break;
			case PHPGW_E_INFO:
				$log_args['severity'] = 'I';
				$log->info($log_args);
				break;
			case PHPGW_E_DEBUG:
				$log_args['severity'] = 'D';
				$log->info($log_args);
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$log_args['severity'] = 'N';
				$log->notice($log_args);
				if(isset($GLOBALS['phpgw_info']['server']['log_levels']['global_level']) && $GLOBALS['phpgw_info']['server']['log_levels']['global_level'] == 'N')
				{
					echo "\n<br>" . lang('ERROR Notice: %1 in %2 at line %3', $error_msg, $error_file, $error_line) . "<br>\n"; //this will be commented in the final version
				}
			//No default, we just ignore it, for now
		}
	}
	set_error_handler('phpgw_handle_error');

	function clean_vars($vars, $safe_redirect = True)
	{
		if ( !is_array($vars) )
		{
			return $GLOBALS['data_cleaner']->clean($vars, $safe_redirect);
		}
		
		foreach ( $vars as $key => $val )
		{
			$vars[$key] = clean_vars($val, $safe_redirect);
		}
		return $vars;
	}

	/* Make sure the header.inc.php is current. */
	if ($GLOBALS['phpgw_info']['server']['versions']['header'] < $GLOBALS['phpgw_info']['server']['versions']['current_header'])
	{
		$setup_dir = ereg_replace($_SERVER['PHP_SELF'],'index.php','setup/');
		echo '<center><b>You need to port your settings to the new header.inc.php version. <a href="' . $setup_dir . '">Run setup now!</a></b></center>';
		exit;
	}

	/* Make sure the developer is following the rules. */
	if (!isset($GLOBALS['phpgw_info']['flags']['currentapp']))
	{
		/* This object does not exist yet. */
	/*	$GLOBALS['phpgw']->log->write(array('text'=>'W-MissingFlags, currentapp flag not set'));*/

		echo '<b>!!! YOU DO NOT HAVE YOUR $GLOBALS[\'phpgw_info\'][\'flags\'][\'currentapp\'] SET !!!';
		echo '<br>!!! PLEASE CORRECT THIS SITUATION !!!</b>';
	}

	magic_quotes_runtime(false);
// Can't use this yet - errorlog hasn't been created.
//	print_debug('sane environment','messageonly','api');

	/****************************************************************************\
	* Multi-Domain support                                                       *
	\****************************************************************************/
	
	/* make them fix their header */
	if (!isset($GLOBALS['phpgw_domain']))
	{
		echo '<center><b>The administrator must upgrade the header.inc.php file before you can continue.</b></center>';
		exit;
	}
	reset($GLOBALS['phpgw_domain']);
	list($GLOBALS['phpgw_info']['server']['default_domain']) = each($GLOBALS['phpgw_domain']);

	if (isset($_POST['login']))	// on login
	{
		$GLOBALS['login'] = $_POST['login'];
		if (strstr($GLOBALS['login'],'@') === False)
		{
			$GLOBALS['login'] .= '@' . phpgw::get_var('logindomain', 'string', 'POST', $GLOBALS['phpgw_info']['server']['default_domain']);
		}
		list(,$GLOBALS['phpgw_info']['user']['domain']) = explode('@',$GLOBALS['login']);
	}
	else	// on "normal" pageview
	{
		$GLOBALS['phpgw_info']['user']['domain'] = phpgw::get_var('domain', 'string', 'REQUEST', false);
	}

	if (isset($GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]))
	{
		$GLOBALS['phpgw_info']['server']['db_host'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_host'];
		$GLOBALS['phpgw_info']['server']['db_name'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_name'];
		$GLOBALS['phpgw_info']['server']['db_user'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_user'];
		$GLOBALS['phpgw_info']['server']['db_pass'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_pass'];
		$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['user']['domain']]['db_type'];
	}
	else
	{
		$GLOBALS['phpgw_info']['server']['db_host'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_host'];
		$GLOBALS['phpgw_info']['server']['db_name'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_name'];
		$GLOBALS['phpgw_info']['server']['db_user'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_user'];
		$GLOBALS['phpgw_info']['server']['db_pass'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_pass'];
		$GLOBALS['phpgw_info']['server']['db_type'] = $GLOBALS['phpgw_domain'][$GLOBALS['phpgw_info']['server']['default_domain']]['db_type'];
	}

	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'login' && ! $GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
	{
		unset ($GLOBALS['phpgw_domain']); // we kill this for security reasons
	}

// Can't use this yet - errorlog hasn't been created.
//	print_debug('domain',@$GLOBALS['phpgw_info']['user']['domain'],'api');

	 /****************************************************************************\
	 * These lines load up the API, fill up the $phpgw_info array, etc            *
	 \****************************************************************************/
	 /* Load main class */
	$GLOBALS['phpgw'] = createObject('phpgwapi.phpgw');
	 /************************************************************************\
	 * Load up the main instance of the db class.                             *
	 \************************************************************************/
	$GLOBALS['phpgw']->db                = createObject('phpgwapi.db');
	$GLOBALS['phpgw']->db->Debug         = $GLOBALS['phpgw']->debug ? 1 : 0;
	$GLOBALS['phpgw']->db->Halt_On_Error = 'no';
	$GLOBALS['phpgw']->adodb             =& $GLOBALS['phpgw']->db->adodb; //Reference
	
	@$GLOBALS['phpgw']->adodb->connect($GLOBALS['phpgw_info']['server']['db_host'],
									   $GLOBALS['phpgw_info']['server']['db_user'],
									   $GLOBALS['phpgw_info']['server']['db_pass'],
									   $GLOBALS['phpgw_info']['server']['db_name']
									  );
	if(!$GLOBALS['phpgw']->adodb->ErrorNo())
	{
		$GLOBALS['phpgw']->adodb->query('select count(config_name) from phpgw_config');
		if($GLOBALS['phpgw']->adodb->ErrorNo())
		{
			$setup_dir = ereg_replace($_SERVER['PHP_SELF'],'index.php','setup/');
			echo '<center><b>Fatal Error:</b> It appears that you have not created the database tables for '
			.'phpGroupWare.  Click <a href="' . $setup_dir . '">here</a> to run setup.</center>';
			exit;
		}
	}
	else
	{
		$setup_dir = ereg_replace($_SERVER['PHP_SELF'],'index.php','setup/');
		echo '<center><b>Fatal Error:</b> Unable to connect to database server '
		.'Click <a href="' . $setup_dir . '">here</a> to run setup.</center>';
		exit;
	}
	$GLOBALS['phpgw']->db->Halt_On_Error = 'yes';

	 /* Fill phpgw_info["server"] array */
	 // An Attempt to speed things up using cache premise
	$GLOBALS['phpgw']->db->query("select config_value from phpgw_config WHERE config_app='phpgwapi' and config_name='cache_phpgw_info'",__LINE__,__FILE__);
	if ($GLOBALS['phpgw']->db->num_rows())
	{
		$GLOBALS['phpgw']->db->next_record();
		$GLOBALS['phpgw_info']['server']['cache_phpgw_info'] = stripslashes($GLOBALS['phpgw']->db->f('config_value'));
	}

	$cache_query = "SELECT content from phpgw_app_sessions WHERE"
		." sessionid = '0' AND loginid = '0' and app = 'phpgwapi' AND location = 'config'";

	$GLOBALS['phpgw']->db->query($cache_query,__LINE__,__FILE__);
	$server_info_cache = $GLOBALS['phpgw']->db->num_rows();

	if(isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info'])
			&& $GLOBALS['phpgw_info']['server']['cache_phpgw_info']
			&& $server_info_cache > 0)
	{
		$GLOBALS['phpgw']->db->next_record();
		$GLOBALS['phpgw_info']['server'] = unserialize(stripslashes($GLOBALS['phpgw']->db->f('content')));
	}
	else
	{
		$c = createObject('phpgwapi.config','phpgwapi');
		$c->read_repository();
		foreach ($c->config_data as $k => $v)
		{
			$GLOBALS['phpgw_info']['server'][$k] = $v;
		}


		if(isset($GLOBALS['phpgw_info']['server']['cache_phpgw_info'])
			&& $GLOBALS['phpgw_info']['server']['cache_phpgw_info'])
		{
			$cache_query = 'INSERT INTO phpgw_app_sessions(sessionid,loginid,app,location,content) VALUES('
				. "'0','0','phpgwapi','config','".$GLOBALS['phpgw']->db->db_addslashes(serialize($GLOBALS['phpgw_info']['server']))."')";
			$GLOBALS['phpgw']->db->query($cache_query,__LINE__,__FILE__);
		}
	}
	unset($cache_query);
	unset($server_info_cache);

	// In the case we use a fall back (mode Half remote_user)
	if(isset($GLOBALS['phpgw_remote_user']) && !empty($GLOBALS['phpgw_remote_user']))
	{
		$GLOBALS['phpgw_info']['server']['auth_type'] = $GLOBALS['phpgw_remote_user'];
	}
	
	// Remove this and I will make sure that you lose important parts of your anatomy - skwashd
	$GLOBALS['RAW_REQUEST'] = $_REQUEST; // if you really need the raw value
	$to_cleans = array('_GET', '_POST', '_COOKIE', '_REQUEST');
	$GLOBALS['data_cleaner'] = createObject('phpgwapi.data_cleaner'); // We create it for the whole call ...
	foreach ( $to_cleans as $to_clean )
	{
		if ( isset($GLOBALS[$to_clean]) && is_array($GLOBALS[$to_clean]) && count($GLOBALS[$to_clean]) )
		{
			$GLOBALS[$to_clean] = clean_vars($GLOBALS[$to_clean]);
		}
	}

	if(isset($GLOBALS['phpgw_info']['server']['enforce_ssl']) && !(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) )
	{
		Header('Location: https://' . $GLOBALS['phpgw_info']['server']['hostname'] . $GLOBALS['phpgw_info']['server']['webserver_url'] . $_SERVER['REQUEST_URI']);
		exit;
	}

	/************************************************************************\
	* Required classes                                                       *
	\************************************************************************/
	$GLOBALS['phpgw']->log			= createObject('phpgwapi.log');
	include_once(PHPGW_API_INC . '/log_functions.inc.php');
	$GLOBALS['phpgw']->translation	= createObject('phpgwapi.translation');
	$GLOBALS['phpgw']->common		= createObject('phpgwapi.common');
	$GLOBALS['phpgw']->hooks		= createObject('phpgwapi.hooks');
	$GLOBALS['phpgw']->auth			= createObject('phpgwapi.auth');
	$GLOBALS['phpgw']->accounts		= createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl			= createObject('phpgwapi.acl');
	$GLOBALS['phpgw']->session		= createObject('phpgwapi.sessions');
	$GLOBALS['phpgw']->preferences	= createObject('phpgwapi.preferences');
	$GLOBALS['phpgw']->applications	= createObject('phpgwapi.applications');
	print_debug('main class loaded', 'messageonly','api');
	// This include was here before for the old error class.  I've left it in for the
	// new log_message class with replaced error.  I'm not sure if it is needed, though. -doug
	include_once(PHPGW_INCLUDE_ROOT.'/phpgwapi/inc/class.log_message.inc.php');

	/****************************************************************************\
	* This is a global constant that should be used                              *
	* instead of / or \ in file paths                                            *
	\****************************************************************************/
	define('SEP', filesystem_separator());

	/*****************************************************************************\
	* ACL defines - moved here to work for xml-rpc/soap, also                     *
	\*****************************************************************************/
	define('PHPGW_ACL_READ',1);
	define('PHPGW_ACL_ADD',2);
	define('PHPGW_ACL_EDIT',4);
	define('PHPGW_ACL_DELETE',8);
	define('PHPGW_ACL_PRIVATE',16);
	define('PHPGW_ACL_GROUP_MANAGERS',32);
	define('PHPGW_ACL_CUSTOM_1',64);
	define('PHPGW_ACL_CUSTOM_2',128);
	define('PHPGW_ACL_CUSTOM_3',256);

	/****************************************************************************\
	* Forcing the footer to run when the rest of the script is done.             *
	\****************************************************************************/
	register_shutdown_function(array($GLOBALS['phpgw']->common, 'phpgw_final'));

	/****************************************************************************\
	* Stuff to use if logging in or logging out                                  *
	\****************************************************************************/
	if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'login' || $GLOBALS['phpgw_info']['flags']['currentapp'] == 'logout')
	{
		if ($GLOBALS['phpgw_info']['flags']['currentapp'] == 'login')
		{
			if (@$_POST['login'] != '')
			{
				list($login) = explode("@",$_POST['login']);
				print_debug('LID',$login,'app');
				$login_id = $GLOBALS['phpgw']->accounts->name2id($login);
				print_debug('User ID',$login_id,'app');
				$GLOBALS['phpgw']->accounts->accounts($login_id);
				$GLOBALS['phpgw']->preferences->preferences($login_id);
				$GLOBALS['phpgw']->datetime = createObject('phpgwapi.datetimefunctions');
			}
		}
	/**************************************************************************\
	* Everything from this point on will ONLY happen if                        *
	* the currentapp is not login or logout                                    *
	\**************************************************************************/
	}
	else
	{
		if (! $GLOBALS['phpgw']->session->verify())
		{
			if( isset($_GET['menuaction']) && $_GET['menuaction'])
			{
				unset($_GET['click_history']);
				unset($_GET['sessionid']);
				unset($_GET['kp3']);
				$GLOBALS['phpgw']->session->phpgw_setcookie('redirect',serialize($_GET),$cookietime=0);
			}
			$cd_array = array();
			if ( isset($GLOBALS['phpgw']->session->cd_reason) && $GLOBALS['phpgw']->session->cd_reason )
			{
				$cd_array['cd'] = $GLOBALS['phpgw']->session->cd_reason;
			}
			$GLOBALS['phpgw']->redirect_link('/login.php', $cd_array );
		}

		if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] !='en')
		{
			$GLOBALS['phpgw']->translation->userlang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
			$GLOBALS['phpgw']->translation->translation($reset = True);
		}

		$redirect = unserialize(htmlspecialchars_decode(stripslashes(phpgw::get_var('redirect','string', 'COOKIE'))));
		if ( is_array($redirect) && count($redirect) )
		{
			foreach($redirect as $key => $value)
			{
				$redirect_data[$key] = $value;
			}
			
			$sessid = phpgw::get_var('sessionid', 'string', 'GET');
			if ( $sessid )
			{
				$redirect_data['sessionid'] = $sessid;
				$redirect_data['kp3'] = phpgw::get_var('kp3', 'string', 'GET');
			}
			
			$GLOBALS['phpgw']->session->phpgw_setcookie('redirect', false, 0);
			$GLOBALS['phpgw']->redirect_link('/index.php', $redirect_data);
			unset($redirect);
			unset($redirect_data);
			unset($sessid);
		}

		$GLOBALS['phpgw']->datetime = createObject('phpgwapi.datetimefunctions');

		/* A few hacker resistant constants that will be used throught the program */
		define('PHPGW_TEMPLATE_DIR', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'phpgwapi'));
		define('PHPGW_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_path', 'phpgwapi'));
		define('PHPGW_IMAGES_FILEDIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir', 'phpgwapi'));
		define('PHPGW_APP_ROOT', ExecMethod('phpgwapi.phpgw.common.get_app_dir'));
		define('PHPGW_APP_INC', ExecMethod('phpgwapi.phpgw.common.get_inc_dir'));
		define('PHPGW_APP_TPL', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir'));
		define('PHPGW_IMAGES', ExecMethod('phpgwapi.phpgw.common.get_image_path'));
		define('PHPGW_APP_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir'));

	/************************************************************************\
	* Load the menuaction                                                    *
	\************************************************************************/
		$GLOBALS['phpgw_info']['menuaction'] = phpgw::get_var('menuaction');
		if(!$GLOBALS['phpgw_info']['menuaction'])
		{
			unset($GLOBALS['phpgw_info']['menuaction']);
		}

		/********* This sets the user variables *********/
		$GLOBALS['phpgw_info']['user']['private_dir'] = $GLOBALS['phpgw_info']['server']['files_dir']
			. '/users/'.$GLOBALS['phpgw_info']['user']['userid'];

		/* This will make sure that a user has the basic default prefs. If not it will add them */
		$GLOBALS['phpgw']->preferences->verify_basic_settings();

		/********* Optional classes, which can be disabled for performance increases *********/
		while ($phpgw_class_name = each($GLOBALS['phpgw_info']['flags']))
		{
			if (ereg('enable_', $phpgw_class_name[0]))
			{
				$enable_class = str_replace('enable_', '', $phpgw_class_name[0]);
				$enable_class = str_replace('_class', '', $enable_class);
				$GLOBALS['phpgw']->$enable_class = createObject("phpgwapi.{$enable_class}");
			}
		}
		unset($enable_class);
		reset($GLOBALS['phpgw_info']['flags']);

		/*************************************************************************\
		* These lines load up the templates class                                 *
		\*************************************************************************/
		if ( !isset($GLOBALS['phpgw_info']['flags']['disable_Template_class'])
			|| !$GLOBALS['phpgw_info']['flags']['disable_Template_class'] )
		{
			$GLOBALS['phpgw']->template = createObject('phpgwapi.Template',PHPGW_APP_TPL);
			$GLOBALS['phpgw']->xslttpl = createObject('phpgwapi.xslttemplates',PHPGW_APP_TPL);
		}

		/*************************************************************************\
		* If they are using frames, we need to set some variables                 *
		\*************************************************************************/
		if ( ( (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['useframes']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['useframes']) 
			&& ( isset($GLOBALS['phpgw_info']['server']['useframes']) && $GLOBALS['phpgw_info']['server']['useframes'] == 'allowed') ) 
			|| ( isset($GLOBALS['phpgw_info']['server']['useframes']) && $GLOBALS['phpgw_info']['server']['useframes'] == 'always') )
		{
			$GLOBALS['phpgw_info']['flags']['navbar_target'] = 'phpgw_body';
		}

		/*************************************************************************\
		* Verify that the users session is still active otherwise kick them out   *
		\*************************************************************************/
		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home' && $GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
		{
			if (!$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $GLOBALS['phpgw_info']['flags']['currentapp']))
			{
				$GLOBALS['phpgw']->common->phpgw_header( true ); //!(isset($GLOBALS['phpgw_info']['flags']['nonavbar']) && $GLOBALS['phpgw_info']['flags']['nonavbar']) );
				$GLOBALS['phpgw']->log->write(array('text'=>'W-Permissions, Attempted to access %1','p1'=>$GLOBALS['phpgw_info']['flags']['currentapp']));

				echo '<p><center><b>'.lang('Access not permitted').'</b></center>';
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}
		}

		if(!is_object($GLOBALS['phpgw']->datetime))
		{
			$GLOBALS['phpgw']->datetime = createObject('phpgwapi.datetimefunctions');
		}
		$GLOBALS['phpgw']->applications->read_installed_apps();	// to get translated app-titles
		
		/*************************************************************************\
		* Load the header unless the developer turns it off                       *
		\*************************************************************************/
		if ( !isset($GLOBALS['phpgw_info']['flags']['noheader']) || !$GLOBALS['phpgw_info']['flags']['noheader'] )
		{
			$inc_navbar = !isset($GLOBALS['phpgw_info']['flags']['nonavbar']) || !$GLOBALS['phpgw_info']['flags']['nonavbar'];
			$GLOBALS['phpgw']->common->phpgw_header($inc_navbar);
			unset($inc_navbar);
		}

		/*************************************************************************\
		* Load the app include files if the exists                                *
		\*************************************************************************/
		/* Then the include file */
		if (! preg_match ("/phpgwapi/i", PHPGW_APP_INC) && file_exists(PHPGW_APP_INC . '/functions.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
		{
			include_once(PHPGW_APP_INC . '/functions.inc.php');
		}
		if (!@$GLOBALS['phpgw_info']['flags']['noheader'] && 
			!@$GLOBALS['phpgw_info']['flags']['noappheader'] &&
			file_exists(PHPGW_APP_INC . '/header.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
		{
			include_once(PHPGW_APP_INC . '/header.inc.php');
		}
	}

	//error_reporting(E_ERROR | E_WARNING | E_PARSE);
