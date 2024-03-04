<?php
	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_template_class' => true,
		'login'					 => true,
		'currentapp'			 => 'login',
		'noheader'				 => true,
		'nonavbar'				 => true,
	);

	$GLOBALS['phpgw_info']['flags']['session_name'] = 'bookingfrontendsession';
	include_once('../header.inc.php');

	if (!phpgw::get_var(session_name(), 'string', 'COOKIE') || !$GLOBALS['phpgw']->session->verify())
	{
		echo 'No valid session detected';
		$GLOBALS['phpgw']->common->phpgw_exit();
	}

	$selected_lang = phpgw::get_var('selected_lang', 'string', 'COOKIE');


	if (phpgw::get_var('lang', 'bool', 'GET'))
	{
		$selected_lang = phpgw::get_var('lang', 'string', 'GET');
		$GLOBALS['phpgw']->session->phpgw_setcookie('selected_lang', $selected_lang, (time() + (60 * 60 * 24 * 14)));
	}

	$userlang  = $selected_lang ? $selected_lang : $GLOBALS['phpgw_info']['user']['preferences']['common']['lang'];
	$return_data = phpgwapi_cache::system_get('phpgwapi', "lang_{$userlang}", true);

	header('Content-Type: application/json');
	echo json_encode($return_data);
	$GLOBALS['phpgw']->common->phpgw_exit();
