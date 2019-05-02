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

//	$login = "bookingguest";
	$c = createobject('phpgwapi.config', 'bookingfrontend');
	$c->read();
	$config = $c->config_data;
	$login = $c->config_data['anonymous_user'];
	$passwd = $c->config_data['anonymous_passwd'];
	$_POST['submitit'] = "";
	$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);

	$GLOBALS['phpgw']->hooks->process('login');

	$bouser = CreateObject('bookingfrontend.bouser');
	$bouser->log_in();

	$redirect = json_decode(phpgw::get_var('redirect', 'raw', 'COOKIE'), true);

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

		$GLOBALS['phpgw']->session->phpgw_setcookie('redirect', false, 0);
		$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $redirect_data);
		unset($redirect);
		unset($redirect_data);
		unset($sessid);
	}

	$after = str_replace('&amp;', '&', urldecode(phpgw::get_var('after', 'string')));
	if (!$after)
	{
		$after = array('menuaction' => 'bookingfrontend.uisearch.index');
	}
	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $after);
	exit;
