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
	* @version $Id$
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

			if ( $_POST['mode'] == 'api' )
			{
				$_POST['submitit'] = true;
				$GLOBALS['phpgw_remote_user_fallback']	 = 'sql';
				$_REQUEST['skip_remote']				 = true;
				switch ($_POST['section'])
				{
					case 'activitycalendarfrontend':
						$GLOBALS['phpgw_info']['flags']['session_name'] = 'activitycalendarfrontendsession';
						break;
					case 'bookingfrontend':
						$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
						break;
					case 'eventplannerfrontend':
						$GLOBALS['phpgw_info']['flags']['session_name'] = 'eventplannerfrontendsession';
						break;
					default://nothing
						break;
				}
			}

			require_once dirname(realpath(__FILE__)) . '/sso/include_login.inc.php';

			$lightbox			 = isset($_REQUEST['lightbox']) && $_REQUEST['lightbox'] ? true : false;
			$partial_url		 = ltrim("{$frontend}/login.php", '/');
			$phpgw_url_for_sso	 = 'phpgwapi/inc/sso/login_server.php';

			if (isset($GLOBALS['phpgw_remote_user']) && !empty($GLOBALS['phpgw_remote_user']))
			{
				$partial_url = 'phpgwapi/inc/sso/login_server.php';
			}

			if($frontend)
			{
				$GLOBALS['phpgw']->hooks->single('set_auth_type', $frontend);
				$GLOBALS['phpgw_info']['login_left_message'] = '';
				$GLOBALS['phpgw_info']['login_right_message'] = '';
			}

			if(!phpgw::get_var('after','string', 'COOKIE'))
			{
				$after = phpgw::get_var('after', 'bool');
				$GLOBALS['phpgw']->session->phpgw_setcookie('after',phpgw::get_var('after', 'string'),$cookietime=0);
			}
			else
			{
				$after = true;
			}

			if (isset($_REQUEST['skip_remote']) && $_REQUEST['skip_remote']) // In case a user failed logged in via SSO - get another try
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = $GLOBALS['phpgw_remote_user_fallback'];
			}

			/* Program starts here */
			$uilogin = new phpgw_uilogin($tmpl, $GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser' && !isset($GLOBALS['phpgw_remote_user']));

			if(phpgw::get_var('hide_lightbox', 'bool'))
			{
				$uilogin->phpgw_display_login(array());
				exit;
			}

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
				$remote_user = explode('@', $_SERVER['REMOTE_USER']);
				$login   = $remote_user[0];//$_SERVER['REMOTE_USER'];
				$passwd	 = '';

				$GLOBALS['hook_values'] = array
				(
					'account_lid' => $login
				);

				$GLOBALS['phpgw']->hooks->process('auto_addaccount', array('frontend', 'helpdesk'));

			//------------------Start login ntlm

				$logindomain = phpgw::get_var('domain', 'string', 'GET');
				if (strstr($login, '#') === false && $logindomain)
				{
					$login .= "#{$logindomain}";
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

					if ($lightbox)
					{
						$cd_array['lightbox'] = true;
					}

					if ($logindomain)
					{
						$cd_array['domain'] = $logindomain;
					}

					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (preg_match('/phpgw_/', $name))
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
				if ($lightbox)
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/login.php", array('hide_lightbox' => true));
				}
				else
				{
					if ($after)
					{
						$this->redirect_after($frontend);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
					}
				}
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

			if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'customsso' &&  (!isset($_REQUEST['skip_remote']) || !$_REQUEST['skip_remote']))
			{
				//Reset auth object
				$GLOBALS['phpgw']->auth	= createObject('phpgwapi.auth');
				$login = $GLOBALS['phpgw']->auth->get_username();
				$logindomain = phpgw::get_var('domain', 'string', 'GET');

				if($login)
				{
					$GLOBALS['hook_values'] = array
					(
						'account_lid' => $login
					);
					$GLOBALS['phpgw']->hooks->process('auto_addaccount', array('frontend', 'helpdesk'));

					if (strstr($login, '#') === false && $logindomain)
					{
						$login .= "#{$logindomain}";
					}

					$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, '');
				}

				if (!$login || empty($GLOBALS['sessionid']))
				{
					$cd_array = array();
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']			 = $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;

					if ($lightbox)
					{
						$cd_array['lightbox'] = true;
					}
					if ($logindomain)
					{
						$cd_array['domain'] = $logindomain;
					}

					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (preg_match('/phpgw_/', $name))
						{
							$name				 = urlencode($name);
							$extra_vars[$name]	 = urlencode($value);
						}
					}
				}

				$extra_vars['cd'] = 'yes';

				if ($lightbox)
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/login.php", array('hide_lightbox' => true));
				}
				else
				{
					$GLOBALS['phpgw']->hooks->process('login');
					if ($after)
					{
						$this->redirect_after($frontend);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
					}
				}
			}
			else if ($GLOBALS['phpgw_info']['server']['auth_type'] == 'azure' &&  (!isset($_REQUEST['skip_remote']) || !$_REQUEST['skip_remote']))
			{
				//Reset auth object
				$GLOBALS['phpgw']->auth	= createObject('phpgwapi.auth');
				$login = $GLOBALS['phpgw']->auth->get_username();
				$logindomain = phpgw::get_var('domain', 'string', 'GET');

				if($login)
				{
					$GLOBALS['hook_values'] = array
					(
						'account_lid' => $login
					);
					$GLOBALS['phpgw']->hooks->process('auto_addaccount', array('frontend', 'helpdesk'));

					if (strstr($login, '#') === false && $logindomain)
					{
						$login .= "#{$logindomain}";
					}

					$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, '');
				}

				if (!$login || empty($GLOBALS['sessionid']))
				{
					$cd_array = array();
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']			 = $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;

					if ($lightbox)
					{
						$cd_array['lightbox'] = true;
					}
					if ($logindomain)
					{
						$cd_array['domain'] = $logindomain;
					}

					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;
				}

				$forward = phpgw::get_var('phpgw_forward');
				if ($forward)
				{
					$extra_vars['phpgw_forward'] = $forward;
					foreach ($_GET as $name => $value)
					{
						if (preg_match('/phpgw_/', $name))
						{
							$name				 = urlencode($name);
							$extra_vars[$name]	 = urlencode($value);
						}
					}
				}

				$extra_vars['cd'] = 'yes';

				if ($lightbox)
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/login.php", array('hide_lightbox' => true));
				}
				else
				{
					$GLOBALS['phpgw']->hooks->process('login');
					if ($after)
					{
						$this->redirect_after($frontend);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
					}
				}
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

				if ( phpgw::get_var('mode', 'string', 'POST') == 'api' )
				{
					header('Content-Type: application/json');
					echo json_encode(array(
						'sessionid' => $GLOBALS['sessionid'],
						'session_name'	=> session_name(),
					));
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
						if (preg_match('/phpgw_/', $name))
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


				if ($lightbox)
				{
					$GLOBALS['phpgw']->redirect_link("{$frontend}/login.php", array('hide_lightbox' => true));
				}
				else
				{
					$GLOBALS['phpgw']->hooks->process('login');
					if ($after)
					{
						$this->redirect_after($frontend);
					}
					else
					{
						$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php", $extra_vars);
					}
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

		function redirect_after($frontend = '')
		{
			$redirect = phpgw::get_var('after','string', 'COOKIE');
		//	_debug_array($_COOKIE);
		//	_debug_array($after);
		//	die();
			$GLOBALS['phpgw']->session->phpgw_setcookie('after', false, 0);

			if ( is_array($redirect) && count($redirect) )
			{
				foreach($redirect as $key => $value)
				{
					$redirect_data[$key] = phpgw::clean_value($value);
				}

				$sessid = phpgw::get_var('sessionid', 'string', 'GET');
				if ( $sessid )
				{
					$redirect_data['sessionid'] = $sessid;
					$redirect_data['kp3'] = phpgw::get_var('kp3', 'string', 'GET');
				}

				$GLOBALS['phpgw']->redirect_link("{$frontend}/index.php", $redirect_data);
			}

			$redirect_arr = explode('.',$redirect);

			if (count($redirect_arr) == 3 && isset($GLOBALS['phpgw_info']['user']['apps'][$redirect_arr[0]]))
			{
				$GLOBALS['phpgw']->redirect_link("{$frontend}/index.php", array('menuaction' => $redirect));
			}
			else
			{
				//failsafe
				$GLOBALS['phpgw']->redirect_link("{$frontend}/home.php");
			}
		}
	}
