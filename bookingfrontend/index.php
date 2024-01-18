<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Jonas Borgström jonas.borgstrom@redpill.se
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2009 Free Software Foundation, Inc. http://www.fsf.org/
	 * This file is part of phpGroupWare.
	 *
	 * phpGroupWare is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or
	 * (at your option) any later version.
	 *
	 * phpGroupWare is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with phpGroupWare; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	 * @internal Development of this application was funded by http://www.bergen.kommune.no/bbb_/ekstern/
	 * @package property
	 * @version $Id$
	 */
	/**
	 * Start page
	 *
	 * This script will check if there is defined a startpage in the users
	 * preferences - and then forward the user to this page
	 */
	$GLOBALS['phpgw_info']['flags']					 = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'login', // To stop functions.inc.php from validating the session
	);
	$GLOBALS['phpgw_info']['flags']['session_name']	 = 'bookingfrontendsession';
	$GLOBALS['phpgw_remote_user_fallback']			 = 'sql';
	include_once('../header.inc.php');

	// Make sure we're always logged in
	if (!phpgw::get_var(session_name(), 'string', 'COOKIE') || !$GLOBALS['phpgw']->session->verify())
	{
		$config = createobject('phpgwapi.config', 'bookingfrontend')->read();

		$login		 = $config['anonymous_user'];
		$logindomain = phpgw::get_var('domain', 'string', 'GET');
		if ($logindomain && strstr($login, '#') === false)
		{
			$login .= "#{$logindomain}";
		}

		$passwd				 = $config['anonymous_passwd'];
		$_POST['submitit']	 = "";

		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
		if (!$GLOBALS['sessionid'])
		{
			$lang_denied = lang('Anonymous access not correctly configured');
			if ($GLOBALS['phpgw']->session->reason)
			{
				$lang_denied = $GLOBALS['phpgw']->session->reason;
			}
			echo <<<HTML
<!DOCTYPE html>
<html>
<head>
	<title>Nede for vedlikehold</title>
	<style>
		body {
			background-color: #f2f2f2;
			font-family: Arial, sans-serif;
		}
		h1 {
			font-size: 48px;
			color: #333333;
			text-align: center;
			margin-top: 100px;
		}
		p {
			font-size: 24px;
			color: #666666;
			text-align: center;
			margin-top: 50px;
		}
		.footer {
			font-size: 14px;
			color: #666666;
			text-align: center;
			position: fixed;
			bottom: 0;
			width: 100%;
			margin-bottom: 10px;
		}
	</style>
</head>
<body>
	<h1>Nede for vedlikehold</h1>
	<p>Vi beklager ulempen, men denne nettsiden er for tiden under vedlikehold. Kom tilbake senere.</p>
	<div class="footer">$lang_denied</div>
</body>
</html>

HTML;

//			echo <<<HTML
//				<div class="error">$lang_denied</div>
//HTML;
			/**
			 * Used for footer on exit
			 */
			define('PHPGW_APP_INC', ExecMethod('phpgwapi.phpgw.common.get_inc_dir'));
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}

	$redirect_input = phpgw::get_var('redirect', 'raw', 'COOKIE');
	$redirect = $redirect_input ? json_decode(phpgw::get_var('redirect', 'raw', 'COOKIE'), true) : null;

	if (is_array($redirect) && count($redirect))
	{
		$redirect_data = array();
		foreach ($redirect as $key => $value)
		{
			$redirect_data[$key] = phpgw::clean_value($value);
		}

		$redirect_data['second_redirect'] = true;

		$sessid = phpgw::get_var('sessionid', 'string', 'GET');
		if ($sessid)
		{
			$redirect_data['sessionid']	 = $sessid;
			$redirect_data['kp3']		 = phpgw::get_var('kp3', 'string', 'GET');
		}

		$GLOBALS['phpgw']->session->phpgw_setcookie('redirect', false, 0);
		$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $redirect_data);
		unset($redirect);
		unset($redirect_data);
		unset($sessid);
	}

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'bookingfrontend';

