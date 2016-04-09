<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function sendReminder( PhpgwContext $c )
	{
		CreateObject('booking.async_task_send_reminder')->run();
	}
	PhpgwEntry::phpgw_call('sendReminder');
