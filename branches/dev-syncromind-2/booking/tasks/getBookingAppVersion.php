<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function getBookingAppVersion( PhpgwContext $c )
	{
		$sql = "SELECT app_version FROM phpgw_applications WHERE app_name = 'booking' LIMIT 1";
		echo $sql . "\n";
		$c->getDb()->query($sql, __LINE__, __FILE__);
		$c->getDb()->next_record();
		echo 'Current Version: ' . $c->getDb()->f('app_version') . "\n";
	}
	PhpgwEntry::phpgw_call('getBookingAppVersion');
