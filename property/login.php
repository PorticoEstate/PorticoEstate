<?php
	/**
	* phpGroupWare
	*
	* phpgroupware base
	* @author Dan Kuykendall <seek3r@phpgroupware.org>
	* @author Joseph Engo <jengo@phpgroupware.org>
	* @copyright Copyright (C) 2000,2001,2002,2003,2004,2005,2006,2007 Free Software Foundation, Inc. http://www.fsf.org/
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
	* @package phpgroupware
	 * @version $Id$
	*/
	// FIXME this should be changed significantly - it duplicates a lot of login code, most of which has been improved

	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
	);
	if(file_exists('../header.inc.php'))
	{

		/**
		* Include phpgroupware header
		*/
		include_once('../header.inc.php');

		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}
	else
	{
		Header('Location: ../setup/index.php');
		exit;
	}
	$GLOBALS['phpgw_info']['server']['template_set'] = $GLOBALS['phpgw_info']['login_template_set'];

	$GLOBALS['phpgw_info']['server']['template_dir'] = PHPGW_SERVER_ROOT
							. "/phpgwapi/templates/{$GLOBALS['phpgw_info']['login_template_set']}";

	$tmpl = CreateObject('phpgwapi.template', PHPGW_SERVER_ROOT . '/property/templates/base');

	// This is used for system downtime, to prevent new logins.
	if(isset($GLOBALS['phpgw_info']['server']['deny_all_logins']) && $GLOBALS['phpgw_info']['server']['deny_all_logins'])
	{
		$tmpl->set_root($GLOBALS['phpgw_info']['server']['template_dir']);
		$tmpl->set_file(
		array(
				'login_form'  => 'login_denylogin.tpl'
			)
		);
		$tmpl->pfp('loginout', 'login_form');
		exit;
	}

	/**
	* Check logout error code
	*
	* @param integer $code Error code
	* @return string Error message
	*/
	function check_logoutcode($code)
	{
		switch($code)
		{
				case 1:
					return lang('You have been successfully logged out');
				case 2:
					return lang('Sorry, your login has expired');
				case 5:
					return lang('Bad login or password');
				case 20:
					return lang('Cannot find the mapping ! (please advice your adminstrator)');
				case 21:
					return lang('you had inactive mapping to %1 account', phpgw::get_var('phpgw_account', 'string', 'GET', ''));
				case 22:
					$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
					$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
					$GLOBALS['phpgw']->session->phpgw_setcookie('domain');
					return lang('you seemed to have an active session elsewhere for the domain "%1", now set to expired - please try again', phpgw::get_var('domain', 'string', 'COOKIE'));
				case 99:
					return lang('Blocked, too many attempts');
				case 10:
					$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
					$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
					$GLOBALS['phpgw']->session->phpgw_setcookie('domain');

					// fix for bug php4 expired sessions bug
					if($GLOBALS['phpgw_info']['server']['sessions_type'] == 'php')
					{
						$GLOBALS['phpgw']->session->phpgw_setcookie('phpgwsessid');
					}

					return lang('Your session could not be verified.');
				default:
					return '&nbsp;';
		}
	}

	/**
	* Check languages
	*/
	function check_langs()
	{
		// echo "<h1>check_langs()</h1>\n";
		if(isset($GLOBALS['phpgw_info']['server']['lang_ctimes']) && !is_array($GLOBALS['phpgw_info']['server']['lang_ctimes']))
		{
			$GLOBALS['phpgw_info']['server']['lang_ctimes'] = unserialize($GLOBALS['phpgw_info']['server']['lang_ctimes']);
		}
		else if(!isset($GLOBALS['phpgw_info']['server']['lang_ctimes']))
		{
			$GLOBALS['phpgw_info']['server']['lang_ctimes'] = array();
		}
		// _debug_array($GLOBALS['phpgw_info']['server']['lang_ctimes']);

		$lang = $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
		$apps = $GLOBALS['phpgw_info']['user']['apps'];
		$apps['phpgwapi'] = true;	// check the api too
		//while(list($app, $data) = each($apps))
                foreach($apps as $app => $data)
		{
			$fname = PHPGW_SERVER_ROOT . "/$app/setup/phpgw_$lang.lang";

			if(file_exists($fname))
			{
				$ctime = filectime($fname);
				$ltime = isset($GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app]) ? intval($GLOBALS['phpgw_info']['server']['lang_ctimes'][$lang][$app]) : 0;
				//echo "checking lang='$lang', app='$app', ctime='$ctime', ltime='$ltime'<br>\n";

				if($ctime != $ltime)
				{
					update_langs();		// update all langs
					break;
				}
			}
		}
	}

	/**
	* Update languages
	*/
	function update_langs()
	{
		$GLOBALS['phpgw_setup'] = CreateObject('phpgwapi.setup');
		$GLOBALS['phpgw_setup']->db = $GLOBALS['phpgw']->db;

		$GLOBALS['phpgw_setup']->detection->check_lang(false);	// get installed langs
		$langs = $GLOBALS['phpgw_info']['setup']['installed_langs'];
		//while(list($lang) = @each($langs))
                if (is_array($langs))
                {
                    foreach($langs as $lang => $value)
		{
			$langs[$lang] = $lang;
		}
                }
		$_POST['submit'] = true;
		$_POST['lang_selected'] = $langs;
		$_POST['upgrademethod'] = 'dumpold';
		$included = 'from_login';

		/**
		* Include languages setup
		*/
		include(PHPGW_SERVER_ROOT . '/setup/lang.php');
	}
	/* Program starts here */
	$GLOBALS['phpgw']->session->phpgw_setcookie(session_name());
