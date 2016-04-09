<?php
	require_once dirname(__FILE__) . '/entryPoint.php';

	function updateReservationState( PhpgwContext $c )
	{
		CreateObject('booking.async_task_update_reservation_state')->run();
	}
	PhpgwEntry::phpgw_call('updateReservationState');
