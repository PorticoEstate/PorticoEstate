<?php
	/**
	 * Holds the queries inserting default data (not test data):
	 *
	 * $oProc->query("sql_statement");
	 *
	 */
// clean up from previous install
	$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'rental'");
	$GLOBALS['phpgw_setup']->oProc->next_record();
	$app_id = $GLOBALS['phpgw_setup']->oProc->f('app_id');

	$GLOBALS['phpgw_setup']->oProc->query("SELECT location_id FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");

	$locations = array();
	while ($GLOBALS['phpgw_setup']->oProc->next_record())
	{
		$locations[] = $GLOBALS['phpgw_setup']->oProc->f('location_id');
	}

	if (count($locations))
	{
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_choice WHERE location_id IN (' . implode(',', $locations) . ')');
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN (' . implode(',', $locations) . ')');
		$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_acl  WHERE location_id IN (' . implode(',', $locations) . ')');
	}

	$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");


	unset($locations);


//Create groups, users, add users to groups and set preferences
	$GLOBALS['phpgw']->locations->add('.', 'Root', 'rental', false);
	$GLOBALS['phpgw']->locations->add('.contract', 'Contract', 'rental', $allow_grant = false, $custom_tbl = false, $c_function = true);
	$GLOBALS['phpgw']->locations->add('.application', 'Application', 'rental', $allow_grant = false, $custom_tbl = false, $c_function = true);
	$GLOBALS['phpgw']->locations->add('.moveout', 'Moveout', 'rental', $allow_grant = true, $custom_tbl = 'rental_moveout', $c_function = true, $c_attrib = true);
	$GLOBALS['phpgw']->locations->add('.movein', 'Movein', 'rental', $allow_grant = true, $custom_tbl = 'rental_movein', $c_function = true, $c_attrib = true);

	$GLOBALS['phpgw']->locations->add('.ORG', 'Locations for organisational units', 'rental', false);
	$GLOBALS['phpgw']->locations->add('.ORG.BK', 'Organisational units in Bergen Kommune', 'rental', false);

	$GLOBALS['phpgw']->locations->add('.RESPONSIBILITY', 'Fields of responsibilities', 'rental', false);

	$loc_id_internal = $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.INTERNAL', 'Field of responsibility: internleie', 'rental', false);
	$loc_id_in = $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.INTO', 'Field of responsibility: innleie', 'rental', false);
	$loc_id_out = $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.OUT', 'Field of responsibility: utleie', 'rental', false);


// Default groups and users
	$GLOBALS['phpgw']->accounts = createObject('phpgwapi.accounts');
	$GLOBALS['phpgw']->acl = CreateObject('phpgwapi.acl');
	$GLOBALS['phpgw']->acl->enable_inheritance = true;


	$modules = array
		(
		'manual',
		'preferences',
		'rental',
		'property'
	);

	$acls = array
		(
		array
			(
			'appname' => 'preferences',
			'location' => 'changepassword',
			'rights' => 1
		),
		array
			(
			'appname' => 'rental',
			'location' => '.',
			'rights' => 1
		),
		array
			(
			'appname' => 'rental',
			'location' => 'run',
			'rights' => 1
		),
		array
			(
			'appname' => 'property',
			'location' => 'run',
			'rights' => 1
		),
		array
			(
			'appname' => 'property',
			'location' => '.',
			'rights' => 1
		)
	);

	$aclobj = & $GLOBALS['phpgw']->acl;

	if (!$GLOBALS['phpgw']->accounts->exists('rental_group')) // no rental accounts already exists
	{
		$account = new phpgwapi_group();
		$account->lid = 'rental_group';
		$account->firstname = 'Rental';
		$account->lastname = 'Group';
		$rental_group = $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
	}
	else
	{
		$rental_group = $GLOBALS['phpgw']->accounts->name2id('rental_group');
	}

	$aclobj->set_account_id($rental_group, true);
	$aclobj->add('rental', '.', 1);
	$aclobj->add('rental', 'run', 1);
	$aclobj->add('property', '.', 1);
	$aclobj->add('property', 'run', 1);
	$aclobj->add('preferences', 'changepassword', 1);
	$aclobj->add('preferences', '.', 1);
	$aclobj->add('preferences', 'run', 1);
	$aclobj->save_repository();

// Create new users: create ($account, $goups, $acls, $arrays)
// - Administrator
	if (!$GLOBALS['phpgw']->accounts->exists('rental_admin')) // no rental accounts already exists
	{
		$account = new phpgwapi_user();
		$account->lid = 'rental_admin';
		$account->firstname = 'Rental';
		$account->lastname = 'Administrator';
		$account->passwd = 'EState12=';
		$account->enabled = true;
		$account->expires = -1;
		$rental_admin = $GLOBALS['phpgw']->accounts->create($account, array($rental_group), array(), array(
			'admin'));
	}
	else
	{
		$rental_admin = $GLOBALS['phpgw']->accounts->name2id('rental_admin');
		//Sigurd: seems to be needed for old installs
		$GLOBALS['phpgw']->accounts->add_user2group($rental_admin, $rental_group);
	}

	$aclobj->set_account_id($rental_admin, true);
	$aclobj->add('rental', '.', 31);
	$aclobj->save_repository();



//- Field of responsibility: Internal
	if (!$GLOBALS['phpgw']->accounts->exists('rental_internal')) // no rental accounts already exists
	{
		$account = new phpgwapi_user();
		$account->lid = 'rental_internal';
		$account->firstname = 'Rental';
		$account->lastname = 'Internal';
		$account->passwd = 'EState12=';
		$account->enabled = true;
		$account->expires = -1;
		$rental_internal = $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
	}
	else
	{
		$rental_internal = $GLOBALS['phpgw']->accounts->name2id('rental_internal');
	}


	$aclobj->set_account_id($rental_internal, true);
	$aclobj->add('rental', '.RESPONSIBILITY.INTERNAL', 15);
	$aclobj->save_repository();