//	$GLOBALS['phpgw']->session->phpgw_setcookie('sessionid');
	$GLOBALS['phpgw']->session->phpgw_setcookie('kp3');
	$GLOBALS['phpgw']->session->phpgw_setcookie('domain');

	$login = phpgw::get_var('login', 'string', 'POST');
	$passwd = phpgw::get_var('passwd', 'string', 'POST');

	if($GLOBALS['phpgw_info']['server']['auth_type'] == 'http' && isset($_SERVER['PHP_AUTH_USER']))
	{
		$submit = true;
		$login = phpgw::get_var('PHP_AUTH_USER', 'string', 'SERVER');
		$passwd = phpgw::get_var('PHP_AUTH_PW', 'string', 'SERVER');
	}

	if($GLOBALS['phpgw_info']['server']['auth_type'] == 'ntlm' && isset($_SERVER['REMOTE_USER']))
	{
		$submit = true;
		$login = phpgw::get_var('REMOTE_USER', 'string', 'SERVER');

		$passwd = '';
	}

	# Apache + mod_ssl style SSL certificate authentication
	# Certificate (chain) verification occurs inside mod_ssl
	if($GLOBALS['phpgw_info']['server']['auth_type'] == 'sqlssl' && isset($_SERVER['SSL_CLIENT_S_DN']) && !isset($_GET['cd']))
	{
		# an X.509 subject looks like:
		# /CN=john.doe/OU=Department/O=Company/C=xx/Email=john@comapy.tld/L=City/
		# the username is deliberately lowercase, to ease LDAP integration
		$sslattribs = phpgw::get_var('SSL_CLIENT_S_DN', 'string', 'SERVER');
		$sslattribs = explode('/', $sslattribs);
		# skip the part in front of the first '/' (nothing)
		while($sslattrib = next($sslattribs))
		{
			list($key, $val) = explode('=', $sslattrib);
			$sslattributes[$key] = $val;
		}

		if(isset($sslattributes['Email']))
		{
			$submit = true;

			# login will be set here if the user logged out and uses a different username with
			# the same SSL-certificate.
			if(!isset($_POST['login']) && isset($sslattributes['Email']))
			{
				$login = $sslattributes['Email'];
				# not checked against the database, but delivered to authentication module
				$passwd = phpgw::get_var('SSL_CLIENT_S_DN', 'string', 'SERVER');
			}
		}
		unset($key);
		unset($val);
		unset($sslattributes);
	}

	if((isset($_POST['submitit']) || isset($_POST['submit_x']) || isset($_POST['submit_y'])))
	{
		if($_SERVER['REQUEST_METHOD'] != 'POST' && !isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['REMOTE_USER']) && !isset($_SERVER['SSL_CLIENT_S_DN'])
		  )
		{
			$GLOBALS['phpgw']->redirect('login.php', array('cd' => '5'));
		}

// start mapping
		if(isset($_POST['loginusertype']) && $_POST['loginusertype'] == 'tenant')
		{
			$db = & $GLOBALS['phpgw']->db;
			$join = $db->join;

			$_passwd = md5($passwd);

			$db->query("SELECT fm_tenant.id, phpgw_accounts.account_lid,phpgw_accounts.account_pwd"
				. " FROM fm_tenant {$join} phpgw_accounts ON fm_tenant.phpgw_account_id = phpgw_accounts.account_id"
				. " WHERE phpgw_accounts.account_status = 'A' AND"
				. " fm_tenant.account_lid = '{$login}' AND"
				. " fm_tenant.account_pwd='{$_passwd}' AND"
			. " fm_tenant.account_status =1", __LINE__, __FILE__);
			$db->next_record();

			if(!$db->f('account_lid'))
			{
				$GLOBALS['phpgw']->redirect('login.php?cd=5');
				exit;
			}

			$tenant_id = $db->f('id');
			$login = $db->f('account_lid');
			$passwd = $db->f('account_pwd');
//_debug_array($passwd);die();
			if(isset($GLOBALS['phpgw_info']['server']['usecookies']) && $GLOBALS['phpgw_info']['server']['usecookies'])
			{
				$GLOBALS['phpgw']->session->phpgw_setcookie('last_usertype', phpgw::get_var('loginusertype'), time() + 1209600); /* For 2 weeks */
			}
		}
