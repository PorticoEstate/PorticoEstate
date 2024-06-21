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
			/*
			 * Generic include for login.php like pages
			 */
			if(!empty( $GLOBALS['phpgw_info']['flags']['session_name'] ))
			{
				$session_name = $GLOBALS['phpgw_info']['flags']['session_name'];
			}

			if(!empty( $GLOBALS['phpgw_info']['flags']['custom_frontend'] ))
			{
				$custom_frontend = $GLOBALS['phpgw_info']['flags']['custom_frontend'];
			}

			$GLOBALS['phpgw_info'] = array();

			$GLOBALS['phpgw_info']['flags'] = array
			(
				'disable_template_class' => true,
				'login'                  => true,
				'currentapp'             => 'login',
				'noheader'               => true
			);
			if(!empty($session_name))
			{
				$GLOBALS['phpgw_info']['flags']['session_name'] = $session_name;
			}
			if(!empty($custom_frontend))
			{
				$GLOBALS['phpgw_info']['flags']['custom_frontend'] = $custom_frontend;
			}

			$header = dirname(realpath(__FILE__)) . '/../../header.inc.php';
			if ( !file_exists($header) )
			{
				Header('Location: ../setup/index.php');
				exit;
			}


			/**
			* check for emailaddress as username
			*/
			if ( isset($_POST['login']) && $_POST['login'] != '')
			{
				if (!filter_var($_POST['login'], FILTER_VALIDATE_EMAIL))
				{
					$_POST['login'] = str_replace('@', '#', $_POST['login']);
				}
			}

			/**
			* Include phpgroupware header
			*/
			require_once $header;

			if(!empty($_GET['debug']))
			{
				_debug_array($_SERVER);
				$GLOBALS['phpgw']->common->phpgw_exit();
			}

			require_once dirname(realpath(__FILE__)) . '/sso/include_login.inc.php';
		}

		public function create_account( )
		{
			require_once dirname(realpath(__FILE__)) . '/sso/create_account.php';
			$create_account = new phpgwapi_create_account();
			$create_account->display_create();
		}

		public function create_mapping( )
		{
			require_once dirname(realpath(__FILE__)) . '/sso/create_mapping.php';
			$create_account = new phpgwapi_create_mapping();
			$create_account->create_mapping();
		}


		public function login($frontend = '', $anonymous = false)
		{

			if(isset($_REQUEST['hide_lightbox']) && $_REQUEST['hide_lightbox'])
			{
				$onload = <<<JS
					<script language="javascript" type="text/javascript">
						if(typeof(parent.lightbox_login) != 'undefined')
						{
						parent.lightbox_login.hide();
						}
						else
						{
							parent.TINY.box.hide();
						}
					</script>
JS;
			echo <<<HTML
<html><head>{$onload}</head></html>
HTML;
				exit;
			}

			if (isset($_REQUEST['skip_remote']) && $_REQUEST['skip_remote'])
			{
				$GLOBALS['phpgw_remote_user_fallback'] = 'sql';
			}

			if (isset($_GET['logout']) && $_GET['logout']) // In case a user logged in via SSO - actively logs out
			{
				$GLOBALS['phpgw_remote_user_fallback']	 = 'sql';
				$_REQUEST['skip_remote']				 = true;
			}

			if ( isset($_POST['api_mode']) && $_POST['api_mode'] == true )
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

			$lightbox			 = isset($_REQUEST['lightbox']) && $_REQUEST['lightbox'] ? true : false;
//			$partial_url		 = ltrim("{$frontend}/login.php", '/');
			$partial_url		 = 'login.php';
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

//			if(!phpgw::get_var('after','string', 'COOKIE'))
//			{
//				$after = phpgw::get_var('after', 'bool');
//				$GLOBALS['phpgw']->session->phpgw_setcookie('after',phpgw::get_var('after', 'string'), 0);
//			}
//			else
//			{
//				$after = true;
//			}

			if (isset($_REQUEST['skip_remote']) && $_REQUEST['skip_remote']) // In case a user failed logged in via SSO - get another try
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = $GLOBALS['phpgw_remote_user_fallback'];
			}

			/* Program starts here */

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

