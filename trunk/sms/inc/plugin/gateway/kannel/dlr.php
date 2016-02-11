<?php
	chdir("../../../");
	include "init.php";
	include "$apps_path[libs]/function.php";
	chdir("plugin/gateway/kannel");

	$remote_addr = $_SERVER["REMOTE_ADDR"];
	if ($remote_addr != $kannel_param['bearerbox_host'])
	{
		die();
	}

	$type = $_GET['type'];
	$slid = $_GET['slid'];
	$uid = $_GET['uid'];

	if ($type && $slid && $uid)
	{
		$stat = 0;
		switch ($type)
		{
			case 1: $stat = 6;
				break; // delivered to phone
			case 2: $stat = 5;
				break; // non delivered to phone
			case 4: $stat = 3;
				break; // queued on SMSC
			case 8: $stat = 4;
				break; // delivered to SMSC
			case 16: $stat = 5;
				break; // non delivered to SMSC
			case 9: $stat = 4;
				break;
			case 12: $stat = 4;
				break;
			case 18: $stat = 5;
				break;
		}
		$p_status = $stat;
		if ($stat)
		{
			$p_status = $stat - 3;
		}
		setsmsdeliverystatus($slid, $uid, $p_status);
		// log dlr
		$db_query = "SELECT kannel_dlr_id FROM phpgw_sms_gwmodkannel_dlr WHERE smslog_id='$slid'";
		$db_result = dba_num_rows($db_query);
		if ($db_result > 0)
		{
			$db_query = "UPDATE phpgw_sms_gwmodkannel_dlr SET kannel_dlr_type='$type' WHERE smslog_id='$slid'";
			$db_result = dba_query($db_query);
		}
		else
		{
			$db_query = "INSERT INTO phpgw_sms_gwmodkannel_dlr (smslog_id,kannel_dlr_type) VALUES ('$slid','$type')";
			$db_result = dba_query($db_query);
		}
	}
