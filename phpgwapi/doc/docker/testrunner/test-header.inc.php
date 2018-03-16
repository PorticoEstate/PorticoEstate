<?php
	/**
	* phpGroupWare
	*
	* phpgroupware header
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
 	* @author Dave Hall <skwashd@phpgroupware.org>
 	* @author Sigurd Nes <sigurdne@online.no>
 	* @copyright Copyright (C) 2000-2017 Free Software Foundation, Inc. http://www.fsf.org/
  	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
  	* @package phpgroupware
 	* @version $Id: header.inc.php.template 18392 2008-01-26 12:45:06Z skwashd $
 	*/

 	/*
 	   This program is free software: you can redistribute it and/or modify
 	   it under the terms of the GNU General Public License as published by
 	   the Free Software Foundation, either version 2 of the License, or
 	   (at your option) any later version.

 	   This program is distributed in the hope that it will be useful,
 	   but WITHOUT ANY WARRANTY; without even the implied warranty of
 	   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 	   GNU General Public License for more details.

 	   You should have received a copy of the GNU General Public License
 	   along with this program.  If not, see <http://www.gnu.org/licenses/>.
  	*/


	// **************************************************************************
	// !!!!!!! EDIT THESE LINES !!!!!!!!
	// This setting allows you to easily move the include directory and the
	// base of the phpGroupWare install. Simple edit the following 2 lines with
	// the absolute path to fit your site, and you should be up and running.
	// **************************************************************************

	/**
	* Server root directory
	*/
	define('PHPGW_SERVER_ROOT', '/var/www/html/portico');

	/**
	* Include root directory - legacy support
	*/
	define('PHPGW_INCLUDE_ROOT', PHPGW_SERVER_ROOT);

	//We only let preset flags remain everything else is killed
	$flags = array();
	if ( isset($GLOBALS['phpgw_info']['flags']) )
	{
		$flags = $GLOBALS['phpgw_info']['flags'];
	}
	unset($GLOBALS['phpgw_info']);

	/**
	* @global array $phpgw_info the phpgroupware information array
	*/
	$GLOBALS['phpgw_info'] = array('flags' => $flags);
	unset($flags);

	/**
	* @global string $phpgw_info['server']['header_admin_password'] Setup administrator password
	*/
	$GLOBALS['phpgw_info']['server']['header_admin_password'] = 'porticotest';

	/**
	* @global string $phpgw_info['server']['default_lang'] The default language
	*/
	$GLOBALS['phpgw_info']['server']['default_lang'] = 'no';

	/**
	* @global string $phpgw_info[ex'server']['system_name'] The system name
	*/
	$GLOBALS['phpgw_info']['server']['system_name'] = 'Portico Estate';

	// phpGroupWare domain-specific db settings
	// Note that with mcrypt enabled- the database settings are encrypted (the web-interface has to be used to produce the settings)

	$GLOBALS['phpgw_domain']['default'] = array
	(
		'db_host' => 'postgres',
		'db_port' => '5432',
		'db_name' => 'porticotest',
		'db_user' => 'portico',
		'db_pass' => 'porticotest',
		// Look at the README file
		'db_type' => 'postgres',
		'db_abstraction' => 'pdo',
		'config_passwd' => 'porticotest'
	);


	/**
	* @global boolean $phpgw_info['server']['show_domain_selectbox']
	* If you want to have your domains in a select box, change to True
	* If not, users will have to login as user@domain
	* Note: This is only for virtual domain support, default domain users can login only using
	* there login id.
	*/
	$GLOBALS['phpgw_info']['server']['show_domain_selectbox'] = False;

	/**
	* @global $phpgw_info['server']['domain_from_host']
	* As an alternative to the domain select box, set this option to True
	* to use the domain name from the browser provided hostname ($_SERVER['HTTP_HOST'])
	*/
	$GLOBALS['phpgw_info']['server']['domain_from_host'] = False;

	/**
	* @global boolean $phpgw_info['server']['db_persistent']
	* Use persistent database connection
	*/
	$GLOBALS['phpgw_info']['server']['db_persistent'] = True;

	/**
 	* @global string $phpgw_info['server']['sessions_type']
 	* phpGroupWare offers 2 session management systems - php and db
 	* Unless you really know what you are doing use php here as it works better 99.5% of the time
  	*/
 	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'php';

	/**
	* @global string $phpgw_info['login_template_set']
	* Select which login template set you want, most people will use 'simple'
	*/
	$GLOBALS['phpgw_info']['login_template_set'] = 'simple';

	/**
	* @global string $phpgw_info['login_left_message']
	* An optional text to be displayed to the left on the login form.
	* FORMATTING HAS TO BE EDITED MANUALLY (links and linebreak)
	*/
	$login_left_message = <<<HTML

HTML;

	$GLOBALS['phpgw_info']['login_left_message'] = nl2br(str_replace(array('[',']'), array('<','>'), $login_left_message));

	/**
	* @global string $phpgw_info['login_right_message']
	* An optional text to be displayed to the right on the login form.
	* FORMATTING HAS TO BE EDITED MANUALLY (links and linebreak)
	*/
	$login_right_message = <<<HTML

