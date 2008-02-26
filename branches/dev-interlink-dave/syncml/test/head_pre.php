<?php
	$phpgw_info = array();

	$GLOBALS['phpgw_info']['flags'] = array(
		'disable_template_class' => True,
		'currentapp'             => 'login',
		'noheader'               => True,
		'nocachecontrol'         => True
	);

	require '../../header.inc.php';
	require '../inc/class.somappings.inc.php';

	var_dump($GLOBALS['phpgw']->session->create('demo', 'guest');

	$ipc_manager = CreateObject('phpgwapi.ipc_manager');

	$ipc_notes = $ipc_manager->getipc('notes');
	$ipc_addressbook = $ipc_manager->getipc('addressbook');

	$somappings = new syncml_somappings();
?>
