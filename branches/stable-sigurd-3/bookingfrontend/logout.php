<?php
	$phpgw_info = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'currentapp'             => 'logout',
		'noheader'   		 	 => true,
		'nofooter'               => true,
		'nonavbar'   			 => true,
	);
	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
	
	if(file_exists('../header.inc.php'))
	{
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}
	
	$sessionid = phpgw::get_var('bookingfrontendsession');

	$verified = $GLOBALS['phpgw']->session->verify();
	
	if ($verified)
	{
		$frontend_user = CreateObject('bookingfrontend.bouser');
		$frontend_user->log_off();

		execMethod('phpgwapi.menu.clear');
		$GLOBALS['phpgw']->hooks->process('logout');
		$GLOBALS['phpgw']->session->destroy($sessionid);
	}
	
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
	
	$redirect = phpgw::get_var('redirect_menuaction', 'string');
	
	if($redirect) {
		$matches = array();
		$extra_vars['menuaction']  = $redirect;
		foreach($_GET as $name => $value) {
			if (preg_match('/^redirect_([\w\_\-]+)/', $name, $matches) && $matches[1] != 'menuaction') {
				$extra_vars[$matches[1]] = phpgw::clean_value($value);
			}
		}
	}
	
	if (!isset($extra_vars['menuaction'])) {
		$extra_vars['menuaction'] = 'bookingfrontend.uisearch.index';
	}

	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/', $extra_vars);
	exit;
