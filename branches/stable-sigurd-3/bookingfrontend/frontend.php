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
	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';

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

	$continue = trim(phpgw::get_var('continue', 'string', ''));
	
	if (!empty($continue))
	{
		header('Location: '. urldecode($continue));
		exit;
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
	
	$extra_vars['menuaction'] = 'bookingfrontend.uisearch.index';

	$GLOBALS['phpgw']->hooks->process('login');

	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/', $extra_vars);
	exit;
