<?php
/**
 * Holds the queries inserting default data (not test data):
 *
 * $oProc->query("sql_statement");
 *
 */

// clean up from previous install
$GLOBALS['phpgw_setup']->oProc->query("SELECT app_id FROM phpgw_applications WHERE app_name = 'controller'");
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
$GLOBALS['phpgw']->locations->add('.',				'Root',			'controller',false);
$GLOBALS['phpgw']->locations->add('admin',			'Admin',		'controller',false);
$GLOBALS['phpgw']->locations->add('.usertype',				'Usertypes',						'controller',false);
$GLOBALS['phpgw']->locations->add('.usertype.superuser',	'Usertype: Superuser',				'controller',false);
$GLOBALS['phpgw']->locations->add('.usertype.user',			'Usertype: User',					'controller',false);

/*
// Default groups and users
$GLOBALS['phpgw']->accounts	= createObject('phpgwapi.accounts');
$GLOBALS['phpgw']->acl		= CreateObject('phpgwapi.acl');
$GLOBALS['phpgw']->acl->enable_inheritance = true;


$modules = array
(
	'manual',
	'preferences',
	'controller',
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
		'appname'	=> 'controller',
		'location'	=> '.',
		'rights'	=> 1
	),
	array
	(
		'appname'	=> 'controller',
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

if (!$GLOBALS['phpgw']->accounts->exists('controller_group') ) // no controller accounts already exists
{
	$account			= new phpgwapi_group();
	$account->lid		= 'controller_group';
	$account->firstname = 'Controller';
	$account->lastname	= 'Group';
	$controller_group		= $GLOBALS['phpgw']->accounts->create($account, array(), array(), $modules);
}
else
{
	$controller_group		= $GLOBALS['phpgw']->accounts->name2id('controller_group');
}

$aclobj->set_account_id($controller_group, true);
$aclobj->add('controller', '.', 1);
$aclobj->add('controller', 'run', 1);
$aclobj->add('property', '.', 1);
$aclobj->add('property', 'run', 1);
$aclobj->add('preferences', 'changepassword',1);
$aclobj->add('preferences', '.',1);
$aclobj->add('preferences', 'run',1);
$aclobj->save_repository();

// Create new users: create ($account, $goups, $acls, $arrays)
// - Administrator
if (!$GLOBALS['phpgw']->accounts->exists('controller_admin') ) // no rental accounts already exists
{
	$account			= new phpgwapi_user();
	$account->lid		= 'controller_admin';
	$account->firstname	= 'Controller';
	$account->lastname	= 'Administrator';
	$account->passwd	= 'EState12=';
	$account->enabled	= true;
	$account->expires	= -1;
	$controller_admin 	= $GLOBALS['phpgw']->accounts->create($account, array($rental_group), array(), array('admin'));
}
else
{
	$controller_admin	= $GLOBALS['phpgw']->accounts->name2id('controller_admin');
	//Sigurd: seems to be needed for old installs
	$GLOBALS['phpgw']->accounts->add_user2group($controller_admin, $controller_group);
}

$aclobj->set_account_id($controller_admin, true);
$aclobj->add('controller', '.', 31);
$aclobj->save_repository();
*/

/*
 * insert default records (test data)
 * TODO: !!Remove before production!!
 */

//insert control areas
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Miljø')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('IK - Brann')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('IK - Løfteinnretning')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('IK - Elektro')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('IK - Vannforsyning')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Svømmeanlegg')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('IK - Tilfluktsrom')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Varmeanlegg')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Ventilasjonsanlegg')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Helse')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Sikkerhet')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Enøk')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Divese - Leietaker')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Diverse - Byggforvalter')");
$oProc->query("INSERT INTO controller_control_area (title) VALUES ('Legionella')");

//insert control groups
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Utstyr, f. eks blomster - og plantekasser', 1, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Benker', 1, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Stell og vedlikehold av grøntanlegg/ utomhusanlegg', 1, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Utendørs fontener og springvann', 1, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Kummer og tanker for tekniske installasjoner', 1, NULL)");

