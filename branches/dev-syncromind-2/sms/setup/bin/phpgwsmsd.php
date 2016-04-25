#!/usr/bin/php -q
<?php
// The path to directory of installed phpgwsms
	$PHPGWSMS_PATH = "/home/sn5607/public_html/phpgroupware/sms";

// DO NOT CHANGE ANYTHING BELOW THE LINE
// ------------------------------------------------------

	$DAEMON_PROCESS = true;
	chdir($PHPGWSMS_PATH);
	if (!function_exists("validatelogin"))
	{
		include_once("init.php");
		$sms = CreateObject('sms.sms');
	}
	$DAEMON_COUNTER = 0;

//while(true)
	while ($DAEMON_COUNTER < 1)
	{
		if (file_exists($PHPGWSMS_PATH))
		{
			$DAEMON_COUNTER++;
			$sms->getsmsinbox();
			$sms->getsmsstatus();
		}
		else
		{
			die("EXIT");
		}
	}
?>
