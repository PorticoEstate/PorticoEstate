<?php
	if (!defined("_SECURE_"))
	{
		die("Intruder: IP " . $_SERVER['REMOTE_ADDR']);
	};

	include "$apps_path[plug]/gateway/$gateway_module/config.php";

	function gw_customcmd()
	{
		// nothing
	}

	function gw_send_sms( $mobile_sender, $sms_sender, $sms_to, $sms_msg, $gp_code = "", $uid = "", $smslog_id = "", $flash = false )
	{
		global $kannel_param;
		global $gateway_number;
		$ok = false;
		if ($gateway_number)
		{
			$sms_from = $gateway_number;
		}
		else
		{
			$sms_from = $mobile_sender;
		}
		if ($sms_sender)
		{
			$sms_msg = $sms_msg . $sms_sender;
		}
		// set failed first
		$p_status = 2;
		setsmsdeliverystatus($smslog_id, $uid, $p_status);
		$sms_type = 2; // text
		if ($flash)
		{
			$sms_type = 1; //flash
		}
		$URL = "/cgi-bin/sendsms?username=" . urlencode($kannel_param['username']) . "&password=" . urlencode($kannel_param['password']);
		$URL .= "&from=" . urlencode($sms_from) . "&to=" . urlencode($sms_to) . "&text=" . urlencode($sms_msg);
		$URL .= "&dlr-mask=31&dlr-url=" . urlencode($kannel_param['phpgwsms_web'] . "/plugin/gateway/kannel/dlr.php?type=%d&slid=$smslog_id&uid=$uid");
		$URL .= "&mclass=$sms_type";
		$connection = fsockopen($kannel_param['bearerbox_host'], $kannel_param['sendsms_port'], $error_number, $error_description, 60);
		if ($connection)
		{
			socket_set_blocking($connection, false);
			fputs($connection, "GET $URL HTTP/1.0\r\n\r\n");
			while (!feof($connection))
			{
				$myline = fgets($connection, 128);
				if ($myline == "Sent.")
				{
					$ok = true;
					// set pending
					$p_status = 0;
					setsmsdeliverystatus($smslog_id, $uid, $p_status);
				}
			}
		}
		fclose($connection);
		return $ok;
	}

	function gw_set_delivery_status( $gp_code = "", $uid = "", $smslog_id = "", $p_datetime = "", $p_update = "" )
	{
		global $kannel_param;
		// not used, depend on kannel delivery status updater
	}

	function gw_set_incoming_action()
	{
		global $kannel_param;
		$handle = @opendir($kannel_param['path'] . "/cache/smsd");
		while ($sms_in_file = @readdir($handle))
		{
			if (preg_match("/^ERR.in/i", $sms_in_file) && !preg_match("/^[.]/", $sms_in_file))
			{
				$fn = $kannel_param['path'] . "/cache/smsd/$sms_in_file";
				$tobe_deleted = $fn;
				$lines = @file($fn);
				$sms_datetime = urldecode(trim($lines[0]));
				$sms_sender = urldecode(trim($lines[1]));
				$message = "";
				for ($lc = 2; $lc < count($lines); $lc++)
				{
					$message .= trim($lines[$lc]);
				}
				$array_target_code = explode(" ", urldecode($message));
				$target_code = strtoupper(trim($array_target_code[0]));
				$message = $array_target_code[1];
				for ($i = 2; $i < count($array_target_code); $i++)
				{
					$message .= " " . $array_target_code[$i];
				}
				// collected:
				// $sms_datetime, $sms_sender, $target_code, $message
				if (setsmsincomingaction($sms_datetime, $sms_sender, $target_code, $message))
				{
					@unlink($tobe_deleted);
				}
			}
		}
	}
