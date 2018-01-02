<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function setBookingAppVersion( PhpgwContext $c )
	{
		$options = array('version' => null);

		$options['version'] = (isset($_ENV['VERSION']) && strlen($_ENV['VERSION'] > 0)) ? $_ENV['VERSION'] : null;

		if (!$options['version'])
		{
			throw new InvalidArgumentException('Missing VERSION');
		}

		$sql = sprintf(
			"UPDATE phpgw_applications SET app_version = '%s' WHERE app_name = 'booking'", $options['version']
		);
		echo $sql . "\n";
		$c->getDb()->query($sql, __LINE__, __FILE__);
	}
	PhpgwEntry::phpgw_call('setBookingAppVersion');
