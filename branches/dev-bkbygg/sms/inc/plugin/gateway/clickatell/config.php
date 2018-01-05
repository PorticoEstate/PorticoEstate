<?php
	if (!defined("_SECURE_"))
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};

	$db_query = "SELECT * FROM phpgw_sms_gwmodclickatell_config";
	$db_result = dba_query($db_query);
	if ($db_row = dba_fetch_array($db_result))
	{
		$clktl_param[name] = $db_row[cfg_name];
		$clktl_param[api_id] = $db_row[cfg_api_id];
		$clktl_param[username] = $db_row[cfg_username];
		$clktl_param[password] = $db_row[cfg_password];
		$clktl_param[sender] = $db_row[cfg_sender];
		$clktl_param[send_url] = $db_row[cfg_send_url];
		$clktl_param[incoming_path] = $db_row[cfg_incoming_path];
	}

	$gateway_number = $clktl_param['sender'];
