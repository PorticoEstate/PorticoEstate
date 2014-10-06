<?php
	/**************************************************************************\
	* phpGroupWare - Registration                                              *
	* http://www.phpgroupware.org                                              *
	* This application written by Joseph Engo <jengo@phpgroupware.org>         *
	* --------------------------------------------                             *
	* Funding for this program was provided by http://www.checkwithmom.com     *
	* --------------------------------------------                             *
	*  This program is free software; you can redistribute it and/or modify it *
	*  under the terms of the GNU General Public License as published by the   *
	*  Free Software Foundation; either version 2 of the License, or (at your  *
	*  option) any later version.                                              *
	\**************************************************************************/

	/* $Id$ */

	/*
	** This program is non-standard, we will create and manage our sessions manually.
	** We don't want users to be kicked out half way through, and we really don't need a true
	** session for it.
	*/

	// Note: This is current not a drop in install, it requires some manual installation
	//       Take a look at the README file
	$domain         = isset($_REQUEST['logindomain']) && $_REQUEST['logindomain'] ? $_REQUEST['logindomain'] : 'default';
	$template_set   = 'checkwithmom';


	$_GET['domain'] = $domain;

    $GLOBALS['phpgw_info']['flags'] = array(
        'noheader'		=> true,
        'nonavbar'		=> true,
        'currentapp'	=> 'login',
 		'noapi'      	=> true		// this stops header.inc.php to include phpgwapi/inc/function.inc.php
    );
    

	$legal_anonymous_access = array
	(
		'registration' => array
		(
			'uireg'	=> array
			(
				'step1'				=> true,
				'tos'				=> true,
				'ready_to_activate'	=> true,
				'lostpw1'			=> true,
				'email_sent_lostpw'	=> true
			),
			'boreg'	=> array
			(
				'step1'				=> true,
				'step2'				=> true,
				'step4'				=> true,
				'lostpw1'			=> true,
				'lostpw2'			=> true,
				'lostpw3'			=> true,
				'get_locations'		=> true
			)
		)
	);


    $GLOBALS['phpgw_info']['flags']['session_name'] = 'registration_session';
	$GLOBALS['phpgw_remote_user_fallback'] = 'sql';
	include_once('../header.inc.php');

	unset($GLOBALS['phpgw_info']['flags']['noapi']);

	$_domain_info = isset($GLOBALS['phpgw_domain'][$_GET['domain']]) ? $GLOBALS['phpgw_domain'][$_GET['domain']] : '';
	$GLOBALS['_phpgw_domain'] = $GLOBALS['phpgw_domain'];

	if(!$_domain_info)
	{
		echo "not a valid domain\n";
		die();
	}
	else
	{
		$GLOBALS['phpgw_domain'] = array();
		$GLOBALS['phpgw_domain'][$_GET['domain']] = $_domain_info;
	}

	include(PHPGW_API_INC.'/functions.inc.php');


	$c = createobject('phpgwapi.config','registration');
	$c->read();
	$config = $c->config_data;

	// Make sure we're always logged in
	if (!phpgw::get_var(session_name()) || !$GLOBALS['phpgw']->session->verify())
	{

//_debug_array($config);die();

		$login = $c->config_data['anonymous_user'];
		$passwd = $c->config_data['anonymous_pass'];
		$_POST['submitit'] = "";

		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
		if(!$GLOBALS['sessionid'])
		{
			$lang_denied = lang('Anonymous access not correctly configured');
			if($GLOBALS['phpgw']->session->reason)
			{
	//			$lang_denied = $GLOBALS['phpgw']->session->reason;
			}
			echo <<<HTML
				<div class="error">$lang_denied</div>
HTML;
			$GLOBALS['phpgw']->common->phpgw_exit(True);
		}
	}
	$GLOBALS['phpgw_info']['flags']['currentapp'] = 'registration';
	
	/////////////////////////////////////////////////////////////////////////////
// BEGIN Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////

		if(isset($GLOBALS['phpgw_info']['user']['preferences']['common']['lang']) && $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] !='en')
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
		* Verify that the users session is still active otherwise kick them out   *
		\*************************************************************************/
		if ($GLOBALS['phpgw_info']['flags']['currentapp'] != 'home' && $GLOBALS['phpgw_info']['flags']['currentapp'] != 'about')
		{
			if (!$GLOBALS['phpgw']->acl->check('run', PHPGW_ACL_READ, $GLOBALS['phpgw_info']['flags']['currentapp']))
			{
				$GLOBALS['phpgw']->common->phpgw_header(true);
				$GLOBALS['phpgw']->log->write(array('text'=>'W-Permissions, Attempted to access %1','p1'=>$GLOBALS['phpgw_info']['flags']['currentapp']));

				$lang_denied = lang('Access not permitted');
				echo <<<HTML
					<div class="error">$lang_denied</div>

HTML;
				$GLOBALS['phpgw']->common->phpgw_exit(True);
			}
		}

	//  Already called from sessions::verify
	//	$GLOBALS['phpgw']->applications->read_installed_apps();	// to get translated app-titles

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

/////////////////////////////////////////////////////////////////////////////
// END Stuff copied from functions.inc.php
/////////////////////////////////////////////////////////////////////////////

	if (isset($_GET['menuaction']))
	{
		list($app,$class,$method) = explode('.',$_GET['menuaction']);
	}
	else
	{
		$app = 'registration';
		if($config['username_is'] != 'email')
		{
			$class = 'uireg';
			$method = 'step1';
		}
		else
		{
			$class = 'boreg';
			$method = 'step1';
		}
	}
	$GLOBALS[$class] = CreateObject("{$app}.{$class}");

	$invalid_data = false;

	if(!isset($legal_anonymous_access[$app][$class][$method]))
	{
		$invalid_data = true;

		$GLOBALS['phpgw']->log->message(array(
			'text' => "W-BadmenuactionVariable, attempted to access private method as anonymous: {$app}.{$class}.{$method}",
			'line' => __LINE__,
			'file' => __FILE__
		));
		$GLOBALS['phpgw']->log->commit();
		echo "This method is not alloved from this application as anonymous: {$app}.{$class}.{$method}";

	}

	if ( !$invalid_data 
		&& is_object($GLOBALS[$class])
		&& isset($GLOBALS[$class]->public_functions) 
		&& is_array($GLOBALS[$class]->public_functions) 
		&& isset($GLOBALS[$class]->public_functions[$method])
		&& $GLOBALS[$class]->public_functions[$method] )

	{
		if ( phpgw::get_var('X-Requested-With', 'string', 'SERVER') == 'XMLHttpRequest'
			 // deprecated
			|| phpgw::get_var('phpgw_return_as', 'string', 'GET') == 'json' )
		{
			// comply with RFC 4627
			header('Content-Type: application/json'); 
			$return_data = $GLOBALS[$class]->$method();
			echo json_encode($return_data);
			$GLOBALS['phpgw_info']['flags']['nofooter'] = true;
			$GLOBALS['phpgw']->common->phpgw_exit();
		}
		else
		{
			$GLOBALS[$class]->$method();	
			$GLOBALS['phpgw']->common->phpgw_footer();
		}
	}
	$GLOBALS['phpgw']->common->phpgw_footer();
