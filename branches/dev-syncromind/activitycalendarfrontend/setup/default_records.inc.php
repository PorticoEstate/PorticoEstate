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