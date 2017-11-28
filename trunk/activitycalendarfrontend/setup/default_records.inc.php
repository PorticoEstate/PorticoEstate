<?php
// Default user
	$GLOBALS['phpgw']->accounts = createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');

	$modules = array
		(
		'activitycalendarfrontend',
//	'preferences'
	);

	$aclobj = & $GLOBALS['phpgw']->acl;

	// Sane defaults for the API
	$values = array
	(
		'usecookies'			=> 'True'
	);

	foreach ( $values as $name => $val )
	{
		$sql = "INSERT INTO phpgw_config VALUES('activitycalendarfrontend', '{$name}', '{$val}')";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}
