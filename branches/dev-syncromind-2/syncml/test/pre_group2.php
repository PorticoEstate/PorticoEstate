<?php
	require 'head_pre.php';

	// remove all mappings
	$somappings->delete_mapping(NULL, NULL, NULL, NULL);

	// empty database

	foreach($ipc_notes->getidlist() as $id)
	{
		$ipc_notes->removedata($id);
	}

	foreach($ipc_addressbook->getidlist() as $id)
	{
		$ipc_addressbook->removedata($id);
	}
?>

It is safe to igore above errors.
