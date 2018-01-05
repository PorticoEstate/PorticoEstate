<?php
// Default user
	$GLOBALS['phpgw']->accounts = createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');

	$modules = array
		(
		'bookingfrontend',
//	'preferences'
	);

	$aclobj = & $GLOBALS['phpgw']->acl;

	if (!$GLOBALS['phpgw']->accounts->exists('bookingguest')) // no guest account already exists
	{
		$passwd = $GLOBALS['phpgw']->common->randomstring(6) . "ABab1!";

		$GLOBALS['phpgw_info']['server']['password_level'] = '8CHAR';
		$account = new phpgwapi_user();
		$account->lid = 'bookingguest';
		$account->firstname = 'booking';
		$account->lastname = 'Guest';
		$account->passwd = $passwd;
		$account->enabled = true;
		$account->expires = -1;
		$bookingguest = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);

		$preferences = createObject('phpgwapi.preferences');
		$preferences->set_account_id($bookingguest);
		$preferences->add('common', 'template_set', 'bookingfrontend');
		$preferences->save_repository(true, $GLOBALS['type']);

		$config = CreateObject('phpgwapi.config', 'bookingfrontend');
		$config->read();
		$config->value('anonymous_user', 'bookingguest');
		$config->value('anonymous_passwd', $passwd);
		$config->save_repository();
	}

	// Sane defaults for the API
	$values = array
	(
		'usecookies'			=> 'True'
	);

	foreach ( $values as $name => $val )
	{
		$sql = "INSERT INTO phpgw_config VALUES('bookingfrontend', '{$name}', '{$val}')";
		$GLOBALS['phpgw_setup']->oProc->query($sql, __LINE__, __FILE__);
	}
