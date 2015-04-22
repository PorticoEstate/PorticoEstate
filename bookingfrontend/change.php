<?php
	$phpgw_info = array();
	
	$GLOBALS['phpgw_info']['flags'] = array
	(
		'disable_template_class' => true,
		'login'                  => true,
		'currentapp'             => 'login',
		'noheader'               => true
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
	
	$GLOBALS['phpgw']->hooks->process('login');
	
	$bouser = CreateObject('bookingfrontend.bouser');
	$bouser->change_org(phpgw::get_var('orgnumber', 'string'));

	$after = str_replace('&amp;', '&', urldecode(phpgw::get_var('after', 'string')));
	if(!$after)
	{
		$after = array('menuaction' => 'bookingfrontend.uisearch.index');
	}
	$GLOBALS['phpgw']->redirect_link('/bookingfrontend/index.php', $after);
	exit;
