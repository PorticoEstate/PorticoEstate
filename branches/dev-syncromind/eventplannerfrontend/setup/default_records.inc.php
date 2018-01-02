<?php
	$GLOBALS['phpgw']->locations->add('.', 'Tom', 'eventplannerfrontend');
	$GLOBALS['phpgw']->locations->add('.admin', 'admin', 'eventplannerfrontend');
	$GLOBALS['phpgw']->locations->add('.application', 'application', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.events', 'events', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.customer', 'customer', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.vendor', 'vendor', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.calendar', 'calendar', 'eventplannerfrontend', $allow_grant = true);
	$GLOBALS['phpgw']->locations->add('.booking', 'booking', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.vendor_report', 'vendor_report', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);
	$GLOBALS['phpgw']->locations->add('.customer_report', 'customer_report', 'eventplannerfrontend', $allow_grant = true, $custom_tbl = '', $c_function = true);


// Default user
	$GLOBALS['phpgw']->accounts = createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');

	$modules = array
		(
		'eventplannerfrontend',
	);


	if (!$GLOBALS['phpgw']->accounts->exists('eventplannerguest')) // no guest account already exists
	{
		$passwd = $GLOBALS['phpgw']->common->randomstring(6) . "ABab1!";

		$GLOBALS['phpgw_info']['server']['password_level'] = '8CHAR';
		$account = new phpgwapi_user();
		$account->lid = 'eventplannerguest';
		$account->firstname = 'Eventplanner';
		$account->lastname = 'Guest';
		$account->passwd = $passwd;
		$account->enabled = true;
		$account->expires = -1;
		$eventplannerguest = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);

		$preferences = createObject('phpgwapi.preferences');
		$preferences->set_account_id($eventplannerguest);
		$preferences->add('common', 'template_set', 'frontend');
		$preferences->save_repository(true, $GLOBALS['type']);
		$config = CreateObject('phpgwapi.config', 'eventplannerfrontend');
		$config->read();
		$config->value('anonymous_user', 'eventplannerguest');
		$config->value('anonymous_passwd', $passwd);
		$config->save_repository();
	}

	if(!$eventplannerguest)
	{
		$eventplannerguest = $GLOBALS['phpgw']->accounts->name2id('eventplannerguest');
	}

	$aclobj = & $GLOBALS['phpgw']->acl;
	$aclobj->set_account_id($eventplannerguest, true);
	$aclobj->add('phpgwapi', 'anonymous', 1);
	$aclobj->add('eventplannerfrontend', 'run', 1);
	$aclobj->add('eventplannerfrontend', '.application', 1);
	$aclobj->add('eventplannerfrontend', '.resource', 1);
	$aclobj->add('eventplannerfrontend', '.customer', 1 | 2 | 4);
	$aclobj->add('eventplannerfrontend', '.vendor', 1 | 2 | 4);
	$aclobj->add('eventplannerfrontend', '.booking', 1 | 2 | 4);
	$aclobj->add('eventplannerfrontend', '.vendor_report', 1 | 2 | 4);
	$aclobj->add('eventplannerfrontend', '.customer_report', 1 | 2 | 4);
	$aclobj->save_repository();

	// Sane defaults for the API
	$values = array
	(
		'usecookies'			=> 'True'
	);

	foreach ( $values as $name => $val )
	{
		$sql = "INSERT INTO phpgw_config VALUES('eventplannerfrontend', '{$name}', '{$val}')";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}
