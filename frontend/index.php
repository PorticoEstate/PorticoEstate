<?php
	$GLOBALS['phpgw_info']['flags'] = array(
		'noheader' => true,
		'nonavbar' => true,
		'currentapp' => 'frontend'
	);

	include_once('../header.inc.php');

	$GLOBALS['phpgw']->redirect_link('/index.php', array('menuaction' => 'frontend.uifrontend.index'));
