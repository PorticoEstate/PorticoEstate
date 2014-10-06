<?php
  /**************************************************************************\
  * phpGroupWare - Setup                                                     *
  * http://www.phpgroupware.org                                              *
  * --------------------------------------------                             *
  * This file written by Joseph Engo<jengo@phpgroupware.org>                 *
  *  and Dan Kuykendall<seek3r@phpgroupware.org>                             *
  *  and Mark Peters<skeeter@phpgroupware.org>                               *
  *  and Miles Lott<milosch@phpgroupware.org>                                *
  *  and Dave Hall skwashd at phpgroupware.org                               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/

  /* $Id$ */

	/**
	* phpGroupWare Information level "error"
	*/
	define('PHPGW_E_INFO', -512);

	/**
	* phpGroupWare debug level "error"
	*/
	define('PHPGW_E_DEBUG', -1024);

	if ( file_exists('../header.inc.php') )
	{
		require_once('../header.inc.php');
	}

	if (version_compare(phpversion(), '5.2.0', '<'))
	{
		die('<h1>You appear to be using PHP ' . PHP_VERSION . " phpGroupWare requires 5.2.0 or later <br>\n"
			. 'Please contact your System Administrator</h1>');
	}

	if ( !function_exists('json_encode') ) // Some distributions have removed the standard JSON extension as of PHP 5.5rc2 due to a license conflict
	{
		die('<h1>You have to install php5-json</h1>');
	}

	/*  If we included the header.inc.php, but it is somehow broken, cover ourselves... */
	if ( !defined('PHPGW_SERVER_ROOT') )
	{
		define('PHPGW_SERVER_ROOT', realpath('..') );
	}

	if ( !defined('PHPGW_INCLUDE_ROOT') )
	{
		define('PHPGW_INCLUDE_ROOT', PHPGW_SERVER_ROOT);
	}

	if ( is_dir(PHPGW_INCLUDE_ROOT . '/phpgwapi') && is_dir(PHPGW_INCLUDE_ROOT . '/phpgwapi/inc')
		&& is_file(PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/common_functions.inc.php') )
	{
		require_once(PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/common_functions.inc.php');
		$GLOBALS['phpgw'] = createObject('phpgwapi.phpgw');
		require_once(PHPGW_INCLUDE_ROOT . '/phpgwapi/inc/log_functions.inc.php');
	}
	else
	{
		die('Your phpGroupWare install is incomplete, please try to '
			. '<a href="http://download.phpgroupware.org/now"'
			. ' target="_blank">download phpGroupWare</a> and try again');
	}

	// Make sure we have an install id - I don't like this, but it works :( skwashd mar2008
	if ( !isset($GLOBALS['phpgw_info']['server']['install_id']) )
	{
		$GLOBALS['phpgw_info']['server']['install_id'] = sha1($_SERVER['HTTP_HOST']);
	}

	/**
	 * Translate a phrase
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

		if ( !isset($GLOBALS['phpgw_setup']->translation) || !is_object($GLOBALS['phpgw_setup']->translation) )
		{
			return sprintf( preg_replace('/(%\d)+/', '%s', $key),  $m1, $m2, $m3, $m4, $m5, $m6, $m7, $m8, $m9, $m10) . ' *#*';
		}
		return $GLOBALS['phpgw_setup']->translation->translate("$key", $vars);
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
					if ( ( isset($GLOBALS['phpgw_info']['server']['header_admin_password']) && $arg == $GLOBALS['phpgw_info']['server']['header_admin_password'] )
						|| ( isset( $GLOBALS['phpgw_info']['server']['db_pass']) && $arg == $GLOBALS['phpgw_info']['server']['db_pass'] )
						|| ( isset($GLOBALS['phpgw_info']['user']['passwd']) && $arg == $GLOBALS['phpgw_info']['user']['passwd'] )
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

		$error_file = '/path/to/phpgroupware' . substr($error_file, strlen(PHPGW_SERVER_ROOT) );

		$bt = debug_backtrace();

		$log_args = array
		(
			'file'	=> $error_file,
			'line'	=> $error_line,
			'text'	=> "$error_msg\n" . phpgw_parse_backtrace($bt)
		);

	//	echo "\n<br>" . lang('ERROR : %1 in %2 at line %3', $error_msg, $error_file, $error_line) . "<br>\n";
		switch ( $error_level )
		{
			case E_USER_ERROR:
			case E_ERROR:
				$log_args['severity'] = 'F'; //all "ERRORS" should be fatal
				$log->fatal($log_args);
				echo "\n<br>" . lang('ERROR Fatal: %1 in %2 at line %3', $error_msg, $error_file, $error_line) . "<br>\n";
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
			case E_STRICT:
				$log_args['severity'] = 'N';
				$log->notice($log_args);
			//	echo "\n<br>" . lang('ERROR Notice: %1 in %2 at line %3', $error_msg, $error_file, $error_line) . "<br>\n"; //this will be commented in the final version
			//No default, we just ignore it, for now
		}
	}
	set_error_handler('phpgw_handle_error');

	/**
	* Get a list of supported languages
	*
	* @returns array supported language ['lang' => iso631_code, 'descr' => language_name, 'available' => bool_is_installed]
	*/
	function get_langs()
	{
		$f = fopen('./lang/languages','rb');
		while($line = fgets($f,200))
		{
			list($x,$y) = explode("\t",$line);
			$languages[$x]['lang']  = trim($x);
			$languages[$x]['descr'] = trim($y);
			$languages[$x]['available'] = False;
		}
		fclose($f);

		$d = dir('./lang');
		while ( $entry = $d->read() )
		{
			if ( strpos($entry, 'phpgw_') === 0 )
			{
				$z = substr($entry,6,2);
				$languages[$z]['available'] = True;
			}
		}
		$d->close();

//		print_r($languages);
		return $languages;
	}

	/**
	* Generate a select box of available languages
	*
	* @param bool $onChange javascript to trigger when selection changes (optional)
	* @returns string HTML snippet for select box
	*/
	function lang_select($onChange = '')
	{
		$ConfigLang = phpgw::get_var('ConfigLang', 'string', 'POST');
		$select = '<select name="ConfigLang"' . ($onChange ? ' onChange="this.form.submit();"' : '') . '>' . "\n";
		$languages = get_langs();
		while(list($null,$data) = each($languages))
		{
			if($data['available'] && !empty($data['lang']))
			{
				$selected = '';
				$short = substr($data['lang'],0,2);
				if ($short == $ConfigLang || empty($ConfigLang) && $short == substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2))
				{
					$selected = ' selected';
				}
				$select .= '<option value="' . $data['lang'] . '"' . $selected . '>' . $data['descr'] . '</option>' . "\n";
			}
		}
		$select .= '</select>' . "\n";

		return $select;
	}

	if(file_exists(PHPGW_SERVER_ROOT . '/phpgwapi/setup/setup.inc.php'))
	{
		require(PHPGW_SERVER_ROOT.'/phpgwapi/setup/setup.inc.php'); /* To set the current core version */
		/* This will change to just use setup_info */
		$GLOBALS['phpgw_info']['server']['versions']['current_header'] = $setup_info['phpgwapi']['versions']['current_header'];
	}
	else
	{
		$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] = 'Undetected';
	}

	$GLOBALS['phpgw_info']['server']['app_images'] = 'templates/base/images';

	if(isset($_POST['setting']['enable_mcrypt']) && $_POST['setting']['enable_mcrypt'] == 'True')
	{
		$GLOBALS['phpgw_info']['server']['mcrypt_enabled'] = true;
		$_iv  = $_POST['setting']['mcrypt_iv'];
		$_key = $_POST['setting']['setup_mcrypt_key'];
	}
	else
	{
		$_iv  = $GLOBALS['phpgw_info']['server']['mcrypt_iv'];
		$_key = $GLOBALS['phpgw_info']['server']['setup_mcrypt_key'];	
	}

	if($_key) // not during upgrade from 0.9.16
	{
		$GLOBALS['phpgw']->crypto->init(array($_key, $_iv));
	}

	$GLOBALS['phpgw_setup'] = CreateObject('phpgwapi.setup', True, True);
