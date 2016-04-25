<?php
	chdir("../../../");
	include "init.php";
	include "$apps_path[libs]/function.php";
	chdir("plugin/gateway/clickatell/");

	$cb_from = $_GET[from];
	$cb_to = $_GET[to];
	$cb_timestamp = $_GET[timestamp];
	$cb_text = $_GET[text];
	$cb_status = $_GET[status];
	$cb_charge = $_GET[charge];
	$cb_apimsgid = $_GET[apiMsgId];

	/*
	  $fc = "from: $cb_from - to: $cb_to - timestamp: $cb_timestamp - text: $cb_text - status: $cb_status - charge: $cb_charge - apimsgid: $cb_apimsgid\n";
	  $fn = "/tmp/clktl_callback";
	  umask(0);
	  $fd = fopen($fn,"a+");
	  fputs($fd,$fc);
	  fclose($fd);
	  die();
	 */

	if ($cb_timestamp && $cb_from && $cb_text)
	{
		$cb_datetime = date($datetime_format, $cb_timestamp);
		$sms_datetime = trim($cb_datetime);
		$sms_sender = trim($cb_from);
		$message = trim($cb_text);
		$array_target_code = explode(" ", $message);
		$target_code = strtoupper(trim($array_target_code[0]));
		$message = $array_target_code[1];
		for ($i = 2; $i < count($array_target_code); $i++)
		{
			$message .= " " . $array_target_code[$i];
		}
		// collected:
		// $sms_datetime, $sms_sender, $target_code, $message
		setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message);
	}

	if ($cb_status && $cb_apimsgid)
	{
		$db_query = "
	SELECT phpgw_sms_tblSMSOutgoing.smslog_id AS smslog_id,phpgw_sms_tblSMSOutgoing.uid AS uid 
	FROM phpgw_sms_tblSMSOutgoing,phpgw_sms_gwmodclickatell_apidata
	WHERE 
	    phpgw_sms_tblSMSOutgoing.smslog_id=phpgw_sms_gwmodclickatell_apidata.smslog_id AND 
	    phpgw_sms_gwmodclickatell_apidata.apimsgid='$cb_apimsgid'
    ";
		$db_result = dba_query($db_query);
		$db_row = dba_fetch_array($db_result);
		$uid = $db_row[uid];
		$smslog_id = $db_row[smslog_id];
		if ($uid && $smslog_id)
		{
			$c_sms_status = 0;
			switch ($cb_status)
			{
				case "001":
				case "002":
				case "011": $c_sms_status = 0;
					break; // pending
				case "003":
				case "008": $c_sms_status = 1;
					break; // sent
				case "005":
				case "006":
				case "007":
				case "009":
				case "010":
				case "012": $c_sms_status = 2;
					break; // failed
				case "004": $c_sms_status = 3;
					break; // delivered
			}
			$c_sms_credit = ceil($cb_charge);
			// pending
			$p_status = 0;
			if ($c_sms_status)
			{
				$p_status = $c_sms_status;
			}
			setsmsdeliverystatus($smslog_id, $uid, $p_status);
		}
	}