// end mapping

		if(strstr($login, '@') === false && isset($_POST['logindomain']))
		{
			$login .= '@' . phpgw::get_var('logindomain', 'string', 'POST');
		}

		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, '', true);

		$GLOBALS['phpgw']->session->appsession('tenant_id', 'property', $tenant_id);


		if(!isset($GLOBALS['sessionid']) || !$GLOBALS['sessionid'])
		{
			$GLOBALS['phpgw']->redirect('login.php?cd=' . $GLOBALS['phpgw']->session->cd_reason);
			exit;
		}

		$forward = phpgw::get_var('phpgw_forward', 'int');
		if($forward)
		{
			$extra_vars['phpgw_forward'] =  $forward;
			foreach($_GET as $name => $value)
			{
				if(preg_match('/phpgw_/', $name))
				{
					$extra_vars[$name] = phpgw::clean_value($value);
				}
			}
		}
		if(!isset($GLOBALS['phpgw_info']['server']['disable_autoload_langfiles']) || !$GLOBALS['phpgw_info']['server']['disable_autoload_langfiles'])
		{
			check_langs();
		}
		$extra_vars['cd'] = 'yes';

		$GLOBALS['phpgw']->hooks->process('login');

		$GLOBALS['phpgw']->redirect_link('/home.php', $extra_vars);
		exit;
	}

	$tmpl->set_file(array('login_form'  => 'login.tpl'));
	$tmpl->set_var('charset', lang('charset'));
	$tmpl->set_block('login_form', 'domain_option', 'domain_options');
	$tmpl->set_block('login_form', 'domain_select', 'domain_selects');
	$tmpl->set_block('login_form', 'domain_from_host', 'domain_from_hosts');
	$tmpl->set_block('login_form', 'usertype_option', 'usertype_options');
	$tmpl->set_block('login_form', 'usertype_select', 'usertype_selects');

	if($GLOBALS['phpgw_info']['server']['domain_from_host'] && !$GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
	{
		$tmpl->set_var(
				array(
					'domain_selects'	=> '',
			'logindomain' => phpgw::get_var('SERVER_NAME', 'string', 'SERVER')
				)
			);
		$tmpl->parse('domain_from_hosts', 'domain_from_host');
	}
	elseif($GLOBALS['phpgw_info']['server']['show_domain_selectbox'])
	{
		foreach($GLOBALS['phpgw_domain'] as $domain_name => $domain_vars)
		{
			$tmpl->set_var('domain_name', $domain_name);

			if(isset($_COOKIE['last_domain']) && $_COOKIE['last_domain'] == $domain_name)
			{
				$tmpl->set_var('domain_selected', 'selected="selected"');
			}
			else
			{
				$tmpl->set_var('domain_selected', '');
			}
			$tmpl->parse('domain_options', 'domain_option', true);
		}
		$tmpl->parse('domain_selects', 'domain_select');
		$tmpl->set_var(
				array(
					'domain_from_hosts'	=> '',
					'lang_domain'		=> lang('domain')
				)
			);
	}
	else
	{
		$tmpl->set_var(
				array(
					'domain_selects'		=> '',
					'domain_from_hosts'	=> ''
				)
			);
	}

	$usertypes = array('tenant' => lang('tenant'), 'internal' => lang('internal'));
	foreach($usertypes as $usertype_id => $usertype_name)
	{
		$tmpl->set_var('usertype_id', $usertype_id);
		$tmpl->set_var('usertype_name', $usertype_name);

		if(isset($_COOKIE['last_usertype']) && $_COOKIE['last_usertype'] == $usertype_id)
		{
			$tmpl->set_var('usertype_selected', 'selected="selected"');
		}
		else
		{
			$tmpl->set_var('usertype_selected', '');
		}
		$tmpl->parse('usertype_options', 'usertype_option', true);
	}
	$tmpl->parse('usertype_selects', 'usertype_select');
	$tmpl->set_var(
			array(
				'usertype_from_hosts'	=> '',
				'lang_usertype'		=> lang('usertype')
			)
		);

	if(isset($_COOKIE['last_loginid']))
	{
		$accounts = CreateObject('phpgwapi.accounts');
		$prefs = CreateObject('phpgwapi.preferences', $accounts->name2id(phpgw::get_var('last_loginid', 'string', 'COOKIE')));

		if(!$prefs->account_id)
		{
			$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = 'en';
		}
		else
		{
			$GLOBALS['phpgw_info']['user']['preferences'] = $prefs->read();
		}
		#print 'LANG:' . $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] . '<br>';
	}
	else
	{
		// If the lastloginid cookies isn't set, we will default to english.
		// Change this if you need.
		$GLOBALS['phpgw_info']['user']['preferences']['common']['lang'] = 'en';
	}
	$GLOBALS['phpgw']->translation->add_app('login');
	$GLOBALS['phpgw']->translation->add_app('loginscreen');
	if(($login_msg = lang('loginscreen_message') ) != 'loginscreen_message*')
	{
		$tmpl->set_var('lang_message', stripslashes($login_msg));
	}
	else
	{
		$tmpl->set_var('lang_message', '&nbsp;');
	}

	if((!isset($GLOBALS['phpgw_info']['server']['usecookies']) || !$GLOBALS['phpgw_info']['server']['usecookies'] ) && (isset($_COOKIE) && is_array($_COOKIE) ))
	{
		if(isset($_COOKIE['last_loginid']))
		{
			unset($_COOKIE['last_loginid']);
		}

		if(isset($_COOKIE['last_domain']))
		{
			unset($_COOKIE['last_domain']);
		}
		if(isset($_COOKIE['last_usertype']))
		{
			unset($_COOKIE['last_usertype']);
		}
	}

	$last_loginid = phpgw::get_var('last_loginid', 'string', 'COOKIE');
	if($GLOBALS['phpgw_info']['server']['show_domain_selectbox'] && $last_loginid !== '')
	{
		reset($GLOBALS['phpgw_domain']);
		//list($default_domain) = each($GLOBALS['phpgw_domain']);
		$default_domain = key($GLOBALS['phpgw_domain']);

		if($_COOKIE['last_domain'] != $default_domain && !empty($_COOKIE['last_domain']))
		{
			$last_loginid .= '@' . phpgw::get_var('last_domain', 'string', 'COOKIE');
		}
	}

	//FIXME switch to an array
	$extra_vars = array();
	foreach($_GET as $name => $value)
	{
		if(preg_match('/phpgw_/', $name))
		{
			$extra_vars[$name] = urlencode(phpgw::clean_value($value));
		}
	}

	$cd = 0;
	if(isset($_GET['cd']))
	{
		$cd = (int)$_GET['cd'];
	}

	$tmpl->set_var('login_url', $GLOBALS['phpgw_info']['server']['webserver_url'] . '/property/login.php?' . http_build_query($extra_vars));
	$tmpl->set_var('registration_url', $GLOBALS['phpgw_info']['server']['webserver_url'] . '/registration/');
	$tmpl->set_var('version', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
	$tmpl->set_var('cd', check_logoutcode($cd));
	$tmpl->set_var('last_loginid', $last_loginid);

	$tmpl->set_var('lang_username', lang('username'));
	$tmpl->set_var('lang_password', lang('password'));
	$tmpl->set_var('lang_login', lang('login'));

	$tmpl->set_var('lang_testjs', lang('Your browser does not support javascript and/or css, please use a modern standards compliant browser.  If you have disabled either of these features please enable them for this site.'));

	$tmpl->set_var('website_title', isset($GLOBALS['phpgw_info']['server']['site_title']) ? $GLOBALS['phpgw_info']['server']['site_title'] : 'phpGroupWare'
						);

	$tmpl->set_var('template_set', $GLOBALS['phpgw_info']['login_template_set']);

	// This really should just use the API CSS, which would fix any conflicts
	if(is_file(PHPGW_SERVER_ROOT . "/property/templates/{$GLOBALS['phpgw_info']['login_template_set']}/css/base.css"))
	{
		$base_css = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/property/templates/{$GLOBALS['phpgw_info']['login_template_set']}/css/base.css";
	}
	else
	{
		$base_css = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/property/templates/base/css/base.css";
	}

	// ditto
	if(is_file(PHPGW_SERVER_ROOT . "/property/templates/{$GLOBALS['phpgw_info']['login_template_set']}/css/login.css"))
	{
		$login_css = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/property/templates/{$GLOBALS['phpgw_info']['login_template_set']}/css/login.css";
	}
	else
	{
		$login_css = "{$GLOBALS['phpgw_info']['server']['webserver_url']}/property/templates/base/css/login.css";
	}

	$tmpl->set_var('base_css', $base_css);
	$tmpl->set_var('login_css', $login_css);

	$autocomplete = '';
	if(isset($GLOBALS['phpgw_info']['server']['autocomplete_login']) && $GLOBALS['phpgw_info']['server']['autocomplete_login'])
	{
		$autocomplete = 'autocomplete="off"';
	}
	$tmpl->set_var('autocomplete', $autocomplete);
	unset($autocomplete);

	$tmpl->pfp('loginout', 'login_form');

