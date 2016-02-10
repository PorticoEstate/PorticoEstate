<?php

// Default user
$GLOBALS['phpgw']->accounts	= createObject('phpgwapi.accounts');
$GLOBALS['phpgw']->acl		= CreateObject('phpgwapi.acl');

$modules = array
(
	'activitycalendar',
//	'preferences'
);

$aclobj =& $GLOBALS['phpgw']->acl;
/*
if (!$GLOBALS['phpgw']->accounts->exists('bookingguest') ) // no guest account already exists
{
	$GLOBALS['phpgw_info']['server']['password_level'] = '8CHAR';
	$account			= new phpgwapi_user();
	$account->lid		= 'bookingguest';
	$account->firstname	= 'booking';
	$account->lastname	= 'Guest';
	$account->passwd	= 'bkbooking';
	$account->enabled	= true;
	$account->expires	= -1;
	$bookingguest 		= $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);

	$preferences	= createObject('phpgwapi.preferences');
	$preferences->set_account_id($bookingguest);
	$preferences->add('activitycalendar','template_set','bkbooking');
	$preferences->save_repository(true,$GLOBALS['type']);
}*/
