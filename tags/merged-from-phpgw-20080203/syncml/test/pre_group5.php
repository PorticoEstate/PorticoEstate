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
?>

It is safe to igore above errors.
