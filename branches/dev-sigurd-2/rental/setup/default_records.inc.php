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

if(count($locations))
{
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_choice WHERE location_id IN ('. implode (',',$locations) . ')');
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_cust_attribute WHERE location_id IN ('. implode (',',$locations). ')');
	$GLOBALS['phpgw_setup']->oProc->query('DELETE FROM phpgw_acl  WHERE location_id IN ('. implode (',',$locations) . ')');
}

$GLOBALS['phpgw_setup']->oProc->query("DELETE FROM phpgw_locations WHERE app_id = {$app_id} AND name != 'run'");


unset($locations);


//Create groups, users, add users to groups and set preferences
$GLOBALS['phpgw']->locations->add('.',				'Root',			'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG',			'Locations for organisational units',				'rental',false);
$GLOBALS['phpgw']->locations->add('.ORG.BK',		'Organisational units in Bergen Kommune',			'rental',false);


// Open the text file from the setup folder in the rental module of portico estate
$lines = file(PHPGW_SERVER_ROOT."/rental/setup/internal_structure.txt");
// Read the first line to get the headers out of the way
$result = array();
$dep_nr = '';
$dep_name = '';

// Loop through each line of the file, parsing CSV data to a php array
foreach ($lines as $row) {
	$columns = explode(';',$row);
	if($dep_nr != $columns[0]){
		$dep_nr = $columns[0];
		$dep_name = $columns[1];
		if(strlen($dep_nr) < 2){
			$dep_nr = str_pad($dep_nr,2,"0",STR_PAD_LEFT);	
		}
		$GLOBALS['phpgw']->locations->add(".ORG.BK.{$dep_nr}",$dep_name,'rental',false);
	}
	$unit_nr = $columns[2];
	$unit_name = $columns[3];
	if(strlen($dep_nr) < 4){
		$unit_nr = str_pad($unit_nr,2,"0",STR_PAD_LEFT);	
	}
	$GLOBALS['phpgw']->locations->add(".ORG.BK.{$dep_nr}.{$unit_nr}",$unit_name,'rental',false);
}

$GLOBALS['phpgw']->locations->add('.RESPONSIBILITY',			'Fields of responsibilities',				'rental',false);

$loc_id_internal	= $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.INTERNAL',		'Field of responsibility: internleie',				'rental',false);
$loc_id_in		 	= $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.INTO',			'Field of responsibility: innleie',					'rental',false);
$loc_id_out			= $GLOBALS['phpgw']->locations->add('.RESPONSIBILITY.OUT',			'Field of responsibility: utleie',					'rental',false);


// Default groups and users
$GLOBALS['phpgw']->accounts	= createObject('phpgwapi.accounts');
$GLOBALS['phpgw']->acl		= CreateObject('phpgwapi.acl');
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
		'appname'	=> 'preferences',
		'location'	=> 'changepassword',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'rental',
		'location'	=> '.',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'rental',
		'location'	=> 'run',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'property',
		'location'	=> 'run',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'property',
		'location'	=> '.',
		'rights'	=> 1
	)
);

$aclobj =& $GLOBALS['phpgw']->acl;

if (!$GLOBALS['phpgw']->accounts->exists('rental_group') ) // no rental accounts already exists
{
	$account			= new phpgwapi_group();
	$account->lid		= 'rental_group';
	$account->firstname = 'Rental';
	$account->lastname	= 'Group';
	$rental_group		= $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
}
else
{
	$rental_group		= $GLOBALS['phpgw']->accounts->name2id('rental_group');
}

$aclobj->set_account_id($rental_group, true);
$aclobj->add('rental', '.', 1);
$aclobj->add('rental', 'run', 1);
$aclobj->add('property', '.', 1);
$aclobj->add('property', 'run', 1);
$aclobj->add('preferences', 'changepassword',1);
$aclobj->save_repository();

// Create new users: create ($account, $goups, $acls, $arrays)
// - Administrator
if (!$GLOBALS['phpgw']->accounts->exists('rental_admin') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_admin';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Administrator';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_admin 		= $GLOBALS['phpgw']->accounts->create($account, array($rental_group), array(), array('admin'));
}
else
{
	$rental_admin		= $GLOBALS['phpgw']->accounts->name2id('rental_admin');
	//Sigurd: seems to be needed for old installs
	$GLOBALS['phpgw']->accounts->add_user2group($rental_admin, $rental_group);
}