//					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					$this->login_failed( $partial_url, $cd_array, $anonymous, $frontend );

					exit;
				}
				
				$this->login_forward();

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

//					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					$this->login_failed( $partial_url, $cd_array, $anonymous, $frontend );
					exit;
				}

				$this->login_forward();
			}

			/**
			 * OpenID Connect
			 */
			else if (
				in_array($GLOBALS['phpgw_info']['server']['auth_type'],  array('remoteuser', 'azure'))
				&& !empty($GLOBALS['phpgw_info']['server']['mapping'])
				&& (isset($_SERVER['OIDC_upn']) || isset($_SERVER['REMOTE_USER']) || isset($_SERVER['OIDC_pid']))
				&& empty($_REQUEST['skip_remote']))
			{
				$phpgw_map_location = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? $_SERVER['HTTP_SHIB_ORIGIN_SITE'] : 'local';
				$phpgw_map_authtype = isset($_SERVER['HTTP_SHIB_ORIGIN_SITE']) ? 'shibboleth':'remoteuser';

				//Create the mapping if necessary :
				if(isset($GLOBALS['phpgw_info']['server']['mapping']) && !empty($GLOBALS['phpgw_info']['server']['mapping']))
				{
					if(!is_object($GLOBALS['phpgw']->mapping))
					{
						$GLOBALS['phpgw']->mapping = CreateObject('phpgwapi.mapping', array('auth_type'=> $phpgw_map_authtype, 'location' => $phpgw_map_location));
					}
				}

				$login = $GLOBALS['phpgw']->auth->get_username();
				$logindomain = phpgw::get_var('domain', 'string', 'GET');

				if($login)
				{
					if (strstr($login, '#') === false && $logindomain)
					{
						$login .= "#{$logindomain}";
					}

					/**
					 * One last check...
					 */
					if(!phpgw::get_var('OIDC_pid', 'string', 'SERVER'))
					{
						$ad_groups = array();
						if(!empty($_SERVER["OIDC_groups"]))
						{
							$OIDC_groups = mb_convert_encoding(mb_convert_encoding($_SERVER["OIDC_groups"], 'ISO-8859-1', 'UTF-8'), 'UTF-8', 'ISO-8859-1');
							$ad_groups	= explode(",",$OIDC_groups);
						}
						$default_group_lid	 = !empty($GLOBALS['phpgw_info']['server']['default_group_lid']) ? $GLOBALS['phpgw_info']['server']['default_group_lid'] : 'Default';

						if (!in_array($default_group_lid, $ad_groups))
						{
							echo lang('missing membership: "%1" is not in the list', $default_group_lid);
							$GLOBALS['phpgw']->common->phpgw_exit();
						}
					}

					$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, '');
				}
				else if (!$login || empty($GLOBALS['sessionid']))
				{
					if(!empty($GLOBALS['phpgw_info']['server']['auto_create_acct']))
					{

						if ($GLOBALS['phpgw_info']['server']['mapping'] == 'id')
						{
							// Redirection to create the new account :
							return $this->create_account();
						}
						else if ($GLOBALS['phpgw_info']['server']['mapping'] == 'table' || $GLOBALS['phpgw_info']['server']['mapping'] == 'all')
						{
							// Redirection to create a new mapping :
							return $this->create_mapping();
						}
					}

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

					$this->login_failed( $partial_url, $cd_array, $anonymous, $frontend );
					exit;
				}

				$this->login_forward();
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

				//Reset auth object
				$GLOBALS['phpgw']->auth	= createObject('phpgwapi.auth');

				$login	 = phpgw::get_var('login', 'string', 'POST');
				// remove entities to stop mangling
				$passwd	 = html_entity_decode(phpgw::get_var('passwd', 'string', 'POST'));

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
//					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					$this->login_failed( $partial_url, $cd_array, $anonymous, $frontend );
					exit;
				}

				if ( phpgw::get_var('api_mode', 'bool', 'POST') )
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

				$this->login_forward();

			}

			//Build vars :
			$variables = array();

			if(!phpgw::get_var('hide_lightbox', 'bool'))
			{
				$variables['lang_login']	= lang('login');
				$variables['partial_url']	= $partial_url;
				$variables['lang_frontend']	= $frontend ? lang($frontend) : '';
				if (isset($GLOBALS['phpgw_info']['server']['half_remote_user']) && $GLOBALS['phpgw_info']['server']['half_remote_user'] == 'remoteuser')
				{
					$variables['lang_additional_url']	 = lang('use sso login');
					$variables['additional_url']		 = $GLOBALS['phpgw']->link('/' . $phpgw_url_for_sso);
				}
			}

			$uilogin = new phpgw_uilogin($GLOBALS['phpgw_info']['server']['auth_type'] == 'remoteuser' && !isset($GLOBALS['phpgw_remote_user']));
			$uilogin->phpgw_display_login($variables);
		}

		function redirect_after()
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

				$GLOBALS['phpgw']->redirect_link("/", $redirect_data);
			}

			$redirect_arr = explode('.',$redirect);

			if (count($redirect_arr) == 3 && isset($GLOBALS['phpgw_info']['user']['apps'][$redirect_arr[0]]))
			{
				$GLOBALS['phpgw']->redirect_link("/", array('menuaction' => $redirect));
			}
			else
			{
				//failsafe
				$GLOBALS['phpgw']->redirect_link("/home.php");
			}
		}
		
		function login_failed( $partial_url, $cd_array = array(), $anonymous = false , $frontend = '')
		{
			if($anonymous && $frontend)
			{
				$GLOBALS['phpgw_info']['server']['auth_type'] = 'sql';
				$config = createobject('phpgwapi.config', $frontend)->read();

				$login = $config['anonymous_user'];
				$passwd = $config['anonymous_passwd'];
				$_POST['submitit'] = "";
				$domain = phpgw::get_var('domain', 'string', 'GET');
				if (strstr($login, '#') === false && $domain)
				{
					$login .= "#{$domain}";
				}

				$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
				if (!$GLOBALS['sessionid'])
				{
					if ($GLOBALS['phpgw']->session->cd_reason)
					{
						$cd_array['cd']	= $GLOBALS['phpgw']->session->cd_reason;
					}
					$cd_array['skip_remote'] = true;
					$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
					exit;

//					$lang_denied = lang('Anonymous access not correctly configured');
//					if ($GLOBALS['phpgw']->session->reason)
//					{
//						$lang_denied = $GLOBALS['phpgw']->session->reason;
//					}
//					echo <<<HTML
//						<div class="error">$lang_denied</div>
//HTML;
//					$GLOBALS['phpgw']->common->phpgw_exit(True);
				}
				ExecMethod('phpgwapi.menu.clear');
				
				$this->login_forward();				
			}
			else
			{
				$cd_array['lang'] = phpgw::get_var('lang', 'string', 'GET');
				$GLOBALS['phpgw']->redirect_link("/{$partial_url}", $cd_array);
				exit;
			}
			
		}
		
		function login_forward()
		{
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
			$lightbox			 = isset($_REQUEST['lightbox']) && $_REQUEST['lightbox'] ? true : false;

			if ($lightbox)
			{
				$GLOBALS['phpgw']->redirect_link("/login.php", array('hide_lightbox' => true));
			}
			else
			{
				$GLOBALS['phpgw']->hooks->process('login');
				$after = phpgw::get_var('after', 'bool');
				if ($after)
				{
					$this->redirect_after();
				}
				else
				{
					$GLOBALS['phpgw']->redirect_link("/home.php", $extra_vars);
				}
			}
			
		}
	}
