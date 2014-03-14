<?php
	/**
	* phpGroupWare - Login
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @author Sigurd Nes <sigurdne@online.no>
	* @copyright Copyright (C) 2000-2013 Free Software Foundation, Inc. http://www.fsf.org/
	* @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License v2 or later
	* @package phpgwapi
	* @subpackage login
	* @version $Id: class.login.inc.php 11651 2014-02-02 17:03:26Z sigurdne $
	*/

	/*
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU Lesser General Public License as published by
		the Free Software Foundation, either version 2 of the License, or
		(at your option) any later version.

		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU Lesser General Public License for more details.

		You should have received a copy of the GNU Lesser General Public License
		along with this program.  If not, see <http://www.gnu.org/licenses/>.
	 */

	/**
	* Login - enables common handling of the login process from different part of the system
	*
	* @package phpgwapi
	* @subpackage login
	*/
	class phpgwapi_login
	{
		public function __construct()
		{
		
		}

		public function login($frontend = '')
		{

			if (isset($_REQUEST['skip_remote']) && $_REQUEST['skip_remote'])
			{
				$GLOBALS['phpgw_remote_user_fallback'] = 'sql';
			}

			if (isset($_GET['logout']) && $_GET['logout']) // In case a user logged in via SSO - actively logs out
			{
				$GLOBALS['phpgw_remote_user_fallback']	 = 'sql';
				$_REQUEST['skip_remote']				 = true;
			}

			require_once dirname(realpath(__FILE__)) . '/sso/include_login.inc.php';

			$lightbox			 = isset($_REQUEST['lightbox']) && $_REQUEST['lightbox'] ? true : false;
			$partial_url		 = "{$frontend}/login.php";
			$phpgw_url_for_sso	 = 'phpgwapi/inc/sso/login_server.php';

			if (isset($GLOBALS['phpgw_remote_user']) && !empty($GLOBALS['phpgw_remote_user']))
			{
				$partial_url = 'phpgwapi/inc/sso/login_server.php';
			}

			if($frontend)
			{
				$GLOBALS['phpgw']->hooks->process('set_auth_type', array($frontend));
			}
			
			if (isset($_REQUEST['skip_remote']) && $_REQUEST['skip_remote']) // In case a user failed logged in via SSO - get another try
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = $GLOBALS['phpgw_remote_user_fallback'];
			}

			/* Program starts here */
			$uilogin = new phpgw_uilogin($tmpl, $GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser' && !isset($GLOBALS['phpgw_remote_user']));

			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser' && isset($GLOBALS['phpgw_info']['server']['mapping']) && !empty($GLOBALS['phpgw_info']['server']['mapping']) && isset($_SERVER['REMOTE_USER']))
			{
				$login = $GLOBALS['phpgw']->mapping->get_mapping($_SERVER['REMOTE_USER']);
				if ($login == '') // mapping failed
				{
					if (isset($GLOBALS['phpgw_info']['server']['auto_create_acct']) && $GLOBALS['phpgw_info']['server']['auto_create_acct'] == true)
					{
						// Redirection to create the new account :
						$GLOBALS['phpgw']->redirect_link('/phpgwapi/inc/sso/create_account.php');
					}
					else if ($GLOBALS['phpgw_info']['server']['mapping'] == 'table' || $GLOBALS['phpgw_info']['server']['mapping'] == 'all')
					{
						// Redirection to create a new mapping :
						$GLOBALS['phpgw']->redirect_link('/phpgwapi/inc/sso/create_mapping.php');
					}
					else if (!(isset($_GET['cd']) && $_GET['cd'] != '0'))
					{
						// An error occurs, bailed out
						$GLOBALS['phpgw']->redirect_link('/' . $partial_url, array('cd' => '20'));
					}
				}
				$passwd	 = $login;
				if (!(isset($_GET['cd']) && $_GET['cd'] != '0'))
				{
					$_POST['submitit'] = true;
				}
			}
			else
			{
				$login	 = phpgw::get_var('login', 'string', 'POST');
				// remove entities to stop mangling
				$passwd	 = html_entity_decode(phpgw::get_var('passwd', 'string', 'POST'));
			}
			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'http' && isset($_SERVER['PHP_AUTH_USER']))
			{
				$submit	 = true;
				$login	 = $_SERVER['PHP_AUTH_USER'];
				$passwd	 = $_SERVER['PHP_AUTH_PW'];
			}

			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'ntlm' && isset($_SERVER['REMOTE_USER']) && (!isset($_REQUEST['skip_remote']) || !$_REQUEST['skip_remote']))
			{
				$login	 = $_SERVER['REMOTE_USER'];
				$passwd	 = '';

				$GLOBALS['hook_values'] = array
				(
					'account_lid' => $login
				);

				$GLOBALS['phpgw']->hooks->process('auto_addaccount', array('frontend'));

			//------------------Start login ntlm

				$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

				if (!isset($GLOBALS['sessionid']) || !$GLOBALS['sessionid'])
				{
					$cd_array = array();
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']			 = $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;

					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (ereg('phpgw_', $name))
						{
							$name				 = urlencode($name);
							$extra_vars[$name]	 = urlencode($value);
						}
					}
				}
				if (!isset($GLOBALS['phpgw_info']['server']['disable_autoload_langfiles']) || !$GLOBALS['phpgw_info']['server']['disable_autoload_langfiles'])
				{
			//			$uilogin->check_langs();
				}
				$extra_vars['cd'] = 'yes';

				$GLOBALS['phpgw']->hooks->process('login');
				$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);

			//----------------- End login ntlm
			}

			# Apache + mod_ssl style SSL certificate authentication
			# Certificate (chain) verification occurs inside mod_ssl
			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'sqlssl' && isset($_SERVER['SSL_CLIENT_S_DN']) && !isset($_GET['cd']))
			{
				# an X.509 subject looks like:
				# /CN=john.doe/OU=Department/O=Company/C=xx/Email=john@comapy.tld/L=City/
				# the username is deliberately lowercase, to ease LDAP integration
				$sslattribs	 = explode('/', $_SERVER['SSL_CLIENT_S_DN']);
				# skip the part in front of the first '/' (nothing)
				while ($sslattrib	 = next($sslattribs))
				{
					list($key, $val) = explode('=', $sslattrib);
					$sslattributes[$key] = $val;
				}

				if (isset($sslattributes['Email']))
				{
					$submit = true;

					# login will be set here if the user logged out and uses a different username with
					# the same SSL-certificate.
					if (!isset($_POST['login']) && isset($sslattributes['Email']))
					{
						$login	 = $sslattributes['Email'];
						# not checked against the database, but delivered to authentication module
						$passwd	 = $_SERVER['SSL_CLIENT_S_DN'];
					}
				}
				unset($key);
				unset($val);
				unset($sslattributes);
			}

			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'customsso' && !isset($_GET['cd']))
			{

				//Reset auth object
				$GLOBALS['phpgw']->auth	= createObject('phpgwapi.auth');
				$login = $GLOBALS['phpgw']->auth->get_username();

				$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, '');

				if (!isset($GLOBALS['sessionid']) || !$GLOBALS['sessionid'])
				{
					$cd_array = array();
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']			 = $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;

					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (ereg('phpgw_', $name))
						{
							$name				 = urlencode($name);
							$extra_vars[$name]	 = urlencode($value);
						}
					}
				}

				$extra_vars['cd'] = 'yes';

				$GLOBALS['phpgw']->hooks->process('login');
				$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
			}


			if ((isset($_POST['submitit']) || isset($_POST['submit_x']) || isset($_POST['submit_y'])))
			{
				if ($_SERVER['REQUEST_METHOD'] != 'POST' &&
						!isset($_SERVER['PHP_AUTH_USER']) &&
						!isset($_SERVER['REMOTE_USER']) &&
						!isset($_SERVER['SSL_CLIENT_S_DN'])
				)
				{
					$GLOBALS['phpgw']->redirect_link('/' . $partial_url, array('cd' => '5'));
				}

				$logindomain = phpgw::get_var('logindomain', 'string', 'POST');
				if (strstr($login, '#') === false && $logindomain)
				{
					$login .= "#{$logindomain}";
				}

				$receipt = array();
				if (isset($GLOBALS['phpgw_info']['server']['usecookies'])
						&& $GLOBALS['phpgw_info']['server']['usecookies'])
				{
					if (isset($_COOKIE['domain']) && $_COOKIE['domain'] != $logindomain)
					{
						$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
						$GLOBALS['phpgw']->session->phpgw_setcookie('domain');
			//				$GLOBALS['phpgw']->redirect_link("/{$partial_url}", array('cd' =>22)); // already within a session
			//				exit;

						$receipt[] = lang('Info: you have changed domain from "%1" to "%2"', $_COOKIE['domain'], $logindomain);
					}
				}

				$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

				if (!isset($GLOBALS['sessionid']) || !$GLOBALS['sessionid'])
				{
					$cd_array = array();
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']			 = $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;
					$cd_array['lightbox']	 = $lightbox;
					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				if ($receipt)
				{
					phpgwapi_cache::message_set($receipt, 'message');
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (ereg('phpgw_', $name))
						{
							//$extra_vars[$name] = $value;
							$name				 = urlencode($name);
							$extra_vars[$name]	 = urlencode($value);
						}
					}
				}
				if (!isset($GLOBALS['phpgw_info']['server']['disable_autoload_langfiles']) || !$GLOBALS['phpgw_info']['server']['disable_autoload_langfiles'])
				{
		//			$uilogin->check_langs();
				}
				$extra_vars['cd'] = 'yes';

				$GLOBALS['phpgw']->hooks->process('login');

				if ($lightbox)
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/login.php", array('hide_lightbox' => true));
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
				}
			}

			//Build vars :
			$variables = array();
			$variables['lang_login']	= lang('login');
			$variables['partial_url']	= $partial_url;
			$variables['lang_frontend']	= $frontend ? lang($frontend) : '';
			if (isset($GLOBALS['phpgw_info']['server']['half_remote_user']) && $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser')
			{
				$variables['lang_additional_url']	 = lang('use sso login');
				$variables['additional_url']		 = $GLOBALS['phpgw']->link('/' . $phpgw_url_for_sso);
			}

			$uilogin->phpgw_display_login($variables);
		}
	}