$aclobj->set_account_id($rental_admin, true);
$aclobj->add('rental', '.', 31);
$aclobj->save_repository();



//- Field of responsibility: Internal
if (!$GLOBALS['phpgw']->accounts->exists('rental_internal') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_internal';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Internal';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_internal 	= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
}
else
{
	$rental_internal	= $GLOBALS['phpgw']->accounts->name2id('rental_internal');
}


$aclobj->set_account_id($rental_internal,true);
$aclobj->add('rental', '.RESPONSIBILITY.INTERNAL', 15);
$aclobj->save_repository();

//- Field of responsibility: In
if (!$GLOBALS['phpgw']->accounts->exists('rental_in') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_in';
	$account->firstname	= 'Rental';
	$account->lastname	= 'In';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_in 			= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
}
else
{
	$rental_in			= $GLOBALS['phpgw']->accounts->name2id('rental_in');
}

$aclobj->set_account_id($rental_in, true);
$aclobj->add('rental', '.RESPONSIBILITY.INTO', 15);
$aclobj->save_repository();

//- Field of responsibility: Out
if (!$GLOBALS['phpgw']->accounts->exists('rental_out') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_out';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Out';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_out 		= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));


}
else
{
	$rental_out			= $GLOBALS['phpgw']->accounts->name2id('rental_out');
}

$aclobj->set_account_id($rental_out, true);
$aclobj->add('rental', '.RESPONSIBILITY.OUT', 15);
$aclobj->save_repository();

//- Manager
if (!$GLOBALS['phpgw']->accounts->exists('rental_manager') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'rental_manager';
	$account->firstname	= 'Rental';
	$account->lastname	= 'Manager';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$rental_manager 	= $GLOBALS['phpgw']->accounts->create($account, array($rental_group));
}
else
{
	$rental_manager		= $GLOBALS['phpgw']->accounts->name2id('rental_manager');
}

/*
//Default rental composites
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Herdla fuglereservat','Pip pip')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Fløibanen','Tut tut')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Perle og Bruse','')");
$oProc->query("INSERT INTO rental_composite (name,description,is_active) VALUES ('Store Lungegårdsvannet','',false)");
$oProc->query("INSERT INTO rental_composite (name,description,address_1,address_2,house_number,postcode,place,has_custom_address) VALUES ('Beddingen','Der Bouvet e','Solheimsgaten','Inngang B','15','5058','BERGEN',true)");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bystasjonen','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Åsane senter','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Byporten','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Ukjent sted','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Lots of levels','A rental composite that consists of areas from all levels.')");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Vitalitetssenteret','')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Bosshus/Trafo','')");
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Gullstøltunet sykehjem - Pumpehus','')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Bergen Rådhus Nye','')");
	// External
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Naustetomt Milde','')");	
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Kafé Lysverkbygget ','')");	
$oProc->query("INSERT INTO rental_composite (name,description) VALUES ('Uteservering i Bergen','')");	

$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (1,'2711')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (1,'2712')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (1,'2717')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (1,'2721')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (2,'2714')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (2,'2716')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (3,'2717')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (3,'2721')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (4,'2726')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (4,'2730')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (5,'7179')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (5,'7183')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (6,'2104-02')"); // Level 2
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (7,'1101-01-02')"); // Level 3
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (8,'3409-01-02-01')"); // Level 4
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (9,'3409-01-02-01-201')"); // Level 5
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (10,'2711')"); // Level 1
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (10,'2104-02')"); // Level 2
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (10,'1101-01-02')"); // Level 3
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (10,'3409-01-02-01')"); // Level 4
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (10,'3409-01-02-01-201')"); // Level 5
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (11,'5807-01')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (12,'3409-01')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (13,'3409-02')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (14,'3409-03')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (15,'1102-01')");
	// External
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (16,'3405-01')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (17,'1129-01')");
$oProc->query("INSERT INTO rental_unit(composite_id, location_code) VALUES (18,'VE01-01-15')");
*/
$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date, account_in, account_out) VALUES ({$loc_id_internal},'contract_type_internleie',183,183,366,'119001','119001')");
$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date) VALUES ({$loc_id_in},'contract_type_innleie',183,183,366)");
$oProc->query("INSERT INTO rental_contract_responsibility (location_id, title, notify_before, notify_before_due_date, notify_after_termination_date, account_out) VALUES ({$loc_id_out},'contract_type_eksternleie',183, 183, 366, '15')");

