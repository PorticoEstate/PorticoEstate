<?php
	$GLOBALS['phpgw_info']['flags'] = array
		(
		'noheader'	 => true,
		'nonavbar'	 => true,
		'currentapp' => 'property'
	);

	include_once('../header.inc.php');
	
	//1556665322 converts to Wednesday May 01, 2019 01:02:02 (am) in time zone Europe/Oslo (CEST)

	$entry_date = $GLOBALS['phpgw']->common->show_date(1556665322, 'Y-m-d H:i:s');
	
	_debug_array($entry_date);
	_debug_array(date('Y-m-d H:i:s',1556665322));

	
