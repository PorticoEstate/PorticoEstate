<?php
	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array
		(
		'disable_template_class' => true,
		'login' => true,
		'currentapp' => 'login',
		'noheader' => true
	);

	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';

	if (file_exists('../header.inc.php'))
	{
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}

	$config = createobject('phpgwapi.config', 'bookingfrontend')->read();

	if(!empty($config['debug_local_login']))
	{
		echo "<H1>Testing pågår - prøv igjen litt senere</H1>";

	}

	if (!phpgw::get_var(session_name()) || !$GLOBALS['phpgw']->session->verify())
	{

		if(!empty($config['debug_local_login']))
		{
			echo "<p>Sesjonen finnes ikke - logger på</p>";
		}

		$login = $config['anonymous_user'];
		$passwd = $config['anonymous_passwd'];
		$_POST['submitit'] = "";
		$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

		$GLOBALS['phpgw']->hooks->process('login');
	}


	/**
	 * Pick up the external login-info
	 */
	$bouser = CreateObject('bookingfrontend.bouser');
	$bouser->log_in();

	$redirect =	json_decode(phpgwapi_cache::session_get('bookingfrontend', 'redirect'), true);


	if(!empty($config['debug_local_login']))
	{
		echo "<p>redirect:</p>";

		_debug_array($redirect);
		die();
	}

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
			$redirect_data['sessionid'] = $sessid;
			$redirect_data['kp3'] = phpgw::get_var('kp3', 'string', 'GET');
		}

		phpgwapi_cache::session_clear('bookingfrontend', 'redirect');
		$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $redirect_data);
	}

	$after = str_replace('&amp;', '&', urldecode(phpgw::get_var('after', 'string')));
	if (!$after)
	{
		$after = array('menuaction' => 'bookingfrontend.uisearch.index');
	}
	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $after);
	exit;
