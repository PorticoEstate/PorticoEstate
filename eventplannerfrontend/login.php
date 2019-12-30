<?php

	require_once '../phpgwapi/inc/class.login.inc.php';
	$GLOBALS['phpgw_info']['flags'] = array(
		'custom_frontend' => 'eventplannerfrontend',
		'session_name' => 'eventplannerfrontendsession'
	);

	$phpgwlogin = new phpgwapi_login;
	$phpgwlogin->login('eventplannerfrontend');
