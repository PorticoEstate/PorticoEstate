<?php
	/**
	* @license http://www.gnu.org/licenses/gpl.html GNU General Public License
	* @package phpgroupware
	* @version $Id$
	*/
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
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}
	$login = "bookingguest";
	$passwd = "bkbooking";
	$_POST['submitit'] = "";
	if ( (isset($_POST['submitit']) || isset($_POST['submit_x']) || isset($_POST['submit_y']) ) )
	{
		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
		$GLOBALS['phpgw']->session->appsession('tenant_id','property',$tenant_id);

		$forward = phpgw::get_var('phpgw_forward', 'int');

		if($forward)
		{
			$extra_vars['phpgw_forward'] =  $forward;
			foreach($_GET as $name => $value)
			{
				if (preg_match('/phpgw_/',$name))
				{
					$extra_vars[$name] = phpgw::clean_value($value);
				}
			}
		}
		
		$extra_vars['menuaction'] = 'booking.uiorganization.index';

		$GLOBALS['phpgw']->hooks->process('login');

		$GLOBALS['phpgw']->redirect_link('/index.php', $extra_vars);
		exit;
	}

	if( $GLOBALS['phpgw_info']['server']['domain_from_host']
		&& !$GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
	{
		$tmpl->set_var(
				array(
					'domain_selects'	=> '',
					'logindomain'		=> phpgw::get_var('SERVER_NAME', 'string' , 'SERVER')
				)
			);
		$tmpl->parse('domain_from_hosts', 'domain_from_host');
	}
	elseif( $GLOBALS['phpgw_info']['server']['show_domain_selectbox'] )
	{
		foreach($GLOBALS['phpgw_domain'] as $domain_name => $domain_vars)
		{
			$tmpl->set_var('domain_name', $domain_name);

			if (isset($_COOKIE['last_domain']) && $_COOKIE['last_domain'] == $domain_name)
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

	$usertypes = array('tenant'=>lang('tenant'),'internal'=>lang('internal'));
	foreach($usertypes as $usertype_id => $usertype_name)
	{
		$tmpl->set_var('usertype_id', $usertype_id);
		$tmpl->set_var('usertype_name', $usertype_name);

		if (isset($_COOKIE['last_usertype']) && $_COOKIE['last_usertype']==$usertype_id)
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
				'lang_usertype'		=> lang('Usertype')
			)
		);

	if (isset($_COOKIE['last_loginid']))
	{
		$accounts = CreateObject('phpgwapi.accounts');
		$prefs = CreateObject('phpgwapi.preferences', $accounts->name2id(phpgw::get_var('last_loginid', 'string', 'COOKIE')));

		if (! $prefs->account_id)
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
	if ( ($login_msg = lang('loginscreen_message') ) != 'loginscreen_message*')
	{
		$tmpl->set_var('lang_message', stripslashes($login_msg) );
	}
	else
	{
		$tmpl->set_var('lang_message', '&nbsp;');
	}

	if( ( !isset($GLOBALS['phpgw_info']['server']['usecookies']) || !$GLOBALS['phpgw_info']['server']['usecookies'] )
		&& (isset($_COOKIE) && is_array($_COOKIE) ) )
	{
		if ( isset($_COOKIE['last_loginid']) )
		{
			unset($_COOKIE['last_loginid']);
		}

		if ( isset($_COOKIE['last_domain']) )
		{
			unset($_COOKIE['last_domain']);
		}
		if ( isset($_COOKIE['last_usertype']) )
		{
			unset($_COOKIE['last_usertype']);
		}

	}

	$last_loginid = phpgw::get_var('last_loginid', 'string', 'COOKIE');
	if($GLOBALS['phpgw_info']['server']['show_domain_selectbox'] && $last_loginid !== '')
	{
		reset($GLOBALS['phpgw_domain']);
		list($default_domain) = each($GLOBALS['phpgw_domain']);

		if ($_COOKIE['last_domain'] != $default_domain && !empty($_COOKIE['last_domain']))
		{
			$last_loginid .= '@' . phpgw::get_var('last_domain', 'string', 'COOKIE');
		}
	}

	//FIXME switch to an array
	$extra_vars = array();
	foreach($_GET as $name => $value)
	{
		if (preg_match('/phpgw_/',$name))
		{
			$extra_vars[$name] = urlencode(phpgw::clean_value($value));
		}
	}

	$cd = 0;
	if ( isset($_GET['cd']) )
	{
		$cd = (int) $_GET['cd'];
	}

	$tmpl->set_var('login_url', $GLOBALS['phpgw_info']['server']['webserver_url'] . '/property/login.php?' . http_build_query($extra_vars) );
	$tmpl->set_var('registration_url',$GLOBALS['phpgw_info']['server']['webserver_url'] . '/registration/');
	$tmpl->set_var('version', $GLOBALS['phpgw_info']['server']['versions']['phpgwapi']);
	$tmpl->set_var('last_loginid', $last_loginid);

	$tmpl->set_var('lang_username', lang('username'));
	$tmpl->set_var('lang_password', lang('password'));
	$tmpl->set_var('lang_login', lang('login'));

	$tmpl->set_var('lang_testjs', lang('Your browser does not support javascript and/or css, please use a modern standards compliant browser.  If you have disabled either of these features please enable them for this site.') );

	$tmpl->set_var('website_title', isset($GLOBALS['phpgw_info']['server']['site_title'])
						? $GLOBALS['phpgw_info']['server']['site_title']
						: 'phpGroupWare'
						);

	$tmpl->set_var('template_set', $GLOBALS['phpgw_info']['login_template_set']);

	$tmpl->set_var('base_css', $base_css);
	$tmpl->set_var('login_css', $login_css);

	$autocomplete = 'autocomplete="off"';
	$tmpl->set_var('autocomplete', $autocomplete);
	unset($autocomplete);

	$tmpl->pfp('loginout','login_form');

