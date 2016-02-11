<?php
	if (!defined("_SECURE_"))
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};

	$db_query = "SELECT * FROM phpgw_sms_gwmodkannel_config";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result))
	{
		$kannel_param['name'] = $db_row['cfg_name'];
		$kannel_param['path'] = $db_row['cfg_incoming_path'];
		$kannel_param['username'] = $db_row['cfg_username'];
		$kannel_param['password'] = $db_row['cfg_password'];
		$kannel_param['global_sender'] = $db_row['cfg_global_sender'];
		$kannel_param['bearerbox_host'] = $db_row['cfg_bearerbox_host'];
		$kannel_param['sendsms_port'] = $db_row['cfg_sendsms_port'];
		$kannel_param['phpgwsms_web'] = $db_row['cfg_phpgwsms_web'];
	}

	$gateway_number = $kannel_param['global_sender'];
