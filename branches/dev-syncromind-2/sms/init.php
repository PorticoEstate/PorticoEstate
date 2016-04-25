<?php
	$path_to_phpgroupware = dirname(__FILE__) . '/..'; // need to be adapted if this script is moved somewhere else
	$_GET['domain'] = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : 'default';

	$GLOBALS['phpgw_info']['flags'] = array(
		'currentapp' => 'login',
		'noapi' => true  // this stops header.inc.php to include phpgwapi/inc/function.inc.php
	);
	include($path_to_phpgroupware . '/header.inc.php');
	unset($GLOBALS['phpgw_info']['flags']['noapi']);
	$db_type = $GLOBALS['phpgw_domain'][$_GET['domain']]['db_type'];
	$GLOBALS['phpgw_info']['server']['sessions_type'] = 'db';
	include(PHPGW_API_INC . '/functions.inc.php');
	$GLOBALS['phpgw_info']['user']['domain'] = $_GET['domain'];

	// more configuration
	$apps_config['multilogin'] = 1; // 0 for single session login; 1 for multi session login
	$GLOBALS['phpgw_info']['sms_config']['common']['apps_config'] = $apps_config;

	include_once($path_to_phpgroupware . '/sms/inc/config.php');