HTML;

	$GLOBALS['phpgw_info']['login_right_message'] = nl2br(str_replace(array('[',']'), array('<','>'), $login_right_message));

	/**
	* @global string $phpgw_info['new_user_url']
	 * An otpional url to new user registration
	 */
	$GLOBALS['phpgw_info']['server']['new_user_url'] = '';

	/**
	* @global string $phpgw_info['lost_password_url']
	* An otpional url to remedy lost passwords
	 */
	$GLOBALS['phpgw_info']['server']['lost_password_url'] = '';

	/**
 	* @global string $phpgw_info['server']['enable_crypto']
 	* phpGroupWare offers 2 crypto systems - mcrypt and libsodium
 	* Note: mcrypt is deprecated from php 7.1+
  	*/
 	$GLOBALS['phpgw_info']['server']['enable_crypto'] = '';

	/**
	* @global boolean $phpgw_info['server']['mcrypt_enabled']
	* This is used to control mcrypt's use
	* Note: deprecated from php 7.1+
	*/
	$GLOBALS['phpgw_info']['server']['mcrypt_enabled'] = false;

	/**
	* @global string $phpgw_info['server']['mcrypt_iv']
	* This is a random string used as the initialization vector for mcrypt
	* feel free to change it when setting up phpgroupware on a clean database,
	* but you must not change it after that point!
	* It should be around 30 bytes in length.
	*/
	$GLOBALS['phpgw_info']['server']['mcrypt_iv'] = '76kZq9vXnDmjiXki76GafgQafn8OAM';

	/**
	* @global string $phpgw_info['server']['setup_mcrypt_key']
	* This is a random string used as the encryption key for mcrypt or libsodium
	* feel free to change it when setting up phpgroupware on a clean database,
	* but you must not change it after that point!
	*/
	$GLOBALS['phpgw_info']['server']['setup_mcrypt_key'] = '1HTmGjvbgTPDc8BxqJD7TTnJ3D6brGYt';

	/*
		This ensures IE gets the right character set
	*/
	header("Content-Type: text/html;charset=utf-8");

	// If you want phpGroupWare to be cached by proxy servers, uncomment the following
	// This is NOT recommended, but phpGroupWare should still work fine.
	if(!isset($GLOBALS['phpgw_info']['flags']['nocachecontrol'])
		|| !$GLOBALS['phpgw_info']['flags']['nocachecontrol'] == True)
	{
		header('Cache-Control: no-cache, must-revalidate');  // HTTP/1.1
		header('Pragma: no-cache');                          // HTTP/1.0
	}

	// debugging settings
	define('DEBUG_APP',  False);
	define('DEBUG_API',  False);
	define('DEBUG_DATATYPES',  True);
	define('DEBUG_LEVEL',  3);
	define('DEBUG_OUTPUT', 2); // 1 = screen,  2 = DB (not supported with PHP3). For both use 3.
	define('DEBUG_TIMER', False);


	/**
	* Perf get microtime
	*
	* @return float Microseconds
	*/
	function perfgetmicrotime()
	{
		list($usec, $sec) = explode(' ',microtime());
		return ((float)$usec + (float)$sec);
	}


	if (DEBUG_TIMER)
	{
		$GLOBALS['debug_timer_start'] = perfgetmicrotime();
	}

	// **************************************************************************
	// Do not edit these lines
	// **************************************************************************

	/**
	* phpgroupware API include root
	*/
	define('PHPGW_API_INC',PHPGW_INCLUDE_ROOT.'/phpgwapi/inc');

	/**
	* Include API setup
	*/
	include(PHPGW_SERVER_ROOT . '/phpgwapi/setup/setup.inc.php');

	/**
	* @global string $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']
	* Installed API version
	*/
	$GLOBALS['phpgw_info']['server']['versions']['phpgwapi'] = $setup_info['phpgwapi']['version'];

	/**
	* @global string $phpgw_info['server']['versions']['system']
	* Installed header version
	*/
	$GLOBALS['phpgw_info']['server']['versions']['system'] = $setup_info['phpgwapi']['versions']['system'];

	/**
	* @global string $phpgw_info['server']['versions']['current_header']
	* Installed header version
	*/
	$GLOBALS['phpgw_info']['server']['versions']['current_header'] = $setup_info['phpgwapi']['versions']['current_header'];

	unset($setup_info);

	/**
	* @global string $phpgw_info['server']['versions']['header']
	* Version of this header file
	*/
	$GLOBALS['phpgw_info']['server']['versions']['header'] = '1.31';


	if ( !isset($GLOBALS['phpgw_info']['flags']['noapi'])
		|| !$GLOBALS['phpgw_info']['flags']['noapi'] )
	{
		/**
		* Include global general functions
		*/
		require_once PHPGW_API_INC . '/functions.inc.php';
	}

	// Leave off the final php closing tag, some editors will add
	// a \n or space after which will mess up cookies later on
