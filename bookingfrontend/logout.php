<?php
	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array
		(
		'disable_template_class' => true,
		'currentapp' => 'logout',
		'noheader' => true,
		'nofooter' => true,
		'nonavbar' => true,
	);
	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';

	if (file_exists('../header.inc.php'))
	{
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}

	$sessionid = phpgw::get_var('bookingfrontendsession');

	$verified = $GLOBALS['phpgw']->session->verify();

	$bookingfrontend_host = '';
	$external_logout = '';
	if ($verified)
	{
		$config = CreateObject('phpgwapi.config', 'bookingfrontend');
		$config->read();

		$bookingfrontend_host = isset($config->config_data['bookingfrontend_host']) && $config->config_data['bookingfrontend_host'] ? $config->config_data['bookingfrontend_host'] : '';
		$bookingfrontend_host = rtrim($bookingfrontend_host, '/');
		$external_logout = isset($config->config_data['external_logout']) && $config->config_data['external_logout'] ? $config->config_data['external_logout'] : '';
//		$external_logout = "https://login-vip.bergen.kommune.no/SSO/logout?p_done_url=";//https://www.bergen.kommune.no"

		$frontend_user = CreateObject('bookingfrontend.bouser');
		$frontend_user->log_off();
		/*
		  // testing external logout


		  $arguments = array('p_done_url' => 'https://www.bergen.kommune.no');
		  $query = http_build_query($arguments);
		  $auth_url = $_integration_config['auth_url'];
		  $request = "https://login-vip.bergen.kommune.no/SSO/logout?{$query}";

		  $aContext = array
		  (
		  'https' => array
		  (
		  'request_fulluri' => true,
		  ),
		  );

		  if(isset($GLOBALS['phpgw_info']['server']['httpproxy_server']))
		  {
		  $aContext['http']['proxy'] = "{$GLOBALS['phpgw_info']['server']['httpproxy_server']}:{$GLOBALS['phpgw_info']['server']['httpproxy_port']}";
		  }

		  $cxContext = stream_context_create($aContext);
		  $response = file_get_contents($request, False, $cxContext);
		 */

		execMethod('phpgwapi.menu.clear');
		$GLOBALS['phpgw']->hooks->process('logout');
		$GLOBALS['phpgw']->session->destroy($sessionid);
	}

	$forward = phpgw::get_var('phpgw_forward', 'int');

	if ($forward)
	{
		$extra_vars['phpgw_forward'] = $forward;
		foreach ($_GET as $name => $value)
		{
			if (preg_match('/phpgw_/', $name))
			{
				$extra_vars[$name] = phpgw::clean_value($value);
			}
		}
	}

	$redirect = phpgw::get_var('redirect_menuaction', 'string');

	if ($redirect)
	{
		$matches = array();
		$extra_vars['menuaction'] = $redirect;
		foreach ($_GET as $name => $value)
		{
			if (preg_match('/^redirect_([\w\_\-]+)/', $name, $matches) && $matches[1] != 'menuaction')
			{
				$extra_vars[$matches[1]] = phpgw::clean_value($value);
			}
		}
	}

	if (!isset($extra_vars['menuaction']))
	{
		$extra_vars['menuaction'] = 'bookingfrontend.uisearch.index';
	}

	if (!$external_logout)
	{
		$GLOBALS['phpgw']->redirect_link('/bookingfrontend/', $extra_vars);
	}
	else
	{
		$result_redirect = '';
		if (substr($external_logout, -1) == '=')
		{
			$external_logout = rtrim($external_logout, '=');
			$result_redirect = $GLOBALS['phpgw']->link('/bookingfrontend/', $extra_vars, true);
		}
		$external_logout_url = "{$external_logout}{$bookingfrontend_host}{$result_redirect}";
		Header("Location: {$external_logout_url}");
	}
	exit;