$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Brannbeskyttelse bærende konstruksjon', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Brannsmitte', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Rømningsvinduer', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Branncellebegrensende konstruksjoner/ branntetting', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Branndekker', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Merking og ledesystem', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Installasjon for manuell brannslokking med vann', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Installasjon for brannslokking med sprinkler', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Brannalarm', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Anlegg for røyk- og brannventilasjon generelt', 2, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Nødlysutstyr', 2, NULL)");

$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Elkraft, generelt', 4, NULL)");

$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Rutine for avviksbehandling', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Logg for avviksbehandling', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Reparasjoner og utbedringer/ renhold basseng', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Vannstand/ vannfylling', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Driftslogg generelt tilsyn/ trykkpumpe', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Sjekkliste og rutiner', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Sjekklister, rutiner og logg', 5, NULL)");
$oProc->query("INSERT INTO controller_control_group (group_name, control_area_id, procedure_id) VALUES ('Vannprøver og resultater', 5, NULL)");

//insert control items
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er alle elektriske kabler betryggende festet?', false, 'Kommer', 'Kommer', 1)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er alle svakstrøm/sterkstrømkabler forlagt adskilt?', false, 'Kommer', 'Kommer', 1)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er noen kabler eller ledninger skadet?', false, 'Kommer', 'Kommer', 1)");

$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er stikkontakter/brytere betryggende festet?', false, 'Kommer', 'Kommer', 2)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er det jordet og ujordet anlegg i samme rom?', false, 'Kommer', 'Kommer', 2)");

$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er lysarmaturer betryggende festet?', false, 'Kommer', 'Kommer', 3)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er rørholdere på lysarmaturer ok?', false, 'Kommer', 'Kommer', 3)");

$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Foreligger dokumentasjon med branntegning og oversiktsskjema der samsvarende referansenr  på brann- og røyktettinger er angitt?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Finnes klassifisering/sertifikat på benyttede produkter samt tilhørende monteringsanvisning?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er oppgitt brannmotstand på benyttede produkter ihht bygningskonstruksjonen/bygningsdelen?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Er den estetiske utformingen av brann- og røyktettinger tilfredsstillende?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Foreligger dokumentasjon med branntegning og oversiktsskjema der samsvarende referansenr  på brann- og røyktettinger er angitt?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Mangler den brannklassifiserte bygningsdelen brann- og røyktettinger?', false, 'Kommer', 'Kommer', 9)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Merking av gjennomføringer ivaretatt?', false, 'Kommer', 'Kommer', 9)");

$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Påse at merking/armaturer er på plass og fri for skader (hel, ren og ikke tildekket)', false, 'Kommer', 'Kommer', 11)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Kontroller alle armaturer ift. funksjonalitet i normaldrift (nett tilkoblet)', false, 'Kontroller alle armaturer ift. funksjonalitet i normaldrift (nett tilkoblet)<ul><li>Grønn lysidiode på sentralen indikerer OK</li><li>Markeringslysets lyskilde lyser</li><li>Ledelysets lyskilde lyser hvor ledelyset er koblet som en del av allmennbelysningen</li></ul>', 'Kommer', 11)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Kontroller alle armaturer ift. funksjonalitet i normaldrift (nett frakoblet)', false, 'Kontroller alle armaturer ift. funksjonalitet i normaldrift (nett frakoblet)<ul><li>Sentralen settes over i nøddrift</li><li>Oppsøk alle armaturer og utfør visuell sjekk av armaturens funksjonalitet (også ledelys i tak/panikkbelysning)</li><li>Test skal ikke vare lenger enn 25 % av total batterikapasitet for sentralen</li></ul>', 'Kommer', 11)");
$oProc->query("INSERT INTO controller_control_item (title, required, what_to_do, how_to_do, control_group_id) VALUES ('Etter utført kontroll - Sett sentralen i normal drift - Kontroller at indikatorlampe lyser.', false, 'Kontroller alle armaturer ift. funksjonalitet i normaldrift (nett frakoblet)<ul><li>Sentralen settes over i nøddrift</li><li>Oppsøk alle armaturer og utfør visuell sjekk av armaturens funksjonalitet (også ledelys i tak/panikkbelysning)</li><li>Test skal ikke vare lenger enn 25 % av total batterikapasitet for sentralen</li></ul>', 'Kommer', 11)");

