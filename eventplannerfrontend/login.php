<?php

	$GLOBALS['phpgw_info']['flags']['session_name'] = 'eventplannerfrontendsession';
	require_once '../phpgwapi/inc/class.login.inc.php';

	$phpgwlogin = new phpgwapi_login;
	$phpgwlogin->login('eventplannerfrontend');