$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Egne', 1)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Innleie', 1)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Investeringskontrakt', 1)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('KF', 1)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Andre', 1)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Feste', 3)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Leilighet', 3)");
$oProc->query("INSERT INTO rental_contract_types (label, responsibility_id) VALUES ('Annen', 3)");

$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('monthly','1')");
$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('annually','12')");
$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('half-year','6')");
$oProc->query("INSERT INTO rental_billing_term (title, months) VALUES ('quarterly','4')");
/*
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1230768000,1253491200,".strtotime('2009-01-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1230768000,1607731200,".strtotime('2009-01-15').",{$loc_id_internal},2,{$rental_internal}, 1250593658, {$rental_internal})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1199145600,1850169600,".strtotime('2008-01-15').",{$loc_id_in},2,{$rental_in}, 1250593658, {$rental_in})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1254355200,1886716800,".strtotime('2009-10-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1253491200,1886716800,".strtotime('2009-09-15').",{$loc_id_in},2,{$rental_in}, 1250593658, {$rental_in})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1233619200,1886716800,".strtotime('2009-02-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1250035200,1886716800,".strtotime('2009-08-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1245110400,1886716800,".strtotime('2009-06-16').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1243814400,1886716800,".strtotime('2009-06-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by) VALUES (1075593600,1706832000,".strtotime('2004-02-15').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out})");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1045008000,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000659','044330','38110','','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1047945600,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000660','721000','38610','','119001','119001','9')");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (915148800,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000585','755200','26110','','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (915148800,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000586','755200','26110','','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (915148800,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000587','755200','26110','','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1136073600,NULL,".strtotime('2006-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00006497','000000','00000','','119001','119001','9')");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1199145600,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000797','040110','13000','Gjelder areal i 8. etg inkl. fellesareal','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1104537600,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000798','030000','13000','Gjelder 4 etg og 5 etg (inkl fellesareal)','119001','119001','9')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, responsibility_id, service_id, invoice_header, account_in, account_out, project_id) VALUES (1104537600,NULL,".strtotime('2005-01-01').",{$loc_id_internal},1,{$rental_internal}, 1250593658, {$rental_internal},'K00000801','013000','13000','Gjelder kjeller, 1 og 9 etg (inkl fellesareal)','119001','119001','9')");
	// External
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, invoice_header) VALUES (".strtotime('2009-02-29').",NULL,".strtotime('2009-01-01').",{$loc_id_out},3,{$rental_out}, 1250593658, {$rental_out},'K00007198','Naustetomt Milde')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, invoice_header) VALUES (".strtotime('2004-01-01').",NULL,".strtotime('2006-06-01').",{$loc_id_out},1,{$rental_out}, 1250593658, {$rental_out},'K00005915','Kafé')");
$oProc->query("INSERT INTO rental_contract (date_start, date_end, billing_start, location_id, term_id, executive_officer, created, created_by, old_contract_id, invoice_header) VALUES (".strtotime('2009-01-01').",".strtotime('2009-12-31').",".strtotime('2009-01-01').",{$loc_id_out},2,{$rental_out}, 1250593658, {$rental_out},'K00007203','Leie uteareal Finnegården')");

$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (1,1)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (2,2)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (3,3)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (4,4)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (5,5)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (6,6)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (7,7)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (8,8)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (9,9)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id)  VALUES (10,10)");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (11,11)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (12,11)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (13,12)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (14,13)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (15,14)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (16,12)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (17,15)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (18,15)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (19,15)");
	// External
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (20,16)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (21,17)");
$oProc->query("INSERT INTO rental_contract_composite (contract_id, composite_id) VALUES (22,18)");

$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('12345678901','Ola','Nordmann',true,'Bergensgt 5','5050','BERGEN')");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('23456789012','Kari','Nordmann',true,'Nordnesgt 7','5020','BERGEN')");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, is_active, address_1, postal_code, place) VALUES ('34567890123','Per','Nordmann',true,'Solheimsviken 13','5008','BERGEN')");
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0443','Åge','Nilssen','IDRETT Sentrum sør','Byrådsavdeling for oppvekst','ar564@bergen.kommune.no','R0443',true)");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0956','Berit','Tande','Bergenhus og Årstad kulturkontor','Byrådsavd. for kultur, næring og idrett','wb902@bergen.kommune.no','R0956',true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, account_number, is_active, location_id) VALUES ('R7552','Anna Milde','Thorbjørnsen','Gullstøltunet','Byrådsavd. for helse og omsorg','vk172@bergen.kommune.no','R7552',true,{$loc_id_ba_helse})");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, address_1, postal_code, place, phone, email, is_active) VALUES ('KF06','Øyvind','Berggreen','Gullstøltunet kjøkken','Øvre Kråkenes 111','5152','Bønes','55929846/48','vm152@bergen.kommune.no',true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, is_active,location_id) VALUES ('R0401','Anne-Marit','Presterud','Gullstøltunet kjøkken','Byrådsavd. for barnehage og skole','jf684@bergen.kommune.no',true,{$loc_id_ba_barnehage})");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0300','Jan-Petter','Stoutland','BHO - Kommunaldirektørens stab','Byrådsavd. for helse og omsorg','gs256@bergen.kommune.no','R0300',true)");
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, company_name, department, email, account_number, is_active) VALUES ('R0130','Robert','Rastad','Seksjon informasjon','Byrådsleders avdeling','jg406@bergen.kommune.no','R0130',true)");
	// External
$oProc->query("INSERT INTO rental_party (identifier, first_name, last_name, address_1, postal_code, place, reskontro, is_active) VALUES ('01017000000','T','S','Starefossvingen 10290','5019','BERGEN','504040',true)");
$oProc->query("INSERT INTO rental_party (identifier, company_name, address_1, postal_code, place, reskontro, is_active) VALUES ('710513','PP Finnegården','Finnegårdsgaten 2A','5003','BERGEN','504042',true)");
$oProc->query("INSERT INTO rental_party (identifier, company_name, address_1, postal_code, place, reskontro, account_number, is_active) VALUES ('985600000','B&M  AS','Rasmus Meyers Alle','5015','BERGEN','503007','14905',true)");
	
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (11, 4, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (12, 5, true)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (13, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (14, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (15, 6, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (16, 7, true)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (17, 8, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (18, 9, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (19, 10, true)");
	// External
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (20, 11, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (21, 12, true)");
$oProc->query("INSERT INTO rental_contract_party (contract_id, party_id, is_payer) VALUES (22, 13, true)");
*/
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Fellesareal', '123456789', true, 34.59)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Administrasjon', 'Y900', true, 23.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Parkeringsplass', '124246242', false, 50.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Forsikring', 'Y901', true, 10.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kapitalkostnad', 'Y904', true, 700.00)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Kom.avg. uten renovasjon', 'Y902', true, 32.29)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Renovasjon', 'Y903', true, 10.94)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Vedlikehold', 'Y905', true, 98.23)");
	// External
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BENA00', false, 500)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Felleskostnader', 'BEAA02', false, 70000)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BEAA02', false, 1000000)");
$oProc->query("INSERT INTO rental_price_item (title, agresso_id, is_area, price) VALUES ('Leie', 'BETGEI', false, 20000)");
/*
	// Vitalitetssenteret
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 11, 'Administrasjon', 1712, 0, 'Y900', true, 23.98, 41053.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 11, 'Forsikring', 1712, 0, 'Y901', true, 10.57, 18095.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 11, 'Kapitalkostnad', 1712, 0, 'Y904', true, 759.85, 1300863.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 11, 'Kom.avg. uten renovasjon', 1712, 0, 'Y902', true, 32.29, 55280.48, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 11, 'Renovasjon', 1712, 0, 'Y903', true, 10.94, 18729.28, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 11, 'Vedlikehold', 1712, 0, 'Y905', true, 98.23, 168169.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 12, 'Administrasjon', 1158, 0, 'Y900', true, 23.98, 27768.84, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 12, 'Forsikring', 1158, 0, 'Y901', true, 10.57, 12240.06, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 12, 'Kapitalkostnad', 1158, 0, 'Y904', true, 702.34, 813309.72, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 12, 'Kom.avg. uten renovasjon', 1158, 0, 'Y902', true, 32.29, 37391.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 12, 'Renovasjon', 1158, 0, 'Y903', true, 10.94, 12668.52, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 12, 'Vedlikehold', 1158, 0, 'Y905', true, 98.23, 113750.34, '2009-01-01', NULL)");
	// Gullstøltunet sykehjem
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 13, 'Administrasjon', 7039, 0, 'Y900', true, 23.98, 168795.22, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 13, 'Forsikring', 7039, 0, 'Y901', true, 10.57, 74402.23, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 13, 'Kapitalkostnad', 7039, 0, 'Y904', true, 835.69, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 13, 'Vedlikehold', 7039, 0, 'Y905', true, 98.23, 691440.97, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 14, 'Administrasjon', 53, 0, 'Y900', true, 23.98, 1270.94, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 14, 'Forsikring', 53, 0, 'Y901', true, 10.57, 560.21, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 14, 'Kapitalkostnad', 53, 0, 'Y904', true, 44291.57, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 14, 'Vedlikehold', 53, 0, 'Y905', true, 98.23, 5206.19, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 15, 'Administrasjon', 13, 0, 'Y900', true, 23.98, 311.74, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 15, 'Forsikring', 13, 0, 'Y901', true, 10.57, 137.41, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 15, 'Kapitalkostnad', 13, 0, 'Y904', true, 10863.97, 5882421.91, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 15, 'Vedlikehold', 13, 0, 'Y905', true, 98.23, 1276.99, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 16, 'Administrasjon', 360, 0, 'Y900', true, 23.98, 8632.80, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 16, 'Forsikring', 360, 0, 'Y901', true, 10.57, 3805.20, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 16, 'Kapitalkostnad', 360, 0, 'Y904', true, 835.69, 300848.40, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 16, 'Vedlikehold', 360, 0, 'Y905', true, 98.23, 35362.80, '2009-01-01', NULL)");
	// Bergen Rådhus
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 17, 'Administrasjon', 792.3, 0, 'Y900', true, 23.27, 18436.82, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 17, 'Forsikring', 792.3, 0, 'Y901', true, 10.25, 8121.08, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 17, 'Kapitalkostnad', 792.3, 0, 'Y904', true, 1042.95, 826329.29, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 17, 'Kom.avg. uten renovasjon', 792.3, 0, 'Y902', true, 32.29, 25583.37, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 17, 'Renovasjon', 792.3, 0, 'Y903', true, 10.94, 8667.76, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 17, 'Vedlikehold', 792.3, 0, 'Y905', true, 95.28, 75490.34, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 18, 'Administrasjon', 1160.4, 0, 'Y900', true, 23.98, 27826.39, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 18, 'Forsikring', 1160.4, 0, 'Y901', true, 10.57, 12265.43, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 18, 'Kapitalkostnad', 1160.4, 0, 'Y904', true, 1075.18, 1247638.87, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 18, 'Kom.avg. uten renovasjon', 1160.4, 0, 'Y902', true, 32.29, 37469.32, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 18, 'Renovasjon', 1160.4, 0, 'Y903', true, 10.94, 12694.78, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 18, 'Vedlikehold', 1160.4, 0, 'Y905', true, 98.23, 113986.09, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (2, 19, 'Administrasjon', 791.3, 0, 'Y900', true, 23.98, 18975.37, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (4, 19, 'Forsikring', 791.3, 0, 'Y901', true, 10.57, 8364.04, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (5, 19, 'Kapitalkostnad', 791.3, 0, 'Y904', true, 1075.18, 850789.93, '2009-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (6, 19, 'Kom.avg. uten renovasjon', 791.3, 0, 'Y902', true, 32.29, 25551.08, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (7, 19, 'Renovasjon', 791.3, 0, 'Y903', true, 10.94, 8656.82, '2005-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (8, 19, 'Vedlikehold', 791.3, 0, 'Y905', true, 98.23, 77729.40, '2009-01-01', NULL)");
	// External
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (9, 20, 'Leie', 0, 1, 'BENA00', false, 500, 500, '2009-02-23', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (10, 21, 'Felleskostnader', 0, 1, 'BEAA02', false, 75335.7, 75335.7, '2006-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (11, 21, 'Leie', 0, 1, 'BEAA02', false, 1137108, 1137108, '2006-01-01', NULL)");
$oProc->query("INSERT INTO rental_contract_price_item (price_item_id, contract_id, title, area, count, agresso_id, is_area, price, total_price, date_start, date_end) VALUES (12, 22, 'Leie', 0, 1, 'BETGEI', false, 22400, 22400, '2009-01-01', NULL)");

	// Bergen Rådhus
$oProc->query("INSERT INTO rental_billing(total_sum, success, created_by, timestamp_start, timestamp_stop, timestamp_commit, location_id, term_id, year, month, deleted, export_format) VALUES ('82508.85',true,{$rental_internal},1254549759,1254549759,NULL,{$loc_id_internal},1,2009,7,false,'agresso_gl07')");

	// Bergen Rådhus
$oProc->query("INSERT INTO rental_invoice(contract_id, billing_id, party_id, timestamp_created, timestamp_start, timestamp_end, total_sum, total_area, header, account_in, account_out, service_id, responsibility_id, project_id) VALUES (19,1,10,1254549759,1246406400,1248998400,'82508.85','793.1','Gjelder kjeller, 1 og 9 etg (inkl fellesareal)','119001','119001','13000','013000','9')");

	// Bergen Rådhus
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Administrasjon','Y900',true,'23.98','791.3',0,'1582.6','2009-07-01','2009-07-31')");
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Forsikring','Y901',true,'10.57','791.3',0,'696.34','2009-07-01','2009-07-31')");
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Kapitalkostnad','Y904',true,'1075.18','791.3',0,'70900.48','2009-07-01','2009-07-31')"); 
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Kom.avg. uten renovasjon','Y902',true,'32.29','791.3',0,'2128.6','2009-07-01','2009-07-31')"); 
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Renovasjon','Y903',true,'10.94','791.3',0,'720.08','2009-07-01','2009-07-31')");
$oProc->query("INSERT INTO rental_invoice_price_item (invoice_id, title, agresso_id, is_area, price, area, count, total_price, date_start, date_end) VALUES (1,'Vedlikehold','Y905',true,'98.23','791.3',0,'6480.75','2009-07-01','2009-07-31')");

$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},11,'Oppdatér leietaker med ny postadresse.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},13,'Leietaker tilbake fra ferie. Følg opp e-post sendt ut for to uker siden.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},15,'Kontrollér at priselementer er i henhold.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},17,'Oppdatér med ny postadresse.',1250593658,0)");
$oProc->query("INSERT INTO rental_notification (location_id, contract_id, message, date, recurrence) VALUES ({$loc_id_internal},18,'Oppdatér med ny postadresse.',1250593658,0)");

$oProc->query("INSERT INTO rental_notification_workbench (account_id, notification_id, date, dismissed) VALUES ({$rental_internal},1,1250593658, 'FALSE')");
$oProc->query("INSERT INTO rental_notification_workbench (account_id, notification_id, date, dismissed) VALUES ({$rental_internal},2,1250593658, 'FALSE')");

$oProc->query("INSERT INTO rental_contract_last_edited VALUES (2,{$rental_internal},1250593658)");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (1,{$rental_in},1250593658)");
$oProc->query("INSERT INTO rental_contract_last_edited VALUES (3,{$rental_out},1250593658)");
*/

$oProc->query("INSERT INTO rental_document_types (title) VALUES ('contracts')");
$oProc->query("INSERT INTO rental_document_types (title) VALUES ('fire_drawings')");
$oProc->query("INSERT INTO rental_document_types (title) VALUES ('calculations_internal_investment')");

$asyncservice = CreateObject('phpgwapi.asyncservice');
$asyncservice->delete('rental_populate_workbench_notifications');
$asyncservice->set_timer(
	array('day' => "*/1"),
	'rental_populate_workbench_notifications',
	'rental.sonotification.populate_workbench_notifications',
	null
	);

