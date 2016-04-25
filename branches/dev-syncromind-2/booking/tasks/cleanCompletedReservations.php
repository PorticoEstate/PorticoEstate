<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function cleanCompletedReservations( PhpgwContext $c )
	{
		$reservation_tables = array('bb_completed_reservation');
		foreach ($reservation_tables as $table)
		{
			$sql = "TRUNCATE table $table CASCADE";
			echo $sql . "\n";
			$c->getDb()->query($sql, __LINE__, __FILE__);
		}
	}
	PhpgwEntry::phpgw_call('cleanCompletedReservations');
