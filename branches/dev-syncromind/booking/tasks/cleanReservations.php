<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function cleanReservations( PhpgwContext $c )
	{
		$reservation_tables = array('bb_booking', 'bb_allocation', 'bb_event');
		foreach ($reservation_tables as $table)
		{
			$sql = "TRUNCATE table $table CASCADE";
			echo $sql . "\n";
			$c->getDb()->query($sql, __LINE__, __FILE__);
		}
	}
	PhpgwEntry::phpgw_call('cleanReservations');
