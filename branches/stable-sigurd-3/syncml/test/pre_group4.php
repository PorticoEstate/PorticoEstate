<?php
	require 'head_pre.php';

	// replace item (just set the dirty flag on the item)
	$somappings->update_mapping(NULL, $_SERVER['argv'][1], NULL, 1);

	// delete item

	$mappings_to_be_deleted = $somappings->get_mapping(
		NULL, $_SERVER['argv'][2], NULL, NULL);

	foreach($mappings_to_be_deleted as $x)
	{
		$ipc_notes->removedata($x['guid']);
	}

	// add 4 items

	$ipc_notes->adddata('dummy 1', 'text/plain');
	$ipc_notes->adddata('dummy 2', 'text/plain');
	$ipc_notes->adddata('dummy 3', 'text/plain');
	$ipc_notes->adddata('dummy 4', 'text/plain');
?>

It is safe to igore above errors
