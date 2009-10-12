<?php
	require 'head_pre.php';

	// add a large object
	$ipc->adddata(
		str_repeat('0123456789', ceil($_SERVER['argv'][1]/10)), 'text/plain');
?>

It is safe to igore above errors.