//- Field of responsibility: In
	if (!$GLOBALS['phpgw']->accounts->exists('rental_in')) // no rental accounts already exists
	{
		$account = new phpgwapi_user();
		$account->lid = 'rental_in';
		$account->firstname = 'Rental';
		$account->lastname = 'In';
		$account->passwd = 'EState12=';
		$account->enabled = true;
		$account->expires = -1;
		$rental_in = $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
	}
	else
	{
		$rental_in = $GLOBALS['phpgw']->accounts->name2id('rental_in');
	}

	$aclobj->set_account_id($rental_in, true);
	$aclobj->add('rental', '.RESPONSIBILITY.INTO', 15);
	$aclobj->save_repository();

//- Field of responsibility: Out
	if (!$GLOBALS['phpgw']->accounts->exists('rental_out')) // no rental accounts already exists
	{
		$account = new phpgwapi_user();
		$account->lid = 'rental_out';
		$account->firstname = 'Rental';
		$account->lastname = 'Out';
		$account->passwd = 'EState12=';
		$account->enabled = true;
		$account->expires = -1;
		$rental_out = $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
	}
	else
	{
		$rental_out = $GLOBALS['phpgw']->accounts->name2id('rental_out');
	}

	$aclobj->set_account_id($rental_out, true);
	$aclobj->add('rental', '.RESPONSIBILITY.OUT', 15);
	$aclobj->save_repository();

//- Manager
	if (!$GLOBALS['phpgw']->accounts->exists('rental_manager')) // no rental accounts already exists
	{
		$account = new phpgwapi_user();
		$account->lid = 'rental_manager';
		$account->firstname = 'Rental';
		$account->lastname = 'Manager';
		$account->passwd = 'EState12=';
		$account->enabled = true;
		$account->expires = -1;
		$rental_manager = $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
	}
	else
	{
		$rental_manager = $GLOBALS['phpgw']->accounts->name2id('rental_manager');
	}

	$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date, account_in, account_out, project_number, agresso_export_format) VALUES ({$loc_id_internal},'contract_type_internleie',183,183,366,'119001','119001','9', 'agresso_gl07')");
	$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date) VALUES ({$loc_id_in},'contract_type_innleie',183,183,366)");
	$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date, account_out, agresso_export_format) VALUES ({$loc_id_out},'contract_type_eksternleie',183, 183, 366, '1510', 'agresso_lg04')");

	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id) VALUES (1, 'contract_type_internleie_egne', 1)");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id) VALUES (2, 'contract_type_internleie_innleie', 1)");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id) VALUES (3, 'contract_type_internleie_investeringskontrakt', 1)");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id) VALUES (4, 'contract_type_internleie_KF', 1)");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id) VALUES (5, 'contract_type_internleie_andre', 1)");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id, account) VALUES (6, 'contract_type_eksternleie_feste', 3, '1520')");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id, account) VALUES (7, 'contract_type_eksternleie_leilighet', 3, '1530')");
	$oProc->query("INSERT INTO rental_contract_types (id, label, responsibility_id, account) VALUES (8, 'contract_type_eksternleie_annen', 3, '1510')");

	$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('monthly','1')");
	$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('annually','12')");
	$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('half-year','6')");
	$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('free_of_charge','0')");

//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Fellesareal', '123456789', true, 34.59)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Administrasjon', 'Y900', true, 23.00)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Parkeringsplass', '124246242', false, 50.00)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Forsikring', 'Y901', true, 10.00)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kapitalkostnad', 'Y904', true, 700.00)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kom.avg. uten renovasjon', 'Y902', true, 32.29)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Renovasjon', 'Y903', true, 10.94)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Vedlikehold', 'Y905', true, 98.23)");
//	// External
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BENA00', false, 500)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Felleskostnader', 'BEAA02', false, 70000)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BEAA02', false, 1000000)");
//$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BETGEI', false, 20000)");

	$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area,is_inactive,is_adjustable,price,responsibility_id) VALUES ('Unknown', 'UNKNOWN', false,false,false, 0, 0)");
	$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area,is_inactive,is_adjustable,price,responsibility_id) VALUES ('Leie', 'INNLEIE', false,false,false, 0, {$loc_id_in})");

	$oProc->query("INSERT INTO rental_document_types (title) VALUES ('contracts')");
	$oProc->query("INSERT INTO rental_document_types (title) VALUES ('fire_drawings')");
	$oProc->query("INSERT INTO rental_document_types (title) VALUES ('calculations_internal_investment')");

	$asyncservice = CreateObject('phpgwapi.asyncservice');
	$asyncservice->delete('rental_populate_workbench_notifications');
	$asyncservice->set_timer(
		array('day' => "*/1"), 'rental_populate_workbench_notifications', 'rental.sonotification.populate_workbench_notifications', null
	);

	$asyncservice->set_timer(
		array('day' => "*/1"), 'rental_run_adjustments', 'rental.soadjustment.run_adjustments', null
	);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO rental_composite_type"
			. " (id, name) VALUES (1, 'Type 1' )", __LINE__, __FILE__);
	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO rental_composite_type"
			. " (id, name) VALUES (2, 'Type 2' )", __LINE__, __FILE__);

	$GLOBALS['phpgw_setup']->oProc->query("INSERT INTO fm_idgenerator(name,value,descr) "
		. "VALUES('faktura_buntnr', 0, 'buntnr utgående faktura')", __LINE__, __FILE__);
