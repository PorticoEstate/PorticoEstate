<?php
	/**
	 * phpGroupWare - property: a Facilities Management System.
	 *
	 * @author Jonas Borgström jonas.borgstrom@redpill.se
	 * @author Sigurd Nes <sigurdne@online.no>
	 * @copyright Copyright (C) 2017 Free Software Foundation, Inc. http://www.fsf.org/
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
	 * @package eventplannerfrontend
	 * @version $Id: index.php 14959 2016-04-30 21:09:01Z sigurdne $
	 */

	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader' => true,
		'nonavbar' => true,
		'currentapp' => 'login', // To stop functions.inc.php from validating the session
	);
	$GLOBALS['phpgw_info']['flags']['session_name'] = 'eventplannerfrontendsession';
	$GLOBALS['phpgw_remote_user_fallback'] = 'sql';
	include_once('../header.inc.php');

	// Make sure we're always logged in
	if (!phpgw::get_var(session_name()) || !$GLOBALS['phpgw']->session->verify())
	{
		$c = createobject('phpgwapi.config', 'eventplannerfrontend');
		$c->read();
		$config = $c->config_data;

		$login = $c->config_data['anonymous_user'];
		$passwd = $c->config_data['anonymous_passwd'];
		$_POST['submitit'] = "";
		$domain = phpgw::get_var('domain', 'string', 'GET');
		if (strstr($login, '#') === false && $domain)
		{
			$login .= "#{$domain}";
		}

		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
		if (!$GLOBALS['sessionid'])
		{
			$lang_denied = lang('Anonymous access not correctly configured');
			if ($GLOBALS['phpgw']->session->reason)
			{
				$lang_denied = $GLOBALS['phpgw']->session->reason;
			}
			echo <<<HTML
				<div class="error">$lang_denied</div>
HTML;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
		ExecMethod('phpgwapi.menu.clear');
	}

	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'eventplannerfrontend';
	$GLOBALS['phpgw_info']['user']['preferences']['common']['template_set'] = 'frontend';

/////////////////////////////////////////////////////////////////////////////
// BEGIN Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////

	if (isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] != 'en')
	{
		$GLOBALS['phpgw']->translation->set_userlang($GLOBALS['phpgw_info']['user']['preferences']['common']['lang'], true);
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
	if(is_array($GLOBALS['phpgw_info']['flags']))
	{
		foreach ($GLOBALS['phpgw_info']['flags'] as $phpgw_class_name => $dummy)
		{
			if (preg_match('/enable_/', $phpgw_class_name))
			{
				$enable_class = str_replace('enable_', '', $phpgw_class_name);
				$enable_class = str_replace('_class', '', $enable_class);
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
		$GLOBALS['phpgw']->template = createObject('phpgwapi.template', PHPGW_APP_TPL);
		$GLOBALS['phpgw']->xslttpl = createObject('phpgwapi.xslttemplates', PHPGW_APP_TPL);
	}

	/*	 * ***********************************************************************\
	 * Verify that the users session is still active otherwise kick them out   *
	  \************************************************************************ */
	if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home' && $GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
	{
		if (!$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $GLOBALS['phpgw_info']['flags']['currentapp']))
		{
			$GLOBALS['phpgw']->common->phpgw_header(true);
			$GLOBALS['phpgw']->log->write(array('text' => 'W-Permissions, Attempted to access %1',
				'p1' => $GLOBALS['phpgw_info']['flags']['currentapp']));

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
