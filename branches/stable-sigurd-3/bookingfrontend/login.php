<?php
	$phpgw_info = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'   		 		 => true,
		'nonavbar'   				 => true,
	);
	
	if(file_exists('../header.inc.php'))
	{
		include_once('../header.inc.php');
		$GLOBALS['phpgw']->sessions = createObject('phpgwapi.sessions');
	}
	
	$login = "bookingguest";
	$passwd = "bkbooking";
	$_POST['submitit'] = "";
	$GLOBALS['sessionid'] = $GLOBALS['phpgw']->session->create($login, $passwd);
	$GLOBALS['phpgw']->session->appsession('tenant_id','property',$tenant_id);
	
	$GLOBALS['phpgw']->hooks->process('login');

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

	$GLOBALS['phpgw']->hooks->process('login');

	$GLOBALS['phpgw']->redirect_link('/index.php', $extra_vars);
	exit;