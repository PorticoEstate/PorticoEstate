<?php
	DEFINE('APP_NAME', 'handyman');

	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader' => true,
		'nonavbar' => true,
		'currentapp' => APP_NAME,
		'enable_vfs_class' => True,
	);

	include('../header.inc.php');
    $start_page = array('menuaction' => APP_NAME . '.export.index');
	$GLOBALS['phpgw']->redirect_link('/index.php', $start_page);