/////////////////////////////////////////////////////////////////////////////
// BEGIN Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////

	$selected_lang = phpgw::get_var('selected_lang', 'string', 'COOKIE');

	if (phpgw::get_var('lang', 'bool', 'GET'))
	{
		$selected_lang = phpgw::get_var('lang', 'string', 'GET');
		$GLOBALS['phpgw']->session->phpgw_setcookie('selected_lang', $selected_lang, (time() + (60 * 60 * 24 * 14)));
	}

	$userlang = $selected_lang ? $selected_lang : $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];

	$GLOBALS['phpgw']->translation->set_userlang($userlang, true);

	$template_set = phpgw::get_var('template_set', 'string', 'COOKIE');

    /**
     *  converted menuactions
     */
    $availableMenuActions = (object) [
        'bookingfrontend.uiapplication.add' => true,
        'bookingfrontend.uiresource.show' => true,
        'bookingfrontend.uibuilding.show' => true,
    ];

	/**
	 * we want the "bookingfrontend" for now
	 */
	switch ($template_set)
	{
		case 'bookingfrontend_2':
			if (str_ends_with(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), 'bookingfrontend/') &&
				(!$_GET['menuaction'] || $availableMenuActions->{$_GET['menuaction']})
			) {
				$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] = $template_set;
				break;
			}
		case 'bookingfrontend':
			$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']	 = 'bookingfrontend';
			break;
		default:
			$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set']	 = 'bookingfrontend';
			break;
	}

	/* A few hacker resistant constants that will be used throught the program */
	define('PHPGW_TEMPLATE_DIR', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir', 'phpgwapi'));
	define('PHPGW_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_path', 'phpgwapi'));
	define('PHPGW_IMAGES_FILEDIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir', 'phpgwapi'));
	define('PHPGW_APP_ROOT', ExecMethod('phpgwapi.phpgw.common.get_app_dir'));
	define('PHPGW_APP_INC', ExecMethod('phpgwapi.phpgw.common.get_inc_dir'));
	define('PHPGW_APP_TPL', ExecMethod('phpgwapi.phpgw.common.get_tpl_dir'));
	define('PHPGW_IMAGES', ExecMethod('phpgwapi.phpgw.common.get_image_path'));
	define('PHPGW_APP_IMAGES_DIR', ExecMethod('phpgwapi.phpgw.common.get_image_dir'));

	/*	 * **********************************************************************\
	 * Load the menuaction                                                    *
	  \*********************************************************************** */
	$GLOBALS['phpgw_info']['menuaction'] = phpgw::get_var('menuaction');
	if (!$GLOBALS['phpgw_info']['menuaction'])
	{
		unset($GLOBALS['phpgw_info']['menuaction']);
	}

	/*	 * ******* This sets the user variables ******** */
	$GLOBALS['phpgw_info']['user']['private_dir'] = $GLOBALS['phpgw_info']['server']['files_dir']
		. '/users/' . $GLOBALS['phpgw_info']['user']['userid'];

	/* This will make sure that a user has the basic default prefs. If not it will add them */
	$GLOBALS['phpgw']->preferences->verify_basic_settings();

	/*	 * ******* Optional classes, which can be disabled for performance increases ******** */
	if (is_array($GLOBALS['phpgw_info']['flags']))
	{
		foreach ($GLOBALS['phpgw_info']['flags'] as $phpgw_class_name => $dummy)
		{
			if (preg_match('/enable_/', $phpgw_class_name))
			{
				$enable_class					 = str_replace('enable_', '', $phpgw_class_name);
				$enable_class					 = str_replace('_class', '', $enable_class);
				$GLOBALS['phpgw']->$enable_class = createObject("phpgwapi.{$enable_class}");
			}
		}
		unset($enable_class);
		reset($GLOBALS['phpgw_info']['flags']);
	}

	/*	 * ***********************************************************************\
	 * These lines load up the templates class                                 *
	  \************************************************************************ */
	if (!isset($GLOBALS['phpgw_info']['flags']['disable_Template_class']) || !$GLOBALS['phpgw_info']['flags']['disable_Template_class'])
	{
		$GLOBALS['phpgw']->template	 = createObject('phpgwapi.template', PHPGW_APP_TPL);
		$GLOBALS['phpgw']->xslttpl	 = createObject('phpgwapi.xslttemplates', PHPGW_APP_TPL);
	}

	/*	 * ***********************************************************************\
	 * Verify that the users session is still active otherwise kick them out   *
	  \************************************************************************ */
	$invalid_data = false;

	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home' && $GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
	{
		if (!$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $GLOBALS['phpgw_info']['flags']['currentapp']))
		{
			$invalid_data = true;
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$GLOBALS['phpgw']->log->write(array('text'	 => 'W-Permissions, Attempted to access %1 from %2',
				'p1'	 => $GLOBALS['phpgw_info']['flags']['currentapp'],
				'p2'	 => phpgw::get_ip_address()
			));

			$lang_denied = lang('Access not permitted');
			echo <<<HTML
					<div class="error">$lang_denied</div>

HTML;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}

	//  Already called from sessions::verify
	//	$GLOBALS['phpgw']->applications->read_installed_apps();	// to get translated app-titles

	/*	 * ***********************************************************************\
	 * Load the header unless the developer turns it off                       *
	  \************************************************************************ */
	if (!isset($GLOBALS['phpgw_info']['flags']['noheader']) || !$GLOBALS['phpgw_info']['flags']['noheader'])
	{
		$inc_navbar = !isset($GLOBALS['phpgw_info']['flags']['nonavbar']) || !$GLOBALS['phpgw_info']['flags']['nonavbar'];
		$GLOBALS['phpgw']->common->phpgw_header($inc_navbar);
		unset($inc_navbar);
	}

	/*	 * ***********************************************************************\
	 * Load the app include files if the exists                                *
	  \************************************************************************ */
	/* Then the include file */
	if (!preg_match("/phpgwapi/i", PHPGW_APP_INC) && file_exists(PHPGW_APP_INC . '/functions.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
	{
		include_once(PHPGW_APP_INC . '/functions.inc.php');
	}
	if (!@$GLOBALS['phpgw_info']['flags']['noheader'] &&
		!@$GLOBALS['phpgw_info']['flags']['noappheader'] &&
		file_exists(PHPGW_APP_INC . '/header.inc.php') && !isset($GLOBALS['phpgw_info']['menuaction']))
	{
		include_once(PHPGW_APP_INC . '/header.inc.php');
	}

/////////////////////////////////////////////////////////////////////////////
// END Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////
	if (isset($_GET['menuaction']))
	{
		list($app, $class, $method) = explode('.', $_GET['menuaction']);
	}
	else
	{
		$app	 = 'bookingfrontend';
		$class	 = 'uisearch';
		$method	 = 'index';
	}

	if ($app != 'bookingfrontend')
	{
		$invalid_data = true;
		$GLOBALS['phpgw']->common->phpgw_header(true);
		$GLOBALS['phpgw']->log->write(array('text'	 => 'W-Permissions, Attempted to access %1 from %2',
			'p1'	 => $app,
			'p2'	 => phpgw::get_ip_address()
		));

		$lang_denied = lang('Access not permitted');
		echo <<<HTML
				<div class="error">$lang_denied</div>

HTML;
		$GLOBALS['phpgw']->common->phpgw_exit(True);
	}

	$GLOBALS[$class] = CreateObject("{$app}.{$class}");

	if (!$invalid_data && is_object($GLOBALS[$class]) && isset($GLOBALS[$class]->public_functions) && is_array($GLOBALS[$class]->public_functions) && isset($GLOBALS[$class]->public_functions[$method]) && $GLOBALS[$class]->public_functions[$method])
	{
		if (phpgw::get_var('X-Requested-With', 'string', 'SERVER') == 'XMLHttpRequest'
			// deprecated
			|| phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json')
		{
			// comply with RFC 4627
			header('Content-Type: application/json');
			$return_data								 = $GLOBALS[$class]->$method();
			echo json_encode($return_data);
			$GLOBALS['phpgw_info']['flags']['nofooter']	 = true;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		else
		{
			$GLOBALS[$class]->$method();
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