//insert procedures
$oProc->query("INSERT INTO controller_procedure (title, purpose, responsibility, description, reference, attachment, revision_no) VALUES ('P40 Hvordan utføre egenkontroll av elektriske anlegg i kommunale bygg', 'Å sikre at elektriske anlegg i holdes forsvarlig stand i.h.t. forskrift om internkontroll av elektriske installasjoner', 'Bergen kommunale bygg er ansvarlig for oppdatering av rutiner og informasjon', 'Elektriske installasjoner skal kontrolleres i.h.t. internkontrollforskriftens § 5', '', '',1)");
$oProc->query("INSERT INTO controller_procedure (title, purpose, responsibility, description, reference, attachment, revision_no) VALUES ('P3811 Vannprøver og resultater', 'Ved jevnlig prøvetaking kan vi avklare avvik så tidlig så mulig og dermed sikre stabil vannkvalitet', 'Byggeier representert ved ansvarlig drifts- og vedlikeholdsingeniør BBE KF.', '<ol><li><span>Drifts- og vedlikeholdsingeniør skal sørge for at det foretas jevnlig prøvetaking med maks 3 måneders intervaller. </span></li><li><span><span></span></span><span>Prøvetakingsutstyret skal være godkjent av Næringsmiddeltilsynet og teknisk hygiene for Bergen og Omland.</span></li><li><span>Personell som skal innhente vannprøver skal ha nødvendig opplæring i dette. </span>Opplæring blir gitt av Næringsmiddeltilsynet.</li><li><span><span></span></span><span>Prøvetaking skal foregå iht. rutiner for prøvetaking som er vedlagt. </span></li><li><span><span></span></span><span>Analyseresultater blir sendt til BBE KF og tjenestested.</span></li><li><span><span></span></span><span>Kopi analyseresultat settes i denne IK-perm kap. 3.</span></li><li><span><span></span></span><span>Dersom vannkvalitet ikke tilfredsstiller kravet må DV-ingeniør konferere Næringsmiddeltilsynet for korrigerende tiltak.</span></li><li><span><span></span></span><span>Eventuelle avvik og korrigerende tiltak loggføres under kap. 6.</span></li><li><span>Punkt 9<br></span></li></ol></li>', '', '',1)");
$oProc->query("INSERT INTO controller_procedure (title, purpose, responsibility, description, reference, attachment, revision_no) VALUES ('P3812 Sjekkliste/Rutiner/Logg', '&nbsp;Ved bruk av sjekklister og faste tilsynsrutiner oppnås stabil drift. Tilsyn og nødvendige kontrollpunkter skal dokumenteres (loggføres) for&nbsp; at vi på en bedre måte kan vurdere anleggets tilstand og dermed raskere oppdage avvik.', 'Driftsleder i bydel skal sørge for at nødvendig tilsyn blir utført i tråd med fastalgte rutiner.', '<ol><li>Drifts- og vedlikeholdsingeniør skal sørge for at denne IK-håndbok ettterleves.</li><li>Driftsleder skal påse at ansvarlig driftspersonell foretar rutinemessig tilsyn/ inspeksjon iht. kapittel 5.</li><li>Evt. avvik i forhold til beskrevne rutiner skal begrunnes under kap. 6 avviksbehandling- (bruk rapportskjema under kap. 6)</li><li>Avvik skal rapporteres til drifts- og vedlikeholdsingeniør. Avvik kan være rutiner som ikke er fulgt, tekniske feil/ mangler, vannkvalitet som ikke tilfredsstiller gjeldende krav o.l.</li></ol></li>', '', '',1)");

