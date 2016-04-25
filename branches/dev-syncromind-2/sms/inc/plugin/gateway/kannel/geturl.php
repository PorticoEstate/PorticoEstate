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

	$t = $_GET['t'];
	$q = $_GET['q'];
	$a = $_GET['a'];

	if ($t && $q && $a)
	{
		$sms_datetime = trim($t);
		$sms_sender = trim($q);
		$message = trim($a);
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
